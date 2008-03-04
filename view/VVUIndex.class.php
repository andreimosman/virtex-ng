<?
	/**
	 *
	 * Classe responsável por montar a tela de login.
	 * classe chamada diretamente de VirtexControllerUsuario.
	 *
	 */
	class VVUIndex extends VirtexViewUsuario {
	
		public function __construct() {
			parent::__construct();
		}
		
		public function exibe() {
		
			// echo "VZ: " . $this->obtemVisualizacao();
		
			
			switch($this->obtemVisualizacao()) {
				case 'menu':
					$this->exibeMenu();
					break;
				case 'home':
					$this->exibeHome();
					break;
				case 'contratos':
					$this->exibeContratos();
					break;
				case 'dados_cadastrais':
					$this->exibeDadosCadastrais();
					break;
				case 'contrato_detalhe':
					$this->exibeContratoDetalhe();
					break;
					
				default:
					$this->exibeIndex();
			
			}
			
			
			
			parent::exibe();
		}
		
		protected function exibeIndex() {
			$this->_file = "index.html";
		}


		protected function exibeMenu() {
			$this->_file = "menu.html";
		}


		protected function exibeHome() {		
			$this->_file = "home.html";
		}
		
		protected function exibeContratos() {		
			$this->_file = "contratos.html";
		}
		
		protected function exibeDadosCadastrais() {		
			$this->_file = "dados_cadastrais.html";
		}
		
		protected function exibeContratoDetalhe() {		
			$this->_file = "contrato_detalhe.html";
		}

	}

?>
