<?

	/**
	 * adtb_admin
	 * Cadastro de usuários do sistema
	 */
	class PERSISTE_DOMINIO extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("dominio", "id_cliente", "provedor", "status", "dominio_provedor");
			$this->_chave           = "dominio";
			$this->_ordem           = "dominio";
			$this->_tabela          = "dominio";
			$this->_sequence        = "";
			
			$this->_filtros			= array("id_cliente" => "numeric", "provedor" => "bool", "dominio_provedor" => "bool");
		}

	}

?>
