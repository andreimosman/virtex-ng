<?

	class PERSISTE_NBTB_BACKUP extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_backup","datahora", "id_admin", "status");
			$this->_chave 		= "id_backup";
			$this->_ordem 		= "id_backup DESC";
			$this->_tabela		= "nbtb_backup";
			$this->_sequence	= "nbsq_id_backup";
			$this->_filtros		= array("id_backup" => "number", "id_admin" => "number");
		}
		
	}
		
