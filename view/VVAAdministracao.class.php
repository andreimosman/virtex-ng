<?

	class VVAAdministracao extends VirtexViewAdmin {

		public function __construct() {
			parent::__construct();
		}

		protected function init() {
			parent::init();
			$this->nomeSessao = "Administração";
		}

		protected function obtemItensMenu() {
			$itensMenu = array();

			switch($this->obtem("op")) {
				case 'planos':
					$itensMenu[] = array("texto" => "Novo: Banda Larga", "url" => "admin-administracao.php?op=planos&tela=cadastro&tipo=BL");
					$itensMenu[] = array("texto" => "Novo: Discado", "url" => "admin-administracao.php?op=planos&tela=cadastro&tipo=D");
					$itensMenu[] = array("texto" => "Novo: Hospedagem", "url" => "admin-administracao.php?op=planos&tela=cadastro&tipo=H");
					break;
				case 'administradores':
					$itensMenu[] = array("texto" => "Novo: Administrador", "url"=> "admin-administracao.php?op=administradores&tela=cadastro");
				default:
					// Do something
			}

			return($itensMenu);

		}

		public function exibe() {

			switch($this->_visualizacao) {
				case 'administradores':
					$this->exibeAdministradores();
					break;
				case 'planos':
					$this->exibePlanos();
					break;
				case 'produtos':
					$this->exibeProdutos();
					break;
				case 'ferramentas':
					$this->exibeFerramentas();
					break;
				case 'relatorios':
					$this->exibeRelatorios();
					break;
				case 'bancodados':
					$this->exibeBancoDados();
					break;
				case 'preferencias':
					$this->exibePreferencias();
					break;	
				case 'altsenha':
					$this->exibeAlteracaoSenha();				
				default:
					// Do Something
			}

			parent::exibe();
		}

		protected function exibeAdministradores() {
			$titulo = " :: Administradores";
			switch($this->obtem("tela") ) {
				case 'cadastro':
					if($this->obtem("id_admin")) {
						$titulo .= " :: Alteração";
					}else {
						$titulo .= " :: Cadastro";
					}

					$this->configureMenu($this->obtemItensMenu(),false,true);
					$this->_file = "administracao_admin_cadastro.html";
					break;
				case 'privilegio':
					$titulo .= " :: Privilégios";
					$this->configureMenu($this->obtemItensMenu(),false,true);
					$this->_file = "administracao_admin_privilegio.html";
				break;
				case 'listagem':
					$titulo .= " :: Listagem";
					$this->_file = "administracao_admin_listagem.html";
					$this->configureMenu($this->obtemItensMenu(), true, true);
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
						$titulo .= " :: Alteração :: " . $this->obtem("nome");
					} else {
						$titulo .= " :: Cadastro";
					}
					$this->configureMenu($this->obtemItensMenu(),false,true);
					$this->_file = "administracao_planos_cadastro.html";
					break;
				case 'listagem':
					$titulo .= " :: Listagem";
					$this->configureMenu($this->obtemItensMenu(),true,true);
					$this->_file = "administracao_planos_listagem.html";
					break;
				default:
					// Do something
			}

			$this->atribui("titulo",$titulo);
		}

		protected function exibeProdutos() {

		}

		protected function exibeFerramentas() {
			// echo "EF";
			$this->_file = "administracao_backup.html";

			$titulo = "Backup";


			$this->atribui("titulo", $titulo);
		}


		protected function exibeRelatorios() {

			switch($this->obtem("relatorio")) {
				case 'eventos':
					$this->atribui("titulo","Log de Eventos do Sistema");
					$this->_file = "administracao_relatorios_eventos.html";
					break;

				default:


			}

			//$this->_file = "administracao_log_admin.html";
			//$this->atribui("titulo","Log dos Administradores");
		}


		protected function exibeBancoDados() {

			$titulo = "Bando de Dados";

			switch($this->obtem("eliminar")) {
				case 'cliente':
					$titulo .= " :: Eliminar Cliente";
					$this->_file = "administracao_bancodados_eliminar.html";
					break;
				case 'contrato':
					$titulo .= " :: Eliminar Contrato";
					$this->_file = "administracao_bancodados_eliminar.html";
					break;
				case 'conta':
					$titulo .= " :: Eliminar Conta";
					$this->_file = "administracao_bancodados_eliminar.html";
					break;
				default:
					break;


			}

			//$this->_file = "administracao_log_admin.html";
			$this->nomeSessao .= $titulo;
			$this->atribui("titulo", $titulo);
		}
		
		
		protected function exibePreferencias() {
			$titulo = "Preferencias";
			
			switch($this->obtem("tela")) {
				case 'geral':
					$this->_file = "administracao_cadastro_preferencias_geral.html";
					$titulo .= " :: Preferências Gerais";
					break;
				case 'provedor':
					$this->_file = "administracao_cadastro_preferencias_provedor.html";
					$titulo .= " :: Preferências do Provedor";
					break;
				case 'cobranca':
					$this->_file = "administracao_cadastro_preferencias_cobranca.html";
					$titulo .= " :: Cobrança";
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
					$this->_file = "administracao_cadastro_preferencias_cidades.html";
					$titulo .= " :: Cidades de Atuação";
					break;
				case 'banda':
					$this->_file = "administracao_cadastro_preferencias_banda.html";
					$titulo .= " :: Faixas de banda";
					break;
				case 'monitoramento':
					$this->_file = "administracao_cadastro_preferencias_monitoramento.html";
					$titulo .= " :: Monitoramento";
					break;
				case 'links':
					$this->_file = "administracao_cadastro_preferencias_links.html";
					$titulo .= " :: Links Externos";
					break;
				case 'registro':
					$this->_file = "administracao_cadastro_preferencias_registro.html";
					$titulo .= " :: Registro";
					break;
				case 'resumo':
					$this->_file = "administracao_cadastro_preferencias_resumo.html";
					$titulo .= " :: Resumo";
					break;
				case 'helpdesk':					
					switch($this->obtem("subtela")) {
						case "cadastro_grupo":
							$this->_file = "administracao_preferencias_helpdesk_cadastro_grupo.html";
							
							if($this->obtem("id_grupo")) {
							
								if($this->obtem("modo_visualizacao")) {
									$titulo .= " :: Helpdesk :: Visualizar grupo \"" . $this->obtem("nome") . "\"";
								} else {
									$titulo .= " :: Helpdesk :: Alterar grupo " . $this->obtem("nome");
								}
								
							} else {
							
								$titulo .= " :: Helpdesk :: Cadastrar grupo";
								
							}
							
							$this->configureMenu($this->obtemItensMenuPreferencias($this->obtem("tela")), false, true);
							break;

						case "altera_usuario":
						case "cadastro_usuarios":
							$this->_file = "administracao_preferencias_helpdesk_cadastro_grupo_usuario.html";
							$titulo .= " :: Helpdesk :: Gerenciamento do grupo \"" . $this->obtem("nome") . "\"";
							break;

						case "config":
							$this->_file = "administracao_preferencias_helpdesk_configuracoes.html";
							$titulo .= " :: Helpdesk :: Configurações";
							break;

						case "listagem":
						default:
							$this->_file = "administracao_preferencias_helpdesk_listagem.html";
							$titulo .= " :: Helpdesk :: Listagem";
							$this->configureMenu($this->obtemItensMenuPreferencias($this->obtem("tela")), true, true);
							break;
					}
					break;
			}
			
			$this->atribui("titulo",$titulo);
		}
		
		
		protected function exibeAlteracaoSenha() {
			$titulo = " :: Alterar Senha";
			$this->_file = "administracao_altsenha.html";
			$this->nomeSessao .= $titulo;
		}		
		
		
		protected function obtemItensMenuPreferencias($tela) {
			$itensMenu = array();
			switch($tela) {
				case 'cobranca':
					$itensMenu[] = array("texto" => "Editar", "url" => "admin-administracao.php?op=preferencias&tela=cobranca&acao=editar");
					$itensMenu[] = array("texto" => "Nova Forma Pgto", "url" => "admin-administracao.php?op=preferencias&tela=cobranca&subtela=forma_pagamento");
					break;
				case 'modelos':
					$itensMenu[] = array("texto" => "Novo Modelo", "url" => "admin-administracao.php?op=preferencias&tela=modelos&subtela=cadastro");
					break;
				case 'helpdesk':
					$itensMenu[] = array("texto" => "Novo: Grupo", "url" => "admin-administracao.php?op=preferencias&tela=helpdesk&subtela=cadastro_grupo");
					$itensMenu[] = array("texto" => "Configurações", "url" => "admin-administracao.php?op=preferencias&tela=helpdesk&subtela=config");
					break;
			}
			return($itensMenu);
		}

	}

?>
