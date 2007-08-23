<?

	class PERSISTE_CFTB_UF extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("uf", "estado");
			$this->_chave 		= "uf";
			$this->_ordem 		= "uf";
			$this->_tabela		= "cftb_uf";
			$this->_sequence	= "";
		}
		
		
	}
		
?>
