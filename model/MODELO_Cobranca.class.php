<?

	/**
	 * Modelo de cobrança (camada de negócios)
	 *
	 * - Gerência de Contratos
	 * - Gerência de Pagamentos
	 */

	class MODELO_Cobranca extends VirtexModelo {
		protected $cbtb_cliente_produto;
		protected $cbtb_contrato;
		protected $cbtb_carne;
		protected $cbtb_carne_impressao;
		protected $pftb_forma_pagamento;
		protected $cbtb_fatura;
		// protected $cbtb_fatura_itens;
		protected $preferencias;
		
		protected $lgtb_reagendamento;
		protected $lgtb_renovacao;

		protected $cltb_cliente;
		protected $prtb_produto;

		protected $cbtb_endereco_cobranca;

		protected $cbtb_lote_cobranca;
		protected $cbtb_lote_fatura;

		protected $cbtb_comissao;
		
		protected $cbtb_remessa;
		protected $cbtb_retorno;
		protected $cbtb_retorno_erro;
		
		protected static $moeda = 9;


		public function __construct() {
			parent::__construct();
			$this->cbtb_cliente_produto = VirtexPersiste::factory("cbtb_cliente_produto");
			$this->cbtb_contrato = VirtexPersiste::factory("cbtb_contrato");
			$this->cbtb_endereco_cobranca = VirtexPersiste::factory("cbtb_endereco_cobranca");
			$this->cbtb_retorno = VirtexPersiste::factory("cbtb_retorno");
			$this->cbtb_retorno_erro = VirtexPersiste::factory("cbtb_retorno_erro");

			$this->cbtb_fatura = VirtexPersiste::factory("cbtb_faturas");
			// $this->cbtb_fatura_itens = VirtexPersiste::factory("cbtb_fatura_itens");
			$this->cbtb_carne = VirtexPersiste::factory("cbtb_carne");
			$this->cbtb_carne_impressao = VirtexPersiste::factory("cbtb_carne_impressao");

			$this->pftb_forma_pagamento = VirtexPersiste::factory("pftb_forma_pagamento");
			$this->preferencias = VirtexModelo::factory("preferencias");
			$this->cltb_cliente = VirtexPersiste::factory("cltb_cliente");
			$this->prtb_produto = VirtexPersiste::factory("prtb_produto");
			
			$this->lgtb_reagendamento = VirtexPersiste::factory("lgtb_reagendamento");
			$this->lgtb_renovacao = VirtexPersiste::factory("lgtb_renovacao");

			$this->cbtb_lote_cobranca = VirtexPersiste::factory("cbtb_lote_cobranca");
			$this->cbtb_lote_fatura = VirtexPersiste::factory("cbtb_lote_fatura");
			
			$this->cbtb_comissao = VirtexPersiste::factory("cbtb_comissao");
			
			$this->cbtb_remessa = VirtexPersiste::factory("cbtb_remessa");
		}

		public function obtemClienteProduto($id_cliente_produto) {
			return($this->cbtb_cliente_produto->obtemUnico(array("id_cliente_produto"=>$id_cliente_produto)));
		}

		public function cadastraEnderecoCobranca($id_cliente_produto,$endereco,$complemento,$bairro,$id_cidade,$cep,$id_condominio_cobranca,$id_bloco_cobranca, $apto_cobranca, $id_cliente) {
			$dados = array(
							"id_cliente_produto" => $id_cliente_produto,
							"endereco" => $endereco,
							"complemento" => $complemento,
							"bairro" => $bairro,
							"id_cidade" => $id_cidade,
							"cep" => $cep,
							"id_cliente" => $id_cliente,
							"apto_cobranca" => $apto_cobranca
							);

			if( $dados["id_condominio_cobranca"] ) {
				$dados["id_condominio_cobranca"] = $id_condominio_cobranca;
			}
			if( $dados["id_bloco_cobranca"] ) {
				$dados["id_bloco_cobranca"] = $id_bloco_cobranca;
			}


			$this->cbtb_endereco_cobranca->insere($dados);
		}

		public function obtemEnderecoCobranca($id_cliente_produto) {
			$filtro = array("id_cliente_produto" => $id_cliente_produto);
			return($this->cbtb_endereco_cobranca->obtemUnico($filtro, "id_endereco_cobranca DESC"));
		}

		
		public function obtemEnderecoCobrancaReferenciado($id_cliente_produto) {
			return($this->cbtb_endereco_cobranca->obtemEnderecoCobrancaReferenciado($id_cliente_produto));
		}

		protected function prorata($data1,$data2,$valor_plano,$valor_comodato) {

			$retorno = array();
			$dias_prorata = MData::diff($data1,$data2);

			$retorno["prorata_plano"] = $valor_plano;
			$retorno["prorata_comodato"] = $valor_comodato;
			
			if( $dias_prorata >= 28 && $dias_prorata <= 31 ) {
				// Não calcula a pro-rata.
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
		 * Função utilizada para contratos antigos onde o dia do vencimento não está gravado no banco de dados.
		 */
		public function obtemDiaVencimento($id_cliente_produto) {
			return($this->cbtb_fatura->obtemDiaVencimento($id_cliente_produto));
		}
		


		/**
		 * Gera a lista de faturas a serem criadas com base nas informações de pagamento.
		 *
		 * Considerações:
		 *   -> data de vencimento
		 *   -> pagamento (pré ou pós pago)
		 *   -> taxa de instalação
		 *   -> comodato
		 *   ->
		 */
		public function gerarListaFaturas($pagamento,$data_contratacao,$vigencia,$dia_vencimento,$valor,$desconto_valor,$desconto_periodo,$tx_instalacao,$valor_comodato,$data_primeiro_vencimento,$faz_prorata,$limite_prorata,$parcelamento_instalacao,$id_cliente_produto=0) {
			//$dia_vencimento = 19;
			//$data_primeiro_vencimento = '19/08/2008';
			//echo "<pre>";
			//print_r(array($pagamento,$data_contratacao,$vigencia,$dia_vencimento,$valor,$desconto_valor,$desconto_periodo,$tx_instalacao,$valor_comodato,$data_primeiro_vencimento,$faz_prorata,$limite_prorata,$parcelamento_instalacao,$id_cliente_produto));
			//echo "</pre>";
		
			$faturas = array();
			
 			$descontos_aplicados = 0;
			$meses_cobrados = 0;
			
			if( !$parcelamento_instalacao ) $parcelamento_instalacao = 1;
			
			$parcelaInstalacao = $tx_instalacao / $parcelamento_instalacao;
			$parcelaInstalacao = (float) number_format($parcelaInstalacao,2,".","");
			
			$parcelasInstCobradas = 0;
			
			// Se a vigência do contrato for menor que o parcelamento da instalação gera pelo menos as faturas do parcelamento.
			if( $tx_instalacao && $vigencia < $parcelamento_instalacao ) {
				$vigencia = $parcelamento_instalacao;
			}
			
			if( $vigencia < $desconto_periodo ) {
				$vigencia = $desconto_periodo;
			}

			$data = MData::proximoDia($dia_vencimento,$data_contratacao);
			
			// echo "DATA: $data<br>\n";
			
			$itens_fatura = array();
			
			// echo "ID_CLIENTE_PRODUTO: $id_cliente_produto<br>\n"; 

			// Pro-Rata Residual (cobrado somente em caso de migracao para produto de valor diferente).
			$diasResidual = 0;
			$valorResidual = 0;
			$residualCobrado = false;

			
			if( $id_cliente_produto ) {
				// 
				// Migração - será necessário utilizar informações do contrato antigo para cálculos.
				// 
			
				$contrato = $this->obtemContratoPeloId($id_cliente_produto);
				
				// Cobrança de pro-rata residual
				$diaVenc = (int)$contrato["vencimento"];
								
				// Condição para evitar problemas em cadastros antigos.
				// if( $contrato["valor_produto"] != $valor && $diaVenc > 0 && $faz_prorata == 't') {
					// Se diasResidual for < 0 caracteriza desconto.
					//$diasResidual = $diaHoje - $diaVenc;
					$valorResidual = ($contrato["valor_produto"] / 30) * $diasResidual;					

					$diaBase = date($diaVenc."/m/Y");				
					$hoje = date("d/m/Y");

					$diasResidual = MData::diff($diaBase,$hoje);
					$valorResidual = ($contrato["valor_produto"] / 30) * $diasResidual;

					if( $diasResidual < 0 ) {
						// Calcula a diferença entre o plano velho e o plano novo, podendo caracterizar desconto ou acrescimo.
						// Trata-se do número de dias que o cliente vai utilizar até a fatura deste mês vencer.

						$valorProrataParcial = ($valor / 30) * $diasResidual;
						$valorProrataParcial *= -1;

						$valorResidual += $valorProrataParcial;

					}

				//}
				
				/**
				echo "<pre>"; 
				//print_r($contrato);
				echo "diaHoje: $diaHoje\n"; 
				echo "diaVenc: $diaVenc\n";
				echo "diaResi: $diasResidual\n";
				echo "valResi: $valorResidual\n";
				
				echo "</pre>"; 
				*/
				
			
			}
			
			if( $pagamento == "POS" ) {
			
        		$composicao = array();
        		

        		if( ((float)$tx_instalacao) > 0 ) {					
					//
					// Fatura da taxa de instalação cobrada imediatamente.
					//
        		
        		
        			$vlPrimeiroBoleto = 0;

					$composicao["instalacao"] = $parcelaInstalacao;
					$composicao["parcela_instalacao"] = $meses_cobrados + 1 . "/" . $parcelamento_instalacao;
					
					$vlPrimeiroBoleto = $parcelaInstalacao;
					
					if( $valorResidual ) {
						$vlPrimeiroBoleto += $valorResidual;
						
						if( $diasResidual < 0 ) {
							$diasResidual *= -1;
							// $valorResidual *= -1;
						}
						
						$composicao["residual"] = array("dias_prorata_residual" => $diasResidual, "prorata_residual" => $valorResidual);
						
						$residualCobrado = true;
						
						
					}
					
					$faturas[] = array("data"=>$data_contratacao,"valor" => $vlPrimeiroBoleto,"composicao"=>$composicao);
					$parcelasInstCobradas++;
					$meses_cobrados++;
					
					unset($vlPrimeiroBoleto);

				}

			} else {
			
				// Pagamento pré-pago. Calcula pro-rata.
				$composicao = array();
				$prorata = $this->prorata($data_contratacao,$data_primeiro_vencimento,$valor,$valor_comodato);
				
        		if( ($prorata["dias_prorata"] > 0) && ($faz_prorata == 't') ) {
					// Pró-rata aplicável.
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
        		if( ((float)$tx_instalacao) > 0) {
					$valor_fatura += $parcelaInstalacao;
					$composicao["parcela_instalacao"] = ($parcelasInstCobradas + 1) . "/" . $parcelamento_instalacao;
					$composicao["instalacao"] = $parcelaInstalacao;
					$parcelasInstCobradas++;
				}
				
				if( $desconto_valor && $desconto_periodo > 0 ) {
					$valor_fatura -= $desconto_valor;
					$descontos_aplicados++;
					$composicao["desconto"] = array("parcela" => $descontos_aplicados . "/" . $desconto_periodo, "valor" => $desconto_valor);
				}

				$meses_cobrados++;

				$faturas[] = array("data"=>$data_contratacao,"valor" => $valor_fatura,"composicao" => $composicao);

				$valor_fatura = $valor+$valor_comodato;
				$composicao["prorata_plano"] = '';
				$composicao["prorata_comodato"] = '';
				$composicao["dias_prorata"] = '';
				$composicao["valor"] = $valor;
				$composicao["comodato"] = $valor_comodato;
				$composicao["instalacao"] = '';

			}
			
			if( $faz_prorata == 't' && (($meses_cobrados == 1 && $prorata["dias_prorata"] < 30)  || empty($prorata)) ) {
				//echo "INC FALSE";
				//echo "<pre>";
				//print_r($prorata);
				//echo "</pre>"; 
				$incrementa = false;
				
				// echo "INC FALSE<br>\n";
				
			} else {
				$incrementa = true;
			}
			
			if( $pagamento == "POS" && $meses_cobrados == 1) {
				$vigencia++;
			}
			
			// $ultimaData = 
			
			for( ; $meses_cobrados < $vigencia ; $meses_cobrados++ ) {
				$got = false;
				$composicao = array();

				if( $pagamento == "POS" && (($tx_instalacao > 0 && $meses_cobrados == 1) || (!$tx_instalacao && !$meses_cobrados) ) ) {
					// echo "AQUI!!!";
					// Primeiro vencimento de pós-pago. Calcular pró-rata.
					$prorata = $this->prorata($data_contratacao,$data_primeiro_vencimento,$valor,$valor_comodato);
					if( ($prorata["dias_prorata"] > 0) && ($faz_prorata == 't') ) {
					
						// echo "PRORATA APLIC";
					
						// Pró-rata aplicável.
						$prorata_plano = $prorata["prorata_plano"];
						$prorata_comodato = $prorata["prorata_comodato"];
						$dias_prorata = $prorata["dias_prorata"];
						
						$valor_fatura = 0;
						
						
						if( !$residualCobrado ) {
							$valor_fatura = $valorResidual;

							if( $diasResidual < 0 ) {
								$diasResidual *= -1;
								// $valorResidual *= -1;
							}

							$composicao["residual"] = array("dias_prorata_residual" => $diasResidual, "prorata_residual" => $valorResidual);
							
							// echo "<pre>"; 
							// print_r($composicao);
							// echo "</pre>"; 

						}


						$data = $data_primeiro_vencimento;
						
						// echo "DATA: $data<br>\n"; 

						$composicao["prorata_plano"] = $prorata_plano;
						$composicao["prorata_comodato"] = $prorata_comodato;
						$composicao["dias_prorata"] = $dias_prorata;
						$valor_fatura += $prorata_plano + $prorata_comodato;

						$got = true;
						
					}
				}

				if( !$got )  {
					$valor_fatura = $valor + $valor_comodato;
					$composicao["valor"] = $valor;

					if( $valor_comodato ) $composicao["comodato"] = $valor_comodato;

					// Reconfigura a data para o dia de vencimento.
					list($d,$m,$a) = explode("/", $data);
					$d = $dia_vencimento;

					// Incrementa 1 mês se aplicável
					if( $pagamento == "PRE" && $faz_prorata == 'f' && $meses_cobrados == 1 ) {
						$incrementa = 0;
					} else {
						if( !($pagamento == "PRE" && !$incrementa) && $data == "$d/$m/$a" ) {
							// echo "INCREMENTA MES";
							$incrementa = 1;
						}
					}
					
					
					
					if( $pagamento == "PRE" && $meses_cobrados == 1 && !$incrementa) {
						// echo "MESES 01, n/a<br>\n";						
						// echo "PAGAMENTO = PRE / MC = 1 / NOT INC<br>\n";
					} else {
						// echo "MESES: $meses_cobrados<br>\n"; 
						
						$diff_ini=MData::diff($data_primeiro_vencimento,$data);
						
						// echo "DATA: $data, $data_primeiro_vencimento: ". $diff_ini ."<br>\n"; 
						if( $pagamento == "POS" && $tx_instalacao > 0 && $meses_cobrados == 1 ) {
							// echo "TX + MES 1<br>\n";
						} else {
							// if( $pagamento == "PRE" && !$tx_instalacao & 
							if( $meses_cobrados || $diff_ini < 0 ) {
								$data = MData::adicionaMes("$d/$m/$a",1);
							}
						}
					}
				}
				
				if( $desconto_valor > 0 && $descontos_aplicados < $desconto_periodo ) {
					// echo "APLICANDO!!!<br>\n ";
					$valor_fatura -= $desconto_valor;
					$descontos_aplicados++;

					$composicao["desconto"] = array("parcela" => $descontos_aplicados . "/" . $desconto_periodo, "valor" => $desconto_valor);
				}

				if( $tx_instalacao > 0 && $parcelasInstCobradas < $parcelamento_instalacao ) {
					$composicao["parcela_instalacao"] = ($parcelasInstCobradas + 1) . "/" . $parcelamento_instalacao;
					$composicao["instalacao"] = $parcelaInstalacao;
					$valor_fatura+=$parcelaInstalacao;
					$parcelasInstCobradas++;
				}

				$faturas[] = array("data"=>$data,"valor" => $valor_fatura,"composicao" => $composicao);


			}

			return($faturas);

		}
		
		function aceiteContrato($id_cliente_produto) {
			$dados = array("aceito" => "t", "data_aceite" => "=now");			
			return($this->cbtb_contrato->altera($dados,array("id_cliente_produto" => $id_cliente_produto)));
		}


		function novoContrato($id_cliente, $id_produto, $dominio, $id_modelo_contrato, $data_contratacao, $vigencia, $pagamento, $data_renovacao, $valor_contrato, $username, $senha,
                          $id_cobranca, $status, $tx_instalacao, $valor_comodato, $desconto_promo, $desconto_periodo, $dia_vencimento, $primeira_fatura, $prorata, $limite_prorata,
                          $carencia, $id_prduto, $id_forma_pagamento, $pro_dados, $da_dados, $bl_dados, $cria_email, $dados_produto, $endereco_cobranca, $endereco_instalacao, 
						  $dados_conta, &$gera_carne = false, $parcelas_instalacao = 1) {
			/**
			echo "<pre>+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
			echo "MODELO_Conbranca::novoContrato()\n";
			echo "primeiro_vencimento:\t\t=\t$primeiro_vencimento\n";
			echo "pro_rata:\t\t\t=\t$pro_rata\n";
			echo "id_cliente\t\t\t=\t".print_r($id_cliente,true)."\n";
			echo "id_produto\t\t\t=\t".print_r($id_produto,true)."\n";
			echo "dominio\t\t\t\t=\t".print_r($dominio,true)."\n";
			echo "data_contratacao\t\t=\t".print_r($data_contratacao,true)."\n";
			echo "vigencia\t\t\t=\t".print_r($vigencia,true)."\n";
			echo "pagamento\t\t\t=\t".print_r($pagamento,true)."\n";
			echo "data_renovacao\t\t\t=\t".print_r($data_renovacao,true)."\n";
			echo "valor_contrato\t\t\t=\t".print_r($valor_contrato,true)."\n";
			echo "username\t\t\t=\t".print_r($username,true)."\n";
			echo "senha\t\t\t\t=\t".print_r($senha,true)."\n";
			echo "id_cobranca\t\t\t=\t".print_r($id_cobranca,true)."\n";
			echo "status\t\t\t\t=\t".print_r($status,true)."\n";
			echo "tx_instalacao\t\t\t=\t".print_r($tx_instalacao,true)."\n";
			echo "valor_comodato\t\t\t=\t".print_r($valor_comodato,true)."\n";
			echo "desconto_promo\t\t\t=\t".print_r($desconto_promo,true)."\n";
			echo "desconto_periodo\t\t=\t".print_r($desconto_periodo,true)."\n";
			echo "dia_vencimento\t\t\t=\t".print_r($dia_vencimento,true)."\n";
			echo "primeira_fatura\t\t\t=\t".print_r($primeira_fatura,true)."\n";
			echo "prorata\t\t\t\t=\t".print_r($prorata,true)."\n";
			echo "limite_prorata\t\t\t=\t".print_r($limite_prorata,true)."\n";
			echo "carencia\t\t\t=\t".print_r($carencia,true)."\n";
			echo "id_prduto\t\t\t=\t".print_r($id_prduto,true)."\n";
			echo "id_forma_de_pagamento\t\t=\t".print_r($id_forma_pagamento,true)."\n";
			echo "pro_dados\t\t\t=\t".print_r($pro_dados,true)."\n";
			echo "da_dados\t\t\t=\t".print_r($da_dados,true)."\n";
			echo "bl_dados\t\t\t=\t".print_r($bl_dados,true)."\n";
			echo "cria_email\t\t\t=\t".print_r($cria_email,true)."\n";
			echo "dados_produto\t\t\t=\t".print_r($dados_produto,true)."\n";
			echo "endereco_cobranca\t\t\t=\t".print_r($endereco_cobranca,true)."\n";
			echo "endereco_instalacao\t\t\t=\t".print_r($endereco_instalacao,true)."\n";
			echo "dados_conta\t\t\t=\t".print_r($dados_conta,true)."\n";
			echo "gera_carne\t\t\t=\t".print_r($gera_carne,true)."\n";
			echo "\n+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++</pre>";
			*/
			
			$formaPagto = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);
			$prefProv = $this->preferencias->obtemPreferenciasProvedor();

			$comodato = $valor_comodato ? true : false;

			$dados = array( "id_cliente" => $id_cliente, "id_produto" => $id_produto, "dominio" => $dominio );
			$id_cliente_produto = $this->cbtb_cliente_produto->insere($dados);

			$disc_franquia_horas = @$dados_produto["franquia_horas"] ? $dados_produto["franquia_horas"] : "";
			$disc_permitir_duplicidade = @$dados_produto["permitir_duplicidade"] ? $dados_produto["permitir_duplicidade"] : "";
			$disc_valor_hora_adicional = @$dados_produto["valor_hora_adicional"] ? $dados_produto["valor_hora_adicional"] : "";

			$hosp_dominio = @$dados_produto["dominio"] ? $dados_produto["dominio"] : "";
			$hosp_franquia_em_mb = @$dados_produto["franquia_em_mb"] ? $dados_produto["franquia_em_mb"] : 0;
			$hosp_valor_mb_adicional = @$dados_produto["valor_mb_adicional"] ? $dados_produto["valor_mb_adicional"] : "";

			$bl_banda_upload_kbps = @$dados_produto["banda_upload_kbps"] ? $dados_produto["banda_upload_kbps"] : "";
			$bl_banda_download_kbps = @$dados_produto["banda_download_kbps"] ? $dados_produto["banda_download_kbps"] : "";
			$bl_franquia_trafego_mensal_gb = @$dados_produto["franquia_trafego_mensal_gb"] ? $dados_produto["franquia_trafego_mensal_gb"] : "";
			$bl_valor_trafego_adicional_gb = @$dados_produto["valor_trafego_adicional_gb"] ? $dados_produto["valor_trafego_adicional_gb"] : "";

			$dados = array(	"id_cliente_produto" => $id_cliente_produto,
							"data_contratacao" => $data_contratacao,
							"vigencia" => $vigencia,
							"data_renovacao" => $data_renovacao,
							"valor_contrato" => $valor_contrato,
							"id_cobranca" => $id_cobranca,
							"status" => $status,
							"tipo_produto" => $dados_produto["tipo"],
							"valor_produto" => $dados_produto["valor"],
							"num_emails" => $dados_produto["num_emails"],
							"quota_por_conta" => $dados_produto["quota_por_conta"],
							"tx_instalacao" => $tx_instalacao,
							"comodato" => $comodato,
							"valor_comodato" => $valor_comodato,
							"desconto_promo" => $desconto_promo,
							"periodo_desconto" => $desconto_periodo,
							"hosp_dominio" => $hosp_dominio,
							"hosp_franquia_em_mb" => $hosp_franquia_em_mb,
							"hosp_valor_mb_adicional" => $hosp_valor_mb_adicional,
							"disc_franquia_horas" => $disc_franquia_horas,
							"disc_permitir_duplicidade" => $disc_permitir_duplicidade,
							"disc_valor_hora_adicional" => $disc_valor_hora_adicional,
							"bl_banda_upload_kbps" => $bl_banda_upload_kbps,
							"bl_banda_download_kbps" => $bl_banda_download_kbps,
							"bl_franquia_trafego_mensal_gb" => $bl_franquia_trafego_mensal_gb,
							"bl_valor_trafego_adicional_gb" => $bl_valor_trafego_adicional_gb,
							"cod_banco" => $pro_dados["codigo_banco"],
							"carteira" => $pro_dados["carteira"],
							"agencia" => $pro_dados["agencia"],
							"num_conta" => $pro_dados["num_conta"],
							"convenio" => $pro_dados["convenio"],
							"cc_vencimento" => "",
							"cc_numero" => "",
							"cc_operadora" => "",
							"db_banco" => "",
							"db_agencia" => "",
							"db_conta" => "",
							"vencimento" => $dia_vencimento,
							"carencia" => $carencia,
							"data_alt_status" => "",
							"id_produto" => $id_produto,
							"id_modelo_contrato" => $id_modelo_contrato,
							"nome_produto" => $dados_produto["nome"],
							"descricao_produto" => $dados_produto["descricao"],
							"disponivel" => $dados_produto["disponivel"],
							"vl_email_adicional"  => $dados_produto["vl_email_adicional"],
							"permitir_outros_dominios" => $dados_produto["permitir_outros_dominios"],
							"email_anexado" => $dados_produto["email_anexado"],
							"numero_contas" => $dados_produto["numero_contas"],
							"valor_estatico" => $dados_produto["valor_estatico"],
							"da_cod_banco" => $da_dados["codigo_banco"],
							"da_carteira" => $da_dados["carteira"],
							"da_convenio" => $da_dados["convenio"],
							"da_agencia" => $da_dados["agencia"],
							"da_num_conta" => $da_dados["num_conta"],
							"bl_cod_banco" => $bl_dados["codigo_banco"],
							"bl_carteira" => $bl_dados["carteira"],
							"bl_convenio" => $bl_dados["convenio"],
							"bl_agencia" => $bl_dados["agencia"],
							"bl_num_conta" => $bl_dados["num_conta"],
							"pagamento" => $pagamento
							);

			if( $id_forma_pagamento ) {
				$dados["id_forma_pagamento"]=$id_forma_pagamento;
			}
			
			if( !$parcelas_instalacao ) {
				$parcelas_instalacao = 1;
			}
			
			$vl_parcelas_instalacao = $tx_instalacao / $parcelas_instalacao;

			$dados["vl_parcelas_instalacao"] = $vl_parcelas_instalacao;
			$dados["num_parcelas_instalacao"] = $parcelas_instalacao;
							


						
			$this->cbtb_contrato->insere($dados);
			$todas_faturas = ((float)$dados_produto["valor"] > 0) ? $this->gerarListaFaturas($pagamento, $data_contratacao, $vigencia, $dia_vencimento, $dados_produto["valor"], $desconto_promo, $desconto_periodo, $tx_instalacao, $valor_comodato, $primeira_fatura,      $prorata,   $limite_prorata, $parcelas_instalacao) : array();
			
			$this->cadastraFaturas($todas_faturas,$formaPagto,$dados_produto,$data_contratacao,$id_cliente_produto,$id_cliente);
			
			/**
			
			$id_cobranca = 0;
			// gera carne
			if ($formaPagto ['carne'] == 't' && count ($todas_faturas) > 0) {
				$gera_carne = true;
				$soma_fatura = 0;
				foreach ($todas_faturas as $fatura){
					$soma_fatura += $fatura ["valor"];
				}

				$dados = array (
					'data_geracao' => $data_contratacao,
					'id_cliente_produto' => $id_cliente_produto,
					'valor' => $soma_fatura,
					'vigencia' => count ($todas_faturas),
					'id_cliente' => $id_cliente,
				);

				$id_cbtb_carne = $this->cbtb_carne->insere ($dados);
			}

			for( $i=0;$i<count($todas_faturas);$i++) {
				$fatura = $todas_faturas[$i];
				$cod_barra = "";
				$linha_digitavel = "";
				$nosso_numero = "";
				
				if ($gera_carne) {
					// gera codigo de barras
					$nosso_numero = $this->pftb_forma_pagamento->obtemProximoNumeroSequencial ($id_forma_pagamento);

					// ($banco,$agencia,$conta,$carteira,$convenio,$vencimento,$valor,$id,$moeda=9,$cnpj_ag_cedente="",$codigo_cedente="",$operacao_cedente="")

					

					switch ($formaPagto ["tipo_cobranca"]) {
						case "PC":
							$cod_barra = MArrecadacao::codigoBarrasPagContas ($fatura ["valor"], $prefProv ['cnpj'], $nosso_numero, $fatura ['data']);
							$linha_digitavel = MArrecadacao::linhaDigitavel ($cod_barra);
							break;
						case "BL":
							$boleto = MBoleto::factory ($formaPagto["codigo_banco"],$formaPagto["agencia"],$formaPagto["conta"],$formaPagto["carteira"],$formaPagto["convenio"],$fatura['data'],$fatura ['valor'],$nosso_numero,self::$moeda,$formaPagto ['cnpj_ag_cedente'],$formaPagto ['codigo_cedente'],$formaPagto ['operacao_cedente']);
							$cod_barra = $boleto->obtemCodigoBoleto ();
							$linha_digitavel = $boleto->obtemLinhaDigitavel();
							break;
						case "MO":
							// Carnê genérico
							$carne = new MCarne($fatura["valor"],$id_cliente_produto,$nosso_numero,$fatura['data']);
							$cod_barra = $carne->obtemCodigoBarras();
							$linha_digitavel = $carne->obtemLinhaDigitavel();
							break;
					}

				}

				$this->cadastraFatura($id_cliente_produto, $id_cobranca, $fatura["data"], $fatura["valor"], $id_forma_pagamento, $dados_produto["nome"], $id_cbtb_carne, $nosso_numero, $linha_digitavel, $cod_barra);

			}
			
			*/

			//$preferencias = VirtexModelo::factory('preferencias');

			$prefGeral = $this->preferencias->obtemPreferenciasGerais();
			$dominio_padrao = $prefGeral["dominio_padrao"];

			$status_conta = "A";
			$obs = "";
			$conta_mestre = "t";

			$contas = VirtexModelo::factory("contas");

			//grava endereco de cobranca
			$dados = $endereco_cobranca;
			$dados["id_cliente"] = $id_cliente;
			$dados["id_cliente_produto"] = $id_cliente_produto;

			$this->cadastraEnderecoCobranca($id_cliente_produto,$dados["endereco"],$dados["complemento"],$dados["bairro"],$dados["id_cidade"],$dados["cep"],$dados["id_condominio_cobranca"], $dados["id_bloco_cobranca"], $dados["apto_cobranca"], $id_cliente);

			if( count($dados_conta) ) {

				switch(trim($dados_produto["tipo"])) {
					case 'BL':
						$id_conta = $contas->cadastraContaBandaLarga($username, $dominio_padrao, $senha, $id_cliente, $id_cliente_produto, $status_conta, $obs,
						$conta_mestre, $dados_conta["id_pop"], $dados_conta["id_nas"], $dados_produto["banda_upload_kbps"], $dados_produto["banda_download_kbps"],
						$dados_conta["mac"], $dados_conta["endereco"]);
					break;
					case 'D':
						$id_conta = $contas->cadastraContaDiscado($username, $dominio_padrao, $senha, $id_cliente, $id_cliente_produto, $status_conta, $obs,
						$conta_mestre, $dados_conta["foneinfo"]);
					break;
					case 'H':
						$id_conta = $contas->cadastraContaHospedagem($username, $dominio_padrao, $senha, $id_cliente, $id_cliente_produto, $status_conta, $obs,
						$conta_mestre, $dados_conta["tipo_hospedagem"], $dados_conta["dominio_hospedagem"]);
					break;
				}

				if ( $cria_email ) {
					$contas->cadastraContaEmail($username, $dominio_padrao, $senha, $id_cliente, $id_cliente_produto, $status_conta, $obs,
					$conta_mestre, $dados_produto["quota_por_conta"]);
				}

				$contas->cadastraEnderecoInstalacao($id_conta,$endereco_instalacao["endereco"],$endereco_instalacao["complemento"],$endereco_instalacao["bairro"],$endereco_instalacao["id_cidade"],$endereco_instalacao["cep"],$endereco_instalacao["id_condominio_instalacao"], $endereco_instalacao["id_bloco_instalacao"],$endereco_instalacao["apto_instalacao"], $id_cliente);
				
			}

			return($id_cliente_produto);


		}


	    public function cadastraFatura($id_cliente_produto, $id_cobranca, $data, $valor, $id_forma_pagamento, $descricao = "",
	                                      $id_carne = 0, $nosso_numero = "", $linha_digitavel = "", $cod_barra = "") {

	      $dados = array("id_cliente_produto"=>$id_cliente_produto, "data"=>$data, "valor"=>$valor, "status"=>"A", "id_forma_pagamento"=>$id_forma_pagamento);

	      if ( $descricao )
	        $dados["descricao"] = $descricao;

	      if ( $id_carne )
	        $dados["id_carne"] = $id_carne;

	      if ( $nosso_numero )
	        $dados["nosso_numero"] = $nosso_numero;

	      if ( $linha_digitavel )
	        $dados["linha_digitavel"] = $linha_digitavel;

	      if ( $cod_barra )
	        $dados["cod_barra"] = $cod_barra;


	      $this->cbtb_fatura->insere($dados);
	    }

	    public function cancelaContrato($id_cliente_produto) {
			$dados = array("status" => "C","data_alt_status" => "=now");
			$this->cbtb_contrato->altera($dados,array("id_cliente_produto" => $id_cliente_produto));
	    }

		public function obtemContratos ($id_cliente,$status="",$tipo="",$aceito="")
		{
			$res = $this->cbtb_cliente_produto->obtemContratos ($id_cliente,$status,$tipo,$aceito);
			return ($res);
		}

		public function obtemContratoPeloId($id_cliente_produto) {
			$filtro = array("id_cliente_produto" => $id_cliente_produto);
			$res = $this->cbtb_contrato->obtemUnico($filtro);
			return($res);
		}

		public function obtemAdesoesPorPeriodo($periodo){
			return( $this->cbtb_contrato->obtemAdesoesPorPeriodo($periodo) );
		}

		public function obtemCancelamentosPorPeriodo($periodo){
			return( $this->cbtb_contrato->obtemCanceladosPorPeriodo($periodo) );
		}
		
		public function obtemEvolucaoPorPeriodo($periodo) {
			return($this->cbtb_contrato->obtemEvolucao($periodo));
		}
		
		public function estornaPagamentoFatura($id_cobranca,$estorna_acrescimo=false) {
			$filtro = array("id_cobranca" => $id_cobranca);
			$dados = array("status" => 'A', valor_pago => 0, data_pagamento => null );
			if( $estorna_acrescimo ) {
				$dados["acrescimo"] = 0;
			}
			
			$fluxo = VirtexModelo::factory('caixa');
			
			$fluxo->estornaPagamentoFatura($id_cobranca);
			
			// echo "<pre>"; 
			// print_r(dados);
			// echo "</pre>";
			
			
			return($this->cbtb_fatura->altera($dados,$filtro));
		}

		public function estornaFatura($id_cobranca) {
			$filtro = array("id_cobranca" => $id_cobranca);
			$dados = array("status" => "E");
			return($this->cbtb_fatura->altera($dados,$filtro));
		}
		
		public function alteraRemessaFatura($id_remessa, $id_cobranca) {
			$filtro = array("id_cobranca" => $id_cobranca);
			$dados = array("id_remessa" => $id_remessa);
			$retorno = $this->cbtb_fatura->altera($dados,$filtro);
			return $retorno;
		}

		public function obtemFaturasPorCarne($id_carne) {
			$filtro = array("status" => "A", "id_carne" => $id_carne);
			return($this->cbtb_fatura->obtem($filtro,"data DESC"));
		}

		public function obtemFaturasPorRetorno($id_retorno) {
			return($this->cbtb_fatura->obtemFaturasPorRetorno($id_retorno));
		}



		/**
		 *
		 */
		public function obtemFaturasPorContrato($id_cliente_produto,$exibirEstornadas=false,$exibirCortesias=false) {
			$filtro = array("id_cliente_produto" => $id_cliente_produto);

			if( !$exibirEstornadas ) {
				$filtro["status"] = "!=:E";
			}
			
			if( !$exibirCortesias ) {
				$filtro["valor"] = ">:0";
			}
			
			$faturas = $this->cbtb_fatura->obtem($filtro, "data DESC");

			$hoje = date("Y-m-d");
			$base =  MData::ptBR_to_ISO($hoje);
			
			//echo "BASE: $base<br>\n";
			
			
			list($aB,$mB,$dB) = explode("-",$base);
			
			$base = mktime(0,0,0,$mB,$dB,$aB);
			//echo "BASE: $base<br>\n"; 


			//echo "HOJE: $hoje<br>\n";			
			list($aH,$mH,$dH) = explode("-",$hoje);
			$hoje = mktime(0,0,0,$mH,$dH,$aH);
			//echo "HOJE: $hoje<br>\n";			
			

			for($i=0;$i<count($faturas);$i++) {
				$faturas[$i]["valor"] = (float) $faturas[$i]["valor"];
				$faturas[$i]["acrescimo"] = (float) $faturas[$i]["acrescimo"];
				$faturas[$i]["desconto"] = (float) $faturas[$i]["desconto"];
				$faturas[$i]["valor_pago"] = (float) $faturas[$i]["valor_pago"];


				$faturas[$i]["valor_restante"] = $faturas[$i]["valor"] + $faturas[$i]["acrescimo"] - $faturas[$i]["desconto"] - $faturas[$i]["valor_pago"];
				

				list($aV,$mV,$dV) = explode("-",$faturas[$i]["data"]);
				$venc = mktime(0,0,0,$mV,$dV,$aV);

				//echo "VENC: " . $faturas[$i]["data"] . "(" . $venc . ")<br>";


				if( $faturas[$i]["data_pagamento"] ) {
					list($aP,$mP,$dP) = explode("-",$faturas[$i]["data_pagamento"]);
					$pgto = mktime(0,0,0,$mP,$dP,$aP);
				}

				// Utilizado por códigos como cancelamento e migração
				$faturas[$i]["estornavel"] = false;

				if( $faturas[$i]["status"] == "E" ) {
					$faturas[$i]["strstatus"] = "Estornada";
					$faturas[$i]["estornavel"] = false;
				}elseif( $faturas[$i]["status"] == "P" ) {
					$faturas[$i]["strstatus"] = "Pago";

					if( $pgto > $venc ) {
						$faturas[$i]["strstatus"] = "Pago com atrazo";
					}


				} else {
					if( $faturas[$i]["status"] == "A" ) {
						$faturas[$i]["estornavel"] = true;
					}
					
					// if( $venc < $hoje ) {
					if( $venc < $base ) {
						$faturas[$i]["strstatus"] = "Em atrazo";
						$faturas[$i]["estornavel"] = false;
					} else {
						$faturas[$i]["strstatus"] = "A vencer";
					}
				}

			}

			return($faturas);




		}

		/*
		 * Obtem Faturas
		 */
		public function obtemFaturas ($id_cliente, &$tem_carne = "", $id_cliente_produto = "", $id_forma_pagamento = "",$id_carne = "")
		{
			if ($id_forma_pagamento > 0 && $id_cliente_produto > 0) {
				if ($id_carne > 0) {
					// obtem faturas do carne
					$tem_carne = true;
					return ($this->cbtb_fatura->obtemFaturas (null, $id_cliente_produto, $id_carne));
				}
				else {
					$formaPagto = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);
					if ($formaPagto ['carne'] == 't') {
						// obtem carne
						$tem_carne = true;

						return ($this->cbtb_carne->obtemCarnes ($id_cliente_produto));
					}
					else {
						$tem_carne = false;
					}
				}
			}
			// obtem todas faturas do cliente.
			return ($this->cbtb_fatura->obtemFaturas ($id_cliente, $id_cliente_produto));
		}

		public function migrarFatura($id_cobranca,$id_cliente_produto) {
			$filtro = array("id_cobranca" => $id_cobranca);
			$dados = array("id_cliente_produto" => $id_cliente_produto);

			$fatura = $this->cbtb_fatura->obtemUnico(array("id_cobranca"=> $id_cobranca));
			$descricao = $fatura["descricao"];
			$descricao .= "\n\n*** MIGRADA DO CONTRATO ".$fatura["id_cliente_produto"] ." ***";
			$dados["descricao"] = $descricao;

			return($this->cbtb_fatura->altera($dados,$filtro));
		}

		public function obtemFatura ($id_cliente_produto, $data) {
			return ($this->cbtb_fatura->obtemUnico (array ("id_cliente_produto" => $id_cliente_produto, "data" => $data)));
		}
		
		public function migrarContrato($id_cliente_produto,$novo_id_cliente_produto,$admin) {
			$filtro = array("id_cliente_produto" => $id_cliente_produto);
			$dados = array(
							"migrado_para" => $novo_id_cliente_produto,
							"migrado_em" => "=now",
							"migrado_por" => $admin,
							"status" => "M"
							);

			$this->cbtb_contrato->altera($dados,$filtro);
			//
		}

		public function obtemFaturasAtrasadasPorPeriodo($periodo){
			$rs = $this->cbtb_fatura->obtemFaturasAtrasadasPorPeriodo($periodo);
			$return = array();
			foreach($rs as $row){
				$ano = $row["ano"];
				$mes = $row["mes"];
				$return[$ano][$mes] = $row["num_contratos"];
			}
			return $return;
		}


		public function obtemFaturasAtrasadasDetalhes($periodo){

			$retorno = $this->cbtb_fatura->obtemFaturasAtrasadasDetalhes($periodo);

			$contas = VirtexModelo::factory("contas");
			for($i=0;$i<count($retorno);$i++) {
				$cnt = $contas->obtemContasPorContrato($retorno[$i]["id_cliente_produto"]);
				$retorno[$i]["contas"] = $cnt;
			}
			return $retorno;

		}

		public function obtemStatusFatura(){
			return $this->cbtb_fatura->enumStatusFatura();
		}

		public function obtemFaturamentoPorPeriodo($periodo){
			return $this->cbtb_fatura->obtemFaturamentoPorPeriodo($periodo);
		}
		
		public function obtemFaturamentoPorMes($ano,$mes) {
			return $this->cbtb_fatura->obtemFaturamentoPorMes($ano,$mes);
		}

		public function obtemFaturamento($tipo="anual",$data='') {
			return $this->cbtb_fatura->obtemRecebimentos($tipo,$data);
		}
		
		public function obtemRecebimentos($tipo="anual",$data='') {
			return $this->cbtb_fatura->obtemRecebimentos($tipo,$data);
		}

		public function obtemFaturamentoPorProduto($ano_select){
			return $this->cbtb_fatura->obtemFaturamentoPorProduto($ano_select);
		}
		public function obtemFaturamentoComparativo($ano){
			return $this->cbtb_fatura->obtemFaturamentoComparativo($ano);		
		}

		public function obtemPrevisaoFaturamento($ano_select) {
			return ($this->cbtb_fatura->obtemPrevisaoFaturamento($ano_select));
		}

		public function obtemFaturaPorIdCobranca ($id_cobranca) {
			return ($this->cbtb_fatura->obtemUnico (array ("id_cobranca" => $id_cobranca)));
		}

		public function obtemFaturaPelaLinhaDigitavel ($codigo) {
			return ($this->cbtb_fatura->obtemUnico (array ("linha_digitavel" => $codigo)));
		}

		public function obtemFaturaPeloCodigoBarras ($codigo) {
			return ($this->cbtb_fatura->obtemUnico (array ("cod_barra" => trim($codigo))));
		}
		
		public function obtemFaturaPeloNossoNumero ($nosso_numero) {
			return ($this->cbtb_fatura->obtemUnico (array ("nosso_numero" => (int)$nosso_numero)));
		}
		
		public function alteraNossoNumero($id_cobranca,$nosso_numero) {
			$dados = array("nosso_numero" => (int)$nosso_numero, "nosso_numero_banco" => (int)$nosso_numero);
			$filtro = array("id_cobranca" => (int)$id_cobranca);
			
			return($this->cbtb_fatura->altera($dados,$filtro));
	
		}
		

		public function obtemAnosFatura() {
			return $this->cbtb_fatura->obtemAnosFatura();
		}
		
		public function obtemReagendamento() {
			$retorno = $this->lgtb_reagendamento->obtemReagendamento();
			return $retorno;
			
		}
		
		public function gravarLogRetorno($formato, $arquivo_enviado, $arquivo, $admin) {
			$dados['datahora'] = '=now';
			$dados['formato'] = $formato;
			$dados['arquivo_enviado'] = $arquivo_enviado;
			$dados['arquivo'] = $arquivo;
			$dados['id_admin'] = $admin['id_admin'];
					
			return($this->cbtb_retorno->insere($dados));
		}

		public function atualizaLogRetorno($id_retorno, $numero_total_registros, $numero_registros_processados, $numero_registros_com_erro, $numero_registros_sem_correspondencia, $data_geracao) {
			$data['numero_total_registros'] = $numero_total_registros;
			$data['numero_registros_processados'] = $numero_registros_processados;
			$data['numero_registros_com_erro'] = $numero_registros_com_erro;
			$data['numero_registros_sem_correspondencia'] = $numero_registros_sem_correspondencia;
			
			$data['data_geracao'] = $data_geracao ? $data_geracao : '=now';
			
			//echo "<pre>ATLR: \n"; 
			//print_r($data);
			//echo "</pre>"; 
		    return($this->cbtb_retorno->altera($data,array("id_retorno" => $id_retorno)));
		}
		
		
		
		
		
		
		public function amortizarFatura($id_cobranca, $desconto, $acrescimo, $amortizar, $data_pagamento, $reagendar,
					$reagendamento, $observacoes, $admin=array(), $id_retorno = "", $data_credito = ""){

			$contas = VirtexModelo::factory("contas");
			$cobranca = VirtexModelo::factory("cobranca");
			$caixa = VirtexModelo::factory("caixa");

			$fatura = $this->obtemFaturaPorIdCobranca($id_cobranca);
			$preferenciasCobranca = $this->preferencias->obtemPreferenciasCobranca();
			
			if( !$data_credito ) $data_credito = $data_pagamento;
			
			$data = array();

			if(	$fatura["status"] == PERSISTE_CBTB_FATURAS::$CANCELADA or
				$fatura["status"] == PERSISTE_CBTB_FATURAS::$ESTORNADA ) {
				if( $id_retorno > 0 ) {
					// Coloca o status como pendente
					//$data["status"] = PERSISTE_CBTB_FATURAS::$ABERTA;
				} else {
					throw new ExcecaoModeloValidacao(ExcecaoModeloValidacao::$EXC_FAT_CANCELADA,"Fatura cancelada/estornada");
				}
			} elseif( $fatura["status"] == PERSISTE_CBTB_FATURAS::$PAGA ) {				
			
				// echo "FATURA PAGA: $id_cobranca / $id_retorno <br>\n";
			
				//if( $id_retorno > 0 ) {
				//	// Estorna a fatura (e o fluxo).
				//	$this->estornaPagamentoFatura($id_cobranca,true);
				//} else {
					throw new ExcecaoModeloValidacao (ExcecaoModeloValidacao::$EXC_FAT_JA_PAGA, "Fatura ja foi paga.");
				//}
			}

			$totalDevido = $fatura["valor"] - $desconto + $acrescimo;
			
			
			
			/* Grava codigo retorno */
			if ($id_retorno > 0)
				$data['id_retorno'] = $id_retorno;

			if($amortizar > 0) {
				$data["valor_pago"] = $fatura["valor_pago"] + $amortizar;
				if($totalDevido == $amortizar){
					$data["status"] = PERSISTE_CBTB_FATURAS::$PAGA;
					$data["pagto_parcial"] = 0;	
				} elseif($amortizar < $totalDevido) {
					$data["status"] = PERSISTE_CBTB_FATURAS::$PARCIAL;
					$data["pagto_parcial"] = $data["valor_pago"];
				} elseif($amortizar > $totalDevido){
					 if( $id_retorno ) {
					 	// Joga como acrescimo. (junta com os acrescimos fornecidos). (quando enviado pelo banco).
					 	$acrescimo += $amortizar - $totalDevido;
					 	$data["status"] = PERSISTE_CBTB_FATURAS::$PAGA;
					 } else {
					 	throw new ExcecaoModeloValidacao (ExcecaoModeloValidacao::$EXC_FAT_VALOR_EXCEDE_PENDENTE,"Valor a receber excede o valor pendente!");
					 }	 
				}

			} elseif($amortizar == 0) {
				if($totalDevido == 0){ // desconto de 100% sobre o valor devido
					$data["status"] = PERSISTE_CBTB_FATURAS::$PAGA;
				} else {
					$data["status"] = $fatura["status"];
				}
			} else {
				throw new ExcecaoModeloValidacao (ExcecaoModeloValidacao::$EXC_FAT_VALOR_NEGATIVO,"Valor a receber nao pode ser negativo!");
			}

			$data["desconto"] = $desconto;
			$data["acrescimo"] = $acrescimo;
			$data["data_pagamento"] = $data_pagamento;
			$data["observacoes"] = $observacoes;

			if($reagendar and $data["status"] != PERSISTE_CBTB_FATURAS::$PAGA){
				$data["reagendamento"] = $reagendamento;
				
				$dadosLog = array("id_cliente_produto" => $fatura["id_cliente_produto"], 
									"data" => $fatura["data"], 
									"data_reagendamento" => "=now",
									"data_para_reagendamento" => $reagendamento,
									"admin" => ((int)@$admin["id_admin"]));
				
				$lgtb_reagendamento = VirtexPersiste::factory("lgtb_reagendamento");
				$lgtb_reagendamento->insere($dadosLog);
				
			}
			
			$seek["id_cobranca"] = $id_cobranca;
					
			$this->cbtb_fatura->altera($data,$seek);
			
			if( $id_retorno ) {
				$caixa->pagamentoViaRetorno($amortizar,$data_pagamento,$data_credito,$id_cobranca,$id_retorno);
			} else {
				$caixa->pagamentoComDinheiro($amortizar,$data_credito,$id_cobranca,$admin["admin"]);
			}
			
			//Remanejamento e verificação de contas bloqueadas
			//$contrato = $this->obtemContrato($fatura["id_cliente_produto"]);
			$atrasados = $this->obtemContratosFaturasAtrasadasBloqueios($preferenciasCobranca["carencia"],$preferenciasCobranca["tempo_banco"],$fatura["id_cliente_produto"],false); 

			$permitir_desbloqueio = false;
			if(!count($atrasados)) { 
				$permitir_desbloqueio = true;
			}
			
			
			
			//for ($i=0; $i<count($atrasados); $i++) {
			//	if($atrasados[$i]["id_cliente_produto"] == $fatura["id_cliente_produto"]) $permitir_desbloqueio = false;
			//}
			
			//echo "Permitir Desbloqueio: $permitir_desbloqueio<br>\n";
			

			if($permitir_desbloqueio) {
				$contas_bloqueadas = $contas->obtemContasBloqueadasPeloContrato($fatura["id_cliente_produto"], $tipo_produto);			
				
				//echo "<pre>CB: \n";
				//print_r($contas_bloqueadas);
				//echo "<pre>";
				
				foreach( $contas_bloqueadas as $i => $conta ) {
					switch(trim($conta["tipo_conta"])) {
						case "BL":
							$cnt = $contas->obtemContaPeloId($conta["id_conta"]);
							$contas->alteraContaBandaLarga($conta["id_conta"], NULL, 'A',$cnt["observacoes"],$cnt["conta_mestre"],$cnt["id_pop"],$cnt["id_nas"],$cnt["upload_kbps"],$cnt["download_kbps"],$cnt["mac"]);
							break; 
						case "D":
							$contas->alteraContaDiscado($conta["id_conta"], NULL, 'A');
							break;
						case "H":
							$contas->alteraContaHospedagem($conta["id_conta"], NULL, 'A');
							break;
					}
					$contas->gravaLogMudancaStatusConta($conta["id_cliente_produto"], $conta["username"], $conta["dominio"], $conta["tipo_conta"], $admin["id_admin"], @$admin["ipaddr"]);
					$contas->gravaLogBloqueioAutomatizado($conta["id_cliente_produto"], 'A', $admin["admin"], 'Ativacao por motivo de pagamento de fatura');
				}
									
			}

			return true;
		}


		public function cadastraLoteCobranca($data_referencia, $periodo, $id_admin,$id_forma_pagamento) {
			$dados = array (	"data_referencia" => $data_referencia,
								"data_geracao" => '=now',
								"periodo" => $periodo,
								"id_forma_pagamento"=>$id_forma_pagamento,
								"id_admin" => $id_admin
							);

			return ($this->cbtb_lote_cobranca->insere($dados));
		}
		
		public function cadastraRemessa($arquivo, $id_remessa) {
			return($this->cbtb_remessa->insere(array("datahora" => "=now", "arquivo" => $arquivo, "id_remessa" => $id_remessa)));
		}	
		
		public function obtemRemessaPeloNomeArquivo($arquivo) {
			$arq = "%:%" . $arquivo . ".txt";
			return($this->cbtb_remessa->obtemUnico(array("arquivo" => $arq)));
		}

		public function obtemFaturasPorPeriodoSemCodigoBarra($data_referencia, $periodo) {
			return ($this->cbtb_fatura->obtemFaturasPorPeriodoSemCodigoBarra($data_referencia, $periodo));
		}
		
		public function obtemFaturasPorPeriodoParaRemessa($data_referencia, $periodo) {
			$faturas = $this->cbtb_fatura->obtemFaturasPorPeriodoParaRemessa($data_referencia, $periodo);
			
			$clientes = VirtexModelo::factory("clientes");

			$preferencias = VirtexModelo::factory("preferencias");
			
			for($i=0;$i<count($faturas);$i++) {
				$cp = $this->obtemClienteProduto($faturas[$i]["id_cliente_produto"]);
				
				// Contrato
				$faturas[$i]["contrato"] = $this->obtemContratoPeloId($faturas[$i]["id_cliente_produto"]);
				
				// Cliente
				$cliente = $clientes->obtemPeloId($cp["id_cliente"]);
				$faturas[$i]["cliente"] = $cliente;
				
				$endereco_cobranca = $this->obtemEnderecoCobranca($faturas[$i]["id_cliente_produto"]);
				
				if( !count($endereco_cobranca) ) {
					$endereco_cobranca = array(
												"id_endereco_cobranca" => 0,
												"id_cliente_produto" => $faturas[$i]["id_cliente_produto"],
												"endereco" => $cliente["endereco"],
												"complemento" => $cliente["complemento"],
												"bairro" => $cliente["bairro"],
												"cep" => $cliente["cep"],
												"id_cidade" => $cliente["id_cidade"],
												"id_condominio_cobranca" => $cliente["id_condominio"],
												"id_bloco_cobranca" => $cliente["id_bloco"],
												"apto_cobranca" => $cliente["apto"]
												);
					
					//
				
				
				
				}
				
				$cidade = $preferencias->obtemCidadePeloId($endereco_cobranca["id_cidade"]);
				$endereco_cobranca["cidade"] = $cidade;
				
				$faturas[$i]["endereco_cobranca"] = $endereco_cobranca;
				
				
				
			}
			
			
			return ($faturas);
		}

		public function obtemFaturasPorPeriodoSemCodigoBarraPorTipoPagamento($data_referencia, $periodo, $id_forma_pagamento) {
					return ($this->cbtb_fatura->obtemFaturasPorPeriodoSemCodigoBarraPorTipoPagamento($data_referencia, $periodo, $id_forma_pagamento));
		}

		public function cadastraLoteFatura($id_remessa, $id_cobranca) {
			$dados = array(	"id_remessa" => $id_remessa,
							"id_cobranca"=> $id_cobranca
							);

			$this->cbtb_lote_fatura->insere($dados);
		}	
		
		public function InsereCodigoBarraseLinhaDigitavel($codigo_barra, $linha_digitavel, $id_cobranca) {
			return ($this->cbtb_fatura->InsereCodigoBarraseLinhaDigitavel($codigo_barra, $linha_digitavel, $id_cobranca));
		}

		public function obtemUltimasRemessas($quantidade) {
			return ($this->cbtb_lote_cobranca->obtemUltimasRemessas($quantidade));
		}		
		
		public function obtemRemessaPeloId($id_remessa) {
			return ($this->cbtb_remessa->obtemUnico(array("id_remessa" => $id_remessa)));
		}
		
		public function obtemUltimosRetornos($quantidade) {
			$extra = "datahora DESC";
			$extra .= ($quantidade) ? " LIMIT $quantidade" :  "";
			return ($this->cbtb_retorno->obtem("", $extra));
		}
		
		public function obtemRetornoPeloArquivoEnviado($arquivo_nome) {
			$dados = array("arquivo_enviado" => $arquivo_nome);
			$retorno = $this->cbtb_retorno->obtemUnico($dados);
			return $retorno;
		}
		
		public function obtemRetornoPeloId($id_retorno) {
			$dados = array("id_retorno" => $id_retorno);
			$retorno = $this->cbtb_retorno->obtemUnico($dados);
			return $retorno;
		}
		

		public function obtemUltimasRemessasCriadas($quantidade) {
			$extra = "datahora DESC";
			$extra .= ($quantidade) ? " LIMIT $quantidade" :  "";
			return ($this->cbtb_remessa->obtem("", $extra));
		}	
		

		public function obtemFaturasPorRemessa($id_remessa) {
			return ($this->cbtb_fatura->obtemFaturasPorRemessa($id_remessa));
		}

		public function obtemContratosFaturasAtrasadas() {
			return ($this->cbtb_contrato->obtemContratosFaturasAtrasadas());
		}
		
		public function obtemContratosVigenciaZero($id_forma_pagamento='') {
			$filtro = array(	"vigencia" => "0",
								"status" => "A"
							);
							
			if ($id_forma_pagamento) {
				$filtro["id_forma_pagamento"] = $id_forma_pagamento;
			}
			
			return($this->cbtb_contrato->obtem($filtro));
		}
		
		public function countContratosFaturasAtrasadasBloqueios() {
			$preferenciasCobranca = $this->preferencias->obtemPreferenciasCobranca();
			$carencia = $preferenciasCobranca["carencia"];
			$tempo_banco=2;
			$id_cliente_produto="";
			$contasAtivas=true;
			$inadimplentes=false;
			
			return ($this->cbtb_contrato->obtemContratosFaturasAtrasadasBloqueios($carencia,$tempo_banco,$id_cliente_produto,$contasAtivas,$inadimplentes,true));
			
		}
		
		public function obtemContratosFaturasAtrasadasBloqueios($carencia="",$tempo_banco=2,$id_cliente_produto="",$contasAtivas=true,$inadimplentes=false) {	
			$preferenciasCobranca = $this->preferencias->obtemPreferenciasCobranca();
			if(!$carencia) $carencia = $preferenciasCobranca["carencia"];
			return ($this->cbtb_contrato->obtemContratosFaturasAtrasadasBloqueios($carencia,$tempo_banco,$id_cliente_produto,$contasAtivas,$inadimplentes));
		}
		
		public function obtemFaturaPorContratoPeriodo($data_referencia, $periodo, $id_contrato) {
			return ($this->cbtb_fatura->obtemFaturaPorContratoPeriodo($data_referencia, $periodo, $id_contrato));
		}			
		
		public function obtemFaturasPorRemessaGeraBoleto($id_remessa) {
			return ($this->cbtb_fatura->obtemFaturasPorRemessaGeraBoleto($id_remessa));
		}
		
		public function obtemContrato($id_cliente_produto) {
			$retorno = $this->cbtb_contrato->obtemContrato($id_cliente_produto);
			return $retorno;
		}
		
		
		public function gravaComissao($id_cliente_produto, $id_admin, $valor, $status='A') {
			$dados = array ("id_cliente_produto" => $id_cliente_produto, "id_admin" => $id_admin, "valor" => $valor, "status" => $status);
			$retorno = $this->cbtb_comissao->insere($dados);
			return $retorno;
		}
		

		// TODO LIST
		public function obtemContratosParaRenovacao($cnt=false) {
			$retorno = $this->cbtb_contrato->obtemContratosRenovacao($cnt);
			
			if( $cnt ) {
				return($retorno);
			}
			
			
			// TODO: LISTAR AS FATURAS PENDENTES.
			for($i=0;$i<count($retorno);$i++) {
				$retorno[$i]["faturas_pendentes"] = $this->cbtb_fatura->obtemNumeroFaturasPendentes($retorno[$i]["id_cliente_produto"]);

			}
	
			return($retorno);
		}
		
		/**
		 * Registro de renovação de contrato.
		 */
		public function insereLogRenovacao($id_cliente_produto,$data_renovacao,$data_proxima_renovacao,$historico="") {
			$dados = array("id_cliente_produto" => $id_cliente_produto, "data_renovacao" => $data_renovacao, "data_proxima_renovacao" => $data_proxima_renovacao, "historico" => $historico);
			return($this->lgtb_renovacao->insere($dados));
		}
		
		/**
		 * Renovacao de Contrato
		 *
		 * Atualiza as informações do contrato para renovacao.
		 */
		public function renovaContrato($id_cliente_produto,$data_renovacao,$pagamento,$desconto_promo,$periodo_desconto) {
		
			$dados = array("data_renovacao" => $data_renovacao, "pagamento" => $pagamento, "desconto_promo" => $desconto_promo, "periodo_desconto" => $periodo_desconto);
			$filtro = array("id_cliente_produto" => $id_cliente_produto);
			
			$this->cbtb_contrato->altera($dados,$filtro);
			
		
			
		}



		/**
		 * cadastraFaturas
		 *
		 * Cadastra as faturas com base na forma de pagamento.
		 *
		 */
		
		function cadastraFaturas($faturas,$formaPagto,$produto,$data_geracao,$id_cliente_produto,$id_cliente) {

			$id_cobranca = 0;
			$gera_carne = false;
			
			$prefProv = $this->preferencias->obtemPreferenciasProvedor();
			
			$id_forma_pagamento = $formaPagto['id_forma_pagamento'];
			
			if ($formaPagto ['carne'] == 't' && count ($faturas) > 0) {
				$gera_carne = true;
				$soma_fatura = 0;
				foreach ($faturas as $fatura){
					$soma_fatura += $fatura ["valor"];
				}

				$dados = array (
					'data_geracao' => $data_geracao,
					'id_cliente_produto' => $id_cliente_produto,
					'valor' => $soma_fatura,
					'vigencia' => count ($faturas),
					'id_cliente' => $id_cliente,
				);

				$id_cbtb_carne = $this->cbtb_carne->insere ($dados);
			}

			for( $i=0;$i<count($faturas);$i++) {
				$fatura = $faturas[$i];
				$cod_barra = "";
				$linha_digitavel = "";
				$nosso_numero = "";
				

				if ($gera_carne) {
					// gera codigo de barras
					$nosso_numero = $this->pftb_forma_pagamento->obtemProximoNumeroSequencial ($id_forma_pagamento);

					// ($banco,$agencia,$conta,$carteira,$convenio,$vencimento,$valor,$id,$moeda=9,$cnpj_ag_cedente="",$codigo_cedente="",$operacao_cedente="")

					

					switch ($formaPagto ["tipo_cobranca"]) {
						case "PC":
							$cod_barra = MArrecadacao::codigoBarrasPagContas ($fatura ["valor"], $prefProv ['cnpj'], $nosso_numero, $fatura ['data']);
							$linha_digitavel = MArrecadacao::linhaDigitavel ($cod_barra);
							break;
						case "BL":
							$boleto = MBoleto::factory ($formaPagto["codigo_banco"],$formaPagto["agencia"],$formaPagto["conta"],$formaPagto["carteira"],$formaPagto["convenio"],$fatura['data'],$fatura ['valor'],$nosso_numero,self::$moeda,$formaPagto ['cnpj_ag_cedente'],$formaPagto ['codigo_cedente'],$formaPagto ['operacao_cedente']);
							$cod_barra = $boleto->obtemCodigoBoleto ();
							$linha_digitavel = $boleto->obtemLinhaDigitavel();
							break;
						case "MO":
							// Carnê genérico
							$carne = new MCarne($fatura["valor"],$id_cliente_produto,$nosso_numero,$fatura['data']);
							$cod_barra = $carne->obtemCodigoBarras();
							$linha_digitavel = $carne->obtemLinhaDigitavel();
							break;
					}

				}
				
				$this->cadastraFatura($id_cliente_produto, $id_cobranca, $fatura["data"], $fatura["valor"], $id_forma_pagamento, $produto["nome"], $id_cbtb_carne, $nosso_numero, $linha_digitavel, $cod_barra);
				

			}
			
			// Caso tenha gerado o carnê retorna o número do mesmo, caso contrário retorna vazio.
			return($gera_carne?$id_cbtb_carne:"");
			
		}
		
		public function registraErroRetorno($id_retorno,$id_cobranca,$codigo_barra,$mensagem,$data_pagamento=null,$valor_recebido=null,$data_credito=null) {
			$dados = array("id_retorno" => $id_retorno, "codigo_barra" => $codigo_barra, "mensagem" => $mensagem);
			
			if( $id_cobranca ) {
				$dados["id_cobranca"] = $id_cobranca;
			}
			
			if( $data_pagamento ) {
				$dados["data_pagamento"] = $data_pagamento;
			}
			
			if( $valor_recebido ) {
				$dados["valor_recebido"] = $valor_recebido;
			}
			
			if( $data_credito ) {
				$dados["data_credito"] = $data_credito;
			}
			
			return($this->cbtb_retorno_erro->insere($dados));
		}
		
		public function estornaErrosRetorno($id_retorno) {
			return($this->cbtb_retorno_erro->exclui(array("id_retorno" => $id_retorno)));
		}

		public function obtemErrosRetorno($id_retorno) {
			return($this->cbtb_retorno_erro->obtem(array("id_retorno" => $id_retorno)));
		}
		
		public function obtemCarnePeloId($id_carne) {
			return($this->cbtb_carne->obtemUnico(array("id_carne"=>$id_carne)));
		}
		
		/**
		 * Gera o código de impressão do carne:
		 *
		 */
		public function obtemCodigoImpressaoCarne($id_carne) {
			$carne = $this->obtemCarnePeloId($id_carne);
			//
			
			if( empty($carne) ) return "";
			
			$fatorData = MBanco::fatorData($carne["data_geracao"]);			
			$idCarne   = sprintf("%06d",$id_carne);
			$idCliente = sprintf("%06d",$carne["id_cliente"]);
			$idClienteProduto = sprintf("%06d",$carne["id_cliente_produto"]);
			
			//$fatorData = 1234;
			
			$dvFator = MBanco::modulo10($fatorData);
			$dvCarne = MBanco::modulo10($idCarne);
			
			// $codigoImpressao = $fatorData . $dvFator . $idCarne . $dvCarne;
			$codigoImpressao = $dvFator . $idCarne;
			$dvGeral = MBanco::Modulo11($codigoImpressao);
			$lixo = '1';
			$codigoImpressao = $lixo . $codigoImpressao . $dvGeral;
			$codigoHexa = strtoupper(dechex($codigoImpressao));
			
			return($codigoHexa);
		
		}

		
		/**
		 * Obtem um Carne pelo Codigo de Impressao.
		 */
		public function obtemCarnePeloCodigoImpressao($codigoHexa) {
			$codigoHexa = strtoupper($codigoHexa);
			$codigoImpressao = hexdec($codigoHexa);
			$codigoImpressao = trim(str_replace(" ","",str_replace(".","",str_replace("-","",$codigoImpressao))));			

			$lixo      = substr($codigoImpressao,0,1);
			$dvFator   = substr($codigoImpressao,1,1);
			$idCarne   = substr($codigoImpressao,2,6);
			$dvGeral   = substr($codigoImpressao,8,1);
			
			$idCarne = (int)$idCarne;
			$ciCheck = $this->obtemCodigoImpressaoCarne($idCarne);
			
			return( $codigoHexa == $ciCheck ? $this->obtemCarnePeloId($idCarne) : array() );
		
		}


		public function obtemCarnesSemConfirmacao($id_carne="",$cnt=false) {
			return($this->cbtb_carne->obtemCarnesSemConfirmacao($id_carne,$cnt));
		}

		public function confirmaImpressaoCarne($id_carne, $id_admin, $codigo_verificacao ) {
			$dados = array("id_carne" => $id_carne, "codigo_verificacao" => $codigo_verificacao,"datahora" => "=now");
			if( (int)$id_admin ) {
				$dados["id_admin"] = (int)$id_admin;
			}
			
			return($this->cbtb_carne_impressao->insere($dados));
			
		}
		
		public function obtemNumeroContratosAtivosPorTipo($id_cliente) {
			return($this->cbtb_contrato->obtemNumeroContratosAtivosPorTipo($id_cliente));
		}
		
		









	}

