<?

	/**
	 * Modelo de Gerenciamento de Contas do Sistema.
	 *
	 * - Integração com Modelos de Preferencias, Equipamentos e Spool.
	 */
	class MODELO_Contas extends VirtexModelo {
		
		protected $cntb_conta;
		protected $cntb_conta_bandalarga;
		protected $cntb_conta_discado;
		protected $cntb_conta_email;
		protected $cntb_conta_hospedagem;
		
		protected $cntb_endereco_instalacao;
		
		protected $preferencias;
		protected $equipamentos;
		protected $spool;
		
		
		public function __construct() {
			parent::__construct();
			$this->cntb_conta 				= VirtexPersiste::factory("cntb_conta");
			$this->cntb_conta_bandalarga 	= VirtexPersiste::factory("cntb_conta_bandalarga");
			$this->cntb_conta_discado		= VirtexPersiste::factory("cntb_conta_discado");
			$this->cntb_conta_email			= VirtexPersiste::factory("cntb_conta_email");
			$this->cntb_cnta_hospedagem		= VirtexPersiste::factory("cntb_conta_hospedagem");
			$this->cntb_endereco_instalacao	= VirtexPersiste::factory("cntb_endereco_instalacao");			
			
			// Classes de preferencias e equipamentos são acessadas internamente p/ minimizar erros de programação.
			$this->preferencias 			= VirtexModelo::factory("preferencias");
			$this->equipamentos 			= VirtexModelo::factory("equipamentos");
			$this->spool					= VirtexModelo::factory("spool");
		
		}
		
		/**
		 * obtemContaPeloId
		 * Retorna as informações de uma conta especificada.
		 */
		public function obtemContaPeloId($id_conta) {
			$info = $this->cntb_conta->obtemUnico(array("id_conta"=>$id_conta));
			if( !count($info) ) {
				return(array());
			}
			
			return($this->obtemContaPeloIdTipo($id_conta,$info["tipo_conta"]));
			
		}
		
		/**
		 * obtemContaPeloUsername()
		 * Retorna a conta baseada no conjunto username, dominio, tipo_conta
		 */
		public function obtemContaPeloUsername($username,$dominio,$tipo_conta) {
			$info = $this->cntb_conta->obtemUnico(array("username" => $username,"dominio" => $dominio, "tipo_conta" => $tipo_conta));
			if( !count($info) ) return array();
			
			return($this->obtemContaPeloIdTipo($info["id_conta"],$tipo_conta));
		}
		
		/**
		 * obtemContaPeloIdTipo()
		 * Retorna a conta (com informações específicas do tipo) com base no id_conta e tipo_conta.
		 */
		public function obtemContaPeloIdTipo($id_conta,$tipo_conta) {
			$tbl = null;
			switch(trim($tipo_conta)) {
				case 'BL':
					$tbl = $this->cntb_conta_bandalarga;
					break;
				case 'D':
					$tbl = $this->cntb_conta_discado;
					break;
				case 'E':
					$tbl = $this->cntb_conta_email;
					break;
				case 'H':
					$tbl = $this->cntb_conta_hospedagem;
					break;
			}
			
			if( !$tbl ) return array();
			
			$filtro = array("id_conta" => $id_conta, "tipo_conta" => $tipo_conta);
			return($tbl->obtemUnico($filtro));
		}
		
		public function obtemContasBandaLarga($id_nas,$status="") {
			$filtro = array("id_nas" => $id_nas);
			if( $status ) {
				$filtro["status"] = $status;
			}
			
			return($this->cntb_conta_bandalarga->obtem($filtro));		
		}
		
		public function obtemContasBandaLargaPeloPOPNAS($id_pop,$id_nas,$status="") {
			$filtro = array();
			if( $id_pop ) {
				$filtro["id_pop"] = $id_pop;
			}
			if( $id_nas ) {
				$filtro["id_nas"] = $id_nas;
			}
			if( $status ) {
				$filtro["status"] = $status;
			}
			return($this->cntb_conta_bandalarga->obtem($filtro));
		}
		
		/**
		 * obtemContasPorContrato()
		 * Retorna as contas de um contrato.
		 */
		public function obtemContasPorContrato($id_cliente_produto,$status="") {
			$filtro = array("id_cliente_produto" => $id_cliente_produto);
			if( $status ) {
				$filtro["status"] = $status;
			}
			return($this->cntb_conta->obtem($filtro));
		}
		
		/**
		 * obtemContasPorCliente()
		 * Retorna as contas de um cliente.
		 */
		public function obtemContasPorCliente($id_cliente,$status="") {
			$filtro = array("id_cliente" => $id_cliente);
			if( $status ) {
				$filtro["status"] = $status;
			}
			return($this->cntb_conta->obtem($filtro));
		}
		
		/**
		 * pesquisaPorConta().
		 * Pesquisa de clientes por conta. Retorna informações do cliente com um array de contas que fizeram,
		 */
		public function pesquisaClientesPorContas($textoPesquisa) {
			// Identificação do Tipo de Pesquisa por Conta
			array($erros);
			$tp = "USER";
			
			// TODO: Indentificar domínio p/ pesquisas em cntb_conta_dominio;
			
			if( MRegex::email($textoPesquisa) ) {
				$tp = "EMAIL";
			} else {
				if( MRegex::ip($textoPesquisa) ) {
					$tp = "IP";
					@list($endIP,$bitsREDE) = explode("/",$textoPesquisa);
					$qr = $bitsREDE ? $textoPesquisa : $textoPesquisa."/24";
					
					try { 
						$r = new MInet($qr);
						if( $bitsREDE ) {
							$textoPesquisa = $r->obtemRede() . "/" . $bitsREDE;
						}
					} catch(MException $e) {
						$erros[] = "Endereço Inválido";
					}
				} else {
					if( MRegex::mac($textoPesquisa) ) {
						$tp = "MAC";
					}
				}
			}
			
			$contas = array();
			$lista_clientes = array();
			
			if( !count($erros) ) {
				$filtroConta = array();
				
				switch($tp) {
					case 'EMAIL':
						@list($usr,$dom) = explode('@',$textoPesquisa);
						$filtroConta["dominio"] = $dom;
						$filtroConta["tipo_conta"] = "E";
					case 'USER':
						$filtroConta["username"] = '%:' . (@$usr ? $usr : $textoPesquisa);
						break;
						
					case 'MAC':
						$contas = $this->cntb_conta_bandalarga->obtemPeloMAC($textoPesquisa);
						break;
					
					case 'IP':
						$contas = $this->cntb_conta_bandalarga->obtemPeloEndereco($textoPesquisa);
						break;
				
				}
				
				if( count($filtroConta) ) {
					$contas = $this->cntb_conta->obtem($filtroConta);
				}
				
				$clientes = array();
				$cntCli = array();
				
				for($i=0;$i<count($contas);$i++) {
					if( !is_array($cntCli[$contas[$i]["id_cliente"]]) ) {
						$cntCli[$contas[$i]["id_cliente"]] = array();
					}
					$cntCli[$contas[$i]["id_cliente"]][] = $contas[$i];
				}
				
				$cltb_cliente = VirtexPersiste::factory("cltb_cliente");
				if( count($cntCli) ) {
					$filtro = array("id_cliente" =>  "in:" . implode("::", array_keys($cntCli)));
					$lista_clientes = $cltb_cliente->obtem($filtro);
					
					for( $i=0;$i<count($lista_clientes);$i++ ) {
						$lista_clientes[$i]["contas"] = $cntCli[$lista_clientes[$i]["id_cliente"]];
					}
				}
				
				
			
			}
			
			return($lista_clientes);
			
		}
		
		
		/**
		 * Cadastra o endereço de instalação.
		 */
		public function cadastraEnderecoInstalacao($id_conta,$endereco,$complemento,$bairro,$id_cidade,$cep,$id_cliente) {
			// TODO: Verificar se já existe outro endereço p/ esta conta. Caso exista tirar o endereço da conta antes de cadastrar a conta.
		
		
			$dados = array(
							"endereco" => $endereco,
							"complemento" => $complemento,
							"bairro" => $bairro,
							"id_cidade" => $id_cidade,
							"cep" => $cep,
							"id_cliente" => $id_cliente
							);
			if( $id_conta ) {
				$dados["id_conta"] = $id_conta;
			}
			return($this->cntb_endereco_instalacao->insere($dados));
		
		}
		
		/**
		 * Obtem o endereço de instalação.
		 * (pega o último endereço atribuído à uma conta específica).
		 */
		public function obtemEnderecoInstalacaoPelaConta($id_conta) {
			$filtro = array("id_conta" => $id_conta);
			return($this->cntb_endereco_instalacao->obtemUnico($filtro,"id_endereco_instalacao DESC"));
		}
		
		/**
		 * Obtem uma lista de endereços de instalação de um cliente.
		 */
		public function obtemListaEnderecosInstalacaoPorCliente($id_cliente) {
			$filtro = array("id_cliente" => $id_cliente);
			return($this->cntb_endereco_instalacao->obtem($filtro));
		}
		
		
		
		
		/**
		 * Cadastra uma conta de Banda Larga.
		 */
		public function cadastraContaBandaLarga($username,$dominio,$senha,$id_cliente,$id_cliente_produto,$status,
										$observacoes,$conta_mestre,
										$id_pop,$id_nas,$upload,$download,$mac,$endereco) {
			
			$nas = $this->equipamentos->obtemNAS($id_nas);
			$senhaCript = MCript::criptSenha($senha);
			
			// Dados comuns
			$dados = array(
							"username"				=> $username,
							"dominio" 				=> $dominio,
							"tipo_conta"				=> 'BL',
							"senha" 				=> $senha,
							"senha_cript"			=> $senhaCript,
							"id_cliente" 			=> $id_cliente,
							"id_cliente_produto" 	=> $id_cliente_produto,
							"status"				=> $status,
							"observacoes"			=> $observacoes,
							"conta_mestre"			=> $conta_mestre,
							
							"id_pop"				=> $id_pop,
							"id_nas"				=> $id_nas,
							"tipo_bandalarga"		=> $nas["tipo_nas"],
							"upload_kbps"			=> $upload,
							"download_kbps"			=> $download
						);
			
			// Registra o MAC somente se recebeu.
			if( $mac ) {
				$dados["mac"] = $mac;
			}

			
			// Verificar o tipo do nas.
			
			$endereco = $endereco ? $endereco : $this->equipamentos->obtemEnderecoDisponivelNAS($id_nas);
			
			if( $nas["tipo_nas"] == "I" ) {
				// Tipo NAS TCP/IP (pegar o ipaddr)
				$dados["rede"] 		= $endereco;
			} else if( $nas["tipo_nas"] == "P" ) {
				// Tipo NAS PPPoE (pegar o ipaddr)
				$dados["ipaddr"] 	= $endereco;
			}
			
			// Insere a conta de Banda Larga.		
			$id_conta = $this->cntb_conta_bandalarga->insere($dados);
			
			// Se o tipo do NAS for tcp/ip ou um nas PPPoE com outro padrão gera instrução p/ spool
			if( $status == "A" && ($nas["tipo_nas"] == "I" || ($nas["tipo_nas"] == "P" && $nas["padrao"] == "O")) ) {
				$this->spool->adicionaContaBandaLarga($id_nas,$id_conta,$username,$endereco,$mac,$upload,$download,$padrao);
			}
			
			return($id_conta);
		
		}
		
		// Funcionalidades comuns
		protected function alteraConta($id_conta,$senha,$status,$observacoes,$conta_mestre) {
			$dados = array("observacoes" => $observacoes,"conta_mestre" => $conta_mestre);

			if( $senha ) {
				$senhaCript = MCript::criptSenha($senha);
				$dados["senha"] = $senha;
				$dados["senhaCript"] = $senhaCript;
			}
			
			if( $status ) {
				$dados["status"] = $status;
			}
			
			$this->cntb_conta->altera($dados,array("id_conta"=>$id_conta));

		}

		/**
		 * Altera uma conta de Banda Larga.
		 */
		public function alteraContaBandaLarga($id_conta,$senha,$status,$observacoes,$conta_mestre,
										$id_pop,$id_nas,$upload,$download,$mac,$endereco,$alterar_endereco = false) {

			// Pegar os dados atuais p/ comparação
			$infoAtual = $this->obtemContaPeloId($id_conta);
			$nasAtual = $this->equipamentos->obtemNAS($infoAtual["id_nas"]);
			$nasNovo = ($infoAtual["id_nas"] != $id_nas ? $this->equipamentos->obtemNAS($id_nas) : $nasAtual);
			
			// Dados p/ básicos.
			$this->alteraConta($id_conta,$senha,$status,$observacoes,$conta_mestre);
			
			if( !$mac ) $mac = null;

			$dados = array("id_pop" => $id_pop, "upload_kbps" => $upload, "download_kbps" => $download, "mac" => $mac, "id_nas" => $id_nas );

			if( $infoAtual["id_nas"] != $id_nas || $alterar_endereco) {
				// Alteração de NAS - Alterar obrigatoriamente o endereço.
				// ou Alteração de endereço.
				
				// Atribuição automática.
				if( !$endereco ) {
					$this->equipamentos->obtemEnderecoDisponivelNAS($id_nas);
				}
				
				if( $nasNovo["tipo_nas"] == "I" ) {
					$dados["rede"] = $endereco;
					$dados["ipaddr"] = null;
				} else {
					$dados["ipaddr"] = $endereco;
					$dados["rede"] = null;
				}

			} else {
				$endereco = $nasAtual["tipo_nas"] == "I" ? $infoAtual["rede"] : $infoAtual["ipaddr"];
			}
			
			/**
			 * Se aplicável envia a instrução de remoção da conta no nas antigo p/ spool.
			 */
			if( $nasAtual["tipo_nas"] == "I" || $nasAtual["padrao"] == "O" && 
				(
					strtoupper(trim($infoAtual["mac"])) != strtoupper(trim($mac)) ||
					$infoAtual["ipaddr"] != $dados["ipaddr"] ||
					$infoAtual["rede"] != $dados["rede"] || 
					$infoAtual["upload_kbps"] != $upload ||
					$infoAtual["download_kbps"] != $download
				)
			) {
				// Enviar instrução p/ spool remover a configuração antiga.
				
				$remEnd = $infoAtual["rede"] ? $infoAtual["rede"] : $infoAtual["ipaddr"];
				
				//echo "<pre>".print_r($remEnd,true)."</pre>";
				//echo "<pre>".print_r($infoAtual,true)."</pre>";
				
				
				
				$this->spool->removeContaBandaLarga($infoAtual["id_nas"],$id_conta,$infoAtual["username"],$remEnd,$infoAtual["mac"],$nasAtual["padrao"]);
			}
			
			/**
			 * Altera os dados da conta
			 */
			$this->cntb_conta_bandalarga->altera($dados,array("id_conta"=>$id_conta));
			
			/**
			 * Envia a instrução de configuração da conta p/ spool.
			 * Se o tipo do NAS for tcp/ip ou um nas PPPoE com outro padrão gera instrução p/ spool.
			 * Somente se a conta tiver ativa, claro!
			 */
			 
			//echo "ENDERECO: $endereco<br>\n";

			if( $status == "A" && ($nasNovo["tipo_nas"] == "I" || ($nasNovo["tipo_nas"] == "P" && $nasNovo["padrao"] == "O")) ) {
				$this->spool->adicionaContaBandaLarga($id_nas,$id_conta,$infoAtual["username"],$endereco,$mac,$upload,$download,$nasNovo["padrao"]);
			}
			
						

			
		}
		
		/**
		 * Cadastra uma conta de Discado.
		 */
		public function cadastraContaDiscado($username,$dominio,$senha,$id_cliente,$id_cliente_produto,$status,
											$observacoes,$conta_mestre,$foneinfo) {
			
			$senhaCript = MCript::criptSenha($senha);
			$dados = array(
							"username"				=> $username,
							"dominio" 				=> $dominio,
							"tipo_conta"				=> 'D',
							"senha" 				=> $senha,
							"senha_cript"			=> $senhaCript,
							"id_cliente" 			=> $id_cliente,
							"id_cliente_produto" 	=> $id_cliente_produto,
							"status"				=> $status,
							"observacoes"			=> $observacoes,
							"conta_mestre"			=> $conta_mestre,
							"foneinfo"				=> $foneinfo
						);
			
			return($this->cntb_conta_discado->insere($dados));
		
		}

		public function alteraContaDiscado($id_conta,$senha,$status,$observacoes,$conta_mestre,$foneinfo) {
			// Altera os dados comuns a todas as contas.
			$this->alteraConta($id_conta,$senha,$status,$observacoes,$conta_mestre);
			
			// Altera os dados específicos do discado.
			$dados = array("foneinfo" => $foneinfo);
			$this->cntb_conta_discado->altera($dados,array("id_conta" => $id_conta));
		}
		
		/**
		 * Cadastra uma conta de Email.
		 */
		public function cadastraContaEmail($username,$dominio,$senha,$id_cliente,$id_cliente_produto,$status,
											$observacoes,$conta_meste,
											$quota,$redirecionar_para="") {

			$email = trim($username) . '@' . trim($dominio);
			$senhaCript = MCript::criptSenha($senha);

			$dados = array(
							"username"				=> $username,
							"dominio" 				=> $dominio,
							"tipo_conta"			=> 'E',
							"senha" 				=> $senha,
							"senha_cript"			=> $senhaCript,
							"id_cliente" 			=> $id_cliente,
							"id_cliente_produto" 	=> $id_cliente_produto,
							"status"				=> $status,
							"observacoes"			=> $observacoes,
							"conta_mestre"			=> $conta_mestre,
							"quota"					=> $quota,
							"email"					=> $email
						);
			
			if( $redirecionar_para ) {
				$dados["redirecionar_para"] = $redirecionar_para;
			}
			
			$id_conta = $this->cntb_conta_email->insere($dados);
			
			$prefGeral = $this->preferencias->obtemPreferenciasGerais();
			$servidor_email = trim($prefGeral["mail_server"]);
			
			// Chamada p/ criação do e-mail via spool.
			$this->spool->adicionaContaEmail($servidor_email,$id_conta,$username,$dominio);

			return($id_conta);
		}

		public function alteraContaEmail($id_conta,$senha,$status,$observacoes,$conta_mestre,$quota) {
			// Altera os dados comuns a todas as contas.
			$this->alteraConta($id_conta,$senha,$status,$observacoes,$conta_mestre);
			
			// Altera os dados específicos do email.
			$dados = array("quota" => $quota);
			$this->cntb_conta_email->altera($dados,array("id_conta" => $id_conta));
		}

		
		/**
		 * Cadastra uma conta de Hospedagem.
		 */
		public function cadastraContaHospedagem($username,$dominio,$senha,$id_cliente,$id_cliente_produto,$status,
												$observacoes,$conta_mestre,
												$tipo_hospedagem, $dominio_hospedagem) {
			
			$prefGeral 		= $this->preferencias->obtemPreferenciasGerais();
			$dominio_padrao	= $prefGeral["dominio_padrao"];
			$hosp_server 	= $prefGeral["hosp_server"];
			$hosp_ns1 		= $prefGeral["hosp_ns1"];
			$hosp_ns2 		= $prefGeral["hosp_ns2"];
			$hosp_uid 		= $prefGeral["hosp_uid"];
			$hosp_gid 		= $prefGeral["hosp_gid"];
			$hosp_base		= $prefGeral["hosp_base"];
			
			$home			= $tipo_hospedagem == "D" ? $hosp_base . "/" . $dominio_hospedagem : $hosp_base . "/" . $dominio_padrao . "/www/usuarios/" . $username;
			$shell			= "/bin/false";		// Shell de Hospedagem será sempre esse.
			
			// Assume-se que a verificação da existência foi feita antes do cadastro da conta.
			if( $tipo_hospedagem == "D" ) {
				$this->preferencias->cadastraDominio($dominio_hospedagem,$id_cliente,'f','A','f');
			}			
			
			$senhaCript = MCript::criptSenha($senha);

			$dados = array(
							"username"				=> $username,
							"dominio" 				=> $dominio_padrao,
							"tipo_conta"				=> 'E',
							"senha" 				=> $senha,
							"senha_cript"			=> $senhaCript,
							"id_cliente" 			=> $id_cliente,
							"id_cliente_produto" 	=> $id_cliente_produto,
							"status"				=> $status,
							"observacoes"			=> $observacoes,
							"conta_mestre"			=> $conta_mestre,
							"quota"					=> $quota,
							"tipo_hospedagem"		=> $tipo_hospedagem,
							"uid"					=> $hosp_uid,
							"gid"					=> $hosp_gid,
							"home"					=> $home,
							"shell"					=> $shell,
							"dominio_hospedagem"	=> $dominio_hospedagem
						);
			
			$id_conta = $this->cntb_conta_hospedagem->insere($dados);
			
			// Chamada na spool p/ criar a conta de hospedagem.
			$this->spool->adicionaContaHospedagem($hosp_server,$id_conta,$username,$dominio_hospedagem,$hosp_ns1,$hosp_ns2);

			return($id_conta);
			
		}

		public function alteraHospedagem($id_conta,$senha,$status,$observacoes,$conta_mestre) {
			// Altera os dados comuns a todas as contas.
			$this->alteraConta($id_conta,$senha,$status,$observacoes,$conta_mestre);
			
			// Não se altera nada além dos dados comuns na hospedagem.
		}
	
	}
	
?>
