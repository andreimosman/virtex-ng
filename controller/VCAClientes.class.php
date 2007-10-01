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
			// echo "EXECUTA PESQUISA<br>\n";
		}
		
		
		protected function executaContrato() {
		
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

					$cobranca = VirtexModelo::factory("cobranca");
					
					if( !$acao ) {
						//Cidades disponiveis
						$this->_view->atribui("cidades_disponiveis",$this->clientes->listaCidades());
						
						//Limite Prorata - criar funcao pra puxar do banco ex.: $this->preferencias->getLimiteProrata();
						$this->_view->atribui("limite_prorata", 20);
						
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
						//print_r($_REQUEST);
						//echo "</pre>";
						/*
						while(list($vr,$vl)=each(@$_REQUEST)) {

							$this->_view->atribui($vr,$vl);
						}
						*/
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

							$dados_produto = $produtos->obtemPlanoPeloId($_REQUEST["id_produto"]);

							//echo "produto<pre>";
							// print_r($formaPagamento);
							//print_r($dados_produto);
							//echo "</pre>";
							
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
			              
							/*
							echo "<pre>";
							print_r($da_dados);
							print_r($bl_dados);
							print_r($pro_dados);
							print_r($dados_produto);
							echo "</pre>";
							*/
              
							switch ($_REQUEST["tipo"]) {

							case "BL":
								$ip = @$_REQUEST["endereco_redeip"] ? $_REQUEST["endereco_redeip"] : "";
								$dados_conta = array( "id_nas"=>$_REQUEST["id_nas"], "id_pop"=>$_REQUEST["id_pop"], "endereco"=>$ip, "mac"=>$_REQUEST["mac"] );
								break;
							case "D":
								$dados_conta = array( "foneinfo"=>$_REQUEST["foneinfo"] );
								break;
							case "H";
								$dados_conta = array( "tipo_hospedagem"=>$_REQUEST["tipo_hospedagem"], "dominio"=>$_REQUEST["dominio"] );
								break;
							}
              
							$cria_e = @$_REQUEST["criar_email"] ? 1 : 0;
							
							/*cadastraContaEmail($username,$dominio,$senha,$id_cliente,$id_cliente_produto,$status,
											$observacoes,$conta_meste,
											$quota,$redirecionar_para="")*/
							

							$gera_carne = false;
							
							$cobranca->novoContrato($_REQUEST["id_cliente"], $_REQUEST["id_produto"], $dominio, $_REQUEST["data_contratacao"], $_REQUEST["vigencia"], $_REQUEST["pagamento"],
													$data_renovacao, $valor_contrato, $_REQUEST["username"], $_REQUEST["senha"], $id_cobranca, $status, $_REQUEST["tx_instalacao"], $_REQUEST["valor_comodato"],
													$_REQUEST["desconto_promo"], $_REQUEST["desconto_periodo"], $_REQUEST["dia_vencimento"], $_REQUEST["primeiro_vencimento"], $_REQUEST["prorata"], $_REQUEST["limite_prorata"], $_REQUEST["carencia"],
													$_REQUEST["id_prduto"], $_REQUEST["id_forma_pagamento"], $pro_dados, $da_dados, $bl_dados, $cria_e, $dados_produto, $endereco_cobranca, $endereco_instalacao, $dados_conta, $gera_carne);
							
							$this->_view->atribui ("gera_carne", $gera_carne);
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
				
					$id_cliente_produto = $_REQUEST ['id_cliente_produto'];
					$data = $_REQUEST ['data'];
					
					$cobranca = VirtexModelo::factory("cobranca"); 
					$fatura = $cobranca->obtemFatura ($id_cliente_produto, $data);
					
					$d = explode ('-', $fatura ['data']);
					$data_vencimento = $d[2] . '/' . $d[1] . '/' . $d[0];
					
					$this->_view->atribui ("data_vencimento", $data_vencimento);
					$this->_view->atribui ("descricao", $fatura ['descricao']);
					break;
			
				default:
					// Resumo
					
		
					break;
			
			}
			
			
		}
		
		protected function executaConta() {
			$this->_view->atribuiVisualizacao("conta");
			$tipo = @$_REQUEST["tipo"];
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
				//die("<pre>".print_r($info,true)."</pre>");
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
			}
			
			if( !$tipo && $id_cliente_produto ) {
				$infoProduto = $cobranca->obtemContratoPeloId($id_cliente_produto);
				$tipo = $infoProduto["tipo_produto"];
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
					
					$alterar_endereco 	= (bool) (@$_REQUEST["altera_rede"] == "t");
					
					$senha = $senha == $confsenha ? $senha : "";
					
					$url = "admin-clientes.php?op=conta&tipo=BL&id_cliente=".$this->id_cliente;
					
					// todo: buscar esse dados
					//  $observacoes, $upload,$download
					
					
					if($id_conta) {  // alteração
						if("BL" == $tipo){
							//die("MODELO_Contas::alteraContaBandaLarga $id_conta,$senha, $status,$observacoes,$conta_mestre,$id_pop,$id_nas,$upload,$download,$mac,$endereco,$alterar_endereco<br />");
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
						} else {							
							die("tipo inválido!");
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
						}
						
						$this->_view->atribui("url",$url);
						$this->_view->atribui("mensagem",$msg);
						$this->_view->atribuiVisualizacao("msgredirect");
					}
					
				} else {
					$this->_view->atribui("cidades_disponiveis",$this->clientes->listaCidades());
					$listaNAS = $equipamentos->obtemListaNAS();
					$this->_view->atribui("listaNAS",$listaNAS);					
					$listaPOP = $equipamentos->obtemListaPOPs('A');
					$this->_view->atribui("listaPOP",$listaPOP);
					$tiposNas = $equipamentos->obtemTiposNAS();					
					$this->_view->atribui("tiposNAS",$tiposNas);
					$bandas = $this->preferencias->obtemListaBandas();
					$this->_view->atribui("bandas",$bandas);
						
					if( $info["tipo_conta"] == "BL" ) {
					
						$endereco_instalacao = $contas->obtemEnderecoInstalacaoPelaConta($id_conta);
						 if(!count($endereco_instalacao)){
							$endereco_instalacao = $this->clientes->obtemPeloId($this->id_cliente);
							$contas->cadastraEnderecoInstalacao($id_conta,$endereco_instalacao["endereco"],$endereco_instalacao["complemento"],$endereco_instalacao["bairro"],
																$endereco_instalacao["id_cidade"], $endereco_instalacao["cep"], $this->id_cliente);
						}
						
						$nas = $equipamentos->obtemNAS($info["id_nas"]);
						$endereco_ip = $nas["tipo_nas"] == "I" ? $info["rede"] : $info["ippaddr"];
						$this->_view->atribui("endereco_ip",$endereco_ip);
						
						
					} else {
						$qtde = $contas->obtemQtdeContasPorContrato($id_cliente_produto, $tipo);
						$contrato = $cobranca->obtemContratoPeloId($id_cliente_produto);
						
						if( "BL" == $tipo){
							$qtdeDisponivel = $contrato["numero_contas"] - $qtde["num_contas"];							
							if($qtdeDisponivel <= 0 ){
								die("não existe mais contas disponiveis!");
							}
						} elseif("E" == $tipo){
							if( $contrato["num_emails"] > 0 ){			
								$qtdeDisponivel = $contrato["num_emails"] - $qtde["num_contas"];							
								if($qtdeDisponivel <= 0 ){
									die("não existe mais contas disponiveis!");
								}
							}
														
							$permite = $contrato["permitir_outros_dominios"] == 't' ? true : false;
							$this->_view->atribui("permite",$permite);
							
							if($permite){
								$listaDominios = $this->preferencias->obtemListaDominios($this->id_cliente);
								
							} else {
								$listaDominios = $preferenciasGerais["dominio_padrao"];
							}
							
							$this->_view->atribui("listaDominios",$listaDominios);
						}
						
						
					} 
					
					$this->_view->atribui("endereco",$endereco_instalacao["endereco"]);
					$this->_view->atribui("bairro",$endereco_instalacao["bairro"]);
					$this->_view->atribui("id_cidade",$endereco_instalacao["id_cidade"]);
					$this->_view->atribui("complemento",$endereco_instalacao["complemento"]);
					$this->_view->atribui("cep",$endereco_instalacao["cep"]);
					
					
				}
			} else {
				// Listagem
				
				$listaContratos = $cobranca->obtemContratos($this->id_cliente,"A",$tipo);

				for($i=0;$i<count($listaContratos);$i++) {
					$listaContas = $contas->obtemContasPorContrato($listaContratos[$i]["id_cliente_produto"]);
					$countContas = count($listaContas);
					
					$contasContrato = array();
					foreach($listaContas as $rowConta){
						$contasContrato[] = $contas->obtemContaPeloId($rowConta["id_conta"]);
					}
					$listaContratos[$i]["contas"] = $contasContrato;
										
					$contrato = $cobranca->obtemContratoPeloId($listaContratos[$i]["id_cliente_produto"]);
					$listaContratos[$i]["qtdeDisponivel"] = $contrato["numero_contas"] - $countContas;
					
					
					print("<pre>".print_r($listemail,true)."</pre>");
					if( $contrato["num_emails"] == 0 ){
						$listaContratos[$i]["emailIlimitado"] = true;
						$listaContratos[$i]["qtdeDisponivel"] = -1;
					} else {
						$qtde = $contas->obtemQtdeContasPorContrato($listaContratos[$i]["id_cliente_produto"], "E");
						$listaContratos[$i]["emailIlimitado"] = false;
						$listaContratos[$i]["qtdeDisponivel"] = $contrato["num_emails"] - $qtde["num_contas"];
					}
					
					
					//die("<pre>".print_r($contrato,true)."</pre>");
					//permitir_outros_dominios
					
					unset($contasContrato);
					unset($listaContas);
				}
				//die("<pre>".print_r($listaContratos,true)."</pre>");
				$this->_view->atribui("listaContratos",$listaContratos);
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