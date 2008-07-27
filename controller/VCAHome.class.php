<?

	class VCAHome extends VirtexControllerAdmin {
		
		protected $cobranca;
	
		public function __construct() {
			parent::__construct();
		}
		
		public function init() {
			parent::init();
			$this->_view = VirtexViewAdmin::factory("home");
		}
		
		public function executa() {

			$this->_view->atribui("licenca",$this->licenca->obtemLicenca());	
			$this->cobranca = VirtexModelo::factory('cobranca');
			
			$atrasados = $this->cobranca->obtemContratosFaturasAtrasadasBloqueios();
			$this->_view->atribui("atrasados", $atrasados);
			
			$countBloqueados = count($atrasados);
			$this->_view->atribui("countBloqueados", $countBloqueados);
			
			
			
			$exibir_renovacao = $this->requirePrivLeitura("_FINANCEIRO_COBRANCA_RENOVACAO",false);
			$this->_view->atribui("exibir_renovacao",$exibir_renovacao);
			
			if( $exibir_renovacao ) {
				$renovacao = $this->cobranca->obtemContratosParaRenovacao();
				$this->_view->atribui("renovacao",$renovacao);
			}
			
			
			
			$countRenovacao = count($renovacao);
			$this->_view->atribui("countRenovacao", $countRenovacao);

		
		}
		
		
	}
?>
