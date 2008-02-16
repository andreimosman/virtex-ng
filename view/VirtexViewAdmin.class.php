<?
	/**
	 * Classe base para as views da interface administrativa
	 */
	
	class VirtexViewAdmin extends VirtexView {

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
			$this->_tplPath .= "/admin";
			$this->nomeSessao = "";
			// TODO: Atribuir as variáveis do login	
			$this->exibirNomeArquivo(true);

		}

		public function configureMenu($itens=array(),$enabled=true,$visible=true) {
			$menu = array("itens" => $itens,"enabled" => $enabled, "visible" => $visible);			
			$this->atribui("sessionMenu", $menu);
		}
		
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
					$retorno = new VVALogin();
					break;
					
				case 'index':
					$retorno = new VVAIndex();
					break;
					
				case 'clientes':
					$retorno = new VVAClientes();
					break;
					
				case 'configuracoes':
					$retorno = new VVAConfiguracoes();
					break;
				
				case 'administracao':
					$retorno = new VVAAdministracao();
					break;
					
				case 'suporte':
					$retorno = new VVASuporte();
					break;
				
				case 'home':
					$retorno = new VVAHome();
					break;
				
				case 'cobranca':
					$retorno = new VVACobranca();
					break;
				
				case 'erro':
					$retorno = new VVAErro();
					break;
					
				case 'faturamento':
					$retorno = new VVAFaturamento();
					break;
					
				case 'cadastro':
					$retorno = new VVACadastro();
					break;					

				case 'financeiro':
					$retorno = new VVAFinanceiro();
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
