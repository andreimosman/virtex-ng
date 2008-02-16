<?

	class PERSISTE_HDTB_GRUPO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_grupo", "nome", "descricao", "ativo", "id_grupo_pai");
			$this->_chave 		= "id_grupo";
			$this->_ordem 		= "nome";
			$this->_tabela		= "hdtb_grupo";
			$this->_sequence	= "hdsq_id_grupo";	
			$this->_filtros		= array("ativo" => "boolean");
		}
		
	}
		
?>