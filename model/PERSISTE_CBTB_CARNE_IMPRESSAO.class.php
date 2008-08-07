<?

	class PERSISTE_CBTB_CARNE_IMPRESSAO extends VirtexPersiste {
	
		public function __construct() {
			parent::__construct();
			
			$this->_campos 		= array("id_impressao", "id_carne", "id_admin", "datahora", "codigo_verificacao");
			$this->_chave		= "id_impressao";
			$this->_tabela		= "cbtb_carne_impressao";
			$this->_ordem		= "id_carne";
			$this->_sequence	= "cbsq_id_impressao";
			$this->_filtros		= array("id_cliente" => "number", "id_cliente_produto" => "number", "valor" => "number","vigencia" => "number", "data_geracao"=>"date");
			
		}
				
	}
?>
