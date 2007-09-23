<?

	class VVAConfiguracoes extends VirtexViewAdmin {

		public function __construct() {
			parent::__construct();

			$this->configureMenu(array(),false,false);	// Configura um menu vazio

		}
		
		protected function init() {
			parent::init();
			$this->nomeSessao = "Configurações";
		}


		protected function obtemItensMenu($op) {
			$itensMenu = array();
			switch($op) {
				case 'servidores':
					$itensMenu[] = array("texto" => "Novo Servidor", "url" => "admin-configuracoes.php?op=equipamentos&tela=servidores&subtela=cadastro");
					break;
				case 'pops':
					$itensMenu[] = array("texto" => "Novo POP", "url" => "admin-configuracoes.php?op=equipamentos&tela=pops&subtela=cadastro");
					break;
				case 'nas':
					$itensMenu[] = array("texto" => "Novo NAS", "url" => "admin-configuracoes.php?op=equipamentos&tela=nas&subtela=cadastro");
					break;
			}
			return($itensMenu);
		}
		
		protected function obtemItensMenuNASRedes($id_nas,$exibir_enderecos="") {
			$itensMenu = array();
			$itensMenu[] = array("texto" => ($exibir_enderecos?"Cadastrar Endereço":"Exibir Endereços"), "url" => "admin-configuracoes.php?op=equipamentos&tela=nas&subtela=redes&id_nas=".$id_nas."&exibir_enderecos=".($exibir_enderecos?"":"1"));
			return($itensMenu);
		}
		
		protected function obtemItensMenuPreferencias($tela) {
			$itensMenu = array();
			switch($tela) {
				case 'cobranca':
					$itensMenu[] = array("texto" => "Editar", "url" => "admin-configuracoes.php?op=preferencias&tela=cobranca&acao=editar");
					$itensMenu[] = array("texto" => "Nova Forma Pgto", "url" => "admin-configuracoes.php?op=preferencias&tela=cobranca&subtela=forma_pagamento");
					break;
				case 'modelos':
					$itensMenu[] = array("texto" => "Novo Modelo", "url" => "admin-configuracoes.php?op=preferencias&tela=modelos&subtela=cadastro");
					break;
			}
			return($itensMenu);
		}

	
		public function exibe() {
			switch($this->_visualizacao) {
				case 'equipamentos':
					$this->exibeEquipamentos();
					break;
				
				case 'preferencias':
					$this->exibePreferencias();
					break;
				
				case 'relatorios':
					$this->exibeRelatorios();
					break;
				
				default:
					// Do something
			
			}

			parent::exibe();

		}
		
		protected function exibeEquipamentos() {
			$titulos = array(
								"servidores" => "Servidores",
								"nas" => "NAS", 
								"pops" => "POPs",
								"cadastro" => "Cadastro",
								"alteracao" => "Alteração",
								"listagem" => "Listagem",
								"redes" => "Endereços"
							);
			
			
			$elementosTit = array();
			
			$elementosTit[] = @$titulos[$this->obtem("tela")];
			
			switch($this->obtem("tela")) {
				case 'servidores':
					switch($this->obtem("subtela")) {
						case 'cadastro':
							$this->_file = "configuracoes_eqpto_servidores_cadastro.html";
							if( $this->obtem("hostname") ) {
								$elementosTit[] = $titulos["alteracao"];
								$elementosTit[] = $this->obtem("hostname");
								$this->configureMenu($this->obtemItensMenu("servidores"),false,true);
							} else {
								$elementosTit[] = $titulos["cadastro"];
							}
							break;
						case 'listagem':
							$this->_file = "configuracoes_eqpto_servidores_listagem.html";
							$elementosTit[] = $titulos["listagem"];
							$this->configureMenu($this->obtemItensMenu("servidores"),true,true);
							
							break;
					}
					break;
				case 'nas':
					switch($this->obtem("subtela")) {

						case 'cadastro':
							$this->_file = "configuracoes_eqpto_nas_cadastro.html";
							$nome = $this->obtem("nome");
							
							if( $nome ) {
								$elementosTit[] = $titulos["alteracao"];
								$elementosTit[] = $nome;
								$this->configureMenu($this->obtemItensMenu("nas"),false,true);
							} else {
								$elementosTit[] = $titulos["cadastro"];
								
							}
							
							break;
						
						case 'redes':
							$elementosTit[] = $this->obtem("nome");
							$elementosTit[] = $titulos["redes"];
							$elementosTit[] = ($exibir_enderecos?"Listagem":"Cadastro");
							$this->configureMenu($this->obtemItensMenuNASRedes($this->obtem("id_nas"),$this->obtem("exibir_enderecos")));
							
							$this->_file = "configuracoes_eqpto_nas_redes.html";
							break;

						default:
							$this->configureMenu($this->obtemItensMenu("nas"),true,true);
							$this->_file = "configuracoes_eqpto_nas_listagem.html";
							break;

					}
					break;
				case 'pops':
					
					switch($this->obtem("subtela")) {
						case 'cadastro':
							// echo "CADASTRO";
							
							if( !$this->obtem("id_pop") ) {
								$elementosTit[] = @$titulos["cadastro"];
							} else {
								$this->configureMenu($this->obtemItensMenu("pops"),false,true);
								$elementosTit[] = @$titulos["alteracao"];
								if( $this->obtem("nome") ) {
									$elementosTit[] = strtoupper($this->obtem("nome"));
								}
							}
							$this->_file = "configuracoes_eqpto_pops_cadastro.html";
							break;

						default:
							// echo "LISTAGEM";
							$this->_file = "configuracoes_eqpto_pops_listagem.html";
							$this->configureMenu($this->obtemItensMenu("pops"),true,true);
							$elementosTit[] = @$titulos["listagem"];
							break;

					}


					break;
			}
			
			$titulo = implode(" :: ",$elementosTit);
			$this->atribui("titulo",$titulo);
			
		}
		
		protected function exibePreferencias() {
			$titulo = "Preferencias";
			
			switch($this->obtem("tela")) {
				case 'geral':
					$this->_file = "configuracoes_preferencias_geral.html";
					$titulo .= " :: Preferências Gerais";
					break;
				case 'provedor':
					$this->_file = "configuracoes_preferencias_provedor.html";
					$titulo .= " :: Preferências do Provedor";
					break;
				case 'cobranca':
					$this->_file = "configuracoes_preferencias_cobranca.html";
					$titulo .= " :: Cobrança";
					if( $this->obtem("subtela") == "forma_pagamento" ) {
						$titulo .= " :: Forma Pagamento";
					} else {
						if( $this->obtem("acao") == "editar" ) {
							$titulo .= " :: Editar";
						}
					}
					$this->configureMenu($this->obtemItensMenuPreferencias($this->obtem("tela"),$this->obtem("subtela")),($this->obtem("acao")=="editar"||$this->obtem("subtela")?false:true),true);
					break;
				case 'modelos':
					if( $this->obtem("subtela") != "exibir_modelo" ) {
						$this->_file = "configuracoes_preferencias_modelos.html";
						$titulo .= " :: Modelos de Contrato";
						$this->configureMenu($this->obtemItensMenuPreferencias($this->obtem("tela"),$this->obtem("subtela")),($this->obtem("subtela")?false:true),true);
					}
					
					break;
				case 'cidades':
					$this->_file = "configuracoes_preferencias_cidades.html";
					$titulo .= " :: Cidades de Atuação";
					break;
				case 'banda':
					$this->_file = "configuracoes_preferencias_banda.html";
					$titulo .= " :: Faixas de banda";
					break;
				case 'monitoramento':
					$this->_file = "configuracoes_preferencias_monitoramento.html";
					$titulo .= " :: Monitoramento";
					break;
				case 'links':
					$this->_file = "configuracoes_preferencias_links.html";
					$titulo .= " :: Links Externos";
					break;
				case 'registro':
					$this->_file = "configuracoes_preferencias_registro.html";
					$titulo .= " :: Registro";
					break;	
			}
			
			$this->atribui("titulo",$titulo);
		}
		
		protected function exibeRelatorios() {
		
		}
	
	}


?>
