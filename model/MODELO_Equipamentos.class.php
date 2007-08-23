<?


	class MODELO_Equipamentos extends VirtexModelo {
	
		protected $cftb_servidor;
		protected $cftb_pop;
		protected $cftb_nas;
		protected $cftb_rede;
		protected $cftb_nas_rede;
		protected $cftb_ip;
		
	
		public function __construct() {
			parent::__construct();
		
			$this->cftb_servidor 	= VirtexPersiste::factory("cftb_servidor");
			$this->cftb_pop 		= VirtexPersiste::factory("cftb_pop");
			$this->cftb_nas 		= VirtexPersiste::factory("cftb_nas");
			$this->cftb_rede		= VirtexPersiste::factory("cftb_rede");
			$this->cftb_nas_rede	= VirtexPersiste::factory("cftb_nas_rede");
			$this->cftb_ip			= VirtexPersiste::factory("cftb_ip");
			
		}
		
		public function obtemListaServidores($somenteDisponiveis=false) {
			$filtro = array();
			if( $somenteDisponiveis ) {
				$filtro["disponivel"] = "t";
			}
			return($this->cftb_servidor->obtem($filtro));
		}
		
		public function obtemServidor($id_servidor) {
			$filtro = array("id_servidor" => $id_servidor);
			return($this->cftb_servidor->obtemUnico($filtro));
		}
		
		// TODO
		public function cadastraServidor() {
		
		}
		
		// TODO
		public function atualizaServidor() {
		
		}
		
		
		
		
		
		public function obtemListaNAS() {
			$filtro = array();
			//if( $disponivel ) {
			//	$filtro["disponivel"] = $disponivel;
			//}
			
			return($this->cftb_nas->obtem($filtro));
			
		}
		
		public function obtemNAS($id_nas) {
			return($this->cftb_nas->obtemUnico(array("id_nas" => $id_nas)));
		}
		
		public function obtemTiposNAS() {
			return($this->cftb_nas->enumTipoNas());
		}
		
		public function obtemRedesNAS($id_nas) {
			return($this->cftb_rede->obtemPeloNAS($id_nas));
		}
		
		public function obtemIPsNAS($id_nas) {
			return($this->cftb_ip->obtemPeloNAS($id_nas));
		}
		
		
		
		protected function _obtemPops($parentId="",$nivel=0,$filtro=array()) {
			$filtro["id_pop_ap"] = ($parentId?$parentId:"null:");

			$retorno = array();

			$registros = $this->cftb_pop->obtem($filtro);
			for($i=0;$i<count($registros);$i++) {
				$registros[$i]["nivel"] = $nivel;
				$retorno[] = $registros[$i];
				$child = $this->_obtemPops($registros[$i]["id_pop"],$nivel+1);
				for($x=0;$x<count($child);$x++) {
					$retorno[] = $child[$x];
				}
			}
			
			return($retorno);

		}
	
		public function obtemListaPOPs($status="") {
			$filtro = array();
			if( $status ) {
				$filtro["status"] = $status;
			}
			return($this->_obtemPops("",0,$filtro));
		}
		
		public function obtemPop($id_pop) {
			return($this->cftb_pop->obtemUnico(array("id_pop" => $id_pop)));
		}
	
	}



?>
