<?

	/**
	 * Classe que faz o meio de campo entre a apresencação (view) e os dados (negócios)
	 */
	class VirtexController extends VirtexAdmin {
		protected $_view;			// view
		protected $_executar;		// Indica se é pra executar a função executa();

		protected $_op;
		protected $acao;

		protected $_script;

		// Recursos de autenticação
		protected $_login;			// Objeto de autenticação
		protected $_loginScript;	// URL p/ autenticação
		protected $primeiroLogin;
		protected $_changePasswordScript;
		protected $_uri;
		protected $_setupScript;

		protected $_registroScript;

		protected $remote_addr;

		public static function &factory($tipo,$controller) {
			$tipo = strtolower($tipo);
			$controller = strtolower($controller);
			$retorno = $tipo == "admin" ? VirtexControllerAdmin::factory($controller) : VirtexControllerUsuario::factory($controller);
			return $retorno;

		}

		protected function verificaLogin() {

			// Validação básica (comum à todas interfaces);
			if( @$this->_login->obtemUsername() ) {
				return(true);
			}

			return(false);

		}

		protected function verificaRegistro() {

			if( !$this->licenca->isValid() ) {
				return(false);
			} else {
				if($this->licenca->congelou()) {
					return(false);
				}

				if($this->licenca->expirou()) {
					if( $this->_view ) $this->_view->atribui("congelamento", 1);
				}
			}

			return(true);

		}
		
		protected function criaDownload($caminhoParaOArquivo,$nomeArquivo) {
			$tmpview = VirtexViewAdmin::factory("dummy");
			
			$linkDownload = "";
			$linkDownload .= $caminhoParaOArquivo;
			$linkDownload .= ($linkDownload) ? "/$nomeArquivo" : $nomeArquivo;
			
			$tmpview->atribui("linkDownload", $linkDownload);
			$tmpview->atribui("nomeArquivo", $nomeArquivo);
			$tmpview->exibe();
		}
		
		
		protected function __construct() {
			parent::__construct();

			// Limites de operação do sistema.			
			ini_set('max_execution_time', 120);
			ini_set('max_input_time', 120);
			ini_set('memory_limit', '512M');
			
			// Dados padrão que podem ser sobrescritos em init()
			$this->_executar 	= true;

			$this->ipaddr = $_SERVER["REMOTE_ADDR"];


			$this->_login = Login::getInstance();

			$tmp = split("/",@$_SERVER["SCRIPT_FILENAME"]);
			$this->_script = $tmp[ count($tmp) -1 ];

			$tmp = split("/",@$_SERVER["REQUEST_URI"]);
			$this->_uri = $tmp[ count($tmp) -1 ];

			unset($tmp);
			
			if( $this->_loginScript && $this->_script ) {
			
				// if(  || ($this->_setupScript && $this->_script != $this->_setupScript ) {
				
				$verificaLogin = true;
				
				if( $this->_setupScript && $this->_script == $this->_setupScript ) $verificaLogin = false;
				
				
				if( $verificaLogin && $this->_script != $this->_loginScript ) {

					if( !$this->verificaLogin() ) {
						// Sem login. Redirecionar.
						if( $this->_view ) {
							$this->_view->redirect($this->_loginScript);
						} else {
							VirtexView::simpleRedirect($this->_loginScript);
						}
						return;
					}
					
					// echo "PL: " . $this->primeiroLogin . "<br>\n";
					
					if( !$this->verificaRegistro() ) {
						// $this->primeiroLogin = 't';
						$this->primeiroLogin = 0;
						
					} else {
										
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
										
					if( $this->_registroScript && $this->_script ) {
						$acao = @$_REQUEST["acao"];
						if( $acao != "upload" && $this->_uri != $this->_registroScript ) {
							if( !$this->verificaRegistro() ) {
								if( $this->_view ) {
									$this->_view->redirect($this->_registroScript);
								} else {
									VirtexView::simpleRedirect($this->_registroScript);
								}
							}
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
		 * Método para configuração do objeto
		 *
		 * Usado geralmente pra setar $this->_startdb e $this->_config
		 */
		protected function selfConfig() {
			parent::selfConfig();
		}

		protected function init() {
			parent::init();
			// Funções de Inicialização
		}

		protected function executa() {
			// Executa as functionalidades básicas

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
