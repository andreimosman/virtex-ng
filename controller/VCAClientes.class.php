<?

	class VCAClientes extends VirtexcontrollerAdmin {
	
		protected $clientes;		
		protected $id_cliente;
		
		protected $itensMenu;
		
	
		public function __construct() {
			parent::__construct();
		}
		
		protected function init() {
			// Inicializações da SuperClasse
			parent::init();

			$this->_view = VirtexViewAdmin::factory("clientes");
			$this->clientes = VirtexModelo::factory("clientes");

			// Inicializações
			$this->id_cliente 	= @$_REQUEST["id_cliente"];
			$this->extra_op 	= @$_REQUEST["extra_op"];
			
		}
		
		protected function executa() {
			// Executa as operações
		
			switch( $this->_op ) {
				case 'cadastro':
					$this->executaCadastro();
					break;
				
				case 'pesquisa':
					$this->executaPesquisa();
					break;
				
				case 'relatorios':
					$this->executaRelatorios();
					break;

				case 'eliminar':
					$this->executaEliminar();
					break;
				
				case 'contrato':
					$this->executaContrato();
					break;

				default:
					// Do Something
			}

		}

		protected function executaCadastro() {
			$this->_view->atribuiVisualizacao("cadastro");
			$this->_view->atribui("extra_op",$this->extra_op);
			$this->_view->atribui("lista_tp_pessoa",$this->clientes->listaTipoPessoa());
			$this->_view->atribui("lista_status",$this->clientes->listaStatusCliente());
			$this->_view->atribui("lista_dia_pagamento",$this->clientes->listaDiaPagamento());
			$this->_view->atribui("cidades_disponiveis",$this->clientes->listaCidades());
			
			if( $this->id_cliente && !$this->acao ) {
				// Tem o id do cliente e não tem ação, pegar do banco
				
				$dados = $this->clientes->obtemPeloId($this->id_cliente);
				
				foreach( $dados as $vr => $vl ) {
					$this->_view->atribui($vr,$vl);
				}
			
			} else {
				foreach( @$_REQUEST as $vr => $vl ) {
					$this->_view->atribui($vr,$vl);
				}
			}
			
			// echo "EXTRA_OP: " . ( !$this->extra_op && $this->_acao ) . " - " . $this->extra_op . "<br>\n";
			
			if( !$this->extra_op && $this->_acao ) {
				try {
					if( $this->id_cliente ) {
						$this->clientes->altera($this->id_cliente,@$_REQUEST);
						$mensagem = "Cliente alterado com sucesso.";
						$id_cliente = $this->id_cliente;
					} else {
						$id_cliente = $this->clientes->cadastra(@$_REQUEST);
						$mensagem = "Cliente cadastrado com sucesso.";
					}
					$this->_view->atribui("mensagem",$mensagem);
					$this->_view->atribui("url","admin-clientes.php?op=cadastro&extra_op=ficha&id_cliente=$id_cliente");
					$this->_view->atribuiVisualizacao("msgredirect");
					
				} catch(ExcecaoModelo $e) {
					/**
					 * 1) Logar exception.
					 * 2) Jogar pra página amigável de erro.
					 */
					 
					 
					$this->_view->atribuiErro($e->obtemCodigo(),$e->obtemMensagem());
					 
					//echo "EXCEPTION!!!";
					
					// echo "EXCEPTION!!!";
					
				}
			}
		}
		
		protected function executaPesquisa() {
			$this->_view->atribuiVisualizacao("pesquisa");
			
			$tipo_pesquisa  = @$_REQUEST["tipo_pesquisa"];
			$texto_pesquisa = @$_REQUEST["texto_pesquisa"];
			
			$this->_view->atribui("tela",@$_REQUEST["tela"]);
			$this->_view->atribui("tipo_pesquisa",$tipo_pesquisa);
			$this->_view->atribui("texto_pesquisa",$texto_pesquisa);
			
			
			if( $this->_acao && $tipo_pesquisa && $texto_pesquisa ) {
				try {
					$registros = array();
					switch($tipo_pesquisa) {
						case 'NOME':
							$registros = $this->clientes->obtemPeloNome($texto_pesquisa);
							break;
						case 'DOCTOS':
							$registros = $this->clientes->obtemPelosDocumentos($texto_pesquisa);
							break;
						
						case 'CONTA':
						case 'EMAIL':
							$registros = array();			// TODO: Buscar em negocios_contrato
							break;
							
						default:
							// Do something
					}
					
					$this->_view->atribui("registros",$registros);
					
				} catch( ExcecaoModelo $e ) {
					/**
					 * 1) Logar exception.
					 * 2) Jogar pra página amigável de erro.
					 */
				}
			} else {
				if( !$tipo_pesquisa && !$text_pesquisa && !$this->_acao ) {
					$registros = $this->clientes->obtemUltimos();
					$this->_view->atribui("registros",$registros);
				}
			}
			// echo "EXECUTA PESQUISA<br>\n";
		}
		
		
		protected function executaContrato() {
			$this->_view->atribuiVisualizacao("contrato");
			$this->_view->atribui("id_cliente",$this->id_cliente);
			$info = $this->clientes->obtemPeloId($this->id_cliente);
			
			$this->_view->atribui("nome_razao",$info["nome_razao"]);
			
			$tela = @$_REQUEST["tela"];
			$this->_view->atribui("tela",$tela);
			
			$acao = @$_REQUEST["acao"];
			$this->_view->atribui("acao",$acao);
			
			switch($tela) {
				
				case 'novo_contrato':
					$produtos     = VirtexModelo::factory("produtos");
					$equipamentos = VirtexModelo::factory("equipamentos");

					// Dados comuns
					$tiposFormaPgto = $this->preferencias->obtemTiposFormaPagamento();
					$this->_view->atribui("tiposFormaPgto",$tiposFormaPgto);

					$bancos = $this->preferencias->obtemListaBancos();
					$this->_view->atribui("bancos",$bancos);

					$preferenciasGerais = $this->preferencias->obtemPreferenciasGerais();
					$this->_view->atribui("preferenciasGerais",$preferenciasGerais);

					$tiposNAS = $equipamentos->obtemTiposNAS();
					$this->_view->atribui("tiposNAS",$tiposNAS);
					
					//echo "<pre>";
					//print_r($tiposNAS);
					//echo "</pre>";
					
					if( !$acao ) {
						// Tela de contrataçao
						
						// Lista de produtos disponíveis por tipo.
						$listaBL 	= $produtos->obtemListaPlanos('BL','t');
						$listaD		= $produtos->obtemListaPlanos('D','t');
						$listaH		= $produtos->obtemListaPlanos('H','t');
						
						$this->_view->atribui("listaBL",MJson::encode($listaBL));
						$this->_view->atribui("listaD",MJson::encode($listaD));
						$this->_view->atribui("listaH",MJson::encode($listaH));
						
						/**
						 * Formas de Pagamento - Agrupadas por tipo
						 */
						$formasPagamento = $this->preferencias->obtemFormasPagamento('t');
						
						$f = array_keys($tiposFormaPgto);
						$formas = array();
						for($i=0;$i<count($f);$i++) {
							$formas[$f[$i]] = array();
						}
						
						for($i=0;$i<count($formasPagamento);$i++) {
							$formas[$formasPagamento[$i]["tipo_cobranca"]][] = $formasPagamento[$i];
						}
						
						$this->_view->atribui("formasPagamento",$formas);
						
						
						/**
						 * Tipos de Pagamento (pré e pós pago).
						 */
						$tiposPagamento = $this->preferencias->obtemTiposPagamento();
						$this->_view->atribui("tiposPagamento",$tiposPagamento);
												
						$preferenciasCobranca = $this->preferencias->obtemPreferenciasCobranca();
						$this->_view->atribui("preferenciasCobranca",$preferenciasCobranca);
						
						$listaNAS = $equipamentos->obtemListaNAS();
						$this->_view->atribui("listaNAS",$listaNAS);
						$listaPOP = $equipamentos->obtemListaPOPs('A');
						$this->_view->atribui("listaPOP",$listaPOP);
						
						// Valores Padrão
						$data_contratacao = date("d/m/Y");
						$this->_view->atribui("dia_vencimento",$preferenciasCobranca["dia_venc"]);
						$this->_view->atribui("data_contratacao",$data_contratacao);
						$this->_view->atribui("pagamento",$preferenciasCobranca["pagamento"]);
						$this->_view->atribui("carencia",$preferenciasCobranca["carencia"]);
						$this->_view->atribui("vigencia","12");
						$this->_view->atribui("comodato","f");
						
					} else {
						$id_cliente_produto = @$_REQUEST["id_cliente_produto"];						
						$cobranca = VirtexModelo::factory("cobranca");

						// Informações do produto contratado
						$produto = $produtos->obtemPlanoPeloId(@$_REQUEST["id_produto"]);
						$valor = @$produto["valor"];
						$this->_view->atribui("produto",$produto);
						
						$tipo = @$_REQUEST["tipo"];
						
						if( $tipo == "BL" ) {
							$id_nas = @$_REQUEST["id_nas"];
							$nas = $equipamentos->obtemNAS($id_nas);
							$this->_view->atribui("nas",$nas);
							
							$id_pop = @$_REQUEST["id_pop"];
							$pop = $equipamentos->obtemPOP($id_pop);
							$this->_view->atribui("pop",$pop);
						}
						//echo "<pre>";
						// print_r($produto);
						//print_r($nas);
						//print_r($pop);
						//echo "</pre>";

						while(list($vr,$vl)=each(@$_REQUEST)) {
							$this->_view->atribui($vr,$vl);
						}
						
						// Lista das faturas que serão geradas
						// TODO: Verificar se é cortesia
						$faturas = $cobranca->gerarListaFaturas(@$_REQUEST["pagamento"],@$_REQUEST["data_contratacao"],@$_REQUEST["vigencia"],@$_REQUEST["dia_vencimento"],$valor,@$_REQUEST["desconto_promo"],@$_REQUEST["periodo_desconto"],@$_REQUEST["tx_instalacao"],@$_REQUEST["valor_comodato"]);
						$this->_view->atribui("faturas",$faturas);

						
						if( $acao == "novo_contrato" ) {
							// Validação dos dados entrados
							
							// Informações do Contratante (cliente)
							$cliente = $this->clientes->obtemPeloId($this->id_cliente);
							$cidade = $this->preferencias->obtemCidadePeloId($cliente["id_cidade"]);
							$cliente["cidade"] = $cidade["cidade"] . "-" . $cidade["uf"];
							$this->_view->atribui("cliente",$cliente);
							
							$id_forma_pagamento = @$_REQUEST["id_forma_pagamento_" . @$_REQUEST["forma_pagamento"]];
							if( $id_forma_pagamento ) {
								$formaPagamento = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);
								$this->_view->atribui("formaPagamento",$formaPagamento);
							}
							
							
							// Seleção de dados para exibição na tela de confirmação.
							
							
							
							
							
							//echo "<pre>";
							// print_r($formaPagamento);
							//print_r($_REQUEST);
							//echo "</pre>";
							
						}
						
						if( $acao == "gravar_novo_contrato" ) {
						
						}

					}
					break;
				
				
				
				default:
					// Resumo
					break;
			
			}
			
			
		}
		
		
		

		protected function executaRelatorios() {
			$this->_view->atribuiVisualizacao("relatorios");
			echo "EXECUTA RELATORIOS<br>\n";
		}

		protected function executaEliminar() {
			$this->_view->atribuiVisualizacao("eliminar");
			echo "EXECUTA ELIMINAR<br>\n";
		}

	}

?>
