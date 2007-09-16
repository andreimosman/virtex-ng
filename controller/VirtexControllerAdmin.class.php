<?
	/**
	 * Controller p/ Interface Administrativa
	 */
	
	class VirtexControllerAdmin extends VirtexController {

		protected $preferencias;
		protected $privilegios;
	
		protected function __construct() {
			parent::__construct();
		}
		

		public static function &factory($controller) {
			
			$controller = strtolower($controller);
			
			switch($controller) {
				case 'login':
					$retorno = new VCALogin();
					break;
					
				case 'index':
					$retorno = new VCAIndex();
					break;

				case 'clientes':
					$retorno = new VCAClientes();
					break;
					
				case 'configuracoes':
					$retorno = new VCAConfiguracoes();
					break;
				
				case 'administracao':
					$retorno = new VCAAdministracao();
					break;
					
				case 'suporte':
					$retorno = new VCASuporte();
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
			$this->preferencias = VirtexModelo::factory("preferencias");
			$this->_loginScript = "admin-login.php";			
			$this->_changePasswordScript = "admin-administracao.php?op=altsenha";
		}
		
		protected function executa() {
			// echo "EXEC<br>\n";
		}
		
		
		/**
		 * Verificações específicas da interface de administração.
		 */
		protected function verificaLogin() {
			// Verifica as regras básicas
			if( !parent::verificaLogin() ) {
				return false;
			}
			
			if( $this->_login->obtem("tipo") != "ADMIN" ) {
				return false;
			}
			
			$privilegios = $this->_login->obtem("privilegios");
			if( !is_array($privilegios) ) {
				return false;
			}
			
			// Joga os privilégios dentro do objeto;
			$this->privilegios = $privilegios;
			
			$primeiroLogin = $this->_login->obtem("primeiroLogin");
			if( !$primeiroLogin ) {
				return(false);
			}
			
			// Joga o primeiro login dentro do objeto;
			$this->primeiroLogin = $primeiroLogin == "t" ? true : false;

			return(true);
			
		}
		
	}



?>
