<?

	class PERSISTE_PFTB_PREFERENCIA_MONITORACAO extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_provedor", "emails", "exibir_monitor","alerta_sonoro","celular","nro_celular","num_pings");
			$this->_chave 		= "id_provedor";
			$this->_ordem 		= "";
			$this->_tabela		= "pftb_preferencia_monitoracao";
			$this->_sequence	= "";
			$this->_filtros		= array("id_provedor" => "number", "exibir_monitor" => "bool", "alerta_sonoro" => "bool", "celular" => "bool", 
										"quota_por_conta" => "number", "vl_email_adicional" => "number", "permitir_outros_dominios" => "bool", 
										"num_pings" => "number");
		}
		
		
	}
		
