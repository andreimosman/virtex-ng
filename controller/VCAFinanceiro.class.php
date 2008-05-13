<?

class VCAFinanceiro extends VirtexControllerAdmin {

	protected $cobranca;
	protected $contas;
	protected $contratos;

	public function __construct() {
		parent::__construct();
	}

	protected function init() {
		parent::init();

		$this->cobranca = VirtexModelo::factory('cobranca');
		$this->contas = VirtexModelo::factory('contas');
		$this->_view = VirtexViewAdmin::factory('financeiro');
	}

	protected function executa() {
	
		switch($this->_op) {
			case 'bloqueios':
				$this->executaBloqueios();
				break;
			case 'amortizacao':
				$this->executaAmortizacao();
				break;
			case 'gerar_cobranca':
				$this->executaGerarCobranca();
				break;
			case 'arquivos':
				$this->executaArquivos();
				break;
			case 'relatorios_cobranca':
				$this->executaRelatoriosCobranca();
				break;
			case 'gerar_lista_boletos':
				$this->geraListaBoletos();
				break;
			case 'gerar_lista_faturas':
				$this->geraListaFaturas();
				break;
			case 'relatorios_faturamento':
				$this->executaRelatoriosFaturamento();
				break;
			case 'download_remessa';
				$this->executaDownloadRemessa();
				break;
		}
	}

	protected function executaBloqueios() {

		$podeGravar = $this->requirePrivGravacao("_FINANCEIRO_COBRANCA_BLOQUEIOS",false);

		$this->_view->atribui("podeGravar", $podeGravar );

		$this->requirePrivLeitura("_FINANCEIRO_COBRANCA_BLOQUEIOS");
		
	
		$this->_view->atribuiVisualizacao("cobranca");

		$contrato_id = @$_REQUEST["contrato_id"];
		$bloquear    = @$_REQUEST["bloquear"];
		$acao        = @$_REQUEST["acao"];
		$url 		 = "admin-financeiro.php?op=bloqueios";

		if( $acao == "bloquear" ) {
		
				$this->requirePrivGravacao("_FINANCEIRO_COBRANCA_BLOQUEIOS");
		

				try {
					$senha_admin = @$_REQUEST["senha_admin"];
					$dadosLogin = $this->_login->obtem("dados");
					if( !$senha_admin ) {
						$erro = "Cancelamento não autorizado: SENHA NÃO FORNECIDA.";
					} elseif (md5(trim($senha_admin)) != $dadosLogin["senha"] ) {
						$erro = "Operação não autorizada: SENHA NÃO CONFERE.";
					}
					if($erro) throw new Exception($erro);

					if ($contrato_id) {
						$dadosLogin = $this->_login->obtem("dados");
						$admin = $dadosLogin["admin"];
						$id_admin = $dadosLogin["id_admin"];

						foreach($contrato_id as $k => $v) {

							$listacontas = $this->contas->obtemContasPeloContrato($k, $v);

							foreach($listacontas as $chave => $campo) {
								if($v == "BL") {
									$this->contas->alteraContaBandaLarga($campo["id_conta"], NULL, 'S');
								} else if($v == "D") {
									$this->contas->alteraContaDiscado($campo["id_conta"], NULL, 'S');
								} else if($v == "H") {
									$this->contas->alteraHospedagem($campo["id_conta"], NULL, 'S');
								}
								$this->contas->gravaLogMudancaStatusConta($campo["id_cliente_produto"], $campo["username"], $campo["dominio"], $campo["tipo_conta"], $id_admin, '');
							}

							$this->contas->gravaLogBloqueioAutomatizado($k, 'S', $admin, 'Suspensão por pendências financeiras');
						}
					}

					$this->_view->atribui("url",$url);
					$this->_view->atribui("mensagem","Dados atualizados com sucesso!");
					$this->_view->atribuiVisualizacao("msgredirect");

					if( $acao ) {
						$erro = "";
					} else {
						VirtexView::simpleRedirect($url);
					}

				}catch (ExcecaoModeloValidacao $e ) {
					$this->_view->atribui ("msg_erro", $e->getMessage());
					$erro = true;
				} catch (Exception $e ) {
					$this->_view->atribui ("msg_erro", $e->getMessage());
					$erro = true;
				}
		}
		
		$prefCobranca = $this->preferencias->obtemPreferenciasCobranca();		
		$atrasados = $this->cobranca->obtemContratosFaturasAtrasadasBloqueios((int)$prefCobranca["carencia"]);
		$this->_view->atribui("atrasados", $atrasados);

		$countBloqueados = count($atrasados);
		$this->_view->atribui("countBloqueados", $countBloqueados);
	}

	protected function executaAmortizacao() {
	
		$this->_view->atribuiVisualizacao("cobranca");
	
		$this->requirePrivGravacao("_FINANCEIRO_COBRANCA_AMORTIZACAO");
		$id_cobranca = @$_REQUEST["id_cobranca"];

		$texto_pesquisa = @$_REQUEST["texto_pesquisa"];
		$tipo_pesquisa  = @$_REQUEST["tipo_pesquisa"];

		if(!$tipo_pesquisa) $tipo_pesquisa = "NUMERODOCUMENTO";

		$this->_view->atribui("texto_pesquisa", $texto_pesquisa);
		$this->_view->atribui("tipo_pesquisa",$tipo_pesquisa);

		if( $texto_pesquisa && $tipo_pesquisa ) {
			// Pesquisar!
			$fatura = array();
			switch($tipo_pesquisa) {
				case 'LINHADIGITAVEL':
					$fatura = $this->cobranca->obtemFaturaPelaLinhaDigitavel($texto_pesquisa);
					break;
				case 'CODIGOBARRAS':
					$fatura = $this->cobranca->obtemFaturaPeloCodigoBarras($texto_pesquisa);
					break;
				case 'NUMERODOCUMENTO':
					if( strstr('-',$texto_pesquisa) ) {
						list($texto_pesquisa,$lixo) = explode("-",$texto_pesquisa);
					}					
					$fatura = $this->cobranca->obtemFaturaPorIdCobranca($texto_pesquisa);					
					break;
			}

			if( !count($fatura) ) {
				$erro = "Nenhuma fatura encontrada com o número fornecido.";
				$this->_view->atribui("erro", $erro);
			} else {
				// Redireciona p/ a fatura.

				$clienteProduto = $this->cobranca->obtemClienteProduto($fatura["id_cliente_produto"]);

				$url = "admin-clientes.php?op=contrato&tela=amortizacao&id_cliente=".$clienteProduto["id_cliente"]."&id_cliente_produto=".$fatura["id_cliente_produto"]."&data=".$fatura["data"]."&id_cobranca=".$fatura["id_cobranca"];
				VirtexView::simpleRedirect($url);
			}
		}
	}

	protected function geraListaFaturas(){

		$this->_view->atribuiVisualizacao("cobranca");
		$id_remessa = @$_REQUEST["id_remessa"];
		$resultado = $this->cobranca->obtemFaturasPorRemessa($id_remessa);
		$this->_view->atribui("resultado", $resultado);


		///$this->arquivoTemplate = "cobranca_listar_faturas.html";


	}

	protected function geraListaBoletos() {
	
		$this->_view->atribuiVisualizacao("cobranca");

		$id_remessa = @$_REQUEST["id_remessa"];

		////echo $id_remessa;

		$resultado = $this->cobranca->obtemFaturasPorRemessaGeraBoleto($id_remessa);

		$cedente = $this->preferencias->obtemPreferenciasGerais();

		$cedente_cobranca = $this->preferencias->obtemPreferenciasProvedor();

		/*echo "<pre>";
		print_r($resultado);
		echo "</pre>";*/

		for($i=0; $i<count($resultado); $i++) {
			$id_forma_pagamento = $resultado[$i]["id_forma_pagamento"];
			$valor = $resultado[$i]["valor"];
			$data = $resultado[$i]["data"];
			$id_cobranca = $resultado[$i]["id_cobranca"];
			$codigo_barra = $resultado[$i]["cod_barra"];
			$linha_digitavel = $resultado[$i]["linha_digitavel"];

			$formaPagto = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);

			$resultado[$i]["hoje"] = date("d/m/Y");

			if( $formaPagto["tipo_cobranca"] == "BL" && $id_forma_pagamento=="11" ) {

				$urlPreto = "view/templates/imagens/preto.gif";
				$urlBranco = "view/templates/imagens/branco.gif";

				$resultado[$i]["barcode"] =  MBanco::htmlBarcode($codigo_barra,$urlPreto,$urlBranco);


			}

		}

		/*echo "<pre>";
		print_r($resultado);
		echo "</pre>";
		*/

		$this->_view->atribui("resultado",$resultado);
		$this->_view->atribui("cedente",$cedente);
		$this->_view->atribui("cedente_cobranca",$cedente_cobranca);

	}

	protected function executaGerarCobranca() {
	
		$podeGravar = $this->requirePrivGravacao("_FINANCEIRO_COBRANCA_GERAR_BOLETOS",false);
		$this->_view->atribui("podeGravar", $podeGravar );

		$this->requirePrivLeitura("_FINANCEIRO_COBRANCA_GERAR_BOLETOS");
	
	
	
		$this->_view->atribuiVisualizacao("cobranca");

		$acao = @$_REQUEST["acao"];
		$ano = @$_REQUEST["ano"];
		$mes = @$_REQUEST["mes"];
		$periodo = @$_REQUEST["periodo"];
		
		if( !$mes ) $mes = date("m");
		if( !$ano ) $ano = date("Y");
		
		$this->_view->atribui("mes",$mes);
		$this->_view->atribui("ano",$ano);


		$formas = $this->preferencias->obtemFormasPagamentoGerarCobranca();
		$this->_view->atribui("formas",$formas);

		$bancos = $this->preferencias->obtemListaBancos();
		$this->_view->atribui("bancos",$bancos);

		
		$pref_cobranca = $this->preferencias->obtemPreferenciasCobranca();
		$pref_provedor = $this->preferencias->obtemPreferenciasProvedor();
		$info_licenca = $this->licenca->obtemLicenca();
		$nome_razao = $info_licenca["empresa"]["nome"];
		$cnpj_empresa = $pref_provedor["cnpj"];
		
		
		if ($acao == "gerar") {
			$this->requirePrivGravacao("_FINANCEIRO_COBRANCA_GERAR_BOLETOS");

			$dados = array();
			

			//Mensagem a ser mostrada pós processamento
			$mensagem = "";
			
			//Insere informações da remessa de cobranca
			$data_referencia = "$ano-$mes";
			$data_referencia_dia1 = "$ano-$mes-01";

			//COLOCAR ESQUEMA DE PEGAR ID DO USUARIO
			$dadosLogin = $this->_login->obtem("dados");
			$id_admin = $dadosLogin["id_admin"];
			$id_forma_pagamento = @$_REQUEST["id_forma_pagamento"];

			$formaPagto = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);
			
			$id_forma_pagamento_fatura = ($formaPagto["carteira_registrada"] == 't' || $formaPagto["impressao_banco"] == 't') ? 9999 : id_forma_pagamento;
			
			//Faz a captura de todos os contratos com vigência 0
			$contratos_vzero = $this->cobranca->obtemContratosVigenciaZero($id_forma_pagamento);		
			
			foreach($contratos_vzero as $chave => $item) {
				$fatura_retorno = $this->cobranca->obtemFaturaPorContratoPeriodo($data_referencia, $periodo, $item["id_cliente_produto"]);	
				
				if(!$fatura_retorno) {
					$valor_fatura = floatval($item["valor_produto"]);
					$valor_fatura += floatval($item["valor_comodato"]);
					//$valor_fatura -= floatval($item["desconto_promo"]);
					
					$data_referencia_fatura = $data_referencia . "-" . $item["vencimento"];

					$this->cobranca->cadastraFatura($item["id_cliente_produto"], $data_referencia, $data_referencia_fatura, $valor_fatura, $id_forma_pagamento_fatura, $item["nome_produto"]);
				}
			}			
			

			if($formaPagto["carteira_registrada"] == 't' || $formaPagto["impressao_banco"] == 't') {
			
				$faturas = $this->cobranca->obtemFaturasPorPeriodoParaRemessa($data_referencia, $periodo, $id_forma_pagamento);
				
				//echo "<pre>"; 
				//print_r($faturas);
				//echo "</pre>";
				//
				//return;
				
				
				if (!$faturas){
					$msg = "Não foram encontradas faturas para esse período.";				
				} else {
				
					$id_remessa = $this->cobranca->cadastraLoteCobranca($data_referencia_dia1, $periodo, $id_admin,$id_forma_pagamento);
					$msg = "Remessa cadastrada com sucesso.";

					$remessa = MRemessa::factory($formaPagto["codigo_banco"]);
					$remessa->init($formaPagto["agencia"], $formaPagto["conta"], $formaPagto["dv_conta"], $formaPagto["carteira"], $formaPagto["convenio"], $nome_razao, $cnpj_empresa, $pref_cobranca["tx_juros"]);

					$arquivo = "C" . date("ym");
					$numero_geracao = 0;

					do{
						$numero_geracao++;
						$arquivo_temp = $arquivo . "$numero_geracao";
						$resultado = $this->cobranca->obtemRemessaPeloNomeArquivo($arquivo_temp);
					} while($resultado);
					
					$arquivo .= "$numero_geracao" . ".txt";	//Nome de geração do arquivo
					
					//Ajusta diretório de gravação
					$scriptname = $_SERVER["SCRIPT_FILENAME"];
					$scriptdir = substr($scriptname, 0, strrpos($scriptname, "/"));
					$remessadir = $scriptdir . "/var/remessa/";
					
					$numero_remessa = $this->cobranca->cadastraRemessa($arquivo, $id_remessa);
					
					foreach($faturas as $chave => $item) {
						$this->cobranca->cadastraLoteFatura($id_remessa, $item["id_cobranca"]);
						$this->cobranca->alteraRemessaFatura($id_remessa, $item["id_cobranca"]);
					}					
					
					$remessa->geraArquivoRemessa($remessadir . $arquivo,$faturas);	
				
				}
				
				
			} else {
			
				$resultado = $this->cobranca->obtemFaturasPorPeriodoSemCodigoBarraPorTipoPagamento($data_referencia, $periodo, $id_forma_pagamento);

				if (!$resultado){
					$msg = "Não foram encontradas faturas para esse período.";
				} else {
					$id_remessa = $this->cobranca->cadastraLoteCobranca($data_referencia_dia1, $periodo, $id_admin,$id_forma_pagamento);
					$formaPagto = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);
					$msg = "Lote cadastrado com sucesso.";
				}

				$urlPreto = "view/templates/imagens/preto.gif";
				$urlBranco = "view/templates/imagens/branco.gif";


				for($i=0; $i<count($resultado); $i++) {
					$id_cobranca = $resultado[$i]["id_cobranca"];
					//echo $id_cobranca;

					$this->cobranca->cadastraLoteFatura($id_remessa, $id_cobranca);

					if( $formaPagto["tipo_cobranca"] == "BL" ) {

						 $boleto = MBoleto::factory(
														$formaPagto["codigo_banco"],$formaPagto["agencia"],
														$formaPagto["conta"],$formaPagto["carteira"],
														$formaPagto["convenio"],
														$resultado[$i]["data"],$resultado[$i]["valor"],
														$id_cobranca,9,$formaPagto["cnpj_agencia_cedente"],
														$formaPagto["codigo_cedente"],
														$formaPagto["operacao_cedente"]
													);

						$codigo_barra = $boleto->obtemCodigoBoleto();
						$linha_digitavel = $boleto->obtemLinhaDigitavel();

					}


					$this->cobranca->InsereCodigoBarraseLinhaDigitavel($codigo_barra, $linha_digitavel, $id_cobranca);

					// $msg = "O Lote foi gerado para o período com sucesso!";

				}
			}

			$url = "admin-financeiro.php?op=gerar_cobranca";
			$this->_view->atribui("url",$url);
			$this->_view->atribui("mensagem", $msg);
			$this->_view->atribuiVisualizacao("msgredirect");

			// VirtexView::simpleRedirect($url);

		}

		$periodo_anos_fatura = $this->cobranca->obtemAnosFatura();

		$this->_view->atribui("periodo_anos", $periodo_anos_fatura);

		$ultimas_remessas = $this->cobranca->obtemUltimasRemessas("10");

		$formas = $this->preferencias->obtemFormasPagamentoGerarCobranca();
		$this->_view->atribui("formas",$formas);

		$bancos = $this->preferencias->obtemListaBancos();
		$this->_view->atribui("bancos",$bancos);


		for($i=0;$i<count($ultimas_remessas);$i++){
			$ultimas_remessas[$i]["forma_pagto"] = $ultimas_remessas[$i]["id_forma_pagamento"] ? $this->preferencias->obtemFormaPagamento($ultimas_remessas[$i]["id_forma_pagamento"]) : array();
		}

		//echo "<pre>";
		//print_r($ultimas_remessas);
		//echo "</pre>";

		$this->_view->atribui("ultimas_remessas", $ultimas_remessas);


	}

	protected function executaArquivos() {
		$this->_view->atribuiVisualizacao("cobranca");
			
		$combo_formato = MRetorno::obtemFormatosRetorno();
		
		$msg = "Retorno efetuado com sucesso";
		$url = "admin-financeiro.php?op=arquivos";
		$msg_error = array();
	
		
		// processa request
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
			// validacao input
			
			$clean = array();
			
			$formato_retorno = @$_POST['formato_retorno'];
			
			if(array_key_exists($formato_retorno, $combo_formato))
				$clean['formato_retorno'] = $formato_retorno;
		
			$arquivo_retorno = $this->cobranca->obtemRetornoPeloArquivoEnviado($_FILES['arquivo_retorno']);
			//$arquivo_retorno = $this->cobranca->obtemRetornoPeloArquivoEnviado("Pc280306.txt");
			
			if($arquivo_retorno) {
				array_push($msg_error, 'Arquivo já foi enviado anteriormente');
			}
			
			
			$uploaddir = './var/retorno/';
			$nome_arquivo_retorno = 'cobranca-retorno-' . date('YmdHis');
			
			if (is_uploaded_file($_FILES['arquivo_retorno']['tmp_name'])) {
			 	move_uploaded_file($_FILES['arquivo_retorno']['tmp_name'], $uploaddir . $nome_arquivo_retorno);   
			}
			else {
				array_push($msg_error, 'Arquivo não foi enviado');
			}
			
			//echo "<pre>";
			//print_r($_FILES['arquivo_retorno']);
			//echo "</pre>";
			
			//die;
			
			if (!array_key_exists('formato_retorno', $clean))
				array_push($msg_error, 'Formato Inválido');
					
			if (empty($msg_error)) {
				$retorno = MRetorno::factory($formato_retorno,$uploaddir . $nome_arquivo_retorno);
				$registros = $retorno->obtemRegistros();
		
				$dadosLogin = $this->_login->obtem("dados");
				$admin['id_admin'] = $dadosLogin["id_admin"];
						
				// gravar log
				$id_retorno = $this->cobranca->gravarLogRetorno($clean['formato_retorno'], $_FILES['arquivo_retorno']["name"], $uploaddir . $nome_arquivo_retorno, $admin);
				
				$numeroTotalRegistros = 0;
				$numeroRegistrosProcessados = 0;
				
				$numeroRegistrosComErro = 0;
									
				foreach($registros as $reg) {
					$observacoes = 'Arquivo de retorno';
					$numeroTotalRegistros++;
					switch($formato_retorno) {			
					
						case 'ITAU':
							$id_cobranca = $reg['numero_documento'];
							$nossonumero = $reg['nossonumero'];
							$desconto = $reg['descontos'] + $reg['valor_abatimento'];
							$acrescimo = $reg['juros_multa_mora'] + $reg['outros_creditos'];
							$amortizar = $reg['valor_principal'];
							$data_pagamento = $reg['data_ocorrencia'];
							$codigo_ocorrencia = $reg['codigo_ocorrencia'];
							$reagendar = null;
							$reagendamento = null;
		
							// obtem fatura		
							$fatura = $this->cobranca->obtemFaturaPorIdCobranca($id_cobranca);				
							if (empty($fatura))
								$fatura = $this->cobranca->obtemFaturaPeloNossoNumero($nosso_numero);
					
							if (empty($fatura))
								continue;		
					
							// altera nosso numero ?
							if ($codigo_ocorrencia == '') {
								$this->cobranca->alteraNossoNumero($id_cobranca, $nossonumero);
							} else {											
								// armotizar fatura
								$this->cobranca->amortizarFatura($id_cobranca, $desconto, $acrescimo, $amortizar, $data_pagamento, $reagendar, $reagendamento, $observacoes, $admin, $id_retorno);	
								$numeroRegistrosProcessados++;
							}						
					
							break;
						
						case 'PAGCONTAS':
							//$codigo_barras = $reg['codigo_barras'];
							//print_r ($reg);
							//exit;
	

							$fatura = $this->cobranca->obtemFaturaPeloCodigoBarras($codigo_barras);										
							
							if (empty($fatura))
								continue;
														
							$id_cobranca = $fatura['id_cobranca'];
							$reagendar = null;
							$reagendamento = null;
							$data_pagamento = $reg['data_pagamento'];
							$amortizar = (int) $reg['valor_recebido'];
							
							$valor_receber = $fatura['valor'] - $fatura['valor_pago'];
														
							if ($valor_receber > $reg['valor_recebido']) {
								$acrescimo = $valor_receber - $amortizar;
								$desconto = 0;
							}
							elseif ($amortizar < $fatura['valor_recebido']) {
								$acrescimo = 0;
								$desconto = $valor_receber - $amortizar;							
							}
							else {
								$acrescimo = 0;
								$desconto = 0;							
							}			
							try {
								$this->cobranca->amortizarFatura($id_cobranca, $desconto, $acrescimo, $amortizar, $data_pagamento, $reagendar, $reagendamento, $observacoes, $admin, $id_retorno);
								$numeroRegistrosProcessados++;
							} catch(Exception $e) {
								$numeroRegistrosComErro++;
							}
							
							break;
														
						case 'BBCBR643':
											
							$nossonumero = $reg['nossonumero'];						
							$fatura = $this->cobranca->obtemFaturaPeloNossoNumero($nossonumero);	

							if (empty($fatura))
								continue;
							
							$id_cobranca = $fatura['id_cobranca'];
							$reagendar = null;
							$reagendamento = null;
							$data_pagamento = $reg['data_credito'];
							$amortizar = $reg['valor_recebido'];
					
							/*
							 * FIXME - validar desconto e acrescimo com Andrei.
							 */
					
							$desconto = $reg['taxa_desconto'] + $reg['juros_desconto'] + $reg['iof_desconto'];
							$acrescimo = $reg['juros_mora'] + $reg['outros_recebimentos'];
	
							$this->cobranca->amortizarFatura($id_cobranca, $desconto, $acrescimo, $amortizar, $data_pagamento, $reagendar, $reagendamento, $observacoes, $admin, $id_retorno);
							$numeroRegistrosProcessados++;
							
							
							break;						
						
						default:
							continue;
					
					}
				}
				
				// echo "TESTE!!!";
				
				$data_geracao = $retorno->obtemDataGeracao();
				
				// ATUALIZA LOG RETORNO
				$numeroRegistrosSemCorrespondencia = $numeroTotalRegistros - $numeroRegistrosProcessados - $numeroRegistrosComErro;
				$this->cobranca->atualizaLogRetorno($id_retorno, $numeroTotalRegistros, $numeroRegistrosProcessados, $numeroRegistrosComErro, $numeroRegistrosSemCorrespondencia, $data_geracao);
				
				
			} else {
			
				$msg = $msg_error[0];
				
			}
			
			
			$this->_view->atribui("url",$url);
			$this->_view->atribui("mensagem", $msg);
			$this->_view->atribuiVisualizacao("msgredirect");
		}
		
		$tela = @$_REQUEST["tela"];
		
		if( !$tela ) {
			$tela = "retorno";
		}
		
		$retornos = $this->cobranca->obtemUltimosRetornos(30);
		$remessas = $this->cobranca->obtemUltimasRemessasCriadas(30);
		
		$this->_view->atribui("retornos", $retornos);
		$this->_view->atribui("remessas", $remessas);
		$this->_view->atribui('combo_formato', $combo_formato);
		$this->_view->atribui("tela",$tela);

	}

	protected function executaRelatoriosCobranca() {

		$this->requirePrivLeitura("_FINANCEIRO_COBRANCA_RELATORIOS");
	
	
	
		$this->_view->atribuiVisualizacao("cobranca");

		$relatorio = @$_REQUEST["relatorio"];

		if("cortesias" == $relatorio){
			$contas = VirtexModelo::factory("contas");
			$rs = $contas->obtemQtdeContasCortesiaDeCadaTipo();
				$resumo["total"] = 0;
			foreach($rs as $row){
				$resumo[$row["tipo_conta"]] = $row["num_contas"];
				$resumo["total"]+=$row["num_contas"];
			}
			$this->_view->atribui("resumo", $resumo);

			$tipoContas = $contas->obtemTiposConta();
			asort($tipoContas);
			$this->_view->atribui("tipoContas", $tipoContas);


			$tipo = @$_REQUEST["filtro"];
			$rs = $contas->obtemContasCortesiaDeCadaTipo($tipo);

			//die("<pre>".print_r($rs,true)."</pre>");
			$this->_view->atribui("contas", $rs);

			$this->_view->atribui("filtro", $tipo);
		} elseif("adesoes" == $relatorio) {
			$periodo = isset($_REQUEST["periodo"]) ? $_REQUEST["periodo"] : 12;
			$this->_view->atribui("periodo", $periodo);
			$adesoes = $this->cobranca->obtemAdesoesPorPeriodo($periodo);
			$this->_view->atribui("adesoes", $adesoes);
			//echo "<pre>";
			//print_r($adesoes);
			//echo "</pre>";


		} elseif("cancelamentos" == $relatorio) {
			$periodo = isset($_REQUEST["periodo"]) ? $_REQUEST["periodo"] : 12;
			$this->_view->atribui("periodo", $periodo);
			$cobranca = VirtexModelo::factory("cobranca");
			$cancelados = $cobranca->obtemCancelamentosPorPeriodo($periodo);
			$this->_view->atribui("cancelados", $cancelados);

		} elseif("atrasos" == $relatorio) {
			$periodo = isset($_REQUEST["periodo"]) ? $_REQUEST["periodo"] : 12;
			$this->_view->atribui("periodo", $periodo);
			$cobranca = VirtexModelo::factory("cobranca");
			$atrasos = $cobranca->obtemFaturasAtrasadasPorPeriodo($periodo);
			$this->_view->atribui("atrasos", $atrasos);

			$i = ( $periodo * -1 ) + 1;
			$meses = array();
			for(;$i<=0;$i++){
				$data = MData::calculaPeriodo(mktime(),$i,"m/Y");
				list($m,$a) = split("/",$data);
				$meses[$data]["mes"] = (int) $m;
				$meses[$data]["ano"] = (int) $a;

				/*$mesesAno["01"]="Janeiro";
				$mesesAno["02"]="Fevereiro";
				$mesesAno["03"]="Março";
				$mesesAno["04"]="Abril";
				$mesesAno["05"]="Maio";
				$mesesAno["06"]="Junho";
				$mesesAno["07"]="Julho";
				$mesesAno["08"]="Agosto";
				$mesesAno["09"]="Setembro";
				$mesesAno["10"]="Outubro";
				$mesesAno["11"]="Novembro";
				$mesesAno["12"]="Dezembro";
				$meses[$data]["strmes"] = $mesesAno[$m]; */
			}
			$this->_view->atribui("meses", $meses);

			} elseif("atrasos_detalhes" == $relatorio) {
			$periodo = @$_REQUEST["ano"]."-".@$_REQUEST["mes"];
			$this->_view->atribui("periodo", $periodo);
			$cobranca = VirtexModelo::factory("cobranca");
			$lista = $cobranca->obtemFaturasAtrasadasDetalhes($periodo);

			$this->_view->atribui("lista", $lista);

		} elseif("cliente_produto" == $relatorio) {
			$produto = VirtexModelo::factory("produtos");
			$lista = $produto->obtemQtdeContratosPorProduto();
			$this->_view->atribui("produto", $lista);

		} elseif("cliente_produto_detalhe" == $relatorio) {
			$contas = VirtexModelo::factory("contas");
			$id_produto = @$_REQUEST["id_produto"];
			$rs = $contas->obtemClientesPorProduto($id_produto);
			$this->_view->atribui("clientes", $rs);

		} elseif("cliente_tipo_produto" == $relatorio) {
			$produto = VirtexModelo::factory("produtos");
			$lista = $produto->obtemQtdeContratosPorTipoDeProduto();
			$this->_view->atribui("produto", $lista);

		} elseif("cliente_tipo_produto_detalhe" == $relatorio) {
			$contas = VirtexModelo::factory("contas");
			$tipo_produto = @$_REQUEST["tipo_produto"];
			$rs = $contas->obtemClientesPorTipoConta($tipo_produto);
			$this->_view->atribui("clientes", $rs);

		} elseif("evolucao" == $relatorio) {
			$cobranca = VirtexModelo::factory("cobranca");
			$periodo = isset($_REQUEST["periodo"]) ? $_REQUEST["periodo"] : 12;
			$this->_view->atribui("periodo", $periodo);
			$evolucao = $cobranca->obtemEvolucaoPorPeriodo($periodo);
			$this->_view->atribui("evolucao", $evolucao);

			//echo "<pre>";
			//print_r($evolucao);
			//echo "</pre>";
		} elseif("reagendamentos" == $relatorio) {
			$cobranca = VirtexModelo::factory("cobranca");
			$lista = $cobranca->obtemReagendamento();
			$this->_view->atribui("cobranca", $lista);

		} elseif("bloqueios_desbloqueios" == $relatorio) {
			$periodo = isset($_REQUEST["periodo"]) ? $_REQUEST["periodo"] : 12;
			$this->_view->atribui("periodo", $periodo);
			$contas = VirtexModelo::factory("contas");
			$bloqueios_desbloqueios = $contas->obtemBloqueiosDesbloqueios($periodo);
			$this->_view->atribui("bloqueios_desbloqueios", $bloqueios_desbloqueios);

		} elseif("bloqueios_desbloqueios_detalhes" == $relatorio) {
			$contas = VirtexModelo::factory("contas");

			$periodoMesAno = $mes = @$_REQUEST["mes"] . -  $ano = @$_REQUEST["ano"];
			$this->_view->atribui("periodoMesAno", $periodoMesAno);

			$periodoAnoMes = @$_REQUEST["ano"] . - @$_REQUEST["mes"];
			$this->_view->atribui("periodoAnoMes", $periodoAnoMes);

			$bloqueados_desbloqueados = $contas->obtemBloqueiosDesbloqueiosDetalhes($periodoAnoMes);
			$this->_view->atribui("bloqueados_desbloqueados", $bloqueados_desbloqueados);

		}
	}
	
	
	protected function executaRelatoriosFaturamento() {

		$this->requirePrivLeitura("_FINANCEIRO_COBRANCA_RELATORIOS");

		$this->_view->atribuiVisualizacao("faturamento");

		$relatorio = @$_REQUEST["relatorio"];

		$cobranca = VirtexModelo::factory("cobranca");
		
		if("previsao" == $relatorio){

			$ano_select = @$_REQUEST["ano_select"];
			
			if( ! $ano_select ) $ano_select = date("Y");
			
			$cobranca = VirtexModelo::factory("cobranca");
			$lista = $cobranca->obtemPrevisaoFaturamento($ano_select);
			$anos_fatura = $cobranca->obtemAnosFatura();
			
			//echo "<pre>";
			//print_r($lista);
			//echo "</pre>";
			
			$dados = array();
			$soma = array();


			for($i=1; $i<=31; $i++) {
				$dados[$i] = array();

				for($j=1; $j<=12; $j++) {
					$dados[$i][$j] = "0.00";
					$soma[$j]="0.00";
				}
			}

			foreach ($lista as $chave => $valor) {
				$vp = $valor["valor_pago"];
				$dados[$valor["dia"]][$valor["mes"]] = $vp ? $vp : "0.00";
				$soma[$valor["mes"]] += $vp;
			}


			$this->_view->atribui("soma", $soma);
			$this->_view->atribui("dados", $dados);
			$this->_view->atribui("ano_select", $ano_select);
			$this->_view->atribui("ano_select1", $ano_select1);
			$this->_view->atribui("anos_fatura", $anos_fatura);


		}elseif ("faturamento" == $relatorio){
			
			$ano_select = @$_REQUEST["ano_select"];
			
			$ano_atual = Date("Y");
			
			$metodo = @$_REQUEST["metodo"];
					
			if (!$ano_select ){
				$ano_select = $ano_atual;
				$metodo = "2";
			}
			
			if ($metodo == "1"){
				$titulo_relatorio = "Comparativo";		
			}else{
				$titulo_relatorio = "Acumulativo";
			}

			$anos_fatura = $cobranca->obtemAnosFatura();
			
			$fat = $cobranca->obtemFaturamentoComparativo($ano_select);
			
			$tabela = array();
					
			for($i=0;$i<count($fat);$i++) {
				$tabela[   ((int)$fat[$i]["dia"]) ][   ((int)$fat[$i]["mes"]) ] = $fat[$i]["faturamento"] ;
		
			}

			for($i=1;$i<=31;$i++) {
				if( !@$tabela[$i] ) {
					$tabela[$i]=array();
				}
				for($x=1;$x<=12;$x++) {
					if( !@$tabela[$i][$x] ) {
						$tabela[$i][$x] = 0;
					}
				}
				//echo $tabela[$i]["1"] . " - " . $tabela[$i]["2"] . " - ". $tabela[$i]["3"] . "<br>\n";
			}
					
			ksort($tabela);
			
			
			$this->_view->atribui("tabela",$tabela);
			$this->_view->atribui("titulo_relatorio",$titulo_relatorio);
			$this->_view->atribui("anos_fatura",$anos_fatura);
			$this->_view->atribui("ano_select",$ano_select);
			$this->_view->atribui("metodo",$metodo);
			
			

		}elseif ("por_produto" == $relatorio){

			$ano_select = @$_REQUEST["ano_select"];
			
			$ano_atual = Date("Y");
			if (!$ano_select ){
				$ano_select = $ano_atual;
			}
			$this->_view->atribui("ano_select", $ano_select);
			
			$lista = $cobranca->obtemFaturamentoPorProduto($ano_select);
			$anos_fatura = $cobranca->obtemAnosFatura();
			$dados_bl = array();
			$dados_h = array();
			$dados_d = array();

			for($i=1; $i<=12; $i++) {
				$dados_bl[$i] = "0.00";
				$dados_h[$i] = "0.00";
				$dados_d[$i] = "0.00";
			}

			for($i=0; $i<count($lista); $i++) {

				$temp = $lista[$i];

				switch($temp["tipo"]) {

					case "BL":
						$dados_bl[$temp["mes"]] = $temp["valor_pago"];
						break;
					case "D":
						$dados_d[$temp["mes"]] = $temp["valor_pago"];
						break;
					case "H":
						$dados_h[$temp["mes"]] = $temp["valor_pago"];
						break;
					default:
						//FAZ NADa POR ENQUANTO
						break;
				}
			}

			$this->_view->atribui("ano_select",$ano_select);
			$this->_view->atribui("anos_fatura",$anos_fatura);
			$this->_view->atribui("dados_bl", $dados_bl);
			$this->_view->atribui("dados_h", $dados_h);
			$this->_view->atribui("dados_d", $dados_d);

		}elseif ("por_periodo" == $relatorio){

			$periodo = 12;
			$this->_view->atribui("periodo", $periodo);
			$cobranca = VirtexModelo::factory("cobranca");

			$lista = $cobranca->obtemFaturamentoPorPeriodo($periodo);
			$dados = array();
			$sumario = array("valor_documento" => 0, "valor_acrescimo" => 0, "valor_desconto" => 0, "valor_pago"=>0);

			for($i=0;$i<count($lista);$i++) {
				if( $lista[$i]["mes"] < 10 ) $lista[$i]["mes"] = "0".$lista[$i]["mes"];
				$dados[ $lista[$i]["ano"] . "-" . $lista[$i]["mes"] ] = $lista[$i];

				$sumario["valor_documento"] += $lista[$i]["valor_documento"];
				$sumario["valor_desconto"] += $lista[$i]["valor_desconto"];
				$sumario["valor_acrescimo"] += $lista[$i]["valor_acrescimo"];
				$sumario["valor_pago"] += $lista[$i]["valor_pago"];
			}

			$this->_view->atribui("sumario",$sumario);
			$dt = date("d/m/Y");

			for($i=0;$i<$periodo;$i++) {
				list($d,$m,$y) = explode("/",$dt);
				$dt = MData::adicionaMes($dt,-1);
				if( ! @$dados[ $y."-".$m ] ) {
					$dados[ $y."-".$m ] = array("ano" => $y, "mes" => $m);
				}
			}
			krsort($dados);
			$this->_view->atribui("lista", $dados);

		}
	}
	
	public function executaDownloadRemessa() {
		$id_remessa = @$_REQUEST["id_remessa"];
		$id_retorno = @$_REQUEST["id_retorno"];
		
		if ($id_remessa) {
			$remessa = $this->cobranca->obtemRemessaPeloId($id_remessa);
			$this->criaDownload("var/remessa", $remessa["arquivo"]);
		} else if ($id_retorno) {
			$retorno = $this->cobranca->obtemRetornoPeloId($id_retorno);
			$nomecompleto = $retorno["arquivo"];
			$diretorio = substr($nomecompleto, 0, strrpos($nomecompleto, "/"));
			$arquivo = substr($nomecompleto, strrpos($nomecompleto, "/")+1);
			
			$this->criaDownload($diretorio, $arquivo);
		}
	}
}
?>
