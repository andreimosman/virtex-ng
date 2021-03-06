<?


	class MODELO_Equipamentos extends VirtexModelo {
	
		protected $cftb_servidor;
		protected $cftb_pop;
		protected $cftb_nas;
		protected $cftb_rede;
		protected $cftb_nas_rede;
		protected $cftb_ip;
		
		protected $sttb_pop_status;
		
		protected $spool;
		
		// Performance stuff
		protected $cacheArvore;
		protected $cacheArvoreMAC;
		
		protected $radius;
		
	
		public function __construct() {
			parent::__construct();
			
			$this->cftb_servidor 	= VirtexPersiste::factory("cftb_servidor");
			$this->cftb_pop 		= VirtexPersiste::factory("cftb_pop");
			$this->cftb_nas 		= VirtexPersiste::factory("cftb_nas");
			$this->cftb_rede		= VirtexPersiste::factory("cftb_rede");
			$this->cftb_nas_rede	= VirtexPersiste::factory("cftb_nas_rede");
			$this->cftb_ip			= VirtexPersiste::factory("cftb_ip");
			
			$this->sttb_pop_status 	= VirtexPersiste::factory("sttb_pop_status");
			
			$this->radius = VirtexModelo::factory("radius");
			
			$this->spool = null;
			
			$this->cacheArvore = array();
			$this->cacheArvoreMAC = array();
			
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
		public function cadastraServidor($hostname, $ip, $porta, $chave, $usuario, $senha, $disponivel) {
			$dados = array("hostname" => $hostname, "ip" => $ip, "porta" => $porta, "chave" => $chave, "usuario" => $usuario, "senha" => $senha, "disponivel" => $disponivel);
			return($this->cftb_servidor->insere($dados));
		}
		
		// TODO
		public function atualizaServidor($id_servidor, $hostname, $ip, $porta, $chave, $usuario, $senha, $disponivel) {
			$filtro = array("id_servidor"=>$id_servidor);
			$dados = array("hostname" => $hostname, "ip" => $ip, "porta" => $porta, "chave" => $chave, "usuario" => $usuario, "disponivel" => $disponivel);
			
			if( $senha ) {
				$dados["senha"] = $senha;
			}
			
			return($this->cftb_servidor->altera($dados, $filtro));
		}

		
		public function obtemListaNAS($disponivel=null,$retornarRas=true) {
			$filtro = array();
			if( $disponivel ) {
				$filtro["disponivel"] = $disponivel;
			}
			
			if( !$retornarRas ) {
				$filtro["tipo_nas"] = "!=:R";
			}
			
			return($this->cftb_nas->obtem($filtro));
			
		}
		
		public function atualizaNAS($id_nas, $nome, $ip, $secret, $id_servidor, $padrao, $enviar_rates_radius) {
			$filtro = array("id_nas"=>$id_nas);
			$dados = array("nome"=>$nome, "ip"=>$ip, "secret"=>$secret, "padrao" => $padrao, "enviar_rates_radius" => $enviar_rates_radius);
			$dados["id_servidor"] = $id_servidor ? $id_servidor : null; 			
			return($this->cftb_nas->altera($dados,$filtro));
		}
		
		public function cadastraNAS($nome, $ip, $secret, $tipo_nas, $id_servidor, $padrao, $enviar_rates_radius) {
			$dados = array("nome"=>$nome, "ip"=>$ip, "secret"=>$secret, "tipo_nas"=>$tipo_nas, "padrao" => $padrao, "enviar_rates_radius" => $enviar_rates_radius);
			$dados["id_servidor"] = $id_servidor ? $id_servidor : null; 			
			return($this->cftb_nas->insere($dados));			
		}
		
		public function obtemNAS($id_nas) {
			return($this->cftb_nas->obtemUnico(array("id_nas" => $id_nas)));
		}
		
		public function obtemTiposNAS() {
			return($this->cftb_nas->enumTipoNas());
		}
		
		public function obtemPadraoPPPoE(){
			return($this->cftb_nas->enumPadroes());
		}
		
		public function obtemRedesNAS($id_nas) {
			return($this->cftb_rede->obtemPeloNAS($id_nas));
		}
		
		public function obtemIPsNAS($id_nas) {
			return($this->cftb_ip->obtemPeloNAS($id_nas));
		}
		
		/**
		 * Retorna um endere?o dispon?vel para o NAS especificado.
		 */
		public function obtemEnderecoDisponivelNAS($id_nas) {
			$info = $this->obtemNAS($id_nas);

			$endereco = null;
			
			if( $info["tipo_nas"] == "P" ) {
				// PPPoE, pegar em cftb_ip
				$endereco = $this->cftb_ip->obtemEnderecoDisponivel($id_nas);
			} else if($info["tipo_nas"] == "I") {
				// TCP/IP, pegar em cftb_rede.
				$endereco = $this->cftb_rede->obtemEnderecoDisponivel($id_nas);
			}
			
			return($endereco);
		
		}
		
		/**
		 * Caso o endere?o perten?a ao NAS solicitado retorna o endere?o correto.
		 */
		public function enderecoPertenceAoNAS($id_nas,$endereco) {
			return($this->cftb_nas_rede->enderecoPertenceAoNAS($id_nas,$endereco));
		}
		
		/**
		 * Retorna os endere?os de infraentrutura do NAS especificado.
		 */
		public function obtemEnderecoInfraestruturaNAS($id_nas) {
			return($this->cftb_rede->obtemPeloNAS($id_nas,"I"));
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
		 * Utilizado para quest?es de performance
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
			$c=0; // N?mero de IPs gerados.
			
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
				
				if( $registros[$i]["tipo"] == "CL" ) {
					$registros[$i]["psk"] = $registros[$i]["mac"]?$this->radius->obtemPSK($registros[$i]["mac"]):"";
				}
				
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
				
		public function obtemPOPsPeloTipo($tipo,$status="") {
			$filtro = array("tipo" => $tipo );
			if( $status ) {
				$filtro["status"] = $status;
			}
			return($this->cftb_pop->obtem($filtro));
		}
	
		public function obtemListaPOPs($status="",$parentId="") {
			$filtro = array();
			if( $status ) {
				$filtro["status"] = $status;
			}
			
			return($this->_obtemPops($parentId,0,$filtro));
		}
		
		public function obtemListaPOPOrdemAlfabetica($somente_disponiveis=true) {
			$filtro = array();
			
			if( $somente_disponiveis ) {
				$filtro["status"] = "A";
			}
			
			$ordem="nome";
		
			$registros = $this->cftb_pop->obtem($filtro,$ordem);
			return $registros;
		}		
		
		public function obtemPop($id_pop) {
			return($this->cftb_pop->obtemUnico(array("id_pop" => $id_pop)));
		}
		
		public function obtemStatusPop(){
			return($this->cftb_pop->enumStatusPop());
		}
		
		public function obtemTipoPop(){
			return($this->cftb_pop->enumTipoPop());
		}
		
		public function atualizaPop($id_pop, $nome, $info, $tipo, $id_pop_ap, $status, $ipaddr, $id_servidor, $ativar_monitoramento, $mac, $clientemacpop, $ativar_snmp='f', $snmp_ro_com='', $snmp_rw_com='', $snmp_versao='') {
			$filtro = array("id_pop"=>$id_pop);
			
			if( !$mac ) $mac = NULL;
			
			$dados = array("nome"=>strtoupper($nome), "info"=>$info, "status" => $status, "mac" => $mac);
			if( $tipo ) {
				$dados["tipo"] = $tipo;
			}
			$dados["id_pop_ap"] = $id_pop_ap ? $id_pop_ap : null; 			
			$dados["ipaddr"] = $ipaddr ? $ipaddr : null; 			
			$dados["id_servidor"] = $id_servidor ? $id_servidor : null; 			
			$dados["ativar_monitoramento"] = $ativar_monitoramento ? $ativar_monitoramento : null; 	
			$dados["clientemacpop"] = $clientemacpop ? $clientemacpop : 'f';
			$dados["ativar_snmp"] = $ativar_snmp ? $ativar_snmp : null; 	
			$dados["snmp_ro_com"] = $snmp_ro_com;
			$dados["snmp_rw_com"] = $snmp_rw_com;
			$dados["snmp_versao"] = $snmp_versao;
			return($this->cftb_pop->altera($dados,$filtro));
		}
		
		public function cadastraPop($id_pop, $nome, $info, $tipo, $id_pop_ap, $status, $ipaddr, $id_servidor, $ativar_monitoramento, $mac, $clientemacpop, $ativar_snmp='f', $snmp_ro_com='', $snmp_rw_com='', $snmp_versao='') {
			$dados = array("nome"=>strtoupper($nome), "info"=>$info, "tipo" => $tipo, "id_pop_ap" => $id_pop_ap, "status" => $status);
			$dados["mac"] = $mac ? $mac : null; 
			$dados["id_pop_ap"] = $id_pop_ap ? $id_pop_ap : null; 			
			$dados["ipaddr"] = $ipaddr ? $ipaddr : null;
			$dados["id_servidor"] = $id_servidor ? $id_servidor : null; 						
			$dados["ativar_monitoramento"] = $ativar_monitoramento ? $ativar_monitoramento : 'f';
			$dados["clientemacpop"] = $clientemacpop ? $clientemacpop : 'f';
			$dados["ativar_snmp"] = $ativar_snmp ? $ativar_snmp : null; 	
			$dados["snmp_ro_com"] = $snmp_ro_com;
			$dados["snmp_rw_com"] = $snmp_rw_com;
			$dados["snmp_versao"] = $snmp_versao;
			return($this->cftb_pop->insere($dados));			
		}
		
		public function obtemMonitoramentoPop($id_pop) {
			return($this->sttb_pop_status->obtemUnico(array("id_pop" => $id_pop)));
		}
		
		public function excluiMonitoramentoPop($id_pop) {
			return($this->sttb_pop_status->exclui(array("id_pop" => $id_pop)));
		}
		
		public function registraMonitoramentoPop($id_pop,$minimo,$maximo,$media,$num_perdas,$num_erros,$num_ping,$status) {
			$dados = array( "id_pop" => $id_pop, "min_ping" => $minimo, "max_ping" => $maximo, "media_ping" => $media,
							"num_perdas" => $num_perdas, "num_erros" => $num_erros, "num_ping" => $num_ping, "status" => $status);
			return($this->sttb_pop_status->insere($dados));
		}
		
		
		/**
		 * Obtem a ?rvore de pops at? a raiz
		 */
		public function obtemArvorePop($id_pop) {
			
			if( @$this->cacheArvore[$id_pop] ) {
				return($this->cacheArvore[$id_pop]);
			}
		
			
			$pop = $this->obtemPop($id_pop);
			
			$retorno = array($pop);
			
			while( $pop["id_pop_ap"] != "" && $pop["id_pop_ap"] != null ) {
				$pop = $this->obtemPop($pop["id_pop_ap"]);
				$retorno[] = $pop;
			}
			
			$this->cacheArvore[$id_pop] = $retorno;
			
			return($retorno);

		}
		
		/**
		 * obtem o MAC do POP (se aplic?vel)
		 */
		public function macPOP($id_pop) {
			if( @$this->cacheArvoreMAC[$id_pop] ) {
				return($this->cacheArvoreMAC[$id_pop]);
			}
		
			$arvore = $this->obtemArvorePop($id_pop);
			
			$retorno = "";
			
			for($i=0;$i<count($arvore);$i++) {
				if( $arvore[$i]["clientemacpop"] == 't' && $arvore[$i]["mac"] ) {
					$retorno = $arvore[$i]["mac"];
				}
			}
			
			$this->cacheArvoreMAC[$id_pop] = $retorno;
			
			return($retorno);
		}
		
		public function obtemPopCLPeloMAC($mac) {
			return($this->cftb_pop->obtemUnico(array("tipo"=>"CL", "usa_mac_ap" => 't', "mac" => "=:".$mac)));
		}
		
		public function obtemPOPsPeloServidor($id_servidor) {
			return($this->cftb_pop->obtem(array("id_servidor" => $id_servidor, "status" => "A")));
		}
		
		public function obtemIPsPOPs() {
			$filtro = array('ipaddr' => "!null:");
			
			return($this->cftb_pop->obtem($filtro,"ipaddr"));
			
		}
	
	}

