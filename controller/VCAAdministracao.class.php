<?


	class VCAAdministracao extends VirtexControllerAdmin {

		protected $produtos;
		protected $administradores;

		public function __construct() {
			parent::__construct();
		}
		
		protected function init() {
			parent::init();
			
			$this->_view 			= VirtexViewAdmin::factory("administracao");
			$this->produtos 		= VirtexModelo::factory("produtos");
			// $this->administradores	= VirtexModelo::factory("administradores");
			
		}
		
		protected function executa() {
			switch($this->_op) {
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
				default:
					// Do Something
			
			}
		}
		
		protected function executaAdministradores() {
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
		
		}
		
		protected function executaRelatorios() {
		
		}
		
	}




?>
