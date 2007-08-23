<?
	/**
	 * Controller p/ Interface Administrativa
	 */
	
	class VirtexControllerAdmin extends VirtexController {

		protected $preferencias;
	
		protected function __construct() {
			parent::__construct();
		}
		
		


		public static function &factory($controller) {
			
			$controller = strtolower($controller);
			
			switch($controller) {
				case 'index':
					$retorno = new VCAIndex();
					break;

				case 'clientes':
					$retorno = new VCAClientes();
					break;
					
				case 'configuracoes':
					$retorno = new VCAConfiguracoes();
					break;
				
				case 'administracao':
					$retorno = new VCAAdministracao();
					break;
					
				case 'suporte':
					$retorno = new VCASuporte();
					break;
					
				default:
					throw new ExcecaoController(255,"Controller não encontrado");
			
			}
			
			return($retorno);
		
		}
		
		protected function selfConfig() {
		
		}
	
		protected function init() {
			parent::init();
			$this->preferencias = VirtexModelo::factory("preferencias");
		}
		
		protected function executa() {
			// echo "EXEC<br>\n";
		}
		



	}



?>
