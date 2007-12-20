<?

	class PERSISTE_LGTB_BLOQUEIO_AUTOMATIZADO extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_processo", "id_cliente_produto", "data_hora", "tipo", "admin", "auto_obs", "admin_obs");
			$this->_chave 		= "id_processo";
			$this->_ordem 		= "id_processo DESC";
			$this->_tabela		= "lgtb_bloqueio_automatizado";
			$this->_sequence	= "lgsq_id_processo";
		}
		

	}
		
?>
