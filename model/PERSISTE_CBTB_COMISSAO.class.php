<?

	class PERSISTE_CBTB_COMISSAO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_comissao", "id_cliente_produto", "id_admin", "valor", "status", "data_pagamento");
			$this->_chave 		= "id_comissao";
			$this->_ordem 		= "id_comissao";
			$this->_tabela		= "cbtb_comissao";
			$this->_sequence	= "cbsq_id_comissao";	
			$this->_filtros		= array("valor" => "number");
		}
		
	}
		
