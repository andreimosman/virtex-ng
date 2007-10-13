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
			
			if(!$tipo_pesquisa) $tipo_pesquisa = "LINHADIGITAVEL";
			
			$this->_view->atribui("texto_pesquisa", $texto_pesquisa);
			$this->_view->atribui("tipo_pesquisa",$tipo_pesquisa);
			
			if( $texto_pesquisa && $tipo_pesquisa ) {
				// Pesquisar!
				
				$fatura = array();
				switch($tipo_pesquisa) {

					case 'LINHADIGITAVEL':
						//$fatura = $this->cobranca->obtemFaturaPelaLinhaDigitavel($texto_pesquisa);
						break;
						
					case 'CODIGOBARRAS':
						//$fatura = $this->cobranca->obtemFaturaPeloCodigoBarras($texto_pesquisa);
						break;
				}
				
				if( !count($fatura) ) {
					$erro = "Nenhuma fatura encontrada com o número fornecido.";
					$this->_view->atribui("erro", $erro);
				} else {
					// Redireciona p/ a fatura.
					
				}
				
			}
			
			
			//if( $id_cobranca ) {
			//	$info = $this->cobranca->obtemFatura($id_cobranca);
			//	echo "<pre>";
			//	print_r($info);
			//	echo "</pre>";
			//}
		}
		
		protected function executaGerarCobranca() {
		
		}
		
		protected function executaArquivos() {
		
		}
		
		protected function executaRelatorios() {
		
		}
		
		
	
	
	}


?>
