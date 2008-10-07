<?php

	/**
	 */
	class PERSISTE_LGTB_RECUPERACAO_EMAIL extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id_recuperacao_email", "datahora", "id_admin","id_cliente","id_conta", "id_cliente_produto", "conta_mestre");
			$this->_chave           = "id_recuperacao_email";
			$this->_ordem           = "id_recuperacao_email";
			$this->_tabela          = "lgtb_recuperacao_email";
			$this->_sequence        = "lgsq_id_recuperacao_email";
			
			$this->_filtro	        = array("id_recuperacao_email" => "number", "datahora" => "date", "id_cliente" => "number", "id_conta" => "number", "id_cliente_produto" => "number");
		}

	}
