<?


	class VirtexCommServer extends VirtexComm {
		protected $host;
		protected $port;		
		protected $userbase;
		
		protected $sessions;
	
		public function __construct($chave,$host="0.0.0.0",$port="11000",$userbase=array()) {
			$this->initVars();
			
			$this->sessions = array();


			$this->chave    = trim($chave);
			$this->host     = trim($host);
			$this->port     = trim($port);
			$this->userbase = $userbase;
		}

		protected function initVars() {
			$this->host = "0.0.0.0";
			$this->port = "11000";
		}
		
		protected function auth($user,$pass) {
			if( @$this->userbase[trim($user)]["enabled"] ) {
				if( trim($pass) && trim($pass) == trim(@$this->userbase[trim($user)]["password"]) ) {
					return true;
				}
			}
			return false;
		}
		
		protected function dialog($allclient, $socket, $buf, $bytes) {

			foreach($allclient as $client) {
				// socket_write($client,"TESTANDO!!!\n");
				$idx = print_r($client,true);

				if(!isset($this->sessions[$idx])) {				
					// Dialogo inicial
					$this->sessions[$idx] = array("challenge" => crypt( rand(1000,2000) ,"VA"),"transmissao" => false, "tipo_transmissao" => '', "dados" => '', "buffer" => '', "user" => '', "pass" => '');
					@socket_write($client,$this->talk("VACH",$this->sessions[$idx]["challenge"],$this->chave));					
				} else {
					if( !$this->sessions[$client]["transmissao"] ) {
						$proc = $this->listen($buf,$this->chave);
						
						switch($proc["comando"]) {
							/**
							 * Autenticaçao
							 */
							case 'VARP':
								// Resposta de challenge - username:senha criptografado com o challenge

								// Decriptografa
								$cript_auth = $proc["parametros"];
								$infoauth = $this->decriptografa(base64_decode($cript_auth),$this->sessions[$idx]["challenge"]);


								// TODO: AutenticaXXo
								@list($this->sessions[$idx]["user"],$this->sessions[$idx]["pass"]) = explode("::",$infoauth);

								if( !$this->auth($this->sessions[$idx]["user"],$this->sessions[$idx]["pass"]) ) {
									@socket_write($client,$this->talk("VAER","Autenticação Inválida",$this->sessions[$idx]["challenge"]));
									continue;
								}

								@socket_write($client,$this->talk("VAOK","Bem vindo",$this->sessions[$idx]["challenge"]));

								break;

							/**
							 * SendStats - O host cliente deseja enviar as estatísticas de acesso.
							 * Início de transmissão
							 */
							case 'VASS':
								$tipo_transmissao = 'stats';
								$this->sessions[$idx]["dados"] = "";
								$this->sessions[$idx]["buffer"] = "";
								@socket_write($client,$this->talk("VAOK","Aguardando início de transmissão",$this->sessions[$idx]["challenge"]));
								$this->sessions[$idx]["transmissao"] = true;
								break;

							/**
							 * Envia a lista de ARP para o cliente.
							 */
							case 'VAAR': // ARP REQUEST
								// Primeiro envia um "VAAS" (iniciando ARP SEND)
								@socket_write($client,$this->talk("VAAS","",$this->sessions[$idx]["challenge"]));
								$ip = trim($proc["parametros"]);

								if(!$ip) $ip = "-a";
								$arp = SOFreeBSD::obtemARP($ip);


								$tabelaarp="";
								for($i=0;$i<count($arp);$i++) {
									$tabelaarp .= $arp[$i]["addr"].",".$arp[$i]["mac"].",".$arp[$i]["iface"]."\n";
								}

								@socket_write($client,base64_encode($this->criptografa($tabelaarp,$this->sessions[$idx]["challenge"])));								
								@socket_write($client,"\n.\n");
								unset($tabelaarp);

								break;

							/**
							 * Evia um FPING
							 */
							case 'VAFP':
								// Primeiro envia um "VAFS" (iniciando FPING SEND)
								@socket_write($client,$this->talk("VAFS","",$this->sessions[$idx]["challenge"]));
								list($ip,$num_pacotes,$tamanho) = explode(":",trim($proc["parametros"]));


								$ping = (!$ip ? array() : SOFreeBSD::fping($ip,$num_pacotes,$tamanho));
								$resposta = implode(":",$ping);

								@socket_write($client,base64_encode($this->criptografa($resposta,$this->sessions[$idx]["challenge"])));
								@socket_write($client,"\n.\n");

								break;

							/**
							 * Envia a lista de estatXsticas para o cliente.
							 */
							case 'VASR': // STAT REQUEST
								// Primeiro envia um "VASI" (iniciando STAT INIT)
								@socket_write($client,$this->talk("VASI","",$this->sessions[$idx]["challenge"]));
								$stats = SOFreeBSD::obtemEstatisticas();


								$estatisticas="";

								while( list($username,$dados) = each($stats) ) {
									$estatisticas .= $username . "," . $dados["up"] . "," . $dados["down"] . "\n";
								}
								

								@socket_write($client,base64_encode($this->criptografa($estatisticas,$this->sessions[$idx]["challenge"])));
								@socket_write($client,"\n.\n");

								unset($estatisticas);
								
								break;
							
							/**
							 * Envia um tablelist para o cliente
							 */
							case 'VATL':
								// Primeiro envia um "VATI" (iniciando um TABLE INIT)
								@socket_write($client,$this->talk("VATI","",$this->sessions[$idx]["challenge"]));
								$nomeTabela=$proc["parametros"];
								$tabela=SOFreeBSD::listaEnderecosTabela($nomeTabela);
								@socket_write($client,base64_encode($this->criptografa(implode(",",$tabela),$this->sessions[$idx]["challenge"])));
								@socket_write($client,"\n.\n");
								unset($nomeTabela);
								unset($tabela);
								
								break;
								
							/**
							 * Adiciona um endereco na tabela
							 */
							case 'VATA':
								// @socket_write($client,$this->talk("VAFS","",$this->sessions[$idx]["challenge"]));
								list($tabela,$ip) = explode(":",trim($proc["parametros"]));
								
								SOFreeBSD::adicionaEnderecoTabela($tabela,$ip);
								
								unset($tabela);
								unset($ip);
								
								@socket_write($client,$this->talk("VAOK","Endereco Adicionado",$this->sessions[$idx]["challenge"]));
								
								break;

							/**
							 * Remove um endereco na tabela
							 */
							case 'VATR':
								// @socket_write($client,$this->talk("VAFS","",$this->sessions[$idx]["challenge"]));
								list($tabela,$ip) = explode(":",trim($proc["parametros"]));
								
								SOFreeBSD::removeEnderecoTabela($tabela,$ip);
								
								unset($tabela);
								unset($ip);
								
								@socket_write($client,$this->talk("VAOK","Endereco Adicionado",$this->sessions[$idx]["challenge"]));
								
								break;


						}
					} else {
						if( trim($buf) == "." ) {
							$this->sessions[$idx]["dados"] .= $this->decriptografa(base64_decode($this->sessions[$idx]["buffer"]),$this->sessions[$idx]["challenge"]);
							@socket_write($client,$this->talk("VAOK","Dados Recebidos",$this->sessions[$idx]["challenge"]));
						} else {
							$this->sessions[$idx]["buffer"] = $buf;
						}
					}
				}
			}
			print_r($allcliets);
		}
		
		/**
		 * Inicializa o servidor
		 */
		public function start() {

			//$server_str = "tcp://" . $this->host . ":" . $this->port;
			//$socket = @stream_socket_server($server_str, $errno, $errstr);			

			//if( !$socket ) {
			//	echo "$errstr ($errno)\n";
			//	exit(-1);
			//} else {
				/**
				 * Loop Principal
				 */
				
				// Socket Inicial
				if (($master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
					echo "socket_create() failed: reason: " . socket_strerror($master) . "\n";
				}
				
				socket_set_option($master, SOL_SOCKET,SO_REUSEADDR, 1); 

				if (($ret = socket_bind($master, $this->host, $this->port)) < 0) {
					echo "socket_bind() failed: reason: " . socpket_strerror($ret) . "\n";
				}

				if (($ret = socket_listen($master, 5)) < 0) {
					echo "socket_listen() failed: reason: " . socket_strerror($ret) . "\n";
				}
				
				$read_sockets = array($master);
				 
				while(true) {
					//echo "LOOP!!\n";
					$changed_sockets = $read_sockets;
					$num_changed_sockets = socket_select($changed_sockets, $write = NULL, $except = NULL, NULL);
					
					foreach($changed_sockets as $socket) {
						if ($socket == $master) {
							if (($client = socket_accept($master)) < 0) {
								continue;
							} else {
								array_push($read_sockets, $client);
								$allclients = $read_sockets;
								$this->dialog($allclients,$sockeet,"",0);
							}
						} else {
							$bytes = @socket_recv($socket, $buffer, 2048, 0);
							//echo "BYTES: $bytes\n";
							if ($bytes == 0) {
								$index = array_search($socket, $read_sockets);
								unset($read_sockets[$index]);
								@socket_close($socket);
							} else {
								$allclients = $read_sockets;
								array_shift($allclients);    // remove master
								$this->dialog($allclients, $socket, $buffer, $bytes);
							}							

						}
					}
				
				}

			//}
		
		}
	
	}






?>
