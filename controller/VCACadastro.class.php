<?

	class VCACadastro extends VirtexControllerAdmin {
	
		protected $produtos;
		protected $administradores;
		protected $cadastro;
		protected $helpdesk;

		public function __construct() {
			parent::__construct();
		}
		
		protected function init() {
			// Inicializações da SuperClasse
			parent::init();
			
			$this->_view = VirtexViewAdmin::factory("cadastro");
			
			$this->produtos = VirtexModelo::factory("produtos");
			$this->administradores = VirtexModelo::factory("administradores");
			$this->cadastro = VirtexModelo::factory("cadastro");
			$this->helpdesk = VirtexModelo::factory("helpdesk");

		}
		
		
		protected function executa() {
		
			switch($this->_op) {
				case 'equipamentos':
					$this->requirePrivLeitura("_CADASTRO_EQUIPAMENTOS");
					$this->executaEquipamentos();
					break;
					
				case 'preferencias':
					$this->requirePrivLeitura("_ADMINISTRACAO_PREFERENCIAS");
					$this->executaPreferencias();
					break;
					
				case 'relatorios':
					$this->requirePrivLeitura("_CADASTRO_RELATORIOS");
					$this->executaRelatorios();
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
					
				case 'condominios':
					$this->executaCondominios();
					break;	
					
				case 'helpdesk':
					$this->executaHelpdesk();
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
		
			$url = "admin-cadastro.php?op=equipamentos&tela=servidores";
	
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
							
							if( !$this->requirePrivGravacao("_CADASTRO_EQUIPAMENTOS",false) ) {
								$this->_view->atribui("podeGravar",false);
							}
							
						} else {
							// Processar alteração
							$this->requirePrivGravacao("_CADASTRO_EQUIPAMENTOS");
							$equipamentos->atualizaServidor($id_servidor, $hostname, $ip, $porta, $chave, $usuario, $senha, $disponivel);
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem","Servidor atualizado com sucesso.");
							$this->_view->atribuiVisualizacao("msgredirect");

						}
					} else {
						// Cadastro
						$this->requirePrivGravacao("_CADASTRO_EQUIPAMENTOS");
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
			
			$url = "admin-cadastro.php?op=equipamentos&tela=pops";
			
			$id_pop = @$_REQUEST["id_pop"];
			$nome = @$_REQUEST["nome"];			
			$info = @$_REQUEST["info"];
			$tipo = @$_REQUEST["tipo"];
			$id_pop_ap = @$_REQUEST["id_pop_ap"];
			$status = @$_REQUEST["status"];
			$ipaddr = @$_REQUEST["ipaddr"];
			$mac = @$_REQUEST["mac"];
			$clientemacpop = @$_REQUEST["clientemacpop"];
			$id_servidor = @$_REQUEST["id_servidor"];
			$ativar_monitoramento = @$_REQUEST["ativar_monitoramento"];
			
			$this->_view->atribui("id_pop",$id_pop);
			switch($subtela) {
				case 'listagem':
					$registros = $equipamentos->obtemListaPOPs();
					$this->_view->atribui("registros",$registros);				
				
					break;

				case 'cadastro':
				
					$this->_view->setNoCache();
				
					if( !$acao ) {
						$servidores = $equipamentos->obtemListaServidores();
						$this->_view->atribui("servidores",$servidores);
						
						$parent_pops = $equipamentos->obtemListaPOPs();		
						
						
						//echo "<pre>";
						//print_r($parent_pops);
						//echo "</pre>";
						
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
									// echo "$vr = $vl<br>\n";
									$this->_view->atribui($vr,$vl);
								}

								if( !$this->requirePrivGravacao("_CADASTRO_EQUIPAMENTOS",false) ) {
									$this->_view->atribui("podeGravar",false);
								}

							}
						} else {
							// Processar alteração								
							$equipamentos->atualizaPop($id_pop, $nome, $info, $tipo, $id_pop_ap, $status, $ipaddr, $id_servidor, $ativar_monitoramento, $mac, $clientemacpop);
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem","Pop atualizado com sucesso.");
							$this->_view->atribuiVisualizacao("msgredirect");
						}
					} else {
						$this->requirePrivGravacao("_CADASTRO_EQUIPAMENTOS");
						$this->_view->atribui("podeGravar",true);

						if( $acao ) {
							// Cadastrar							
							$equipamentos->cadastraPop($id_pop, $nome, $info, $tipo, $id_pop_ap, $status, $ipaddr, $id_servidor, $ativar_monitoramento, $mac, $clientemacpop);
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
					
					$this->_view->atribui("podeGravar",$this->requirePrivGravacao("_CADASTRO_EQUIPAMENTOS",false));
															
					if( $id_nas ) {
						if( !$acao ) {
							// Exibir os dados						
							
							$dados = $equipamentos->obtemNAS($id_nas);
							while(list($vr,$vl)=each($dados)) {
								$this->_view->atribui($vr,$vl);
							}
							if( !$this->requirePrivGravacao("_CADASTRO_EQUIPAMENTOS",false) ) {
								$this->_view->atribui("podeGravar",false);
							}
							
						} else {
							//ALTERAR
							$this->requirePrivGravacao("_CADASTRO_EQUIPAMENTOS");
							$equipamentos->atualizaNAS($id_nas, $nome, $ip, $secret, $id_servidor, $padrao);
							
							$url = "admin-cadastro.php?op=equipamentos&tela=nas";
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem","NAS atualizado com sucesso.");
							$this->_view->atribuiVisualizacao("msgredirect");							
						}					
						
					} else {
						// echo "CADASTRO<br>\n";
						$this->requirePrivGravacao("_CADASTRO_EQUIPAMENTOS");
						$this->_view->atribui("podeGravar",true);
						
						if( $acao ) {
							//CADASTRAR							
							$equipamentos->cadastraNAS($nome, $ip, $secret, $tipo_nas, $id_servidor, $padrao);
							
							$url = "admin-cadastro.php?op=equipamentos&tela=nas&subtela=cadastro";
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
						if( !$this->requirePrivGravacao("_CADASTRO_EQUIPAMENTOS",false) ) {
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
				
				case 'pop_ip':
					$equipamentos = VirtexModelo::factory("equipamentos");
					$lista = $equipamentos->obtemIPsPOPs();
					
					$this->_view->atribui("lista",$lista);
					
					
					break;
					
				case 'condominios':
					
					switch($tipo) {
						case 'instalado':
						default:						
							$condominios = $this->cadastro->obtemCondominiosInstalados();
							$this->_view->atribui("condominios", $condominios);
						break;
					}
					
					break;
			}

		}
		
		
		protected function executaAdministradores() {
			$this->requirePrivLeitura("_CADASTRO_ADMINISTRADORES");
			$this->_view->atribui("podeGravar", true);
			$this->_view->atribuiVisualizacao("administradores");
			$tela = @$_REQUEST["tela"] ? $_REQUEST["tela"] : "listagem";

			$id_admin = @$_REQUEST["id_admin"];
			$nome = @$_REQUEST["nome"];
			$senha = @$_REQUEST["senha"];
			$admin = @$_REQUEST["admin"];
			$status = @$_REQUEST["status"];
			$email = @$_REQUEST["email"];
			$vendedor = @$_REQUEST["vendedor"];
			$comissionado = @$_REQUEST["comissionado"];
			$tipo_admin = @$_REQUEST["tipo_admin"];

			$this->_view->atribui("tela",$tela);
			$this->_view->atribui("id_admin", $id_admin);

			$podeGravar = false;
			if( $this->requirePrivGravacao("_CADASTRO_ADMINISTRADORES", false) ) {
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
							$this->requirePrivGravacao("_CADASTRO_ADMINISTRADORES");
							$this->administradores->alteraAdmin($id_admin, $admin, $email, $nome, $senha, $status, $vendedor, $comissionado, $tipo_admin);

							$url = "admin-cadastro.php?op=administradores&tela=listagem";

							$mensagem = "Administrador alterado com sucesso";
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem",$mensagem);
							$this->_view->atribuiVisualizacao("msgredirect");
						}

					} else { //Cadastro
						$this->requirePrivGravacao("_CADASTRO_ADMINISTRADORES");
						if(!$this->_acao) {
							$this->_view->atribui("acao","cadastrar");
						} else {

							$url = "admin-cadastro.php?op=administradores&tela=listagem";
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
								$this->administradores->cadastraAdmin($admin, $email, $nome, $senha, $status, $vendedor, $comissionado, $tipo_admin, TRUE);
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


					$this->requirePrivLeitura("_CADASTRO_ADMINISTRADORES");


					$acao = @$_REQUEST["acao"];
					$acesso = @$_REQUEST["acesso"];

					$id_admin = @$_REQUEST["id_admin"];


					if($acao=="gravar"){
						$this->requirePrivGravacao("_CADASTRO_ADMINISTRADORES");
						$this->administradores->gravaPrivilegioUsuario($id_admin,$acesso);
						$this->_view->atribui("url","admin-cadastro.php?op=administradores&tela=listagem");
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

						if( $this->requirePrivGravacao("_CADASTRO_ADMINISTRADORES", false) ) {
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
					$this->_view->atribui("modelos_contrato", $this->preferencias->obtemListaModelosContrato());					

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
							
							if(!$dados["modelo_contrato"]) 
								$dados["modelo_contrato"] = NULL;

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

							$url = "admin-cadastro.php?op=planos&tela=listagem";

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
		
		
		protected function executaCondominios() {
			$tela = @$_REQUEST["tela"];
			
			$dados = $_REQUEST;

			$this->_view->atribuiVisualizacao("condominios");
			$this->_view->atribui("situacoes", $this->cadastro->obtemSituacoes());
			
			//Atribuição da tela que será mostrada pela view
			$this->_view->atribui("tela", $tela);
			
			
			switch($tela) {
				case 'cadastro':
					//Cidades disponíveis
					$clientes = VirtexModelo::factory("clientes");
					$this->_view->atribui("cidades_disponiveis", $clientes->listaCidades());
					$this->_view->atribui("acao", "gravar");
					break;
				case 'bloco':
					$equipamentos = VirtexModelo::factory("equipamentos");
					$this->_view->atribui("acao", "gravar");
					$this->_view->atribui("listapops", $equipamentos->obtemListaPOPs("A"));					
					break;
				case 'listagem':
					//Pega a listagem de todos os condomínios cadastrados
					$condominios_cadastrados = $this->cadastro->obtemCondominio();
					$this->_view->atribui("condominios_cadastrados", $condominios_cadastrados);
					break;
				default:
						//Aqui num faz nada mesmo
			}
			
			if($this->_acao) {	//Entra em modo de gravação ou alteração
				
				//Faz a inversão da data recebida pelo formulário
				if($dados["data_instalacao"]) {
					list($d, $m, $a) = explode("/", $dados["data_instalacao"]);
					$dados["data_instalacao"] = "$a-$m-$d"; 
				}

				if($data_ativacao) {
					list($d, $m, $a) = explode("/", $dados["data_ativacao"]);
					$dados["data_ativacao"] = "$a-$m-$d"; 
				} 

				switch($tela) {
					case 'cadastro':
						if(@$_REQUEST["id_condominio"]) {
							//Por enquantto ainda nao faz nada.. mas aqui vai entrar a rotina de alteração de registro
							$this->cadastro->alterarCondominio($dados["id_condominio"], $dados["nome"],$dados["endereco"],$dados["complemento"],$dados["bairro"],$dados["id_cidade"],$dados["cep"],$dados["fone"],$dados["quantidade_edificios"],$dados["situacao"], $dados["data_instalacao"],$dados["data_ativacao"],$dados["sindico_nome"],$dados["sindico_fone"],$dados["zelador_nome"],$dados["zelador_fone"],$dados["observacoes"] );
							$mensagem = "Condomínio alterado com sucesso.";
							$url = "admin-cadastro.php?op=condominios&tela=cadastro&visualizacao=1&id_condominio=" . @$_REQUEST["id_condominio"];
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem",$mensagem);
							$this->_view->atribuiVisualizacao("msgredirect");					
						} else {
							$novoid = $this->cadastro->cadastrarCondominio( $dados["nome"],$dados["endereco"],$dados["complemento"],$dados["bairro"],$dados["id_cidade"],$dados["cep"],$dados["fone"],$dados["quantidade_edificios"],$dados["situacao"], $dados["data_instalacao"],$dados["data_ativacao"],$dados["sindico_nome"],$dados["sindico_fone"],$dados["zelador_nome"],$dados["zelador_fone"],$dados["observacoes"] );
							if($novoid) {
								$mensagem = "Condomínio cadastrado com sucesso.";
								$url = "admin-cadastro.php?op=condominios&tela=listagem";
								$this->_view->atribui("url",$url);
								$this->_view->atribui("mensagem",$mensagem);
								$this->_view->atribuiVisualizacao("msgredirect");
							} else {
								$mensagem = "Falha no cadastro do condomínio.";
								$url = "admin-cadastro.php?op=condominios&tela=listagem";
								$this->_view->atribui("url",$url);
								$this->_view->atribui("mensagem",$mensagem);
								$this->_view->atribuiVisualizacao("msgredirect");
							}	
						}
						break;
						
					case 'bloco':
						$id_condominio = $dados["id_condominio"];
						$id_bloco = $dados["id_bloco"];
						
						/* Em caso de receber o ID do condominio e o ID do bloco então, 
						 * então o tratamento de gravação será o considerado ALTERAÇÃO do bloco
						 * Em caso de recebimente de somente o ID do condomínio sem o recebimento
						 * do ID do bloco, será feitoo tratamento de GRAVAÇÃO de um novo bloco
						 */ 
						if($id_condominio && $id_bloco) { //ALTERAÇÃO 
						
							$this->cadastro->alterarCondominioBloco( $dados["id_bloco"], $dados["nome"], $dados["numero_andares"], $dados["apartamentos_andar"], $dados["total_apartamentos"], $dados["situacao"], $dados["id_pop"], $dados["observacoes"] );
							$url = "admin-cadastro.php?op=condominios&tela=cadastro&visualizacao=1&id_condominio=" . $dados["id_condominio"];
							$mensagem = "Bloco/Prédio alterado com sucesso";
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem",$mensagem);
							$this->_view->atribuiVisualizacao("msgredirect");							
							
						} else if ($id_condominio) {	// GRAVACAO 
						
							$novoid = $this->cadastro->cadastrarCondominioBloco( $dados["id_condominio"], $dados["nome"], $dados["numero_andares"], $dados["apartamentos_andar"], $dados["total_apartamentos"], $dados["situacao"], $dados["id_pop"], $dados["observacoes"] );
							
							if($novoid) {
								$mensagem = "Bloco/Prédio do Condomínio cadastrado com sucesso.";
								$url = "admin-cadastro.php?op=condominios&tela=cadastro&visualizacao=1&id_condominio=" . $dados["id_condominio"];
								$this->_view->atribui("url",$url);
								$this->_view->atribui("mensagem",$mensagem);
								$this->_view->atribuiVisualizacao("msgredirect");
							} else {
								$mensagem = "Falha no cadastro do Bloco/Prédio do Condomínio.";
								$url = "admin-cadastro.php?op=condominios&tela=cadastro&visualizacao=1&id_condominio=" . $dados["id_condominio"];
								$this->_view->atribui("url",$url);
								$this->_view->atribui("mensagem",$mensagem);
								$this->_view->atribuiVisualizacao("msgredirect");
							}
							
						}
				}
				
			} else {	// Entra em modo de vizualização de condominio e seus blocos
			
				$id_condominio = $dados["id_condominio"];
				$id_bloco = $dados["id_bloco"];
				
				switch($tela) {
				
					case "cadastro";				
				
						if($id_condominio) { 
							$condominio = $this->cadastro->obtemCondominio($id_condominio);

							if($condominio["data_instalacao"]) {
								@list($ano, $mes, $dia) = explode("-", $condominio["data_instalacao"]);
								$condominio["data_instalacao"] = "$dia/$mes/$ano";
							}

							if($condominio["data_ativacao"]) {
								@list($ano, $mes, $dia) = explode("-", $condominio["data_ativacao"]);
								$condominio["data_ativacao"] = "$dia/$mes/$ano";
							}

							foreach ($condominio as $chave => $valor) {
								$this->_view->atribui("$chave", $valor);
							}

							//Indica se o formulário será na forma de visualização ou de alteração
							$modo_visualizacao = false;
							if(@$_REQUEST["visualizacao"]) {	//Se for definido como 
								$modo_visualizacao = true;

								//Blocos pertencentes ao condomínio escolhido
								$blocos_condominio = $this->cadastro->obtemCondominioBloco($id_condominio);								
								//$situacoes = $this->cadastro->obtemSituacoes();
								$this->_view->atribui("blocos_condominio", $blocos_condominio);
								//$this->_view->atribui("situacoes", $situacoes);
							}

							$this->_view->atribui("modo_visualizacao", $modo_visualizacao);	
						}
						break;
						
					case "bloco":
					
						if($id_condominio && $id_bloco)  {	//TELA DE ALTERAÇÃO 

							$condominio = $this->cadastro->obtemCondominio($id_condominio);
							$bloco_condominio = $this->cadastro->obtemCondominioBloco(NULL, $id_bloco);
							
							//Atribui informações do condominio
							foreach($condominio as $chave => $valor) {
								$this->_view->atribui("condominio_$chave", $valor);								
							}
							
							//Atribui informacoes do bloco
							foreach($bloco_condominio as $chave => $valor) {
								$this->_view->atribui("$chave", $valor);								
							}
								
						} else if ($id_condominio) {
							
							$condominio = $this->cadastro->obtemCondominio($id_condominio);
							$this->_view->atribui("id_condominio", $id_condominio);
							
							//Atribui informações do condominio
							foreach($condominio as $chave => $valor) {
								$this->_view->atribui("condominio_$chave", $valor);								
							}
							
						}
						break;
				}
				
			}
			
		}
		
		
		protected function executaHelpdesk() {
		
			$this->_view->atribuiVisualizacao("helpdesk");	
			
			
			$id_condominio = @$_REQUEST["id_condominio"];
			$id_bloco = @$_REQUETS["id_bloco"];
			$id_chamado_pai = @$_REQUEST["id_chamado_pai"];
			$tela = @$_REQUEST["tela"];
			$subtela = @$_REQUEST["subtela"];
			$op = @$_REQUEST["op"];
			$acao = @$_REQUEST["acao"];			
			
			//Atribui informaçoes do condomínio atual
			$condominio = null;
			if($id_condominio) {
				$condominio = $this->cadastro->obtemCondominio($id_condominio);
				$this->_view->atribui("condominio", $condominio);
			}
			

			$dadosLogin = $this->_login->obtem("dados");
			$prioridades = $this->helpdesk->obtemPrioridades();
			
		
			//Atribuições
			$this->_view->atribui("id_condominio", $id_condominio);
			$this->_view->atribui("id_bloco", $id_bloco);
			$this->_view->atribui("tela", $tela);
			$this->_view->atribui("op", $op);
			
			$this->_view->atribui("prioridades", $prioridades);
			$this->_view->atribui("subtela", $subtela);
			$this->_view->atribui("dadosLogin", $dadosLogin);
			
			
			switch($tela) {
				case 'cadastro':
					
					if(!$acao) {
						$contas = VirtexModelo::factory("contas");
						
						$tipos = $this->helpdesk->obtemTiposChamado();
						$origens = $this->helpdesk->obtemOrigensChamado();
						$classificacoes = $this->helpdesk->obtemClassificacoesChamado();
						$status_chamado = $this->helpdesk->obtemStatusChamado();

						$grupos = $this->helpdesk->obtemListaGruposComPopulacao(true);
						$responsaveis = $this->helpdesk->obtemListaAdminGrupo();
						$blocos = $this->cadastro->obtemCondominioBloco($id_condominio);
						$prioridades = $this->helpdesk->obtemPrioridades();

						$this->_view->atribui("criado_por", $dadosLogin["id_admin"]);
						$this->_view->atribui("acao", "gravar");
						$this->_view->atribui("tipos", $tipos);
						$this->_view->atribui("blocos", $blocos);
						$this->_view->atribui("origens", $origens);
						$this->_view->atribui("classificacoes", $classificacoes);
						$this->_view->atribui("status_chamado", $status_chamado);
						$this->_view->atribui("grupos", $grupos);
						$this->_view->atribui("responsaveis", MJson::encode($responsaveis));
						$this->_view->atribui("prioridades", $prioridades);
					} else  {
					
						//Faz outras coisas
						$tipo = @$_REQUEST["tipo"];
						$criado_por = @$_REQUEST["criado_por"];
						$id_grupo = @$_REQUEST["id_grupo"];
						$assunto = @$_REQUEST["assunto"];
						$descricao = @$_REQUEST["descricao"];
						$origem = @$_REQUEST["origem"];
						$classificacao = @$_REQUEST["classificacao"];
						$responsavel= @$_REQUEST["responsavel"];
						$id_condominio = @$_REQUEST["id_condominio"];
						$id_bloco = @$_REQUEST["id_bloco"];

						$prioridade = @$_REQUEST["prioridade"];
						
						
						$id_condominio = $id_condominio ? $id_condominio: 0;
						$id_bloco = $id_bloco ? $id_bloco : 0;

						$responsavel = $responsavel ? $responsavel : null;					
						
						//if($id_conta) {
						//	$contas = VirtexModelo::factory("contas");
						//	$conta = $contas->obtemContaPeloId($id_conta);
						//	$id_cliente_produto = $conta["id_cliente_produto"];
						//}

						$id_chamado = $this->helpdesk->abreChamado($tipo,$criado_por,$id_grupo,$assunto,$descricao,$origem,$classificacao,$prioridade,$responsavel,0,0,0,0,0,0,0,$id_condominio,$id_bloco);						
						$confirma_chamado = $this->helpdesk->obtemChamadoPeloId($id_chamado);
						
						$mensagem = "";
						$url = "";
						
						if($confirma_chamado) {
							$mensagem = "Chamado criado com sucesso.";
							$url = "admin-cadastro.php?op=helpdesk&tela=alteracao&id_chamado=" . $confirma_chamado["id_chamado"] . "&id_condominio=" . $id_condominio;
						} else {
							$mensagem = "Erro ao criar o chamado.";
							$url = "admin-cadastro.php?op=helpdesk&tela=listagem&id_condominio=" . $id_condominio;
						}
						
						$this->_view->atribui("mensagem",$mensagem);
						$this->_view->atribui("url",$url);
						$this->_view->atribuiVisualizacao("msgredirect");
					
					}
					break;
					
					
				case 'alteracao':
					$id_chamado = $_REQUEST["id_chamado"];
				
					if(!$acao) {
					
						//Seleciona o chamado desejado
						$chamado = $this->helpdesk->obtemChamadoPeloId($id_chamado);
						$tmp_pref = VirtexModelo::factory("preferencias");

						if($chamado) {
							if($chamado["tipo"] == PERSISTE_HDTB_CHAMADO::$TIPO_CHAMADO && ($chamado["status"] == PERSISTE_HDTB_CHAMADO::$STATUS_RESOLVIDO || $chamado["status"] == PERSISTE_HDTB_CHAMADO::$STATUS_FECHADO)) {
								
								$preferencias = $this->preferencias->obtemPreferenciasHelpdesk();
								
								$data_fim = $chamado["fechamento"];
								$data_fim = substr($data_fim,0,10);
								$temp = explode('-', $data_fim);
								
								$data_fim = mktime(0,0,0,$temp[1],$temp[2],$temp[0]); 
								$data_hoje = time();
								
								$data_diferenca = $data_hoje - $data_fim;
								$data_diferenca = intval($data_diferenca / (60 * 60 * 24));
								
								if($data_diferenca < $preferencias["limite_tempo_reabertura_chamado"]) {
									$this->_view->atribui("pode_reabrir", true);
								}
							}
							
							//Confere a existência de determinados campos relacionados
							
							//CONDOMINIO
							if($chamado["id_condominio"]) {
								$info_condominio_chamado = $this->cadastro->obtemCondominio($chamado["id_condominio"]);
								$chamado["condominio_nome"] = @$info_condominio_chamado["nome"];	
								$chamado["condominio_fone"] = @$info_condominio_chamado["fone"];	
								$chamado["condominio_endereco"] = @$info_condominio_chamado["endereco"];
								$chamado["condominio_bairro"] = @$info_condominio_chamado["bairro"];
								
								$cidade = $tmp_pref->obtemCidadePeloID($info_condominio_chamado["id_cidade"]);
								
								$chamado["cidade"] = $cidade["cidade"] . " - " . $cidade["uf"];
							}
							
							if($chamado["id_bloco"]) {							
								$info_bloco_chamado = $this->cadastro->obtemCondominioBloco(null, $chamado["id_bloco"]);
								$chamado["bloco_nome"] = @$info_bloco_chamado["nome"];
							}							

							$pop_nome = NULL;
							if($chamado["id_bloco"]) {
								$bloco_info = $this->cadastro->obtemCondominioBloco(NULL, $chamado["id_bloco"]);
								$pop_nome = $bloco_info["popnome"];
								$chamado["condominio_pop"] = $pop_nome;
							}

							if($subtela == "imprimir_os") {
								$prefGeral = $this->preferencias->obtemPreferenciasGerais();

								$chamado["dns1"] = $prefGeral["hosp_ns1"];
								$chamado["dns2"] = $prefGeral["hosp_ns2"];
							}						
							
							$chamado["condominio_pop"] = $pop_nome;

							$pop_nome = null;
							if(@$info_condominio_chamado["id_pop"]) {
								$tmp_pop = $equipamentos->obtemPop($info_condominio_chamado["id_pop"]);
								$pop_nome = $tmp_pop["nome"];
								$chamado["condominio_pop"] = $pop_nome;
							}				

							
						}		
						
						$tipos = $this->helpdesk->obtemTiposChamado();
						$origens = $this->helpdesk->obtemOrigensChamado();
						$classificacoes = $this->helpdesk->obtemClassificacoesChamado();
						$status_chamado = $this->helpdesk->obtemStatusChamado();

						$array_grupos = $this->helpdesk->obtemListaGrupos(array("ativo" => "t"));
						$array_responsaveis = $this->helpdesk->obtemListaAdminGrupo();
						
						$historico_chamado = $this->helpdesk->obtemHistoricoChamado($id_chamado);
						
						//matriz de responsáveis(remake)
						$responsaveis = array();
						foreach($array_responsaveis as $chave => $valor) {
							$responsaveis[$valor["id_admin"]] = $valor["admnome"];
						}
						
						//matriz de grupos(remake)
						$grupos = array();
						foreach($array_grupos as $chave => $valor) {
							$grupos[$valor["id_grupo"]] = $valor["nome"];
						}
						
						
						//confirma se o usuário pertence ao grupo
						$admin_grupo = false;
						$pertence_grupo = false;
						$admin_usuario = $this->helpdesk->obtemListaAdminGrupo($chamado["id_grupo"], $dadosLogin["id_admin"]);
						$grupo_usuarios = $this->helpdesk->obtemListaAdminGrupo($chamado["id_grupo"] );
						$os_pendentes = $this->helpdesk->obtemOrdemPedidoPendentesPorChamado($id_chamado);
						$os_finalizados = $this->helpdesk->obtemOrdemPedidoFinalizadasPorChamado($id_chamado);
						$ordens_servico_chamado = array_merge($os_pendentes, $os_finalizados);
						
						
						if(count($admin_usuario)) {
							if($admin_usuario["ativo"]) {
								$pertence_grupo = true;
							}
							
							if($admin_usuario["admin"] && $admin_usuario["ativo"]) { 
								$admin_grupo = true;
							}	
						}
						
						
						$periodos = $this->helpdesk->obtemPeriodos();
						$info_os = $this->helpdesk->obtemOrdemServicoPeloIdChamado($chamado["id_chamado"]);
						
						$caracterizacao = $this->helpdesk->obtemCaracterizacao();
						
						$this->_view->atribui("caracterizacao", $caracterizacao);	

						$this->_view->atribui("periodos", $periodos);
						$this->_view->atribui("os_pendentes", $os_pendentes);
						$this->_view->atribui("os_finalizados", $os_finalizados);						
						$this->_view->atribui("ordens_servico_chamado", $ordens_servico_chamado);	
						
						$this->_view->atribui("grupo_usuarios", $grupo_usuarios);
						$this->_view->atribui("usuario_grupo", $pertence_grupo);
						$this->_view->atribui("admin_grupo", $admin_grupo);
						$this->_view->atribui("historico_chamado", $historico_chamado);
						$this->_view->atribui("chamado", $chamado);
						$this->_view->atribui("info_os", $info_os);
						$this->_view->atribui("criado_por", $dadosLogin["id_admin"]);
						$this->_view->atribui("tipos", $tipos);
						$this->_view->atribui("origens", $origens);
						$this->_view->atribui("classificacoes", $classificacoes);
						$this->_view->atribui("status_chamado", $status_chamado);
						$this->_view->atribui("grupos", $grupos);
						$this->_view->atribui("responsaveis", $responsaveis); 
						
						//Ações extras caso seja ordem de serviços
						if($subtela == "ordemservico") {
						
							//Cria matriz de ids de contas
							$enderecos_cobranca = array();
							$enderecos_instalacao = array();							

							$tipos = $this->helpdesk->obtemTiposChamado();
							$origens = $this->helpdesk->obtemOrigensChamado();
							$classificacoes = $this->helpdesk->obtemClassificacoesChamado();
							$status_chamado = $this->helpdesk->obtemStatusChamado();
							$blocos = $this->cadastro->obtemCondominioBloco(@$chamado["id_condominio"]);

							$grupos = $this->helpdesk->obtemListaGrupos(array("ativo" => "t"));
							
							$responsaveis = $this->helpdesk->obtemListaAdminGrupo();
							$periodos = $this->helpdesk->obtemPeriodos();
							
							if($id_chamado) $this->_view->atribui("acao","gravar");
							else $this->_view->atribui("acao","alterar");														
							
							$this->_view->atribui("periodos", $periodos);
							$this->_view->atribui("enderecos_cobranca", MJson::encode($enderecos_cobranca));
							$this->_view->atribui("enderecos_instalacao", MJson::encode($enderecos_instalacao));
							$this->_view->atribui("grupos", $grupos);
							$this->_view->atribui("blocos", $blocos);
							$this->_view->atribui("responsaveis", MJson::encode($responsaveis));
						}						
						
					} else {
					
						//Faz outras coisas
						$tipo = @$_REQUEST["tipo"];
						$senha_admin = @$_REQUEST["senha_admin"];
						$subtela = @$_REQUEST["subtela"];
						
						$erro="";
						$mensagem="";
						$url_redir="";
						$this->_view->atribui("id_chamado", $id_chamado);
						$this->_view->atribui("id_condominio", $id_condominio);
	
						if ($subtela == "ordemservico") {
							
							$tipo = @$_REQUEST["tipo"];
							$criado_por = @$_REQUEST["criado_por"];
							$id_grupo = @$_REQUEST["id_grupo"];
							$assunto = @$_REQUEST["assunto"];
							$descricao = @$_REQUEST["descricao"];
							$origem = @$_REQUEST["origem"];
							$classificacao = @$_REQUEST["classificacao"];
							$responsavel= @$_REQUEST["responsavel"];
							$id_condominio = @$_REQUEST["id_condominio"];
							$id_bloco = @$_REQUEST["id_bloco"];	
							$prioridade = @$_REQUEST["prioridade"];
							
							$agendamento = @$_REQUEST["agendamento"];
							$periodo = @$_REQUEST["periodo"];
							$endereco_os = @$_REQUEST["endereco_os"];
							$complemento_os = @$_REQUEST["complemento_os"];
							$bairro_os = @$_REQUEST["bairro_os"];
							$cidade_os = @$_REQUEST["cidade_os"];
							
							$id_condominio_os = @$_REQUEST["id_condominio"];
							$id_bloco_os = @$_REQUEST["id_bloco"];
							$apto = @$_REQUEST["apto"];
							
							
							//Entra procedimento aqui para adquirir o nome do condominio e as informações necessárias para o seu funcionamento;
							
							$id_chamado = $this->helpdesk->abreChamado($tipo,$criado_por,$id_grupo,$assunto,$descricao,$origem,$classificacao,$prioridade,$responsavel,0,0,0,0,0,0,0,$id_condominio,$id_bloco, $id_chamado_pai);						
							$confirma_chamado = $this->helpdesk->obtemChamadoPeloId($id_chamado);
							
							
							if($confirma_chamado) {
								$data_agendamento = null;
								if($agendamento){ 
									$data_tmp = explode("/", $agendamento);
									$data_agendamento = $data_tmp[2] . "-" . $data_tmp[1] . "-" . $data_tmp[0];
								}
								
								$this->helpdesk->registrarOrdemServico($id_chamado, $endereco_os, $complemento_os, $bairro_os, $cidade_os, $data_agendamento, $periodo);							
							
								$url_redir = "admin-cadastro.php?op=helpdesk&tela=alteracao&id_condominio=$id_condominio&id_chamado=$id_chamado_pai";
								$mensagem = "Ordem de serviço criada com sucesso";
							} else {
								$mensagem = "Erro ao criar a ordem de serviço";
								$url_redir = "admin-cadastro.php?op=helpdesk&tela=alteracao&id_condominio=$id_condominio&id_chamado=$id_chamado_pai";
							}
							
							
							//if($agendamento && $confirma_chamado) {
							//	$data_tmp = explode("/", $agendamento);
							//	$data_agendamento = $data_tmp[2] . "-" . $data_tmp[1] . "-" . $data_tmp[0];
							//	$this->helpdesk->registrarOrdemServico($id_chamado, $endereco_os, $complemento_os, $bairro_os, $cidade_os, $data_agendamento, $periodo);
							//}
							
						
						
						} else {
						
							if($dadosLogin["senha"] != md5($senha_admin)) {
								$erro = "Senha não confere";
							}

							$this->_view->atribui("erro", $erro);
							$this->_view->atribui("acao", "alteracao");
							
							$url_redir = "admin-cadastro.php?op=helpdesk&tela=alteracao&id_chamado=$id_chamado&id_condominio=$id_condominio";
							
							if($erro) {
								$mensagem="Operação não permitida: Senha não confere";
							} else {

								switch($acao) {
									case 'comentar':
										$comentario = @$_REQUEST["comentario"];
										$this->helpdesk->adicionaHistoricoChamado($id_chamado,"Comentário",$comentario,$dadosLogin["id_admin"]);
										$mensagem = "Comentário efetuado com sucesso";
										break;

									case 'delegar':
										$responsavel = @$_REQUEST["responsavel"];
										$novoresponsavel = @$_REQUEST["novoresponsavel"];
										$this->helpdesk->alteraResponsavelChamado($id_chamado,$dadosLogin["id_admin"], $novoresponsavel);
										$this->helpdesk->alteraStatus($id_chamado, PERSISTE_HDTB_CHAMADO::$STATUS_ABERTO, $dadosLogin["id_admin"]);
										$mensagem = "Delegação efetuada com sucesso.";
										break;	
									case 'pegar':
										$responsavel = @$_REQUEST["responsavel"];
										$this->helpdesk->alteraResponsavelChamado($id_chamado,$dadosLogin["id_admin"], $responsavel);
										$this->helpdesk->alteraStatus($id_chamado, PERSISTE_HDTB_CHAMADO::$STATUS_ABERTO, $dadosLogin["id_admin"]);
										$mensagem = "Tomada de posse de chamado efetuada com sucesso.";
										break;	
										
									case 'resolver':
										$novostatus = @$_REQUEST["novostatus"];
										$comentario = @$_REQUEST["comentariofim"];

										if($novostatus == PERSISTE_HDTB_CHAMADO::$STATUS_PENDENTE || $novostatus == PERSISTE_HDTB_CHAMADO::$STATUS_PENDENTE_CLI || $novostatus == PERSISTE_HDTB_CHAMADO::$STATUS_ABERTO) {
											$this->helpdesk->alteraStatus($id_chamado, $novostatus, $dadosLogin["id_admin"], $comentario);
										} 
										//RESOLVIDO
										else if($novostatus == PERSISTE_HDTB_CHAMADO::$STATUS_RESOLVIDO) {
											$this->helpdesk->finalizaChamado($id_chamado, $resolvido=true, $dadosLogin["id_admin"], $comentario);
										} 
										//FECHADO
										else if($novostatus == PERSISTE_HDTB_CHAMADO::$STATUS_FECHADO) {
											$this->helpdesk->finalizaChamado($id_chamado, $resolvido=true, $dadosLogin["id_admin"], $comentario);
										}

										$mensagem = "Status do chamado atualizado com sucesso";

										if($novostatus = "F" || $novostatus = "OK"){ 

											$id_chamado = @$_REQUEST["id_chamado"]; 

											$data_execucao = @$_REQUEST["data_execucao"]; 
											if($data_execucao){
												$temp = explode("/", $data_execucao);
												$data_execucao = "$temp[2]-$temp[1]-$temp[0]";
											}

											$horario_chegada = @$_REQUEST["horario_chegada"]; 
											$horario_saida = @$_REQUEST["horario_saida"]; 
											$caracterizacao = @$_REQUEST["caracterizacao"]; 
											$icmp_ip = @$_REQUEST["icmp_ip"]; 
											$icmp_media = @$_REQUEST["icmp_media"]; 
											$icmp_minimo = @$_REQUEST["icmp_minimo"]; 
											$ftp_ip = @$_REQUEST["ftp_ip"]; 
											$ftp_media = @$_REQUEST["ftp_media"]; 
											$ftp_minimo = @$_REQUEST["ftp_minimo"];

											$this->helpdesk->registrarVisitaTecnica($id_chamado, $data_execucao, $horario_chegada, $horario_saida, $caracterizacao, $icmp_ip, $icmp_media, $icmp_minimo, $ftp_ip, $ftp_media, $ftp_minimo);
											//$mensagem = "Dados da visita técnica atualizados com sucesso.";
										}
										break;
										
									case 'priorizar':
										$prioridade = @$_REQUEST["prioridade"];
										$comentario = @$_REQUEST["prioridade_comentario"];
										$this->helpdesk->alteraPrioridade($id_chamado, $prioridade, $dadosLogin["id_admin"], $comentario);
										$mensagem = "Alteraçao de prioridade do chamado efetuada com sucesso.";
										
									case 'reabrir':
										$comentario = @$_REQUEST["comentario_reabertura"];
										$this->helpdesk->reabreChamado($id_chamado, $dadosLogin["id_admin"], $comentario);
										$mensagem = "Chamado reaberto com sucesso";
										break;
								}

							}

						}
						
						$this->_view->atribui("mensagem",$mensagem);
						$this->_view->atribui("url",$url_redir);
						$this->_view->atribuiVisualizacao("msgredirect");						
						
					} 
					break; 
				
				
				case 'listagem':
				default:
					$chamados_pendentes = $this->helpdesk->obtemChamadosPendentesPorCondominio($id_condominio);				
					$chamados_terminados = $this->helpdesk->obtemChamadosFinalizadosPorCondominio($id_condominio);
					$tipos_chamado = $this->helpdesk->obtemTiposChamado();
					
					$array_grupos = $this->helpdesk->obtemListaGrupos();
					$array_responsaveis = $this->helpdesk->obtemListaAdminGrupo();					
					
					//matriz de responsáveis(remake)
					$responsaveis = array();
					foreach($array_responsaveis as $chave => $valor) {
						$responsaveis[$valor["id_admin"]] = $valor["admnome"];
					}

					//matriz de grupos(remake)
					$grupos = array();
					foreach($array_grupos as $chave => $valor) {
						$grupos[$valor["id_grupo"]] = $valor["nome"];
					}
					

					$this->_view->atribui("responsaveis",$responsaveis);
					$this->_view->atribui("grupos",$grupos);
					$this->_view->atribui("tipos_chamado",$tipos_chamado);
					$this->_view->atribui("chamados_pendentes",$chamados_pendentes);
					$this->_view->atribui("chamados_terminados",$chamados_terminados);				
					break;
			}
	
		}
	
	}
	
?>
