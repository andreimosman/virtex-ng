<?

	class PERSISTE_HDTB_ADMIN_GRUPO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array( "id_grupo", "id_admin", "admin", "ativo" );
			$this->_chave 		= "id_grupo";
			$this->_tabela		= "hdtb_admin_grupo";
			$this->_filtros		= array("ativo" => "boolean", "admin" => "boolean");
		}
		
	
		//Retorna a listagem de administradores que não estão inclusos no grupo
		public function obtemListaAdminForaGrupo($id_grupo) {

			$sql  = "SELECT ";
			$sql .= "	a.id_admin, a.nome ";
			$sql .= "FROM ";
			$sql .= "	adtb_admin a LEFT OUTER JOIN hdtb_admin_grupo ag ON (ag.id_admin = a.id_admin AND ag.id_grupo = $id_grupo ) ";
			$sql .= "WHERE ";
			$sql .="	ag.id_admin is null ";

			$retorno = $this->bd->obtemRegistros($sql);
			return $retorno;

		}
		
		//retrona a listagem de administradores que estão no grupo
		public function obtemListaAdminGrupo($id_grupo=NULL, $id_admin=NULL) {
		
			$sql  = "SELECT a.id_admin, a.nome as admnome, a.status as atvsis, ag.id_grupo, ag.ativo, ag.admin, gr.nome as nome ";
			$sql .= "FROM adtb_admin a INNER JOIN hdtb_admin_grupo ag ON (";
			$sql .= "												a.id_admin = ag.id_admin ";
			$sql .= $id_grupo ? "									AND ag.id_grupo = $id_grupo " : "";
			$sql .= ") INNER JOIN hdtb_grupo gr ON (ag.id_grupo = gr.id_grupo) ";
			
			if($id_admin) {
				$sql .= "WHERE a.id_admin = $id_admin ";
			}
			
			//echo $sql . "<br><br>";
			
			if($id_admin) {
				$retorno = $this->bd->obtemUnicoRegistro($sql);
			} else { 
				$retorno = $this->bd->obtemRegistros($sql);
			}
			return $retorno;
		}
		
		
		public function obtemListaGruposPertencentesAdmin($id_admin, $ativo=NULL) {
		
			$sql  = "SELECT ";
			$sql .= "	ag.id_grupo, ag.ativo, ag.admin, gr.nome as nome ";
			$sql .= "FROM hdtb_admin_grupo ag INNER JOIN hdtb_grupo gr ON (ag.id_grupo = gr.id_grupo) ";
			$sql .= "WHERE ag.id_admin = $id_admin ";
			
			if($ativo) {	
				$sql .= "AND gr.ativo = '$ativo' ";
			}
			
			$retorno = $this->bd->obtemRegistros($sql);
			return $retorno;
		}
		
	}
		
?>


 