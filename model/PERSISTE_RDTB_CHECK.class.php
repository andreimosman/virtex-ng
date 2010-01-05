<?

	/**
	 * rdtb_check
	 */
	class PERSISTE_RDTB_CHECK extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id", "UserName", "Attribute", "op", "Value");
			$this->_chave           = "id";
			$this->_ordem           = "id DESC";
			$this->_tabela          = "rdtb_check";
			$this->_sequence        = "rdtb_check_id_seq";
			$this->filtro			= array("id" => "numeric");
		}

	}
