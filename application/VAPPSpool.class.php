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
			
			foreach($this->infoNAS as $id_nas => $dados_nas) {
			
				if( $dados_nas["tipo_nas"] == "I" || (($dados_nas["tipo_nas"] == "P" || $dados_nas["tipo_nas"] == "T" ) && $dados_nas["padrao"] == "O") ) {
				
					$listaContas = $contas->obtemContasBandaLarga($id_nas,"A");	
					// $interface = $dados_nas["tipo_nas"] == "I" ?
					
					if( $dados_nas["tipo_nas"] == "I" ) $interface = $this->tcpip[$id_nas];
					if( $dados_nas["tipo_nas"] == "P" ) $interface = $this->pppoe[$id_nas];
					if( $dados_nas["tipo_nas"] == "T" ) $interface = $this->pptp[$id_nas];
					
					for($i=0;$i<count($listaContas);$i++) {
						$endereco = ($listaContas[$i]["tipo_bandalarga"] == "I" ? @$listaContas[$i]["rede"] : @$listaContas[$i]["ipaddr"]);
						$this->atuador->processaInstrucaoBandaLarga($id_nas,$interface,@$listaContas[$i]["id_conta"],MODELO_Spool::$ADICIONAR,@$listaContas[$i]["username"],$endereco,@$listaContas[$i]["mac"],@$dados_nas["padrao"],@$listaContas[$i]["upload_kbps"],@$listaContas[$i]["download_kbps"],$this->fator[$id_nas]);
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

		}
		
		
	}
	
?>
