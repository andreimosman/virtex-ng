<?

	/**
	 * adtb_admin
	 * Cadastro de usuários do sistema
	 */
	class PERSISTE_EVTB_EVENTO extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id_evento", "datahora", "tipo", "id_admin", "natureza", "ipaddr", "confirmado_por_senha", "descricao","id_conta","id_cliente_produto","id_cobranca");
			$this->_chave           = "id_evento";
			$this->_ordem           = "datahora desc";
			$this->_tabela          = "evtb_evento";
			$this->_sequence        = "evsq_id_evento";
		}

	}

?>
