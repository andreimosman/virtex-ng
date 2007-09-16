<?
	/**
	 *
	 * Classe responsável por montar a tela de login.
	 * classe chamada diretamente de VirtexControllerAdmin.
	 *
	 */
	class VVALogin extends VirtexViewAdmin {
	
		public function __construct() {
			parent::__construct();
		}
		
		public function exibe() {
			$this->_file = "login.html";
			
			
			
			
			parent::exibe();
			
			
		}

	}

?>
