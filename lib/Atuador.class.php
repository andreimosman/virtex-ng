<?

	/**
	 * Classe que promove interação com o sistema operacional dentro do escopo do sistema.
	 *
	 * Baseada no modelo da spool.
	 *
	 */
	class Atuador {

		public static $FW_SUB_BASERULE			= 2000;
		public static $FW_IP_BASERULE			= 10000;
		public static $FW_IP_BASEPIPE_IN		= 18000;
		public static $FW_IP_BASEPIPE_OUT		= 26000;

		public static $FW_PPPoE_BASERULE		= 34000;
		public static $FW_PPPoE_BASEPIPE_IN		= 42000;
		public static $FW_PPPoE_BASEPIPE_OUT	= 50000;

		
		protected $SO;
		protected $contas;
		protected $equipamentos;

		
	

		public function __construct() {
			$this->SO = new SOFreeBSD();
			$this->contas = VirtexModelo::factory("contas");
			$this->equipamentos = VirtexModelo::factory("equipamentos");
		}
		
		public function configuraRede($interface,$ip,$mascara,$gateway="") {
			$this->SO->ifConfig($interface,$ip,$mascara);
			
			if( $gateway ) {
				$this->SO->routeAdd("default",$dados["gateway"]);
			}
		}
		
	
		public function processaInstrucaoInfraestrutura($id_nas,$interface,$id,$op,$rede) {
			// self::SO
			$addr = new MInet($rede);
			
			if( $op == MODELO_Spool::$ADICIONAR ) {
				$this->SO->ifConfig($interface,$addr->obtemUltimoIP(),$addr->obtemMascara());
				$this->SO->adicionaRegraSP($id,self::$FW_SUB_BASERULE,$rede,$this->ext_iface);
			} else {
				$this->SO->ifUnConfig($interface,$addr->obtemUltimoIP());
				$this->SO->deletaRegraSP($id,self::$FW_SUB_BASERULE);
			}
			
			
			// Verificar de existe MAC configurado no equipamento e atribuir o bloqueio de MAC
			$nas = $this->equipamentos->obtemNAS($id_nas);
			if( @$nas["id_servidor"] ) {
				$pops = $this->equipamentos->obtemPOPsPeloServidor($dados_nas["id_servidor"]);
				for($i=0;$i<count($pops);$i++) {
					if( $pops[$i]["ipaddr"] && $pops[$i]["status"] == "A" ) {
						$macArvore = $this->equipamentos->macPOP($pops[$i]["id_pop"]);
						$this->SO->removeARP($pops[$i]["ipaddr"]);
						if( $macArvore ) {
							$this->SO->atribuiARP($pops[$i]["ipaddr"],$macArvore);
						}
					}
				}
			}
			
			return(true);
			
		}
		
		public function processaInstrucaoBandaLarga($id_nas,$interface,$id,$op,$username,$endereco,$mac,$padrao,$upload,$download,$fator=1) {
			$addr = new MInet($endereco);
			
			if( !$fator ) $fator = 1;
			
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

				$this->SO->removeARP($ip);
				
				if( $this->infoNAS[$id_nas]["tipo_nas"] == "I" ) {
					$conta = $this->contas->obtemContaPeloId($id);
					if( @$conta["id_pop"] ) {
						$macPOP = $this->equipamentos->macPOP($conta["id_pop"]);
						if( $macPOP ) {
							$mac = $macPOP;
						}
					}
				}
				
				


				$this->SO->ifConfig($interface,$addr->obtemPrimeiroIP(),$addr->obtemMascara());
				$this->SO->adicionaRegraBW($id,$baserule,$basepipe_in,$basepipe_out,$interface,$this->ext_iface,$ip,$mac,$upload*$fator,$download*$fator,$username);
				
			} else {
				$this->SO->removeARP($ip);
				$this->SO->ifUnConfig($interface,$addr->obtemPrimeiroIP());
				$this->SO->deletaRegraBW($id,$baserule,$basepipe_in,$basepipe_out);
			}
		}
	
	
	}




?>
