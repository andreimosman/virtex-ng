<?

	/**
	 * Classe que faz o meio de campo entre a apresencação (view) e os dados (negócios)
	 */
	class VirtexController extends VirtexAdmin {
		protected $_view;			// view
		protected $_executar;		// Indica se é pra executar a função executa();
		
		protected $_op;
		protected $acao;
		
		public static function &factory($tipo,$controller) {
			$tipo = strtolower($tipo);
			$controller = strtolower($controller);
			
			return $tipo == "admin" ? VirtexControllerAdmin::factory($controller) : VirtexControllerUsuario::factory($controller);
			
		}
		
		protected function __construct() {
			parent::__construct();
			// Dados padrão que podem ser sobrescritos em init()
			$this->_executar 	= true;
			

			$this->_op			= @$_REQUEST["op"];
			$this->_acao 		= @$_REQUEST["acao"];

			if( $this->_view ) {
				$this->_view->atribui("op",$this->_op);
				$this->_view->atribui("acao",$this->_acao);
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
				$this->_view->exibe();
			}
		}
	
	
	}

?>
