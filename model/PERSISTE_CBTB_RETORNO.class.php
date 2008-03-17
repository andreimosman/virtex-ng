<?php

	/**
	 * adtb_admin
	 * Cadastro de usuários do sistema
	 */
	class PERSISTE_CBTB_RETORNO extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id_retorno", "data_hora", "formato","numero_total_registros","numero_registros_processados","id_admin");
			$this->_chave           = "id_retorno";
			$this->_ordem           = "data_hora";
			$this->_tabela          = "cbtb_retorno";
			$this->_sequence        = "cbsq_ìd_retorno";
			
			$this->_filtro	        = array("id_retorno" => "number", "numero_total_registros" => "number", "numero_registros_processados" => "number");			
		}

	}
?>