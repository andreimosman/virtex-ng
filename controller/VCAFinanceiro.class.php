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
		}
	}

	protected function executaBloqueios() {
	
		$this->_view->atribuiVisualizacao("cobranca");

		$contrato_id = @$_REQUEST["contrato_id"];
		$bloquear    = @$_REQUEST["bloquear"];
		$acao        = @$_REQUEST["acao"];
		$url 		 = "admin-financeiro.php?op=bloqueios";

		if( $acao == "bloquear" ) {

				try {
					$senha_admin = @$_REQUEST["senha_admin"];
					$dadosLogin = $this->_login->obtem("dados");
					if( !$senha_admin ) {
						$erro = "Cancelamento n�o autorizado: SENHA N�O FORNECIDA.";
					} elseif (md5(trim($senha_admin)) != $dadosLogin["senha"] ) {
						$erro = "Opera��o n�o autorizada: SENHA N�O CONFERE.";
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

							$this->contas->gravaLogBloqueioAutomatizado($k, 'S', $admin, 'Suspens�o por pend�ncias financeiras');
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

		$atrasados = $this->cobranca->obtemContratosFaturasAtrasadasBloqueios(30);
		$this->_view->atribui("atrasados", $atrasados);

		//echo "<pre>";
		//print_r($atrasados);
		///echo "</pre>";

		$countBloqueados = count($atrasados);
		$this->_view->atribui("countBloqueados", $countBloqueados);
	}

	protected function executaAmortizacao() {
	
		$this->_view->atribuiVisualizacao("cobranca");
	
		$this->requirePrivGravacao("_COBRANCA_AMORTIZACAO");
		$id_cobranca = @$_REQUEST["id_cobranca"];

		$texto_pesquisa = @$_REQUEST["texto_pesquisa"];
		$tipo_pesquisa  = @$_REQUEST["tipo_pesquisa"];

		if(!$tipo_pesquisa) $tipo_pesquisa = "CODIGOBARRAS";

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
			}

			if( !count($fatura) ) {
				$erro = "Nenhuma fatura encontrada com o n�mero fornecido.";
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
	
		$this->_view->atribuiVisualizacao("cobranca");

		$acao = @$_REQUEST["acao"];
		$ano = @$_REQUEST["ano"];
		$mes = @$_REQUEST["mes"];
		$periodo = @$_REQUEST["periodo"];


		$formas = $this->preferencias->obtemFormasPagamentoGerarCobranca();
		$this->_view->atribui("formas",$formas);

		$bancos = $this->preferencias->obtemListaBancos();
		$this->_view->atribui("bancos",$bancos);


		//echo "<pre>";
		//print_r($formas);
		//echo "</pre>";



		if ($acao == "gerar") {
			$dados = array();

			//Insere informa��es da remessa de cobranca
			$data_referencia = "$ano-$mes";
			$data_referencia_dia1 = "$ano-$mes-01";

			//COLOCAR ESQUEMA DE PEGAR ID DO USUARIO
			$dadosLogin = $this->_login->obtem("dados");
			$id_admin = $dadosLogin["id_admin"];
			$id_forma_pagamento = @$_REQUEST["id_forma_pagamento"];

			//phpinfo();

			//echo "<pre>";
			//print_r($_REQUEST);
			//echo "</pre>";

			//die;

			$resultado = $this->cobranca->obtemFaturasPorPeriodoSemCodigoBarraPorTipoPagamento($data_referencia, $periodo, $id_forma_pagamento);

			if (!$resultado){
				$msg = "N�o foram encontradas faturas para esse per�odo.";
			} else {
				$id_remessa = $this->cobranca->cadastraLoteCobranca($data_referencia_dia1, $periodo, $id_admin,$id_forma_pagamento);
				$formaPagto = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);
				$msg = "Remessa cadastrada com sucesso.";
			}

			$urlPreto = "view/templates/imagens/preto.gif";
			$urlBranco = "view/templates/imagens/branco.gif";

			/*echo "<pre>";
			print_r($formaPagto);
			echo "</pre>";*/


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

				// $msg = "O Lote foi gerado para o per�odo com sucesso!";

			}

			$url = "admin-cobranca.php?op=gerar_cobranca";
			$this->_view->atribui("url",$url);
			$this->_view->atribui("mensagem", $msg);
			$this->_view->atribuiVisualizacao("msgredirect");

			// VirtexView::simpleRedirect($url);

		}

		$periodo_anos_fatura = $this->cobranca->obtemAnosFatura();

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
	
		$tela = @$_REQUEST["tela"];
		$this->_view->atribui("tela",$tela);

	}

	protected function executaRelatoriosCobranca() {
	
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
				$mesesAno["03"]="Mar�o";
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

			//echo "<pre>";
			//print_r($periodoAnoMes);
			//echo "</pre>";
		}
	}
	
	
	protected function executaRelatoriosFaturamento() {

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
}
?>