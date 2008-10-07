<?

	/**
	 * VAPPCaptive - Aplicativo para Captive Portal, utilizado pelo squid
	 * através de redirect_program
	 */
	class VAPPCaptive extends VirtexApplication {
	
		protected $captiveConfig;

		public function __construct() {
			parent::__construct();
			$this->captiveConfig = MConfig::getInstance("etc/captive.ini");
		}
		
		protected function selfConfig() {
			$this->_startdb 	= false;
		}
		
		public function executa() {
			// Pega o ip da interface externa pra chamar no redirecionador.
			$target = trim($this->netConfig->config[$this->ext_iface]["ipaddr"]);
			
			if( $target ) {
				$target = "http://$target:8080/";
			}
			
			if( !(int)@$this->captiveConfig->config["geral"]["enabled"] ) {
				$target = "";	// Sem target o sistema funciona como se estivesse desabilitado.
			} else {
				$timeout = (int)@$this->captiveConfig->config["geral"]["timeout"];
				if( !$timeout ) $timeout = 120;
			}
			
			$tabela = "auth";	// CONSTANTE, NÃO MUDA, SOMENTE ESTA TABELA SERÁ USADA PARA ESTE FIM
			
			while(true) {
				$fd = fopen("php://stdin","r");
				while( !feof($fd) ) {
					$linha = fgets($fd,4096);
					if( !$target ) {
						echo "\n";
						continue;
					}
					
					list($url,$b) = explode(" ",$linha,2);
					list($ip,$lixo) = explode("/",$b,2);
					
					unset($b);
					unset($lixo);
					
					$url = rawurlencode(base64_encode($url));
					
					
					if( !$this->SO->verificaEnderecoTabela($tabela,$ip,$timeout) ) {
						echo $target."?url=".$url;
					}
					echo "\n";
										
				}
				fclose($fd);
			
			}
			
		}

	}

