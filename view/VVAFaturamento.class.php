<?

	class VVAFaturamento extends VirtexViewAdmin {
	
		protected function __construct() {
			parent::__construct();
			$this->configureMenu(array(),false,false);	// Configura um menu vazio
		}
		
		protected function init() {
			parent::init();
			$this->nomeSessao = "Faturamento";
		}
		
		public function exibe() {

		switch($this->obtem("op")) {
				case 'relatorios':
					$this->exibeRelatorio();
					break;
			
			}
			
			parent::exibe();

		}

		
		public function exibeRelatorio() {
			
			$relatorio = @$_REQUEST["relatorio"];
			$titulo = "Relatório :: ";
			
			
			switch($relatorio) {
				
				case "previsao":	
					$this->_file = "faturamento_previsao.html";	
					$this->atribui("titulo", $titulo."Previsão de Faturamento");	
					break;
				
				case "faturamento":					
					$this->_file = "faturamento_corporativo.html";	
					$this->atribui("titulo", $titulo."Faturamento Corporativo");	
					break;
				
				case "por_produto":					
					$this->_file = "faturamento_produto.html";	
					$this->atribui("titulo", $titulo."Faturamento por Produto");	
					break;
					
				case "por_periodo":					
					$this->_file = "faturamento_periodo.html";	
					$this->atribui("titulo", $titulo."Faturamento por Período");	
					break;
			}
		}

		
		
	}
?>
