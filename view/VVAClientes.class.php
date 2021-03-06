<?



	class VVAClientes extends VirtexViewAdmin {	
		protected $titulos;
		
		public function __construct() {
			parent::__construct();
			
			$this->titulos = array(
										"cadastro" => "Inclus?o",
										"alteracao" => "Altera??o",
										"ficha" => "Ficha do Cliente",
										"pesquisa" => "Pesquisa",
										"eliminar" => "Eliminar",
										"emails_cancelados" => "Emails Cancelados"
									);
			//
			$this->configureMenu(array(),false,false);	// Configura um menu vazio
			
		}
		
		protected function init() {
			parent::init();
			$this->nomeSessao = "Clientes";
		}
		
		protected function obtemItensMenu() {
			$id_cliente = $this->obtem("id_cliente");
			// echo "ID CLIENTE: ". $id_cliente . "\n"; 
			//echo "<pre>"; 
			//print_r($this->_login);
			//echo "</pre>"; 
		
			$itensMenu = array();
			if( ($this->obtem("op") == "cadastro" && $this->obtem("extra_op") != "ficha") || $this->obtem("op") != "cadastro" ) {
					$itensMenu[] = array("texto" => "Ficha do Cliente", "url" => "admin-clientes.php?op=cadastro&extra_op=ficha&id_cliente=".$this->obtem("id_cliente"));
			} else {
				$itensMenu[] = array("texto" => "Alterar Cadastro", "url" => "admin-clientes.php?op=cadastro&id_cliente=".$this->obtem("id_cliente"));
			}
			
			if( $id_cliente ) {
						
				$cobranca = VirtexModelo::factory("cobranca");
				$contratos = $cobranca->obtemNumeroContratosAtivosPorTipo($id_cliente);
				
				if( $this->podeLer("_CLIENTES_BANDALARGA") && @$contratos["bl"] ) {
					$itensMenu[] = array("texto" => "Banda Larga (".$contratos["bl"].")", "url" => "admin-clientes.php?op=conta&tipo=BL&id_cliente=".$this->obtem("id_cliente"));
				}
				if( $this->podeLer("_CLIENTES_DISCADO") && @$contratos["d"] ) {
					$itensMenu[] = array("texto" => "Discado (".$contratos["d"].")", "url" => "admin-clientes.php?op=conta&tipo=D&id_cliente=".$this->obtem("id_cliente"));
				}
				if( $this->podeLer("_CLIENTES_HOSPEDAGEM") && @$contratos["h"] ) {
					$itensMenu[] = array("texto" => "Hospedagem (".$contratos["h"].")", "url" => "admin-clientes.php?op=conta&tipo=H&id_cliente=".$this->obtem("id_cliente"));
				}


				if( $this->podeLer("_CLIENTES_CONTRATOS") ) {
					$itensMenu[] = array("texto" => "Contrato/Faturas (".($contratos["bl"]+$contratos["d"]+$contratos["h"]).")", "url" => "admin-clientes.php?op=contrato&id_cliente=".$this->obtem("id_cliente"));
				}
				$itensMenu[] = array("texto" => "Helpdesk", "url" => "admin-clientes.php?op=helpdesk&tela=listagem&id_cliente=".$this->obtem("id_cliente"));
				if( $this->podeLer("_CLIENTES_EMAILS_CANCELADOS") ) {
					$contas = VirtexModelo::factory("contas");
					$emails_cancelados = $contas->obtemContasEmailCanceladas($id_cliente);
				
					$itensMenu[] = array("texto" => "Emails Cancelados (".count($emails_cancelados).")", "url" => "admin-clientes.php?op=emails_cancelados&tela=listagem&id_cliente=".$this->obtem("id_cliente"));
				}
			}
			
			return($itensMenu);
			
		}
		
		public function exibe() {
			switch($this->_visualizacao) {
				case 'cadastro':
					$this->exibeCadastro();
					break;
					
				case 'conta':
					$this->exibeConta();
					break;
				case 'pesquisa':
					$this->exibePesquisa();
					break;
					
				case 'relatorios':
					$this->exibeRelatorios();
					break;
					
				case 'eliminar':
					$this->exibeEliminar();
					break;
					
				case 'contrato':
					$this->exibeContrato();
					break;
					
				case 'helpdesk':
					$this->exibeHelpdesk();
					break;
					
				case 'emails_cancelados':
					$this->exibeEmailsCancelados();
					break;
					
				default:
					// Do something
			
			}
		
			parent::exibe();
		}
		
		protected function atribuiTitulo() {
		
			$titulo = "";
			if( $this->obtem("nome_razao") ) {
				$titulo = ($this->obtem("id_cliente") ? "[" . str_pad($this->obtem("id_cliente"),8,"0",STR_PAD_LEFT) . "] " : "") . $this->obtem("nome_razao") . " :: ";
			}
			
			if( $this->_visualizacao == "cadastro" && $this->obtem("id_cliente") ) {
				$titulo .= @$this->titulos[ $this->obtem("extra_op") == "ficha" ? "ficha" : "alteracao" ];
			} else {
				$titulo .= @$this->titulos[ $this->_visualizacao ];
			}

			$this->atribui("titulo",$titulo);
		}
		
		protected function exibeEmailsCancelados() {
			$tela = $this->obtem("tela");

			if( $tela == "recuperar" ) {
				$this->_file = "clientes_emails_cancelados_recuperar.html";
			} else {
				$this->_file = "clientes_emails_cancelados.html";
			}
		
			
			$this->atribuiTitulo();
			$this->configureMenu($this->obtemItensMenu());
			
			
		}
		
		protected function exibeCadastro() {
			$this->_file = "clientes_cadastro.html";
			$this->atribui("acao","submit");
			
			if( $this->obtem("id_cliente") ) {
				$this->configureMenu($this->obtemItensMenu(),($this->obtem("extra_op")),true);
			}
			
			$this->atribuiTitulo();
			
			if( $this->obtem("id_cliente") ) {
				$this->atribui("submit","Alterar");
			} else {
				$this->atribui("submit","Cadastrar");
			}
			
		}
		
		protected function exibePesquisa() {
			$this->_file = "clientes_pesquisa.html";
			$this->atribui("acao","submit");
			$this->atribuiTitulo();
		}
		
		protected function exibeContrato() {
			$titulo = $this->obtem("nome_razao");			
			$this->_file = "clientes_contrato.html";
			$selecionavel = true;	// Se o menu estar? habilitado
			
			$menu = true;
			
			
			switch($this->obtem("tela")) {
				case 'imprime_carne':
					$this->_file = "clientes_contrato_imprime_faturas.html";
					break;
			
				case 'novo_contrato':
					$titulo .= " :: Novo Contrato";
					if($this->obtem("acao")=="novo_contrato") {
						$titulo .= " :: Confirma??o";
					}
					$selecionavel = false;
					break;
					
				case 'cancelar_contrato':	// CANCELAMENTO
				case 'contrato':			// DETALHES
					$titulo .= " :: ".($this->obtem("tela")=='cancelar_contrato'?'CANCELAR ':"")."Contrato #" . str_pad($this->obtem("id_cliente_produto"),6,"0",STR_PAD_LEFT);
					break;
				
				case 'contratos':
					$titulo .=  " :: Contratos";
					
					break;
					
				case 'migrar':
					$titulo .= " :: Migrar Contrato #". str_pad($this->obtem("id_cliente_produto"),6,"0",STR_PAD_LEFT);
					break;
					
				case 'faturas':
					$id_cliente_produto = $this->obtem("id_cliente_produto");
					$titulo .= " :: Faturas";
					if ($id_cliente_produto) $titulo .= " :: Contrato #$id_cliente_produto";
					break;
				
				case 'estorno':
					$titulo .= " :: Estorno de Fatura :: #" . $this->obtem("id_cobranca");
					break;
			
				case 'amortizacao':
					$titulo .= " :: Amortiza??o";
					break;
					
				case 'imprime':
					// echo "IMPRIME";
					$this->_file = "clientes_contrato_imprime.html";
					$menu = false;
					
				
					//echo "<pre>"; 
					//print_r($this->_bag);
					//echo "</pre>";
				
				
					break;
			
				default:
					$selecionavel = true;
					$titulo .= " :: Resumo Contratos/Faturas";
			
			}
			
			
			if( $menu ) {
				$this->configureMenu($this->obtemItensMenu(),$selecionavel,true);
			}
			$this->atribui("titulo",$titulo);
		
		}
		
		/** 
		 * Exibe as contas de um determinado cliente.
		 * Organizadas por contrato.
		 */
		protected function exibeConta() {
			$titulo = $this->obtem("nome_razao");
			$this->_file = "clientes_contas.html";
			$selecionavel = true;
			
			if( $this->obtem("tela") == "cadastro" ) {
				$selecionavel = false;
				$this->_file = "clientes_contas_cadastro.html";

				if( $this->obtem("id_conta") ) {
					$titulo .= " :: " . $this->obtem("username") . " (" . $this->obtem("tipo_conta") . ")";
				} else {
					$titulo .= " :: Nova Conta";
				}

			} else if( $this->obtem("tela") == "ficha" ) {
				$selecionavel = true;
				$this->_file = "clientes_contas_ficha.html";
				
				$titulo .= " :: " . $this->obtem("username") . " (" . $this->obtem("tipo_conta") . ")";
				
			} else if( $this->obtem("tela") == "gerarChave" ) {
				$selecionavel = true;
				$this->_file = "clientes_contas_gerar_chave.html";
				
				$titulo .= " :: NOVA CHAVE :: " . $this->obtem("username") . " (" . $this->obtem("tipo_conta") . ")";
				
			} else {
				switch( $this->obtem("tipo") ) {
					case 'BL':
						$titulo .= " :: Banda Larga";
						break;
					case 'D':
						$titulo .= " :: Discado ";
						break;
					case 'H':
						$titulo .= " :: Hospedagem ";
						break;
				}
			}
			
			$this->configureMenu($this->obtemItensMenu(),$selecionavel,true);
			$this->atribui("titulo",$titulo);
			
		}
		
		
		
		
		protected function exibeRelatorios() {
			
			$titulo = "Relat?rios";
			
			switch($this->obtem("relatorio")) {
				case 'lista_geral':
					$titulo .= " :: Listagem Geral ";
					
					if( $this->obtem("inicial") ) {
						$titulo .= " :: " . $this->obtem("inicial");
					} elseif( $this->obtem("acao") == "TODOS" ) {
						$titulo .= " :: Listagem Completa";
					} else {
						$titulo .= " :: ?ltimos Cadastrados";
					}
					
					
					$this->_file = "clientes_relatorios_geral.html";
					
					break;

				case 'cliente_cidade':
					$titulo .= " ::  Clientes por Cidade";
					if( $this->obtem("id_cidade") && $this->obtem("cidade") && $this->obtem("uf")) {
						$titulo .= " :: " . $this->obtem("cidade") . "-" . $this->obtem("uf");
					} else if( $this->obtem("id_cidade") == ':NULL:' ) {
						$titulo .= " :: Sem Cidade Definida";
					}
					
					
					$this->_file = "clientes_relatorios_cidade.html";
					
					break;
			}
			
			$this->atribui("titulo",$titulo);
			
			
		}
		
		
		protected function exibeEliminar() {
			echo "EXIBE ELIMINAR<br>\n";
			$this->atribuiTitulo();
		}
	
	
		protected function exibeHelpdesk() {
		
			$titulo = $this->obtem("nome_razao") . " :: Chamados";;		
			$subtela = $this->obtem("subtela");
			$this->_file = "clientes_helpdesk_chamado.html";
						
			switch($this->obtem("tela")) {
				case 'cadastro':
					$titulo .= " :: Novo Chamado/Ocorr&ecirc;ncia";
					$this->_file = "clientes_helpdesk_chamado_novo.html";
					break;

				case 'alteracao':
					$titulo .= " :: Chamado #";
					$chamado = @$this->obtem("chamado");
					$titulo .= $chamado["id_chamado"];
					
					$this->_file = "clientes_helpdesk_chamado_alteracao.html";
					
					if($subtela == "ordemservico") { 
						$titulo .= " :: Gerar Ordem de Servi?o";
						$this->_file = "clientes_helpdesk_chamado_alteracao_ordemservico.html";
					} else if($subtela == 'imprimir_os') {
						$this->_file = "clientes_helpdesk_chamado_imprimir_os.html";
						$this->atribui("prtopt", 'sem_header');
					}
					
					
					break;


				case 'listagem':
				default:
					$selecionavel = true;
					$this->configureMenu($this->obtemItensMenu(),$selecionavel,true);
					break;
			}
			
			if(!($this->obtem("tela") == "alteracao" && $this->obtem("subtela") == "imprimir_os"))
				$this->atribui("titulo", $titulo);
		
		}
		
	
	}

