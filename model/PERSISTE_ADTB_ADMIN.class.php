<?

	/**
	 * adtb_admin
	 * Cadastro de usuários do sistema
	 */
	class PERSISTE_ADTB_ADMIN extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id_admin", "admin", "senha", "status", "nome", "email", "vendedor", "comissionado", "tipo_admin","primeiro_login");
			$this->_chave           = "id_admin";
			$this->_ordem           = "admin";
			$this->_tabela          = "adtb_admin";
			$this->_sequence        = "adsq_id_admin";
			$this->filtro			= array("vendedor" => "bool", "comissionado" => "bool");
		}

	}

?>
