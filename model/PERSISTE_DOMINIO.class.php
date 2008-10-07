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

		/**
		 * Retorna uma lista de domínios.
		 * Caso seja informado o id do cliente o sistema retornará os domínios do provedor + os domínios do cliente.
		 * Se $todos for true o sistema ignorará o id do cliente e retornará TODOS os domínios.
		 */
		public function obtemListaDominios($id_cliente=0,$todos=false) {
			$filtro = array();
			
			if( !$todos ) {
				if($id_cliente) {
					$sql = "select * from dominio where id_cliente = $id_cliente or provedor = true";					
				} else {
					// Retornar domínios do provedor
					$sql = "select * from dominio where provedor = true";
				}
				$sql = "select * from dominio";
			}
			return $this->bd->obtemRegistros($sql);			
		}
	
	}

