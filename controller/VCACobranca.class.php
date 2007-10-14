<?


	class VCACobranca extends VirtexControllerAdmin {
	
		protected $cobranca;
	
		public function __construct() {
			parent::__construct();
		}
		
		protected function init() {
			parent::init();
			
			$this->cobranca = VirtexModelo::factory('cobranca');
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
			
			
			}
			
		}
		
		protected function executaBloqueios() {
		
		}
		
		protected function executaAmortizacao() {
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
		
		protected function executaGerarCobranca() {
		
		}
		
		protected function executaArquivos() {
		
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
			} elseif("cancelamentos" == $relatorio) {
				$periodo = isset($_REQUEST["periodo"]) ? $_REQUEST["periodo"] : 12;
				$this->_view->atribui("periodo", $periodo); 
				$cobranca = VirtexModelo::factory("cobranca"); 
				$cancelados = $cobranca->obtemContratosCanceladosPorPeriodo($periodo);
				$this->_view->atribui("cancelados", $cancelados);
				
				$i = ( $periodo * -1 ) + 1;
				$meses = array();
				for(;$i<=0;$i++){
					$data = MData::calculaPeriodo(mktime(),$i,"m/Y");
					list($meses[$data]["mes"],$meses[$data]["ano"]) = split("/",$data);
				}
				$this->_view->atribui("meses", $meses);								
			}
			
		}
		
		
	
	
	}


?>
