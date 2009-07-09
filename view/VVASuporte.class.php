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
				case 'snmp':
					$this->exibeSNMP();
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
		
		
		protected function obtemItensMenuHelpdesk() {
			$itensMenu = array();

			$itensMenu[] = array("texto" => "Novo: Chamado", "url" => "admin-suporte.php?op=helpdesk&tela=cadastro");
			
			return($itensMenu);
			
		}
		
		
		protected function exibeMonitoramento() {
			$this->_file = "suporte_monitoramento.html";
			$this->atribui("titulo","Monitoramento");
		}
		
		protected function exibeSnmp() {
			$this->_file = "suporte_snmp.html";
			$this->atribui("titulo","SNMP");
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
				case 'helpdesk':
					$titulo .= " :: Helpdesk";
					switch($this->obtem("tipo")) {
						case "ocorrencias":
						default:
							$titulo .= " :: Chamados/Ocorrências";
							$this->_file = "suporte_relatorios_helpdesk_ocorrencias.html";
						break;
					}
					break;
			}
			
			$this->atribui("titulo",$titulo);
		
		}
		
		
		protected function exibeHelpdesk() {
			$titulo = "Helpdesk";
			$subtela = $this->obtem("subtela");
			
			switch($this->obtem("tela")) {
			
				case 'cadastro':
					$titulo .= " :: Novo Chamado/Ocorr&ecirc;ncia";
					$this->_file = "suporte_helpdesk_chamado_novo.html";
					break;

				case 'alteracao':
					$titulo .= " :: Chamado #";
					$chamado = @$this->obtem("chamado");
					$titulo .= $chamado["id_chamado"];
					
					$this->_file = "suporte_helpdesk_chamado_alteracao.html";
					
					if( $subtela == "ordemservico") { 
						$titulo .= " :: Gerar Ordem de Serviço";
						$this->_file = "suporte_helpdesk_chamado_alteracao_ordemservico.html";
					}
					
					break;
			
				case 'listagem':
				default:
					
					if($this->obtem("subtela") == "mini") {
						$this->_file = "suporte_helpdesk_chamado_lista_mini.html";
						$titulo = "";
						$this->atribui("prtopt", 'sem_header');
					} else {
						$titulo .= " :: Lista de Chamados";
						$this->_file = "suporte_helpdesk_chamado.html";
						$this->configureMenu($this->obtemItensMenuHelpdesk());
					}
					break;
			}
			
			
			$this->atribui("titulo",$titulo);

		
		}
	
	}

