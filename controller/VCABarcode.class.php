<?

	class VCABarcode extends VirtexControllerAdmin {
	
		public function __construct() {
			parent::__construct();
		}

		protected function init() {
			parent::init();
		}
		
		
		protected function executa() {
			$codigo = @$_REQUEST["codigo"];
			
			if( !$codigo ) die("erro!");
			
			// echo "CODIGO: $codigo";
			
			
			MBanco::imageBarcode($codigo);
			
			
			
		
		}

	
	}

