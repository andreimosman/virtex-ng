<?

	class VVADummy extends VirtexViewAdmin {
	
		protected $titulo;

		protected function __contruct() {
			parent::__construct();
		}

		protected function init() {
			parent::init();
		}

		public function exibe() {

			switch( $this->_visualizacao ) {
				default:
					$this->exibePadrao();
					break;
			}
			
			parent::exibe();

		}
		
		protected function exibePadrao() {
			ob_start();
			$caminhoParaOArquivo = $this->obtem("linkDownload");
			$nomeArquivo = $this->obtem("nomeArquivo");
			
			header("Cache-Control: public, must-revalidate"); 
			header("Pragma: hack"); 
			header("Content-Type: application/octet-stream"); 
			header("Content-Length: " .(string)(filesize($caminhoParaOArquivo)) ); 
			header('Content-Disposition: attachment; filename="'.$nomeArquivo.'"'); 
			header("Content-Transfer-Encoding: binary\n"); 
			ob_end_clean();
			
			readfile($caminhoParaOArquivo); 
			

		}
		
	}

?>