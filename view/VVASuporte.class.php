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
				case 'links':
					$this->exibeLinks();
					break;
				case 'relatorios':
					$this->exibeRelatorios();
					break;
				case 'helpdesk':
					$this->exibeHelpdesk();
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
		
		protected function exibeLinks() {
			$this->_file = "suporte_links.html";
			$this->atribui("titulo","Links Externos");
		}
		
		protected function exibeRelatorios() {
			$titulo = "Relatórios";
			
			switch($this->obtem("relatorio")) {
				case 'cliente_sem_mac':
					$titulo .= " :: Clientes Sem MAC";
					$this->_file = "suporte_relatorios_semmac.html";
					
					break;
				case 'banda':
					$titulo .= " :: Clientes por Banda";
					$this->_file = "suporte_relatorios_banda.html";
					break;
			}
			
			$this->atribui("titulo",$titulo);
		
		}
		
		
		protected function exibeHelpdesk() {
			$titulo = "Helpdesk";
			
			switch($this->obtem($tela)) {
				case 'listagem':
				default:
					$titulo .= " :: Lista de Chamados";
					$this->_file = "suporte_helpdesk_chamado.html";
					break;
			}
		
		}
	
	
	
	
	
	
	}
	
?>