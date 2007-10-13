<?

	class VVACobranca extends VirtexViewAdmin {
	
		protected function __contruct() {
			parent::__construct();
		}
		
		protected function init() {
			parent::init();
			$this->nomeSessao = "Cobrança";
		}
		
		public function exibe() {
			
			switch($this->obtem("op")) {

				case 'bloqueios':
					$this->exibeBloqueios();
					break;
					
				case 'amortizacao':
					$this->exibeAmortizacao();
					break;
					
				case 'gerar_cobranca':
					$this->exibeGerarCobranca();
					break;
					
				case 'arquivos':
					$this->exibeArquivos();
					break;
					
				case 'relatorios':
					$this->exibeRelatorios();
					break;
			
			}
		
			parent::exibe();
		
		}
		
		protected function exibeBloqueios() {
		
		}
		
		protected function exibeAmortizacao() {
			$this->_file = "cobranca_amortizacao.html";
			$this->atribui("titulo", "Amortização");
		}
		
		protected function exibeGerarCobranca() {
		
		}
		
		protected function exibeArquivos() {
		
		}
		
		protected function exibeRelatorios() {
		
		}
		
	}
	
?>