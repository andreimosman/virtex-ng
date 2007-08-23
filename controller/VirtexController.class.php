<?

	/**
	 * Classe que faz o meio de campo entre a apresenca��o (view) e os dados (neg�cios)
	 */
	class VirtexController extends VirtexAdmin {
		protected $_view;			// view
		protected $_executar;		// Indica se � pra executar a fun��o executa();
		
		protected $_op;
		protected $acao;
		
		public static function &factory($tipo,$controller) {
			$tipo = strtolower($tipo);
			$controller = strtolower($controller);
			
			return $tipo == "admin" ? VirtexControllerAdmin::factory($controller) : VirtexControllerUsuario::factory($controller);
			
		}
		
		protected function __construct() {
			parent::__construct();
			// Dados padr�o que podem ser sobrescritos em init()
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
				$this->_view->exibe();
			}
		}
	
	
	}

?>
