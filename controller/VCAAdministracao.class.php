<?


	class VCAAdministracao extends VirtexControllerAdmin {

		protected $produtos;
		protected $administradores;
		protected $contas;

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
				case 'bancodados':
					$this->executaBancoDados();
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
			$this->requirePrivLeitura("_ADMINISTRACAO_ADMINISTRADORES");
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

			$podeGravar = false;
			if( $this->requirePrivGravacao("_ADMINISTRACAO_ADMINISTRADORES", false) ) {
				$podeGravar = true;
			}

			switch($tela) {
				case 'cadastro':

					if($id_admin) { //Alteração

						if(!$this->_acao) {
							$this->_view->atribui("podeGravar",$podeGravar);

							$info = $this->administradores->obtemAdminPeloId($id_admin);
							$this->_view->atribui("acao","cadastrar");

							while(list($vr,$vl) = each($info)){
								$this->_view->atribui($vr,$vl);
							}

						} else {
							$this->requirePrivGravacao("_ADMINISTRACAO_ADMINISTRADORES");
							$this->administradores->alteraAdmin($id_admin, $admin, $email, $nome, $senha, $status);

							$url = "admin-administracao.php?op=administradores&tela=listagem";

							$mensagem = "Administrador alterado com sucesso";
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem",$mensagem);
							$this->_view->atribuiVisualizacao("msgredirect");
						}

					} else { //Cadastro
						$this->requirePrivGravacao("_ADMINISTRACAO_ADMINISTRADORES");
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


					$this->requirePrivLeitura("_ADMINISTRACAO_ADMINISTRADORES");


					$acao = @$_REQUEST["acao"];
					$acesso = @$_REQUEST["acesso"];

					$id_admin = @$_REQUEST["id_admin"];


					if($acao=="gravar"){
						$this->requirePrivGravacao("_ADMINISTRACAO_ADMINISTRADORES");
						$this->administradores->gravaPrivilegioUsuario($id_admin,$acesso);
						$this->_view->atribui("url","admin-administracao.php?op=administradores&tela=listagem");
						$this->_view->atribui("mensagem","Privilégios gravados com sucesso!");
						$this->_view->atribuiVisualizacao("msgredirect");
					} else {

						$admin = $this->administradores->obtemAdminPeloId($id_admin);
						$privilegios = $this->administradores->obtemPrivilegios();

						/*echo "<pre>";
						print_r($privilegios);
						echo "</pre>";*/

    					$privilegiosUsuario = $this->administradores->obtemPrivilegiosUsuario($id_admin);
    					$cachePriv = array();

    					for($i=0;$i<count($privilegiosUsuario);$i++) {
    						$cachePriv[ $privilegiosUsuario[$i]["id_priv"] ] = $privilegiosUsuario[$i]["pode_gravar"];
    					}

    					for($i=0;$i<count($privilegios);$i++) {
    						if( @$cachePriv[ $privilegios[$i]["id_priv"] ] ) {
    							$privilegios[$i]["selecao"] = $cachePriv[ $privilegios[$i]["id_priv"] ];
    						} else {
    							$privilegios[$i]["selecao"] = "0";
    						}
    					}

    					$this->_view->atribui("privilegios",$privilegios);


						$podeGravar = false;

						if( $this->requirePrivGravacao("_ADMINISTRACAO_ADMINISTRADORES", false) ) {
							$podeGravar = true;
						}

						$this->_view->atribui("podeGravar",$podeGravar);

    					$acessos = $this->administradores->obtemAcessos();

    					$this->_view->atribui("acessos",$acessos);

						//echo "<pre>";
						//print_r($privilegios);
						//print_r($acessos);
						//echo "</pre>";

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

							//Dados de taxa de instalação
							if(isset($dados["tx_instalacao"]))
								$dados["tx_instalacao"] = $dados["valor"];
							else
								$dados["tx_instalacao"] = 0;



							//Dados dos descontos promocionais
							if(!isset($dados["desconto"])) {
								$dados["desconto_promo"] = 0;
								$dados["periodo_desconto"] = 0;
							}



							//Comodato
							if(!isset($dados["comodato"])) {
								$dados["comodato"] = 'f';
								$dados["valor_comodato"] = '0';
							} else {
								$dados["comodato"] = 't';
							}



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
			$ferramenta = @$_REQUEST["ferramenta"];
			$this->_view->atribuiVisualizacao("ferramentas");

			$tela = @$_REQUEST["tela"];
			$this->_view->atribui("tela",$tela);

			switch($ferramenta) {
				case 'backup':
					/**
					 * Rotina de backup
					 */






					break;

			}
		}

		protected function executaRelatorios() {
			$this->_view->atribuiVisualizacao("relatorios");

			$relatorio = @$_REQUEST["relatorio"];

			$this->_view->atribui("relatorio",$relatorio);



			switch($relatorio) {

				case 'eventos':
					$this->executaRelatorioEventos();
					break;

			}


		}

		protected function executaRelatorioEventos() {

			$this->requirePrivLeitura("_ADMINISTRACAO_RELATORIOS");

			//
			$tipo = @$_REQUEST["tipo"];
			$id_admin = @$_REQUEST["id_admin"];
			$natureza = @$_REQUEST["natureza"];
			$id_conta = @$_REQUEST["id_conta"];

			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$limite = @$_REQUEST["limite"];



				$administradores_select= $this->administradores->obtemListaAdmin();
				$this->_view->atribui("administradores_select",$administradores_select);

				$this->_view->atribui("id_admin",$id_admin);

				$natureza_select= $this->eventos->obtemNatureza();
				$this->_view->atribui("natureza_select",$natureza_select);

				$this->_view->atribui("natureza",$natureza);

				$tipos_select= $this->eventos->obtemTipos();
				$this->_view->atribui("tipos_select",$tipos_select);

				$this->_view->atribui("tipo",$tipo);






			// TODO: PERÍODO.

			$filtro = array();

			// $tipo = "INFO";
			// $natureza = "LOGIN";
			// $natureza = "ALTERACAO CONTA";
			// $id_admin = 1;
			// $id_conta = 204;
			// $id_conta = 195;
			// $id_cliente_produto = 309;

			if( $tipo ) $filtro["tipo"] = $tipo;
			if( $natureza ) $filtro["natureza"] = $natureza;
			if( $id_admin ) $filtro["id_admin"] = $id_admin;
			if( $id_conta ) $filtro["id_conta"] = $id_conta;
			if( $id_cliente_produto ) $filtro["id_cliente_produto"] = $id_cliente_produto;
			if( $id_cobranca ) $filtro["id_cobranca"] = $id_cobranca;


			if( !$limite && !count($filtro)) $limite = 20;


			$eventos = $this->eventos->obtem($filtro,$limite);

			$this->_view->atribui("eventos",$eventos);
			$this->_view->atribui("limite",$limite);

			//echo "<pre>";
			//print_r($eventos);
			//echo "</pre>";



		}


		protected function executaBancoDados() {

			//$this->requirePrivLeitura("_ADMINISTRACAO_BANCODADOS");
			$this->_view->atribuiVisualizacao("bancodados");

			$eliminar = @$_REQUEST["eliminar"];
			$this->_view->atribui("eliminar", $eliminar);

			$id_conta = @$_REQUEST["id_conta"];
			$id_contrato = @$_REQUEST["id_contrato"];
			$id_cliente = @$_REQUEST["id_cliente"];

			if ($id_conta) {				//se houver um id de conta para ser eliminado
				$this->executaEliminarConta($id_conta);			
			} else if($id_cliente) {		//se houver um id de cliente para ser eliminado	
				$this->executaEliminarCliente($id_cliente);
			} else if($id_contrato) {		//se houver um id de contrato para ser eliminado
				$this->executaEliminarContrato($id_contrato);
			}


		}


		//Elimina uma conta do banco de dados
		protected function executaEliminarConta($id_eliminar="") {

			if ($id_eliminar) {
				$this->contas->eliminaConta($id_eliminar);
			}

		}

	}




?>
