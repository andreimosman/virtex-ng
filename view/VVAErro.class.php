<?

	/** 
	 * Visualização de erros.
	 */
	class VVAErro extends VirtexViewAdmin {
	
		public function __construct() {
			parent::__construct();
		}
		
		public final function exibe() {
			switch($this->obtem("tipo")) {
				case 'acessonegado':
					$this->_file = "erro_acessonegado.html";
					break;
			}
			
			parent::exibe();
		}
	
	
	}
	
	
?>
