<?

	class PERSISTE_CNTB_ENDERECO_INSTALACAO extends VirtexPersiste {
		
		public function __construct($bd=null) {
			parent::__construct();
			
			$this->_campos		= array("id_endereco_instalacao","id_conta","endereco","complemento","bairro","id_cidade","cep","id_cliente");
			$this->_chave 		= "id_endereco_instalacao";
			$this->_ordem		= "id_endereco_instalacao DESC";
			$this->_tabela		= "cntb_endereco_instalacao";
			$this->_sequence	= "cnsq_id_endereco_instalacao";
			
			$this->_filtros		= array("id_endereco_instalacao" => "numeric", "id_conta" => "numeric", "id_cidade" => "numeric", "id_cliente" => "numeric");
		
		}
	
	
	
	}
	
?>