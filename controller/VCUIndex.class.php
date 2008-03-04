<?


	class VCUIndex extends VirtexControllerUsuario {
		
		public function __construct() {
			parent::__construct();
			
			
		}
		
		public function init() {
			parent::init();
			
			
			
		}
		
		public function executa() {
			parent::executa();

			$this->_view = VirtexViewUsuario::factory("index");
			
			switch(@$_REQUEST["op"]) {
			
				case 'menu':
					$this->executaMenu();
					break;

				case 'home':
					$this->executaHome();
					break;
					
				case 'dados_cadastrais':
					$this->executaDadosCadastrais();
					break;
				
				case 'contratos':
					$this->executaContratos();
					break;
				
				case 'contrato_detalhe':
					$this->executaContratoDetalhe();
					break;
			
			
			}
			
			
		
		
		}
		
		protected function executaMenu() {
			$this->_view->atribuiVisualizacao("menu");			
		}
		
		protected function executaHome() {
			$this->_view->atribuiVisualizacao("home");
					
		}
		
		protected function executaDadosCadastrais() {
			$this->_view->atribuiVisualizacao("dados_cadastrais");
			
			$dadosLogin = $this->_login->obtem("dados");
			
			$preferencias = VirtexModelo::factory("preferencias");
			
			$this->_view->atribui("cliente",$dadosLogin["cliente"]);
			
			$cidade_uf = $preferencias->obtemCidadePeloID($dadosLogin["cliente"]["id_cidade"]);
			
			$this->_view->atribui("cidade_uf",$cidade_uf);

			
			
			/*echo "<pre>";
			print_r($cidade);
			echo "</pre>";*/
					
		}
		
		protected function executaContratos() {
			$this->_view->atribuiVisualizacao("contratos");

			$dadosLogin = $this->_login->obtem("dados");

			$cobranca = VirtexModelo::factory("cobranca");

			$contratos = $cobranca->obtemContratos($dadosLogin["cliente"]["id_cliente"],"A");

			$this->_view->atribui("contratos",$contratos);

			/*echo "<pre>";
			print_r($contratos);
			echo "</pre>";*/
		
		
		}
		
		protected function executaContratoDetalhe() {
			
			$this->_view->atribuiVisualizacao("contrato_detalhe");
			
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			
			
			//// echo $id_cliente_produto;
			
			$cobranca = VirtexModelo::factory("cobranca");
			$contrato_detalhado = $cobranca->obtemContratoPeloId($id_cliente_produto);
			
			

			$this->_view->atribui("contrato_detalhado",$contrato_detalhado);

			echo "<pre>";
			print_r($contrato_detalhado);
			echo "</pre>";

		
		}
		
		
		
	
	}

?>
