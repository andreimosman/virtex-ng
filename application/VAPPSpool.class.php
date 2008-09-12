<?

	/**
	 * Classe de execução da spool.
	 */
	class VAPPSpool extends VirtexApplication {
		
		protected $atuador;
		
		public function __construct() {
			parent::__construct();
			
			$this->atuador = new Atuador();
			
		}
		
		protected function selfConfig() {
			$this->_shortopts 	= "b";
			$this->_longopts 	= array("boot");
			
		}
		
		public function executa() {
			// print_r($this->options);
			if( @is_array($this->options[0]) && (in_array("b",@$this->options[0]) || in_array("--boot",@$this->options[0])) ) {
				// Rotina de Boot

				$this->bootNetwork();
				$this->bootInfraestrutura();
				$this->bootClientes();
				//$this->bootPPPoE();
				//$this->bootPPTP();
				
			} else {
				// Processamento de spool
				$this->processaSpool();
				
			}
			
		}
		
		/**
		 * Ergue os parametros de rede (network.ini)
		 */
		protected function bootNetwork() {
			echo "Configurando Rede Inicial... ";
			
			// Configuração de Rede
			foreach($this->network as $interface => $dados) {
				if( @$dados["status"] == "up" ) {
					$ip = @$dados["ipaddr"];
					$mascara = $ip && @$dados["netmask"] ? @$dados["netmask"] : "";
					$gateway="";

					if( @$dados["type"] == "external" && $ip && $mascara && @$dados["gateway"] ) {
						// Adicionar rota padrão.						
						$gateway = $dados["gateway"];
					}
					
					$this->atuador->configuraRede($ip,$mascara,$gateway);
				}
			}
			
			echo "OK\n";
		}
		
		/**
		 * Ergue os endereços de infra-estrutura.
		 */
		protected function bootInfraestrutura() {
			echo "Conigurando Rede de Infra-Estrutura... ";

			// Modelo de Equipamentos (p/ pegar as redes p/ configurar).
			$equipamentos = VirtexModelo::factory('equipamentos');
			
			foreach($this->infoNAS as $id_nas => $dados_nas) {
				if( @$dados_nas["tipo_nas"] == "I" ) {
					$info = $equipamentos->obtemEnderecoInfraestruturaNAS($id_nas);
					$interface = $this->tcpip[$id_nas];
					for($i=0;$i<count($info);$i++) {
						$this->atuador->processaInstrucaoInfraestrutura($id_nas,$interface,$info[$i]["id_rede"],MODELO_Spool::$ADICIONAR,$info[$i]["rede"]);
					}					
				}
				
			}
			
			echo "OK\n";
		}
		
		/**
		 * Ergue os endereços de tcpip dos clientes.
		 */
		protected function bootClientes() {
			echo "Configurando acesso dos clientes... ";

			$contas = VirtexModelo::factory('contas');
			$equipamentos = VirtexModelo::factory('equipamentos');
			
			foreach($this->infoNAS as $id_nas => $dados_nas) {
			
				if( $dados_nas["tipo_nas"] == "I" || (($dados_nas["tipo_nas"] == "P" || $dados_nas["tipo_nas"] == "T" ) && $dados_nas["padrao"] == "O") ) {
				
					$listaContas = $contas->obtemContasBandaLarga($id_nas,"A",false);	
					// $interface = $dados_nas["tipo_nas"] == "I" ?
					
					if( $dados_nas["tipo_nas"] == "I" ) $interface = $this->tcpip[$id_nas];
					if( $dados_nas["tipo_nas"] == "P" ) $interface = $this->pppoe[$id_nas];
					if( $dados_nas["tipo_nas"] == "T" ) $interface = $this->pptp[$id_nas];
					
					for($i=0;$i<count($listaContas);$i++) {
						$mac = @$listaContas[$i]["mac"];
						$id_pop = @$listaContas[$i]["id_pop"];
						$macPOP = $equipamentos->macPOP($listaContas[$i]["id_pop"]);
						
						if( $macPOP ) {
							$mac = $macPOP;
						}
					
						$endereco = ($listaContas[$i]["tipo_bandalarga"] == "I" ? @$listaContas[$i]["rede"] : @$listaContas[$i]["ipaddr"]);
						$this->atuador->processaInstrucaoBandaLarga($id_nas,$interface,@$listaContas[$i]["id_conta"],MODELO_Spool::$ADICIONAR,@$listaContas[$i]["username"],$endereco,$mac,@$dados_nas["padrao"],@$listaContas[$i]["upload_kbps"],@$listaContas[$i]["download_kbps"],$this->fator[$id_nas]);
					}
				
				}
			}
			
			echo "OK\n";
		}
		
		/**
		 * Ergue o serviço de PPPoE
		 */
		protected function bootPPPoE() {
			echo "Configurando Serviço PPPoE... ";
			
			// DO SOMETHING
			
			echo "OK\n";
		}
		
		/**
		 * Ergue o serviço de PPTP e as regras relevantes.
		 */
		protected function bootPPTP() {
			echo "Configurando Serviço PPTP... ";
			
			// DO SOMETHING
			
			echo "OK\n";
		}
		
		
		/**
		 * Processa Spool
		 * TODO: Tratar concorrência!
		 */
		protected function processaSpool() {
			$spool = VirtexModelo::factory('spool');
			
			$preferencias = VirtexModelo::factory("preferencias");
			
			foreach( $this->tcpip as $id_nas => $interface ) {
				// echo " - $id_nas -> $interface\n";
				
				// Instruções de Spool de InfraEstrutura.
				$filaInfra = $spool->obtemInstrucoesSpool($id_nas,MODELO_Spool::$INFRAESTRUTURA,MODELO_Spool::$ST_AGUARDANDO,true);
				for($i=0;$i<count($filaInfra);$i++) {
					// TODO: REGISTRAR O ERRO ESPECÍFICO
					try {
						$this->atuador->processaInstrucaoInfraestrutura($id_nas,$interface,@$filaInfra[$i]["id"],@$filaInfra[$i]["op"],@$filaInfra[$i]["parametros"]["rede"]);
						$status = MODELO_Spool::$ST_OK;
					} catch(VirtexExcecao $e) {
						$status = MODELO_Spool::$ST_ERRO;
					}
					
					$spool->atualizaStatusInstrucao($filaInfra[$i]["id_spool"],$status);
					
				}
				unset($filaInfra);
				
				// Instruções de Spool p/ Contas de Banda Larga.
				$filaBL = $spool->obtemInstrucoesSpool($id_nas,MODELO_Spool::$BANDA_LARGA,MODELO_Spool::$ST_AGUARDANDO);
				for($i=0;$i<count($filaBL);$i++) {
					// 
					try {
						$this->atuador->processaInstrucaoBandaLarga($id_nas,$interface,@$filaBL[$i]["id"],@$filaBL[$i]["op"],@$filaBL[$i]["parametros"]["username"],@$filaBL[$i]["parametros"]["endereco"],@$filaBL[$i]["parametros"]["mac"],@$filaBL[$i]["parametros"]["padrao"],@$filaBL[$i]["parametros"]["upload"],@$filaBL[$i]["parametros"]["download"],$this->fator[$id_nas]);
						$status = MODELO_Spool::$ST_OK;
					} catch(VirtexExcecao $e) {
						$status = MODELO_Spool::$ST_ERRO;
					}
					
					$spool->atualizaStatusInstrucao($filaBL[$i]["id_spool"],$status);
				}
				unset($filaBL);
				
			}
			
			
			if( $this->ftpEnabled ) {
				// Cria diretórios de FTP
				$prefGeral = $preferencias->obtemPreferenciasGerais();
				$filaH = $spool->obtemInstrucoesSpool("",MODELO_Spool::$HOSPEDAGEM,MODELO_Spool::$ST_AGUARDANDO);
				
				$dominios_processados = 0;
				
				for($i=0;$i<count($filaH);$i++) {
				
					// print_r($filaH[$i]);
				
					$tipo_hospedagem = trim($filaH[$i]["parametros"]["tipo_hospedagem"]);
					$username = @$filaH[$i]["parametros"]["username"];
					$dominio  = @$filaH[$i]["parametros"]["dominio"];
					
					if( !$dominio ) $dominio = $prefGeral["dominio_padrao"];
					
					$base_dir = $prefGeral["hosp_base"];
					
					// Cria HOMEDIR
					$home_dir = $this->SO->homeDirMake($tipo_hospedagem,$base_dir,$dominio,$username,$prefGeral["hosp_uid"],$prefGeral["hosp_gid"]);
					
					// 
					$status = MODELO_Spool::$ST_OK;
					
					// Configura Apache
					if( $tipo_hospedagem == "D" ) {
					
						$apacheHospConfDir 	= "/mosman/virtex/app/etc/hospedagem";
						$apacheHospFile		= $apacheHospConfDir . "/httpd." . str_replace(".","-",$dominio) . ".conf";
						
						$this->SO->installDir($apacheHospConfDir);
						
						$fd = @fopen($apacheHospFile,"w");
						if( !$fd ) {
							$status = MODELO_Spool::$ST_ERRO;
						} else {
						
							$dominios_processados++;
						
							$header_http  = "<VirtualHost ".$this->ext_ip.">\n";
							$header_https = "<VirtualHost ".$this->ext_ip.":443>\n";

							$conteudo  = "       DocumentRoot ".$home_dir."/www\n";
							$conteudo .= "       ServerAdmin webmaster@".$prefGeral["dominio_padrao"]."\n";
							$conteudo .= "       ServerName ".$dominio."\n";
							$conteudo .= "       ServerSignature email\n";
							$conteudo .= "       HostNameLookups off\n";
							$conteudo .= "       CustomLog ".$home_dir."/log/access_log combined\n";
							$conteudo .= "       ErrorLog ".$home_dir."/log/error_log\n";
							$conteudo .= "</VirtualHost>\n";
							
							$contArq = $header_http . $conteudo . $header_https . $conteudo;
							
							fwrite($fd,$contArq,strlen($contArq));
							fclose($fd);
							
						}
					
					}
					
					$spool->atualizaStatusInstrucao($filaH[$i]["id_spool"],$status);

				}
				
				if( $dominios_processados ) {
					$this->SO->apachectl("restart");
				}
				
				
			}
			
			
			
			/**
			if( $this->dnsEnabled ) {
				// Cria entradas de DNS (primário e secundário)
				
				//$filaH = $spool->obtemInstrucoesSpool("",MODELO_Spool::$HOSPEDAGEM,MODELO_Spool::$ST_AGUARDANDO);
				echo "DNS ENABLED\n";
				//print_r($filaH);


			}
			*/

			if( $this->mailEnabled ) {
				// Cria estrutura de diretórios de e-mail				
				$prefGeral = $preferencias->obtemPreferenciasGerais();

				$filaE = $spool->obtemInstrucoesSpool("",MODELO_Spool::$EMAIL,MODELO_Spool::$ST_AGUARDANDO);
				
				for($i=0;$i<count($filaE);$i++) {
					
					$username = $filaE[$i]["parametros"]["username"];
					$dominio = $filaE[$i]["parametros"]["dominio"];
					
					$target = $prefGeral["email_base"] . "/" . trim($dominio) . "/" . trim($username);
					$this->SO->mailDirMake($target,$prefGeral["mail_uid"],$prefGeral["mail_gid"]);

					$status = MODELO_Spool::$ST_OK;
					$spool->atualizaStatusInstrucao($filaE[$i]["id_spool"],$status);
										
				}
				
			}

		}
		
		
	}
	
?>
