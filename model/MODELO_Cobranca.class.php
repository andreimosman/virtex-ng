<?

	/**
	 * Modelo de cobran�a (camada de neg�cios)
	 *
	 * - Ger�ncia de Contratos
	 * - Ger�ncia de Pagamentos
	 */

	class MODELO_Cobranca extends VirtexModelo {
		protected $cbtb_cliente_produto;
		protected $cbtb_contrato;
		
		public function __construct() {
			parent::__construct();
			$this->cbtb_cliente_produto = VirtexPersiste::factory("cbtb_cliente_produto");
			$this->cbtb_contrato = VirtexPersiste::factory("cbtb_contrato");
		}
		
		





		protected function prorata($data1,$data2,$valor_plano,$valor_comodato) {
			$retorno = array();
			$dias_prorata = MData::diff($data1,$data2);

			$retorno["valor_plano"] = $valor_plano;
			$retorno["valor_comodato"] = $valor_comodato;
			
			if( $dias_prorata > 28 ) {
				// N�o calcula a pro-rata.
				$retorno["dias_prorata"] = 0;				
			} else {
				$prorata_plano = round(($valor_plano/30)*$dias_prorata,2);
				$prorata_comodato = round(($valor_comodato/30)*$dias_prorata,2);
				$retorno["prorata_plano"] = $prorata_plano;
				$retorno["prorata_comodato"] = $prorata_comodato;
				$retorno["dias_prorata"] = $dias_prorata;			
			}
			
			return($retorno);
		
		}


		/**
		 * Gera a lista de faturas a serem criadas com base nas informa��es de pagamento.
		 *
		 * Considera��es:
		 *   -> data de vencimento
		 *   -> pagamento (pr� ou p�s pago)
		 *   -> taxa de instala��o
		 *   -> comodato
		 *   -> 
		 */
		public function gerarListaFaturas($pagamento,$data_contratacao,$vigencia,$dia_vencimento,$valor,$desconto_valor,$desconto_periodo,$tx_instalacao,$valor_comodato) {
			$faturas = array();
			
			$data_primeiro_vencimento = date("d/m/Y");
			
			$descontos_aplicados = 0;
			$meses_cobrados = 0;


			$data = MData::proximoDia($dia_vencimento,$data_contratacao);

			if( $pagamento == "POS" ) {
				// $data = MData::proximoDia($dia_vencimento,$data_contratacao);
				// Pagamento p�s pago
				$composicao = array();
				if( $tx_instalacao ) {
					// Gerar fatura 0 com a taxa de instala�ao
					$composicao["instalacao"] = $tx_instalacao;
					$faturas[] = array("data"=>$data_primeiro_vencimento,"valor" => $tx_instalacao,"composicao"=>$composicao);
				}
			} else {
				// Pagamento pr�-pago. Calcula pro-rata.
				$composicao = array();
				$prorata = $this->prorata($data_contratacao,$data,$valor,$valor_comodato);
				
				if( $prorata["dias_prorata"] > 0 ) {
					// Pr�-rata aplic�vel.
					$prorata_plano = $prorata["prorata_plano"];
					$prorata_comodato = $prorata["prorata_comodato"];
					$dias_prorata = $prorata["dias_prorata"];

					$composicao["prorata_plano"] = $prorata_plano;
					$composicao["prorata_comodato"] = $prorata_comodato;
					$composicao["dias_prorata"] = $dias_prorata;
					$valor_fatura = $prorata_plano + $prorata_comodato;
				
				} else {
					// Valores diretos
					$valor_fatura = $valor + $valor_comodato;
					$composicao["valor"] = $valor;
					$composicao["comodato"] = $valor_comodato;
				}
												
				if( $tx_instalacao ) {
					$valor_fatura += $tx_instalacao;
					$composicao["instalacao"] = $tx_instalacao;
				}
				
				if( $desconto_valor && $desconto_periodo > 0 ) {
					$valor_fatura -= $desconto_valor;
					$descontos_aplicados++;
					$composicao["desconto"] = array("parcela" => $descontos_aplicados . "/" . $desconto_periodo, "valor" => $desconto_valor);
				}
				
				$meses_cobrados++;
				
				$faturas[] = array("data"=>$data_primeiro_vencimento,"valor" => $valor_fatura,"composicao" => $composicao);
				
			}
			
			
			
			for( ; $meses_cobrados < $vigencia ; $meses_cobrados++ ) {
				$got = false;
				$composicao = array();
				
				if( $pagamento == "POS" && $meses_cobrados == 0 ) {
					// Primeiro vencimento de p�s-pago. Calcular pr�-rata.
					$prorata = $this->prorata($data_contratacao,$data,$valor,$valor_comodato);
					if( $prorata["dias_prorata"] > 0 ) {
						// Pr�-rata aplic�vel.
						$prorata_plano = $prorata["prorata_plano"];
						$prorata_comodato = $prorata["prorata_comodato"];
						$dias_prorata = $prorata["dias_prorata"];

						$composicao["prorata_plano"] = $prorata_plano;
						$composicao["prorata_comodato"] = $prorata_comodato;
						$composicao["dias_prorata"] = $dias_prorata;
						$valor_fatura = $prorata_plano + $prorata_comodato;
						
						$got = true;
					} 					
				} 
				
				if( !$got )  {
					$valor_fatura = $valor + $valor_comodato;
					$composicao["valor"] = $valor;

					if( $valor_comodato ) $composicao["comodato"] = $valor_comodato;

					// Reconfigura a data para o dia de vencimento.
					list($d,$m,$a) = explode("/",$data);
					$d = $dia_vencimento;
					
					// Incrementa 1 m�s
					$data = MData::adicionaMes("$d/$m/$a",1);
				}
				
				if( $desconto_valor > 0 && $descontos_aplicados < $desconto_periodo ) {
					$valor_fatura -= $desconto_valor;
					$descontos_aplicados++;
					
					$composicao["desconto"] = array("parcela" => $descontos_aplicados . "/" . $desconto_periodo, "valor" => $desconto_valor);
				}

				$faturas[] = array("data"=>$data,"valor" => $valor_fatura,"composicao" => $composicao);
				

			}
			
			return($faturas);
		
		}
	
	
	}


?>
