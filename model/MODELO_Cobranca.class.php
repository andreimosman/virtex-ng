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
		protected $pftb_forma_pagamento;
		protected $cbtb_fatura;
		protected $preferencias;

		protected $cltb_cliente;
		protected $prtb_produto;

		protected $cbtb_endereco_cobranca;

		protected $cbtb_lote_cobranca;
		protected $cbtb_lote_fatura;

		protected static $moeda = 9;


		public function __construct() {
			parent::__construct();
			$this->cbtb_cliente_produto = VirtexPersiste::factory("cbtb_cliente_produto");
			$this->cbtb_contrato = VirtexPersiste::factory("cbtb_contrato");
			$this->cbtb_endereco_cobranca = VirtexPersiste::factory("cbtb_endereco_cobranca");

			$this->cbtb_fatura = VirtexPersiste::factory("cbtb_faturas");
			$this->cbtb_carne = VirtexPersiste::factory("cbtb_carne");

			$this->pftb_forma_pagamento = VirtexPersiste::factory("pftb_forma_pagamento");
			$this->preferencias = VirtexModelo::factory("preferencias");
			$this->cltb_cliente = VirtexPersiste::factory("cltb_cliente");
			$this->prtb_produto = VirtexPersiste::factory("prtb_produto");

			$this->cbtb_lote_cobranca = VirtexPersiste::factory("cbtb_lote_cobranca");
			$this->cbtb_lote_fatura = VirtexPersiste::factory("cbtb_lote_fatura");
		}

		public function obtemClienteProduto($id_cliente_produto) {
			return($this->cbtb_cliente_produto->obtemUnico(array("id_cliente_produto"=>$id_cliente_produto)));
		}

		public function cadastraEnderecoCobranca($id_cliente_produto,$endereco,$complemento,$bairro,$id_cidade,$cep,$id_cliente) {
			$dados = array(
							"id_cliente_produto" => $id_cliente_produto,
							"endereco" => $endereco,
							"complemento" => $complemento,
							"bairro" => $bairro,
							"id_cidade" => $id_cidade,
							"cep" => $cep,
							"id_cliente" => $id_cliente
							);
			$this->cbtb_endereco_cobranca->insere($dados);
		}

		public function obtemEnderecoCobranca($id_cliente_produto) {
			$filtro = array("id_cliente_produto" => $id_cliente_produto);
			return($this->cbtb_endereco_cobranca->obtemUnico($filtro));
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

        		if( ((float)$tx_instalacao) > 0 ) {
        			echo "TX!!";
					// Gerar fatura 0 com a taxa de instalaçao
					$composicao["instalacao"] = $tx_instalacao;
					$faturas[] = array("data"=>$data_contratacao,"valor" => $tx_instalacao,"composicao"=>$composicao);
					$meses_cobrados++;
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
                          $carencia, $id_prduto, $id_forma_pagamento, $pro_dados, $da_dados, $bl_dados, $cria_email, $dados_produto, $endereco_cobranca, $endereco_instalacao,
						  $dados_conta, &$gera_carne = false) {
			/*echo "<pre>+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
			echo "MODELO_Conbranca::novoContrato()\n";
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
			/*exit;*/
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
							"pagamento" => $pagamento);

			if( $id_forma_pagamento ) {
				$dados["id_forma_pagamento"]=$id_forma_pagamento;
			}
			$this->cbtb_contrato->insere($dados);
			$todas_faturas = ((float)$dados_produto["valor"] > 0) ? $this->gerarListaFaturas($pagamento, $data_contratacao ,$vigencia, $dia_vencimento, $dados_produto["valor"], $desconto_promo, $desconto_periodo, $tx_instalacao, $valor_comodato, $primeiro_vencimento, $pro_rata, $limite_prorata) : array();

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

			$this->cadastraEnderecoCobranca($id_cliente_produto,$dados["endereco"],$dados["complemento"],$dados["bairro"],$dados["id_cidade"],$dados["cep"],$id_cliente);

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

				$contas->cadastraEnderecoInstalacao($id_conta,$endereco_instalacao["endereco"],$endereco_instalacao["complemento"],$endereco_instalacao["bairro"],$endereco_instalacao["id_cidade"],$endereco_instalacao["cep"],$id_cliente);

			}

			return($id_cliente_produto);


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

	    public function cancelaContrato($id_cliente_produto) {
			$dados = array("status" => "C","data_alt_status" => "=now");
			$this->cbtb_contrato->altera($dados,array("id_cliente_produto" => $id_cliente_produto));
	    }

		public function obtemContratos ($id_cliente,$status="",$tipo="")
		{
			$res = $this->cbtb_cliente_produto->obtemContratos ($id_cliente,$status,$tipo);
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

		public function estornaFatura($id_cobranca) {
			$filtro = array("id_cobranca" => $id_cobranca);
			$dados = array("status" => "E");
			return($this->cbtb_fatura->altera($dados,$filtro));
		}

		public function obtemFaturasPorCarne($id_carne) {
			$filtro = array("status" => "A", "id_carne" => $id_carne);
			return($this->cbtb_fatura->obtem($filtro));
		}


		/**
		 *
		 */
		public function obtemFaturasPorContrato($id_cliente_produto,$exibirEstornadas=false) {
			$filtro = array("id_cliente_produto" => $id_cliente_produto);

			if( !$exibirEstornadas ) {
				$filtro["status"] = "!=:E";
			}
			$faturas = $this->cbtb_fatura->obtem($filtro);

			$hoje = date("Y-m-d");
			list($aH,$mH,$dH) = explode("-",$hoje);
			$hoje = mktime(0,0,0,$mH,$dH,$aH);

			for($i=0;$i<count($faturas);$i++) {
				$faturas[$i]["valor"] = (float) $faturas[$i]["valor"];
				$faturas[$i]["acrescimo"] = (float) $faturas[$i]["acrescimo"];
				$faturas[$i]["desconto"] = (float) $faturas[$i]["desconto"];
				$faturas[$i]["valor_pago"] = (float) $faturas[$i]["valor_pago"];


				$faturas[$i]["valor_restante"] = $faturas[$i]["valor"] + $faturas[$i]["acrescimo"] - $faturas[$i]["desconto"] - $faturas[$i]["valor_pago"];

				list($aV,$mV,$dV) = explode("-",$faturas[$i]["data"]);
				$venc = mktime(0,0,0,$mV,$dV,$aV);


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

					if( $venc < $hoje ) {
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
			return ($this->cbtb_fatura->obtemFaturas ($id_cliente));
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
			//echo "<PRE>";
			//print_r($retorno);
			//echo "</PRE>";
			return $retorno;

		}

		public function obtemStatusFatura(){
			return $this->cbtb_fatura->enumStatusFatura();
		}

		public function obtemFaturamentoPorPeriodo($periodo){
			return $this->cbtb_fatura->obtemFaturamentoPorPeriodo($periodo);

			//echo "<PRE>";
			//	print_r($periodo);
			//echo "</PRE>";
		}

		public function obtemFaturamentoPorProduto($ano_select){
			return $this->cbtb_fatura->obtemFaturamentoPorProduto($ano_select);
			//echo "<PRE>";
			//	print_r($ano_select);
			//echo "</PRE>";
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
			return ($this->cbtb_fatura->obtemUnico (array ("cod_barra" => $codigo)));
		}

		public function obtemAnosFatura() {
			return $this->cbtb_fatura->obtemAnosFatura();
		}

		public function amortizarFatura($id_cobranca, $desconto, $acrescimo, $amortizar, $data_pagamento, $reagendar,
					$reagendamento, $observacoes, $admin=array()){

			$fatura = $this->obtemFaturaPorIdCobranca($id_cobranca);
			$data = array();

			if(	$fatura["status"] == PERSISTE_CBTB_FATURAS::$CANCELADA or
				$fatura["status"] == PERSISTE_CBTB_FATURAS::$ESTORNADA or
				$fatura["status"] == PERSISTE_CBTB_FATURAS::$PAGA ) {
				return false;
			}

			$totalDevido = $fatura["valor"] - $desconto + $acrescimo;

			if($amortizar > 0) {
				$data["valor_pago"] = $fatura["valor_pago"] + $amortizar;
				if($totalDevido == $amortizar){
					$data["status"] = PERSISTE_CBTB_FATURAS::$PAGA;
					$data["pagto_parcial"] = 0;
				} elseif($amortizar < $totalDevido) {
					$data["status"] = PERSISTE_CBTB_FATURAS::$PARCIAL;
					$data["pagto_parcial"] = $data["valor_pago"];
				} elseif($amortizar > $totalDevido){
					 throw new ExcecaoModeloValidacao (1,"Valor a receber excede o valor pendente!");
				}

			} elseif($amortizar == 0) {
				if($totalDevido == 0){ // desconto de 100% sobre o valor devido
					$data["status"] = PERSISTE_CBTB_FATURAS::$PAGA;
				} else {
					$data["status"] = $fatura["status"];
				}
			} else {
				throw new ExcecaoModeloValidacao (2,"Valor a receber não pode ser negativo!");
			}

			$data["desconto"] = $desconto;
			$data["acrescimo"] = $acrescimo;
			$data["data_pagamento"] = $data_pagamento;

			if($reagendar and $data["status"] != PERSISTE_CBTB_FATURAS::$PAGA){
				$data["reagendamento"] = $reagendamento;
				$data["observacoes"] = $observacoes;
				
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

			return true;
		}


		public function cadastraLoteCobranca($data_referencia, $periodo, $id_admin) {
			$dados = array (	"data_referencia" => $data_referencia,
								"data_geracao" => '=now',
								"periodo" => $periodo,
								"id_admin" => $id_admin
							);

			return ($this->cbtb_lote_cobranca->insere($dados));
		}


		public function obtemFaturasPorPeriodoSemCodigoBarra($data_referencia, $periodo) {
					return ($this->cbtb_fatura->obtemFaturasPorPeriodoSemCodigoBarra($data_referencia, $periodo));
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

		public function obtemFaturasPorRemessa($id_remessa) {
			return ($this->cbtb_fatura->obtemFaturasPorRemessa($id_remessa));
		}

		public function obtemContratosFaturasAtrasadas() {
			return ($this->cbtb_contrato->obtemContratosFaturasAtrasadas());
		}


	}




?>