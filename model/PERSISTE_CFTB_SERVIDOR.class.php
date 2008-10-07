<?

	class PERSISTE_CFTB_SERVIDOR extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_servidor", "hostname", "ip", "porta", "chave","usuario","senha","disponivel");
			$this->_chave 		= "id_servidor";
			$this->_ordem 		= "hostname";
			$this->_tabela		= "cftb_servidor";
			$this->_sequence	= "cfsq_servidor_id_servidor";
			$this->_filtros		= array("id_servidor" => "number", "ip" => "inet", "porta" => "number", "disponivel" => "bool" );
		}
		
		
	}
		
