<?

	class VVACobranca extends VirtexViewAdmin {
	
		protected function __contruct() {
			parent::__construct();
		}
		
		protected function init() {
			parent::init();
			$this->nomeSessao = "Cobrança";
		}
		
		public function exibe() {
			
			switch($this->obtem("op")) {

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
					
				case 'relatorios':
					$this->exibeRelatorios();
					break;
			
			}
		
			parent::exibe();
		
		}
		
		protected function exibeBloqueios() {
			$this->_file = "cobranca_bloqueios.html";
			$this->atribui("titulo","Clientes Passíveis de Bloqueio");
		}
		
		protected function exibeAmortizacao() {
			$this->_file = "cobranca_amortizacao.html";
			$this->atribui("titulo", "Amortização");
		}
		
		protected function exibeGerarCobranca() {
			$this->_file = "cobranca_gerar_cobranca.html";
			$this->atribui("titulo","Gerar Cobrança/Boletos");
		}
		
		protected function exibeArquivos() {
			$this->_file = "cobranca_arquivos.html";
			$this->atribui("titulo","Troca de Arquivos");
		}
		
		protected function exibeRelatorios() {
			$relatorio = @$_REQUEST["relatorio"];
			$titulo = "Relatório :: ";
			switch($relatorio) {
				case "cortesias":					
					$this->_file = "relatorio_cortesia.html";	
					$this->atribui("titulo", $titulo."Cortesias");	
					break;
				case "cancelamentos":					
					$this->_file = "relatorio_cancelamento.html";	
					$this->atribui("titulo", $titulo."Cancelamentos");	
					break;
				case "atrasos":					
					$this->_file = "relatorio_atraso.html";	
					$this->atribui("titulo", $titulo."Atrasos");	
					break;
				case "atrasos_detalhes":					
				$this->_file = "relatorio_atraso_detalhes.html";	
				$this->atribui("titulo", $titulo."Detalhes de Atrasos");	
				break;
				case "cliente_produto":					
					$this->_file = "relatorio_cliente_produto.html";	
					$this->atribui("titulo", $titulo."Contratos por produto");	
					break;
				case "cliente_produto_detalhe":					
					$this->_file = "relatorio_cliente_produto_detalhe.html";	
					$this->atribui("titulo", $titulo."Clientes por produto");	
					break;
				case "cliente_tipo_produto":					
					$this->_file = "relatorio_cliente_tipo_produto.html";	
					$this->atribui("titulo", $titulo."Contratos por tipo de produto");	
					break;	
				case "cliente_tipo_produto_detalhe":					
					$this->_file = "relatorio_cliente_tipo_produto_detalhe.html";	
					$this->atribui("titulo", $titulo."Clientes por tipo de produto");	
					break;
				case "adesoes":
					$this->_file = "relatorio_adesoes.html";	
					$this->atribui("titulo", $titulo."Adesões");
					break;
				case "reagendamentos":
					$this->_file = "relatorio_reagendamentos.html";	
					$this->atribui("titulo", $titulo."Reagendamentos");
					break;
				case "bloqueios_desbloqueios":
					$this->_file = "relatorio_bloqueios_desbloqueios.html";	
					$this->atribui("titulo", $titulo."Bloqueios e Desbloqueios");
					break;
				case "emails_cobranca":
					$this->_file = "relatorio_emails_cobranca.html";	
					$this->atribui("titulo", $titulo."Emails de Cobrança");
					break;
				case "evolucao":
					$this->_file = "relatorio_evolucao.html";	
					$this->atribui("titulo", $titulo."Evolução");
					break;
					
				default:
					die("erro");	
			}
		}
		
	}
	
?>