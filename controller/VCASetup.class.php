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
		
		
		protected $netCfg;
		protected $nasCfg;
		
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


		
		protected function executeSQLScript($arquivo) {
			$script = "var/install/" . $arquivo;		
			return($this->bd->executeSQLScript($script));
		}
		
		public function executa() {
		
			//echo "<pre>";
			//print_r($this->SO->executa("php bin/vtx-update.php -DC | psql -U virtex"));
			//echo "</pre>";
			
			//exit;
			
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
			
			/**
			$this->_view->atribui("",$);
			$this->_view->atribui("",$);
			$this->_view->atribui("",$);
			$this->_view->atribui("",$);
			*/
			
			
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
			
			
			$faltandoConfiguracao = true;
			
			if( $cidadesOK && $servidoresOK && $nasOK && $popOK && $adminOK && $modelosOK && $prefCobraOK && $formaPagtoOK && $prefProvOK && $prefGeralOK ) { 
				$faltandoConfiguracao = false;
			}
			
			$this->_view->atribui("faltandoConfiguracao",$faltandoConfiguracao);
			
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
					$this->preferencias->atualizaPreferenciasProvedor($endereco,$localidade,$cep,$cnpj,$fone);
				}

				/**
				 * Preferencias gerais.
				 */
				if( !$prefGeralOK ) {
					// TODO: pegar informações de arquivo ini ou de formulário
					// $nome = "VirtexAdmin Service Provider";
					// $dominio_padrao = "mosman.com.br";
					$radius_server = "127.0.0.1";
					$hosp_server = "127.0.0.1";
					$hosp_ns1 = "127.0.0.1";
					$hosp_ns2 = "127.0.0.1";
					$hosp_uid = "65534";
					$hosp_gid = "65534";
					$hosp_base = "/mosman/virtex/dados/hospedagem";
					$mail_server = "127.0.0.1";
					$pop_host = "mail.".$dominio_padrao;
					$smtp_host = $pop_host;
					$mail_uid = "65534";
					$mail_gid = "65534";
					$email_base = "/mosman/virtex/dados/emails";
					$agrupar = 20;

					$this->preferencias->atualizaPreferenciasGerais($dominio_padrao,$nome,$radius_server,$hosp_server,$hosp_ns1,$hosp_ns2,$hosp_uid,$hosp_gid,$hosp_base,$mail_server,$mail_uid,$mail_gid,$email_base,$pop_host,$smtp_host,$agrupar);

				}
				
				$this->_view->atribui("url","admin-login.php");
				$this->_view->atribui("mensagem","Servidor configurado com sucesso.");
				$this->_view->atribuiVisualizacao("msgredirect");
				
				
			}

		}
	
	}
	

?>
