<?


	class VirtexViewUsuario extends VirtexView {

		protected $nomeSessao;

		protected function __construct() {
			parent::__construct();
			$this->atribui("lista_boolean",$this->obtemListaBoolean());
			//print_r($this->obtemListaBoolean());
		}
		
		protected function selfConfig() {
			parent::selfConfig();
		}


		protected function init() {
			parent::init();
			$this->_tplPath .= "/usuario";
			$this->nomeSessao = "";
			// TODO: Atribuir as variáveis do login		

		}


		// TODO: Subir para o VirtexView
		protected function obtemListaBoolean() {
			$retorno 	= array();
			$retorno[] 	= array("id" => "t", "nome" => "Sim");
			$retorno[] 	= array("id" => "f", "nome" => "Não");
			return($retorno);
		}

		public static function &factory($view) {
			$view = strtolower($view);
			
			
			switch($view) {
				case 'login':
					$retorno = new VVULogin();
					break;
					
				case 'index':
					$retorno = new VVUIndex();
					break;
					
				default:
					throw new ExcecaoView(255,"View não disponível");
			}
			
			return($retorno);
		}

		public function exibe() {
			$this->atribui("nomeSessao",$this->nomeSessao);
			parent::exibe();
		}


	}	




?>
