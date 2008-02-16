<?

	/**
	 * Controller Base da Interface do Usuário do Provedor
	 */
	class VirtexControllerUsuario extends VirtexController {
	
		protected $eventos;
		protected $preferencias;
		
	
		protected function __construct() {
			parent::__construct();
			
		}
		
		public static function &factory($controller) {
			
			$controller = strtolower($controller);
			
			switch($controller) {
			
				case 'login':
					$retorno = new VCULogin();
					break;
					
				case 'index':
					$retorno = new VCUIndex();
					break;
				
				default:
					throw new ExcecaoController(255,"Controller não encontrado");
			
			}
			
			return($retorno);
		}
	
		protected function selfConfig() {
		
		}
	
		protected function init() {
			parent::init();
			$this->eventos		= VirtexModelo::factory("eventos");
			$this->preferencias = VirtexModelo::factory("preferencias");
			
			$this->_loginScript = "login.php";
			//$this->_changePasswordScript = "admin-administracao.php?op=altsenha";
			//$this->_registroScript = "admin-configuracoes.php?op=preferencias&tela=registro";
			
		}
		
		
		protected function verificaLogin() {
			// Verifica as regras básicas
			if( !parent::verificaLogin() ) {
				return false;
			}
			
			if( $this->_login->obtem("tipo") != "USUARIO" ) {
				return false;
			}
			
			// TODO: Redirecionamento p/ aceite de contrato????

			return(true);
		}
		
		
	}

?>
