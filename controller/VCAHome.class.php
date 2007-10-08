<?

	class VCAHome extends VirtexControllerAdmin {
	
		public function __construct() {
			parent::__construct();
		}
		
		public function init() {
			parent::init();
			$this->_view = VirtexViewAdmin::factory("home");
		}	
	
	}
?>
