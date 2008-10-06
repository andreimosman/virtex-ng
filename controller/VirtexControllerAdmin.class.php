<?
	/**
	 * Controller p/ Interface Administrativa
	 */
	
	class VirtexControllerAdmin extends VirtexController {

		protected $eventos;
		protected $preferencias;
		
	
		protected function __construct() {
			parent::__construct();
		}
		

		public static function &factory($controller) {
			
			$controller = strtolower($controller);
			
			switch($controller) {
				case 'setup':
					$retorno = new VCASetup();
					break;
					
				case 'js':
					$retorno = new VCAJs();
					break;
			
				case 'ajax':
					$retorno = new VCAAjax();
					break;
					
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
					
				case 'home':
					$retorno = new VCAHome();
					break;
					
				case 'cobranca':
					$retorno = new VCACobranca();
					break;
					
				case 'faturamento':
					$retorno = new VCAFaturamento();
					break;

				case 'cadastro':
					$retorno = new VCACadastro();
					break;
					
				case 'financeiro':
					$retorno = new VCAFinanceiro();
					break;
				
				case 'barcode':
					$retorno = new VCABarcode();
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
			
			$this->_loginScript = "admin-login.php";
			$this->_setupScript = "admin-setup.php";
			$this->_changePasswordScript = "admin-administracao.php?op=altsenha";
			
			$this->_registroScript = "admin-administracao.php?op=preferencias&tela=registro";
			
		}
		
		protected function executa() {

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
			
			$primeiroLogin = $this->_login->obtem("primeiroLogin");
			if( !$primeiroLogin ) {
				return(true);
			}
			
			// Joga o primeiro login dentro do objeto;
			$this->primeiroLogin = $primeiroLogin == "t" ? true : false;

			return(true);
			
		}
		
		/**
		 * Verificações de licença específicas da interface de administração.
		 */
		protected function verificaRegistro() {
			// Verifica as regras básicas.
			if( !parent::verificaRegistro() ) {
				return false;
			}
						
			return true;			
			
		}
		
		
		protected function acessoNegado() {		
			$this->_view = VirtexViewAdmin::factory("erro");
			$this->_view->atribui("tipo","acessonegado");
			$this->_view->exibe();
			exit(0);
		}

		
		protected function requirePrivLeitura($privilegio,$stop=true) {		
			if( !$this->_login->podeLer($privilegio) ) {
				if( $stop ) {
					$this->acessoNegado();
				}
				return(false);
			}
			return(true);
		}
		
		protected function requirePrivGravacao($privilegio,$stop=true) {
			if( !$this->_login->podeGravar($privilegio) ) {
				if( $stop ) {
					$this->acessoNegado();
				}
				return(false);
			}
			return(true);
		}
	
	


		
	}

