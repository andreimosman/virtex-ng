<?


	class PERSISTE_LGTB_REAGENDAMENTO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct();

			$this->_campos 		= array("id_reagendamento","id_cliente_produto","data","admin","data_reagendamento","data_para_reagendamento");
			$this->_chave 		= "id_reagendamento";
			$this->_ordem 		= "";
			$this->_tabela 		= "lgtb_reagendamento";
			$this->_sequence	= "lgtb_reagendamento_id_reagendamento_seq";
			$this->_filtros		= array("id_reagendamento" => "number", "id_cliente_produto" => "number", 
										"data"=>"date", "admin"=>"number", "data_reagendamento"=>"date", 
										"data_para_reagendamento" => "date" );

		}
	
	
	}



?>
