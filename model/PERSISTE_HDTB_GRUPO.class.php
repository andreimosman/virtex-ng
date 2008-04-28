<?

	class PERSISTE_HDTB_GRUPO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_grupo", "nome", "descricao", "ativo", "id_grupo_pai");
			$this->_chave 		= "id_grupo";
			$this->_ordem 		= "nome";
			$this->_tabela		= "hdtb_grupo";
			$this->_sequence	= "hdsq_id_grupo";	
			$this->_filtros		= array("ativo" => "boolean");
		}
		
		
		public function obtemListaGruposComPopulacao($somente_ativos=false) {
			$sql  = "SELECT grp.nome, grp.id_grupo, grp.descricao, grp.ativo, grp.id_grupo_pai, grc.usuarios ";
			$sql .= "FROM ";
			$sql .= "	hdtb_grupo grp LEFT OUTER JOIN ";
			$sql .= "	(SELECT id_grupo, count(*) as usuarios FROM hdtb_admin_grupo WHERE ativo=true GROUP BY id_grupo) grc ";
			$sql .= "	ON grp.id_grupo = grc.id_grupo ";
			
			if($somente_ativos) {
				$sql .= "WHERE grp.ativo = true ";
			}
			
			$sql .= "ORDER by nome ";
			
			return($this->bd->obtemRegistros($sql));			
		}

		
	}
		
?>