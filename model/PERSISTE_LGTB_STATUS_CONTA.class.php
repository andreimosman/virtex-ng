<?

	class PERSISTE_LGTB_STATUS_CONTA extends VirtexPersiste {

		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_processo", "id_cliente_produto", "username", "dominio", "tipo_conta", "data_hora", "id_admin", "ip_admin", "operacao", "cod_operacao");
			$this->_chave 		= "id_processo";
			$this->_ordem 		= "id_processo DESC";
			$this->_tabela		= "lgtb_status_conta";
			$this->_sequence	= "lgsq_id_processo";
		}

	}

