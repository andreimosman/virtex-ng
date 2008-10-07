<?

	class PERSISTE_PFTB_PREFERENCIA_PROVEDOR extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_provedor", "endereco", "localidade", "cep", "cnpj", "fone");
			$this->_chave 		= "id_provedor";
			$this->_ordem 		= "";
			$this->_tabela		= "pftb_preferencia_provedor";
			$this->_sequence	= "";
			$this->_filtros		= array("id_provedor" => "number");
		}
		
		
	}
		
