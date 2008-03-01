<?


	/**
	 * Classe de Autenticação (RADIUS).
	 *
	 * -A 	auth
	 *
	 *
	 *
	 *
	 * -C 	accounting
	 */
	
	class VAPPRadius extends VirtexApplication {
		protected $auth;
		protected $acct;
		
		protected $username;
		protected $password;
		protected $foneinfo;
		
		protected $entrada;
		protected $saida;
		protected $session;
		protected $bytes_in;
		protected $bytes_out;
		protected $nas;
		protected $ip_addr;
		protected $tempo;
		
		protected $terminate_cause;
		
		
		protected $tipo_conta;
		
		
		
		// Objetos base
		protected $preferencias;
		protected $radius;
		
		public function __construct() {
			parent::__construct();
			
			$this->preferencias		= VirtexModelo::factory('preferencias');
			$this->radius 			= VirtexModelo::factory('radius');

		}


		protected function selfConfig() {
			$this->_shortopts	= "ACu:w:f:ESs:I:O:n:i:t:c:";
			
			$this->auth = 0;
			$this->acct = 0;
			
			$this->username = "";
			$this->password = "";
			$this->foneinfo = "";
			
			$this->entrada 		= 0;
			$this->saida 		= 0;
			$this->session 		= 0;
			$this->bytes_in 	= 0;
			$this->bytes_out	= 0;
			$this->nas			= "";
			$this->ip_addr		= "";
			$this->tempo		= 0;
			
			$this->terminate_cause = "";
			
			$this->tipo_conta = "";
		
		}
		
		/**
		 * Analiza as opções e configura a execução do aplicativo.
		 */
		protected function configuraExecucao() {
			// echo "TESTE\n";
		
			foreach($this->options as $op) {
				$opcao = @$op[0];
				$param = @$op[1];
			
				switch($opcao) {
					/**
					 * Opções de Execução
					 */
					case 'A':
						$this->auth = 1;
						break;
						
					case 'C':
						$this->acct = 1;
						break;
						
					/**
					 * Parametros de autenticação
					 */
					
					case 'u':
						$this->username = $param;
						break;
					
					case 'w':
						$this->password = $param;
						break;
						
					case 'f':
						$this->foneinfo = $param;
						break;
					
					/**
					 * Parametros de Accounting
					 */
					
					case 'E':
						$this->entrada = 1;
						break;
					
					case 'S':
						$this->saida = 1;
						break;
					
					case 's':
						$this->session = $param;
						break;
					
					case 'I':
						$this->bytes_in = $param;
						break;
					
					case 'O':
						$this->bytes_out = $param;
						break;
					
					case 'n':
						$this->nas = $param;
						break;
						
					case 'i':
						$this->ip_addr = $param;
						break;
						
					case 't':
						$this->tempo = $param;
						break;
					
					case 'c':
						$this->terminate_cause = $param;
						break;

				}
			
			
			}
		
		
		
		
		}
		
		public function executa() {
			$this->configuraExecucao();
			
			if( ($this->auth && $this->acct) || (!$this->auth && !$this->acct) ) {
				// A execução requer um (e somente um) parâmetro de tipo de operação (autenticação ou accouting).
				echo "ERR\n";
				return(-1);
			}
			
			$prefs = $this->preferencias->obtemPreferenciasGerais();
			$dominio_padrao = $prefs["dominio_padrao"];
			
			/****************************************************
			 * AUTENTICAÇÃO                                     *
			 ****************************************************/
			 
			 

			if( $this->auth ) {
				
				try {
			
					if( !$this->username ) throw new Exception("Username em branco");				
					if( !$this->password ) throw new Exception("Senha em branco");
					if( !$this->foneinfo ) throw new Exception("Origem da conexão (caller-id) em branco.");

					// IDENTIFICAÇÃO DO TIPO DE ACESSO

					$mac_pattern = "^([0-9a-fA-F]{1,2}[:\-]{1}){5}[0-9a-fA-F]{1,2}$";
					$this->tipo_conta = ereg($mac_pattern,$this->foneinfo) ? "BL" : "D";
					
					if( $this->tipo_conta == 'BL' ) {
						$this->foneinfo = self::mac($this->foneinfo);
					}

					$contas = VirtexModelo::factory("contas");
					$conta = $contas->obtemContaPeloUsername($this->username,$dominio_padrao,$this->tipo_conta);

					// Verificação da existência da conta.
					if( !count($conta) ) throw new Exception("Conta não encontrada");

					// Verificação do status da conta.
					if( @$conta["status"] != 'A' ) throw new Exception("Conta " . (@$conta["status"] == "C" ? "cancelada" : (@$conta["status"] == "S" ? "suspensa (pagamento)" : "bloqueada")));

					// Verificação da senha
					$salt = MCript::obtemSal($conta["senha_cript"]);				
					if( $conta["senha_cript"] != MCript::criptSenha($this->password,$salt) ) throw new Exception("Senha não confere");
					
					$registraMac = false;

					// Verificação de foneinfo.
					if( $this->tipo_conta == "D" ) {
						// Discado
						if( $conta["foneinfo"] ) {
							// Caso tenha cadastrado o foneinfo.
							if( !ereg($user["foneinfo"],$this->foneinfo) ) throw new Exception("Telefone não confere com cadastrado no sistema");
						}

					} else {

						if( $conta["mac"] ) {
							if( self::mac($conta["mac"]) != $this->foneinfo ) throw new Exception("MAC não confere com cadastro no sistema");
						} else {
							$registraMac = true;
						}


					}

					// Verificação da situação do contrato.
					$cobranca = VirtexModelo::factory("cobranca");

					$cliente_produto = $cobranca->obtemClienteProduto($conta["id_cliente_produto"]);

					if( !count($cliente_produto) ) throw new Exception("Inconsistência: cliente_produto inexistente");
					if( $cliente_produto["excluido"] == 't' ) throw new Exception("Inconsistência: Contrato excluido");

					$contrato = $cobranca->obtemContratoPeloId($conta["id_cliente_produto"]);

					if( !count($contrato) ) throw new Exception("Inconsistência: Contrato inexistente");				
					if( trim($contrato["status"]) != 'A' ) throw new Exception("Contrato " . ($contato["status"] == "C" ? "cancelado" : "suspenso (pagamento)"));
					
					
					
					if( $registraMac ) {
						// Não tem MAC, cadastrar no sistema. TODO: Opção para personalizaçào do MAC

						$conta["mac"] = $this->foneinfo;

						$contas->alteraContaBandaLarga($conta["id_conta"],"",$conta["status"],"",$conta["conta_mestre"],
										$conta["id_pop"], $conta["id_nas"],
										$conta["upload_kbps"],
										$conta["download_kbps"],$conta["mac"]);

						$mensagem = "Registrando MAC";

						// LOG
						$this->radius->registraEventoInfo($this->username,$mensagem,$this->foneinfo);
					}
					
				} catch(Exception $e) {
					$this->radius->registraEventoErro($this->username,$e->getMessage(),$this->foneinfo);
					return(-1);
				}
				
				/**
				 * Autenticado. Verificar Retorno
				 */
				
				// print_r($conta);
				
				if( $this->tipo_conta == 'BL' ) {
					// Contas Banda Larga
					if( $conta["tipo_bandalarga"] == 'P' && $conta["ipaddr"] ) {
						// Delegação estática de IP (para fins de monitoramento).
						echo "Framed-IP-Address = " . $conta["ipaddr"] . "\n";
						echo "Framed-Compression = Van-Jacobson-TCP-IP\n";
						echo "Framed-Protocol = PPP\n";
						echo "Service-Type = Framed-User\n";
					}
					
					if( $conta["upload_kbps"] && $conta["download_kbps"]) {
						// Informações de upload p/ configuração de banda.
						echo "Mikrotik-Rate-Limit = " . $conta["upload_kbps"] . 'k/' . $conta["upload_kbps"] . "k" . "\n";
						echo "Ascend-Data-Rate = " . ($conta["upload_kbps"]*1000) . '/' . ($conta["upload_kbps"]*1000) . "\n";
					}
					
				}
				
				$this->radius->registraEventoOK($this->username,"Login OK",$this->foneinfo);
				
				return(0);
				
			}

			/****************************************************
			 * ACCOUNTING                                       *
			 ****************************************************/			

			if( $this->acct ) {
				try {
			
					if ( $this->entrada && $this->saida ) throw new Exception("Accounting: Especificado operação de entrada e saída simultaneamente");
					if (!$this->entrada && !$this->saida) throw new Exception("Accounting: Operação de entrada ou saida não especificada");
					if (!$this->session ) throw new Exception("Accounting: Sessão não especificada");

					if( $this->entrada ) {
						$this->radius->acctStart($this->session,$this->username,$this->foneinfo, $this->nas, $this->ip_addr);
					} else if ($this->saida) {
						$this->radius->acctStop($this->session,$this->tempo,$this->terminate_cause,$this->bytes_in,$this->bytes_out);
					}


				} catch(Exception $e) {
					$origem = array();
					if( $this->nas ) $origem[] = $this->nas;
					if( $this->foneinfo ) $origem[] = $this->foneinfo;
				
					$this->registraEventoErro($this->username,$e->getMessage(),implode($this->nas,implode(",",$origem)));
					
					return(-1);
				}
			
			
			}
			
			return(0);

			
			
		}
		
		protected static function mac($mac){
			$mac = strtoupper($mac);
			$mac = str_replace("-",":",$mac);
			$el = explode(":",$mac);
			for($i=0;$i<count($el);$i++) {
				if( strlen($el[$i]) < 2 ) {
					$el[$i] = "0".$el[$i];
				}
			}
			$mac = implode(":",$el);

			return($mac);

		}
	
	
	}

?>
