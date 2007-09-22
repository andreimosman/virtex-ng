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
		
		protected $preferencias;

		
		public function __construct() {
			parent::__construct();
			$this->cbtb_cliente_produto = VirtexPersiste::factory("cbtb_cliente_produto");
			$this->cbtb_contrato = VirtexPersiste::factory("cbtb_contrato");
			$this->cbtb_endereco_cobranca = VirtexPersiste::factory("cbtb_endereco_cobranca");

			$this->cbtb_fatura = VirtexPersiste::factory("cbtb_faturas");
			$this->cbtb_carne = VirtexPersiste::factory("cbtb_carne");

			$this->preferencias = VirtexModelo::factory("preferencias");

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
		 * Gera a lista de faturas a serem criadas com base nas informações de pagamento.
		 *
		 * Considerações:
		 *   -> data de vencimento
		 *   -> pagamento (pré ou pós pago)
		 *   -> taxa de instalação
		 *   -> comodato
		 *   -> 
		 */
		public function gerarListaFaturas($pagamento,$data_contratacao,$vigencia,$dia_vencimento,$valor,$desconto_valor,$desconto_periodo,$tx_instalacao,$valor_comodato,$data_primeiro_vencimento,$faz_prorata,$limite_prorata) {
			$faturas = array();

 			$descontos_aplicados = 0;
			$meses_cobrados = 0;


			$data = MData::proximoDia($dia_vencimento,$data_contratacao);

			if( $pagamento == "POS" ) {
				// $data = MData::proximoDia($dia_vencimento,$data_contratacao);
				// Pagamento pós pago

        		$composicao = array();

        		if( $tx_instalacao ) {
					// Gerar fatura 0 com a taxa de instalaçao
					$composicao["instalacao"] = $tx_instalacao;
					$faturas[] = array("data"=>$data_contratacao,"valor" => $tx_instalacao,"composicao"=>$composicao);
				}
				$meses_cobrados++;
				

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
				
				$faturas[] = array("data"=>$data_contratacao,"valor" => $valor_fatura,"composicao" => $composicao);

				$valor_fatura = $valor+$valor_comodata;
				$composicao["prorata_plano"] = '';
				$composicao["prorata_comodato"] = '';
				$composicao["dias_prorata"] = '';
				$composicao["valor"] = $valor;
				$composicao["comodato"] = $valor_comodato;
				$composicao["instalacao"] = '';

				if( $desconto_valor && $desconto_periodo > 0 ) {
					$valor_fatura -= $desconto_valor;
					$descontos_aplicados++;
					$composicao["desconto"] = array("parcela" => $descontos_aplicados . "/" . $desconto_periodo, "valor" => $desconto_valor);
				}
				
			}
			

			
			for( ; $meses_cobrados < $vigencia ; $meses_cobrados++ ) {
				$got = false;
				$composicao = array();
				
				if( $pagamento == "POS" && $meses_cobrados == 1 ) {
					// Primeiro vencimento de pós-pago. Calcular pró-rata.
					$prorata = $this->prorata($data_contratacao,$data_primeiro_vencimento,$valor,$valor_comodato);
					if( ($prorata["dias_prorata"] > 0) && ($faz_prorata == 't') ) {
						// Pró-rata aplicável.
						$prorata_plano = $prorata["prorata_plano"];
						$prorata_comodato = $prorata["prorata_comodato"];
						$dias_prorata = $prorata["dias_prorata"];

						$data = $data_primeiro_vencimento;
            
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
					list($d,$m,$a) = explode("/", $data);
					$d = $dia_vencimento;
					
					// Incrementa 1 mês
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
		
		
		function novoContrato($id_cliente, $id_produto, $dominio, $data_contratacao, $vigencia, $pagamento, $data_renovacao, $valor_contrato, $username, $senha,
                          $id_cobranca, $status, $tx_instalacao, $valor_comodato, $desconto_promo, $desconto_periodo, $dia_vencimento, $primeira_fatura, $prorata, $limite_prorata,
                          $carencia, $id_prduto, $id_forma_de_pagamento, $pro_dados, $da_dados, $bl_dados, $cria_email, $dados_produto, $endereco_cobranca, $endereco_instalacao, $dados_conta) {
			
			$formaPagto = $this->preferencias->obtemFormaPagamento($id_forma_de_pagamento);
			
			
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

      $dados = array("id_cliente_produto" => $id_cliente_produto, "data_contratacao" => $data_contratacao, "vigencia" => $vigencia, "data_renovacao" => $data_renovacao,
      "valor_contrato" => $valor_contrato, "id_cobranca" => $id_cobranca, "status" => $status, "tipo_produto" => $dados_produto["tipo"], "valor_produto" => $dados_produto["valor"],
      "num_emails" => $dados_produto["num_emails"], "quota_por_conta" => $dados_produto["quota_por_conta"], "tx_instalacao" => $tx_instalacao, "comodato" => $comodato,
      "valor_comodato" => $valor_comodato, "desconto_promo" => $desconto_promo, "periodo_desconto" => $desconto_periodo, "hosp_dominio" => $hosp_dominio, "hosp_franquia_em_mb" => $hosp_franquia_em_mb,
      "hosp_valor_mb_adicional" => $hosp_valor_mb_adicional, "disc_franquia_horas" => $disc_franquia_horas, "disc_permitir_duplicidade" => $disc_permitir_duplicidade, "disc_valor_hora_adicional" => $disc_valor_hora_adicional,
      "bl_banda_upload_kbps" => $bl_banda_upload_kbps, "bl_banda_download_kbps" => $bl_banda_download_kbps, "bl_franquia_trafego_mensal_gb" => $bl_franquia_trafego_mensal_gb,
      "bl_valor_trafego_adicional_gb" => $bl_valor_trafego_adicional_gb, "cod_banco" => $pro_dados["codigo_banco"], "carteira" => $pro_dados["carteira"],
      "agencia" => $pro_dados["agencia"], "num_conta" => $pro_dados["num_conta"], "convenio" => $pro_dados["convenio"], "cc_vencimento" => "", "cc_numero" => "",
      "cc_operadora" => "", "db_banco" => "", "db_agencia" => "", "db_conta" => "", "vencimento" => $dia_vencimento, "carencia" => $carencia,
      "data_alt_status" => "", "id_produto" => $id_produto, "nome_produto" => $dados_produto["nome"], "descricao_produto" => $dados_produto["descricao"],
      "disponivel" => $dados_produto["disponivel"], "vl_email_adicional"  => $dados_produto["vl_email_adicional"], "permitir_outros_dominios" => $dados_produto["permitir_outros_dominios"],
      "email_anexado" => $dados_produto["email_anexado"], "numero_contas" => $dados_produto["numero_contas"], "valor_estatico" => $dados_produto["valor_estatico"],
      "da_cod_banco" => $da_dados["codigo_banco"], "da_carteira" => $da_dados["carteira"], "da_convenio" => $da_dados["convenio"], "da_agencia" => $da_dados["agencia"],
      "da_num_conta" => $da_dados["num_conta"], "bl_cod_banco" => $bl_dados["codigo_banco"], "bl_carteira" => $bl_dados["carteira"],
      "bl_convenio" => $bl_dados["convenio"], "bl_agencia" => $bl_dados["agencia"], "bl_num_conta" => $bl_dados["num_conta"], "id_forma_pagamento"=>$id_forma_de_pagamento);

      /*
      echo "<pre>";
      print_r($dados_produto);
      echo "</pre>";
      */
      $this->cbtb_contrato->insere($dados);
      
      
      $todas_faturas = $this->gerarListaFaturas($pagamento, $data_contratacao ,$vigencia, $dia_vencimento, $dados_produto["valor"], $desconto_promo, $desconto_periodo, $tx_instalacao, $valor_comodato, $primeiro_vencimento, $pro_rata, $limite_prorata);

      echo "argh";
      $id_cobranca = 0;
      
  

      // gera carne
      if ($formaPagto ['carne'] == 't' && count ($todas_faturas) > 0) {
      	$soma_fatura = 0;
      	foreach ($todas_faturas as $fatura)
      		$soma_fatura += $fatura ["valor"];
      	      
   	$dados = array (
   		'data_geracao' => $data_contratacao,
   		'id_cliente_produto' => $id_cliente_produto,
   		'valor' => $soma_fatura,
   		'vigencia' => count ($todas_faturas),
   		'id_cliente' => $id_cliente,   	
   	);   
      
      	$id_cbtb_carne = $this->cbtb_carne->insere ($dados);
      }
      
      foreach( $todas_faturas as $fatura ) {
        $this->cadastraFatura($id_cliente_produto, $id_cobranca, $fatura["data"], $fatura["valor"], $id_forma_de_pagamento, $dados_produto["nome"], $id_cbtb_carne);
      }
      /*
      echo "<pre>";
      print_r($faturas);
      echo "</pre>";
      */


      //$preferencias = VirtexModelo::factory('preferencias');
      //echo "<pre>";
      //print_r($this->preferencias);
      //echo "</pre>";
      
  		$prefGeral = $this->preferencias->obtemPreferenciasGerais();
      $dominio_padrao = $prefGeral["dominio_padrao"];

      $status_conta = "A";
      $obs = "";
      $conta_mestre = "t";
      
      $contas = VirtexModelo::factory("contas");
      
      switch($dados_produto["tipo"]) {
      
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
     


											
     if ( $cria_conta ) {
       $contas->cadastraContaEmail($username, $dominio_padrao, $senha, $id_cliente, $id_cliente_produto, $status_conta, $obs,
                                                                 $conta_mestre, $dados_produto["quota_por_conta"]);
     }

     //PAREI AQUI
     
      //grava endereco de cobranca
      $dados = $endereco_cobranca;
      $dados["id_cliente"] = $id_cliente;
      $dados["id_cliente_produto"] = $id_cliente_produto;
      
      $this->cbtb_endereco_cobranca->insere($dados);


      
		}


    protected function cadastraFatura($id_cliente_produto, $id_cobranca, $data, $valor, $id_forma_pagamento, $descricao = "",
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

	
	}


?>