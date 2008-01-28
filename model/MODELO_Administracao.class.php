<?

	class MODELO_Administracao extends VirtexModelo {

		protected $adtb_admin;
		protected $adtb_privilegio;
		protected $adtb_usuario_privilegio;

		protected $cntb_conta;

		public function __construct() {
			parent::__construct();
			$this->adtb_admin = VirtexPersiste::factory("adtb_admin");
			$this->adtb_privilegio = VirtexPersiste::factory("adtb_privilegio");
			$this->adtb_usuario_privilegio = VirtexPersiste::factory("adtb_usuario_privilegio");
			$this->cntb_conta = VirtexPersiste::factory("cntb_conta");
		}

		public function obtemListaAdmin() {
			return ($this->adtb_admin->obtem());
		}

		public function obtemAdminPeloId($id) {
			return ($this->adtb_admin->obtemUnico(array("id_admin"=>$id)));
		}

		public function obtemAdminPeloEmail($email) {
			return ($this->adtb_admin->obtemUnico(array("email"=>$email)));
		}

		public function obtemAdminPeloUsername($username) {
			return ($this->adtb_admin->obtemUnico(array("admin"=>$username)));
		}

		public function cadastraAdmin($admin, $email, $nome, $senha, $status, $primeiro_login) {
			$dados = array("admin"=>trim($admin), "email"=>trim($email), "nome"=>trim($nome), "status"=>trim($status), "senha"=>md5(trim($senha)), "primeiro_login"=>$primeiro_login);

			return ($this->adtb_admin->insere($dados));
		}

		public function alteraAdmin($id_admin, $admin, $email, $nome, $senha, $status,$primeiro_login="") {
			$filtro = array("id_admin" => trim($id_admin));
			$dados = array("email"=>trim($email), "admin"=>trim($admin), "nome"=>trim($nome), "status" => trim($status));

			if($senha) {
				$dados["senha"] = md5(trim($senha));
				$dados["primeiro_login"] = $primeiro_login ? $primeiro_login : "t";
			}

			return ($this->adtb_admin->altera($dados, $filtro));
		}

		public function obtemPrivilegios(){
			return $this->adtb_privilegio->obtemPrivilegios();
		}

		public function obtemPrivilegiosUsuario($id_admin) {
			return($this->adtb_usuario_privilegio->obtemPrivilegiosUsuario($id_admin));
		}


		public function obtemAcessos(){
			return $this->adtb_privilegio->enumAcessos();
		}


		public function gravaPrivilegioUsuario($id_admin, $dados){
				$this->adtb_usuario_privilegio->apagaPrivilegiosUsuario($id_admin, $dados);
				$this->adtb_usuario_privilegio->gravaPrivilegiosUsuario($id_admin, $dados);
		}

	}



