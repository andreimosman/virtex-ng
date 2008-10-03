<?

	class VCAClientes extends VirtexControllerAdmin {

		protected $clientes;
		protected $id_cliente;
		protected $helpdesk;
		protected $itensMenu;


		public function __construct() {
			parent::__construct();
		}

		protected function init() {
			// Inicializações da SuperClasse
			parent::init();

			$this->_view = VirtexViewAdmin::factory("clientes");
			$this->clientes = VirtexModelo::factory("clientes");
			$this->eventos = VirtexModelo::factory("eventos");
			$this->helpdesk = VirtexModelo::factory("helpdesk");


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
				
				case 'helpdesk':
					$this->executaHelpdesk();
					break;
					
				case 'emails_cancelados':
					$this->executaEmailsCancelados();
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
			

			$cadastro = VirtexModelo::factory("cadastro");
			$condominios = $cadastro->obtemCondominio();
			$clientes_cpf=NULL;
			
			$this->_view->atribui("condominios", MJson::encode($condominios));

			if( $this->id_cliente ) {
				if( $this->extra_op ) {
					// Tela de ficha
					$this->requirePrivLeitura("_CLIENTES");	
				} else {
					// Tela de alteração
					$this->requirePrivGravacao("_CLIENTES");
				}
			} else {
				// Tela de cadastro
				$this->requirePrivGravacao("_CLIENTES");
			}

			if( $this->id_cliente && !$this->acao ) {
				// Tem o id do cliente e não tem ação, pegar do banco

				$dados = $this->clientes->obtemPeloId($this->id_cliente);
				
				
				$clientes_cpf = $this->clientes->obtemPelosDocumentos($dados["cpf_cnpj"]);
				
				for($i=0; $i<count($clientes_cpf); $i++) {
					if($clientes_cpf[$i]["id_cliente"] == $dados["id_cliente"]) {
						unset($clientes_cpf[$i]);
					}
				}
				
				$this->_view->atribui("clientes_cpf",$clientes_cpf);
				
				foreach( $dados as $vr => $vl ) {
					$this->_view->atribui($vr,$vl);
				}

			} else {
				foreach( @$_REQUEST as $vr => $vl ) {
					$this->_view->atribui($vr,$vl);
				}
			}

			if( !$this->extra_op && $this->_acao ) {
				$this->requirePrivGravacao("_CLIENTES");
				try {
					$dados = @$_REQUEST;
					if(isset($dados["id_condominio"]) && !$dados["id_condominio"]) unset($dados["id_condominio"]);
					if(isset($dados["id_bloco"]) && !$dados["id_bloco"]) unset($dados["id_bloco"]);
					
					if($dados["nascimento"]) {
						$datatemp = explode("/", $dados["nascimento"]);
						$dados["nascimento"] = "$datatemp[2]-$datatemp[1]-$datatemp[0]";
					} else {
						$dados["nascimento"] = null;
					}
					
					if( $this->id_cliente ) {
						$this->clientes->altera($this->id_cliente, $dados);
						$mensagem = "Cliente alterado com sucesso.";
						$id_cliente = $this->id_cliente;
					} else {
						$id_cliente = $this->clientes->cadastra($dados);
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
			$this->requirePrivLeitura("_CLIENTES");

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
					$registros = $this->clientes->obtemUltimos(20);
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
			$cliente = $info;
			$this->_view->atribui("cliente",$info);
			

			$this->_view->atribui("nome_razao",$info["nome_razao"]);
			$this->_view->atribui("endereco",$info["endereco"]);
			$this->_view->atribui("bairro",$info["bairro"]);
			$this->_view->atribui("id_cidade",$info["id_cidade"]);
			$this->_view->atribui("complemento",$info["complemento"]);
			$this->_view->atribui("cep",$info["cep"]);
			$this->_view->atribui("id_condominio",$info["id_condominio"]);
			$this->_view->atribui("id_bloco",$info["id_bloco"]);
			$this->_view->atribui("apto",$info["apto"]);

			$tela = @$_REQUEST["tela"];
			$this->_view->atribui("tela",$tela);

			$acao = @$_REQUEST["acao"];
			$this->_view->atribui("acao",$acao);

			switch($tela) {
				case 'imprime_carne':
					$this->requirePrivLeitura("_CLIENTES_FATURAS");
					
					$id_carne = @$_REQUEST["id_carne"];
					$id_cliente = @$_REQUEST["id_cliente"];
					$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
					$id_cobranca = @$_REQUEST["id_cobranca"];

					$cobranca = VirtexModelo::factory('cobranca');
					
					$endereco_cobranca = $cobranca->obtemEnderecoCobranca($id_cliente_produto);
					
					if( empty($endereco_cobranca) ) {
						$clientes = VirtexModelo::factory('clientes');
						$cliente = $clientes->obtemPeloId($id_cliente);
						$endereco_cobranca["endereco"] = $cliente["endereco"];
						$endereco_cobranca["complemento"] = $cliente["complemento"];
						$endereco_cobranca["bairro"] = $cliente["bairro"];
						$endereco_cobranca["cep"] = $cliente["cep"];
						$endereco_cobranca["id_cidade"] = $cliente["id_cidade"];
						
						
					}
					
					
					$this->_view->atribui("endereco_cobranca",$endereco_cobranca);
					$cidade_cobranca = array();
					if( @$endereco_cobranca["id_cidade"] ) {
						$cidade_cobranca = $this->preferencias->obtemCidadePeloId($endereco_cobranca["id_cidade"]);
					}
					$this->_view->atribui("cidade_cobranca",$cidade_cobranca);
										
					/**
					 * CODIGO DE VERIFICACAO DO CARNE
					 */
					
					if( $id_carne && !$id_cobranca ) {
						// Se não for impressao individual de carnê gera o código. (em hexa).
						$codigoImpressao = $cobranca->obtemCodigoImpressaoCarne($id_carne);
						$this->_view->atribui("codigoImpressao",$codigoImpressao);
					}

					$this->_view->atribui("id_carne",$id_carne);
					$this->_view->atribui("id_cliente_produto",$id_cliente_produto);
					
					
					if( $id_cobranca ) {
						$faturas = array( $cobranca->obtemFaturaPorIdCobranca($id_cobranca) );
					} else {
						$faturas = $cobranca->obtemFaturasPorCarne($id_carne);
					}

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
					$this->requirePrivGravacao("_CLIENTES_CONTRATOS_CANCELAMENTO");

				case 'contrato':
				case 'imprime':
					$this->requirePrivLeitura("_CLIENTES_CONTRATOS");

					// TELA DE DETALHES DO CONTRATO
					$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
					$this->_view->atribui("id_cliente_produto",$id_cliente_produto);

					//$cliente = $this->clientes->obtemPeloId($this->id_cliente);
					//

					//echo "<pre>"; 
					//print_r($cliente);


					if( $cliente["id_cidade"] ) {
						$cidade = $this->preferencias->obtemCidadePeloId($cliente["id_cidade"]);
						$cliente["cidade"] = $cidade["cidade"];
						$cliente["uf"] = $cidade["uf"];
						
						// print_r($cidade);
					}

					//echo "</pre>";
					
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
					
					$contrato["valor_extenso"] = MFormata::valorExtenso($contrato["valor_produto"]);
					$contrato["data_contratacao_extenso"] = MFormata::escreveData($contrato["data_contratacao"]);
					
					//echo "<br><br><br><br><br>";
					//echo MFormata::valorExtenso(12341.412);
					// 
					

					if(!$contrato["nome_produto"]) {
						$produtos = VirtexModelo::factory("produtos");
					
						$produto = $produtos->obtemPlanoPeloId($contrato["id_produto"]);
						
						$contrato["nome_produto"] = $produto["nome"];
						$contrato["descricao_produto"] = $produto["descricao"];
					
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
					
					
					if( $tela == "imprime" ) {
					
						// 
						
						//echo "IMP!!!!";
						
						// echo "IDM: " . $contrato["id_modelo"];
						
						
						if( $contrato["id_modelo_contrato"] ) {
							$tpl = new MTemplate("var/contrato");
							
							$equipamentos = VirtexModelo::factory("equipamentos");
							
							for($i=0;$i<count($listaContas);$i++) {
								if( @$listaContas[$i]["id_pop"]) {
									$listaContas[$i]["pop"] = $equipamentos->obtemPop($listaContas[$i]["id_pop"]);
								}
							}
							
							
							
							$tpl->atribui("cliente",$this->_view->obtem("cliente"));
							$tpl->atribui("listaContas",$listaContas);
							$tpl->atribui("formaPagamento",$formaPagamento);
							// $tpl->atribui("produto",$produto);
							$tpl->atribui("contrato",$contrato);
							$tpl->atribui("listaContas",$listaContas);
							
							
							
							$prefGerais = $this->preferencias->obtemPreferenciasGerais();
							$prefProvedor = $this->preferencias->obtemPreferenciasProvedor();
							$tpl->atribui("prefProvedor",$prefProvedor);
							$tpl->atribui("prefGerais",$prefGerais);
							
							$nomeArquivo = sprintf("%05d",$contrato["id_modelo_contrato"]);
							
							$this->_view->atribui("texto_contrato", $tpl->obtemPagina($nomeArquivo));
							
							
							// echo "Na: $nomeArquivo";
							
						
						}
						

					}

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
					
					
					$cadastro = VirtexModelo::factory("cadastro");				
					$condominios = $cadastro->obtemCondominio();
					$condominios_instalacao = $cadastro->obtemCondominio(NULL, true);
					$this->_view->atribui("condominios", MJson::encode($condominios));					
					$this->_view->atribui("condominios_instalacao", MJson::encode($condominios_instalacao));					

					$this->requirePrivGravacao("_CLIENTES_CONTRATOS");

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

					$parcelas_instalacao = @$_REQUEST["parcelas_instalacao"];
					if( !$parcelas_instalacao ) {
						$parcelas_instalacao = 1;
					}
					$this->_view->atribui("parcelas_instalacao",$parcelas_instalacao);

					$pro_rata = @$_REQUEST["pro_rata"];
					if( !$pro_rata ) $pro_rata = 'f';
					$this->_view->atribui("pro_rata",$pro_rata);

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
						$listaPOP = $equipamentos->obtemListaPOPOrdemAlfabetica();
						$this->_view->atribui("listaPOP",$listaPOP);

						// Valores Padrão
						$data_contratacao = date("d/m/Y");

						$dia_venc 	= $tela == "migrar" ? $contrato["vencimento"] : $preferenciasCobranca["dia_venc"];
						
						if( !$dia_venc ) {
							$dia_venc = $cobranca->obtemDiaVencimento($contrato["id_cliente_produto"]);
							if( !$dia_venc ) {
								$dia_venc = $prefCobra["dia_venc"];
							}
						}
						
						
						
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
						
						$faturas = $cobranca->gerarListaFaturas(@$_REQUEST["pagamento"],@$_REQUEST["data_contratacao"],@$_REQUEST["vigencia"],@$_REQUEST["dia_vencimento"],$valor,@$_REQUEST["desconto_promo"],@$_REQUEST["periodo_desconto"],@$_REQUEST["tx_instalacao"],@$_REQUEST["valor_comodato"],@$_REQUEST["primeiro_vencimento"],$pro_rata,@$_REQUEST["limite_prorata"],$parcelas_instalacao,$id_cliente_produto);
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
						

						if(@$_REQUEST["forma_pagamento"] == "NA") { 
							$id_forma_pagamento = "9999";
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
								$data_renovacao = MData::adicionaMes($_REQUEST["data_contratacao"], $_REQUEST["vigencia"]);
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
								
								$difEnderecoMail = @$_REQUEST["difEnderecoMail"];
								$difEnderecoSetup = @$_REQUEST["difEnderecoSetup"];
								
								$endereco_cobranca = array( "endereco" => $_REQUEST["endereco_cobranca"], "id_cidade" => $_REQUEST["id_cidade_cobranca"], "cep" => $_REQUEST["cep_cobranca"], "bairro" => $_REQUEST["bairro_cobranca"], "complemento" => $_REQUEST["complemento_cobranca"], "id_condominio_cobranca" => @$_REQUEST["id_condominio_cobranca"], "id_bloco_cobranca" => $_REQUEST["id_bloco_cobranca"], "apto_cobranca" => $_REQUEST["apto_cobranca"] );
								$endereco_instalacao = array( "endereco" => $_REQUEST["endereco_instalacao"], "id_cidade" => $_REQUEST["id_cidade_instalacao"], "cep" => $_REQUEST["cep_instalacao"], "bairro" => $_REQUEST["bairro_instalacao"], "complemento" => $_REQUEST["complemento_instalacao"], "id_condominio_instalacao" => $_REQUEST["id_condominio_instalacao"], "id_bloco_instalacao" => $_REQUEST["id_bloco_instalacao"], "apto_instalacao" => $_REQUEST["apto_instalacao"] );
								
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

								$id_modelo_contrato = @$info_produto["modelo_contrato"];
								$tipo_produto = @$_REQUEST["tipo"];
								
								if(!$id_modelo_contrato) {
									$info_modelo = $this->preferencias->obtemModeloContratoPadrao($tipo_produto);
									$id_modelo_contrato = $info_modelo["id_modelo_contrato"];
								}
								
								$novo_id_cliente_produto = $cobranca->novoContrato(	$_REQUEST["id_cliente"], $_REQUEST["id_produto"], $dominio, $id_modelo_contrato, $_REQUEST["data_contratacao"], $_REQUEST["vigencia"], $_REQUEST["pagamento"],
																					$data_renovacao, $valor_contrato, $_REQUEST["username"], $_REQUEST["senha"], $id_cobranca, $status, $_REQUEST["tx_instalacao"], $_REQUEST["valor_comodato"],
																					$_REQUEST["desconto_promo"], $_REQUEST["periodo_desconto"], $dia_vencimento, $_REQUEST["primeiro_vencimento"], $_REQUEST["pro_rata"], $_REQUEST["limite_prorata"], $carencia,
																					$_REQUEST["id_produto"], $id_forma_pagamento, $pro_dados, $da_dados, $bl_dados, $cria_e, $dados_produto, $endereco_cobranca, $endereco_instalacao, $dados_conta, $gera_carne, $parcelas_instalacao);
								

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
								
								
								//Calcula o valor da comissao
								$produto = $produtos->obtemPlanoPeloId($_REQUEST["id_produto"]);
								$valor_comissao = 0;
								$valor_produto = $produto["valor"];	
								$perc_comissao = 0;
							
								if($tela == "migrar") {
									$perc_comissao = $produto["comissao_migracao"];
								} else {
									$perc_comissao = $produto["comissao"];
								}

								$dadosLogin = $this->_login->obtem("dados");
								$valor_comissao = ($perc_comissao * $valor_produto) / 100;

								//Faz a gravação na tabela de comissoes
								if($valor_comissao) {
									$cobranca->gravaComissao($novo_id_cliente_produto, $dadosLogin["id_admin"], $valor_comissao);
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
					$this->requirePrivLeitura("_CLIENTES_CONTRATOS");
					$cobranca = VirtexModelo::factory("cobranca");
					$contratos = $cobranca->obtemContratos ($_REQUEST ["id_cliente"]);
					$this->_view->atribui ('contratos', $contratos);
					break;
				
				case "estorno":


					$this->requirePrivGravacao("_CLIENTES_FATURAS_ESTORNO");

				
					$id_cliente = @$_REQUEST["id_cliente"];
				
					$id_cobranca = @$_REQUEST["id_cobranca"];					
					$this->_view->atribui("id_cobranca",$id_cobranca);
					
					$cobranca = VirtexModelo::factory("cobranca");
					$fatura = $cobranca->obtemFaturaPorIdCobranca($id_cobranca);
					$this->_view->atribui("fatura",$fatura);

					$this->_view->atribui("status_fatura",$cobranca->obtemStatusFatura());


					$prefCobra = $this->preferencias->obtemPreferenciasCobranca();
					
					$formaPagamento = array();
					if( $fatura["id_forma_pagamento"] ) {
						$formaPagamento = $this->preferencias->obtemFormaPagamento($fatura["id_forma_pagamento"]);
					}
					
					$this->_view->atribui("formaPagamento",$formaPagamento);
					$this->_view->atribui("bancos",$this->preferencias->obtemListaBancos());
					$this->_view->atribui("tiposFormaPgto",$this->preferencias->obtemTiposFormaPagamento());
					
					$acao = @$_REQUEST["acao"];
					
					
					if( $acao ) {

						$senha_admin = @$_REQUEST["senha_admin"];

						$dadosLogin = $this->_login->obtem("dados");
						
						try {

							if( !$senha_admin ) {
								$erro = "Operação não autorizada: SENHA NÃO FORNECIDA.";
							} elseif (md5(trim($senha_admin)) != $dadosLogin["senha"] ) {
								$erro = "Operação não autorizada: SENHA NÃO CONFERE.";
							}

							if($erro) throw new Exception($erro);
							
							$this->eventos->registraEstornoFatura($this->ipaddr,$dadosLogin["id_admin"],$id_cobranca,$fatura["id_cliente_produto"],$fatura["valor"],$fatura["acrescimo"],$fatura["desconto"],$fatura["valor_pago"],$fatura["data"], $fatura["data_pagamento"], $fatura["reagendamento"]);
							$cobranca->estornaPagamentoFatura($id_cobranca,true);

							$url = "admin-clientes.php?op=contrato&tela=faturas&id_cliente=$id_cliente";

							$this->_view->atribui("url",$url);
							$this->_view->atribui("mensagem","Pagamento da fatura estornado com sucesso!");
							$this->_view->atribuiVisualizacao("msgredirect");

						
						} catch (ExcecaoModeloValidacao $e ) {
							$this->_view->atribui ("msg_erro", $e->getMessage());
							$erro = true;
						} catch (Exception $e ) {
							$this->_view->atribui ("msg_erro", $e->getMessage());
							$erro = true;
						}
						
						
						

					

					}
					
					
					
					
					
					break;

				case "faturas":
				
					//echo "XPTO";

					$this->requirePrivLeitura("_CLIENTES_FATURAS");

					$podeEstornar = $this->requirePrivGravacao("_CLIENTES_FATURAS_ESTORNO", false);
					$this->_view->atribui("podeEstornar",$podeEstornar);
					

					$tem_carne = "";
					$cobranca = VirtexModelo::factory("cobranca");
					$faturas = $cobranca->obtemFaturas ($_REQUEST ['id_cliente'], $tem_carne, $_REQUEST ['id_cliente_produto'],
								 $_REQUEST ['id_forma_pagamento'], $_REQUEST ['id_carne']);
								 
					//echo "<pre>";
					//print_r($faturas);
					//echo "</pre>";
							

					$this->_view->atribui ('id_cliente_produto', @$_REQUEST['id_cliente_produto']);
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

					if( !($this->requirePrivLeitura("_CLIENTES_FATURAS",false) || $this->requirePrivGravacao("_CLIENTES_FATURAS",false) || $this->requirePrivGravacao("_FINANCEIRO_COBRANCA_AMORTIZACAO",false)) ) {
						$this->acessoNegado();
					}

					if( $this->requirePrivGravacao("_CLIENTES_FATURAS_DESCONTO",false)) {
						$this->_view->atribui("podeConcederDesconto", true);
					}
					
					$podeReagendar = $this->requirePrivGravacao("_CLIENTES_FATURAS_REAGENDAMENTO",false);
					$this->_view->atribui("podeReagendar", $podeReagendar);

					$id_cliente_produto = @$_REQUEST ['id_cliente_produto'];
					$id_cliente = @$_REQUEST ['id_cliente'];
					$id_cobranca = @$_REQUEST ['id_cobranca'];
					$data = @$_REQUEST ['data'];
					$acao = @$_REQUEST ['acao'];
					$cobranca = VirtexModelo::factory("cobranca");
					$erro = false;


					$dadosLogin = $this->_login->obtem("dados");
					
					//echo "<pre>";
					//print_r($dadosLogin);
					//echo "</pre>";

					if($acao && ($this->requirePrivGravacao("_CLIENTES_FATURAS",false) || $this->requirePrivGravacao("_FINANCEIRO_COBRANCA_AMORTIZACAO",false) || $this->requirePrivGravacao("_CLIENTES_FATURAS_REAGENDAMENTO",false))) {
						$desconto		= @$_REQUEST["desconto"];
						$acrescimo		= @$_REQUEST["acrescimo"];
						$amortizar		= @$_REQUEST["amortizar"];
						$data_pagamento	= @$_REQUEST["data_pagamento"];


						$reagendar		= @$_REQUEST["reagendar"];
						$reagendamento	= @$_REQUEST["reagendamento"];
						$observacoes	= @$_REQUEST["observacoes"];

						$url = "admin-clientes.php?op=contrato&tela=faturas&id_cliente=$id_cliente";

						try {


							$senha_admin = @$_REQUEST["senha_admin"];

							if( !$senha_admin ) {
								$erro = "Operação não autorizada: SENHA NÃO FORNECIDA.";
							} elseif (md5(trim($senha_admin)) != $dadosLogin["senha"] ) {
								$erro = "Operação não autorizada: SENHA NÃO CONFERE.";
							}
							if($erro) throw new Exception($erro);

							$contas = VirtexModelo::factory("contas");

							if( $cobranca->amortizarFatura($id_cobranca, $desconto, $acrescimo, $amortizar, $data_pagamento, $reagendar,
									$reagendamento, $observacoes,$dadosLogin) ) { 
									//$fluxo=VirtexPersiste::factory("cxtb_fluxo");
									//$fluxo->pagamentoComDinheiro($amortizar,$data_pagamento,$id_cobranca,$dadosLogin["admin"]);

									$fatura = $cobranca->obtemFaturaPorIdCobranca($id_cobranca);

									$this->eventos->registraPagamentoFatura($this->ipaddr,$dadosLogin["id_admin"],$id_cobranca,$fatura["valor"],$acrescimo,$desconto,$amortizar,$reagendar,$fatura["id_cliente_produto"], $conta[0]["id_conta"]);

									$this->_view->atribui("url",$url);
									$this->_view->atribui("mensagem","Dados atualizados com sucesso!");
									$this->_view->atribuiVisualizacao("msgredirect");
									if( $acao ) {
										$erro = "";
									} else {
										VirtexView::simpleRedirect($url);
									}
							}
						} catch (ExcecaoModeloValidacao $e ) {
							$this->_view->atribui ("msg_erro", $e->getMessage());
							$erro = true;
						} catch (Exception $e ) {
							$this->_view->atribui ("msg_erro", $e->getMessage());
							$erro = true;
						}
					}


					$fatura = $cobranca->obtemFaturaPorIdCobranca ($id_cobranca);
					
					$ret = array();
					
					$prefCobra = $this->preferencias->obtemPreferenciasCobranca();
					
					$formaPagamento = array();
					if( $fatura["id_forma_pagamento"] ) {
						$formaPagamento = $this->preferencias->obtemFormaPagamento($fatura["id_forma_pagamento"]);
					}
					
					$this->_view->atribui("formaPagamento",$formaPagamento);
					$this->_view->atribui("bancos",$this->preferencias->obtemListaBancos());
					$this->_view->atribui("tiposFormaPgto",$this->preferencias->obtemTiposFormaPagamento());
					
					
					if( $fatura["id_retorno"] ) {
						$ret = $cobranca->obtemRetornoPeloId($fatura["id_retorno"]);
					}
					
					$this->_view->atribui("retorno",$ret);
					
					
					foreach($fatura as $k => $v){
						if("data"  == $k ) {
							$v = strftime("%d/%m/%Y",strtotime($v));
						}
						$this->_view->atribui ($k, $v);
					}

					$valor_restante = (float) ( (float) $fatura["valor"] - (float) $fatura["desconto"] - (float) $fatura["pagto_parcial"] + (float) $fatura["acrescimo"] );
					$valor_restante = number_format($valor_restante,2);
					$this->_view->atribui ("valor_restante", $valor_restante);


					if(	!$this->requirePrivGravacao("_CLIENTES_FATURAS",false) ) {
						$this->_view->atribui ("editavel", false);
					} else {
						$this->_view->atribui ("editavel", true);
					}
					
					
					if( $fatura["status"] == PERSISTE_CBTB_FATURAS::$CANCELADA ||
						$fatura["status"] == PERSISTE_CBTB_FATURAS::$ESTORNADA ||
						$fatura["status"] == PERSISTE_CBTB_FATURAS::$PAGA ) {
						$this->_view->atribui ("editavel", false);
						$this->_view->atribui ("podeReagendar", false);
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
			$id_conta 	= @$_REQUEST["id_conta"];
			$contas = VirtexModelo::factory("contas");
			
			if( $id_conta ) {
				$infoConta = $contas->obtemContaPeloId($id_conta); 
			} else {
				$infoConta = array();
			}

			if( !$tipo && $id_conta ) {
				$tipo = @$infoConta["tipo_conta"];
			}
			
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			if( !$id_cliente_produto && @$infoConta["id_cliente_produto"] ) {
				$id_cliente_produto = $infoConta["id_cliente_produto"];
			}


			$this->_view->atribui("tipo",$tipo);

			$privReq = "";

			switch($tipo) {
				case 'BL':
					$privReq = "_CLIENTES_BANDALARGA";
					break;
				case'D':
					$privReq = "_CLIENTES_DISCADO";
					break;
				case 'E':
					$privReq = "_CLIENTES_EMAIL";
					break;
				case 'H':
					$privReq = "_CLIENTES_HOSPEDAGEM";
					break;
			}

			$this->requirePrivLeitura($privReq);

			$this->_view->atribui("id_cliente",$this->id_cliente);


			$info = $this->clientes->obtemPeloId($this->id_cliente);

			$this->_view->atribui("nome_razao",$info["nome_razao"]);

			$cobranca = VirtexModelo::factory("cobranca");


			$tela 		= @$_REQUEST["tela"];

			if( $tela == "cadastro" ) {
				// Precisa de Privilégio de Gravação

				$this->requirePrivGravacao($privReq);

			}



			$acao 		= @$_REQUEST["acao"];

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
					
					// echo "POP: $pop<br>\n";
					
					
					$arvorePop = array();
					if( @$pop["id_pop"] ) {
						$arvorePop = $equipamentos->obtemArvorePop($pop["id_pop"]);
					}

					//echo "<pre>"; 
					//print_r($arvorePop);
					//echo "</pre>";
					
					$this->_view->atribui("arvorePop",$arvorePop);

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

				$infoProduto = $cobranca->obtemContratoPeloId($info["id_cliente_produto"]);
				$tipo = trim($infoProduto["tipo_produto"]);
				$this->_view->atribui("tipo",$tipo);
				$this->_view->atribui("infoProduto",$infoProduto);
				
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
					$data_instalacao 	= @$_REQUEST["data_instalacao"];
					$data_ativacao 		= "";
					$conta_ativada 		= @$_REQUEST["conta_ativada"];

					$tipo_hospedagem	= @$_REQUEST["tipo_hospedagem"];
					$dominio_hospedagem = @$_REQUEST["dominio_hospedagem"];

					$alterar_endereco 	= (bool) (@$_REQUEST["altera_rede"] == "t");

					$senha = $senha == $confsenha ? $senha : "";

					$selecao_redeip		= @$_REQUEST["selecao_redeip"];

					// if(!$status) $status = "N";
					if(!$status) $status = "A";
					else if(!$data_instalacao && $status != "I" && !$conta_ativada) $data_ativacao = date("Y-m-d");

					$contrato = $cobranca->obtemContratoPeloId($id_cliente_produto);
					$url = "admin-clientes.php?op=conta&tipo=".@$contrato["tipo_produto"]."&id_cliente=".$this->id_cliente;

					// todo: buscar esse dados
					//  $observacoes, $upload,$download

					if($id_conta) {  // alteração
						$contaAtual = $contas->obtemContaPeloId($id_conta);

						if("BL" == $tipo){
							//die;

							$contas->alteraContaBandaLarga($id_conta,$senha, $status,$observacoes,$conta_mestre,
									$id_pop,$id_nas,$upload,$download,$mac,$endereco_redeip, $alterar_endereco);

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
															"cep" => @$_REQUEST["cep_instalacao"],
															"id_condominio_instalacao" => @$_REQUEST["id_condominio_instalacao"], 
															"id_bloco_instalacao" => @$_REQUEST["id_bloco_instalacao"],
															"apto_instalacao" => @$_REQUEST["apto_instalacao"]
														);

								if($info_endereco != $endereco_instalacao){  
								
								
									$contas->cadastraEnderecoInstalacao($id_conta,$endereco_instalacao["endereco"],$endereco_instalacao["complemento"],$endereco_instalacao["bairro"],
																		$endereco_instalacao["id_cidade"], $endereco_instalacao["cep"], $endereco_instalacao["id_condominio_instalacao"], $endereco_instalacao["id_bloco_instalacao"], $endereco_instalacao["apto_instalacao"],$this->id_cliente);
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
							// $contas->alteraConta($id_conta,$senha,$status,$observacoes,$conta_mestre);
							$msg = "Conta alterada com sucesso.";
						} else {
							// die("tipo inválido!");
						}

						$contaNova = $contas->obtemContaPeloId($id_conta);
						$dadosLogin = $this->_login->obtem("dados");

						// Registra no sistema.
						$endAtual = @$contaAtual["ipaddr"] ? @$contaAtual["ipaddr"] : @$contaAtual["rede"];
						$endNovo  = @$contaNova["ipaddr"] ? @$contaNova["ipaddr"] : @$contaNova["rede"];

						$this->eventos->registraAlteracaoConta($this->ipaddr,$dadosLogin["id_admin"],$contaAtual["id_conta"],$contaAtual["id_cliente_produto"],
																@$contaAtual["status"], @$contaNova["status"],
																$endAtual,$endNovo,
																@$contaAtual["id_pop"], @$contaNova["id_pop"],
																@$contaAtual["id_nas"], @$contaNova["id_nas"],
																@$contaAtual["mac"], @$contaNova["mac"],
																@$contaAtual["upload_kbps"], @$contaNova["upload_kbps"],
																@$contaAtual["download_kbps"], @$contaNova["download_kbps"]
																);
																
						if($data_ativacao) {
							$contas->alteraDataAtivacao($id_conta, $data_ativacao);
						}
						
						if($data_instalacao) {
							$data = explode("/", $data_instalacao);
							$data_instalacao = "$data[2]-$data[1]-$data[0]"; 
							$contas->alteraDataInstalacao($id_conta, $data_instalacao);
							$contas->alteraConta($id_conta,$senha,"I");
						}

						$this->_view->atribui("url",$url);
						$this->_view->atribui("mensagem",$msg);
						$this->_view->atribuiVisualizacao("msgredirect");


					} else {  //  cadastro

						$conta_existente = $contas->obtemContaPeloUsername($username,$dominio,$tipo);
						
						if($conta_existente) {
							$msg = "Esta conta já foi cadastrada previamente e não poderá ser cadastrada outra vez.";
						} else {

							if("BL" == $tipo){

								$contas->cadastraContaBandaLarga($username,$dominio,$senha,$id_cliente,$id_cliente_produto,$status,
																 $observacoes,$conta_mestre,$id_pop,$id_nas,$upload,$download,$mac,$endereco_redeip);

								if($difEnderecoSetup){
									$endereco_instalacao = array(
																"endereco" => @$_REQUEST["endereco_instalacao"],
																"bairro" => @$_REQUEST["bairro_instalacao"],
																"id_cidade" => @$_REQUEST["id_cidade_instalacao"],
																"complemento" => @$_REQUEST["complemento_instalacao"],
																"cep" => @$_REQUEST["cep_instalacao"],
																"id_condominio_instalacao" => @$_REQUEST["id_condominio_instalacao"], 
																"id_bloco_instalacao" => @$_REQUEST["id_bloco_instalacao"],
																"apto_instalacao" => @$_REQUEST["apto_instalacao"]
															);
								} else { 
									$info_endereco = $contas->obtemEnderecoInstalacaoPelaConta($id_conta);
								}



								$contas->cadastraEnderecoInstalacao($id_conta,$endereco_instalacao["endereco"],$endereco_instalacao["complemento"],$endereco_instalacao["bairro"],
																	$endereco_instalacao["id_cidade"], $endereco_instalacao["cep"], $endereco_instalacao["id_condominio_instalacao"], $endereco_instalacao["id_bloco_instalacao"], $endereco_instalacao["apto_instalacao"] ,$this->id_cliente);
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
					$listaPOP = $equipamentos->obtemListaPOPOrdemAlfabetica();
					
					$this->_view->atribui("listaPOP",$listaPOP);
					$tiposNas = $equipamentos->obtemTiposNAS();
					$this->_view->atribui("tiposNAS",$tiposNas);
					$bandas = $this->preferencias->obtemListaBandas();
					$this->_view->atribui("bandas",$bandas);
					
					$cadastro = VirtexModelo::factory("cadastro");
					$condominios = $cadastro->obtemCondominio();
					$this->_view->atribui("condominios", MJson::encode($condominios));						

					if( trim($info["tipo_conta"]) == "BL" || trim($info["tipo_conta"]) == "D") {

						$endereco_instalacao = $contas->obtemEnderecoInstalacaoPelaConta($id_conta);
						
						if(!$endereco_instalacao){
							$endereco_instalacao = $this->clientes->obtemPeloId($this->id_cliente);
							
							$contas->cadastraEnderecoInstalacao($id_conta,$endereco_instalacao["endereco"],$endereco_instalacao["complemento"],$endereco_instalacao["bairro"],
																$endereco_instalacao["id_cidade"], $endereco_instalacao["cep"], $endereco_instalacao["id_condominio"], $endereco_instalacao["id_bloco"], $endereco_instalacao["apto"],$this->id_cliente);
						}

						$nas = $equipamentos->obtemNAS($info["id_nas"]);
						$endereco_ip = $nas["tipo_nas"] == "I" ? $info["rede"] : $info["ippaddr"];
						$this->_view->atribui("endereco_ip",$endereco_ip);

						$this->_view->atribui("endereco",$endereco_instalacao["endereco"]);
						$this->_view->atribui("bairro",$endereco_instalacao["bairro"]);
						$this->_view->atribui("id_cidade",$endereco_instalacao["id_cidade"]);
						$this->_view->atribui("complemento",$endereco_instalacao["complemento"]);
						$this->_view->atribui("cep",$endereco_instalacao["cep"]);
						$this->_view->atribui("id_condominio_instalacao",$endereco_instalacao["id_condominio_instalacao"]);
						$this->_view->atribui("id_bloco_instalacao",$endereco_instalacao["id_bloco_instalacao"]);
						$this->_view->atribui("apto_instalacao",$endereco_instalacao["apto_instalacao"]);

					}

					$qtde = $contas->obtemQtdeContasPorContrato($id_cliente_produto, $tipo);
					$contrato = $cobranca->obtemContratoPeloId($id_cliente_produto);
					
					$produtos = VirtexModelo::factory('produtos');
					$produto = $produtos->obtemPlanoPeloId($contrato["id_produto"]);
					
					if( $tipo == "E" ) {
						if( !$id_conta && $contrato["num_emails"] > 0 ){
							$qtdeDisponivel = $contrato["num_emails"] - $qtde["num_contas"];
							if($qtdeDisponivel <= 0 ){
								// die("não existe mais contas disponiveis!");
								$mensagem = "Não é possível criar conta adicional. Este contrato excedeu o limite de e-mails adicionais.";
								$url = "admin-clientes.php?op=conta&tipo=" . trim($produto["tipo"]) . "&id_cliente=" . $this->id_cliente;

								$this->_view->atribui("mensagem",$mensagem);
								$this->_view->atribui("url",$url);
								$this->_view->atribuiVisualizacao("msgredirect");

								return;


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
							// die("não existe mais contas disponiveis!");
							$mensagem = "Não é possível criar conta adicional. Este contrato excedeu o limite de contas.";
							$url = "admin-clientes.php?op=conta&tipo=" . trim($produto["tipo"]) . "&id_cliente=" . $this->id_cliente;

							$this->_view->atribui("mensagem",$mensagem);
							$this->_view->atribui("url",$url);
							$this->_view->atribuiVisualizacao("msgredirect");

							return;

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

			$this->requirePrivLeitura("_CLIENTES_RELATORIOS");




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
						// echo "ID_CIDADE: $id_cidade<br>\n";
						
						//if( $id_cidade == ':NULL:' ) {
						//	$id_cidade = null;
						//}
						
						// echo "ID_CIDADE: $id_cidade<br>\n";
						
						$registros = $this->clientes->obtemClientesPorCidade(($id_cidade == ':NULL:' ? null : $id_cidade));

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
			// echo "EXECUTA ELIMINAR<br>\n";
		}
		

		protected function executaHelpdesk() {
			
			$this->_view->atribuiVisualizacao("helpdesk");
			
			$id_cliente = @$_REQUEST["id_cliente"];
			$id_chamado_pai = @$_REQUEST["id_chamado_pai"];
			$tela = @$_REQUEST["tela"];
			$subtela = @$_REQUEST["subtela"];
			$op = @$_REQUEST["op"];
			$acao = @$_REQUEST["acao"];
			
			$this->_view->atribui("tela", $tela);
			$this->_view->atribui("op", $op);
			
			$info_cliente = $this->clientes->obtemPeloId($id_cliente);
			
			$dadosLogin = $this->_login->obtem("dados");
			
			$prioridades = $this->helpdesk->obtemPrioridades();
			
			$this->_view->atribui("prioridades", $prioridades);
			$this->_view->atribui("subtela", $subtela);
			$this->_view->atribui("dadosLogin", $dadosLogin);
			$this->_view->atribui("id_cliente", $id_cliente);
			$this->_view->atribui("nome_razao", $info_cliente["nome_razao"]);
			
			$classes = $this->helpdesk->obtemListaClasses();
			$this->_view->atribui("classes",$classes);
			
			switch($tela) {
				case 'cadastro': 		//Cadastro de novos chamados
					if(!$acao) {
						$contas = VirtexModelo::factory("contas");
						
						$tipos = $this->helpdesk->obtemTiposChamado();
						$origens = $this->helpdesk->obtemOrigensChamado();
						$classificacoes = $this->helpdesk->obtemClassificacoesChamado();
						$status_chamado = $this->helpdesk->obtemStatusChamado();

						$grupos = $this->helpdesk->obtemListaGruposComPopulacao(true);
						$responsaveis = $this->helpdesk->obtemListaAdminGrupo();
						$contas_cliente = $contas->obtemContasPorCliente($id_cliente,"A"); // Somente contas ativas
						$prioridades = $this->helpdesk->obtemPrioridades();

						$this->_view->atribui("chamados_pendentes", $chamados_pendentes);
						$this->_view->atribui("criado_por", $dadosLogin["id_admin"]);
						$this->_view->atribui("acao", "gravar");
						$this->_view->atribui("contas_cliente", $contas_cliente);
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
						$id_cliente = @$_REQUEST["id_cliente"];
						$id_conta = @$_REQUEST["id_conta"];
						$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
						$prioridade = @$_REQUEST["prioridade"];
						
						$id_classe = @$_REQUEST["id_classe"];
						
						
						$id_cliente = $id_cliente ? $id_cliente : 0;
						$id_conta = $id_conta ? $id_conta : 0;
						$id_cliente_produto = $id_cliente_produto ? $id_cliente_produto : 0;
						$responsavel = $responsavel ? $responsavel : null;					
						
						if($id_conta) {
							$contas = VirtexModelo::factory("contas");
							$conta = $contas->obtemContaPeloId($id_conta);
							$id_cliente_produto = $conta["id_cliente_produto"];
						}

						$id_chamado = $this->helpdesk->abreChamado($tipo,$criado_por,$id_grupo,$assunto,$descricao,$origem,$classificacao,$prioridade,$responsavel,$id_classe,$id_cliente,$id_cliente_produto,$id_conta);
						$confirma_chamado = $this->helpdesk->obtemChamadoPeloId($id_chamado);
						
						$mensagem = "";
						$url = "";
						
						if($confirma_chamado) {
							$mensagem = "Chamado criado com sucesso.";
							$url = "admin-clientes.php?op=helpdesk&tela=alteracao&id_chamado=$id_chamado&id_cliente=$id_cliente";
						} else {
							$mensagem = "Erro ao criar o chamado.";
							$url = "admin-clientes.php?op=desktop&tela=listagem&id_cliente=$id_cliente";
						}
						
						$this->_view->atribui("mensagem",$mensagem);
						$this->_view->atribui("url",$url);
						$this->_view->atribuiVisualizacao("msgredirect");
					
					}
					break;
				
				
				case 'alteracao': 		//Alteração de chamados existentes
					$id_chamado = $_REQUEST["id_chamado"];
				
					if(!$acao) {
					
						$equipamentos = VirtexModelo::factory("equipamentos");
						$cadastro = VirtexModelo::factory("cadastro");
						//Seleciona o chamado desejado
						$chamado = $this->helpdesk->obtemChamadoPeloId($id_chamado);
						
						$info_cliente_chamado = array();

						//CLIENTE
						if($chamado["id_cliente"]) {
							$info_cliente_chamado = $this->clientes->obtemPeloId($chamado["id_cliente"]);
							
							
							$condominio = array();
							
							if( $info_cliente_chamado["id_condominio"] ) {
								$condominio = $cadastro->obtemCondominio($info_cliente_chamado["id_condominio"]);
							}
							
							$bloco = array();

							if( $info_cliente_chamado["id_bloco"] ) {
								$bloco = $cadastro->obtemCondominioBloco(null,$info_cliente_chamado["id_bloco"]);
							}
							
							$info_cliente_chamado["condominio"] = $condominio;
							$info_cliente_chamado["bloco"] = $bloco;
							
							$chamado["cliente_nome"] = @$info_cliente_chamado["nome_razao"];	
						}
						
						$this->_view->atribui("info_cliente_chamado",$info_cliente_chamado);
						

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
							
							
							//CONTA
							if ($chamado["id_conta"]) {
								$contas = VirtexModelo::factory("contas");
								$cobranca = VirtexModelo::factory("cobranca");
								
								
								
								$info_conta_chamado = $contas->obtemContaPeloId($chamado["id_conta"]);
								$pop_nome = null;
								$nas_nome = null;
								if(@$info_conta_chamado["id_pop"]) {
									$tmp_pop = $equipamentos->obtemPop($info_conta_chamado["id_pop"]);
									$pop_nome = $tmp_pop["nome"];
								}
								
								if(@$info_conta_chamado["id_nas"]) {
									$tmp_nas = $equipamentos->obtemNAS($info_conta_chamado["id_nas"]);
									$nas_nome = $tmp_nas["nome"];
								}
								
								$info_contrato_chamado = $cobranca->obtemContratoPeloId($info_conta_chamado["id_cliente_produto"]);								
								
								$chamado["conta_username"] 		= @$info_conta_chamado["username"];
								$chamado["conta_tipo"] 			= @$info_conta_chamado["tipo_conta"];
								$chamado["conta_dominio"] 		= @$info_conta_chamado["dominio"];
								$chamado["conta_pop"]			= $pop_nome;
								$chamado["conta_nas"]			= $nas_nome;
								$chamado["conta_contrato"]		= @$info_conta_chamado["id_cliente_produto"];
								$chamado["conta_ip"]			= @$info_conta_chamado["ipaddr"];
								$chamado["conta_mac"]			= @$info_conta_chamado["mac"];
								$chamado["conta_gateway"]		= @$info_conta_chamado["ip_externo"];
								$chamado["conta_produto"]		= @$info_contrato_chamado["nome_produto"];
								
								
								
								//$chamado["conta_produto"]		= @$info_conta_chamado["id_cliente_produto"];
							}							
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

						$array_grupos = $this->helpdesk->obtemListaGruposComPopulacao(true);
						$array_responsaveis = $this->helpdesk->obtemListaAdminGrupo();
						$contas_cliente = $contas->obtemContasPorCliente($id_cliente);
						
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
						if($subtela == "ordemservico" || $subtela == "imprimir_os") {
							$tmp_pref = VirtexModelo::factory("preferencias");
						
							//Cria matriz de ids de contas
							$enderecos_cobranca = array();
							$enderecos_instalacao = array();
							$endereco_cliente = array();
														
							$tmp_cliente = $this->clientes->obtemPeloId($id_cliente);
							
							$cidade = $tmp_pref->obtemCidadePeloID($tmp_cliente["id_cidade"]);
							
							$endereco_cliente["endereco"] = $tmp_cliente["endereco"];
							$endereco_cliente["complemento"] = $tmp_cliente["complemento"];
							$endereco_cliente["cidade"] = $cidade["cidade"] . " - " . $cidade["uf"];
							$endereco_cliente["cep"] = $tmp_cliente["cep"];
							$endereco_cliente["bairro"] = $tmp_cliente["bairro"];
							
							
							foreach($contas_cliente as $chave => $valor) {	
								$cobranca_temp = $cobranca->obtemEnderecoCobrancaReferenciado($valor["id_cliente_produto"]);
								$instalacao_temp = $contas->obtemEnderecoInstalacaoReferenciado($valor["id_conta"]);
								
								if ($cobranca_temp) $enderecos_cobranca[$valor["id_conta"]] = $cobranca_temp;
								if ($instalacao_temp) $enderecos_instalacao[$valor["id_conta"]] = $instalacao_temp;
							}
							
							$tipos = $this->helpdesk->obtemTiposChamado();
							$origens = $this->helpdesk->obtemOrigensChamado();
							$classificacoes = $this->helpdesk->obtemClassificacoesChamado();
							$status_chamado = $this->helpdesk->obtemStatusChamado();

							$grupos = $this->helpdesk->obtemListaGruposComPopulacao(true);
							$contas_cliente = $contas->obtemContasPorCliente($id_cliente);	
							$responsaveis = $this->helpdesk->obtemListaAdminGrupo();
							$periodos = $this->helpdesk->obtemPeriodos();
							
							if($id_chamado) $this->_view->atribui("acao","gravar");
							else $this->_view->atribui("acao","alterar");														
							
							$this->_view->atribui("periodos", $periodos);
							$this->_view->atribui("endereco_cliente", MJson::encode($endereco_cliente));
							$this->_view->atribui("enderecos_cobranca", MJson::encode($enderecos_cobranca));
							$this->_view->atribui("enderecos_instalacao", MJson::encode($enderecos_instalacao));
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
						$this->_view->atribui("id_chamado", $id_chamado);
						$this->_view->atribui("id_cliente", $id_cliente);
	
						if ($subtela == "ordemservico") {						
							
							$tipo = @$_REQUEST["tipo"];
							$criado_por = @$_REQUEST["criado_por"];
							$id_grupo = @$_REQUEST["id_grupo"];
							$assunto = @$_REQUEST["assunto"];
							$descricao = @$_REQUEST["descricao"];
							$origem = @$_REQUEST["origem"];
							$classificacao = @$_REQUEST["classificacao"];
							$responsavel= @$_REQUEST["responsavel"];
							$id_cliente = @$_REQUEST["id_cliente"];
							$id_conta = @$_REQUEST["id_conta"];
							$id_cliente_produto = @$_REQUEST["id_cliente_produto"];		
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
							
							
							
							$id_chamado = $this->helpdesk->abreChamado($tipo,$criado_por,$id_grupo,$assunto,$descricao,$origem,$classificacao,$prioridade,$responsavel,$id_classe,$id_cliente,$id_cliente_produto,$id_conta,0,0,0,0,0,0,$id_chamado_pai);
							$confirma_chamado = $this->helpdesk->obtemChamadoPeloId($id_chamado);
							
							if($confirma_chamado) {
								$data_agendamento = null;
								if($agendamento){ 
									$data_tmp = explode("/", $agendamento);
									$data_agendamento = $data_tmp[2] . "-" . $data_tmp[1] . "-" . $data_tmp[0];
								}
								
								$this->helpdesk->registrarOrdemServico($id_chamado, $endereco_os, $complemento_os, $bairro_os, $cidade_os, $data_agendamento, $periodo, $id_classe);
							
								$url_redir = "admin-clientes.php?op=helpdesk&tela=alteracao&id_cliente=$id_cliente&id_chamado=$id_chamado_pai";
								$mensagem = "Ordem de serviço criada com sucesso";
							} else {
								$mensagem = "Erro ao criar a ordem de serviço";
								$url_redir = "admin-clientes.php?op=helpdesk&tela=alteracao&id_cliente=$id_cliente&id_chamado=$id_chamado_pai";
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
							
							$url_redir = "admin-clientes.php?op=helpdesk&tela=alteracao&id_cliente=$id_cliente&id_chamado=$id_chamado";
							
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
										
					$chamados_pendentes = $this->helpdesk->obtemChamadosPendentesPorCliente($id_cliente);
					$chamados_terminados = $this->helpdesk->obtemChamadosFinalizadosPorCliente($id_cliente);
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
		
		protected function executaEmailsCancelados() {
		
			$this->requirePrivLeitura("_CLIENTES_EMAILS_CANCELADOS");
		

			$this->_view->atribuiVisualizacao("emails_cancelados");
			
			$id_cliente = @$_REQUEST["id_cliente"];
			$this->_view->atribui("id_cliente",$id_cliente);
			
			$tela = @$_REQUEST["tela"];
			if( !$tela ) $tela = "listagem";
			$this->_view->atribui("tela",$tela);
			
			$id_conta = @$_REQUEST["id_conta"];
			$this->_view->atribui("id_conta",$id_conta);

			$info = $this->clientes->obtemPeloId($this->id_cliente);
			$this->_view->atribui("nome_razao",$info["nome_razao"]);
			
			$contas = VirtexModelo::factory("contas");
			$cobranca = VirtexModelo::factory("cobranca");
			$produtos = VirtexModelo::factory("produtos");
			
			if( $tela == "listagem" ) {
				$emails = $contas->obtemContasEmailCanceladas($this->id_cliente);
				$this->_view->atribui("emails", $emails);
			} else if( $tela == "recuperar" && $id_conta ) {
			
				$this->requirePrivGravacao("_CLIENTES_EMAILS_CANCELADOS");
			
			
				$conta = $contas->obtemContaPeloId($id_conta);
				
				$conta["contrato"] = $cobranca->obtemContratoPeloId($conta["id_cliente_produto"]);
				$conta["produto"] = $produtos->obtemPlanoPeloId($conta["contrato"]["id_produto"]);
							
				$this->_view->atribui("conta",$conta);
				
				//echo "ID CLIENTE: $id_cliente<br>\n";

				// Obtem os contratos ativos do cliente (para migração);
				$contratos_cliente = $cobranca->obtemContratos($id_cliente,"A");
				
				for($i=0;$i<count($contratos_cliente);$i++) {
					$contratos_cliente[$i]["id_cliente"] = $id_cliente;
				}
				
				// Obtem os contratos de hospedagem ativos do provedor para micracao
				$contratos_provedor = $cobranca->obtemContratos(1,"A","H");

				for($i=0;$i<count($contratos_provedor);$i++) {
					$contratos_provedor[$i]["id_cliente"] = 1;
				}
				
				$contratos = array_merge($contratos_cliente,$contratos_provedor);
				$this->_view->atribui("contratos",$contratos);
				
				
				$acao = @$_REQUEST["acao"];
				
				$dadosLogin = $this->_login->obtem("dados");
				$this->_view->atribui("dadosLogin",$dadosLogin);
				
				if( $acao ) {
			
					$id_cp_destino = @$_REQUEST["id_cp_destino"];
					$this->_view->atribui("id_cp_destino",$id_cp_destino);
					
					$erro = "";

					$senha_admin = @$_REQUEST["senha_admin"];
					if( !$senha_admin ) {
						$erro = "Operação não autorizada: SENHA NÃO FORNECIDA.";
					} else {
						if( md5(trim($senha_admin)) != $dadosLogin["senha"] ) {
							$erro = "Operação não autorizada: SENHA NÃO CONFERE.";
						}

					}

					$this->_view->atribui("erro",$erro);

					if( !$erro ) {
						/**
						 * FAZ A RECUPERACAO!!!
						 */
						
						$contas->recuperaEmail($id_conta,$id_cp_destino,$dadosLogin);
						





						/**
						 * Recuperado com sucesso...
						 */
						$url = "admin-clientes.php?op=emails_cancelados&tela=listagem&id_cliente=".$id_cliente;
						$this->_view->atribui("url",$url);
						$this->_view->atribui("mensagem","E-mail recuperado com sucesso.");
						$this->_view->atribuiVisualizacao("msgredirect");
					
					}

				}
								
			
			}


			
		}
		
	}

?>