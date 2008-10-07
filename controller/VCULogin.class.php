<?


	class VCULogin extends VirtexControllerUsuario {
	
		public function __construct() {
			parent::__construct();
		}
		
		public function init() {
			parent::init();
			$this->_view = VirtexViewUsuario::factory("login");
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
					$contas = VirtexModelo::factory("contas");
					
					if(strstr($username,'@')) {
						list($username,$dominio) = explode('@',$useranme,2);
						$tipo = 'E';
					} else {
						$prefGeral = $this->preferencias->obtemPreferenciasGerais();
						$dominio = $prefGeral["dominio_padrao"];
						$tipo = '';
					}
					
					// Todo, verificar se retornou mais de um username p/ resolver conflito.
					$conta = $contas->obtemContaPeloUsername($username,$dominio,$tipo);
					
					if( @$conta["tipo_conta"] ) {
						// Retornou somente um registro.
						$conta = array($conta);
					}
					
					
					$loginOk = false;
					$dadosLogin = array();
					
					for( $i=0;$i<count($conta);$i++ ) {
						$sal = MCript::obtemSal($conta[$i]["senha_cript"]);
						$senhaCript = MCript::criptSenha($password,$sal);
						
						if( $senhaCript == $conta[$i]["senha_cript"] ) {
							$loginOk = true;
							$dadosLogin = $conta[$i];
							
							// Se o login passar como conta mestre dá o processamento da lista por encerrado.
							if( $dadosLogin["conta_mestre"] == 't' ) {
								break;
							}
						}
					
					}
					
					$mensagem = "";
					
					if( $loginOk ) {
						// Verificar o status da conta (somente restringe o acesso de conta CANCELADA)
						if( $dadosLogin["status"] == 'C' ) {
							$mensagem = "Conta cancelada.";
						} else {
						
							// Pegar os dados do cliente
							$clientes = VirtexModelo::factory("clientes");
							$dadosLogin["cliente"] = $clientes->obtemPeloId($dadosLogin["id_cliente"]);

							// Pegar os dados do contrato
							$cobranca = VirtexModelo::factory("cobranca");
							$dadosLogin["contrato"] = $cobranca->obtemContratoPeloId($dadosLogin["id_cliente_produto"]);

							$this->_login->atribuiUsername($username);
							$this->_login->atribui("tipo","USUARIO");	// Indica que esta autenticação é do ambiente do usuário.

							$this->_login->atribui("dados",$dadosLogin);
							$this->_login->persisteSessao();

							// TODO: Adicionar no modelo de eventos a autenticação dos clientes na interface de gerenciamento..
							
							// TODO: Aceite de contrato.

							$pagina = "index.php";
							$this->_view->redirect($pagina);
						
							return;
						}

					} else {
						$mensagem = "Usuário inválido ou senha incorreta.";
					}
					
					if( $mensagem ) {
						$this->_view->atribui("mensagem",$mensagem);
					}

				}
				
			}
			
		}		
	
	}

