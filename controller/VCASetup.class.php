<?

	/**
	 * Setup do aplicativo
	 *
	 * TODO:
	 *  - Tela pra configurações iniciais.
	 *    - Escolher a cidade (ajax)
	 *  - Diminuir install-script (tirar opções de seleção de NAS e afins).
	 *  - Criar estrutura do banco de dados (caso não exista).
	 *  - Usar endereços externos de IPs (do arquivo de configurações).
	 *  - Aumentar as classes de IP (p/ pelo menos 4096 ips de tcp/ip e pppoe)
	 */
	class VCASetup extends VirtexControllerAdmin {

		protected $administradores;
		protected $equipamentos;
		
		protected $bd;
		
		
		// protected $netCfg;
		// protected $nasCfg;
		
		protected $ext_if;
		protected $ext_ip;
		
		protected $interfaces;

		public function __construct() {
			parent::__construct();
		}
		
		public function init() {
			parent::init();
			$this->_view = VirtexViewAdmin::factory("setup");
			$this->administradores = VirtexModelo::factory("administradores");
			$this->equipamentos = VirtexModelo::factory("equipamentos");
			
			$this->bd = MDatabase::getInstance();
			
			/**

			$this->nasCfg = new MConfig("etc/nas.ini");
			$this->netCfg = new MConfig("etc/network.ini");
			
			$this->interfaces = array();
			
			while( list($interface,$config) = each($this->netCfg->config) ) {
				$this->interfaces[] = $interface;
				if($config["type"] == "external") {
					$this->ext_if = $interface;
					$this->ext_ip = $config["ipaddr"];
				}
			}
			*/
			
		}
		
		/**
		 * Verifica se o administrador inicial já foi configurado
		 */
		protected function verificaAdmin() {			
			$admin = $this->administradores->obtemAdminPeloUsername("admin");
			return(count($admin)>0);
		}
		
		/**
		 * Verifica se existem cidades cadastradas.
		 */
		protected function verificaCidades() {
			$cidades = $this->preferencias->obtemListaCidadesPorUF("AL");
			return(count($cidades)>0);
		}
		
		/**
		 * Verifica as configurações de Servidores
		 */
		protected function verificaServidores() {
			$servers = $this->equipamentos->obtemListaServidores();
			return(count($servers)>0);
		}
		
		/**
		 * Verifica as configurações de NAS
		 */
		protected function verificaNAS() {
			$nas = $this->equipamentos->obtemListaNAS();
			return(count($nas)>0);
		}
		
		/**
		 * Verifica POPs
		 */
		protected function verificaPOPs() {
			$pops = $this->equipamentos->obtemListaPOPs();
			return(count($pops)>0);
		}
		
		/**
		 * Verifica Modelos de Contrato.
		 */
		protected function verificaModelosContrato() {
			$modelosBL = $this->preferencias->obtemModeloContratoPadrao("BL");
			$modelosD = $this->preferencias->obtemModeloContratoPadrao("D");
			$modelosH = $this->preferencias->obtemModeloContratoPadrao("H");
			return(count($modelosBL)>0 && count($modelosD)>0 && count($modelosH)>0);
			
		}
		
		/**
		 * Verifica as preferencias de cobranca.
		 */
		protected function verificaPreferenciasCobranca() {
			$prefCobr = $this->preferencias->obtemPreferenciasCobranca();
			return(count($prefCobr)>0);			
		}
		
		/**
		 * Verifica as formas de pagamento.
		 */
		protected function verificaFormasPagamento() {
			$formas = $this->preferencias->obtemFormasPagamento();
			return(count($formas)>0);
			
		}
		
		/**
		 * Verifica as preferencias gerais
		 */
		protected function verificaPreferenciasGerais() {
			$prefGeral = $this->preferencias->obtemPreferenciasGerais();
			return(count($prefGeral)>0);
		}
		
		/**
		 * Verifica as preferencias do provedor.
		 */

		protected function verificaPreferenciasProvedor() {
			$prefProv = $this->preferencias->obtemPreferenciasProvedor();
			return(count($prefProv)>0);
		}
		
		/**
		 * Verifica as faixas de banda.
		 */
		protected function verificaFaixasBanda() {
			$faixas = $this->preferencias->obtemListaBandas();
			return(count($faixas)>0);
		}
		
		/**
		 * Verifica os Links Externos.
		 */
		protected function verificaLinksExternos() {
			$links = $this->preferencias->obtemListaLinks();
			return(count($links)>0);
		}
		
		protected function verificaMonitoramento() {
			$monitor = $this->preferencias->obtemMonitoramento();
			return(count($monitor)>0);
		}
		
		protected function verificaProdutos() {
			$produtos = VirtexModelo::factory("produtos");
			$planos = $produtos->obtemListaPlanos();
			return(count($planos)>0);
		}
		
		protected function verificaHelpdesk() {
			$helpdesk = VirtexModelo::factory("helpdesk");
			$grupos = $helpdesk->obtemListaGrupos();
			return(count($grupos)>0);
		}
		

		protected function executeSQLScript($arquivo) {
			$script = "var/install/" . $arquivo;
			$ret = $this->bd->executeSQLScript($script);
			
			//echo "<pre>";
			//print_r($ret);
			//echo "</pre>";
			
			return($ret);
		}
		
		protected function vtxUpdate() {

			$so = $this->SO->getSO();
			
			if( $so == "Linux" ) {
				$psql = "/usr/bin/psql";
				$php = "/usr/bin/php";
			} else {
				// FreeBSD
				$psql = "/usr/local/bin/psql";
				$php = "/usr/local/bin/php";
			}
			
			
			
			// 
			
			//echo "<pre>";
			//print_r($this->_cfg->config);
			//echo "</pre>";
			
			
			//return;
			
			@list($proto,$host_bd) = @explode("://",@$this->_cfg->config["DB"]["dsn"]);
			@list($db_user,$db_pass,$db_host,$db_base) = @preg_split('/[:@\/]/',$host_bd);
			
			$comando = $php . " bin/vtx-update.php -DC ";
			
			//echo "<pre>";
			//echo $this->SO->executa($comando);
			//echo "</pre>";
			
			$pipe = " | " . $psql . " -U $db_user $db_base ";
			
			// echo "<pre>";
			$this->SO->executa($comando . $pipe);
			// echo "</pre>";
						
		}
		
		protected function gravaIni($arquivo,$conteudo) {
			$arquivo = "etc/" . $arquivo;
			
			$fd = fopen($arquivo,"w");
			
			if( !$fd ) {
				echo "NÃO FOI POSSÍVEL CRIAR O ARQUIVO: $arquivo<br>\n";
				return false;
			}
			
			fwrite($fd,$conteudo,strlen($conteudo));
			fclose($fd);
			
			
			return true;
			
			
			//echo "<pre>";
			//echo "### ARQUIVO: $arquivo\n";
			//echo $conteudo;
			//echo "#################################\n";
			//echo "</pre>";
		}
		
		public function criaNetworkIni($networkInfo,$nat) {
			$output = "";
			
			$defGw = $this->SO->getDefaultRoute();
			$ext_if = $this->SO->getExtIf();
			$interfaces = $this->SO->getInterfaces();
			
			//echo "<pre>";
			//print_r($networkInfo);
			//echo "</pre>";
			
			$got = array();
			
			while(list($iface,$dados)=each($networkInfo)) {
				
				// Para o caso de interfaces não utilizadas pelo VA (tun ppp slip plip lo)
				if( !in_array($iface,$interfaces) ) {
					continue;
				}
				
				$output .= "[$iface]\n";
				$output .= "status=up\n";

				for($i=0;$i<count($dados["inet"]);$i++) {
					if( $dados["inet"][$i]->contem($defGw) ) {
						// Interface Externa.
						$output .= "type=external\n";
						$output .= "ipaddr=" . $dados["inet"][$i]->obtemIP() . "\n";
						$output .= "netmask=" . $dados["inet"][$i]->obtemMascara() . "\n";
						$output .= "ipaddr=" . $defGw . "\n";
						$output .= "nat=" . ($nat?1:0) . "\n";
					}
				}
				
				$output .= "\n";
				
			}
			
			return($this->gravaIni("network.ini",$output));
						
		}
		
		public function criaNasIni($enable_tcpip,$tcpip_iface,$enable_pppoe,$pppoe_iface) {
		
			$fator_padrao = 3;
			$output  = "[tcpip:1]\n";
			$output .= "enabled=" . ($enable_tcpip?1:0) . "\n";
			$output .= "interface=" . $tcpip_iface . "\n";
			$output .= "fator=".$fator_padrao . "\n\n";
			
			$output .= "[pppoe:2]\n";
			$output .= "enabled=" . ($enable_pppoe?1:0) . "\n";
			$output .= "interface=" . $pppoe_iface . "\n";
			$output .= "fator=".$fator_padrao . "\n\n";

			return($this->gravaIni("nas.ini",$output));
						
		}
		
		public function criaFtpIni($enable) {
			$output  = "[geral]\n";
			$output .= "enabled=" . ($enable?1:0) . "\n";
			
			return($this->gravaIni("ftp.ini",$output));
		
		}
		
		public function criaDnsIni($type,$enable) {
			$output  = "[geral]\n";
			$output .= "type=" . $type . "\n";
			$output .= "enabled=" . ($enable?1:0) . "\n";
			
			return($this->gravaIni("dns.ini",$output));		
		}
		
		public function criaEmailIni($enable) {
			$output  = "[geral]\n";
			$output .= "enabled=" . ($enable?1:0) . "\n";
			
			return($this->gravaIni("email.ini",$output));
		
		}
		
		public function verificaArquivo($arquivo) {
			return( file_exists($arquivo) );
		}
		
		public function verificaNetworkIni() {
			return($this->verificaArquivo("etc/network.ini"));
		}
		
		public function verificaNasIni() {
			return($this->verificaArquivo("etc/nas.ini"));
		}
		
		public function verificaFtpIni() {
			return($this->verificaArquivo("etc/ftp.ini"));
		}

		public function verificaDnsIni() {
			return($this->verificaArquivo("etc/dns.ini"));
		}

		public function verificaEmailIni() {
			return($this->verificaArquivo("etc/email.ini"));
		}




		public function executa() {
			/**
			 * Gera a estrutura do banco de dados.
			 */
			//$this->vtxUpdate();
			
			$ext_if = $this->SO->getExtIf();
			$interfaces = $this->SO->getInterfaces();
			
			$int_ifaces = array();
			
			for($i=0;$i<count($interfaces);$i++) {
				if( $interfaces[$i] != $ext_if ) {
					$int_ifaces[] = $interfaces[$i];
				}
			}
			
			$networkInfo = $this->SO->getNetworkInfo();
			
			// TESTE
			// 
			
			// /TESTE
			
			
			$defIface = @$int_ifaces[ count($int_ifaces) -1 ];
			$defGw = $this->SO->getDefaultRoute();
			
			
			$this->_view->atribui("int_ifaces", $int_ifaces);
			$this->_view->atribui("defIface",$defIface);
			
			$extIf = array();
			
			while(list($iface,$dados) = each($networkInfo)) {
				if( $iface == $ext_if ) {
					for($i=0;$i<count($dados["inet"]);$i++) {
						// Procura as configurações IP da interface relacionada ao gateway
						if( $dados["inet"][$i]->contem($defGw) ) {
							$extIf["interface"] = $iface;
							$extIf["ipaddr"] = $dados["inet"][$i]->obtemIP();
							$extIf["netmask"] = $dados["inet"][$i]->obtemMascara();
							$extIf["gateway"] = $defGw;
						}
					}
				}
			}
			
			$this->_view->atribui("extIf",$extIf);
			
			$infoLocalId = $this->licenca->obtemInfoLocalId();
			$this->_view->atribui("infoLocalId",$infoLocalId);
			
			
			
			$dominio = @$_REQUEST["dominio"];
			$nome = @$_REQUEST["nome"];
			
			if( !$dominio ) $dominio = "mosman.com.br";
			if( !$nome ) $nome = "VirtexAdmin Service Provider";


			$endereco = @$_REQUEST["endereco"];
			$localidade = @$_REQUEST["localidade"];
			$cep = @$_REQUEST["cep"];
			$cnpj = @$_REQUEST["cnpj"];
			$fone = @$_REQUEST["fone"];
			
			$this->_view->atribui("dominio", $dominio);
			$this->_view->atribui("nome",$nome);
			$this->_view->atribui("endereco",$endereco);
			$this->_view->atribui("localidade",$localidade);
			$this->_view->atribui("cep",$cep);
			$this->_view->atribui("cnpj",$cnpj);
			$this->_view->atribui("fone",$fone);
			
			$networkIniOK	= $this->verificaNetworkIni();
			$nasIniOK	= $this->verificaNasIni();
			$ftpIniOK	= $this->verificaFtpIni();
			$emailIniOK	= $this->verificaEmailIni();
			$dnsIniOK	= $this->verificaDnsIni();
			
			$helpdeskOK		= $this->verificaHelpdesk();
			$planosOK		= $this->verificaProdutos();
			$monitorOK		= $this->verificaMonitoramento();
			$linksOK		= $this->verificaLinksExternos();
			$bandasOK		= $this->verificaFaixasBanda();
			$cidadesOK 		= $this->verificaCidades();
			$servidoresOK 	= $this->verificaServidores();
			$nasOK 			= $this->verificaNAS();
			$popOK 			= $this->verificaPOPs();
			$adminOK 		= $this->verificaAdmin();
			$modelosOK		= $this->verificaModelosContrato();
			$prefCobraOK	= $this->verificaPreferenciasCobranca();
			$formaPagtoOK	= $this->verificaFormasPagamento();
			$prefProvOK		= $this->verificaPreferenciasProvedor();
			$prefGeralOK	= $this->verificaPreferenciasGerais();
			$registroOK		= $this->verificaRegistro();
						
			$faltandoConfiguracao = true;
			
			if( $helpdeskOK && $planosOK && $monitorOK && $networkIniOK && $nasIniOK && $ftpIniOK && $emailIniOK && $dnsIniOK && $linksOK && $bandasOK && $registroOK && $cidadesOK && $servidoresOK && $nasOK && $popOK && $adminOK && $modelosOK && $prefCobraOK && $formaPagtoOK && $prefProvOK && $prefGeralOK ) { 
				$faltandoConfiguracao = false;
			}
			
			$this->_view->atribui("faltandoConfiguracao",$faltandoConfiguracao);
			
			$this->_view->atribui("networkIniOK",$networkIniOK);
			$this->_view->atribui("nasIniOK",$nasIniOK);
			$this->_view->atribui("ftpIniOK",$ftpIniOK);
			$this->_view->atribui("dnsIniOK",$ftpIniOK);
			$this->_view->atribui("emailIniOK",$emailIniOK);
			$this->_view->atribui("servidoresIniOK",$servidoresIniOK);
			
			$this->_view->atribui("helpdeskOK",$helpdeskOK);
			$this->_view->atribui("planosOK",$planosOK);
			$this->_view->atribui("monitorOK",$monitorOK);
			$this->_view->atribui("linksOK",$linksOK);
			$this->_view->atribui("registroOK",$registroOK);
			$this->_view->atribui("bandasOK",$bandasOK);
			$this->_view->atribui("cidadesOK",$cidadesOK);
			$this->_view->atribui("servidoresOK",$servidoresOK);
			$this->_view->atribui("nasOK",$nasOK);
			$this->_view->atribui("popOK",$popOK);
			$this->_view->atribui("adminOK",$adminOK);
			$this->_view->atribui("modelosOK",$modelosOK);
			$this->_view->atribui("prefCobraOK",$prefCobraOK);
			$this->_view->atribui("formaPagtoOK",$formaPagtoOK);
			$this->_view->atribui("prefProvOK",$prefProvOK);
			$this->_view->atribui("prefGeralOK",$prefGeralOK);
			
			if( strtolower( $_SERVER["REQUEST_METHOD"] ) == "get" ) {
				//echo "GET";
			} else {
				// POST (executa ação);
				/**
				 * ARQUIVOS INI.
				 */
				if( !$networkIniOK ) {
					$this->criaNetworkIni($networkInfo,@$_REQUEST["nat"]);
				}
				
				if( !$nasIniOK ) {
					$this->criaNasIni(@$_REQUEST["enable_tcpip"],@$_REQUEST["tcpip_iface"],@$_REQUEST["enable_pppoe"],@$_REQUEST["pppoe_iface"]);
				}
				
				if( !$ftpIniOK ) {
					// Desabilitado por padrão
					$this->criaFtpIni(false);
				}
				
				if( !$dnsIniOK ) {
					// Habilitado por padrão, como MASTER.
					$this->criaDnsIni("master",true);
				}
				
				if( !$emailIniOK ) {
					// Desabilitado por padrão
					$this->criaEmailIni(false);
				}
				
				if( !$registroOK ) {
					// UPLOAD REGISTRO.
					if( @$_FILES["arquivo_licenca"] ) {
						move_uploaded_file(@$_FILES["arquivo_licenca"]["tmp_name"],"etc/virtex.lic");
					}
				}
				
				/**
				 * Monitoramento.
				 */
				if( !$monitorOK ) {
					$monitor = array("id_provedor" => 1, "exibir_monitor" => "t", "alerta_sonoro" => "f", "num_pings" => 5);
					$this->preferencias->atualizaMonitoramento($monitor);
					unset($monitor);
				}

				/**
				 * Links Externos.
				 */
				if( !$linksOK ) {
					$this->preferencias->cadastraLink("Mosman Consultoria","http://www.mosman.com.br/","Site da Mosman Consultoria","_blank");
				}
				
				/**
				 * Faixas de Bandas.
				 */
				if( !$bandasOK ) {
					$this->preferencias->cadastraBanda("32","32");
					$this->preferencias->cadastraBanda("64","64");
					$this->preferencias->cadastraBanda("96","96");
					$this->preferencias->cadastraBanda("128","128");
					$this->preferencias->cadastraBanda("192","192");
					$this->preferencias->cadastraBanda("256","256");
					$this->preferencias->cadastraBanda("384","384");
					$this->preferencias->cadastraBanda("512","512");
					$this->preferencias->cadastraBanda("768","768");
					$this->preferencias->cadastraBanda("1024","1024");
					$this->preferencias->cadastraBanda("0","Sem Controle");
				}
				
				/**
				 * Criação das cidades e estados
				 */
				if( !$cidadesOK ) {
					// Cadastrar UFs
					$this->executeSQLScript("cftb_uf.sql");

					// Cadastrar Cidades
					$this->executeSQLScript("cftb_cidade.sql");
				} 

				/**
				 * Criação dos servidores (registros e arquivos de configuração)
				 */
				if( !$servidoresOK ) {
					$fd = @fopen("/dev/random","r");
					if( !$fd ) {
						$chave = "chavepadrao";
						$senha = "senhapadrao";
					} else {
						$base = fread($fd,20);
						fclose($fd);
						$chave = str_replace("=","",base64_encode(substr($base,0,10)));
						$senha = str_replace("=","",base64_encode(substr($base,10,10)));
					}

					$commServerIni = "[geral]\nchave=$chave\nhost=0.0.0.0\nport=11000\n";
					$commUsersIni  = "[virtex]\npassword=$senha\nenabled=1\n";

					// Grava o comm.server.ini
					$fd = fopen("etc/comm.server.ini","w");
					fwrite($fd,$commServerIni,strlen($commServerIni));
					fclose($fd);

					// Grava o comm.users.ini
					$fd = fopen("etc/comm.users.ini","w");
					fwrite($fd,$commUsersIni,strlen($commServerIni));
					fclose($fd);

					$id_servidor = $this->equipamentos->cadastraServidor("local", "127.0.0.1", "11000", $chave, "virtex", $senha, "t");
				}

				/** 
				 * Cria os NAS primários e suas redes.
				 */
				if( !$nasOK ) {
					if( !$id_servidor ) $id_servidor = 1;

					// NAS TCP/IP
					$id_nas_tcpip = $this->equipamentos->cadastraNAS("TCP/IP", "127.0.0.1", null, "I", $id_servidor, "");

					// Cadastra Rede de InfraEstrutura
					$rede_infra="172.31.255.0/24";
					$this->equipamentos->cadastraRedeIPNAS($id_nas_tcpip,$rede_infra,"I");

					// Cadastra Rede de Clientes
					$rede_origem = "172.16.0.0/20";
					$rede_inicial = "172.16.0.0/30";
					$maximo_redes = 5000;
					$tipo_rede = "C";

					for($ip = new MInet($rede_inicial,$rede_origem),$c=0; $ip->obtemRede() != "" && $c<$maximo_redes; $ip = $ip->proximaRede(),$c++) {
						$nova_rede = $ip->obtemRede() . "/" . $ip->obtemBitmask();
						$this->equipamentos->cadastraRedeIPNAS($id_nas_tcpip,$nova_rede,$tipo_rede);
					}

					// NAS PPPoE
					$id_nas_pppoe = $this->equipamentos->cadastraNAS("PPPoE", "127.0.0.1", "somesecret", "P", $id_servidor, "");
					$rede_pppoe = "172.16.16.0/22";
					$this->equipamentos->cadastraRedePPPoENAS($id_nas_pppoe,$rede_pppoe,$tipo_rede);
				}

				/**
				 * Criação dos POPs iniciais.
				 */
				if( !$popOK ) {
					if( !$id_servidor ) $id_servidor = 1;
					$this->equipamentos->cadastraPop("", "POP Wireless", "POP GERADO PELO SISTEMA", "AP", null, "A", null, $id_servidor, "t", null, null);
					$this->equipamentos->cadastraPop("", "POP Cabo", "POP GERADO PELO SISTEMA", "C", null, "A", null, $id_servidor, "t", null, null);				
				}

				/**
				 * Criação de administradores e privilégios
				 */
				if( !$adminOK ) {

					// Cria o admin
					$id_admin = $this->administradores->cadastraAdmin("admin", "admin@mosman.com.br", "Administrador do Sistema", "123mudar", "A", "f", "f", "", "t");
					$id_admin_suporte = $this->administradores->cadastraAdmin("mosman-suporte", "consultoria@mosman.com.br", "Suporte Mosman Consultoria", "123mudar", "A", "f", "f", "", "t");

					// Criar os privilégios
					$this->executeSQLScript("adtb_privilegio.sql");

					// Vincula o administrador aos privilegios
					$sql = "INSERT INTO adtb_usuario_privilegio SELECT $id_admin as id_admin, id_priv, tem_gravacao as pode_gravar FROM adtb_privilegio";
					$this->bd->consulta($sql,false);
					$sql = "INSERT INTO adtb_usuario_privilegio SELECT $id_admin_suporte as id_admin, id_priv, tem_gravacao as pode_gravar FROM adtb_privilegio";
					$this->bd->consulta($sql,false);

					unset($id_admin);
					unset($id_admin_suporte);
					unset($consulta);

				}

				/**
				 * Criação dos modelos vazios de contrato.
				 */
				if( !$modelosOK ) {

					$modelosBL = $this->preferencias->obtemModeloContratoPadrao("BL");
					$modelosD = $this->preferencias->obtemModeloContratoPadrao("D");
					$modelosH = $this->preferencias->obtemModeloContratoPadrao("H");

					$path_upload_modelo = "var/contrato";

					if( !count($modelosBL) ) {
						$id_modelo_bl = $this->preferencias->cadastraModeloContrato("BL","Modelo Vazio","t","t");
						$arquivoBL = $path_upload_modelo . "/" . str_pad($id_modelo_bl,5,"0",STR_PAD_LEFT);

						$fd = fopen($arquivoBL,"w");
						fputs($fd," ");
						fclose($fd);
					}

					if( !count($modelosD) ) {
						$id_modelo_d = $this->preferencias->cadastraModeloContrato("D","Modelo Vazio","t","t");
						$arquivoD = $path_upload_modelo . "/" . str_pad($id_modelo_d,5,"0",STR_PAD_LEFT);

						$fd = fopen($arquivoD,"w");
						fputs($fd," ");
						fclose($fd);

					}

					if( !count($modelosH) ) {
						$id_modelo_h = $this->preferencias->cadastraModeloContrato("H","Modelo Vazio","t","t");
						$arquivoH = $path_upload_modelo . "/" . str_pad($id_modelo_h,5,"0",STR_PAD_LEFT);

						$fd = fopen($arquivoH,"w");
						fputs($fd," ");
						fclose($fd);

					}
				}

				/**
				 * Planos
				 */
				if( !$planosOK ) {
					// Cria plano BL cortesia.
					$produtos = VirtexModelo::factory("produtos");
					
					$plano = array(
									"nome" => "Cortesia 128",
									"descricao" => "Plano de Cortesia. Utilizado para testes.",
									"tipo" => "BL",
									"valor" => "0",
									"num_emails" => "0",
									"quota_por_conta" => "5000",
									"vl_email_adicional" => "0",
									"permitir_outros_dominios" => "f",
									"email_anexado" => "f",
									"numero_contas" => "1",
									"comodato" => "f",
									"valor_comodato" => "0",
									"desconto_promo" => "0",
									"periodo_desconto" => "0",
									"tx_instalacao" => "0",
									"valor_estatico" => "t",
									"comissao" => "0",
									"comissao_migracao" => "0",
									"modelo_contrato" => "1",
									"banda_upload_kbps" => "128",
									"banda_download_kbps" => "128",
									"franquia_trafego_mensal_gb" => "0",
									"valor_trafego_adicional_gb" => "0",
									"disponivel" => "t"
									);
					
					
					
					$produtos->cadastraPlano($plano);
				}
				
				
				/**
				 * Configuração do helpdesk.
				 */
				if( !$helpdeskOK ) {
					$helpdesk = VirtexModelo::factory("helpdesk");
					$id_gr_adm = $helpdesk->cadastraGrupo("ADMINISTRATIVO", "Questões Administrativas", 't');
					$id_gr_tec = $helpdesk->cadastraGrupo("TECNICO", "Questões Técnicas", 't');
					$id_gr_fin = $helpdesk->cadastraGrupo("FINANCEIRO", "Questões Financeiras/Cobrança", 't');
					
					// admin
					$infoAdmin = $this->administradores->obtemAdminPeloUsername("admin");
					$infoSuporte = $this->administradores->obtemAdminPeloUsername("mosman-suporte");
					
					if( count($infoAdmin) ) {
						// Vincula com os 3 grupos.
						$helpdesk->cadastraUsuarioGrupo($id_gr_adm, $infoAdmin["id_admin"], $infoAdmin["admin"], "t");
						$helpdesk->cadastraUsuarioGrupo($id_gr_tec, $infoAdmin["id_admin"], $infoAdmin["admin"], "t");
						$helpdesk->cadastraUsuarioGrupo($id_gr_fin, $infoAdmin["id_admin"], $infoAdmin["admin"], "t");
					}
					
					if( count($infoSuporte) ) {
						// Vincula com o grupo técnico.
						$helpdesk->cadastraUsuarioGrupo($id_gr_tec, $infoSuporte["id_admin"], $infoSuporte["admin"], "t");
					}
					
				}
		


				/**
				 * Configuração das preferencias de cobrança.
				 */
				if( !$prefCobraOK ) {
					$this->preferencias->atualizaPreferenciasCobranca("1","2",15,"PRE",20,"/mosman/virtex/dados/contratos","APÓS O VENCIMENTO COBRAR 2% DE MULTA. MAIS JUROS DIÁRIOS DE 1% a.m.","f","admin@mosman.com.br","Prezado usuário,\n\nExistem faturas pendentes. Evite cortes no acesso. entre em contato imediatamente com o provedor.",1);
				}

				/**
				 * Formas de Pagamento
				 */
				if( !$formaPagtoOK ) {
					$sql = "INSERT INTO pftb_forma_pagamento(id_forma_pagamento,tipo_cobranca) VALUES (9999,'NA')";
					$this->bd->consulta($sql,false);

					$dados = array();
					$dados["tipo_cobranca"] = "MO";
					$dados["descricao"] = "Cobrança Manual";

					$this->preferencias->cadastraFormaPagamento($dados);

					unset($dados);
				}

				/**
				 * Preferencias do provedor.
				 */
				if( !$prefProvOK ) {
				
					$localidade = "São Pedro-SP";

					$cidades = $this->preferencias->pesquisaCidadesPeloNome($localidade);
					
					$id_cidade = 0;

					if( count($cidades) ) {
						$id_cidade = $cidades[0]["id_cidade"];
						$this->preferencias->atualizaDisponibilidadeCidade($id_cidade,"t");
					}
				
					$this->preferencias->atualizaPreferenciasProvedor($endereco,$localidade,$cep,$cnpj,$fone);
					
					// Atualizar o cliente provedor (id_cliente 1)
					if( $id_cidade ) {
						$clientes = VirtexModelo::factory("clientes");
						$infoCliente = $clientes->obtemPeloId(1);
						$infoCliente["id_cidade"] = $id_cidade;
						$clientes->altera(1,$infoCliente);
					}
					
				}

				/**
				 * Preferencias gerais.
				 */
				if( !$prefGeralOK ) {
					// TODO: pegar informações de arquivo ini ou de formulário
					// $nome = "VirtexAdmin Service Provider";
					// $dominio_padrao = "mosman.com.br";
					
					$ipaddr = @$extIf["ipaddr"] ? @$extIf["ipaddr"] : "127.0.0.1";
					
					$radius_server = $ipaddr;
					$hosp_server = $ipaddr;
					$hosp_ns1 = $ipaddr;
					$hosp_ns2 = $ipaddr;
					$hosp_uid = "65534";
					$hosp_gid = "65534";
					$hosp_base = "/mosman/virtex/dados/hospedagem";
					$mail_server = $ipaddr;
					$pop_host = "mail.".$dominio;
					$smtp_host = $pop_host;
					$mail_uid = "65534";
					$mail_gid = "65534";
					$email_base = "/mosman/virtex/dados/emails";
					$agrupar = 20;

					$this->preferencias->atualizaPreferenciasGerais($dominio,$nome,$radius_server,$hosp_server,$hosp_ns1,$hosp_ns2,$hosp_uid,$hosp_gid,$hosp_base,$mail_server,$mail_uid,$mail_gid,$email_base,$pop_host,$smtp_host,$agrupar);

				}
				
				$this->_view->atribui("url","admin-login.php");
				$this->_view->atribui("mensagem","Servidor configurado com sucesso.");
				$this->_view->atribuiVisualizacao("msgredirect");
				
				
			}

		}
	
	}
	

