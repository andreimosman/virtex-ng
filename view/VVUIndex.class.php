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
				case 'contrato_faturas':
					$this->exibeContratoFaturas();
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
			$imagemHeader = "view/templates/imagens/header_central.jpg";
			$imagemHeaderPersonalizada = "view/templates/imagens/header_central_personalizado.jpg";
			
			/**
			 * Se existir o arquivo header_central_personalizado.jpg o sistema irá exibí-lo logo acima do menu.
			 * Tamanho: 603x70
			 */
			
			if( file_exists($imagemHeaderPersonalizada) ) {
				$imagemHeader = $imagemHeaderPersonalizada;
			}
			
			$this->atribui("imagem_header",$imagemHeader);
		
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
		
		protected function exibeContratoFaturas() {
			$this->_file = "contrato_faturas.html";
		}

	}

?>
