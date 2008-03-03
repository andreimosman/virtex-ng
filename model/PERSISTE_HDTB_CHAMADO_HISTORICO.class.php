<?


	class PERSISTE_HDTB_CHAMADO_HISTORICO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_historico", "id_chamado", "datahora", "titulo", "comentarios", "id_admin");
			$this->_chave 		= "id_historico";
			$this->_ordem 		= "datahora DESC";
			$this->_tabela		= "hdtb_chamado_historico";
			$this->_sequence	= "hdsq_id_historico";	
			//$this->_filtros		= array("ativo" => "boolean");
		}
	
	}
	
?>
