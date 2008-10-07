<?

	class VirtexCommClient extends VirtexComm {
	
		//protected $conectado;
		protected $incremento;

		public function __construct($incremento=0) {
			parent::__construct();
			
			$this->incremento = $incremento;
		}
		
		/**
		 * Autenticação
		 */
		protected function clientAuth($chave,$user,$pass) {
			if( $this->conectado ) {
				/**
				 * Interpreta a solicitação
				 */
				// Zera a indicação de conectado.
				//$this->conectado 	= false;
				
				$this->conectado = true;
				$this->chave 		= "";

				while( ($linha=fgets($this->conn)) && !feof($this->conn) ) {
					$proc = $this->listen($linha,$chave);
					switch($proc["comando"]) {

						case 'VACH':
							// Recebeu o challenge

							$challenge = $proc["parametros"];
							$infoauth = "$user::$pass";
							$cript_auth = base64_encode($this->criptografa($infoauth,$challenge));

							// Enviar resposta
							fputs($this->conn,$this->talk("VARP",$cript_auth,$chave));
							break;

						case 'VAOK':
							$this->challenge 	= $challenge;
							$this->chave		= $chave;
							$this->conectado 	= true;
							return(true);
							break;


						case 'VAER':
							$tihs->conectado 	= false;
							return(false);
							break;

					}
				}
			}
			
			return(false);
		}
		
		/**
		 * Obtem Dados
		 */
		protected function getData($comando,$parametros) {
			if( !$this->conectado ) return "";

			@fputs($this->conn,$this->talk($comando,$parametros,$this->chave));
			$recebendo = false;
			$dados = "";
			while( ($linha=fgets($this->conn)) && !feof($this->conn) ) {
				if(!$recebendo) {
					$proc = $this->listen($linha,$this->chave);

					switch($proc["comando"]) {
						case 'VAER':
							return("");
							break;

						case 'VAAS':
							// INICIANDO O ARP SEND
						case 'VAFS':
							// INICIANDO O FPING SEND
						case 'VASI':
							// STATS INIT
						case 'VATI':
							// TABLE INIT

							$recebendo = true;
							break;

					}
				} else {
					if( trim($linha) == "." ) {
						// Final de transmissão
						//echo "-------------------------------\n";
						//echo "$dados\n";
						//echo "--------------------------------\n";

						$dados = $this->decriptografa(base64_decode($dados),$this->challenge);

						//echo "-------------------------------\n";
						//echo "$dados\n";
						//echo "--------------------------------\n";

						return $dados;


					}
					$dados .= $linha;
				}
			}

		}

		
		/**
		 * Abre uma conexão
		 */
		public function open($host,$porta,$chave,$user,$pass) {

			if( $this->conn ) {
				@fclose($this->conn);			
			}
			
			// $this->conn = @fsockopen($host,$porta+$this->incremento,$errno,$errstr,30);
			$this->conn = @fsockopen($host,$porta,$errno,$errstr,30);

			if( !$this->conn ) {

				$this->conectado = false;
				return(false);
			} else {
				$this->conectado = true;
				

				if( !$this->clientAuth($chave,$user,$pass) ) {
					$this->conectado = false;
					return(false);
				}
				
			}
			$this->conectado = true;
			
			return(true);
		
		}
		
		/**
		 * Fecha a conexão
		 */
		public function close() {
			@fclose($this->conn);
		}
		
		/**
		 * Obtem a tabela arp geral ou do endereço especificado
		 */
		public function getARP($ip="") {
			$dados = $this->getData("VAAR",$ip);
			
			$arp = array();
			
			$linhas = explode("\n",$dados);
			
			for($i=0;$i<count($linhas);$i++) {
				@list($addr,$mac,$iface) = explode(',',$linhas[$i]);
				$arp[] = array( "addr" => $addr, "mac" => $mac, "iface" => $iface );
			}
			
			return($arp);
		}
		
		/**
		 * Obtem a tabela (do pf)
		 */
		public function getTableList($tabela) {
			$dados = trim($this->getData("VATL",$tabela));
			$retorno = $dados ? explode(",",$dados) : array();
			return($retorno);
		}
		
		/**
		 * Adiciona endereco na tabela
		 */
		public function addTableAddr($tabela,$addr) {
			@fputs($this->conn,$this->talk("VATA","$tabela:$addr",$this->chave));
		}

		/**
		 * Remove endereco na tabela
		 */
		public function delTableAddr($tabela,$addr) {
			@fputs($this->conn,$this->talk("VATR","$tabela:$addr",$this->chave));
		}

		/**
		 * Obtem o resultado de um fping do endereço especificado.
		 */
		public function getFPING($ip,$num_pacotes=2,$tamanho="") {
			$dados = $this->getData("VAFP",$ip.":".$num_pacotes.":".$tamanho);
			return(explode(":",$dados));
		}

		
		/**
		 * Obtem as estatísticas de acesso
		 * Retorna lista no formato padrão
		 */
		public function getStats() {
			$dados = $this->getData("VASR","");
			return($dados);
		}

	}

