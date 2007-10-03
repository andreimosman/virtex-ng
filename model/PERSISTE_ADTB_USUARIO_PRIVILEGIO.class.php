<?

	class PERSISTE_ADTB_USUARIO_PRIVILEGIO extends VirtexPersiste {

		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id_admin", "id_priv", "pode_gravar");
			$this->_chave           = "id_admin,id_priv";
			$this->_ordem           = "";
			$this->_tabela          = "adtb_usuario_privilegio";
			$this->_sequence        = "";
		}

		public function obtemPrivilegiosUsuario($id_admin) {
			$where = "WHERE ".$this->_tabela.".id_admin = '" . $this->bd->escape($id_admin) ."'";
			$sql = "SELECT " . implode(",",$this->obtemCamposComTabela()) . ", p.cod_priv, p.nome FROM " . $this->_tabela . " INNER JOIN adtb_privilegio p USING (id_priv) " . $where;

			return($this->bd->obtemRegistros($sql));

		}
		
		
		public function apagaPrivilegiosUsuario($id_admin){			
			$where["id_admin"] = $id_admin;						
			$this->exclui($where);			
		}
		
		public function gravaPrivilegiosUsuario($id_admin, $dados){					
			$where["id_admin"] = $id_admin;						
			foreach($dados as $id_priv => $acesso){						
				if($acesso != "0"){										
					$where["id_priv"] = $id_priv;
					$where["pode_gravar"] = $acesso;											
					$this->insere($where);
				}
			}				
		}

	}

?>
