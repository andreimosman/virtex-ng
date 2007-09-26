<?



	class VVAClientes extends VirtexViewAdmin {	
		protected $titulos;
		
		public function __construct() {
			parent::__construct();
			
			$this->titulos = array(
										"cadastro" => "Cadastro",
										"alteracao" => "Alteração",
										"ficha" => "Ficha do Cliente",
										"pesquisa" => "Pesquisa",
										"eliminar" => "Eliminar"
									);
			//
			$this->configureMenu(array(),false,false);	// Configura um menu vazio
			
		}
		
		protected function init() {
			parent::init();
			$this->nomeSessao = "Clientes";
		}
		
		protected function obtemItensMenu() {
			$itensMenu = array();
			if( ($this->obtem("op") == "cadastro" && $this->obtem("extra_op") != "ficha") || $this->obtem("op") != "cadastro" ) {
					$itensMenu[] = array("texto" => "Ficha do Cliente", "url" => "admin-clientes.php?op=cadastro&extra_op=ficha&id_cliente=".$this->obtem("id_cliente"));
			} else {
				$itensMenu[] = array("texto" => "Alterar Cadastro", "url" => "admin-clientes.php?op=cadastro&id_cliente=".$this->obtem("id_cliente"));
			}
			$itensMenu[] = array("texto" => "Banda Larga", "url" => "admin-clientes.php?op=conta&tipo=BL&id_cliente=".$this->obtem("id_cliente"));
			$itensMenu[] = array("texto" => "Discado", "url" => "admin-clientes.php?op=conta&tipo=D&id_cliente=".$this->obtem("id_cliente"));
			$itensMenu[] = array("texto" => "Hospedagem", "url" => "admin-clientes.php?op=conta&tipo=H&id_cliente=".$this->obtem("id_cliente"));
			$itensMenu[] = array("texto" => "Contrato/Faturas", "url" => "admin-clientes.php?op=contrato&id_cliente=".$this->obtem("id_cliente"));
			
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
					
				default:
					// Do something
			
			}
		
			parent::exibe();
		}
		
		protected function atribuiTitulo() {
		
			$titulo = "";
			if( $this->obtem("nome_razao") ) {
				$titulo = $this->obtem("nome_razao") . " :: ";
			}
			
			if( $this->_visualizacao == "cadastro" && $this->obtem("id_cliente") ) {
				$titulo .= @$this->titulos[ $this->obtem("extra_op") == "ficha" ? "ficha" : "alteracao" ];
			} else {
				$titulo .= @$this->titulos[ $this->_visualizacao ];
			}

			$this->atribui("titulo",$titulo);
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
			$selecionavel = true;	// Se o menu estará habilitado
			
			
			switch($this->obtem("tela")) {
				case 'novo_contrato':
					$titulo .= " :: Novo Contrato";
					if($this->obtem("acao")=="novo_contrato") {
						$titulo .= " :: Confirmação";
					}
					$selecionavel = false;
					break;
				
				case 'contratos':
					$titulo .=  " :: Contratos";
					break;
					
				case 'faturas':
					$titulo .= " :: Faturas";
					break;
			
				case 'amortizacao':
					$titulo .= " :: Amortização";
					break;
			
				default:
					$selecionavel = true;
					$titulo .= " :: Resumo Contratos/Faturas";
			
			}
			
			

			$this->configureMenu($this->obtemItensMenu(),$selecionavel,true);
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
				$selecionavel = false;
				$this->_file = "clientes_contas_ficha.html";
				
				$titulo .= " :: " . $this->obtem("username") . " (" . $this->obtem("tipo_conta") . ")";
				
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
			echo "EXIBE RELATORIOS<br>\n";
			$this->atribuiTitulo();
		}
		
		protected function exibeEliminar() {
			echo "EXIBE ELIMINAR<br>\n";
			$this->atribuiTitulo();
		}
	
	}



?>
