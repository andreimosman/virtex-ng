<?
	/**
	 *
	 * Classe responsável por montar a tela de login.
	 * classe chamada diretamente de VirtexControllerUsuario.
	 *
	 */
	class VVULogin extends VirtexViewUsuario {
	
		public function __construct() {
			parent::__construct();
		}
		
		public function exibe() {
			$this->_file = "login.html";
			parent::exibe();
		}

	}

?>
