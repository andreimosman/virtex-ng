<?

	class PERSISTE_STTB_POP_STATUS extends VirtexPersiste {

		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_pop","min_ping","max_ping","media_ping","num_perdas","num_erros","num_ping","laststats","status");
			$this->_chave 		= "id_pop";
			$this->_ordem 		= "id_pop";
			$this->_tabela		= "sttb_pop_status";
			$this->_sequence	= "";
			$this->_filtros		= array("id_pop" => "number","min_ping"=>"number","max_ping"=>"number","media_ping"=>"number",
										"num_perdas" => "number","num_erros"=> "number","num_ping"=>"number","laststats"=>"date");
		}
		

	}
		
