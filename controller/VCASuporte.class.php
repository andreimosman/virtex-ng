<?
	class VCASuporte extends VirtexControllerAdmin {
	
	
		public function __construct() {
			parent::__construct();
		}
		
		public function init() {
			parent::init();
			$this->_view = VirtexViewAdmin::factory("suporte");
		}
		
		protected function executa() {
			switch($this->_op) {
				case 'ferramentas':
					$this->executaFerramentas();
					break;
				default:
					// do something
			}
		}
		
		protected function executaFerramentas() {
			$ferramenta = @$_REQUEST["ferramenta"];
			
			$this->_view->atribuiVisualizacao("ferramentas");
			$this->_view->atribui("ferramenta",$ferramenta);
			
			$ip = @$_REQUEST["ip"];
			$mascara = @$_REQUEST["mascara"];
			
			$this->_view->atribui("ip",$ip);
			$this->_view->atribui("mascara",$mascara);
			
			if( $ip && $mascara ) {
				
				
				try {
					$info = MInet::calculadora($ip,$mascara);
					$this->_view->atribui("info",$info->toArray());
				} catch(MException $e) {
					$this->_view->atribui("erro",$e->getMessage());
				}
				
				//echo "<pre>";
				//print_r($info);
				//echo "</pre>";
			
			}


		}
	
	
	
	
	
	
	
	}



?>
