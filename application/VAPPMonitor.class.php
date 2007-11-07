<?

	class VAPPMonitor extends VirtexApplication {
	
		protected $useConnCache;	// Faz o cache das conexões
	
		public function __construct() {
			parent::__construct();
			$this->useConnCache	= false;
			$this->serverCache();
		}
		
		protected function selfConfig() {
		
		}
		
		/**
		 *
		 */
		protected function serverCache() {
			$equipamentos = VirtexModelo::factory('equipamentos');
			$servidores = $equipamentos->obtemListaServidores();

			$this->cacheServidores = array();
			
			foreach($servidores as $servidor) {
				if( $servidor["disponivel"] == 't' && $servidor["ip"] && $servidor["chave"] && $servidor["usuario"] && $servidor["senha"] ) {
					$this->cacheServidores[ $servidor["id_servidor"] ] = $servidor;
					
					if( $this->useConnCache ) {
						$this->cacheServidores[ $servidor["id_servidor"] ]["conn"] = new VirtexCommClient();
						$this->cacheServidores[ $servidor["id_servidor"] ]["conn"]->open($servidor["ip"],$servidor["porta"],$servidor["chave"],$servidor["usuario"],$servidor["senha"]);
					} else {
						$this->cacheServidores[ $servidor["id_servidor"] ]["conn"] = null;
					}
										
				}
			}		
		}
		
		protected function &getCachedServerConnection($id_servidor) {
			if( $this->cacheServidores[$id_servidor]["conn"] == null) {
				$servidor = $this->cacheServidores[$id_servidor];
				$this->cacheServidores[ $id_servidor ]["conn"] = new VirtexCommClient();
				$this->cacheServidores[ $id_servidor ]["conn"]->open($servidor["ip"],$servidor["porta"],$servidor["chave"],$servidor["usuario"],$servidor["senha"]);
			}
			
			return( $this->cacheServidores[ $id_servidor ]["conn"] );
		}
		
		protected function closeCachedServerConnection($id_servidor) {
			if( !$this->useConnCache  && $this->cacheServidores[$id_servidor]["conn"] != null) {
				$this->cacheServidores[$id_servidor]["conn"]->close();
				$this->cacheServidores[$id_servidor]["conn"] = null;
			}
		}
		
		protected function flushCachedServerConnections() {
			foreach($this->cacheServidores as $k => $v) {
				if( $this->cacheServidores[$k]["conn"] ) {
					$this->cacheServidores[$k]["conn"]->close();
				} 
			}
			
			$this->cacheServidores = array();
		}
		

		public function executa() {
			$preferencias = VirtexModelo::factory('preferencias');
			$equipamentos = VirtexModelo::factory('equipamentos');

			$prefMon = $preferencias->obtemMonitoramento();
			$tamanho = "32";
			
			$numPings = @$prefMon["num_pings"] ? $prefMon["num_pings"] : 5;
			

			$pops = $equipamentos->obtemListaPOPs();
			
			foreach($pops as $pop) {
				if( $pop["ativar_monitoramento"] == 't' && $pop["ipaddr"] ) {
				
					$conn = $this->getCachedServerConnection($pop["id_servidor"]);
					$resposta = $conn->getFPING($pop["ipaddr"],$numPings,$tamanho);
					$this->closeCachedServerConnection($pop["id_servidor"]);
					unset($conn);

					$perdas = 0;
					$minimo = 9999999999999999999;
					$maximo = 0;
					$soma 	= 0;
					
					$num_erros = 0;
					
					for($i=0;$i<count($resposta);$i++) {
						if(trim($resposta[$i]) === "-") {
							$perdas++;
						} else {
							$resposta[$i]*=1000;
							$soma+=$resposta[$i];
							if( $resposta[$i] < $minimo ) {
								$minimo = $resposta[$i];
							}
							if( $resposta[$i] > $maximo ) {
								$maximo = $resposta[$i];
							}
						}
					}
					
					$status='OK';
					$media = (int)(count($resposta)?$soma/(count($resposta)-$perdas):0);
					
					if( $perdas == count($resposta) ) {
						$status = !count($resposta) ? "IER" : "ERR";
						$minimo = 0;
						$maximo = 0;
						$media = 0;
					}
					
					if($perdas > 0 && $perdas < count($resposta)) $status = 'WRN';
					
					if( $status != 'OK' ) {
						$num_erros=1;
					}
					
					$equipamentos->excluiMonitoramentoPop($pop["id_pop"]);
					$equipamentos->registraMonitoramentoPop($pop["id_pop"],$minimo,$maximo,$media,$perdas,$num_erros,count($resposta),$status);

				}
			}
			
			$this->flushCachedServerConnections();
			
		
		}
	
	}







?>
