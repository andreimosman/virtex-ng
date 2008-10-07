<?

	class VVASetup extends VirtexViewAdmin {

		public function __construct() {
			parent::__construct();
			
		}

		public function exibe() {
			$this->_file = "setup.html";
			parent::exibe();
		}


	}

