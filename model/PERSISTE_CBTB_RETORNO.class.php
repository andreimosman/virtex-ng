<?php

	/**
	 * adtb_admin
	 * Cadastro de usuários do sistema
	 */
	class PERSISTE_CBTB_RETORNO extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id_retorno", "datahora", "formato","numero_total_registros","numero_registros_processados", "numero_registros_com_erro", "numero_registros_sem_correspondencia", "id_admin", "data_geracao", "arquivo_enviado", "arquivo");
			$this->_chave           = "id_retorno";
			$this->_ordem           = "datahora DESC";
			$this->_tabela          = "cbtb_retorno";
			$this->_sequence        = "cbsq_ìd_retorno";
			
			$this->_filtro	        = array("id_retorno" => "number", "numero_total_registros" => "number", "numero_registros_processados" => "number");			
		}

	}
?>