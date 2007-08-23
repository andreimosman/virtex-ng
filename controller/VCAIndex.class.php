<?

	class VCAIndex extends VirtexControllerAdmin {
	
		public function __construct() {
			parent::__construct();
		}
	
		protected function init() {
			// Inicializações da SuperClasse
			parent::init();
			$this->_view = VirtexViewAdmin::factory("index");

		}

		protected function executa() {
		}
		
	}

?>
