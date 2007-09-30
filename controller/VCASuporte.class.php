<?
	class VCASuporte extends VirtexControllerAdmin {
	
	
		public function __construct() {
			parent::__construct();
		}
		
		public function init() {
			parent::init();
			$this->_view = VirtexViewAdmin::factory("suporte");
		}
		
		protected function executa() {
			switch($this->_op) {
				case 'ferramentas':
					$this->executaFerramentas();
					break;
				case 'monitoramento':
					$this->executaMonitoramento();
					break;
				case 'graficos':
					$this->executaGraficos();
					break;
				default:
					// do something
			}
		}
		
		protected function executaFerramentas() {
			$ferramenta = @$_REQUEST["ferramenta"];
			
			$this->_view->atribuiVisualizacao("ferramentas");
			$this->_view->atribui("ferramenta",$ferramenta);
			
			switch($ferramenta) {
				case 'ipcalc':
					$this->executaIPCalc();
					break;
				case 'arp':
					$this->executaARP();
					break;
				case 'ping':
					$this->executaPING();
					break;
			
			}
			


		}
		
		protected function executaIPCalc() {
			$ip = @$_REQUEST["ip"];
			$mascara = @$_REQUEST["mascara"];
			
			$this->_view->atribui("ip",$ip);
			$this->_view->atribui("mascara",$mascara);
			
			if( $ip && $mascara ) {
				try {
					$info = MInet::calculadora($ip,$mascara);
					$this->_view->atribui("info",$info->toArray());
				} catch(MException $e) {
					$this->_view->atribui("erro",$e->getMessage());
				}
			}		
		}
		
		protected function executaARP() {
			$equipamentos = VirtexModelo::factory('equipamentos');
			$servidores = $equipamentos->obtemListaServidores(true);
			
			$this->_view->atribui("servidores",$servidores);
			
			$id_servidor = @$_REQUEST["id_servidor"];
			
			if( $id_servidor ) {
				$servidor = $equipamentos->obtemServidor($id_servidor);
				$this->_view->atribui("servidor",$servidor);
				
				$ip = @$_REQUEST["ip"];
				$this->_view->atribui("ip",$ip);
				
				if( !$ip ) $ip = "-a";
				
				$conn = new VirtexCommClient();
				if(@!$conn->open($servidor["ip"],$servidor["porta"],$servidor["chave"],$servidor["usuario"],$servidor["senha"])) {
					$erro = "Erro de conexão com o servidor";
					$this->_view->atribui("erro",$erro);
				} else {
					if( $conn->estaConectado() ) {
						$tabelaARP = $conn->getARP($ip);
						$this->_view->atribui("tabelaARP",$tabelaARP);
					}
				
				}
			
			}
		}
		
		protected function executaPing() {
			$equipamentos = VirtexModelo::factory('equipamentos');
			$servidores = $equipamentos->obtemListaServidores(true);
			
			$this->_view->atribui("servidores",$servidores);
			
			$id_servidor = @$_REQUEST["id_servidor"];
			
			$pacotes = @$_REQUEST["pacotes"];
			$tamanho = @$_REQUEST["tamanho"];
			
			if( !$pacotes ) $pacotes = 4;
			if( !$tamanho ) $tamanho = 32;
			
			$this->_view->atribui("pacotes",$pacotes);
			$this->_view->atribui("tamanho",$tamanho);
			
			$ip = @$_REQUEST["ip"];			
			$this->_view->atribui("ip",$ip);
			
			if( $id_servidor ) {
			
				if( !$ip ) {
					$erro = "Endereço IP não fornecido.";
					$this->_view->atribui("erro",$erro);
				} else {
			
					$servidor = $equipamentos->obtemServidor($id_servidor);
					$this->_view->atribui("servidor",$servidor);

					$conn = new VirtexCommClient();
					if(@!$conn->open($servidor["ip"],$servidor["porta"],$servidor["chave"],$servidor["usuario"],$servidor["senha"])) {
						// echo "ERRO DE CONEXÃO\n\n";
						$erro = "Erro de conexão com o servidor";
						$this->_view->atribui("erro",$erro);
					} else {
						if( $conn->estaConectado() ) {
							$ping = $conn->getFPING($ip,$pacotes,$tamanho);
							$this->_view->atribui("ping",$ping);
						}

					}
				}
			
			}
		
		}
		
		
		protected function executaMonitoramento() {
			$this->_view->atribuiVisualizacao("monitoramento");
			$tela = @$_REQUEST["tela"];
			$this->_view->atribui("tela",$tela);

			$equipamentos = VirtexModelo::factory('equipamentos');
			$registros = $equipamentos->obtemListaPOPs();
			
			$resumo = array("IER" => 0, "WRN" => 0, "OK" => 0);
			
			for($i=0;$i<count($registros);$i++) {
				if($registros[$i]["ativar_monitoramento"] == 't' && $registros[$i]["ip"]) {
					$status = $equipamentos->obtemMonitoramentoPop($registros[$i]["id_pop"]);
					
					$registros[$i] = array_merge($registros[$i],$status);
					$registros[$i]["st_mon"] = $status["status"];
					if( $status["status"] ) {
						$resumo[ $status["status"] ]++;
					}
					
				}
			}
			
			$this->_view->atribui("resumo",$resumo);			
			$this->_view->atribui("registros",$registros);
		
		}
		
		protected function executaGraficos() {
			$this->_view->atribuiVisualizacao("graficos");

		}
		
	}



?>
