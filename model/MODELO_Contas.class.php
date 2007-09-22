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
			$this->cntb_endereco_instacao	= VirtexPersiste::factory("cntb_endereco_instalacao");
			
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
		public function pesquisaClientesPorContas($textoPesquisa,$tipo_pesquisa="C") {
			// 
		}
		
		
		/**
		 * Cadastra o endereço de instalação.
		 */
		public function cadastraEnderecoInstalacao($id_conta,$endereco,$complemento,$bairro,$id_cidade,$id_cliente) {
			// TODO: Verificar se já existe outro endereço p/ esta conta. Caso exista tirar o endereço da conta antes de cadastrar a conta.
		
		
			$dados = array(
							"endereco" => $endereco,
							"complemento" => $complemento,
							"bairro" => $bairro,
							"id_cidade" => $id_cidade,
							"id_cliente" => $id_cliente
							);
			if( $id_conta ) {
				$dados["id_conta"] = $id_conta;
			}
			return($this->cntb_endereco_instalacao->insere($dados));
		
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
							"tipo_conta"				=> 'B',
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
		
	
	
	}
	
	
?>
