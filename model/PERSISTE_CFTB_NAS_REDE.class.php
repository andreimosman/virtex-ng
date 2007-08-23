<?

	class PERSISTE_CFTB_NAS_REDE extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("rede", "id_nas");
			$this->_chave 		= "rede,id_nas";
			$this->_ordem 		= "rede";
			$this->_tabela		= "cftb_nas_rede";
			$this->_sequence	= "";
			$this->_filtros		= array("id_nas" => "number", "rede" => "cidr");
		}
		
		
	}
		
?>
