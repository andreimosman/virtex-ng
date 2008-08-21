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
			
			//$atrasados = $this->cobranca->obtemContratosFaturasAtrasadasBloqueios();
			//$this->_view->atribui("atrasados", $atrasados);
			
			$countBloqueados = $this->cobranca->countContratosFaturasAtrasadasBloqueios();
			$this->_view->atribui("countBloqueados", $countBloqueados);
			
			
			
			$exibir_renovacao = $this->requirePrivLeitura("_FINANCEIRO_COBRANCA_RENOVACAO",false);
			$this->_view->atribui("exibir_renovacao",$exibir_renovacao);
			
			if( $exibir_renovacao ) {
				//$renovacao = $this->cobranca->obtemContratosParaRenovacao();
				//$this->_view->atribui("renovacao",$renovacao);

				$countRenovacao = $this->cobranca->obtemContratosParaRenovacao(true);
				$this->_view->atribui("countRenovacao", $countRenovacao);

			}

			//$carnes = $this->cobranca->obtemCarnesSemConfirmacao();
			$countCarnes = $this->cobranca->obtemCarnesSemConfirmacao(0,true);
			// $this->_view->atribui("carnes",$carnes);
			$this->_view->atribui("countCarnes",$countCarnes);
		
		}
		
		
	}
?>
