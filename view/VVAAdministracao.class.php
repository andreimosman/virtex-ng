<?

	class VVAAdministracao extends VirtexViewAdmin {

		public function __construct() {
			parent::__construct();
		}
		
		protected function init() {
			parent::init();
			$this->nomeSessao = "Administração";
		}

		protected function obtemItensMenu() {
			$itensMenu = array();
			
			switch($this->obtem("op")) {
				case 'planos':
					$itensMenu[] = array("texto" => "Novo: Banda Larga", "url" => "admin-administracao.php?op=planos&tela=cadastro&tipo=BL");
					$itensMenu[] = array("texto" => "Novo: Discado", "url" => "admin-administracao.php?op=planos&tela=cadastro&tipo=D");
					$itensMenu[] = array("texto" => "Novo: Hospedagem", "url" => "admin-administracao.php?op=planos&tela=cadastro&tipo=H");
					break;
				case 'administradores':
					$itensMenu[] = array("texto" => "Novo: Administrador", "url"=> "admin-administracao.php?op=administradores&tela=cadastro");
				default:
					// Do something
			}
			
			return($itensMenu);
			
		}

		public function exibe() {
			switch($this->_visualizacao) {
				case 'altsenha':
					$this->exibeAlteracaoSenha();
					break;
				case 'administradores':
					$this->exibeAdministradores();
					break;
				case 'planos':
					$this->exibePlanos();
					break;
				case 'produtos':
					$this->exibeProdutos();
					break;
				case 'ferramentas':
					$this->exibeFerramentas();
					break;
				case 'relatorios':
					$this->exibeRelatorios();
					break;
				default:
					// Do Something
			}
			
			parent::exibe();
		}
		
		protected function exibeAlteracaoSenha() {
			$dadosLogin = $this->obtem("dadosLogin");
			echo "<pre>";
			print_r($dados_login);
			echo "</pre>";
			$this->_file = "administracao_altsenha.html";
			$titulo = " :: ".(@$dadosLogin["nome"])." :: Alteração de Senha";
			
			$this->nomeSessao .= $titulo;
		}
		
		protected function exibeAdministradores() {
			$titulo = " :: Administradores";
			switch($this->obtem("tela") ) {
				case 'cadastro':
					if($this->obtem("id_admin")) {
						$titulo .= " :: Alteração";
					}else {
						$titulo .= " :: Cadastro";
					}
					
					$this->configureMenu($this->obtemItensMenu(),false,true);
					$this->_file = "administracao_admin_cadastro.html";
					break;
				case 'privilegio':	
					$titulo .= " :: Privilégios";
					$this->configureMenu($this->obtemItensMenu(),false,true);
					$this->_file = "administracao_admin_privilegio.html";				
				break;	
				case 'listagem':
					$titulo .= " :: Listagem";
					$this->_file = "administracao_admin_listagem.html";
					$this->configureMenu($this->obtemItensMenu(), true, true);
					break;
				default:
					//Do Something
			}
			
			$this->nomeSessao .= $titulo;
		}
		
		protected function exibePlanos() {
			$titulo = "Planos";
			switch( $this->obtem("tela") ) {
				case 'cadastro':
					if( $this->obtem("id_produto") ) {
						$titulo .= " :: Alteração :: " . $this->obtem("nome");
					} else {
						$titulo .= " :: Cadastro";
					}
					$this->configureMenu($this->obtemItensMenu(),false,true);
					$this->_file = "administracao_planos_cadastro.html";
					break;
				case 'listagem':
					$titulo .= " :: Listagem";
					$this->configureMenu($this->obtemItensMenu(),true,true);
					$this->_file = "administracao_planos_listagem.html";
					break;
				default:
					// Do something
			}
			
			$this->atribui("titulo",$titulo);
		}
		
		protected function exibeProdutos() {
		
		}
		
		protected function exibeFerramentas() {
		
		}
		
		protected function exibeRelatorios() {
		
		}
		
	
	}

?>
