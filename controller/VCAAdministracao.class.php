<?


	class VCAAdministracao extends VirtexControllerAdmin {

		protected $produtos;
		protected $administradores;

		public function __construct() {
			parent::__construct();
		}
		
		protected function init() {
			parent::init();
			
			$this->_view 			= VirtexViewAdmin::factory("administracao");
			$this->produtos 		= VirtexModelo::factory("produtos");
			$this->administradores	= VirtexModelo::factory("administradores");
	
		}
		
		protected function executa() {
			switch($this->_op) {
				case 'altsenha':
					$this->executaAlteracaoSenha();
					break;
				case 'administradores':
					$this->executaAdministradores();
					break;
				case 'planos':
					$this->executaPlanos();
					break;
				case 'produtos':
					$this->executaProdutos();
					break;
				case 'ferramentas':
					$this->executaFerramentas();
					break;
				case 'relatorios':
					$this->executaRelatorios();
					break;
				default:
					// Do Something
			
			}
		}
		
		protected function executaAlteracaoSenha() {
			$this->_view->atribuiVisualizacao("altsenha");
			
			$dadosLogin = $this->_login->obtem("dados");
			$admin = VirtexModelo::factory("administradores");
			$info = $admin->obtemAdminPeloId($dadosLogin["id_admin"]);

			$acao = @$_REQUEST["acao"];			
			if( !$acao ) {				
				//echo "<pre>INFO: ";
				//print_r($info);
				//echo "</pre>";
				
				
			} else {
				// Faz a validação.
				$senha_atual = @$_REQUEST["senha_atual"];
				$nova_senha = @$_REQUEST["nova_senha"];
				$nova_senha_conf = @$_REQUEST["nova_senha_conf"];
				
				$erroMensagem = "";
				
				if( md5(trim($senha_atual)) != trim($info["senha"]) ) {
					$erroMensagem = "Senha atual não confere";
				} else {
					if( !$nova_senha || !$nova_senha_conf ) {
						$erroMensagem = "Todos os campos são obrigatórios.";
					} else {
						if( $nova_senha != $nova_senha_conf ) {
							$erroMensagem = "A senha e a confirmação não conferem.";
						}
					}
				}
				$this->_view->atribui("erroMensagem",$erroMensagem);
				
				if( !$erroMensagem ) {
					// Faz a alteração
					// $dados = array("senha" => $nova_senha, "primeiro_login" => "f");
					$admin->alteraAdmin($info["id_admin"],$info["admin"],$info["email"],$info["nome"],$nova_senha,$info["status"],"f");
					
					// Registrar as informações atualizadas na session
					$info = $admin->obtemAdminPeloId($info["id_admin"]);
					
					// Dados do login
					$this->_login->atribui("primeiroLogin",$info["primeiro_login"]);
					$this->_login->atribui("dados",$info);

					// Grava na sessão.
					$this->_login->persisteSessao();

					// Redirecionamento
					$url = "admin.php";
					$mensagem = "Senha alterada com sucesso.";
					$this->_view->atribui("url",$url);
					$this->_view->atribui("mensagem",$mensagem);
					$this->_view->atribui("target","_top");
					$this->_view->atribuiVisualizacao("msgredirect");
					
				}

			}
		
		}
		
		protected function executaAdministradores() {
			$this->_view->atribuiVisualizacao("administradores");
			$tela = @$_REQUEST["tela"] ? $_REQUEST["tela"] : "listagem";
			
			$id_admin = @$_REQUEST["id_admin"];
			$nome = @$_REQUEST["nome"];
			$senha = @$_REQUEST["senha"];
			$admin = @$_REQUEST["admin"];
			$status = @$_REQUEST["status"];
			$email = @$_REQUEST["email"];
			
			$this->_view->atribui("tela",$tela);
			$this->_view->atribui("id_admin", $id_admin);

			switch($tela) {
				case 'cadastro':
												
					if($id_admin) { //Alteração 
					
						if(!$this->_acao) {
						
							$info = $this->administradores->obtemAdminPeloId($id_admin);
							$this->_view->atribui("acao","cadastrar");
							
							while(list($vr,$vl) = each($info)){
								$this->_view->atribui($vr,$vl);					
							}
							
						} else {
							
							$this->administradores->alteraAdmin($id_admin, $admin, $email, $nome, $senha, $status);
							
							$url = "admin-administracao.php?op=administradores&tela=listagem";
							
							$mensagem = "Administrador alterado com sucesso";
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem",$mensagem);
							$this->_view->atribuiVisualizacao("msgredirect");
						}
						
					} else { //Cadastro
										
						if(!$this->_acao) {
							$this->_view->atribui("acao","cadastrar");
						} else {
						
							$url = "admin-administracao.php?op=administradores&tela=listagem";
							$mensagem = "Administrador cadastrado com sucesso";
							$erroMensagem="";
							
							$resultado = $this->administradores->obtemAdminPeloUsername($admin);
							if ($resultado) {
								$erroMensagem = "Já existe outro usuario cadastrado com este username.";
							}
							
							$resultado = $this->administradores->obtemAdminPeloEmail($email);
							if($resultado) {
								$erroMensagem = "Já existe outro usuário cadastrado com este email";
							}
							
							if(!$erroMensagem) {
								$this->administradores->cadastraAdmin($admin, $email, $nome, $senha, $status, TRUE);
								$this->_view->atribui("url",$url);
								$this->_view->atribui("mensagem",$mensagem);
								$this->_view->atribuiVisualizacao("msgredirect");
							} else {
								while(list($vr,$vl)=each(@$_REQUEST)) {
									$this->_view->atribui($vr,$vl);
								}
								
								$this->_view->atribui("erroMensagem",$erroMensagem);
								
							}
							
						}
						
					}
					
					
					break;
				case 'privilegio':	
					
					
					$acao = @$_REQUEST["acao"];
					$acesso = @$_REQUEST["acesso"];
					
					
					if("gravar" == $acao){
						$this->administradores->gravaPrivilegioUsuario($id_admin,$acesso);
						$this->_view->atribui("url","admin-administracao.php?op=administradores&tela=listagem");
						$this->_view->atribui("mensagem","Privilégios gravados com sucesso!");
						$this->_view->atribuiVisualizacao("msgredirect");		
					} else {
						$list = $this->administradores->obtemPrivilegios();
    					$this->_view->atribui("privilegios",$list);
    					
    					$list = $this->administradores->obtemAcessos();
    					$this->_view->atribui("acessos",$list);				
					}
										
				break;						
				case 'listagem':
					$this->_view->atribui("registros", $this->administradores->obtemListaAdmin());
					break;
				default:
					//Do something
			}
		}
		
		protected function executaPlanos() {	
			// Configuração do objeto de visualização		
			$this->_view->atribuiVisualizacao("planos");
			$tela = @$_REQUEST["tela"] ? $_REQUEST["tela"] : "listagem";
			$id_produto = @$_REQUEST["id_produto"];
			$tipo = @$_REQUEST["tipo"];

			$this->_view->atribui("tela",$tela);
			$this->_view->atribui("id_produto",$id_produto);
			$this->_view->atribui("tipo",$tipo);

			switch($tela) {
				case 'cadastro':
					$this->_view->atribui("lista_bandas",$this->preferencias->obtemListaBandas());
					
					if( $id_produto ) {
						
						if( $this->_acao ) {
							$info = $_REQUEST;
						} else {
							$info = $this->produtos->obtemPlanoPeloId($id_produto);
							$this->_view->atribui("acao","cadastrar");
						}
						
						$info["tipo"] = trim($info["tipo"]);
						while(list($vr,$vl) = each($info)){
							$this->_view->atribui($vr,$vl);
						}
					} else {
						if( !$this->_acao ) {
							$this->_view->atribui("acao","cadastrar");
						}
					}

					try {
						// echo "ACAO: " . $this->_acao;
						if( $this->_acao ) {
							// TODO: Tratar $dados
							$dados = $_REQUEST;

							if( $id_produto ) {
								// Alteração
								$this->produtos->alteraPlano($id_produto,$dados);
								$mensagem = "Plano alterado com sucesso";
							} else {
								// Cadastro
								$id_produto	= $this->produtos->cadastraPlano($dados);
								$mensagem 	= "Produto cadastrado com sucesso";
							}
							
							$url = "admin-administracao.php?op=planos&tela=listagem";
							
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem",$mensagem);
							$this->_view->atribuiVisualizacao("msgredirect");

						}

					} catch(ExcecaoModelo $e) {
						echo "EXCEPTION!!!!<br>\n";
						$this->_view->atribuiErro($e->obtemCodigo(),$e->obtemMensagem());
					}
					break;

				case 'listagem':
					$tipo = @$_REQUEST["tipo"];
					$disponivel = @$_REQUEST["disponivel"];
					if( !$disponivel ) {
						$disponivel='t';
					}
					
					$this->_view->atribui("tipo",$tipo);
					$this->_view->atribui("disponivel",$disponivel);
					
					$registros = $this->produtos->obtemListaPlanos($tipo,$disponivel);
					$this->_view->atribui("registros",$registros);					
					break;

				default:
					// Do something
			}

		}
		
		protected function executaProdutos() {
		}
		
		protected function executaFerramentas() {
		
		}
		
		protected function executaRelatorios() {
		
		}
		
	}




?>

