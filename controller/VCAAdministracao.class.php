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
					break;
				case 'preferencias':
					$this->executaPreferencias();
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
					$admin->alteraAdmin($info["id_admin"],$info["admin"],$info["email"],$info["nome"],$nova_senha,$info["status"], $info["$vendedor"], $info["$comissionado"], $info["$tipo_admin"], "f");

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

		protected function executaProdutos() {
		}

		protected function executaFerramentas() {
			$ferramenta = @$_REQUEST["ferramenta"];
			$this->_view->atribuiVisualizacao("ferramentas");
			
			
			if( !$ferramenta ) {
				$ferramenta = "backup";
			}
			
			$this->_view->atribui("ferramenta",$ferramenta);

			$tela = @$_REQUEST["tela"];
			$this->_view->atribui("tela",$tela);


			switch($ferramenta) {
				case 'backup':
					/**
					 * Rotina de backup
					 */
					$this->executaFerramentaBackup();
					




					break;

			}
		}
		
		protected function executaFerramentaBackup() {
			$this->requirePrivGravacao("_ADMINISTRACAO_FERRAMENTAS_BACKUP");

			$backup = VirtexModelo::factory("backup");
			
			$tela = $this->_view->obtem("tela");
			if( !$tela ) {
				$tela = "inicio";
			}
			
			$this->_view->atribui("tela",$tela);
			
			if( $tela == "inicio" || $tela == "historico" ) {
				$limite = ($tela == "historico"?0:5);
				$backups = $backup->obtemBackups($limite);
				$this->_view->atribui("backups", $backups);
			} else if( $tela == "fazer_backup" ) {
				$submit = @$_REQUEST["submit"];
				$opBackup = @$_REQUEST["backup"];

				$dadosLogin = $this->_login->obtem("dados");

				if( $submit && is_array($opBackup) ) {
					// echo "GERAR BACKUP!!!";
					// echo "PHP: " . ;
					
					$param = "-R " . $dadosLogin["id_admin"];
					
					if( @$opBackup["D"] ) {
						$param .= " -D";
					}
					
					if( @$opBackup["E"] ) {
						$param .= " -E";
					}

					if( @$opBackup["C"] ) {
						$param .= " -C";
					}
										
					$comando = $this->SO->obtemPHP() . "  " . getcwd() . "/bin/vtx-backup.php " . trim($param);
					$this->SO->executa($comando);
					
					$url = "admin-administracao.php?op=ferramentas&ferramenta=backup&tela=inicio";
					$mensagem = "Backup realizado com sucesso.";
					$this->_view->atribui("url",$url);
					$this->_view->atribui("mensagem",$mensagem);
					$this->_view->atribui("target","_self");
					$this->_view->atribuiVisualizacao("msgredirect");
					return;
				}
				
				//echo "<pre>";
				//print_r($opBackup);
				//echo "</pre>";
				
			
			} else if( $tela == "download" ) {
				// echo "DOWNLOAD";
				$id_arquivo = @$_REQUEST["id_arquivo"];
				if( $id_arquivo ) {
					$arquivo = $backup->obtemArquivo($id_arquivo);
					
					$this->criaDownload($arquivo["path"], $arquivo["nome"]);
					
				}
				
				return;
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

			$this->requirePrivLeitura("_ADMINISTRACAO_BANCODADOS");
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
		
		//----------------------------------------------------------
		// PREFERENCIAS
		//----------------------------------------------------------
		

		protected function executaPreferencias() {
			$tela = @$_REQUEST["tela"];
			$this->_view->atribui("tela",$tela);
			$this->_view->atribuiVisualizacao("preferencias");
			
			if($tela != "registro") {
				$this->requirePrivLeitura("_ADMINISTRACAO_PREFERENCIAS");
				if( $this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS",false)  ) {
					$this->_view->atribui("podeGravar",true);
				} else {
					$this->_view->atribui("podeGravar",false);
				}
			}
			
			switch($tela) {
				case 'geral':
					$this->executaPreferenciasGeral();
					break;
				case 'provedor':
					$this->executaPreferenciasProvedor();
					break;
				case 'cobranca':
					$this->executaPreferenciasCobranca();
					break;
				case 'cidades':
					$this->executaPreferenciasCidades();
					break;
				case 'banda':
					$this->executaPreferenciasBanda();
					break;
				case 'monitoramento':
					$this->executaPreferenciasMonitoramento();
					break;
				case 'links':
					$this->executaPreferenciasLinks();
					break;
				case 'modelos':
					$this->executaPreferenciasModeloContrato();
					break;
				case 'registro':
					$this->requirePrivLeitura("_ADMINISTRACAO_PREFERENCIAS_REGISTRO");
					$this->executaPreferenciasRegistro();
					break;
				case 'resumo':
					$this->executaPreferenciasResumo();
					break;
				case 'helpdesk':
					$this->executaPreferenciasHelpdesk();
					break;
				default:
					// Do Something
					// $this->executaValidacaoPreferencias();
					break;
				
			}
			
		}
				
		protected function executaPreferenciasResumo() {
			$acao = @$_REQUEST["acao"];
			
			$subtela = @$_REQUEST["subtela"];
			$this->_view->atribui("subtela",$subtela);
			
			$geral = $this->preferencias->obtemPreferenciasGerais();
			$this->_view->atribui("geral",$geral);
			
			$provedor = $this->preferencias->obtemPreferenciasProvedor();
			$this->_view->atribui("provedor",$provedor);
			
			$tipos_forma = $this->preferencias->obtemTiposFormaPagamento();			
			$this->_view->atribui("tipos_forma",$tipos_forma);
			
			$bancos = $this->preferencias->obtemListaBancos();
			$this->_view->atribui("bancos",$bancos);
			
			$cobranca 	= $this->preferencias->obtemPreferenciasCobranca();
			$this->_view->atribui("cobranca",$cobranca);
			
			
			$tipos 	= $this->preferencias->obtemTiposPagamento();
			$this->_view->atribui("tipos",$tipos);
			
			$formas = $this->preferencias->obtemFormasPagamento();
			$this->_view->atribui("formas",$formas);
			
			//die("<pre>".print_r($formas,true)."<pre>");

			$url = "admin-administracao.php?op=preferencias&tela=resumo";
		}
		
		protected function executaPreferenciasRegistro() {
			$acao = @$_REQUEST["acao"];
			
			$subtela = @$_REQUEST["subtela"];
			$this->_view->atribui("subtela",$subtela);

			$url = "admin-administracao.php?op=preferencias&tela=registro";
			
			if( $this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS_REGISTRO",false) ) {
				$this->_view->atribui("podeGravar",true);
			} else {
				$this->_view->atribui("podeGravar",false);
			}
			
			
			if ($acao == "upload"){
				$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS_REGISTRO");
				$diretorio = "./etc";
				$nome_aceitavel = "virtex.lic";
				$file = $_FILES["arquivo_registro"];
				$arquivo = $diretorio."/".$nome_aceitavel;
				
				if(is_uploaded_file($file["tmp_name"]) ){
					if( filesize($file["tmp_name"]) > 0 ) {
						if (file_exists($arquivo)) {							
							rename($arquivo, $diretorio."/_virtex.lic");
						}
						if(move_uploaded_file($file["tmp_name"],$arquivo ) ){
							$mensagem = "Arquivo de registro aplicado com sucesso!<br><br>Refaça o login no sistema.";
							$url = "admin-login.php";
						} else {
							$mensagem = "Falha ao tratar o arquivo";
						}
					} else {
						$mensagem = "Arquivo Inválido.";
					}
				} else {
					$mensagem = "Falha no upload do arquivo";
				}
				
				
				$this->_view->atribui("mensagem",$mensagem);
				$this->_view->atribui("url",$url);
				$this->_view->atribui("target","_top");
				$this->_view->atribuiVisualizacao("msgredirect");
				
			} else {
			
				if($this->licenca->isValid()){
					$status = "ativo";
					if($this->licenca->expirou()){
						$status = "expirado";
					}elseif ($this->licenca->congelou()){
						$status = "congelado";
					}
					
					$this->_view->atribui("licenca",$this->licenca->obtemLicenca());
					$this->_view->atribui("status",$status);
					$this->_view->atribui("registrado",true);
					
					
				} else {
					$this->_view->atribui("registrado",false);
				}
				
				$this->_view->atribui("infoLocalId",$this->licenca->obtemInfoLocalId());
			}
	
			
			/*if( !$subtela ) {
				//$modelos = $this->preferencias->obtemListaModelosContrato();
				//$this->_view->atribui("registros",$modelos);
			} else if( $subtela == "exibir_registro") {
				
			}*/
			
		}
		
		protected function executaPreferenciasModeloContrato() {
			$acao = @$_REQUEST["acao"];
			
			$path_upload_modelo = "var/contrato";
			
			$subtela = @$_REQUEST["subtela"];
			$this->_view->atribui("subtela",$subtela);

			$url = "admin-administracao.php?op=preferencias&tela=modelos";
			$id_modelo_contrato = @$_REQUEST["id_modelo_contrato"];
			
			$this->_view->atribui("tipos",$this->preferencias->obtemTiposContrato());
			if( !$subtela ) {
				$modelos = $this->preferencias->obtemListaModelosContrato();
				$this->_view->atribui("registros",$modelos);
			} else if( $subtela == "exibir_modelo") {
				// echo "EXIBIR MODELO: " . ;
				if( $id_modelo_contrato ) {
					$arqModelo = $path_upload_modelo ."/". str_pad($id_modelo_contrato,5,"0",STR_PAD_LEFT);
					$fd = @fopen($arqModelo,"r");
					if( !$fd ) {
						// ERRO
						echo "<font face='verdana' size=-1 color=red><b>Não foi possível acessar o modelo. Contate o suporte tecnico.</b></font>";
					} else {
						echo fread($fd,filesize($arqModelo));
						fclose($fd);
					}
				}
			
			} else {
				if( !$acao && $id_modelo_contrato ) {
					$info = $this->preferencias->obtemModeloContrato($id_modelo_contrato);
					while(list($vr,$vl)=each($info)) {
						$this->_view->atribui($vr,$vl);
					}
				} else {
					$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");

					if( $acao ) {
						if( $id_modelo_contrato ) {
							// Alterar.
							$this->preferencias->atualizaModeloContrato(@$_REQUEST["id_modelo_contrato"],@$_REQUEST["tipo"],@$_REQUEST["descricao"],@$_REQUEST["padrao"],@$_REQUEST["disponivel"]);
							
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem","Modelo de Contrato atualizado com sucesso.");
							$this->_view->atribuiVisualizacao("msgredirect");
													
						} else {

							$tmp = @explode(".",@$_FILES["arquivo"]["name"]);
							$ext = strtolower(@$tmp[ count($tmp) - 1 ]);
							
							$erro = "";
							
							if( $ext != "htm" && $ext != "html" ) {
								$erro = "Arquivo inválido. Extensão do arquivo não é HTML ou HTM.";
							} else {
							
								$arquivoTemporario = $_FILES["arquivo"]["tmp_name"];

								$tmp = explode("/",$arquivoTemporario);
								$arq = array_pop($tmp);
								$tmpPath = implode("/",$tmp);

								$tmp = explode("/",$_SERVER["SCRIPT_NAME"]);
								array_pop($tmp);
								$scriptAlvo = "http://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . implode("/",$tmp) . "/smarty-test.php?tmp=" . base64_encode($arq);

								$fd = fopen($scriptAlvo,"r");
								$resposta="";
								while(!feof($fd)) {
									$resposta .= fgets($fd,1024);
								}
								fclose($fd);

								if( !$resposta ) {
									$erro = "O arquivo enviado não é um template de contrato válido.";
								} else {
									// Arquivo válido. Copiar, inserir, etc.
									
									$this->preferencias->begin();
									
									$id_modelo_contrato = $this->preferencias->cadastraModeloContrato(@$_REQUEST["tipo"],@$_REQUEST["descricao"],@$_REQUEST["padrao"],@$_REQUEST["disponivel"]);
									
									if( @move_uploaded_file($_FILES['arquivo']['tmp_name'], $path_upload_modelo ."/". str_pad($id_modelo_contrato,5,"0",STR_PAD_LEFT))) {
										$this->_view->atribui("url",$url);
										$this->_view->atribui("mensagem","Modelo de Contrato cadastrado com sucesso.");
										$this->_view->atribuiVisualizacao("msgredirect");

										$this->preferencias->commit();
									} else {
										$erro = "Ocorreu um erro na gravação do modelo. Favor acionar o suporte técnico. A informação NÃO foi gravada.";
										$this->preferencias->rollback();
									}

								}
							}
							
							if( $erro ) {
								$this->_view->atribui("erro",$erro);
								while(list($vr,$vl)=each($_REQUEST)) {
									$this->_view->atribui($vr,$vl);
								}
							}
						}
					}
				}
			}
		}
		
		protected function executaPreferenciasGeral() {
			$acao = @$_REQUEST["acao"];
			
			if( !$acao ) {
				$info = $this->preferencias->obtemPreferenciasGerais();

				while(list($vr,$vl)=each($info)) {
					$this->_view->atribui($vr,$vl);
				}
			} else {
				$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");
				
				/**
				 * Processar alterações
				 */
				
				$this->preferencias->atualizaPreferenciasGerais(
																	@$_REQUEST["dominio_padrao"],
																	@$_REQUEST["nome"],
																	@$_REQUEST["radius_server"],
																	@$_REQUEST["hosp_server"],
																	@$_REQUEST["hosp_ns1"],
																	@$_REQUEST["hosp_ns2"],
																	@$_REQUEST["hosp_uid"],
																	@$_REQUEST["hosp_gid"],
																	@$_REQUEST["hosp_base"],
																	@$_REQUEST["mail_server"],
																	@$_REQUEST["mail_uid"],
																	@$_REQUEST["mail_gid"],
																	@$_REQUEST["email_base"],
																	@$_REQUEST["pop_host"],
																	@$_REQUEST["smtp_host"],
																	@$_REQUEST["agrupar"]
																);

				$this->_view->atribui("url","admin-administracao.php?op=preferencias&tela=geral");				
				$this->_view->atribui("mensagem","Preferências gerais atualizadas com sucesso.");
				$this->_view->atribuiVisualizacao("msgredirect");
			
			}
		}
		
		protected function executaPreferenciasProvedor() {
			$acao = @$_REQUEST["acao"];
			if(!$acao) {
				$info = $this->preferencias->obtemPreferenciasProvedor();
				
				while(list($vr,$vl)=each($info)) {
					$this->_view->atribui($vr,$vl);
				}
			} else {
				$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");
				$this->preferencias->atualizaPreferenciasProvedor(
																	@$_REQUEST["endereco"],
																	@$_REQUEST["localidade"],
																	@$_REQUEST["cep"],
																	@$_REQUEST["cnpj"],
																	@$_REQUEST["fone"]
																);

				$this->_view->atribui("url","admin-administracao.php?op=preferencias&tela=provedor");				
				$this->_view->atribui("mensagem","Preferências do provedor atualizadas com sucesso.");
				$this->_view->atribuiVisualizacao("msgredirect");
				
			}
		}
		
		protected function executaPreferenciasCobranca() {
			$acao = @$_REQUEST["acao"];

			$subtela = @$_REQUEST["subtela"];
			$this->_view->atribui("subtela",$subtela);

			$tipos_forma = $this->preferencias->obtemTiposFormaPagamento();
			$this->_view->atribui("tipos_forma",$tipos_forma);
			
			$bancos = $this->preferencias->obtemListaBancos();
			$this->_view->atribui("bancos",$bancos);
			
			$url = "admin-administracao.php?op=preferencias&tela=cobranca";			
			$this->_view->atribui("url",$url);
			if( !$subtela ) {
				if( !$acao || $acao == "editar" ) {
					$info 	= $this->preferencias->obtemPreferenciasCobranca();
					while(list($vr,$vl)=each($info)) {
						$this->_view->atribui($vr,$vl);
					}
					$tipos 	= $this->preferencias->obtemTiposPagamento();
					$this->_view->atribui("tipos",$tipos);

					if( !$acao ) {
						// Lista de formas de pagamento
						// echo "LISTA FORMAS<br>\n";
						$formas = $this->preferencias->obtemFormasPagamento();
						
						for($i=0; $i<count($formas); $i++) {
							if($formas[$i]["id_forma_pagamento"] == "9999") {
								unset($formas[$i]);
							}
						}
						
						$this->_view->atribui("formas",$formas);						
						//echo "<pre>";
						//print_r($formas);
						//echo "</pre>";
						
					}
				}
				if( $acao == "alterar" ) {
					// Rotina de alteração.
					// echo "Alterar!!!<br>\n";
					$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");
					
					$tx_juros = @$_REQUEST["tx_juros"];
					$multa = @$_REQUEST["multa"];
					$dia_venc = @$_REQUEST["dia_venc"];
					$pagamento = @$_REQUEST["pagamento"];
					$carencia = @$_REQUEST["carencia"];
					$path_contrato = @$_REQUEST["path_contrato"];
					$observacoes = @$_REQUEST["observacoes"];
					$enviar_email = @$_REQUEST["enviar_email"];
					$email_remetente = @$_REQUEST["email_remetente"];
					$mensagem_email = @$_REQUEST["mensagem_email"];
					$dias_minimo_cobranca = @$_REQUEST["dias_minimo_cobranca"];
					
					$this->preferencias->atualizaPreferenciasCobranca($tx_juros,$multa,$dia_venc,$pagamento,$carencia,$path_contrato,$observacoes,$enviar_email,$email_remetente,$mensagem_email,$dias_minimo_cobranca);
					$this->_view->atribui("mensagem","Preferências atualizadas com sucesso.");
					$this->_view->atribuiVisualizacao("msgredirect");

				}
			} else {
				// FORMA DE PAGAMENTO
				
				$id_forma_pagamento = @$_REQUEST["id_forma_pagamento"];
				$this->_view->atribui("id_forma_pagamento",$id_forma_pagamento);


				
				if( $id_forma_pagamento && !$acao ) {
					$info = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);					
					while(list($vr,$vl)=each($info)) {
						$this->_view->atribui($vr,$vl);
					}
				}
				
				if( !$id_forma_pagamento ) {
					$this->_view->atribui("nossonumero_inicial", 1);
					$this->_view->atribui("nossonumero_final", 10000000);
					$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");
				}
				
				if( $acao ) {
					$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");
					
					if( $id_forma_pagamento ) {
						// Alteração
						$disponivel = @$_REQUEST["disponivel"];
						$nossonumero_inicial = @$_REQUEST["nossonumero_inicial"];
						$nossonumero_final = @$_REQUEST["nossonumero_final"];
						$this->preferencias->atualizaFormaPagamento($id_forma_pagamento,$disponivel,$nossonumero_inicial,$nossonumero_final);
						$mensagem = "Disponibilidade da forma de pagamento alterada com sucesso.";
					} else {
						// Cadastro
						$this->preferencias->cadastraFormaPagamento(@$_REQUEST);
						$mensagem = "Forma de pagamento cadastrada com sucesso.";
						
					}
					$this->_view->atribui("mensagem",$mensagem);					
					$this->_view->atribuiVisualizacao("msgredirect");

				}

			}
			
			
			
			//echo "<pre>";
			//print_r($info);
			//print_r($tipos);
			//print_r($tipos_forma);
			//echo "</pre>";
		}
		
		
		
		
		
		protected function executaPreferenciasCidades() {
		
			$acao = @$_REQUEST["acao"];
			
			if( $acao == "atualiza" ) {
				$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");
				// Executa a atualização
				$disponivel = @$_REQUEST["disponivel"];
				
				if( $disponivel && count($disponivel) ) {
					while(list($id,$disp) = each($disponivel)) {
						$this->preferencias->atualizaDisponibilidadeCidade($id,$disp);
					}
				}

				$this->_view->atribui("mensagem","Disponibilidade atualizada com sucesso.");
				$this->_view->atribui("url","admin-administracao.php?op=preferencias&tela=cidades");
				$this->_view->atribuiVisualizacao("msgredirect");

				
			} else {
		
				$texto_pesquisa = @$_REQUEST["texto_pesquisa"];
				$this->_view->atribui("texto_pesquisa",$texto_pesquisa);

				$uf = @$_REQUEST["uf"];
				$this->_view->atribui("uf",$uf);

				$this->_view->atribui("lista_uf",$this->preferencias->obtemListaUF());

				if( $texto_pesquisa ) {
					$registros = $this->preferencias->pesquisaCidadesPeloNome($texto_pesquisa);
				} else {

					if( $uf ) {
						$registros = $this->preferencias->obtemListaCidadesPorUF($uf);
					} else {
						$registros = $this->preferencias->obtemListaCidadesDisponiveis();
					}
				}

				$this->_view->atribui("registros",$registros);
			}
			
		}
		
		protected function executaPreferenciasBanda() {
			$registros = $this->preferencias->obtemListaBandas();
			$this->_view->atribui("registros",$registros);
			
			$id = @$_REQUEST["id"];
			
			$acao = @$_REQUEST["acao"];

			$valor_banda = @$_REQUEST["valor_banda"];
			$descricao_banda = @$_REQUEST["descricao_banda"];

			$this->_view->atribui("url","admin-administracao.php?op=preferencias&tela=banda");

			
			if( $acao == "cadastra" ) {
				$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");
				// Cadastra
				$this->preferencias->cadastraBanda($valor_banda,$descricao_banda);
				$this->_view->atribui("mensagem","Banda cadastrada com sucesso.");
				$this->_view->atribuiVisualizacao("msgredirect");
				
			} else {
				if( $id != '' ) {
					if( !$acao ) {
						$achou = 0;
						for($i=0;$i<count($registros);$i++) {
							if( $registros[$i]["id"] == $id ) {
								$achou = 1;
								$this->_view->atribui("banda",$registros[$i]["banda"]);
							}
						}

						if( !$achou ) {
							$id = '';
						}

					} else {
						$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");
						if( $acao == "atualiza" ) {
							// Update
							$this->preferencias->atualizaBanda($id,$valor_banda,$descricao_banda);
							$mensagem = "Banda atualizada com sucesso.";
						} else if( $acao == "exclui" ) {
							// Exclui
							$this->preferencias->excluiBanda($id);
							$mensagem = "Banda excluída com sucesso.";
						}
						
						$this->_view->atribui("mensagem",$mensagem);
						$this->_view->atribuiVisualizacao("msgredirect");
						
					} 

				}
				$this->_view->atribui("id",$id);
			}
			
			
		}
		
		protected function executaPreferenciasMonitoramento() {
			$info = $this->preferencias->obtemMonitoramento();
			
			$acao = @$_REQUEST["acao"];
			
			if( !$acao ) {
				while( list($vr,$vl) = each($info) ) {
					$this->_view->atribui($vr,$vl);
				}
			} else {
				$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");
				$this->preferencias->atualizaMonitoramento($_REQUEST);
				$this->_view->atribui("url","admin-administracao.php?op=preferencias&tela=monitoramento");
				$this->_view->atribui("mensagem","Preferência atualizada com sucesso.");
				$this->_view->atribuiVisualizacao("msgredirect");
			}
			
		}
		
		protected function executaPreferenciasLinks() {
			$registros = $this->preferencias->obtemListaLinks();
			$this->_view->atribui("registros",$registros);
			
			$id_link = @$_REQUEST["id_link"];
			$this->_view->atribui("id_link",$id_link);
			
			$acao = @$_REQUEST["acao"];
			$this->_view->atribui("acao",$acao);
			
			$this->_view->atribui("targets",$this->preferencias->obtemListaTargetsLink());
			
			
			$titulo_link = @$_REQUEST["titulo_link"];
			$url = @$_REQUEST["url"];
			$descricao = @$_REQUEST["descricao"];
			$target = @$_REQUEST["target"];
			
			
			$url_redir = "admin-administracao.php?op=preferencias&tela=links";
			
			if( $acao == "cadastra" ) {
				$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");
				// Cadastra
				$this->preferencias->cadastraLink($titulo_link,$url,$descricao,$target);
				$this->_view->atribui("url",$url_redir);
				$this->_view->atribui("mensagem","Link externo cadastrado com sucesso.");
				$this->_view->atribuiVisualizacao("msgredirect");
				
			} else {
				if( $id_link ) {
					if( !$acao ) {
						$info = $this->preferencias->obtemLinkPeloId($id_link);
						
						while(list($vr,$vl)=each($info)) {
							if( $vr == "titulo" ) {
								$vr = "titulo_link";
							}
							
							$this->_view->atribui($vr,$vl);
						}
						
					} else {
						$this->requirePrivGravacao("_ADMINISTRACAO_PREFERENCIAS");
						if($acao == "atualiza") {
							$this->preferencias->atualizaLink($id_link,$titulo_link,$url,$descricao,$target);
							$mensagem = "Link externo alterado com sucesso.";
						} else if($acao == "exclui") {
							$this->preferencias->excluiLink($id_link);
							$mensagem = "Link externo excluído com sucesso.";
						}

						$this->_view->atribui("url",$url_redir);
						$this->_view->atribui("mensagem",$mensagem);
						$this->_view->atribuiVisualizacao("msgredirect");

					}
				} 
			}
		}
		
		
		
		//Funçao de execução do Preferências HELPDESK
		function executaPreferenciasHelpdesk() { 
			$helpdesk = VirtexModelo::factory("helpdesk");
		
			$tela = @$_REQUEST["tela"];
			$subtela = @$_REQUEST["subtela"];
			$modo_visualizacao = @$_REQUEST["visualizacao"];
		
			$this->_view->atribui("tela", $tela);
			$this->_view->atribui("modo_visualizacao", $modo_visualizacao);
			$this->_view->atribui("subtela", $subtela);
			
			
			switch($subtela) {
				
				case 'cadastro_grupo': 
					$dados = $_REQUEST;
					
					if($this->_acao) {
					
						if($dados["id_grupo"]) {		//ALTERAÇÃO D EUM GRUPO EXISTENTE
						
							$id_grupo = $helpdesk->alteraGrupo($dados["nome"], $dados["descricao"], $dados["ativo"], $dados["id_grupo_pai"], $dados["id_grupo"]);
							$url_redir = "admin-administracao.php?op=preferencias&tela=helpdesk&subtela=cadastro_grupo&visualizacao=1&id_grupo=" . $dados["id_grupo"];
							$mensagem = "Grupo alterado com sucesso";
							$this->_view->atribui("url",$url_redir);
							$this->_view->atribui("mensagem",$mensagem);
							$this->_view->atribuiVisualizacao("msgredirect");							
						
						} else {	//GRAVAÇÃO DE UM NOVO GRUPO

							$id_grupo = $helpdesk->cadastraGrupo($dados["nome"], $dados["descricao"], $dados["ativo"], $dados["id_grupo_pai"]);
							$url_redir = "admin-administracao.php?op=preferencias&tela=helpdesk";
							$mensagem = "Grupo cadastrado com sucesso";
							$this->_view->atribui("url",$url_redir);
							$this->_view->atribui("mensagem",$mensagem);
							$this->_view->atribuiVisualizacao("msgredirect");	

						}
						
					} else {
					
						$registros = $helpdesk->obtemListaGrupos();		
						$this->_view->atribui("grupos_pai", $registros);
						
						if($dados["id_grupo"]) { 	//informacoes para a tela de alteracao
							$infogrupo = $helpdesk->obtemGrupoPeloId($dados["id_grupo"]);							
							foreach($infogrupo as $chave => $valor) {	//Passa todas a informações adquiridas para o VIEW
								$this->_view->atribui($chave, $valor);
							}
							
							$usuarios_grupo = $helpdesk->obtemListaAdminGrupo($dados["id_grupo"]);							
							$this->_view->atribui("usuarios_grupo", $usuarios_grupo);
						}
						
					}
					break;
				
				case 'altera_usuario':
					$acao = "alterar";
					$this->_view->atribui("acao", $acao);
				case 'cadastro_usuarios':
				
					$dados = $_REQUEST;
					$id_admin = $dados["id_admin"];
					
					if($this->_acao == "gravar") {	//GRAVA UM NOVO USUARIO
						
						$retorno = $helpdesk->cadastraUsuarioGrupo($dados["id_grupo"], $dados["id_admin"], $dados["nome_admin"], $dados["admin"] , $dados["ativo"]);
						$url_redir = "admin-administracao.php?op=preferencias&tela=helpdesk&subtela=cadastro_grupo&visualizacao=1&id_grupo=" . $dados["id_grupo"];
						$mensagem = "Administrador cadastrado com sucesso";
						$this->_view->atribui("url",$url_redir);
						$this->_view->atribui("mensagem",$mensagem);
						$this->_view->atribuiVisualizacao("msgredirect");
						
					} else if ($this->_acao == "alterar") {

						$retorno = $helpdesk->alteraUsuarioGrupo($dados["id_grupo"], $dados["id_admin"], $dados["nome_admin"], $dados["admin"], $dados["ativo"]);
						$url_redir = "admin-administracao.php?op=preferencias&tela=helpdesk&subtela=cadastro_grupo&visualizacao=1&id_grupo=" . $dados["id_grupo"];
						$mensagem = "Administrador alterado com sucesso";
						$this->_view->atribui("url",$url_redir);
						$this->_view->atribui("mensagem",$mensagem);
						$this->_view->atribuiVisualizacao("msgredirect");					
					
					} else {
					
						$infogrupo = NULL;
						
						if($dados["id_admin"] && $dados["id_grupo"]) { 
							$infogrupo = $helpdesk->obtemListaAdminGrupo($dados["id_grupo"], $dados["id_admin"]);							
						} else { 
							$infogrupo = $helpdesk->obtemGrupoPeloId($dados["id_grupo"]);							
						}

						foreach($infogrupo as $chave => $valor) {	//Passa todas a informações adquiridas para o VIEW
							$this->_view->atribui($chave, $valor);
						}

						$administradores = $helpdesk->obtemListaAdminForaGrupo($dados["id_grupo"]);
						$this->_view->atribui("lista_admin", $administradores);		
						
					}
					
					if(@$dados["novo"]) {
						$this->_view->atribui("acao", "gravar");
					}
					break;
				
				case 'config':
					$preferencias = VirtexModelo::factory("preferencias");
					if(!$this->_acao) {						
						$preferencia_helpdesk = $preferencias->obtemPreferenciasHelpdesk();
						$this->_view->atribui("preferencia", $preferencia_helpdesk);
					} else {
						
						$limite_tempo_reabertura_chamado = @$_REQUEST["limite_tempo_reabertura_chamado"];
	
						$limite_tempo_reabertura_chamado = (!$limite_tempo_reabertura_chamado) ? 0 : $limite_tempo_reabertura_chamado;
						$preferencias->alteraPreferenciasHelpdesk($limite_tempo_reabertura_chamado);
						
						$url_redir = "admin-administracao.php?op=preferencias&tela=helpdesk";
						$mensagem = "Configurações alteradas com sucesso";
						
						$this->_view->atribui("url",$url_redir);
						$this->_view->atribui("mensagem",$mensagem);
						$this->_view->atribuiVisualizacao("msgredirect");							
					}
					break;
				
				case 'listagem':
				default:
					$registros = $helpdesk->obtemListaGrupos();

					$this->_view->atribui("registros", $registros);
					break;
			
			}
		}
		
		//----------------------------------------------------------
		// FIM: PREFERENCIAS
		//----------------------------------------------------------
		

	}




?>
