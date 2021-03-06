<?

	class VVACadastro extends VirtexViewAdmin {

		public function __construct() {
			parent::__construct();

			$this->configureMenu(array(),false,false);	// Configura um menu vazio

		}
		
		protected function init() {
			parent::init();
			$this->nomeSessao = "Cadastro";
		}


		protected function obtemItensMenu($op) {
			$itensMenu = array();
			switch($op) {
				case 'servidores':
					$itensMenu[] = array("texto" => "Novo Servidor", "url" => "admin-cadastro.php?op=equipamentos&tela=servidores&subtela=cadastro");
					break;
				case 'pops':
					$itensMenu[] = array("texto" => "Novo POP", "url" => "admin-cadastro.php?op=equipamentos&tela=pops&subtela=cadastro");
					break;
				case 'nas':
					$itensMenu[] = array("texto" => "Novo NAS", "url" => "admin-cadastro.php?op=equipamentos&tela=nas&subtela=cadastro");
					break;					
				case 'mikrotik':
					$itensMenu[] = array("texto" => "Novo Mikrotik", "url" => "admin-cadastro.php?op=equipamentos&tela=mikrotik&subtela=cadastro");
					break;					
			}
			return($itensMenu);
		}
		
		
		protected function obtemItensMenuPlanos() {
			$itensMenu = array();

			$itensMenu[] = array("texto" => "Novo: Banda Larga", "url" => "admin-cadastro.php?op=planos&tela=cadastro&tipo=BL");
			$itensMenu[] = array("texto" => "Novo: Discado", "url" => "admin-cadastro.php?op=planos&tela=cadastro&tipo=D");
			$itensMenu[] = array("texto" => "Novo: Hospedagem", "url" => "admin-cadastro.php?op=planos&tela=cadastro&tipo=H");

			return($itensMenu);
		}
		
		protected function obtemItensMenuAdministradores() {
			$itensMenu[] = array("texto" => "Novo: Administrador", "url"=> "admin-cadastro.php?op=administradores&tela=cadastro");
			return $itensMenu;
		}
		
		
		protected function obtemItensMenuNASRedes($id_nas,$exibir_enderecos="") {
			$itensMenu = array();
			$itensMenu[] = array("texto" => ($exibir_enderecos?"Cadastrar Enderešo":"Exibir Enderešos"), "url" => "admin-cadastro.php?op=equipamentos&tela=nas&subtela=redes&id_nas=".$id_nas."&exibir_enderecos=".($exibir_enderecos?"":"1"));
			return($itensMenu);
		}
		
		
		protected function obtemItensMenuCondominio($tela="") {
			$itensMenu = array();
			
			$itensMenu[] = array("texto" => "Novo: CondomÝnio", "url" => "admin-cadastro.php?op=condominios&tela=cadastro");
			
			switch($tela) {
				case 'alteracao':
					if($this->obtem("modo_visualizacao")) {
						$itensMenu[] = array("texto" => "Helpdesk", "url" => "admin-cadastro.php?op=helpdesk&tela=listagem&id_condominio=" . $this->obtem("id_condominio") );
					}
					break;
			}

			return($itensMenu);
		}
		
		
		protected function obtemItensMenuHelpdesk($tela="") {
			$itensMenu = array();
			
			$itensMenu[] = array("texto" => "Ficha: CondomÝnio", "url" => "admin-cadastro.php?op=condominios&tela=cadastro&visualizacao=1&id_condominio=" . $this->obtem("id_condominio"));
			return($itensMenu);
		}

	
		public function exibe() {
			switch($this->_visualizacao) {
				case 'equipamentos':
					$this->exibeEquipamentos();
					break;
				
				case 'preferencias':
					$this->exibePreferencias();
					break;
				
				case 'relatorios':
					$this->exibeRelatorios();
					break;
					
				case 'administradores':
					$this->exibeAdministradores();
					break;
					
				case 'planos':
					$this->exibePlanos();
					break;
					
				case 'produtos':
					$this->exibeProdutos();
					break;	
					
				case 'condominios':
					$this->exibeCondominios();					
					break;
				
				case 'helpdesk':
					$this->exibeHelpdesk();
					break;
				
				default:
					// Do something
			
			}

			parent::exibe();

		}
		
		protected function exibeEquipamentos() {
			$titulos = array(
								"servidores" => "Servidores",
								"nas" => "NAS", 
								"pops" => "POPs",
								"cadastro" => "Cadastro",
								"alteracao" => "AlterašŃo",
								"listagem" => "Listagem",
								"redes" => "Enderešos",
								"mikrotik" => "Mikrotik"
							);
			
			
			$elementosTit = array();
			
			$elementosTit[] = @$titulos[$this->obtem("tela")];
			
			switch($this->obtem("tela")) {
				case 'servidores':
					switch($this->obtem("subtela")) {
						case 'cadastro':
							$this->_file = "cadastro_eqpto_servidores_cadastro.html";
							if( $this->obtem("hostname") ) {
								$elementosTit[] = $titulos["alteracao"];
								$elementosTit[] = $this->obtem("hostname");
								$this->configureMenu($this->obtemItensMenu("servidores"),false,true);
							} else {
								$elementosTit[] = $titulos["cadastro"];
							}
							break;
						case 'listagem':
							$this->_file = "cadastro_eqpto_servidores_listagem.html";
							$elementosTit[] = $titulos["listagem"];
							$this->configureMenu($this->obtemItensMenu("servidores"),true,true);
							
							break;
					}
					break;
				case 'nas':
					switch($this->obtem("subtela")) {

						case 'cadastro':
							$this->_file = "cadastro_eqpto_nas_cadastro.html";
							$nome = $this->obtem("nome");
							
							if( $nome ) {
								$elementosTit[] = $titulos["alteracao"];
								$elementosTit[] = $nome;
								$this->configureMenu($this->obtemItensMenu("nas"),false,true);
							} else {
								$elementosTit[] = $titulos["cadastro"];
								
							}
							
							break;
						
						case 'redes':
							$elementosTit[] = $this->obtem("nome");
							$elementosTit[] = $titulos["redes"];
							$elementosTit[] = ($exibir_enderecos?"Listagem":"Cadastro");
							$this->configureMenu($this->obtemItensMenuNASRedes($this->obtem("id_nas"),$this->obtem("exibir_enderecos")));
							
							$this->_file = "cadastro_eqpto_nas_redes.html";
							break;

						default:
							$this->configureMenu($this->obtemItensMenu("nas"),true,true);
							$this->_file = "cadastro_eqpto_nas_listagem.html";
							break;

					}
					break;
				case 'pops':
					
					switch($this->obtem("subtela")) {
						case 'cadastro':
							// echo "CADASTRO";
							
							if( !$this->obtem("id_pop") ) {
								$elementosTit[] = @$titulos["cadastro"];
							} else {
								$this->configureMenu($this->obtemItensMenu("pops"),false,true);
								$elementosTit[] = @$titulos["alteracao"];
								if( $this->obtem("nome") ) {
									$elementosTit[] = strtoupper($this->obtem("nome"));
								}
							}
							$this->_file = "cadastro_eqpto_pops_cadastro.html";
							break;
						
						case 'gerarChave':
							$selecionavel = true;
							$this->_file = "cadastro_eqpto_gerar_chave.html";

							$elementosTit[] = " NOVA CHAVE :: " . $this->obtem("nome") . " (" . $this->obtem("tipo") . ")";

							break;
				

						default:
							// echo "LISTAGEM";
							$this->_file = "cadastro_eqpto_pops_listagem.html";
							$this->configureMenu($this->obtemItensMenu("pops"),true,true);
							$elementosTit[] = @$titulos["listagem"];
							break;

					}


					break;
				case 'mikrotik':
					switch($this->obtem("subtela")) {
						case 'cadastro':
							if( !$this->obtem("id_nas") ) {
								$elementosTit[] = @$titulos["cadastro"];
							} else {
								$this->configureMenu($this->obtemItensMenu("pops"),false,true);
								$elementosTit[] = @$titulos["alteracao"];
								if( $this->obtem("nome") ) {
									$elementosTit[] = strtoupper($this->obtem("nome"));
								}
							}
							$this->_file = "cadastro_eqpto_mikrotik_cadastro.html";
							break;

						default:
							// echo "LISTAGEM";
							$this->_file = "cadastro_eqpto_mikrotik_listagem.html";
							$this->configureMenu($this->obtemItensMenu("mikrotik"),true,true);
							$elementosTit[] = @$titulos["listagem"];
							break;
					
					}
					break;
					
			}
			
			$titulo = implode(" :: ",$elementosTit);
			$this->atribui("titulo",$titulo);
			
		}
		
		protected function exibePreferencias() {
			$titulo = "Preferencias";
			
			switch($this->obtem("tela")) {
				case 'geral':
					$this->_file = "cadastro_preferencias_geral.html";
					$titulo .= " :: Preferŕncias Gerais";
					break;
				case 'provedor':
					$this->_file = "cadastro_preferencias_provedor.html";
					$titulo .= " :: Preferŕncias do Provedor";
					break;
				case 'cobranca':
					$this->_file = "cadastro_preferencias_cobranca.html";
					$titulo .= " :: Cobranša";
					if( $this->obtem("subtela") == "forma_pagamento" ) {
						$titulo .= " :: Forma Pagamento";
					} else {
						if( $this->obtem("acao") == "editar" ) {
							$titulo .= " :: Editar";
						}
					}
					$this->configureMenu($this->obtemItensMenuPreferencias($this->obtem("tela"),$this->obtem("subtela")),($this->obtem("acao")=="editar"||$this->obtem("subtela")?false:true),true);
					break;
				case 'modelos':
					if( $this->obtem("subtela") != "exibir_modelo" ) {
						$this->_file = "cadastro_preferencias_modelos.html";
						$titulo .= " :: Modelos de Contrato";
						$this->configureMenu($this->obtemItensMenuPreferencias($this->obtem("tela"),$this->obtem("subtela")),($this->obtem("subtela")?false:true),true);
					}
					
					break;
				case 'cidades':
					$this->_file = "cadastro_preferencias_cidades.html";
					$titulo .= " :: Cidades de AtuašŃo";
					break;
				case 'banda':
					$this->_file = "cadastro_preferencias_banda.html";
					$titulo .= " :: Faixas de banda";
					break;
				case 'monitoramento':
					$this->_file = "cadastro_preferencias_monitoramento.html";
					$titulo .= " :: Monitoramento";
					break;
				case 'links':
					$this->_file = "cadastro_preferencias_links.html";
					$titulo .= " :: Links Externos";
					break;
				case 'registro':
					$this->_file = "cadastro_preferencias_registro.html";
					$titulo .= " :: Registro";
					break;
				case 'resumo':
					$this->_file = "cadastro_preferencias_resumo.html";
					$titulo .= " :: Resumo";
					break;		
			}
			
			$this->atribui("titulo",$titulo);
		}
		
		protected function exibeRelatorios() {
			switch($this->obtem("relatorio")) {
				case 'carga':
					$this->_file = "cadastro_relatorios_carga.html";
					
					$titulo = "Carga";
					
					switch($this->obtem("tipo")) {
						case 'nas':
							$titulo .= " :: Por NAS";
							if($this->obtem("id_nas")) {
								$nas = $this->obtem("nas");
								$tiposNas = $this->obtem("tiposNas");
								$titulo .= " :: " . $nas["id_nas"] . " - " . $nas["nome"] . " (" . $tiposNas[$nas["tipo_nas"]] . ")";
							}
							break;
						case 'pop':
						case 'ap':
							$titulo .= $this->obtem("tipo") == "pop" ? " :: Por POP" : " :: Por AP";
							if($this->obtem("id_pop")) {
								$pop = $this->obtem("pop");
								$tiposPop = $this->obtem("tiposPop");
								$titulo .= " :: " . $pop["id_pop"] . " - " . $pop["nome"] . " (" . $tiposPop[$pop["tipo"]] . ")";
							}
							break;
							break;
					
					}
					
					$this->atribui("titulo",$titulo);
					break;
				
				case 'pop_ip':
					// echo "POP IP<br>\n"; 					
					$titulo = "Relatˇrio::IPs dos Equipamentos";
					$this->atribui("titulo",$titulo);
					
					$this->_file = "cadastro_relatorios_ips.html";
					
					
					break;
					
				case 'condominios':
				
					$titulo = "CondomÝnios";
				
					switch($this->obtem("tipo")) {
						case 'instalado':
						default:
							$this->_file = "cadastro_relatorios_condominios_instalados.html";
							$titulo .= " :: Instalados";
						break;
					}
					$this->atribui("titulo",$titulo);
					break;
			}
		}


		protected function exibeAdministradores() {
			$titulo = " :: Administradores";
			switch($this->obtem("tela") ) {
				case 'cadastro':
					if($this->obtem("id_admin")) {
						$titulo .= " :: AlterašŃo";
					}else {
						$titulo .= " :: Cadastro";
					}

					$this->configureMenu($this->obtemItensMenuAdministradores(),false,true);
					$this->_file = "cadastro_admin_cadastro.html";
					break;
				case 'privilegio':
					$titulo .= " :: PrivilÚgios";
					$this->configureMenu($this->obtemItensMenuAdministradores(),false,true);
					$this->_file = "cadastro_admin_privilegio.html";
				break;
				case 'listagem':
					$titulo .= " :: Listagem";
					$this->_file = "cadastro_admin_listagem.html";
					$this->configureMenu($this->obtemItensMenuAdministradores(), true, true);
					break;
				default:
					//Do Something
			}

			$this->nomeSessao .= $titulo;
		}


		protected function exibePlanos() {
			$titulo = "Planos";
			switch( $this->obtem("tela") ) {
				case 'cadastro':
					if( $this->obtem("id_produto") ) {
						$titulo .= " :: AlterašŃo :: " . $this->obtem("nome");
					} else {
						$titulo .= " :: Cadastro";
					}
					$this->configureMenu($this->obtemItensMenuPlanos(),false,true);
					$this->_file = "cadastro_planos_cadastro.html";
					break;
				case 'listagem':
					$titulo .= " :: Listagem";
					$this->configureMenu($this->obtemItensMenuPlanos(),true,true);
					$this->_file = "cadastro_planos_listagem.html";
					break;
				default:
					// Do something
			}

			$this->atribui("titulo",$titulo);
		}
		

		protected function exibeProdutos() {

		}
		
		
		protected function exibeCondominios() {
			$titulo = "CondomÝnios";
			
			switch ($this->obtem("tela")) {
				case 'cadastro':
					if(@$this->obtem("id_condominio")) {						
						if ($this->obtem("modo_visualizacao")) {
							$titulo .= " :: VisualizašŃo :: " . $this->obtem("nome");
							$this->configureMenu($this->obtemItensMenuCondominio("alteracao"), false, true);
						} else {
							$titulo .= " :: AlterašŃo :: " . $this->obtem("nome");
						}
					} else {
						$titulo .= " :: Cadastro de novo condomÝnio ";
						$this->configureMenu($this->obtemItensMenuCondominio(), false, true);
					}
					
					$this->_file = "cadastro_condominios_cadastro.html";
					break;
				
				case 'bloco':
					if(@$this->obtem("id_bloco")) {						
						$titulo .= " :: Alterar Bloco";
					} else {
						$titulo .= " :: Cadastrar Bloco";
					}

					$this->configureMenu($this->obtemItensMenuCondominio(), false, true);
					$this->_file = "cadastro_condominios_bloco_cadastro.html";
					break;
					
				case 'listagem':
					$titulo .= " :: Listagem ";
					$this->configureMenu($this->obtemItensMenuCondominio(), true, true);
					$this->_file = "cadastro_condominios_listagem.html";
					break;
			}
			
			$this->atribui("titulo", $titulo);
		}
		
		protected function exibeHelpdesk() {
			$titulo = "Helpdesk";
			
			//Nome do condomÝnio
			$condominio = $this->obtem("condominio");
			$subtela = @$this->obtem("subtela");
			if($condominio) $titulo .= " :: $condominio[nome]";
			
			
			switch($this->obtem("tela")) {
				case 'cadastro':
					$titulo .= " :: Novo Chamado/Ocorrŕncia";
					$this->_file="cadastro_condominio_helpdesk_chamado_novo.html";
					break;				
				
				case 'alteracao':
					$titulo .= " :: Chamado #";
					$chamado = @$this->obtem("chamado");
					$titulo .= $chamado["id_chamado"];
					
					$this->_file = "cadastro_condominio_helpdesk_chamado_alteracao.html";
					
					if($subtela == "ordemservico") { 
						$titulo .= " :: Gerar Ordem de Servišo";
						$this->_file = "cadastro_condominio_helpdesk_chamado_alteracao_ordemservico.html";
					} else if($subtela == 'imprimir_os') {
						$this->_file = "cadastro_helpdesk_chamado_imprimir_os.html";
						$this->atribui("prtopt", 'sem_header');
					}
					
					break;
					
				case 'listagem':
					$titulo .= " :: Listagem";
					$this->_file = "cadastro_condominio_helpdesk_chamado.html";
					$this->configureMenu($this->obtemItensMenuHelpdesk());
					break;
			}
			
			if(!($this->obtem("tela") == "alteracao" && $this->obtem("subtela") == "imprimir_os"))
				$this->atribui("titulo", $titulo);
		}


		
	
	}

