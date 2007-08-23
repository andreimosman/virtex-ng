<?

	class PERSISTE_CFTB_BANDA extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("banda","id");
			$this->_chave 		= "id";
			$this->_ordem 		= "id";
			$this->_tabela		= "cftb_banda";
			$this->_sequence	= null;
		}
	}

?>
