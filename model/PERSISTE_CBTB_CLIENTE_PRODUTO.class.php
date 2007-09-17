<?
	class PERSISTE_CBTB_CLIENTE_PRODUTO extends VirtexPersiste {
	
		public function __construct($bd=null) {
  		parent::__construct();
  		
			$this->_campos 		= array("id_cliente_produto", "id_cliente", "id_produto", "dominio", "excluido");
			$this->_chave 		= "id_cliente_produto";
			$this->_ordem		= "";
			$this->_tabela		= "cbtb_cliente_produto";
			$this->_sequence	= "cbsq_id_cliente_produto";
			$this->_filtros 	= array("id_cliente_produto" => "number", "id_cliente" => "number", "id_produto" => "number", "excluído" => "bool");
		}
	
	}
?>