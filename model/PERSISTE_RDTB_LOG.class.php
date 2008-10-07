<?

	/**
	 * rdtb_log
	 * Registros de ocorrências na autenticação (radius)
	 */
	class PERSISTE_RDTB_LOG extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id_log", "datahora", "tipo", "username", "mensagem", "caller_id");
			$this->_chave           = "id_log";
			$this->_ordem           = "id_log DESC";
			$this->_tabela          = "rdtb_log";
			$this->_sequence        = "rdtb_log_id_log";
			$this->filtro			= array("id_log" => "numeric");
		}

	}
