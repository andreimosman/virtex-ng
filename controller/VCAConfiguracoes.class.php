<?

	class VCAConfiguracoes extends VirtexControllerAdmin {

		public function __construct() {
			parent::__construct();
		}
		
		protected function init() {
			// Inicializações da SuperClasse
			parent::init();
			
			$this->_view = VirtexViewAdmin::factory("configuracoes");

		}
		
		
		protected function executa() {
		
			switch($this->_op) {
				case 'equipamentos':
					$this->requirePrivLeitura("_CONFIGURACOES_EQUIPAMENTOS");
					$this->executaEquipamentos();
					break;
					
				case 'preferencias':
					$this->requirePrivLeitura("_CONFIGURACOES_PREFERENCIAS");
					$this->executaPreferencias();
					break;
					
				case 'relatorios':
					$this->requirePrivLeitura("_CONFIGURACOES_RELATORIOS");
					$this->executaRelatorios();
					break;
					
				default:
					// Do something
			
			}
		
		}
		
		//---------------------------------------------------//
		//-- INICIO: EQUIPAMENTOS                          --//
		//---------------------------------------------------//
		
		
		protected function executaEquipamentos() {
			$tela = @$_REQUEST["tela"];
			
			$this->_view->atribuiVisualizacao("equipamentos");
			$this->_view->atribui("tela",$tela);
						
			switch( $tela ) {
				case 'servidores':
					$this->executaEquipamentosServidores();
					break;
				case 'pops':
					$this->executaEquipamentosPOPs();
					break;
				case 'nas':
					$this->executaEquipamentosNAS();
					break;
				
				default:
					// Do something
			
			}
		}
		
		protected function executaEquipamentosServidores() {
			$equipamentos = VirtexModelo::factory("equipamentos");
			
			$subtela = @$_REQUEST["subtela"] ? $_REQUEST["subtela"] : "listagem";
			$this->_view->atribui("subtela",$subtela);
			
			$acao = @$_REQUEST["acao"];
			
			$id_servidor = @$_REQUEST["id_servidor"];																					
			$hostname = @$_REQUEST["hostname"];
			$ip = @$_REQUEST["ip"];
			$usuario = @$_REQUEST["usuario"]; 
			$senha = @$_REQUEST["senha"]; 
			$disponivel = @$_REQUEST["disponivel"];	

			$porta = @$_REQUEST["porta"];
			if( !$porta ) $porta = 11000;
			$this->_view->atribui("porta",$porta);
			
			$chave = @$_REQUEST["chave"];
		
			$url = "admin-configuracoes.php?op=equipamentos&tela=servidores";
	
			$this->_view->atribui("id_servidor",$id_servidor);
			
			switch($subtela) {
				case 'listagem':
					$registros = $equipamentos->obtemListaServidores();
					$this->_view->atribui("registros",$registros);
					break;
				case 'cadastro':
					if($id_servidor) {
						if( !$acao ) {
							// Pegar do banco
							$info = $equipamentos->obtemServidor($id_servidor);
							while(list($vr,$vl)=each($info)) {
								$this->_view->atribui($vr,$vl);
							}
							
							if( !$this->requirePrivGravacao("_CONFIGURACOES_EQUIPAMENTOS",false) ) {
								$this->_view->atribui("podeGravar",false);
							}
							
						} else {
							// Processar alteração
							$this->requirePrivGravacao("_CONFIGURACOES_EQUIPAMENTOS");
							$equipamentos->atualizaServidor($id_servidor, $hostname, $ip, $porta, $chave, $usuario, $senha, $disponivel);
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem","Servidor atualizado com sucesso.");
							$this->_view->atribuiVisualizacao("msgredirect");

						}
					} else {
						// Cadastro
						$this->requirePrivGravacao("_CONFIGURACOES_EQUIPAMENTOS");
						$this->_view->atribui("podeGravar",true);
						if( $acao ) {
							// Cadastrar
							$id_servidor = $equipamentos->cadastraServidor($hostname, $ip, $porta, $chave, $usuario, $senha, $disponivel);
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem","Servidor cadastrado com sucesso.");
							$this->_view->atribuiVisualizacao("msgredirect");

						}
					}
					break;
			}
		}
		
		protected function executaEquipamentosPOPs() {
			$equipamentos = VirtexModelo::factory("equipamentos");
			
			$subtela = @$_REQUEST["subtela"] ? $_REQUEST["subtela"] : "listagem";
			$this->_view->atribui("subtela",$subtela);
			
			$acao = @$_REQUEST["acao"];
			
			$url = "admin-configuracoes.php?op=equipamentos&tela=pops";
			
			$id_pop = @$_REQUEST["id_pop"];
			$nome = @$_REQUEST["nome"];			
			$info = @$_REQUEST["info"];
			$tipo = @$_REQUEST["tipo"];
			$id_pop_ap = @$_REQUEST["id_pop_ap"];
			$status = @$_REQUEST["status"];
			$ipaddr = @$_REQUEST["ipaddr"];
			$id_servidor = @$_REQUEST["id_servidor"];
			$ativar_monitoramento = @$_REQUEST["ativar_monitoramento"];
			
			$this->_view->atribui("id_pop",$id_pop);
			switch($subtela) {
				case 'listagem':
					$registros = $equipamentos->obtemListaPOPs();
					$this->_view->atribui("registros",$registros);				
				
					break;

				case 'cadastro':
					if( !$acao ) {
						$servidores = $equipamentos->obtemListaServidores();
						$this->_view->atribui("servidores",$servidores);
						
						$parent_pops = $equipamentos->obtemListaPOPs();		
						$this->_view->atribui("parent_pops",MJson::encode($parent_pops) );
											
						$status_pop = $equipamentos->obtemStatusPop();
						$this->_view->atribui("status_pop",$status_pop);
						
						$tipos = $equipamentos->obtemTipoPop();
						$this->_view->atribui("tipo_pop",$tipos);
					}
					if($id_pop) {
						if( !$acao ) {
							// Pegar do banco
							
							if($id_pop) {
								if( $this->_acao ) {
									$info = @$_REQUEST;
								} else {
									$info = $equipamentos->obtemPop($id_pop);
								}
								while(list($vr,$vl)=each($info)) {
									$this->_view->atribui($vr,$vl);
								}

								if( !$this->requirePrivGravacao("_CONFIGURACOES_EQUIPAMENTOS",false) ) {
									$this->_view->atribui("podeGravar",false);
								}

							}
						} else {
							// Processar alteração								
							$equipamentos->atualizaPop($id_pop, $nome, $info, $tipo, $id_pop_ap, $status, $ipaddr, $id_servidor, $ativar_monitoramento);
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem","Pop atualizado com sucesso.");
							$this->_view->atribuiVisualizacao("msgredirect");
						}
					} else {
						$this->requirePrivGravacao("_CONFIGURACOES_EQUIPAMENTOS");
						$this->_view->atribui("podeGravar",true);

						if( $acao ) {
							// Cadastrar							
							$equipamentos->cadastraPop($id_pop, $nome, $info, $tipo, $id_pop_ap, $status, $ipaddr, $id_servidor, $ativar_monitoramento);
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem","Pop cadastrado com sucesso.");
							$this->_view->atribuiVisualizacao("msgredirect");
						}
					}
					break;
			}
		}
		
		protected function executaEquipamentosNAS() {
			$equipamentos = VirtexModelo::factory("equipamentos");
			$subtela = @$_REQUEST["subtela"] ? $_REQUEST["subtela"] : "listagem";
			$this->_view->atribui("subtela",$subtela);
			
			switch($subtela) {
				case 'listagem':
					$registros = $equipamentos->obtemListaNAS();
					$this->_view->atribui("registros",$registros);				
				
					break;
				case 'cadastro':
					$servidores = $equipamentos->obtemListaServidores();					
					$this->_view->atribui("servidores",$servidores);
					
					$padroes = $equipamentos->obtemPadraoPPPoE();
					$this->_view->atribui("padroes",$padroes);
					
					$tipos = $equipamentos->obtemTiposNAS();
					$this->_view->atribui("tipos",$tipos);

					$id_nas = @$_REQUEST["id_nas"];					
					$nome = @$_REQUEST["nome"];
					$ip = @$_REQUEST["ip"];					
					$secret = @$_REQUEST["secret"];
					$tipo_nas = @$_REQUEST["tipo_nas"];
					$id_servidor = @$_REQUEST["id_servidor"];
					$padrao = @$_REQUEST["padrao"];
					
					$this->_view->atribui("id_nas", $id_nas);

					$acao = @$_REQUEST["acao"];
															
					if( $id_nas ) {
						if( !$acao ) {
							// Exibir os dados						
							
							$dados = $equipamentos->obtemNAS($id_nas);
							while(list($vr,$vl)=each($dados)) {
								$this->_view->atribui($vr,$vl);
							}
							if( !$this->requirePrivGravacao("_CONFIGURACOES_EQUIPAMENTOS",false) ) {
								$this->_view->atribui("podeGravar",false);
							}
							
						} else {
							//ALTERAR
							$this->requirePrivGravacao("_CONFIGURACOES_EQUIPAMENTOS");
							$equipamentos->atualizaNAS($id_nas, $nome, $ip, $secret, $id_servidor, $padrao);
							
							$url = "admin-configuracoes.php?op=equipamentos&tela=nas";
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem","NAS atualizado com sucesso.");
							$this->_view->atribuiVisualizacao("msgredirect");							
						}					
						
					} else {
						// echo "CADASTRO<br>\n";
						$this->requirePrivGravacao("_CONFIGURACOES_EQUIPAMENTOS");
						$this->_view->atribui("podeGravar",true);
						
						if( $acao ) {
							//CADASTRAR							
							$equipamentos->cadastraNAS($nome, $ip, $secret, $tipo_nas, $id_servidor, $padrao);
							
							$url = "admin-configuracoes.php?op=equipamentos&tela=nas&subtela=cadastro";
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem","NAS cadastrado com sucesso.");
							$this->_view->atribuiVisualizacao("msgredirect");							
						}
						
					}
					
					break;
				case 'redes':
					$id_nas = @$_REQUEST["id_nas"];
					$this->_view->atribui("id_nas",$id_nas);
					$info = $equipamentos->obtemNAS($id_nas);
					$this->_view->atribui("nome",@$info["nome"]);
					$this->_view->atribui("tipo_nas",@$info["tipo_nas"]);
					
					$exibir_enderecos = @$_REQUEST["exibir_enderecos"];
					$this->_view->atribui("exibir_enderecos",$exibir_enderecos);
										
					if( $exibir_enderecos ) {
						if( $info["tipo_nas"] == "I" ) {
							// NAS IP
							$registros = $equipamentos->obtemRedesNAS($id_nas);

						} else if( $info["tipo_nas"] == "P" ) {
							// PPPoE
							$registros = $equipamentos->obtemIPsNAS($id_nas);
							$this->_view->atribui("registros",$registros);
						} else {
							$registros = array();
						}
						$this->_view->atribui("registros",$registros);
					} else {
						// Cadastro
						if( !$this->requirePrivGravacao("_CONFIGURACOES_EQUIPAMENTOS",false) ) {
							$this->_view->atribui("podeGravar",false);
						} else {
							$this->_view->atribui("podeGravar",true);
						}

						if( $info["tipo_nas"] == "I" ) {
							// IP
							$bits_rede = @$_REQUEST["bits_rede"];
							if(!$bits_rede) $bits_rede = 30;
							$this->_view->atribui("bits_rede",$bits_rede);
							
							$maximo_redes = @$_REQUEST["maximo_redes"];
							if(!$maximo_redes) $maximo_redes = 1024;
							$this->_view->atribui("maximo_redes",$maximo_redes);
						}
						
						if( $info["tipo_nas"] == "P" ) {
							// PPPoE
						}
						
						
						$acao = @$_REQUEST["acao"];
						
						if( $acao ) {
						
							$tipo_rede = @$_REQUEST["tipo_rede"];
							if(!$tipo_rede) $tipo_rede = "C";
							$this->_view->atribui("tipo_rede",$tipo_rede);
						

							$log_cadastro = array("ok" => array(),"erro" => array());
							if( $info["tipo_nas"] == "I" ) {
								// NAS IP
								$rede_origem = @$_REQUEST["rede_origem"];								
								
								@list($rede_inicial,$br) = explode("/",@$_REQUEST["rede_inicial"]);
								$rede_inicial = $rede_inicial . "/" . $bits_rede;
								
								$this->_view->atribui("rede_origem",$rede_origem);
								$this->_view->atribui("rede_inicial",$rede_inicial);
								
								try {
								
								
									for($ip = new MInet($rede_inicial,$rede_origem),$c=0; $ip->obtemRede() != "" && $c<$maximo_redes; $ip = $ip->proximaRede(),$c++) {
										//
										// 1) Verificar se a rede que está tentando cadastrar não está cadastrada. 
										//

										$nova_rede = $ip->obtemRede() . "/" . $ip->obtemBitmask();
										$conflitos = $equipamentos->obtemRedesAssociadas($nova_rede);

										if( !count($conflitos) ) {
											$log_cadastro["ok"][] = array("endereco" => $nova_rede, "msg" => "Rede cadastrada com sucesso.");
										} else {
											$log_cadastro["erro"][] = array("endereco" => $nova_rede, "msg" => "Rede já está cadastrada no sistema ou sobrepõe-se a uma rede cadastrada.");
										}

										//
										// 2) Cadastro no Banco de Dados
										//
										$equipamentos->cadastraRedeIPNAS($id_nas,$nova_rede,$tipo_rede);

									}

								} catch(MException $e) {
									$this->_view->atribui("erro_inet",$e->getMessage());
								}
								
								$this->_view->atribui("count_ok",count($log_cadastro["ok"]));
								$this->_view->atribui("count_erro",count($log_cadastro["erro"]));
								
							} else if( $info["tipo_nas"] == "P" ) {
								// NAS PPPoE								
								$erro_inet = "";
								
								$endereco = @$_REQUEST["endereco"];
								@list($rede,$bits) = explode("/",$endereco);
								if( !$bits ) $erro_inet = "Endereço da rede não está no formato ip/bits";
								
								if( !$erro_inet ) {
									
									try {
										$ip = new MInet($endereco);	// Se não for válido vai desparar uma exception.
										$this->_view->atribui("endereco",$endereco);
										
										$conflitos = $equipamentos->obtemRedesAssociadas($endereco);
										
										if( count($conflitos) ) {
											$erro_inet = "ERRO: Rede já está cadastrada no sistema ou sobrepõe-se a uma rede cadastrada.";
										} else {
											$equipamentos->cadastraRedePPPoENAS($id_nas,$endereco,$tipo_rede);
										}
										
									} catch(MException $e) {
										$erro_inet = $e->getMessage();
									}
								
								}
								
								$this->_view->atribui("erro_inet",$erro_inet);
								
							}
							
							$this->_view->atribui("log_cadastro",$log_cadastro);
						}
					}
					
					
					break;
			}
			
			
			
		}
		
		
		//---------------------------------------------------//
		//-- FIM: EQUIPAMENTOS                             --//
		//---------------------------------------------------//
		
		
		
		
		
		
		
		//---------------------------------------------------//
		//-- INICIO: PREFERENCIAS                          --//
		//---------------------------------------------------//
		
		
		
		protected function executaPreferencias() {
			$tela = @$_REQUEST["tela"];
			$this->_view->atribui("tela",$tela);
			$this->_view->atribuiVisualizacao("preferencias");
			
			if($tela != "registro") {
				$this->requirePrivLeitura("_CONFIGURACOES_PREFERENCIAS");
				if( $this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS",false)  ) {
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
					$this->requirePrivLeitura("_CONFIGURACOES_PREFERENCIAS_REGISTRO");
					$this->executaPreferenciasRegistro();
					break;
				case 'resumo':
					$this->executaPreferenciasResumo();
					break;	
				default:
					// Do Something
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

			$url = "admin-configuracoes.php?op=preferencias&tela=resumo";
		}
		
		protected function executaPreferenciasRegistro() {
			$acao = @$_REQUEST["acao"];
			
			$subtela = @$_REQUEST["subtela"];
			$this->_view->atribui("subtela",$subtela);

			$url = "admin-configuracoes.php?op=preferencias&tela=registro";
			
			if( $this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS_REGISTRO",false) ) {
				$this->_view->atribui("podeGravar",true);
			} else {
				$this->_view->atribui("podeGravar",false);
			}
			
			
			if ($acao == "upload"){
				$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS_REGISTRO");
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

			$url = "admin-configuracoes.php?op=preferencias&tela=modelos";
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
					$this->requirePrivGravacao("_COBRANCA_PREFERENCIAS");

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
				$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS");
				
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

				$this->_view->atribui("url","admin-configuracoes.php?op=preferencias&tela=geral");				
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
				$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS");
				$this->preferencias->atualizaPreferenciasProvedor(
																	@$_REQUEST["endereco"],
																	@$_REQUEST["localidade"],
																	@$_REQUEST["cep"],
																	@$_REQUEST["cnpj"],
																	@$_REQUEST["fone"]
																);

				$this->_view->atribui("url","admin-configuracoes.php?op=preferencias&tela=provedor");				
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
			
			$url = "admin-configuracoes.php?op=preferencias&tela=cobranca";			
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
						$this->_view->atribui("formas",$formas);						
						//echo "<pre>";
						//print_r($formas);
						//echo "</pre>";
						
					}
				}
				if( $acao == "alterar" ) {
					// Rotina de alteração.
					// echo "Alterar!!!<br>\n";
					$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS");
					
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
					$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS");
				}
				
				if( $acao ) {
					$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS");
					
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
				$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS");
				// Executa a atualização
				$disponivel = @$_REQUEST["disponivel"];
				
				if( $disponivel && count($disponivel) ) {
					while(list($id,$disp) = each($disponivel)) {
						$this->preferencias->atualizaDisponibilidadeCidade($id,$disp);
					}
				}

				$this->_view->atribui("mensagem","Disponibilidade atualizada com sucesso.");
				$this->_view->atribui("url","admin-configuracoes.php?op=preferencias&tela=cidades");
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

			$this->_view->atribui("url","admin-configuracoes.php?op=preferencias&tela=banda");

			
			if( $acao == "cadastra" ) {
				$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS");
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
						$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS");
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
				$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS");
				$this->preferencias->atualizaMonitoramento($_REQUEST);
				$this->_view->atribui("url","admin-configuracoes.php?op=preferencias&tela=monitoramento");
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
			
			
			$url_redir = "admin-configuracoes.php?op=preferencias&tela=links";
			
			if( $acao == "cadastra" ) {
				$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS");
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
						$this->requirePrivGravacao("_CONFIGURACOES_PREFERENCIAS");
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



		//---------------------------------------------------//
		//-- FIM: PREFERENCIAS                             --//
		//---------------------------------------------------//



		
		protected function executaRelatorios() {
			
			$this->_view->atribuiVisualizacao("relatorios");
		
			$contas = VirtexModelo::factory('contas');
			$equipamentos = VirtexModelo::factory('equipamentos');
			
			$relatorio = @$_REQUEST["relatorio"];
			$tipo = @$_REQUEST["tipo"];
			
			$this->_view->atribui("relatorio",$relatorio);
			$this->_view->atribui("tipo",$tipo);


			
			switch($relatorio) {
				case 'carga':
					$listaContas = array();
					$listaEqpto = array();

					$tiposPop = $equipamentos->obtemTipoPop();
					$this->_view->atribui("tiposPop",$tiposPop);
							
					$tiposNas = $equipamentos->obtemTiposNAS();
					$this->_view->atribui("tiposNas", $tiposNas);

					switch($tipo) {
						case 'ap':
							$id_pop = @$_REQUEST["id"];
							$this->_view->atribui("id_pop",$id_pop);
							
							if($id_pop) {
								$pop = $equipamentos->obtemPOP($id_pop);
								$this->_view->atribui("pop",$pop);
								
								$listaPops = array($id_pop);

								$tmp = $equipamentos->obtemListaPOPs("",$id_pop);
								foreach($tmp as $t) {
									$listaPops[] = $t["id_pop"];
								}
								
								foreach($listaPops as $p) {
									$listaContas = array_merge($listaContas,$contas->obtemContasBandaLargaPeloPOPNAS($p,false,"A"));
								}
								
							} else {
								$listaEqpto=$equipamentos->obtemPOPsPeloTipo("AP");
							}
						
						
							break;

						case 'pop':
							$id_pop = @$_REQUEST["id"];
							$this->_view->atribui("id_pop",$id_pop);
							
							if( $id_pop ) {
								$pop = $equipamentos->obtemPOP($id_pop);
								$this->_view->atribui("pop",$pop);
								
								$listaContas = $contas->obtemContasBandaLargaPeloPOPNAS($id_pop,false,"A");
								print_r($lista_contas);
							} else {
								$listaEqpto = $equipamentos->obtemListaPops();
							}
							break;

						case 'nas':
							$id_nas = @$_REQUEST["id"];
							$this->_view->atribui("id_nas",$id_nas);
							
							if( $id_nas ) {
								$nas = $equipamentos->obtemNAS($id_nas);
								$this->_view->atribui("nas",$nas);
								$listaContas = $contas->obtemContasBandaLarga($id_nas,"A");
							} else {
								$listaEqpto = $equipamentos->obtemListaNAS(null,false);
							}
							break;
						
					
					}
					
					if( @$listaEqpto && count($listaEqpto) ) {
						$contas_ativas = 0;
						$soma_upload = 0;
						$soma_download = 0;

						for($i=0;$i<count($listaEqpto);$i++) {
							$id_pop = $tipo == "pop" || $tipo == "ap" ? $listaEqpto[$i]["id_pop"] : null;
							$id_nas = $tipo == "nas" ? $listaEqpto[$i]["id_nas"] : null;
							
							$listaEqpto[$i]["id"] = $tipo == "nas" ? $id_nas : $id_pop;
							
							$listaPops = array($id_pop);
							if( $tipo == "ap" ) {
								// $tipo == pop --> Pegar os "childs" do pop
								$tmp = $equipamentos->obtemListaPOPs("",$id_pop);
								foreach($tmp as $t) {
									$listaPops[] = $t["id_pop"];
								}
								
							}
							
							$listaEqpto[$i]["contas_ativas"] = 0;
							$listaEqpto[$i]["soma_upload"] = 0;
							$listaEqpto[$i]["soma_download"] = 0;
							
							foreach($listaPops as $p) {
								$lc = $contas->obtemContasBandaLargaPeloPOPNAS($p,$id_nas,"A");

								foreach($lc as $conta) {
									$listaEqpto[$i]["contas_ativas"]++;
									$listaEqpto[$i]["soma_upload"] += $conta["upload_kbps"];
									$listaEqpto[$i]["soma_download"] += $conta["download_kbps"];										
								}
								$contas_ativas += $listaEqpto[$i]["contas_ativas"];
								$soma_upload += $listaEqpto[$i]["soma_upload"];
								$soma_download += $listaEqpto[$i]["soma_download"];
							}

							$this->_view->atribui("contas_ativas",$contas_ativas);
							$this->_view->atribui("soma_upload",$soma_upload);
							$this->_view->atribui("soma_download",$soma_download);
						}					
					}
					
					$clientes = VirtexModelo::factory("clientes");
					
					if( @$listaContas && count($listaContas) ) {
						$cache_nas = array();
						foreach($equipamentos->obtemListaNas() as $n) {
							$cache_nas[$n["id_nas"]] = $n;
						}

						$cache_pop = array();

						$soma_upload = 0;
						$soma_download = 0;
						
						$cache_cliente = array();
						$cache_cidade = array();

						for($i=0;$i<count($listaContas);$i++) {
							$listaContas[$i]["nas"] = $cache_nas[ $listaContas[$i]["id_nas"] ];

							if( !@$cache_pop[ $listaContas[$i]["id_pop"] ] ) {
								$cache_pop[ $listaContas[$i]["id_pop"] ] = $equipamentos->obtemPop( $listaContas[$i]["id_pop"] );
							}
							
							if( !@$cache_cliente[ $listaContas[$i]["id_cliente"] ] ) {
								$cache_cliente[ $listaContas[$i]["id_cliente"] ] = $clientes->obtemPeloID($listaContas[$i]["id_cliente"]);
								
								if( !@$cache_cidade[ $cache_cliente[ $listaContas[$i]["id_cliente"] ]["id_cidade"] ] ) {
									$cache_cidade[ $cache_cliente[ $listaContas[$i]["id_cliente"] ]["id_cidade"] ] = $this->preferencias->obtemCidadePeloId( $cache_cliente[ $listaContas[$i]["id_cliente"] ]["id_cidade"] );
								}
								$cache_cliente[ $listaContas[$i]["id_cliente"] ]["cidade"] = $cache_cidade[ $cache_cliente[ $listaContas[$i]["id_cliente"] ]["id_cidade"] ];
							}
							$listaContas[$i]["cliente"] = $cache_cliente[ $listaContas[$i]["id_cliente"] ];

							$listaContas[$i]["pop"] = $cache_pop[ $listaContas[$i]["id_pop"] ];

							$soma_upload += $listaContas[$i]["upload_kbps"];
							$soma_download += $listaContas[$i]["download_kbps"];

							$this->_view->atribui("soma_upload",$soma_upload);
							$this->_view->atribui("soma_download",$soma_download);
						}
						
						//echo "<pre>";
						//print_r($cache_cliente);
						//print_r($cache_cidade);
						//echo "</pre>";

						$this->_view->atribui("listaContas",$listaContas);					
					}
					
					$this->_view->atribui("listaEqpto",$listaEqpto);
					
					//echo "<pre>";
					//print_r($listaNas);
					//echo "</pre>";
				
				
				
				
				
				
				
				
				
					break;
				case 'clientes_ap':
				
					break;
			
			}
			
		
		
		
		
		}
	
	}
	
?>
