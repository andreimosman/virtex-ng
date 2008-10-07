<?
	/**
	 *
	 * Classe respons�vel por montar a tela principal.
	 * - inclui responsabilidade pelos menus.
	 *
	 */
	class VVAIndex extends VirtexViewAdmin {
		
		protected $_menu;
		
		public function __construct() {
			parent::__construct();
		}
		
		/**
		 * Carrega o template de menu utilizando as informa��es configuradas. (ex: privil�gios)
		 */
		public function carregaMenu() {
		
		}
		
		public final function exibe() {
			// echo "EXIBINDO...";
			
			$this->_file = "index.html";
			
			parent::exibe();
		}

	}

