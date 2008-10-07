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
	
		public function __construct() {
			parent::__construct();
			
			$this->rdtb_log = VirtexPersiste::factory("rdtb_log");
			$this->rdtb_accounting = VirtexPersiste::factory("rdtb_accounting");
		
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
		
		
	
	}
	
