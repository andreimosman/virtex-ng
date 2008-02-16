<?

	/**
	 * Modelo de Helpdesk (camada de negócios)
	 *
	 * - Gerência de Grupos
	 * - Gerência de Administradores de grupos
	 */

	class MODELO_Helpdesk extends VirtexModelo {
	
		protected $hdtb_grupo;
		protected $hdtb_admin_grupo;
		
		public function __construct() {
			parent::__construct();
			$this->hdtb_grupo 						= VirtexPersiste::factory("hdtb_grupo");
			$this->hdtb_admin_grupo 				= VirtexPersiste::factory("hdtb_admin_grupo");
		}
		
	
		//Faz o cadastro de um novo grupo
		public function cadastraGrupo($nome, $descricao, $ativo='t', $id_grupo_pai="") {
			$dados = array("nome" => $nome, "descricao" => $descricao, "ativo" =>$ativo);
			$dados["id_grupo_pai"] = ($id_grupo_pai)?$id_grupo_pai:NULL;
			$this->hdtb_grupo->insere($dados);
		}
		
		
		//Faz a alteração de um grupo já cadastrado
		public function alteraGrupo($nome, $descricao, $ativo='t', $id_grupo_pai, $id_grupo) {
			$dados = array("nome" => $nome, "descricao" => $descricao, "ativo" =>$ativo, "id_grupo_pai" =>$id_grupo_pai);
			$dados["id_grupo_pai"] = ($id_grupo_pai)?$id_grupo_pai:NULL;
			$filtro = array("id_grupo" => $id_grupo);
			$this->hdtb_grupo->altera($dados, $filtro);
		}
		
		
		//Faz a listagem de grupos ordenada hierarquicamente
		protected function _obtemGrupos($parentId="",$nivel=0,$filtro=array()) {
			$filtro["id_grupo_pai"] = ($parentId?$parentId:"null:");

			$retorno = array();

			$registros = $this->hdtb_grupo->obtem($filtro);
			for($i=0;$i<count($registros);$i++) {
				$registros[$i]["nivel"] = $nivel;
				$retorno[] = $registros[$i];
				$child = $this->_obtemGrupos($registros[$i]["id_grupo"],$nivel+1);
				for($x=0;$x<count($child);$x++) {
					$retorno[] = $child[$x];
				}
			}
			return($retorno);	
		}
		
		//Faz a listagem de grupos
		public function obtemListaGrupos($status="",$parentId="") {
			$filtro = array();
			if( $status ) {
				$filtro["status"] = $status;
			}
			
			return($this->_obtemGrupos($parentId,0,$filtro));
		}
		
		
		//Faz a pesquisa de um determinadod grupo por ID
		public function obtemGrupoPeloId($id_grupo) {
			$filtro = array("id_grupo" => $id_grupo);
			return $this->hdtb_grupo->obtemUnico($filtro);
		}
		
		
		//Faz a pesquisa de todos os administradores nao pertencentes ao grupo escolhido
		public function obtemListaAdminForaGrupo($id_grupo) {		
			return $this->hdtb_admin_grupo->obtemListaAdminForaGrupo($id_grupo);
		}
		
		
		//faz o cadastro de um novo administrador em um determinado grupo
		public function cadastraUsuarioGrupo($id_grupo, $id_admin, $admin, $ativo) {
			$dados = array("id_grupo" => $id_grupo, "id_admin" => $id_admin, "nome" =>$nome, "admin" => $admin, "ativo" => $ativo);
			$retorno = $this->hdtb_admin_grupo->insere($dados);
			return $retorno;
		}
		
		
		//faz a alteração de um administrador em um determinado grupo
		public function alteraUsuarioGrupo($id_grupo, $id_admin, $nome, $admin, $ativo) {
			$dados = array("id_grupo" => $id_grupo, "nome" =>$nome, "admin" => $admin, "ativo" => $ativo);
			$filtro = array("id_admin" => $id_admin);
			$retorno = $this->hdtb_admin_grupo->altera($dados, $filtro);
			return $retorno;
		}


		//Retorna a listagem de administradores inclusos no grupo
		public function obtemListaAdminGrupo($id_grupo, $id_admin=NULL) {
			return $this->hdtb_admin_grupo->obtemListaAdminGrupo($id_grupo, $id_admin);
		}

	}

?>
