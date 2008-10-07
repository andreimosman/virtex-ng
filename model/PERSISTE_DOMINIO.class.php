<?

	/**
	 * adtb_admin
	 * Cadastro de usu�rios do sistema
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
		 * Retorna uma lista de dom�nios.
		 * Caso seja informado o id do cliente o sistema retornar� os dom�nios do provedor + os dom�nios do cliente.
		 * Se $todos for true o sistema ignorar� o id do cliente e retornar� TODOS os dom�nios.
		 */
		public function obtemListaDominios($id_cliente=0,$todos=false) {
			$filtro = array();
			
			if( !$todos ) {
				if($id_cliente) {
					$sql = "select * from dominio where id_cliente = $id_cliente or provedor = true";					
				} else {
					// Retornar dom�nios do provedor
					$sql = "select * from dominio where provedor = true";
				}
				$sql = "select * from dominio";
			}
			return $this->bd->obtemRegistros($sql);			
		}
	
	}

