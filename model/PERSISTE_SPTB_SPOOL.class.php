<?


	class PERSISTE_SPTB_SPOOL extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			
			$this->_campos		= array("id_spool","registro","execucao","destino", "tipo", "op","id","parametros","status","cod_erro");
			$this->_chave		= "id_spool";
			$this->_ordem		= "id_spool DESC";
			$this->_tabela		= "sptb_spool";
			$this->_sequence	= "spsq_id_spool";
			$this->_filtros		= array("id_spool" => "number", "registro" => "date", "execucao" => "date", "id" => "number", "status" => "custom", "cod_erro" => "number");
		
		}
	
	
	}




?>
