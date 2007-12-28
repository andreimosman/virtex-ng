<?

	class VCAHome extends VirtexControllerAdmin {
		
		protected $cobranca;
	
		public function __construct() {
			parent::__construct();
		}
		
		public function init() {
			parent::init();
			$this->_view = VirtexViewAdmin::factory("home");
			$this->_view->atribui("licenca",$this->licenca->obtemLicenca());	
			$this->cobranca = VirtexModelo::factory('cobranca');
			
			$atrasados = $this->cobranca->obtemContratosFaturasAtrasadas();
			$this->_view->atribui("atrasados", $atrasados);
			
			$countBloqueados = count($atrasados);
			$this->_view->atribui("countBloqueados", $countBloqueados);
		}
		
		
	}
?>
