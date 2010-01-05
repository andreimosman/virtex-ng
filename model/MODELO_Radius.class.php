<?

	/**
	 * Modelo Radius
	 *
	 * - Autenticação
	 * - Accounting
	 * - Log de erros
	 */

	class MODELO_Radius extends VirtexModelo {
	
		public static $LOG_OK			= "OK";
		public static $LOG_ERRO			= "E";
		public static $LOG_INFO			= "I";
		public static $LOG_ALERTA		= "A";
		
		
		protected $rdtb_log;
		protected $rdtb_accounting;
		
		// FreeRadius
		protected $rdtb_check;
		protected $rdtb_reply;
	
		public function __construct() {
			parent::__construct();
			
			$this->rdtb_log = VirtexPersiste::factory("rdtb_log");
			$this->rdtb_accounting = VirtexPersiste::factory("rdtb_accounting");
			
			$this->rdtb_check = VirtexPersiste::factory("rdtb_check");
			$this->rdtb_reply = VirtexPersiste::factory("rdtb_reply");
		
		}
		
		
		/**
		 * Registra um evento genérico
		 */
		protected function registraEvento($tipo,$username,$mensagem,$caller) {
			$dados = array("tipo" => $tipo, "username" => $username, "mensagem" => $mensagem, "caller_id" => $caller);
			return($this->rdtb_log->insere($dados));
		}
		
		/**
		 * Registra um evento OK
		 */
		public function registraEventoOK($username,$mensagem,$caller) {
			return($this->registraEvento(self::$LOG_OK,$username,$mensagem,$caller));
		}

		/**
		 * Registra um evento de erro
		 */
		public function registraEventoErro($username,$mensagem,$caller) {
			return($this->registraEvento(self::$LOG_ERRO,$username,$mensagem,$caller));
		}

		/**
		 * Registra um evento informativo
		 */
		public function registraEventoInfo($username,$mensagem,$caller) {
			return($this->registraEvento(self::$LOG_INFO,$username,$mensagem,$caller));
		}

		/**
		 * Registra um evento alerta
		 */
		public function registraEventoAlerta($username,$mensagem,$caller) {
			return($this->registraEvento(self::$LOG_ALERTA,$username,$mensagem,$caller));
		}
		
		
		public function acctStart($session,$username,$caller,$nas,$framed_ip_address) {
			$dados = array("session_id" => $session, "username" => $username, "caller_id" => $caller, "nas" => $nas, "framed_ip_address" => $framed_ip_address);
			return($this->rdtb_accounting->insere($dados));
		}
		
		public function acctStop($session,$tempo,$terminate_cause,$bytes_in,$bytes_out) {
			$dados = array("tempo" => $tempo, "terminate_cause" => $terminate_cause, "bytes_in" => $bytes_in, "bytes_out" => $bytes_out, "logout" => "=now");
			$filtro = array("session_id" => $session);
			return($this->rdtb_accounting->altera($dados,$filtro));
		}
		
		
		
		
		
		/**********************************************************************
		 *                                                                    *
		 * FUNÇÕES DO FREERADIUS (PARA USO COM WPA)                           *
		 *                                                                    *
		 **********************************************************************/
		
		public function cadastraChaveWPA2($mac,$chave) {
			// Excluir registros anteriores
			$this->rdtb_check->exclui(array("UserName"=>$mac));
			$this->rdtb_reply->exclui(array("UserName"=>$mac));
		
			// Usuario = MAC / Senha = MAC
			$dados = array("UserName" => $mac, "Attribute" => "Password", "op" => "==", "Value" => $mac);
			$this->rdtb_check->insere($dados);
			
			// Resposta
			$dados = array("UserName" => $mac, "Attribute" => "Mikrotik-Wireless-PSK", "op" => "=", "Value" => $chave);
			$this->rdtb_reply->insere($dados);
		
		}
		
		
	
	}
	
