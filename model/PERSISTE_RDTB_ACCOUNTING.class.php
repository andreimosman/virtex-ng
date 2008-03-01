<?

	/**
	 * rdtb_accounting
	 * Registros de ocorrências na autenticação (radius)
	 */
	class PERSISTE_RDTB_ACCOUNTING extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("session_id", "username", "login", "logout", "tempo", "caller_id", "nas", "framed_ip_address", "terminate_cause", "bytes_in", "bytes_out");
			$this->_chave           = "session_id";
			$this->_ordem           = "CASE WHEN logout IS NULL THEN login ELSE logout END DESC";
			$this->_tabela          = "rdtb_accounting";
			$this->_sequence        = "";
		}

	}


?>
