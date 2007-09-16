<?

	/**
	 * Classe que faz o meio de campo entre a apresenca��o (view) e os dados (neg�cios)
	 */
	class VirtexController extends VirtexAdmin {
		protected $_view;			// view
		protected $_executar;		// Indica se � pra executar a fun��o executa();
		
		protected $_op;
		protected $acao;
		
		protected $_script;
		
		// Recursos de autentica��o
		protected $_login;			// Objeto de autentica��o
		protected $_loginScript;	// URL p/ autentica��o
		protected $primeiroLogin;
		protected $_changePasswordScript;
		protected $_uri;
		
		public static function &factory($tipo,$controller) {
			$tipo = strtolower($tipo);
			$controller = strtolower($controller);
			
			return $tipo == "admin" ? VirtexControllerAdmin::factory($controller) : VirtexControllerUsuario::factory($controller);
			
		}
		
		protected function verificaLogin() {
			
			// Valida��o b�sica (comum � todas interfaces);
			if( @$this->_login->obtemUsername() ) {
				return(true);
			}
					
			return(false);
		
		}
		
		protected function __construct() {
			parent::__construct();
			// Dados padr�o que podem ser sobrescritos em init()
			$this->_executar 	= true;
			
			
			$this->_login = Login::getInstance();
			
			$tmp = split("/",@$_SERVER["SCRIPT_FILENAME"]);
			$this->_script = $tmp[ count($tmp) -1 ];

			$tmp = split("/",@$_SERVER["REQUEST_URI"]);
			$this->_uri = $tmp[ count($tmp) -1 ];
			
			unset($tmp);
			
			if( $this->_loginScript && $this->_script ) {
				if( $this->_script != $this->_loginScript ) {
					if( !$this->verificaLogin() ) {
						// Sem login. Redirecionar.
						if( $this->_view ) {
							$this->_view->redirect($this->_loginScript);
						} else {
							VirtexView::simpleRedirect($this->_loginScript);
						}
						return;
					}
					
					if($this->primeiroLogin && $this->_changePasswordScript ) {
						if( $this->_uri != $this->_changePasswordScript ) {
							if( $this->_view ) {
								$this->_view->redirect($this->_changePasswordScript);
							} else {
								VirtexView::simpleRedirect($this->_changePasswordScript);
							}
							return;						
						}
						
					}

				}

			}
			
			
			$this->_op			= @$_REQUEST["op"];
			$this->_acao 		= @$_REQUEST["acao"];

			if( $this->_view ) {
				$this->_view->atribui("op",$this->_op);
				$this->_view->atribui("acao",$this->_acao);
				$this->_view->atribui("dadosLogin",$this->_login->obtem("dados"));
			}
			
			if( $this->_executar ) {
				$this->executa();
			}
			$this->exibe();
		}
		
		/**
		 * M�todo para configura��o do objeto
		 *
		 * Usado geralmente pra setar $this->_startdb e $this->_config
		 */
		protected function selfConfig() {
			parent::selfConfig();
		}
		
		protected function init() {
			parent::init();
			// Fun��es de Inicializa��o
		}
		
		protected function executa() {
			// Executa as functionalidades b�sicas
			
			// Configura a view
		}
		
		protected function exibe() {
			// Exibe a view
			if( isset($this->_view) ) {
				//print_r($this->_view);
				$this->_view->exibe();				
			}
		}
	
	
	}

?>
