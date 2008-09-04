<?


	/**
	 * Classe para gerenciar configurações DHCP.
	 */
	class VAPPDHCP extends VirtexApplication {
		
		protected static $_DHCP_SERVICE = "/usr/local/etc/rc.d/isc-dhcpd";
		
		
		protected function selfConfig() {
			$this->_startdb 	= true;
			$this->_shortopts 	= "C"; // -C exibe o arquivo de configurações.
		}
		
		protected function DHCPdStart() {
			SOFreeBSD::executa(self::$_DHCP_SERVICE . " start");
		}
		
		protected function DHCPdStop() {
			SOFreeBSD::executa(self::$_DHCP_SERVICE . " stop");		
		}
		
		protected function DHCPdRestart() {
			$this->DHCPdStop();
			$this->DHCPdStart();
		}
		
		protected function geraDHCPDConfig($dhcpClasses) {
			$preferencias = VirtexModelo::factory("preferencias");
			$prefGeral = $preferencias->obtemPreferenciasGerais();
			
			$dominioPadrao = $prefGeral["dominio_padrao"];
			
			$arquivoDHCP = "etc/dhcpd.conf";
			// $arquivoDHCP = "php://stdout";
			
			$fd = fopen($arquivoDHCP,"w");
			
			if( !$fd ) {
				return false;	// DEU ERRO
			}
			
			fputs($fd,"####################################\n");
			fputs($fd,"# VIRTEX ADMIN DHCP CONFIG FILE    #\n");
			fputs($fd,"#                                  #\n");
			fputs($fd,"# Info: consultoria@mosman.com.br  #\n");
			fputs($fd,"####################################\n\n");
			fputs($fd,"default-lease-time 600;\n");
			fputs($fd,"max-lease-time 7200;\n");
			fputs($fd,"ddns-update-style ad-hoc;\n");
			//fputs($fd,"ddns-updates on;\n");
			fputs($fd,'option domain-name "' . $dominioPadrao . '"' . ";\n");
			
			//$dns1 = $prefGeral["hosp_ns1"];
			//$dns2 = $prefGeral["hosp_ns2"];
			//if( !$dns2 ) $dns2 = $dns1;
			
			$dns = $this->ext_ip;
			
			// TODO: Pegar o ip da interface externa no network.ini
			fputs($fd,"option domain-name-servers $dns;\n");
			
			fputs($fd,"\n");
			
			
			$hosts = array();
			
			$delayedConf = "";
			
			foreach($dhcpClasses as $interface => $redes) {
				if( count($redes) ) {
					$delayedConf .= "shared-network $interface {\n";
					
					for($i=0;$i<count($redes);$i++) {
						$rede = new MInet($redes[$i]["rede"]);

						$delayedConf .= "\tsubnet " . $rede->obtemRede() . " netmask " . $rede->obtemMascara() . " {\n";
						$delayedConf .= "\t\toption routers " . $rede->obtemPrimeiroIP() . ";\n";
						$delayedConf .= "\t\tignore unknown-clients;\n";
						$delayedConf .= "\n";
												
						$host = "host " . $redes[$i]["username"] . " {\n";
						$host .= "\thardware ethernet " . $redes[$i]["mac"] . ";\n";
						$host .= "\tfixed-address " . $rede->obtemUltimoIP() . ";\n";
						$host .= "}\n";
						
						$hosts[] = $host;
						
						$delayedConf .= "\t}\n";
					}
					$delayedConf .= "}\n";
				}
			}
			
			for($i=0;$i<count($hosts);$i++) {
				fputs($fd,$hosts[$i]);
			}
			
			fputs($fd,$delayedConf);
			
			fclose($fd);
		
		}
		
		
		
		public function executa() {
			//print_r($this);
			//exit;
		
			$contas = VirtexModelo::factory("contas");
			
			// Conta quantas vezes um dado MAC apareceu na lista.
			$macs = array();
			
			// Gera as classes de DHCP
			$dhcpTemp = array();
			
			
			foreach($this->infoNAS as $id_nas => $dados_nas) {
			
				// SOMENTE VÁLIDO PARA INTERFACES TCP/IP.
				if( $dados_nas["tipo_nas"] == "I" ) {
					
					$interface = $this->tcpip[$id_nas];
					
					$listaContas = $contas->obtemContasBandaLarga($id_nas,"A");	
					
					// print_r($listaContas);
					
					
					
					
					for($i=0;$i<count($listaContas);$i++) {
						if( @$listaContas[$i]["mac"] ) {
							if( !isset($macs[$listaContas[$i]["mac"]]) ) {
								$macs[$listaContas[$i]["mac"]] = 1;
							} else {
								$macs[$listaContas[$i]["mac"]]++;
							}
							
							if( !isset($dhcpTemp[$interface]) ) {
								$dhcpTemp[$interface] = array();
							}
							
							

							$dhcpTemp[$interface][] = array("rede" => $listaContas[$i]["rede"], "mac" => $listaContas[$i]["mac"], "username" => $listaContas[$i]["username"]);
						}
						
						
					}
					
					unset($listaContas);
					// print_r($macs);
					
				}
				
			}
			
			$dhcpClasses = array();
			
			foreach($dhcpTemp as $interface => $redes) {
				$dhcpClasses[$interface] = array();
				
				for($i=0;$i<count($redes);$i++) {
					if( $macs[$redes[$i]["mac"]] == 1 ) {
						$dhcpClasses[$interface][] = $redes[$i];
						// echo "USER: " . $redes[$i]["username"] . "\n";
					}
				}
				
			}
			
			unset($macs);
			unset($dhcpTemp);
			
			$this->geraDHCPDConfig($dhcpClasses);
			$this->DHCPdRestart();
			
		}
	
	}


?>
