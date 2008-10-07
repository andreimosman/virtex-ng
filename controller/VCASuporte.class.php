<?
	class VCASuporte extends VirtexControllerAdmin {
	
		protected $helpdesk;
	
		public function __construct() {
			parent::__construct();
		}
		
		public function init() {
			parent::init();
			$this->_view = VirtexViewAdmin::factory("suporte");
			$this->helpdesk = VirtexModelo::factory("helpdesk");
		}
		
		protected function executa() {
			switch($this->_op) {
				case 'ferramentas':
					$this->executaFerramentas();
					break;
				case 'monitoramento':
					$this->executaMonitoramento();
					break;
				case 'graficos':
					$this->executaGraficos();
					break;
				case 'links':
					$this->executaLinks();
					break;
				case 'relatorios':
					$this->executaRelatorios();
					break;
				case 'helpdesk':
					$this->executaHelpdesk();
					break;					
				default:
					// do something
			}
		}
		
		protected function executaFerramentas() {
			$ferramenta = @$_REQUEST["ferramenta"];
			
			$this->_view->atribuiVisualizacao("ferramentas");
			$this->_view->atribui("ferramenta",$ferramenta);
			
			switch($ferramenta) {
				case 'ipcalc':
					$this->executaIPCalc();
					break;
				case 'arp':
					$this->executaARP();
					break;
				case 'ping':
					$this->executaPING();
					break;
			
			}
			
		}
		
		protected function executaIPCalc() {
			$this->requirePrivLeitura("_SUPORTE_FERRAMENTAS_CALCULADORA_IP");
		
			$ip = @$_REQUEST["ip"];
			$mascara = @$_REQUEST["mascara"];
			
			$this->_view->atribui("ip",$ip);
			$this->_view->atribui("mascara",$mascara);
			
			if( $ip && $mascara ) {
				try {
					$info = MInet::calculadora($ip,$mascara);
					$this->_view->atribui("info",$info->toArray());
				} catch(MException $e) {
					$this->_view->atribui("erro",$e->getMessage());
				} catch(Exception $e) {
					$this->_view->atribui("erro",$e->getMessage());
				}
			}		
		}
		
		protected function executaARP() {
			$this->requirePrivLeitura("_SUPORTE_FERRAMENTAS_ARP");
		
			$equipamentos = VirtexModelo::factory('equipamentos');
			$servidores = $equipamentos->obtemListaServidores(true);
			
			$this->_view->atribui("servidores",$servidores);
			
			$id_servidor = @$_REQUEST["id_servidor"];
			
			if( $id_servidor ) {
				$servidor = $equipamentos->obtemServidor($id_servidor);
				$this->_view->atribui("servidor",$servidor);
				
				$ip = @$_REQUEST["ip"];
				$this->_view->atribui("ip",$ip);
				
				if( !$ip ) $ip = "-a";
				
				$conn = new VirtexCommClient(VirtexComm::$INC_ARP);
				if(@!$conn->open($servidor["ip"],$servidor["porta"],$servidor["chave"],$servidor["usuario"],$servidor["senha"])) {
					$erro = "Erro de conexão com o servidor";
					$this->_view->atribui("erro",$erro);
				} else {
					if( $conn->estaConectado() ) {
						$tabelaARP = $conn->getARP($ip);
						$this->_view->atribui("tabelaARP",$tabelaARP);
					}
				
				}
			
			}
		}
		
		protected function executaPing() {
			$this->requirePrivLeitura("_SUPORTE_FERRAMENTAS_PING");
			
			$equipamentos = VirtexModelo::factory('equipamentos');
			$servidores = $equipamentos->obtemListaServidores(true);
			
			$this->_view->atribui("servidores",$servidores);
			
			$id_servidor = @$_REQUEST["id_servidor"];
			
			$pacotes = @$_REQUEST["pacotes"];
			$tamanho = @$_REQUEST["tamanho"];
			
			if( !$pacotes ) $pacotes = 4;
			if( !$tamanho ) $tamanho = 32;
			
			$this->_view->atribui("pacotes",$pacotes);
			$this->_view->atribui("tamanho",$tamanho);
			
			$ip = @$_REQUEST["ip"];			
			$this->_view->atribui("ip",$ip);
			
			if( $id_servidor ) {
			
				if( !$ip ) {
					$erro = "Endereço IP não fornecido.";
					$this->_view->atribui("erro",$erro);
				} else {
			
					$servidor = $equipamentos->obtemServidor($id_servidor);
					$this->_view->atribui("servidor",$servidor);

					$conn = new VirtexCommClient(VirtexComm::$INC_PING);
					if(@!$conn->open($servidor["ip"],$servidor["porta"],$servidor["chave"],$servidor["usuario"],$servidor["senha"])) {
						// echo "ERRO DE CONEXÃO\n\n";
						$erro = "Erro de conexão com o servidor";
						$this->_view->atribui("erro",$erro);
					} else {
						if( $conn->estaConectado() ) {
							$ping = $conn->getFPING($ip,$pacotes,$tamanho);
							$this->_view->atribui("ping",$ping);
						}

					}
				}
			
			}
		
		}
		
		
		protected function executaMonitoramento() {
			$this->requirePrivLeitura("_SUPORTE_MONITORAMENTO");
			
			$this->_view->atribuiVisualizacao("monitoramento");
			$tela = @$_REQUEST["tela"];
			$this->_view->atribui("tela",$tela);

			$equipamentos = VirtexModelo::factory('equipamentos');
			$registros = $equipamentos->obtemListaPOPs();
			
			//
			$resumo = array("ERR" => 0, "WRN" => 0, "OK" => 0);
			
			for($i=0;$i<count($registros);$i++) {
				if($registros[$i]["ativar_monitoramento"] == 't' && $registros[$i]["ipaddr"]) {
					$status = $equipamentos->obtemMonitoramentoPop($registros[$i]["id_pop"]);
					
					$registros[$i] = array_merge($registros[$i],$status);
					$registros[$i]["st_mon"] = $status["status"];
					if( $status["status"] ) {
						$st = $status["status"] == "IER" ? "ERR" : $status["status"];
						$resumo[ $st ]++;
					}
				}
			}
			
			$this->_view->atribui("resumo",$resumo);			
			$this->_view->atribui("registros",$registros);
		
		}
		
		protected function executaGraficos() {
			$this->requirePrivLeitura("_SUPORTE_GRAFICOS");
			
			$this->_view->atribuiVisualizacao("graficos");
			
			$contas			= VirtexModelo::factory('contas');
			$equipamentos 	= VirtexModelo::factory('equipamentos');
			
			$listaPOPs 		= $equipamentos->obtemListaPOPOrdemAlfabetica();
			$listaNAS		= $equipamentos->obtemListaNAS();
			
			$id_nas			= @$_REQUEST["id_nas"];
			$id_pop			= @$_REQUEST["id_pop"];
						
			$this->_view->atribui("listaPOPs",$listaPOPs);
			$this->_view->atribui("listaNAS",$listaNAS);
			
			$this->_view->atribui("id_nas",$id_nas);
			$this->_view->atribui("id_pop",$id_pop);
			
			if( $id_pop || $id_nas ) {
				// echo "PESQUISAR";	
				$listaContas = $contas->obtemContasBandaLargaPeloPOPNAS($id_pop,$id_nas,"A");
				$this->_view->atribui("listaContas",$listaContas);
				
				if( !count($listaContas) ) {
					$this->_view->atribui("erro","Nenhuma conta ativa satisfaz as condições de pesquisa.");
				}
			
			}
			
		}
		
		protected function executaLinks() {
			$this->requirePrivLeitura("_SUPORTE_LINKS");
			$this->_view->atribuiVisualizacao("links");
			
			$links = $this->preferencias->obtemListaLinks();
			$this->_view->atribui("links",$links);
			
		}
		
		protected function executaRelatorios() {
			$this->requirePrivLeitura("_SUPORTE_RELATORIOS");
			$relatorio = @$_REQUEST["relatorio"];
			$tipo = @$_REQUEST["tipo"];
			$this->_view->atribuiVisualizacao("relatorios");
			$this->_view->atribui("relatorio",$relatorio);
			$this->_view->atribui("tipo",$tipo);
			
			switch($relatorio) {
				case 'cliente_sem_mac':
					$contas = VirtexModelo::factory('contas');
					$lista = $contas->obtemContasSemMac();
					$url = "admin-suporte.php?op=relatorios&relatorio=cliente_sem_mac";
			
					$podeGravarConta = $this->requirePrivGravacao("_CLIENTES_BANDALARGA",false);
					$this->_view->atribui("podeGravarConta",$podeGravarConta);

					$bloquear = @$_REQUEST["bloquear"];
					$acao = @$_REQUEST["acao"];
					
					if( $acao == "bloquear" ) {
							$this->requirePrivGravacao("_CLIENTES_BANDALARGA");
							if( $bloquear ) {
								try {
									$senha_admin = @$_REQUEST["senha_admin"];
									$dadosLogin = $this->_login->obtem("dados");
									if( !$senha_admin ) {
										$erro = "Cancelamento não autorizado: SENHA NÃO FORNECIDA.";
									} elseif (md5(trim($senha_admin)) != $dadosLogin["senha"] ) {
										$erro = "Operação não autorizada: SENHA NÃO CONFERE.";								
									}
									if($erro) throw new Exception($erro);
									
									foreach( $bloquear as $id_conta => $lixo ) {
										$contas->alteraContaBandaLarga($id_conta, NULL, 'B');
									}
								
									$this->_view->atribui("url",$url);
									$this->_view->atribui("mensagem","Dados atualizados com sucesso!");
									$this->_view->atribuiVisualizacao("msgredirect");
									
									if( $acao ) {
										$erro = "";										
									} else {
										VirtexView::simpleRedirect($url);
									}
								
						
							}catch (ExcecaoModeloValidacao $e ) {
								$this->_view->atribui ("msg_erro", $e->getMessage());
								$erro = true;	
							} catch (Exception $e ) {
								$this->_view->atribui ("msg_erro", $e->getMessage());
								$erro = true;	
							}
					}
				}
					$this->_view->atribui("lista",$lista);
					break;
				case 'banda':
					
					$contas = VirtexModelo::factory('contas');
					
					$banda = isset($_REQUEST["banda"]) ? $_REQUEST["banda"] : null;
					$this->_view->atribui("banda",$banda);
					if( is_null($banda) ) {
						// Lista Geral
						$lista = $this->preferencias->obtemListaBandas();
						for($i=0;$i<count($lista);$i++) {
							$listaContas = $contas->obtemContasPorBanda($lista[$i]["id"]);
							$lista[$i]["num_contas"] = count($listaContas);
						}
						$this->_view->atribui("lista",$lista);
					} else {
						$infoBanda = $this->preferencias->obtemBanda($banda);
						$this->_view->atribui("infoBanda",$infoBanda);
						
						$listaContas = $contas->obtemContasPorBanda($banda);
						$this->_view->atribui("listaContas",$listaContas);
					}
										
					break;
				
				case 'helpdesk':
				
					switch($tipo) {
						case 'ocorrencias':
						default:
							$de = @$_REQUEST["de"];
							$ate = @$_REQUEST["ate"];
							
							
							
							if(!$ate) {
								$ate = Date("d/m/Y");
							}
							
							if(!$de) {
								$dt = explode("/", $ate);
								$tstamp = mktime(0,0,0,$dt[1], $dt[0], $dt[2]);
								$tstamp -= (60 * 60 * 24 * 30);
								$de = Date("d/m/Y", $tstamp);						
							}
							
							$dt = explode("/", $de);
							$data_inicial = $dt[2] . "-" . $dt[1] . "-" . $dt[0];
							$dt = explode("/", $ate);
							$data_final = $dt[2] . "-" . $dt[1] . "-" . $dt[0];
							
							
							
							$retorno = $this->helpdesk->obtemChamadosPorPeriodo($data_inicial, $data_final);
													
							$this->_view->atribui("chamados", $retorno);							
							$this->_view->atribui("de", $de);
							$this->_view->atribui("ate", $ate);
							
							break;
							
					}
				
					break;

			}
			
		}
		
		
		
		protected function executaHelpdesk() {
		
			$tela = @$_REQUEST["tela"];
			$subtela = @$_REQUEST["subtela"];
			
			$this->_view->atribuiVisualizacao("helpdesk");
			
			$id_chamado_pai = @$_REQUEST["id_chamado_pai"];
			$tela = @$_REQUEST["tela"];
			$subtela = @$_REQUEST["subtela"];
			$op = @$_REQUEST["op"];
			$acao = @$_REQUEST["acao"];
			
			$dadosLogin = $this->_login->obtem("dados");
			
			$prioridades = $this->helpdesk->obtemPrioridades();
			
			$origens = $this->helpdesk->obtemOrigensChamado();
			
			$this->_view->atribui("origens", $origens);
			$this->_view->atribui("prioridades", $prioridades);
			$this->_view->atribui("subtela", $subtela);
			$this->_view->atribui("dadosLogin", $dadosLogin);
			
			
			$this->_view->atribuiVisualizacao("helpdesk");
			$this->_view->atribui("tela",$tela);
			$this->_view->atribui("op", $op);
			$this->_view->atribui("subtela",$subtela);
			
			$dadosLogin = $this->_login->obtem("dados");

			$classes = $this->helpdesk->obtemListaClasses();
			$this->_view->atribui("classes",$classes);

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
						
						$prioridades = $this->helpdesk->obtemPrioridades();

						$this->_view->atribui("chamados_pendentes", $chamados_pendentes);
						$this->_view->atribui("criado_por", $dadosLogin["id_admin"]);
						$this->_view->atribui("acao", "gravar");
						$this->_view->atribui("tipos", $tipos);
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
						$prioridade = @$_REQUEST["prioridade"];
						
						$id_classe = @$_REQUEST["id_classe"];
						
						$responsavel = $responsavel ? $responsavel : null;					

						$id_chamado = $this->helpdesk->abreChamado($tipo,$criado_por,$id_grupo,$assunto,$descricao,$origem,$classificacao,$prioridade,$responsavel,$id_classe);
						$confirma_chamado = $this->helpdesk->obtemChamadoPeloId($id_chamado);
						
						$mensagem = "";
						$url = "";
						
						if($confirma_chamado) {
							$mensagem = "Chamado criado com sucesso.";
							$url = "admin-suporte.php?op=helpdesk&tela=alteracao&id_chamado=$id_chamado";
						} else {
							$mensagem = "Erro ao criar o chamado.";
							$url = "admin-suporte.php?op=desktop&tela=listagem";
						}
						
						$this->_view->atribui("mensagem",$mensagem);
						$this->_view->atribui("url",$url);
						$this->_view->atribuiVisualizacao("msgredirect");
					}					
				
					break;
				

				case 'alteracao': 		//Alteração de chamados existentes
				
				
					$id_chamado = $_REQUEST["id_chamado"];
					$this->_view->atribui("id_chamado", $id_chamado);
				
					if(!$acao) {
					
						//Seleciona o chamado desejado
						$chamado = $this->helpdesk->obtemChamadoPeloId($id_chamado);
						

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
							
						}
						
						if($subtela == "imprimir_os") {
							$prefGeral = $this->preferencias->obtemPreferenciasGerais();

							$chamado["dns1"] = $prefGeral["hosp_ns1"];
							$chamado["dns2"] = $prefGeral["hosp_ns2"];
						}
						


						$contas = VirtexModelo::factory("contas");
						$cobranca = VirtexModelo::factory("cobranca");		
						
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

							$grupos = $this->helpdesk->obtemListaGruposComPopulacao(true);							
							$responsaveis = $this->helpdesk->obtemListaAdminGrupo();
							$periodos = $this->helpdesk->obtemPeriodos();
							
							if($id_chamado) $this->_view->atribui("acao","gravar");
							else $this->_view->atribui("acao","alterar");														
							
							$this->_view->atribui("periodos", $periodos);

							$this->_view->atribui("grupos", $grupos);
							$this->_view->atribui("contas_cliente", $contas_cliente);
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

	
						if ($subtela == "ordemservico") {
						
							
							$tipo = @$_REQUEST["tipo"];
							$criado_por = @$_REQUEST["criado_por"];
							$id_grupo = @$_REQUEST["id_grupo"];
							$assunto = @$_REQUEST["assunto"];
							$descricao = @$_REQUEST["descricao"];
							$origem = @$_REQUEST["origem"];
							$classificacao = @$_REQUEST["classificacao"];
							$responsavel= @$_REQUEST["responsavel"];

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
							
							$id_classe = @$_REQUEST["id_classe"];
							
							//Entra procedimento aqui para adquirir o nome do condominio e as informações necessárias para o seu funcionamento;
							
							
							
							$id_chamado = $this->helpdesk->abreChamado($tipo,$criado_por,$id_grupo,$assunto,$descricao,$origem,$classificacao,$prioridade,$responsavel,$id_classe,0,0,0,0,0,0,0,0,0,$id_chamado_pai);
							$confirma_chamado = $this->helpdesk->obtemChamadoPeloId($id_chamado);
							
							if($confirma_chamado) {
								$data_agendamento = null;
								if($agendamento){ 
									$data_tmp = explode("/", $agendamento);
									$data_agendamento = $data_tmp[2] . "-" . $data_tmp[1] . "-" . $data_tmp[0];
								}
								
								$this->helpdesk->registrarOrdemServico($id_chamado, $endereco_os, $complemento_os, $bairro_os, $cidade_os, $data_agendamento, $periodo, $id_classe);
							
								$url_redir = "admin-suporte.php?op=helpdesk&tela=alteracao&id_chamado=$id_chamado_pai";
								$mensagem = "Ordem de serviço criada com sucesso";
							} else {
								$mensagem = "Erro ao criar a ordem de serviço";
								$url_redir = "admin-suporte.php?op=helpdesk&tela=alteracao&id_chamado=$id_chamado_pai";
							}
							
						
						} else {
						
							if($dadosLogin["senha"] != md5($senha_admin)) {
								$erro = "Senha não confere";
							}

							$this->_view->atribui("erro", $erro);
							$this->_view->atribui("acao", "alteracao");
							
							$url_redir = "admin-suporte.php?op=helpdesk&tela=alteracao&id_chamado=$id_chamado";
							
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
				
					$chamados_pendentes = $this->helpdesk->obtemChamadosPendentesPeloResponsavel($dadosLogin["id_admin"]);
					
					if($subtela == "mini") {		
						$quant_chamados_usuario = $this->helpdesk->obtemChamadosPendentesPeloResponsavel($dadosLogin["id_admin"]);
						$quant_chamados = 0;
						$quant_os = 0;
						
						for($i=0; $i<count($quant_chamados_usuario); $i++) {
							if($quant_chamados_usuario[$i]["tipo"] == "CH") 
								$quant_chamados++;
							else if($quant_chamados_usuario[$i]["tipo"] == "OS")
								$quant_os++;
						}
						
						$quant_chamados_grupo = $this->helpdesk->obtemQuantidadeChamadosAbertosGruposUsuario($dadosLogin["id_admin"]);
						$this->_view->atribui("quant_chamados", $quant_chamados);
						$this->_view->atribui("quant_os", $quant_os);
						$this->_view->atribui("quant_chamados_grupo", $quant_chamados_grupo["chamados"]);
										
					}

					$chamados_usuario = array();
					$os_usuario = array();

					foreach($chamados_pendentes as $chave => $valor) {
						if($valor["tipo"] == "OS")
							array_push($os_usuario, $valor);
						else if($valor["tipo"] == "CH")
							array_push($chamados_usuario, $valor);
					}


					$array_grupos = $this->helpdesk->obtemListaGrupos();
					$array_responsaveis = $this->helpdesk->obtemListaAdminGrupo();				


					//Agrupa todos os chamados dos grupos que o administrador participa
					$grupos_pertencentes = $this->helpdesk->obtemListaGruposPertencentesAdmin($dadosLogin["id_admin"], 't');					
					$chamados_por_grupo = array();

					foreach($grupos_pertencentes as $chave => $valor) {
						$chamados_por_grupo[$valor["id_grupo"]] = array("nome" => $valor["nome"], "chamados" => array());
						$chamados_grupo = $this->helpdesk->obtemChamadosPendentesPeloGrupo($valor["id_grupo"]);

						foreach($chamados_grupo as $chaveg => $valorg) {
							if($valorg["responsavel"] != $dadosLogin["id_admin"]) {
								$chamados_por_grupo[$valorg["id_grupo"]]["chamados"][] = $valorg;
							}
						}						

					}				

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

					$this->_view->atribui("chamados_por_grupo",$chamados_por_grupo);
					$this->_view->atribui("responsaveis",$responsaveis);
					$this->_view->atribui("grupos",$grupos);
					//$this->_view->atribui("tipos_chamado",$tipos_chamado);
					//$this->_view->atribui("chamados_terminados",$chamados_terminados);
					$this->_view->atribui("chamados_pendentes", $chamados_pendentes);
					$this->_view->atribui("chamados_usuario", $chamados_usuario);
					$this->_view->atribui("os_usuario", $os_usuario);
					break;

			}
			
			
			

		}		
		
		
	}



