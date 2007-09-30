<?

	class VVASuporte extends VirtexViewAdmin {
	
		protected function __construct() {
			parent::__construct();
			$this->configureMenu(array(),false,false);	// Configura um menu vazio
		}
		
		protected function init() {
			parent::init();
			$this->nomeSessao = "Suporte";
		}
		
		public function exibe() {
			switch($this->_visualizacao) {
				case 'ferramentas':
					$this->exibeFerramentas();
					break;
				case 'monitoramento':
					$this->exibeMonitoramento();
					break;
				case 'graficos':
					$this->exibeGraficos();
					break;
				default:
					// Do something
			}
			
			parent::exibe();
		}
		
		protected function exibeFerramentas() {
			switch( $this->obtem("ferramenta") ) {
				case 'ipcalc':
					$this->_file = "suporte_ferramentas_ipcalc.html";
					$this->atribui("titulo","Ferramentas :: Calculadora IP");
					break;
				case 'arp':
					$this->_file = "suporte_ferramentas_arp.html";
					$this->atribui("titulo","Ferramentas :: Tabela ARP");
					break;
				case 'ping';
					$this->_file = "suporte_ferramentas_ping.html";
					$this->atribui("titulo","Ferramentas :: PING");
					break;
				default:
					// Do something
			}
		}
		
		protected function exibeMonitoramento() {
			$this->_file = "suporte_monitoramento.html";
			$this->atribui("titulo","Monitoramento");
		}
		
		protected function exibeGraficos() {
			$this->_file = "suporte_graficos.html";
			$this->atribui("titulo","Gráficos");
			
		}
	
	
	
	
	
	
	}
	
?>