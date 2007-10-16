<?

	class VCAClientes extends VirtexControllerAdmin {
	
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
					
				case 'conta':
					$this->executaConta();
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
							//$registros = array();			// TODO: Buscar em negocios_contrato
							$registros = $this->clientes->pesquisaClientesPorConta($texto_pesquisa);
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
		}
		
		
		protected function executaContrato() {
		
			$urlPreto 	= "view/templates/imagens/preto.gif";
			$urlBranco 	= "view/templates/imagens/branco.gif";
			
		
			$this->_view->atribuiVisualizacao("contrato");
			$this->_view->atribui("id_cliente",$this->id_cliente);
			$info = $this->clientes->obtemPeloId($this->id_cliente);
			
			$this->_view->atribui("nome_razao",$info["nome_razao"]);
			$this->_view->atribui("endereco",$info["endereco"]);
			$this->_view->atribui("bairro",$info["bairro"]);
			$this->_view->atribui("id_cidade",$info["id_cidade"]);
			$this->_view->atribui("complemento",$info["complemento"]);
			$this->_view->atribui("cep",$info["cep"]);
			
			$tela = @$_REQUEST["tela"];
			$this->_view->atribui("tela",$tela);
			
			$acao = @$_REQUEST["acao"];
			$this->_view->atribui("acao",$acao);
			
			switch($tela) {
				case 'imprime_carne':
					$id_carne = @$_REQUEST["id_carne"];
					$id_cliente = @$_REQUEST["id_cliente"];
					$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
					
					$this->_view->atribui("id_carne",$id_carne);
					$this->_view->atribui("id_cliente_produto",$id_cliente_produto);
					
					$cobranca = VirtexModelo::factory('cobranca');
					$faturas = $cobranca->obtemFaturasPorCarne($id_carne);
					
					$prefGeral 		= $this->preferencias->obtemPreferenciasGerais();
					$prefProvedor 	= $this->preferencias->obtemPreferenciasProvedor();
					$prefCobranca	= $this->preferencias->obtemPreferenciasCobranca();
					$this->_view->atribui("prefGeral",$prefGeral);
					$this->_view->atribui("prefProvedor",$prefProvedor);
					$this->_view->atribui("prefCobranca",$prefCobranca);
					
					for($i=0;$i<count($faturas);$i++) {
						$faturas[$i]["html_barcode"] = MBanco::htmlBarcode($faturas[$i]["cod_barra"],$urlPreto,$urlBranco);
						
						@list($a,$m,$d) = explode("-",$faturas[$i]["data"]);
						if($d && $m && $a) {
							$faturas[$i]["data"] = "$d/$m/$a";
						}
						
						$faturas[$i]["forma_pagamento"] = $this->preferencias->obtemFormaPagamento($faturas[$i]["id_forma_pagamento"]);
						
					}
					
					$emissao = date("d/m/Y");
					$this->_view->atribui("emissao",$emissao);
					$this->_view->atribui("faturas",$faturas);
					
					break;
				
				case 'cancelar_contrato':
				case 'contrato':
					// TELA DE DETALHES DO CONTRATO
					$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
					$this->_view->atribui("id_cliente_produto",$id_cliente_produto);
					
					$cliente = $this->clientes->obtemPeloId($this->id_cliente);
					$this->_view->atribui("cliente",$cliente);
					
					$cobranca = VirtexModelo::factory('cobranca');
										
					$endereco_cobranca = $cobranca->obtemEnderecoCobranca($id_cliente_produto);
					$this->_view->atribui("endereco_cobranca",$endereco_cobranca);
					
					$cidade_cobranca = array();
					if( @$endereco_cobranca["id_cidade"] ) {
						$cidade_cobranca = $this->preferencias->obtemCidadePeloId($endereco_cobranca["id_cidade"]);
					}
					$this->_view->atribui("cidade_cobranca",$cidade_cobranca);
					
					$contrato = $cobranca->obtemContratoPeloId($id_cliente_produto);
					// Alguns campos podem ser CHAR ou invés de VARCHAR
					foreach( $contrato as $vr => $vl ) {
						$contrato[$vr] = trim($vl);
					}
					$this->_view->atribui("contrato",$contrato);
					
					$formaPagamento = array();
					if($contrato["id_forma_pagamento"]) {
						$formaPagamento = $this->preferencias->obtemFormaPagamento($contrato["id_forma_pagamento"]);
					}
					$this->_view->atribui("formaPagamento",$formaPagamento);

					$tiposFormaPgto = $this->preferencias->obtemTiposFormaPagamento();
					$this->_view->atribui("tiposFormaPgto",$tiposFormaPgto);

					$bancos = $this->preferencias->obtemListaBancos();
					$this->_view->atribui("bancos",$bancos);

					$contas = VirtexModelo::factory('contas');
					$listaContas = $contas->obtemContasPorContrato($id_cliente_produto);
					for($i=0;$i<count($listaContas);$i++) {
						$listaContas[$i] = $contas->obtemContaPeloIdTipo($listaContas[$i]["id_conta"],$listaContas[$i]["tipo_conta"]);
					}
					$this->_view->atribui("listaContas",$listaContas);
					
					$exibirEstornadas = $contrato["status"] == "C" || $contrato["status"] == "M" ? true : false;
					
					$faturas = $cobranca->obtemFaturasPorContrato($id_cliente_produto,$exibirEstornadas);
					$this->_view->atribui("faturas",$faturas);
					
					$acao = @$_REQUEST["acao"];
					$dadosLogin = $this->_login->obtem("dados");
					
					$this->_view->atribui("dadosLogin",$dadosLogin);
					
					if( $acao ) {
						$erro = "";
						
						$senha_admin = @$_REQUEST["senha_admin"];
						
						if( !$senha_admin ) {
							$erro = "Cancelamento não autorizado: SENHA NÃO FORNECIDA.";
						} else {
							if( md5(trim($senha_admin)) != $dadosLogin["senha"] ) {
								$erro = "Operação não autorizada: SENHA NÃO CONFERE.";
							}
						
						}
						
						$this->_view->atribui("erro",$erro);
						
						if( !$erro ) {
						
							// Cancela as contas.
							for($i=0;$i<count($listaContas);$i++) {
								if( $listaContas[$i]["tipo_conta"] == "BL" ) {
									$contas->alteraContaBandaLarga($listaContas[$i]["id_conta"],"","C");
								} else {
									$contas->alteraConta($listaContas[$i]["id_conta"],"","C");
								}
							}
							
							// Estornar as faturas.
							for($i=0;$i<count($faturas);$i++) {
								if( $faturas[$i]["estornavel"] ) {
									$cobranca->estornaFatura($faturas[$i]["id_cobranca"]);
								}
							}
							
							// Cancela o contrato.
							$cobranca->cancelaContrato($id_cliente_produto);							
							
							$url = "admin-clientes.php?op=contrato&tela=contratos&id_cliente=" . $this->id_cliente;
							$msg = "Contrato cancelado com sucesso.";
							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem",$msg);
							$this->_view->atribuiVisualizacao("msgredirect");
							
							
							
						}
					
					
					}
					
					
					break;
				
				case 'migrar':
				case 'novo_contrato':
					$id_cliente_produto = @$_REQUEST["id_cliente_produto"];					
					$this->_view->atribui("id_cliente_produto",$id_cliente_produto);
				
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
					
					$cobranca = VirtexModelo::factory("cobranca");
					
					// Migração
					$contrato = $tela == "migrar" ? $cobranca->obtemContratoPeloId($id_cliente_produto) : array();
					$this->_view->atribui("contrato",$contrato);
					$this->_view->atribui("cttJSON",MJson::encode($contrato));
					
					if( $tela == "migrar" ) {
						// Forma de pagamento
						$id_forma_pagamento = $contrato["id_forma_pagamento"];
						$forma = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);

						$this->_view->atribui("forma_pagamento_original",$forma["tipo_cobranca"]);
						$this->_view->atribui("fp_original",$forma);

						// Contas que serão migradas:
						$contas = VirtexModelo::factory('contas');
						
						$listaContas = $contas->obtemContasPorContrato($id_cliente_produto);
						$this->_view->atribui("listaContas",$listaContas);

					}

					if( !$acao ) {
						//Cidades disponiveis
						$this->_view->atribui("cidades_disponiveis",$this->clientes->listaCidades());
						
						//Limite Prorata - criar funcao pra puxar do banco ex.: $this->preferencias->getLimiteProrata();
						$prefCobranca = $this->preferencias->obtemPreferenciasCobranca();
						$this->_view->atribui("limite_prorata", ((int)$prefCobranca["dias_minimo_cobranca"]));
						
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
						// $listaPOP = $equipamentos->obtemListaPOPs('A');
						$listaPOP = $equipamentos->obtemListaPOPs();
						$this->_view->atribui("listaPOP",$listaPOP);
						
						// Valores Padrão
						$data_contratacao = date("d/m/Y");
						
						$dia_venc 	= $tela == "migrar" ? $contrato["vencimento"] : $preferenciasCobranca["dia_venc"];
						$pagamento 	= $tela == "migrar" ? $contrato["pagamento"] 	: $preferenciasCobranca["pagamento"];
						$vigencia 	= $tela == "migrar" ? $contrato["vigencia"] 	: "12";
						$carencia 	= $tela == "migrar" ? $contrato["carencia"] 	: $preferenciasCobranca["carencia"];
						$comodato	= $tela == "migrar" ? $contrato["comodato"] 	: "f";
						

						$this->_view->atribui("dia_vencimento",$dia_venc);
						$this->_view->atribui("data_contratacao",$data_contratacao);
						$this->_view->atribui("pagamento",$pagamento);
						$this->_view->atribui("carencia",$carencia);
						$this->_view->atribui("vigencia",$vigencia);
						$this->_view->atribui("comodato","f");
						
					} else {


						// Informações do produto contratado
						$produto = $produtos->obtemPlanoPeloId(@$_REQUEST["id_produto"]);
						$valor = @$produto["valor"];
						$this->_view->atribui("produto",$produto);

						$endereco_cobranca = @$_REQUEST['endereco_cobranca'] ? $_REQUEST["endereco_cobranca"] : "";
						$endereco_instalacao = @$_REQUEST['endereco_instalacao'] ? $_REQUEST["endereco_instalacao"] : "";
						
						$bairro_cobranca = @$_REQUEST['bairro_cobranca'] ? $_REQUEST["bairro_cobranca"] : "";
						$bairro_instalacao = @$_REQUEST['bairro_instalacao'] ? $_REQUEST["bairro_instalacao"] : "";

						$complemento_cobranca = @$_REQUEST['complemento_cobranca'] ? $_REQUEST["complemento_cobranca"] : "";
						$complemento_instalacao = @$_REQUEST['complemento_instalacao'] ? $_REQUEST["complemento_instalacao"] : "";
						
						$cep_cobranca = @$_REQUEST['cep_cobranca'] ? $_REQUEST["cep_cobranca"] : "";
						$cep_instalacao = @$_REQUEST['cep_instalacao'] ? $_REQUEST["cep_instalacao"] : "";

						$cidade_cobranca = @$_REQUEST['id_cidade_cobranca'] ? $this->preferencias->obtemCidadePeloId($_REQUEST["id_cidade_cobranca"]) : array();
						$cidade_instalacao = @$_REQUEST['id_cidade_instalacao'] ? $this->preferencias->obtemCidadePeloId($_REQUEST["id_cidade_instalacao"]) : array();

			            if ( ! $endereco_cobranca ) {
			                 $endereco_cobranca = $info['endereco'];
			            }

			            if ( ! $endereco_instalacao ) {
			                 $endereco_instalacao = $endereco_cobranca;
			            }
			            
			            if ( ! $bairro_cobranca ) {
			                 $bairro_cobranca = $info['bairro'];
			            }

			            if ( ! $bairro_instalacao ) {
			                 $bairro_instalacao = $bairro_cobranca;
			            }
			            
			            if ( ! $cep_cobranca ) {
			                 $cep_cobranca = $info['cep'];
			            }

			            if ( ! $cep_instalacao ) {
			                 $cep_instalacao = $cep_cobranca;
			            }
			            
			            if ( ! $complemento_cobranca ) {
			                 $complemento_cobranca = $info['complemento'];
			            }

			            if ( ! $complemento_instalacao ) {
			                 $complemento_instalacao = $complemento_cobranca;
			            }
			            
			            if ( ! count($cidade_cobranca) ) {
			                 $cidade_cobranca = $this->preferencias->obtemCidadePeloId($info['id_cidade']);
			            }
			            
			            if ( ! count($cidade_instalacao) ) {
			                 $cidade_instalacao = $cidade_cobranca;
			            }

			            $this->_view->atribui("endereco_cobranca",trim($endereco_cobranca));
			            $this->_view->atribui("endereco_instalacao",trim($endereco_instalacao));

			            $this->_view->atribui("bairro_cobranca",trim($bairro_cobranca));
			            $this->_view->atribui("bairro_instalacao",trim($bairro_instalacao));

			            $this->_view->atribui("complemento_cobranca",trim($complemento_cobranca));
									$this->_view->atribui("complemento_instalacao",trim($complemento_instalacao));

			            $this->_view->atribui("cep_cobranca",trim($cep_cobranca));
			            $this->_view->atribui("cep_instalacao",trim($cep_instalacao));

			            $this->_view->atribui("cidade_cobranca",$cidade_cobranca);
						$this->_view->atribui("cidade_instalacao",$cidade_instalacao);
						
						$tipo = @$_REQUEST["tipo"];

						if( $tela != "migrar" ) {
							if( $tipo == "BL" ) {
								$id_nas = @$_REQUEST["id_nas"];
								$nas = $equipamentos->obtemNAS($id_nas);
								$this->_view->atribui("nas",$nas);

								$id_pop = @$_REQUEST["id_pop"];
								$pop = $equipamentos->obtemPOP($id_pop);
								$this->_view->atribui("pop",$pop);
							}
						}

						$keys = array_keys($_REQUEST);
			            for($i=0;$i<count($keys);$i++) {
			               $this->_view->atribui($keys[$i],$_REQUEST[$keys[$i]]);
			            }

						// Lista das faturas que serão geradas
						// TODO: Verificar se é cortesia
						$faturas = $cobranca->gerarListaFaturas(@$_REQUEST["pagamento"],@$_REQUEST["data_contratacao"],@$_REQUEST["vigencia"],@$_REQUEST["dia_vencimento"],$valor,@$_REQUEST["desconto_promo"],@$_REQUEST["periodo_desconto"],@$_REQUEST["tx_instalacao"],@$_REQUEST["valor_comodato"],@$_REQUEST["primeiro_vencimento"],@$_REQUEST["pro_rata"],@$_REQUEST["limite_prorata"]);
						$this->_view->atribui("faturas",$faturas);

						
						if( $acao == "novo_contrato" ) {

							// Validação dos dados entrados
							
							// Informações do Contratante (cliente)
							$cliente = $this->clientes->obtemPeloId($this->id_cliente);
							$cidade = $this->preferencias->obtemCidadePeloId($cliente["id_cidade"]);
							$cliente["cidade"] = $cidade["cidade"] . "-" . $cidade["uf"];
							$this->_view->atribui("cliente",$cliente);

							$form_post = @$_REQUEST;
							$form_post["acao"] = "gravar_novo_contrato";
							$this->_view->atribui("form_post",$form_post);
							
							
							// Seleção de dados para exibição na tela de confirmação.
							
						}
						

						if( !$id_forma_pagamento ) {
							$id_forma_pagamento = @$_REQUEST["id_forma_pagamento_" . @$_REQUEST["forma_pagamento"]];
							if( $id_forma_pagamento ) {
								$formaPagamento = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);
								$this->_view->atribui("formaPagamento",$formaPagamento);
							}
						}
							


						if( $acao == "gravar_novo_contrato" ) {
						
							$erro = "";

							$senha_admin = @$_REQUEST["senha_admin"];
							$dadosLogin = $this->_login->obtem("dados");

							if( !$senha_admin ) {
								$erro = "Cancelamento não autorizado: SENHA NÃO FORNECIDA.";
							} else {
								if( md5(trim($senha_admin)) != $dadosLogin["senha"] ) {
									$erro = "Operação não autorizada: SENHA NÃO CONFERE.";
								}

							}

							$this->_view->atribui("erro",$erro);
							$this->_view->atribui("acao","novo_contrato");
							
							if( !$erro ) {

								$dados_produto = $produtos->obtemPlanoPeloId($_REQUEST["id_produto"]);
	
								$dominio = @$_REQUEST["dominio"] ? $_REQUEST["dominio"] : "";
								$data_renovacao = MData::adicionaMes($_REQUEST["primeiro_vencimento"], $_REQUEST["vigencia"]);
								$valor_contrato = 0;
								$id_cobranca = 0;
								$status = "A";

								$da_codigo_banco = @$_REQUEST["da_codigo_banco"] ? $_REQUEST["da_codigo_banco"] : "";
								$da_carteira = @$_REQUEST["da_carteira"] ? $_REQUEST["da_carteira"] : "";
								$da_convenio = @$_REQUEST["da_convenio"] ? $_REQUEST["da_convenio"] : "";
								$da_agencia = @$_REQUEST["da_agencia"] ? $_REQUEST["da_agencia"] : "";
								$da_num_conta = @$_REQUEST["da_conta"] ? $_REQUEST["da_conta"] : "";

								$da_dados = array( "codigo_banco" => $da_codigo_banco, "carteira" => $da_carteira, "convenio" => $da_convenio, "agencia" => $da_agencia, "num_conta" => $da_num_conta );

								$bl_codigo_banco = @$_REQUEST["bl_codigo_banco"] ? $_REQUEST["bl_codigo_banco"] : "";
								$bl_carteira = @$_REQUEST["bl_carteira"] ? $_REQUEST["bl_carteira"] : "";
								$bl_convenio = @$_REQUEST["bl_convenio"] ? $_REQUEST["bl_convenio"] : "";
								$bl_agencia = @$_REQUEST["bl_agencia"] ? $_REQUEST["bl_agencia"] : "";
								$bl_num_conta = @$_REQUEST["bl_conta"] ? $_REQUEST["bl_conta"] : "";

								$bl_dados = array( "codigo_banco" => $bl_codigo_banco, "carteira" => $bl_carteira, "convenio" => $bl_convenio, "agencia" => $bl_agencia, "num_conta" => $bl_num_conta );

								$codigo_banco = @$_REQUEST["codigo_banco"] ? $_REQUEST["codigo_banco"] : "";
								$carteira = @$_REQUEST["carteira"] ? $_REQUEST["carteira"] : "";
								$convenio = @$_REQUEST["convenio"] ? $_REQUEST["convenio"] : "";
								$agencia = @$_REQUEST["agencia"] ? $_REQUEST["agencia"] : "";
								$num_conta = @$_REQUEST["conta"] ? $_REQUEST["conta"] : "";

								$pro_dados = array( "codigo_banco" => $codigo_banco, "carteira" => $carteira, "convenio" => $convenio, "agencia" => $agencia, "num_conta" => $num_conta );

								$endereco_cobranca = array( "endereco" => $_REQUEST["endereco_cobranca"], "id_cidade" => $_REQUEST["id_cidade_cobranca"], "cep" => $_REQUEST["cep_cobranca"], "bairro" => $_REQUEST["bairro_cobranca"], "complemento" => $_REQUEST["complemento_cobranca"] );
								$endereco_instalacao = array( "endereco" => $_REQUEST["endereco_instalacao"], "id_cidade" => $_REQUEST["id_cidade_instalacao"], "cep" => $_REQUEST["cep_instalacao"], "bairro" => $_REQUEST["bairro_instalacao"], "complemento" => $_REQUEST["complemento_instalacao"] );
								
								$dados_conta = array();
								$cria_e = 0;
								
								$dia_vencimento = @$_REQUEST["dia_vencimento"];
								$carencia 		= @$_REQUEST["carencia"];
								
								/**
								 * Procedimentos específicos da contratação
								 */
								if( $tela != "migrar" ) {
									switch (trim($_REQUEST["tipo"])) {
										case "BL":
											$ip = @$_REQUEST["endereco_redeip"] ? $_REQUEST["endereco_redeip"] : "";
											$dados_conta = array( "id_nas"=>$_REQUEST["id_nas"], "id_pop"=>$_REQUEST["id_pop"], "endereco"=>$ip, "mac"=>$_REQUEST["mac"] );
											break;
										case "D":
											$dados_conta = array( "foneinfo"=>$_REQUEST["foneinfo"] );
											break;
										case "H";
											$dados_conta = array( "tipo_hospedagem"=>$_REQUEST["tipo_hospedagem"], "dominio_hospedagem"=>$_REQUEST["dominio_hospedagem"] );
											break;
									}

									$cria_e = @$_REQUEST["criar_email"] ? 1 : 0;

									/*cadastraContaEmail($username,$dominio,$senha,$id_cliente,$id_cliente_produto,$status,
													$observacoes,$conta_meste,
													$quota,$redirecionar_para="")*/

									
									$prefCobranca	= $this->preferencias->obtemPreferenciasCobranca();
									$dia_vencimento = ( $dia_vencimento > 0 ) ? $dia_vencimento : $prefCobranca["dia_venc"];
									$carencia 		= ( $carencia > 0 ) ? $carencia : $prefCobranca["carencia"];
								}

								$gera_carne = false;
								$this->_view->atribui ("gera_carne", $gera_carne);
								
								// $listaContas = $contas->obtemContasPorContrato($id_cliente_produto);
								
								$novo_id_cliente_produto = $cobranca->novoContrato(	$_REQUEST["id_cliente"], $_REQUEST["id_produto"], $dominio, $_REQUEST["data_contratacao"], $_REQUEST["vigencia"], $_REQUEST["pagamento"],
																					$data_renovacao, $valor_contrato, $_REQUEST["username"], $_REQUEST["senha"], $id_cobranca, $status, $_REQUEST["tx_instalacao"], $_REQUEST["valor_comodato"],
																					$_REQUEST["desconto_promo"], $_REQUEST["desconto_periodo"], $dia_vencimento, $_REQUEST["primeiro_vencimento"], $_REQUEST["prorata"], $_REQUEST["limite_prorata"], $carencia,
																					$_REQUEST["id_produto"], $id_forma_pagamento, $pro_dados, $da_dados, $bl_dados, $cria_e, $dados_produto, $endereco_cobranca, $endereco_instalacao, $dados_conta, $gera_carne);

								if( $tela == "migrar" ) {
									// ESTORNAR/MIGRAR AS FATURAS 
									$faturas = $cobranca->obtemFaturasPorContrato($id_cliente_produto,$exibirEstornadas);

									// Estornar as faturas.
									for($i=0;$i<count($faturas);$i++) {
										if( $faturas[$i]["estornavel"] ) {
											$cobranca->estornaFatura($faturas[$i]["id_cobranca"]);
										} else {
											// Migrar faturas não-pagas p/ novo contrato.
											if( $daturas[$i]["status"] != "P" ) {
												$cobranca->migrarFatura($faturas[$i]["id_cobranca"],$novo_id_cliente_produto);
											}
										}
									}
									
									
									// Migra as contas.
									for($i=0;$i<count($listaContas);$i++) {
										$contas->migrarConta($listaContas[$i]["id_conta"],$novo_id_cliente_produto);
									
										switch( trim($listaContas[$i]["tipo_conta"]) ) {
											case "BL":
												$contas->alteraContaBandaLarga($listaContas[$i]["id_conta"],"",$listaContas[$i]["status"],$listaContas[$i]["observacoes"],
																				$listaContas[$i]["conta_mestre"],$listaContas[$i]["id_pop"],$listaContas[$i]["id_nas"],
																				$produto["banda_upload_kbps"], $produto["banda_download_kbps"], $listaContas[$i]["mac"],
																				"",false);
												break;
											case "E":
												$contas->alteraContaEmail($listaContas[$i]["id_conta"],"",$listaContas[$i]["status"],$listaContas[$i]["observacoes"],
																			$listaContas[$i]["conta_mestre"],$produto["quota_por_conta"]);
												break;
											case "H":
												// TODO
												break;
											case "D":
												// TODO
												break;
										}
									}
									
									$cobranca->migrarContrato($id_cliente_produto,$novo_id_cliente_produto,$dadosLogin["admin"]);
							
								}
								
								$url = "admin-clientes.php?op=contrato&tela=contrato&id_cliente=".$this->id_cliente."&id_cliente_produto=" . $novo_id_cliente_produto;
								$mensagem = $tela == "migrar" ? "Migração realizada com sucesso" : "Contratação realizada com sucesso";
								$this->_view->atribui("url",$url);
								$this->_view->atribui("mensagem",$mensagem);
								$this->_view->atribuiVisualizacao("msgredirect");

							}
						}

					}
					
					break;
				
				case "contratos":
					$cobranca = VirtexModelo::factory("cobranca");
					$contratos = $cobranca->obtemContratos ($_REQUEST ["id_cliente"]);
					$this->_view->atribui ('contratos', $contratos);
				break;
				
				
				case "faturas":
					
					$tem_carne = "";
					$cobranca = VirtexModelo::factory("cobranca"); 
					$faturas = $cobranca->obtemFaturas ($_REQUEST ['id_cliente'], $tem_carne, $_REQUEST ['id_cliente_produto'], 
								 $_REQUEST ['id_forma_pagamento'], $_REQUEST ['id_carne']);
			
					$this->_view->atribui ('tem_carne', $tem_carne);
					if ($tem_carne && $_REQUEST ["id_carne"] > 0) {
						$acao = 'faturas';
					}
					elseif ($tem_carne) {
						$acao = 'carnes';
					}
					else {
						$acao = "faturas";
					}
					$this->_view->atribui ('faturas', $faturas);
					$this->_view->atribui ('acao', $acao);
					$this->_view->atribui ('id_forma_pagamento', $_REQUEST ['id_forma_pagamento']);
					break;
				
				case "amortizacao":
				
					$id_cliente_produto = @$_REQUEST ['id_cliente_produto'];
					$id_cliente = @$_REQUEST ['id_cliente'];
					$id_cobranca = @$_REQUEST ['id_cobranca'];
					$data = @$_REQUEST ['data'];
					$acao = @$_REQUEST ['acao'];
					$cobranca = VirtexModelo::factory("cobranca");
					$erro = false;
					
					if($acao) {
						$desconto		= @$_REQUEST["desconto"];
						$acrescimo		= @$_REQUEST["acrescimo"];
						$amortizar		= @$_REQUEST["amortizar"];
						$data_pagamento	= @$_REQUEST["data_pagamento"];
						$reagendar		= @$_REQUEST["reagendar"];
						$reagendamento	= @$_REQUEST["reagendamento"];
						$observacoes	= @$_REQUEST["observacoes"];
						
						$url = "admin-clientes.php?op=contrato&tela=faturas&id_cliente=$id_cliente";
						
						try {
							if( $cobranca->amortizarFatura($id_cobranca, $desconto, $acrescimo, $amortizar, $data_pagamento, $reagendar,
									$reagendamento, $observacoes) ) {
									$this->_view->atribui("url",$url);
									$this->_view->atribui("mensagem","Dados atualizados com sucesso!");
									$this->_view->atribuiVisualizacao("msgredirect");
							} else {
								VirtexView::simpleRedirect($url);
							}
						} catch (ExcecaoModeloValidacao $e ) {
							$this->_view->atribui ("msg_erro", $e->getMessage());
							$erro = true;	
						}
					}
					
					
					$fatura = $cobranca->obtemFaturaPorIdCobranca ($id_cobranca);
					
					foreach($fatura as $k => $v){
						if("data"  == $k ) {
							$v = strftime("%d/%m/%Y",strtotime($v));
						} 
						$this->_view->atribui ($k, $v);
					}
					
					$valor_restante = (float) ( (float) $fatura["valor"] - (float) $fatura["desconto"] - (float) $fatura["pagto_parcial"] + (float) $fatura["acrescimo"] );
					$valor_restante = number_format($valor_restante,2);   
					$this->_view->atribui ("valor_restante", $valor_restante);
					
					
					if(	$fatura["status"] == PERSISTE_CBTB_FATURAS::$CANCELADA or 
						$fatura["status"] == PERSISTE_CBTB_FATURAS::$ESTORNADA or
						$fatura["status"] == PERSISTE_CBTB_FATURAS::$PAGA ) {
							$this->_view->atribui ("editavel", false);
					} else {
						$this->_view->atribui ("editavel", true);
					}
					
					$this->_view->atribui("status_fatura",$cobranca->obtemStatusFatura());
					 
					if($erro){
						$this->_view->atribui("desconto",$desconto);	
						$this->_view->atribui("acrescimo",$acrescimo);
						$this->_view->atribui("amortizar",$amortizar);
						$this->_view->atribui("data_pagamento",$data_pagamento);
						$this->_view->atribui("reagendar",$reagendar);
						$this->_view->atribui("reagendamento",$reagendamento);
						$this->_view->atribui("observacoes",$observacoes);
					}
					break;
			
				default:
					// Resumo
					$cobranca = VirtexModelo::factory("cobranca");
					$contratos = $cobranca->obtemContratos ($_REQUEST ["id_cliente"],"A");
					$this->_view->atribui ('contratos', $contratos);
					
		
					break;
			
			}
			
			
		}
		
		protected function executaConta() {
			$this->_view->atribuiVisualizacao("conta");
			$tipo = trim($_REQUEST["tipo"]);
			$this->_view->atribui("tipo",$tipo);

			$this->_view->atribui("id_cliente",$this->id_cliente);
			
			
			$info = $this->clientes->obtemPeloId($this->id_cliente);
			
			$this->_view->atribui("nome_razao",$info["nome_razao"]);
			
			$cobranca = VirtexModelo::factory("cobranca");
			$contas = VirtexModelo::factory("contas");

			
			$tela 		= @$_REQUEST["tela"];
			$id_conta 	= @$_REQUEST["id_conta"];
			$acao 		= @$_REQUEST["acao"];
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			
			
			$this->_view->atribui("tela",$tela);
			$this->_view->atribui("id_conta",$id_conta);
			$this->_view->atribui("acao",$acao);
			$this->_view->atribui("id_cliente_produto",$id_cliente_produto);
			
			if( $id_conta && !$acao ) {
				$info = $contas->obtemContaPeloId($id_conta);

				foreach($info as $vr => $vl) {
					$this->_view->atribui($vr,$vl);
				}
				
				if("C" == $info["status"]) {
					$this->_view->atribui("status_msg","CANCELADO");
					$this->_view->atribui("formDisabled",true);
				} elseif("S" == $info["status"]) {
					$this->_view->atribui("status_msg","SUSENSO");
					$this->_view->atribui("formDisabled",true);
				} else {
					$this->_view->atribui("status_msg","");
					$this->_view->atribui("formDisabled",false);
				}
				
				if( @$info["id_cliente_produto"] ) {
					$contrato = $cobranca->obtemContratoPeloId($info["id_cliente_produto"]);
					$tipo_contrato = trim($contrato["tipo_produto"]);
					$this->_view->atribui("tipo_contrato",$tipo_contrato);
				}

				
			}
			
			if( !$tipo && $id_cliente_produto ) {
				$infoProduto = $cobranca->obtemContratoPeloId($id_cliente_produto);
				$tipo = trim($infoProduto["tipo_produto"]);
				$this->_view->atribui("tipo",$tipo);
			}

			$equipamentos = VirtexModelo::factory('equipamentos');
			$preferenciasGerais = $this->preferencias->obtemPreferenciasGerais();
			$this->_view->atribui("preferenciasGerais",$preferenciasGerais);
			
			if( $tela == "ficha"  ) {
				// Informações específicas da ficha.
				
				if($info["tipo_conta"] == "BL") {
					$nas = $equipamentos->obtemNAS($info["id_nas"]);					
					$this->_view->atribui("nas",$nas);
					$pop = $equipamentos->obtemPOP($info["id_pop"]);
					$this->_view->atribui("pop",$pop);
							
					
					$infoConta == array();
					if( $nas["tipo_nas"] == "I" ) {
						$endereco = $info["rede"];
						if( $endereco ) {
							$ip = new MInet($endereco);
							$infoConta["ip"] = $ip->obtemUltimoIP();
							$infoConta["mascara"] = $ip->obtemMascara();
							$infoConta["gateway"] = $ip->obtemPrimeiroIP();
						}
					} else {
						$infoConta["ip"] = $info["ipaddr"] . "(config. automática)";
						$infoConta["mascara"] = "255.255.255.0";
						$infoConta["gateway"] = "PPPoE";
					}
					$this->_view->atribui("infoConta",$infoConta);	
				} 
			} else if( $tela == "cadastro" ) {
				
				if( $acao ) {
					// Processar alteração/cadastro.
					
					$username			= @$_REQUEST["username"];
					$status				= @$_REQUEST["status"];
					$conta_mestre		= @$_REQUEST["conta_mestre"];
					$id_cliente			= @$_REQUEST["id_cliente"];
					$senha 				= @$_REQUEST["senha"];
					$confsenha 			= @$_REQUEST["confsenha"];
					$mac 				= @$_REQUEST["mac"];
					$id_nas 			= @$_REQUEST["id_nas"];
					$id_pop 			= @$_REQUEST["id_pop"];
					$endereco_redeip	= @$_REQUEST["endereco_redeip"];
					$upload 			= @$_REQUEST["upload"];
					$download 			= @$_REQUEST["download"];
					$difEnderecoSetup 	= @$_REQUEST["difEnderecoSetup"];
					$observacoes 		= @$_REQUEST["observacoes"];
					$quota 				= @$_REQUEST["quota"];
					$dominio 			= $preferenciasGerais["dominio_padrao"];
					
					$tipo_hospedagem	= @$_REQUEST["tipo_hospedagem"];
					$dominio_hospedagem = @$_REQUEST["dominio_hospedagem"];
					
					$alterar_endereco 	= (bool) (@$_REQUEST["altera_rede"] == "t");
					
					$senha = $senha == $confsenha ? $senha : "";
					
					$selecao_redeip		= @$_REQUEST["selecao_redeip"];
					
					
					$contrato = $cobranca->obtemContratoPeloId($id_cliente_produto);					
					$url = "admin-clientes.php?op=conta&tipo=".@$contrato["tipo_produto"]."&id_cliente=".$this->id_cliente;
					
					// todo: buscar esse dados
					//  $observacoes, $upload,$download
					
					
					if($id_conta) {  // alteração
						if("BL" == $tipo){
							
							$contas->alteraContaBandaLarga($id_conta,$senha, $status,$observacoes,$conta_mestre,
									$id_pop,$id_nas,$upload,$download,$mac,$endereco_redeip,$alterar_endereco);

							if($difEnderecoSetup){						
								$info_endereco = $contas->obtemEnderecoInstalacaoPelaConta($id_conta);
								unset(	$info_endereco["id_endereco_instalacao"], 
										$info_endereco["id_conta"],
										$info_endereco["id_cliente"] );
							
								$endereco_instalacao = array(
															"endereco" => @$_REQUEST["endereco_instalacao"],
															"bairro" => @$_REQUEST["bairro_instalacao"], 
															"id_cidade" => @$_REQUEST["id_cidade_instalacao"], 
															"complemento" => @$_REQUEST["complemento_instalacao"],
															"cep" => @$_REQUEST["cep_instalacao"]
														);
								if($info_endereco != $endereco_instalacao){
									$contas->cadastraEnderecoInstalacao($id_conta,$endereco_instalacao["endereco"],$endereco_instalacao["complemento"],$endereco_instalacao["bairro"],
																		$endereco_instalacao["id_cidade"], $endereco_instalacao["cep"], $this->id_cliente);
								}
							}
							$msg = "Conta alterada com sucesso.";
						} elseif("E" == $tipo){
							$contas->alteraContaEmail($id_conta,$senha,$status,$observacoes,$conta_mestre,$quota);
							$msg = "Email alterado com sucesso.";
						} elseif($tipo == "D") {
							$contas->alteraContaDiscado($id_conta,$senha,$status,$observacoes,$conta_mestre,$foneinfo);
							$msg = "Conta alterada com sucesso.";
						} elseif($tipo == "H") {
							$contas->alteraContaHospedagem($id_conta,$senha,$status,$observacoes,$conta_mestre);
							$msg = "Conta alterada com sucesso.";
						} else {							
							// die("tipo inválido!");
						}

						$this->_view->atribui("url",$url);
						$this->_view->atribui("mensagem",$msg);
						$this->_view->atribuiVisualizacao("msgredirect");
						
						
					} else {  //  cadastro
					
						
						if("BL" == $tipo){
							$contas->cadastraContaBandaLarga($username,$dominio,$senha,$id_cliente,$id_cliente_produto,$status,
															 $observacoes,$conta_mestre,$id_pop,$id_nas,$upload,$download,$mac,$endereco_redeip);
															 
							if($difEnderecoSetup){
								$endereco_instalacao = array(
															"endereco" => @$_REQUEST["endereco_instalacao"],
															"bairro" => @$_REQUEST["bairro_instalacao"], 
															"id_cidade" => @$_REQUEST["id_cidade_instalacao"], 
															"complemento" => @$_REQUEST["complemento_instalacao"],
															"cep" => @$_REQUEST["cep_instalacao"]
														);
							} else {
								$info_endereco = $contas->obtemEnderecoInstalacaoPelaConta($id_conta);
							}
							
							$contas->cadastraEnderecoInstalacao($id_conta,$endereco_instalacao["endereco"],$endereco_instalacao["complemento"],$endereco_instalacao["bairro"],
																$endereco_instalacao["id_cidade"], $endereco_instalacao["cep"], $this->id_cliente);
							$msg = "Conta cadastrada com sucesso.";
						} elseif("E" == $tipo){
						
							$dominio = isset($_REQUEST["sel_dominio"]) ? $_REQUEST["sel_dominio"] : $dominio;
							$contas->cadastraContaEmail($username,$dominio,$senha,$id_cliente,$id_cliente_produto,$status,
														$observacoes,$conta_meste, $quota) ;
							$msg = "Email cadastrado com sucesso.";
						} elseif( $tipo == "D" ) {
							$contas->cadastraContaDiscado($username,$dominio,$senha,$id_cliente,$id_cliente_produto,$status,
														$observacoes,$conta_meste, $foneinfo) ;
							$msg = "Conta cadastrada com sucesso.";
						
						} elseif( $tipo == "H" ) {
							$contas->cadastraContaHospedagem($username,$dominio,$senha,$id_cliente,$id_cliente_produto,$status,
														$observacoes,$conta_meste, $tipo_hospedagem,$dominio_hospedagem) ;
							$msg = "Conta cadastrada com sucesso.";						
						}
						
						$this->_view->atribui("url",$url);
						$this->_view->atribui("mensagem",$msg);
						$this->_view->atribuiVisualizacao("msgredirect");
					}
					
				} else {
					$this->_view->atribui("cidades_disponiveis",$this->clientes->listaCidades());
					$listaNAS = $equipamentos->obtemListaNAS();
					$this->_view->atribui("listaNAS",$listaNAS);
					// $listaPOP = $equipamentos->obtemListaPOPs('A');
					$listaPOP = $equipamentos->obtemListaPOPs();
					$this->_view->atribui("listaPOP",$listaPOP);
					$tiposNas = $equipamentos->obtemTiposNAS();					
					$this->_view->atribui("tiposNAS",$tiposNas);
					$bandas = $this->preferencias->obtemListaBandas();
					$this->_view->atribui("bandas",$bandas);
						
					if( trim($info["tipo_conta"]) == "BL" || trim($info["tipo_conta"]) == "D") {
					
						$endereco_instalacao = $contas->obtemEnderecoInstalacaoPelaConta($id_conta);
						 if(!count($endereco_instalacao)){
							$endereco_instalacao = $this->clientes->obtemPeloId($this->id_cliente);
							$contas->cadastraEnderecoInstalacao($id_conta,$endereco_instalacao["endereco"],$endereco_instalacao["complemento"],$endereco_instalacao["bairro"],
																$endereco_instalacao["id_cidade"], $endereco_instalacao["cep"], $this->id_cliente);
						}
						
						$nas = $equipamentos->obtemNAS($info["id_nas"]);
						$endereco_ip = $nas["tipo_nas"] == "I" ? $info["rede"] : $info["ippaddr"];
						$this->_view->atribui("endereco_ip",$endereco_ip);
						
						$this->_view->atribui("endereco",$endereco_instalacao["endereco"]);
						$this->_view->atribui("bairro",$endereco_instalacao["bairro"]);
						$this->_view->atribui("id_cidade",$endereco_instalacao["id_cidade"]);
						$this->_view->atribui("complemento",$endereco_instalacao["complemento"]);
						$this->_view->atribui("cep",$endereco_instalacao["cep"]);
					
					} 

					$qtde = $contas->obtemQtdeContasPorContrato($id_cliente_produto, $tipo);
					$contrato = $cobranca->obtemContratoPeloId($id_cliente_produto);

					if( $tipo == "E" ) {
						if( !$id_conta && $contrato["num_emails"] > 0 ){			
							$qtdeDisponivel = $contrato["num_emails"] - $qtde["num_contas"];							
							if($qtdeDisponivel <= 0 ){
								die("não existe mais contas disponiveis!");
							}
						}

						if( !$id_conta ) {
							// Pega a cota do contrato;
							$quota = $contrato["quota_por_conta"];
							$this->_view->atribui("quota",$quota);
						}

						$permite = $contrato["permitir_outros_dominios"] == 't' ? true : false;
						$this->_view->atribui("permite",$permite);

						if($permite){
							$listaDominios = $this->preferencias->obtemListaDominios($this->id_cliente);

						} else {
							$listaDominios = $preferenciasGerais["dominio_padrao"];
						}

						$this->_view->atribui("listaDominios",$listaDominios);
					} else {
						$qtdeDisponivel = $contrato["numero_contas"] - $qtde["num_contas"];	
						if(!$id_conta && $qtdeDisponivel <= 0 ){
							die("não existe mais contas disponiveis!");
						}
					}
				}
			} else {
				// Listagem
				
				$listaContratos = $cobranca->obtemContratos($this->id_cliente,"A",$tipo);
				
				for($i=0;$i<count($listaContratos);$i++) {
					$listaContas = $contas->obtemContasPorContrato($listaContratos[$i]["id_cliente_produto"]);
					$countContas = count($listaContas);
					
					$numContasEmail = 0;
					$numContasTipo = 0;
					
					$contasContrato = array();
					foreach($listaContas as $rowConta){
						 $conta = $contas->obtemContaPeloId($rowConta["id_conta"]);
						 $contasContrato[] = $conta;
						 
						 if( trim($conta["tipo_conta"]) == "E" ) {
						 	$numContasEmail++;
						 } else {
						 	$numContasTipo++;
						 }
						 
					}
					
					$listaContratos[$i]["numContasEmail"] = $numContasEmail;
					$listaContratos[$i]["numContasTipo"] = $numContasTipo;

					$listaContratos[$i]["contas"] = $contasContrato;
										
					$contrato = $cobranca->obtemContratoPeloId($listaContratos[$i]["id_cliente_produto"]);
					$listaContratos[$i]["qtdeTipoDisponivel"] = $contrato["numero_contas"] - $numContasTipo;

					if( $contrato["num_emails"] == 0 ){
						$listaContratos[$i]["emailIlimitado"] = true;
						$listaContratos[$i]["qtdeEmailsDisponivel"] = -1;
					} else {
						$listaContratos[$i]["emailIlimitado"] = false;
						$listaContratos[$i]["qtdeEmailsDisponivel"] = $contrato["num_emails"] - $numContasEmails;;
					}
					
					unset($listaContas);
				}

				$this->_view->atribui("listaContratos",$listaContratos);
			}
			
		}

		protected function executaRelatorios() {
			$this->_view->atribuiVisualizacao("relatorios");
			
			$relatorio = @$_REQUEST["relatorio"];
			$this->_view->atribui("relatorio",$relatorio);
			
			
			$cobranca = VirtexModelo::factory('cobranca');
			
			switch($relatorio) {
				case 'lista_geral':
					$inicial 	= @$_REQUEST["inicial"];
					$acao 		= @$_REQUEST["acao"];
					
					$this->_view->atribui("acao",$acao);
					$this->_view->atribui("inicial",$inicial);
					
					$iniciais = array("0-9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","Y","X","Z");
					$this->_view->atribui("iniciais",$iniciais);
					
					$listaClientes = array();
					
					if( !$acao && !$inicial ) {
						// Últimos Cadastrados
						$listaClientes = $this->clientes->obtemUltimos(5);
					} elseif($inicial) {
						// Letra Inicial
						$listaClientes = $this->clientes->obtemPelaInicial($inicial);
					} elseif($acao == "TODOS") {
						// TODOS
						$listaClientes = $this->clientes->obtem(array());
					}
					
					$cache_cidades = array();
					
					for($i=0;$i<count($listaClientes);$i++) {
						if( !@$cache_cidades[$listaClientes[$i]["id_cidade"]] ) {
							$cache_cidades[$listaClientes[$i]["id_cidade"]] = $this->preferencias->obtemCidadePeloID($listaClientes[$i]["id_cidade"]);
						}
						
						$listaClientes[$i]["cidade"] = $cache_cidades[$listaClientes[$i]["id_cidade"]];					

						$contratos = $cobranca->obtemContratos ($listaClientes[$i]["id_cliente"],"A");

						$cbl = 0;
						$cd  = 0;
						$ch  = 0;

						foreach($contratos as $contrato) {
							switch( trim($contrato["tipo_produto"]) ) {
								case 'BL':
									$cbl++;
									break;
								case 'D':
									$cd++;
									break;
								case 'H':
									$ch++;
									break;
							}
						}

						$listaClientes[$i]["contratos_bl"] = $cbl;
						$listaClientes[$i]["contratos_d"]  = $cd;
						$listaClientes[$i]["contratos_h"]  = $ch;

					}
					
					$this->_view->atribui("listaClientes",$listaClientes);					
					break;

				case 'cliente_cidade':
					$id_cidade 	= @$_REQUEST["id_cidade"];
					$this->_view->atribui("id_cidade",$id_cidade);
					
					$registros = array();
					
					if( $id_cidade ) {
						$registros = $this->clientes->obtemClientesPorCidade($id_cidade);
						
						$infoCidade = $this->preferencias->obtemCidadePeloID($id_cidade);
						$this->_view->atribui("cidade",$infoCidade["cidade"]);
						$this->_view->atribui("uf",$infoCidade["uf"]);
						
						for($i=0;$i<count($registros);$i++) {
							$contratos = $cobranca->obtemContratos ($registros[$i]["id_cliente"],"A");

							$cbl = 0;
							$cd  = 0;
							$ch  = 0;
							
							foreach($contratos as $contrato) {
								switch( trim($contrato["tipo_produto"]) ) {
									case 'BL':
										$cbl++;
										break;
									case 'D':
										$cd++;
										break;
									case 'H':
										$ch++;
										break;
								}
							}
							
							$registros[$i]["contratos_bl"] = $cbl;
							$registros[$i]["contratos_d"]  = $cd;
							$registros[$i]["contratos_h"]  = $ch;
							
						}
						
						$numClientes = count($registros);
						

					} else {
						$registros = $this->clientes->countClientesPorCidade();
						$numClientes = 0;
						for($i=0;$i<count($registros);$i++) {
							$numClientes += $registros[$i]["count"];
						}
						
					}

					$this->_view->atribui("numClientes",$numClientes);
					
					
					$this->_view->atribui("registros",$registros);
					
					break;
					
			}
			
			









		}

		protected function executaEliminar() {
			$this->_view->atribuiVisualizacao("eliminar");
			echo "EXECUTA ELIMINAR<br>\n";
		}

	}

?>