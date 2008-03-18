<?

	class VVAFinanceiro extends VirtexViewAdmin {
	
		protected $titulo;

		protected function __contruct() {
			parent::__construct();
		}

		protected function init() {
			parent::init();
			$this->nomeSessao = "Financeiro";
		}

		public function exibe() {

			switch( $this->_visualizacao ) {
				case 'cobranca':
					$this->titulo = "Cobrança :: ";
					switch($this->obtem("op")) {

						case 'gerar_lista_boletos':
							$this->exibeListaBoletos();
							break;

						case 'bloqueios':
							$this->exibeBloqueios();
							break;

						case 'amortizacao':
							$this->exibeAmortizacao();
							break;

						case 'gerar_cobranca':
							$this->exibeGerarCobranca();
							break;

						case 'arquivos':
							$this->exibeArquivos();
							break;

						case 'relatorios_cobranca':
							$this->exibeRelatoriosCobranca();
							break;

						case 'gerar_lista_faturas':
							$this->exibeListaFaturas();
							break;

						default:

					} 
					break;
					
				case 'faturamento':
					//Vai fazer alguma coisa ainda
					$this->titulo = "Faturamento :: ";
					switch($this->obtem("op")) {
						case 'relatorios_faturamento':
							$this->exibeRelatoriosFaturamento();
							break;

					} 					
					break;
			}
			
			//echo "<pre>";
			//print_r($this);
			//echo "</pre>";
			parent::exibe();

		}

		protected function exibeListaBoletos() {
			$this->_file = "financeiro_cobranca_listar_boletos.html";
			$this->titulo .= "Boleto das Faturas do Lote";
			$this->atribui("titulo", $this->titulo);
		}

		protected function exibeBloqueios() {
			$this->_file = "financeiro_cobranca_bloqueios.html";
			$this->titulo .= "Clientes Passíveis de Bloqueio";
			$this->atribui("titulo", $this->titulo);
		}

		protected function exibeAmortizacao() {
			$this->_file = "financeiro_cobranca_amortizacao.html";
			$this->titulo .= "Amortização";
			$this->atribui("titulo", $this->titulo);
		}
		
		protected function exibeListaFaturas() {
			$this->_file = "financeiro_cobranca_listar_faturas.html";
			$this->titulo .= "Listagem das Faturas";
			$this->atribui("titulo", $this->titulo);
		}
		
		

		protected function exibeGerarCobranca() {
			$id_remessa = @$_REQUEST["id_remessa"];
			$this->titulo .= "Gerar Cobrança/Boletos";
			$this->_file .= "financeiro_cobranca_gerar_cobranca.html";
			if($id_remessa) $this->_file = "financeiro_cobranca_gerar_cobranca.html";
			$this->atribui("titulo", $this->titulo);
		}

		protected function exibeArquivos() {
			
			$this->_file = "financeiro_cobranca_retorno.html";
			$this->titulo .= "Troca de Arquivos";
			
			$menu = array();
			
			$menu[] = array("texto" => "Retorno", "url" => "admin-financeiro.php?op=arquivos&tela=retorno");
			$menu[] = array("texto" => "Remessa", "url" => "admin-financeiro.php?op=arquivos&tela=remessa");
			
			$selected = $this->obtem("tela") == "remessa" ? 2 : 1;
			
			$this->titulo .= " :: " . ($this->obtem("tela") == "remessa" ? "Remessa" : "Retorno");

			$this->configureMenu($menu,true,true, $selected);
			
			$this->atribui("titulo", $this->titulo);
		}

		protected function exibeRelatoriosCobranca() {
			$relatorio = @$_REQUEST["relatorio"];
			$this->titulo .= "Relatório :: ";
			switch($relatorio) {
				case "cortesias":
					$this->_file = "relatorio_cortesia.html";
					$this->titulo .= "Cortesias";
					$this->atribui("titulo", $this->titulo);
					break;
				case "cancelamentos":
					$this->_file = "relatorio_cancelamento.html";
					$this->titulo .= "Cancelamentos";
					$this->atribui("titulo", $this->titulo);
					break;
				case "atrasos":
					$this->_file = "relatorio_atraso.html";
					$this->titulo .= "Atrasos";
					$this->atribui("titulo", $this->titulo);
					break;
				case "atrasos_detalhes":
				$this->_file = "relatorio_atraso_detalhes.html";
				$this->titulo .= "Detalhes de Atrasos";
				$this->atribui("titulo", $this->titulo);
				break;
				case "cliente_produto":
					$this->_file = "relatorio_cliente_produto.html";
					$this->titulo .= "Contratos por produto";
					$this->atribui("titulo", $this->titulo);
					break;
				case "cliente_produto_detalhe":
					$this->_file = "relatorio_cliente_produto_detalhe.html";
					$this->titulo .= "Clientes por produto";
					$this->atribui("titulo", $this->titulo);
					break;
				case "cliente_tipo_produto":
					$this->_file = "relatorio_cliente_tipo_produto.html";
					$this->titulo .= "Contratos por tipo de produto";
					$this->atribui("titulo", $this->titulo);
					break;
				case "cliente_tipo_produto_detalhe":
					$this->_file = "relatorio_cliente_tipo_produto_detalhe.html";
					$this->titulo .= "Clientes por tipo de produto";
					$this->atribui("titulo", $this->titulo);
					break;
				case "adesoes":
					$this->_file = "relatorio_adesoes.html";
					$this->titulo .= "Adesões";
					$this->atribui("titulo", $this->titulo);
					break;
				case "reagendamentos":
					$this->_file = "relatorio_reagendamentos.html";
					$this->titulo .= "Reagendamentos";
					$this->atribui("titulo", $this->titulo);
					break;
				case "bloqueios_desbloqueios":
					$this->_file = "relatorio_bloqueios_desbloqueios.html";
					$this->titulo .= "Bloqueios e Desbloqueios";
					$this->atribui("titulo", $this->titulo);
					break;
				case "bloqueios_desbloqueios_detalhes":
					$this->_file = "relatorio_bloqueios_desbloqueios_detalhes.html";
					$this->titulo .= "Detalhes Bloqueios e Desbloqueios";
					$this->atribui("titulo", $this->titulo);
					break;
				case "emails_cobranca":
					$this->_file = "relatorio_emails_cobranca.html";
					$this->titulo .= "Emails de Cobrança";
					$this->atribui("titulo", $this->titulo);
					break;
				case "evolucao":
					$this->_file = "relatorio_evolucao.html";
					$this->titulo .= "Evolução";
					$this->atribui("titulo", $this->titulo);
					break;

				default:
					die("erro");
			}
		}
		
		public function exibeRelatoriosFaturamento() {
			
			$relatorio = @$_REQUEST["relatorio"];
			$titulo = "Relatório :: ";
			
			
			switch($relatorio) {
				
				case "previsao":	
					$this->_file = "financeiro_faturamento_previsao.html";	
					$this->atribui("titulo", $titulo."Previsão de Faturamento");	
					break;
				
				case "faturamento":					
					$this->_file = "financeiro_faturamento_corporativo.html";	
					$this->atribui("titulo", $titulo."Faturamento Anual");	
					break;
				
				case "por_produto":					
					$this->_file = "financeiro_faturamento_produto.html";	
					$this->atribui("titulo", $titulo."Faturamento por Produto");	
					break;
					
				case "por_periodo":					
					$this->_file = "financeiro_faturamento_periodo.html";	
					$this->atribui("titulo", $titulo."Faturamento por Período");	
					break;
			}
		}		

	}

?>