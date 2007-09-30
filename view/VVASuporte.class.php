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
				default:
					// Do something
			}
			
			parent::exibe();
		}
		
		protected function exibeFerramentas() {
			switch( $this->obtem("ferramenta") ) {
				case 'ipcalc':
					$this->_file = "suporte_ferramentas_ipcalc.html";
					$this->atribui("titulo","Calculadora IP");
					break;
				case 'arp':
					$this->_file = "suporte_ferramentas_arp.html";
					$this->atribui("titulo","Tabela ARP");
					break;
				case 'ping';
					$this->_file = "suporte_ferramentas_ping.html";
					$this->atribui("titulo","PING");
					break;
				default:
					// Do something
			}
		}
	
	
	
	
	
	
	}
	
?>