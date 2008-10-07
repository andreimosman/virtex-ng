<?
	/**
	 *
	 * Classe responsável por montar a tela principal.
	 * - inclui responsabilidade pelos menus.
	 *
	 */
	class VVAIndex extends VirtexViewAdmin {
		
		protected $_menu;
		
		public function __construct() {
			parent::__construct();
		}
		
		/**
		 * Carrega o template de menu utilizando as informações configuradas. (ex: privilégios)
		 */
		public function carregaMenu() {
		
		}
		
		public final function exibe() {
			// echo "EXIBINDO...";
			
			$this->_file = "index.html";
			
			parent::exibe();
		}

	}

