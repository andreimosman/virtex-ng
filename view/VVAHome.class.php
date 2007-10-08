<?

	class VVAHome extends VirtexViewAdmin {
	
		protected function __construct() {
			parent::__construct();
			$this->configureMenu(array(),false,false);	// Configura um menu vazio
		}
		
		protected function init() {
			parent::init();
			$this->nomeSessao = "Seja bem vindo";
		}
		
		public function exibe() {
			$this->_file = "home.html";


			parent::exibe();
		}
	}
?>
