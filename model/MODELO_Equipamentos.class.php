<?


	class MODELO_Equipamentos extends VirtexModelo {
	
		protected $cftb_servidor;
		protected $cftb_pop;
		protected $cftb_nas;
		protected $cftb_rede;
		protected $cftb_nas_rede;
		protected $cftb_ip;
		
		protected $spool;
		
	
		public function __construct() {
			parent::__construct();
		
			$this->cftb_servidor 	= VirtexPersiste::factory("cftb_servidor");
			$this->cftb_pop 		= VirtexPersiste::factory("cftb_pop");
			$this->cftb_nas 		= VirtexPersiste::factory("cftb_nas");
			$this->cftb_rede		= VirtexPersiste::factory("cftb_rede");
			$this->cftb_nas_rede	= VirtexPersiste::factory("cftb_nas_rede");
			$this->cftb_ip			= VirtexPersiste::factory("cftb_ip");
			
			$this->spool = null;
			
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
		public function cadastraServidor($hostname, $ip, $porta, $usuario, $senha, $disponivel) {
			$dados = array("hostname" => $hostname, "ip" => $ip, "porta" => $porta, "usuario" => $usuario, "senha" => $senha, "disponivel" => $disponivel);
			return($this->cftb_servidor->insere($dados));
		}
		
		// TODO
		public function atualizaServidor($id_servidor, $hostname, $ip, $porta, $usuario, $senha, $disponivel) {
			$filtro = array("id_servidor"=>$id_servidor);
			$dados = array("hostname" => $hostname, "ip" => $ip, "porta" => $porta, "usuario" => $usuario, "senha" => $senha, "disponivel" => $disponivel);
			return($this->cftb_servidor->altera($dados, $filtro));
		}

		
		public function obtemListaNAS() {
			$filtro = array();
			//if( $disponivel ) {
			//	$filtro["disponivel"] = $disponivel;
			//}
			
			return($this->cftb_nas->obtem($filtro));
			
		}
		
		public function atualizaNAS($id_nas, $nome, $ip, $secret, $id_servidor) {
			$filtro = array("id_nas"=>$id_nas);
			$dados = array("nome"=>$nome, "ip"=>$ip, "secret"=>$secret, "id_servidor"=>$id_servidor);
			return($this->cftb_nas->altera($dados,$filtro));
		}
		
		public function cadastraNAS($nome, $ip, $secret, $tipo_nas, $id_servidor) {
			$dados = array("nome"=>$nome, "ip"=>$ip, "secret"=>$secret, "tipo_nas"=>$tipo_nas, "id_servidor"=>$id_servidor);
			return($this->cftb_nas->insere($dados));			
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
		
		/**
		 * Cadastra uma rede IP em um NAS
		 */
		public function cadastraRedeIPNAS($id_nas,$rede,$tipo_rede,$pppoe=false) {
			//
			// Inserir rede
			//
			$id_rede = $this->cftb_rede->insere(array("rede"=>$rede,"tipo_rede"=>$tipo_rede));
			$this->cftb_nas_rede->insere(array("rede"=>$rede,"id_nas"=>$id_nas));
			
			//
			// Enviar chamada p/ spool (somente para classes de infra-estrutura e cadastros !pppoe)
			//
			if( !$pppoe && $tipo_rede == "I" ) {
				// SPOOL
				$spool = $this->obtemSpool();
				$spool->adicionaRedeInfraestrutura($id_nas,$id_rede,$rede);
			}
			
			
			return($id_rede);
		}
		
		/**
		 * Utilizado para questões de performance
		 */
		protected function obtemSpool() {
			if( $this->spool == null ) {
				$this->spool = VirtexModelo::factory("spool");
			}
			return($this->spool);
		}
		
		
		public function cadastraRedePPPoENAS($id_nas,$rede,$tipo_rede) {
			$ip = new MInet($rede);
			$id_rede = $this->cadastraRedeIPNAS($id_nas,$rede,$tipo_rede,true);

			$nw = $ip->obtemRede();
			$bc = $ip->obtemBroadcast();
			$c=0; // Número de IPs gerados.
			
			@list($inicio,$bits) = explode("/",$rede);
			
			for( $ip = new MInet($inicio."/32",$rede); $ip->obtemRede() != ""; $ip = $ip->proximaRede()) {
				$ipaddr = $ip->obtemRede();

				if( $ipaddr != $nw && $ipaddr != $bc ) {
					$c++; 

					//
					// INSERIR O IP NO NAS.
					//
					$this->cftb_ip->insere(array("ipaddr" => $ipaddr));


				}

			}
			
			return($c);
			
		
		
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
		
		public function obtemRedesAssociadas($rede) {
			return($this->cftb_rede->obtemAssociacoes($rede));
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
