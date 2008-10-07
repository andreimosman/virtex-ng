<?


	class PERSISTE_LGTB_RENOVACAO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct();

			$this->_campos 		= array("id_cliente_produto","data_renovacao","data_proxima_renovacao","historico","id_renovacao");
			$this->_chave 		= "id_renovacao";
			$this->_ordem 		= "id_renovacao DESC";
			$this->_tabela 		= "lgtb_renovacao";
			$this->_sequence	= "lgtb_renovacao_id_renovacao_seq";
			$this->_filtros		= array("id_cliente_produto" => "number", "id_renovacao" => "number", 
										"data_renovacao"=> "date", "data_proxima_renovacao"=>"date" );

		}
		
	}



