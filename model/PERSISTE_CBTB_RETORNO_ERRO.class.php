<?php

	/**
	 */
	class PERSISTE_CBTB_RETORNO_ERRO extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id_retorno_erro", "id_retorno", "id_cobranca","codigo_barra","mensagem");
			$this->_chave           = "id_retorno_erro";
			$this->_ordem           = "id_retorno_erro";
			$this->_tabela          = "cbtb_retorno_erro";
			$this->_sequence        = "cbsq_ìd_retorno_erro";
			
			$this->_filtro	        = array("id_retorno_erro" => "number", "id_retorno" => "number", "id_cobranca" => "number");
		}

	}
?>
