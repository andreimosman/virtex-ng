<?

	class PERSISTE_CBTB_ENDERECO_COBRANCA extends VirtexPersiste {
		
		public function __construct($bd=null) {
			parent::__construct();
			
			$this->_campos		= array("id_endereco_cobranca","id_cliente_produto","endereco","complemento","bairro","id_cidade","cep","id_cliente", "id_condominio_cobranca", "id_bloco_cobranca");
			$this->_chave 		= "id_endereco_cobranca";
			$this->_ordem		= "id_endereco_cobranca DESC";
			$this->_tabela		= "cbtb_endereco_cobranca";
			$this->_sequence	= "cbsq_id_endereco_cobranca";
			
			$this->_filtros		= array("id_endereco_cobranca" => "numeric", "id_cliente_produto" => "numeric", "id_cidade" => "numeric", "id_cliente" => "numeric");
		
		}
	
	
	
	}
	
?>