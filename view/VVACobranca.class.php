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
			$relatorio = @$_REQUEST["relatorio"];
			$titulo = "Relatório :: ";
			switch($relatorio) {
				case "cortesias":					
					$this->_file = "relatorio_cortesia.html";	
					$this->atribui("titulo", $titulo."Cortesias");	
					break;
				case "cancelamentos":					
					$this->_file = "relatorio_cancelamento.html";	
					$this->atribui("titulo", $titulo."Cancelamentos");	
					break;
				case "atrasos":					
					$this->_file = "relatorio_atraso.html";	
					$this->atribui("titulo", $titulo."Atrasos");	
					break;		
				default:
					die("erro");	
			}
		}
		
	}
	
?>