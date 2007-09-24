<?

	/**
	 * Classe de execução da spool.
	 */
	class VAPPSpool extends VirtexApplication {


		public static $FW_SUB_BASERULE			= 2000;
		public static $FW_IP_BASERULE			= 10000;
		public static $FW_IP_BASEPIPE_IN		= 18000;
		public static $FW_IP_BASEPIPE_OUT		= 26000;

		public static $FW_PPPoE_BASERULE		= 34000;
		public static $FW_PPPoE_BASEPIPE_IN		= 42000;
		public static $FW_PPPoE_BASEPIPE_OUT	= 50000;



		
		// tcpip.ini - Arquivo de configurações de NAS tcpip.
		protected $nasConfig;
		protected $netConfig;
		
		
		// Cache de informações
		protected $tcpip;
		protected $pppoe;
		protected $pptp;
		
		protected $network;
		protected $ext_iface;	// Interface externa
		
		protected $infoNAS;
		
		protected $SO;
		
		public function __construct() {
			parent::__construct();
			$this->SO = new SOFreeBSD();
			
			// Configurações dos NAS
			$this->nasConfig = MConfig::getInstance("etc/nas.ini");
			
			// Configurações de Rede
			$this->netConfig = MConfig::getInstance("etc/network.ini");
			
			$this->processaConfig();
			
		}
		
		/**
		 * Realiza processamento dos arquivos de configuração. (p/ cache de objetos significativos).
		 */
		protected function processaConfig() {
			$this->tcpip 	= array();
			$this->pppoe 	= array();
			$this->infoNAS 	= array();
			
			$this->network = $this->netConfig->config;
			
			foreach($this->network as $iface => $dados) {
				if( $dados["status"] == "up" ) {
					if( $dados["type"] == "external" ) {
						$this->ext_iface = $iface;
					}
				}
			}
			
			$equipamentos = null;
			
			if( count($this->nasConfig->config) ) {
				$equipamentos = VirtexModelo::factory("equipamentos");
			}
			
			foreach($this->nasConfig->config as $nas => $dados) {
				// echo "NAS: " . $nas . "\n";
				@list($tipo,$id_nas) = explode(":",$nas);
				
				if( ((int)$dados["enabled"]) && trim($dados["interface"]) ) {
					if( $equipamentos ) {
						$this->infoNAS[$id_nas] = $equipamentos->obtemNAS($id_nas);
					}
				
					// Adicionar no cache
					
					if( $tipo == "pppoe" ) {
						// 
						$this->pppoe[$id_nas] = trim($dados["interface"]);
					}
					
					if( $tipo == "pptp" ) {
						//
						$this->pptp[$id_nas] = trim($dados["interface"]);
					}

					if( $tipo == "tcpip" ) {
						//
						$this->tcpip[$id_nas] = trim($dados["interface"]);
					}

				}
				
			}

		}
		
		protected function selfConfig() {
			$this->_shortopts 	= "b";
			$this->_longopts 	= array("boot");
			
		}
		
		public function executa() {
			// echo "\n\n\nEXECUTA\n\n\n";
			
			if( in_array("b",$this->options) || in_array("--boot",$this->options) ) {
				// Rotina de Boot

				echo "BOOT!!\n";
				
				// $this->bootNetwork();
				$this->bootInfraestrutura();
				//$this->bootClientes();
				//$this->bootPPPoE();
				//$this->bootPPTP();
				
			} else {
				// Processamento de spool
				echo "SPOOL!!\n";
				$this->processaSpool();
				
				
				// print_r($spool);
				
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
					$this->SO->ifConfig($interface,$ip,$mascara);

					if( @$dados["type"] == "external" && $ip && $mascara && @$dados["gateway"] ) {
						// Adicionar rota padrão.
						$this->SO->routeAdd("default",$dados["gateway"]);
					}
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
						$this->processaInstrucaoInfraestrutura($id_nas,$interface,$info[$i]["id_rede"],MODELO_Spool::$ADICIONAR,$info[$i]["rede"]);
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
			
			foreach($this->infoNAS as $id_nas => $dados_nas) {
			
				if( $dados_nas["tipo_nas"] == "I" || (($dados_nas["tipo_nas"] == "P" || $dados_nas["tipo_nas"] == "T" ) && $dados_nas["padrao"] == "O") ) {
				
					$listaContas = $contas->obtemContasBandaLarga($id_nas,"A");	
					// $interface = $dados_nas["tipo_nas"] == "I" ?
					
					if( $dados_nas["tipo_nas"] == "I" ) $interface = $this->tcpip[$id_nas];
					if( $dados_nas["tipo_nas"] == "P" ) $interface = $this->pppoe[$id_nas];
					if( $dados_nas["tipo_nas"] == "T" ) $interface = $this->pptp[$id_nas];
					
					for($i=0;$i<count($listaContas);$i++) {
						$endereco = ($listaContas[$i]["tipo_bandalarga"] == "I" ? @$listaContas[$i]["rede"] : @$listaContas[$i]["ipaddr"]);
						$this->processaInstrucaoBandaLarga($id_nas,$interface,@$listaContas[$i]["id_conta"],MODELO_Spool::$ADICIONAR,@$listaContas[$i]["username"],$endereco,@$listaContas[$i]["mac"],@$dados_nas["padrao"],@$listaContas[$i]["upload_kbps"],@$listaContas[$i]["download_kbps"]);
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
			
			foreach( $this->tcpip as $id_nas => $interface ) {
				// echo " - $id_nas -> $interface\n";
				
				// Instruções de Spool de InfraEstrutura.
				$filaInfra = $spool->obtemInstrucoesSpool($id_nas,MODELO_Spool::$INFRAESTRUTURA,MODELO_Spool::$ST_AGUARDANDO,true);
				//print_r($filaInfra);
				for($i=0;$i<count($filaInfra);$i++) {
					// TODO: REGISTRAR O ERRO ESPECÍFICO
					try {
						$this->processaInstrucaoInfraestrutura($id_nas,$interface,@$filaInfra[$i]["id"],@$filaInfra[$i]["op"],@$filaInfra[$i]["parametros"]["rede"]);
						$status = MODELO_Spool::$ST_OK;
					} catch(VirtexExcecao $e) {
						$status = MODELO_Spool::$ST_ERRO;
					}
					
					$spool->atualizaStatusInstrucao($filaInfra[$i]["id_spool"],$status);
					
				}
				unset($filaInfra);
				
				// Instruções de Spool p/ Contas de Banda Larga.
				$filaBL = $spool->obtemInstrucoesSpool($id_nas,MODELO_Spool::$BANDA_LARGA,MODELO_Spool::$ST_AGUARDANDO);
				// print_r($filaBL);
				for($i=0;$i<count($filaBL);$i++) {
					// 
					try {
						$this->processaInstrucaoBandaLarga($id_nas,$interface,@$filaBL[$i]["id"],@$filaBL[$i]["op"],@$filaBL[$i]["parametros"]["username"],@$filaBL[$i]["parametros"]["endereco"],@$filaBL[$i]["parametros"]["mac"],@$filaBL[$i]["parametros"]["padrao"],@$filaBL[$i]["parametros"]["upload"],@$filaBL[$i]["parametros"]["download"]);
						$status = MODELO_Spool::$ST_OK;
					} catch(VirtexExcecao $e) {
						$status = MODELO_Spool::$ST_ERRO;
					}
					
					$spool->atualizaStatusInstrucao($filaBL[$i]["id_spool"],$status);
				}
				unset($filaBL);
				
			}

		}
		
		protected function processaInstrucaoInfraestrutura($id_nas,$interface,$id,$op,$rede) {
			// self::SO
			$addr = new MInet($rede);
			
			if( $op == MODELO_Spool::$ADICIONAR ) {
				$this->SO->ifConfig($interface,$addr->obtemUltimoIP(),$addr->obtemMascara());
				$this->SO->adicionaRegraSP($id,self::$FW_SUB_BASERULE,$rede,$this->ext_iface);
			} else {
				$this->SO->ifUnConfig($interface,$addr->obtemUltimoIP());
				$this->SO->deletaRegraSP($id,self::$FW_SUB_BASERULE);
			}
			
			return(true);
			
		}
		
		protected function processaInstrucaoBandaLarga($id_nas,$interface,$id,$op,$username,$endereco,$mac,$padrao,$upload,$download) {
			$addr = new MInet($endereco);
			
			// Padrão é somente para outros padrões (com gerenciamento externo da banda)
			// A responsabilidade seria somente p/ regra e coleta de estatística.
			if( $padrao ) {
				$upload 		= 0;
				$download 		= 0;
			}
			
			if( @$this->infoNAS[$id_nas]["tipo_nas"] == "P" ) {
				$baserule 		= self::$FW_PPPoE_BASERULE;
				$basepipe_in 	= self::$FW_PPPoE_BASEPIPE_IN;
				$basepipe_out	= self::$FW_PPPoE_BASEPIPE_OUT;
			} else {
				$baserule 		= self::$FW_IP_BASERULE;
				$basepipe_in 	= self::$FW_IP_BASEPIPE_IN;
				$basepipe_out	= self::$FW_IP_BASEPIPE_OUT;
			}
			
			$ip = $addr->obtemUltimoIP();
			if( $op == MODELO_Spool::$ADICIONAR ) {
				$this->SO->ifConfig($interface,$addr->obtemPrimeiroIP(),$addr->obtemMascara());
				$this->SO->adicionaRegraBW($id,$baserule,$basepipe_in,$basepipe_out,$interface,$this->ext_iface,$ip,$mac,$upload,$download,$username);
			} else {
				$this->SO->ifUnConfig($interface,$addr->obtemPrimeiroIP());
				$this->SO->deletaRegraBW($id,$baserule,$basepipe_in,$basepipe_out);
			}
		}
		
	}
	
?>
