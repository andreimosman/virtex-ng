<?

	/**
	 * rdtb_reply
	 */
	class PERSISTE_RDTB_REPLY extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id", "UserName", "Attribute", "op", "Value");
			$this->_chave           = "id";
			$this->_ordem           = "id DESC";
			$this->_tabela          = "rdtb_reply";
			$this->_sequence        = "rdtb_reply_id_seq";
			$this->filtro			= array("id" => "numeric");
		}

	}
