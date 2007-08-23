<?

	class PERSISTE_PFTB_PREFERENCIA_GERAL extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_provedor", "dominio_padrao", "nome","radius_server","hosp_server","hosp_ns1","hosp_ns2",
										"hosp_uid","hosp_gid","mail_server","mail_uid","mail_gid","pop_host","smtp_host","hosp_base",
										"agrupar","email_base");
			$this->_chave 		= "id_provedor";
			$this->_ordem 		= "";
			$this->_tabela		= "pftb_preferencia_geral";
			$this->_sequence	= "";
			$this->_filtros		= array("id_provedor" => "number", "radius_server" => "inet", "hosp_server" => "inet", "hosp_ns1" => "inet",
										"hosp_ns2" => "inet", "hosp_uid" => "number", "hosp_gid" => "number", "mail_server" => "inet",
										"mail_uid" => "number", "mail_gid" => "number", "agrupar" => "number");
		}
		
		
	}
		
?>
