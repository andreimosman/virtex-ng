<?

class VCACobranca extends VirtexControllerAdmin {

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
		$this->_view = VirtexViewAdmin::factory('cobranca');
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
			case 'relatorios':
				$this->executaRelatorios();
				break;
			case 'gerar_lista_boletos':
				$this->geraListaBoletos();
				break;
		}
	}

	protected function executaBloqueios() {

		$contrato_id = @$_REQUEST["contrato_id"];

		if ($contrato_id) {
			$dadosLogin = $this->_login->obtem("dados");
			$admin = $dadosLogin["admin"];
			$id_admin = $dadosLogin["id_admin"];

			foreach($contrato_id as $k => $v) {

				$listacontas = $this->contas->obtemContasPeloContrato($k, $v);

				foreach($listacontas as $chave => $campo) {
					if($v == "BL") {
						$this->contas->alteraContaBandaLarga($k, NULL, 'S');
					} else if($v == "D") {
						$this->contas->alteraContaDiscado($k, NULL, 'S');
					} else if($v == "H") {
						$this->contas->alteraHospedagem($k, NULL, 'S');
					}
					$this->contas->gravaLogMudancaStatusConta($campo["id_cliente_produto"], $campo["username"], $campo["dominio"], $campo["tipo_conta"], $id_admin, '');
				}

				$this->contas->gravaLogBloqueioAutomatizado($k, 'S', $admin, 'Suspensão por pendências financeiras');
			}
		}

		$atrasados = $this->cobranca->obtemContratosFaturasAtrasadas();
		$this->_view->atribui("atrasados", $atrasados);
		
		$countBloqueados = count($atrasados);
		$this->_view->atribui("countBloqueados", $countBloqueados);
		
		echo "<pre>";
		print_r($countBloqueados);
		echo "</pre>";
	}

	protected function executaAmortizacao() {
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

	protected function geraListaBoletos() {

		$id_remessa = @$_REQUEST["id_remessa"];

		////echo $id_remessa;

		$resultado = $this->cobranca->obtemFaturasPorRemessa($id_remessa);

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


			if( $formaPagto["tipo_cobranca"] == "BL" && $id_forma_pagamento=="11" ) {

				$urlPreto = "view/templates/imagens/preto.gif";
				$urlBranco = "view/templates/imagens/branco.gif";

				echo MBanco::htmlBarcode($codigo_barra,$urlPreto,$urlBranco);


			}

		}

	}

	protected function executaGerarCobranca() {

		$acao = @$_REQUEST["acao"];
		$ano = @$_REQUEST["ano"];
		$mes = @$_REQUEST["mes"];
		$periodo = @$_REQUEST["periodo"];

		$formas = $this->preferencias->obtemFormasPagamento();
		$this->_view->atribui("formas",$formas);

		//echo "<pre>";
		//print_r($formas);
		//echo "</pre>";



		if ($acao == "gerar") {
			$dados = array();

			//Insere informações da remessa de cobranca
			$data_referencia = "$ano-$mes";
			$data_referencia_dia1 = "$ano-$mes-01";

			//COLOCAR ESQUEMA DE PEGAR ID DO USUARIO
			$dadosLogin = $this->_login->obtem("dados");
			$id_admin = $dadosLogin["id_admin"];
			$id_forma_pagamento = @$_REQUEST["tipo_pagamento"];

			//phpinfo();

			$id_remessa = $this->cobranca->cadastraLoteCobranca($data_referencia_dia1, $periodo, $id_admin);
			$resultado = $this->cobranca->obtemFaturasPorPeriodoSemCodigoBarraPorTipoPagamento($data_referencia, $periodo, $id_forma_pagamento);

			$formaPagto = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);



			//return;


			if (!$resultado){

				$msg = "Não foram encontradas faturas para esse período";

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

				$msg = "O Lote foi gerado para o período com sucesso!";

			}
		}
		$periodo_anos_fatura = $this->cobranca->obtemAnosFatura();
		$ultimas_remessas = $this->cobranca->obtemUltimasRemessas(50);

		$this->_view->atribui("periodo_anos", $periodo_anos_fatura);
		$this->_view->atribui("ultimas_remessas", $ultimas_remessas);
		$this->_view->atribui("msg", $msg);


		$ultimas_remessas = $this->cobranca->obtemUltimasRemessas("10");

	}

	protected function executaArquivos() {
		$tela = @$_REQUEST["tela"];
		$this->_view->atribui("tela",$tela);

	}

	protected function executaRelatorios() {

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
		}
	}
}
?>
