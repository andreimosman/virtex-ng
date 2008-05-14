<?


	class VCUIndex extends VirtexControllerUsuario {

		protected $cacheFormaPagamento;
		
		public function __construct() {
			parent::__construct();
			
			
		}
		
		public function init() {
			parent::init();
			
			$this->cacheFormaPagamento = array();
			
		}
		
		public function executa() {
			parent::executa();

			$this->_view = VirtexViewUsuario::factory("index");
			
			switch(@$_REQUEST["op"]) {
			
				case 'menu':
					$this->executaMenu();
					break;

				case 'home':
					$this->executaHome();
					break;
					
				case 'dados_cadastrais':
					$this->executaDadosCadastrais();
					break;
				
				case 'contratos':
					$this->executaContratos();
					break;
				
				case 'contrato_detalhe':
					$this->executaContratoDetalhe();
					break;
					
				case 'contrato_faturas':
					$this->executaContratoFaturas();
					break;
			
			
			}
			
			
		
		
		}
		
		protected function executaMenu() {
			$this->_view->atribuiVisualizacao("menu");			
		}
		
		protected function executaHome() {
			$this->_view->atribuiVisualizacao("home");
			
			$cobranca = VirtexModelo::factory("cobranca");
			
			$dadosLogin = $this->_login->obtem("dados");
			
			$this->_view->atribui("cliente",$dadosLogin["cliente"]);
			
			$contratos = count($cobranca->obtemContratos($dadosLogin["cliente"]["id_cliente"],"A","",""));
			
			$contratos_sem_aceite = count($cobranca->obtemContratos($dadosLogin["cliente"]["id_cliente"],"A","","f"));
			
			$this->_view->atribui("contratos",$contratos);
			$this->_view->atribui("contratos_sem_aceite",$contratos_sem_aceite);
			
			$preferencias = VirtexModelo::factory("preferencias");
			
			$provedor_geral = $preferencias->obtemPreferenciasGerais();
			$this->_view->atribui("nome_provedor",$provedor_geral["nome"]);
			
					
		}
		
		protected function executaDadosCadastrais() {
			
			$this->_view->atribuiVisualizacao("dados_cadastrais");
		
		
			$dadosLogin = $this->_login->obtem("dados");
			
			$preferencias = VirtexModelo::factory("preferencias");
			
			$this->_view->atribui("cliente",$dadosLogin["cliente"]);
			
			$cidade_uf = $preferencias->obtemCidadePeloID($dadosLogin["cliente"]["id_cidade"]);
			
			$this->_view->atribui("cidade_uf",$cidade_uf);

			$preferencias = VirtexModelo::factory("preferencias");
						
			$provedor_geral = $preferencias->obtemPreferenciasGerais();
			$this->_view->atribui("nome_provedor",$provedor_geral["nome"]);
			
					
		}
		
		protected function executaContratos() {
			
			$this->_view->atribuiVisualizacao("contratos");

			$dadosLogin = $this->_login->obtem("dados");

			$cobranca = VirtexModelo::factory("cobranca");
			
			$aceite = @$_REQUEST["aceite"];
			$this->_view->atribui("aceite",$aceite);
			
			
			$contratos = $cobranca->obtemContratos($dadosLogin["cliente"]["id_cliente"],"A","",$aceite);
			$numero_contratos = count($contratos);
			$this->_view->atribui("numero_contratos",$numero_contratos);

			$this->_view->atribui("contratos",$contratos);
			
			$preferencias = VirtexModelo::factory("preferencias");
						
			$provedor_geral = $preferencias->obtemPreferenciasGerais();
			$this->_view->atribui("nome_provedor",$provedor_geral["nome"]);
			
		
		}
		
		
		
		protected function executaContratoDetalhe() {
		
			$dadosLogin = $this->_login->obtem("dados");
			$this->_view->atribuiVisualizacao("contrato_detalhe");
			
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			
			
			$aceito = @$_REQUEST["aceito"];


			$cobranca = VirtexModelo::factory("cobranca");
			
			if( $aceito ) {
				$this->_view->atribuiVisualizacao("msgredirect");


				// Aceito
				$cobranca->aceiteContrato($id_cliente_produto);
				
				$mensagem = "Contrato aceito com sucesso.";
				$url = "index.php?op=".$_REQUEST["op"]."&id_cliente_produto=".$id_cliente_produto;
				$target = "_self";
				
				$this->_view->atribui("mensagem",$mensagem);
				$this->_view->atribui("url",$url);
				$this->_view->atribui("target",$target);
				
				return;
			
			}
			
			
			
			$contrato_detalhado = $cobranca->obtemContratoPeloId($id_cliente_produto);
			
			$preferencias = VirtexModelo::factory("preferencias");
			
			$provedor = $preferencias->obtemPreferenciasProvedor();
			
			$provedor_geral = $preferencias->obtemPreferenciasGerais();
			
			$forma_pagamento = $preferencias->obtemFormaPagamento($contrato_detalhado["id_forma_pagamento"]);
			
			$cidade_uf = $preferencias->obtemCidadePeloID($dadosLogin["cliente"]["id_cidade"]);
			
			$dadosLogin["cliente"]["cidade"] = $cidade_uf["cidade"];
			$dadosLogin["cliente"]["estado"] = $cidade_uf["uf"];
			
			
			$this->_view->atribui("op",@$_REQUEST["op"]);
			$this->_view->atribui("id_cliente_produto",$id_cliente_produto);

			
			$this->_view->atribui("cli",$dadosLogin["cliente"]);
			$this->_view->atribui("contrato_detalhado",$contrato_detalhado);
			$this->_view->atribui("nome_provedor",$provedor_geral["nome"]);
			$this->_view->atribui("cnpj_provedor",$provedor["cnpj"]);
			$this->_view->atribui("produto",$contrato_detalhado["nome_produto"]);
			$this->_view->atribui("tipo_produto",$contrato_detalhado["tipo_produto"]);
			$this->_view->atribui("localidade",$provedor["localidade"]);
			$this->_view->atribui("valor_contrato",$contrato_detalhado["valor_produto"]);
			$this->_view->atribui("download_kbps", $contrato_detalhado["bl_banda_download_kbps"]);
			$this->_view->atribui("forma_pagamento",$forma_pagamento["descricao"]);
			$this->_view->atribui("dominio_provedor",strtoupper($provedor_geral["dominio_padrao"]));
			

			
			$contrato_cliente = "";
			
			if( $contrato_detalhado["id_modelo_contrato"] ) {
				// $modelo_contrato = $this->preferencias->obtemModeloContrato($contrato_detalhado["id_modelo_contrato"]);
				
				$tplCTT = new MTemplate("var/contrato");
				
				$nomeArquivo = str_pad($contrato_detalhado["id_modelo_contrato"],5,"0",STR_PAD_LEFT);
				
				if( file_exists("var/contrato/" . $nomeArquivo) && is_readable("var/contrato/" . $nomeArquivo) ) {
				
					$tplCTT->atribui("cli",$dadosLogin["cliente"]);
					$tplCTT->atribui("contrato_detalhado",$contrato_detalhado);
					$tplCTT->atribui("nome_provedor",$provedor_geral["nome"]);
					$tplCTT->atribui("cnpj_provedor",$provedor["cnpj"]);
					$tplCTT->atribui("produto",$contrato_detalhado["nome_produto"]);
					$tplCTT->atribui("tipo_produto",$contrato_detalhado["tipo_produto"]);
					$tplCTT->atribui("localidade",$provedor["localidade"]);
					$tplCTT->atribui("valor_contrato",$contrato_detalhado["valor_produto"]);
					$tplCTT->atribui("download_kbps", $contrato_detalhado["bl_banda_download_kbps"]);
					$tplCTT->atribui("forma_pagamento",$forma_pagamento["descricao"]);
					$tplCTT->atribui("dominio_provedor",strtoupper($provedor_geral["dominio_padrao"]));
										
					$contrato_cliente = $tplCTT->obtemPagina($nomeArquivo);
					
				
				
				}
				
				$this->_view->atribui("contrato_cliente",$contrato_cliente);
				unset($contrato_cliente);
				
			
			}
			
		
		}
		
		
		/**
		 * 
		 */
		protected function obtemFormaPagamento($id_forma_pagamento) {
			if( !@$this->cacheFormaPagamento[$id_forma_pagamento] ) {
				$fp = $this->preferencias->obtemFormaPagamento($id_forma_pagamento);
				
				if( count($fp) ) {
					$this->cacheFormaPagamento[$id_forma_pagamento] = $fp;
				}
			}
			
			return(@$this->cacheFormaPagamento[$id_forma_pagamento]);
			
		}
		
		/**
		 * Lista das Faturas do usuário
		 */
		protected function executaContratoFaturas() {
		
			$dadosLogin = $this->_login->obtem("dados");
			$this->_view->atribuiVisualizacao("contrato_faturas");
			
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			
			
			$cobranca = VirtexModelo::factory("cobranca");
			
			$contrato = $cobranca->obtemContratoPeloId($id_cliente_produto);
			
			$faturas = $cobranca->obtemFaturasPorContrato($id_cliente_produto);
			
			$listaFaturas = array();
			
			for( $i=0; $i<count($faturas);$i++ ) {
			
				$venc = $faturas[$i]["data"];
				$data_pagamento = $faturas[$i]["data_pagamento"];
				
				if( !$data_pagamento ) {
					$data_pagamento = date("Y-m-d");
				}
				
				$diffDias = MData::diff($data_pagamento,$venc);
				
				$status = $faturas[$i]["status"];
				$nossoNumeroBanco = $faturas[$i]["nosso_numero_banco"];
				
				//$nossoNumeroBanco = 394036092;
				
				if( $diffDias < 30 ) {
					$listaFaturas[] = $faturas[$i];

					$forma = $this->obtemFormaPagamento( $faturas[$i]["id_forma_pagamento"] ) ;

					
					if( $diffDias < 0 ) {
						// Atrazado
					
						$listaFaturas[ count($listaFaturas) - 1]["dias_atrazo"] = ($diffDias * -1);
						$listaFaturas[ count($listaFaturas) - 1]["dias_a_vencer"] = 0;
						
						if( $nossoNumeroBanco && $status == "A" && $forma["linkAtrazado"]) {
							$listaFaturas[ count($listaFaturas) - 1]["link"] = $forma["linkAtrazado"] . $nossoNumeroBanco;
						}
						
						
					} else {
						// A vencer
					
						$listaFaturas[ count($listaFaturas) - 1]["dias_atrazo"] = 0;
						$listaFaturas[ count($listaFaturas) - 1]["dias_a_vencer"] = $diffDias;
						
						if( $nossoNumeroBanco && $status == "A" && $forma["linkEmDia"]) {
							$listaFaturas[ count($listaFaturas) - 1]["link"] = $forma["linkEmDia"] . $nossoNumeroBanco;
						}
						
					}
					
					$listaFaturas[ count($listaFaturas) - 1]["total"] = $faturas[$i]["valor"] + $faturas[$i]["acrescimo"] - $faturas[$i]["desconto"] ;					
					$listaFaturas[ count($listaFaturas) - 1]["forma"] = $forma;
					
					

				}
			
			}
			
			unset($faturas);
			
			$faturas = $listaFaturas;
			unset($listaFaturas);
			
			$this->_view->atribui("faturas",$faturas);
			
			
		}
		
		
		
	
	}

?>
