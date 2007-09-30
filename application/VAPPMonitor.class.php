<?

	class VAPPMonitor extends VirtexApplication {
	
		public function __construct() {
			parent::__construct();
		}
		
		protected function selfConfig() {
		
		}

		public function executa() {
			$preferencias = VirtexModelo::factory('preferencias');
			$equipamentos = VirtexModelo::factory('equipamentos');

			$prefMon = $preferencias->obtemMonitoramento();
			$tamanho = "32";
			
			$numPings = @$prefMon["num_pings"] ? $prefMon["num_pings"] : 5;
			
			$servidores = $equipamentos->obtemListaServidores();
			
			$cacheServidores = array();
			
			foreach($servidores as $servidor) {
				if( $servidor["disponivel"] == 't' && $servidor["ip"] && $servidor["chave"] && $servidor["usuario"] && $servidor["senha"] ) {
					$cacheServidores[ $servidor["id_servidor"] ] = $servidor;
					
					$cacheServidores[ $servidor["id_servidor"] ]["conn"] = new VirtexCommClient();
					$cacheServidores[ $servidor["id_servidor"] ]["conn"]->open($servidor["ip"],$servidor["porta"],$servidor["chave"],$servidor["usuario"],$servidor["senha"]);
										
				}
			}

			$pops = $equipamentos->obtemListaPOPs();
			
			foreach($pops as $pop) {
				if( $pop["ativar_monitoramento"] == 't' && $pop["ipaddr"] && count( @$cacheServidores[ $pop["id_servidor"] ] ) ) {
					// echo "POP: " . $pop["id_pop"] . " / SRV: " . $pop["id_servidor"] . "\n";
					
					echo "PINGANDO " . $pop["ipaddr"] . "\n";
					
					$resposta = $cacheServidores[ $pop["id_servidor"] ]["conn"]->getFPING($pop["ipaddr"],$numPings,$tamanho);
					
					print_r($resposta);
					echo "--------------------------------\n";

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
					$media = (int)(count($resposta)?$soma/count($resposta):0);
					
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
					$equipamentos->registraMonitoramentoPop($pop["id_pop"],$minimo,$maximo,$media,$perdas,$num_erros,count($retorno),$status);

				}
			}
			
		
		}
	
	}







?>
