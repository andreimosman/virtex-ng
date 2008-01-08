<?
	class VCALogin extends VirtexControllerAdmin {
	
	
		public function __construct() {
			parent::__construct();
		}
		
		public function init() {
			parent::init();
			$this->_view = VirtexViewAdmin::factory("login");
		}
		
		public function executa() {
			parent::executa();
			
			$acao = @$_REQUEST["acao"];
			
			/**
			 * Retirar objeto da sessão.
			 */
			 
			$this->_login->init();
			
			/**
			 * Autenticação.
			 */
			
			if( $acao == "login" ) {
				// Fazer o login


				// Mensagem de Erro Padrão.
				$erro = "Usuário Inválido ou Senha Incorreta.";
			
				$username = trim(@$_REQUEST["username"]);
				$password = trim(@$_REQUEST["password"]);
				
				if( $username && $password ) {
					$admin = VirtexModelo::factory("administradores");
				
					$info = $admin->obtemAdminPeloUsername($username);
					
					if( count($info) && md5($password) == trim($info["senha"]) && $info["status"] == "A") {
						/**
						 * Registrar na sessão.
						 */
						 
						$this->_login->atribuiUsername($username);
						$this->_login->atribui("tipo","ADMIN");	// Indica que esta autenticação é do ambiente administrativo.
						
						$privilegios = $admin->obtemPrivilegiosUsuario($info["id_admin"]); // TODO: Pegar privilégios no sistema
						$this->_login->atribuiPrivilegios($privilegios);
						
						$this->_login->atribui("primeiroLogin",$info["primeiro_login"]);
						$this->_login->atribui("dados",$info);

						// Grava na sessão.
						$this->_login->persisteSessao();

						// Registra a entrada no sistema.
						$this->eventos->registraLoginOk($this->ipaddr,$info["id_admin"],($info["primeiro_login"] == 't' ? true : false));
						
						/**
						 * Redirecionar p/ site.
						 */						
						$pagina = "admin.php";
						if( $info["primeiro_login"] == "t" ) {
							// Se for o primeiro login do usuário redirecionar p/ alteração de senha.
							$pagina = "admin-administracao.php?op=altsenha";	// URL DE ALTERACAO DE SENHA;
						} 
						
						$this->_view->redirect($pagina);
						
						// Administrador Autenticado.
						return;
					}
				}
				
				$this->eventos->registraLoginErro($this->ipaddr,null,$username);

				$this->_view->atribui("erro",$erro);
				
			}
			
			
		}

	}


		
