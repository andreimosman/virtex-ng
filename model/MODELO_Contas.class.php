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

		protected $lgtb_bloqueio_automatizado;
		protected $lgtb_status_conta;
		
		// Registro de Alteração de IP
		protected $lgtb_alteracao_ip;

		protected $preferencias;
		protected $equipamentos;
		protected $spool;
		protected $evento;


		public function __construct() {
			parent::__construct();
			$this->cntb_conta 					= VirtexPersiste::factory("cntb_conta");
			$this->cntb_conta_bandalarga 		= VirtexPersiste::factory("cntb_conta_bandalarga");
			$this->cntb_conta_discado			= VirtexPersiste::factory("cntb_conta_discado");
			$this->cntb_conta_email				= VirtexPersiste::factory("cntb_conta_email");
			$this->cntb_conta_hospedagem		= VirtexPersiste::factory("cntb_conta_hospedagem");
			$this->cntb_endereco_instalacao		= VirtexPersiste::factory("cntb_endereco_instalacao");

			$this->lgtb_bloqueio_automatizado 	= VirtexPersiste::factory("lgtb_bloqueio_automatizado");
			$this->lgtb_status_conta 			= VirtexPersiste::factory("lgtb_status_conta");
			
			$this->lgtb_alteracao_ip 			= VirtexPersiste::factory("lgtb_alteracao_ip");

			// Classes de preferencias e equipamentos são acessadas internamente p/ minimizar erros de programação.
			$this->preferencias 				= VirtexModelo::factory("preferencias");
			$this->equipamentos 				= VirtexModelo::factory("equipamentos");
			$this->spool						= VirtexModelo::factory("spool");
			$this->eventos						= VirtexModelo::factory("eventos");

		}

		public function obtemContas(){
			return $this->cntb_conta->obtem();
		}

		public function obtemContasCortesia(){
			return $this->cntb_conta->obtem(true);
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
		public function obtemContaPeloUsername($username,$dominio,$tipo_conta='') {
			$filtro = array("username" => $username,"dominio" => $dominio);
			
			if( $tipo_conta ) {
				$filtro["tipo_conta"] = $tipo_conta;
			}
			
			$info = $this->cntb_conta->obtem($filtro);
			if( !count($info) ) return array();
			
			if( count($info) == 1 ) {
				$retorno = $this->obtemContaPeloIdTipo($info[0]["id_conta"],$info[0]["tipo_conta"]);
			} else {
				$retorno = array();
				
				for($i=0;$i<count($info);$i++) {
					$retorno[] = $this->obtemContaPeloIdTipo($info[$i]["id_conta"],$info[$i]["tipo_conta"]);
				}
				
			}
			

			return($retorno);
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
			$conta = $tbl->obtemUnico($filtro);

			if( $tipo_conta == "BL" ) {
				$radius = VirtexModelo::factory("radius");
				
				$conta["psk"] = $conta["mac"]?$radius->obtemPSK($conta["mac"]):"";
			
			}
			return($conta);
		}

		/**
		 * Migra a conta para outro contrato.
		 */
		public function migrarConta($id_conta,$id_cliente_produto) {
			$filtro = array("id_conta" => $id_conta);
			$dados = array("id_cliente_produto" => $id_cliente_produto);
			$this->cntb_conta->altera($dados,$filtro);
		}
		
		public function obtemContasEmail($id_cliente,$status="") {
			$filtro = array("id_cliente" => $id_cliente);
			if( $status ) {
				$filtro["status"] = $status;
			}
			
			return($this->cntb_conta_email->obtem($filtro));
		}
		
		public function obtemContasEmailCanceladas($id_cliente) {
			return($this->obtemContasEmail($id_cliente,"C"));
		}
		

		public function obtemContasBandaLarga($id_nas,$status="",$queryContrato=true) {
			$filtro = array("id_nas" => $id_nas);
			if( $status ) {
				$filtro["status"] = $status;
			}
			
			$cobranca = VirtexModelo::factory("cobranca");
			
			$contas = $this->cntb_conta_bandalarga->obtem($filtro);
			
			
			if( $queryContrato ) {
				for($i=0;$i<count($contas);$i++) {
					$contas[$i]["contrato"] = $cobranca->obtemContratoPeloId($contas[$i]["id_cliente_produto"]);
				}
			}
			
			$radius = VirtexModelo::factory("radius");
			
			for( $i=0;$i<count($contas);$i++ ) {
				$contas[$i]["psk"] = $contas[$i]["mac"]?$radius->obtemPSK($contas[$i]["mac"]):"";
			}

			return($contas);
		}

		public function obtemContasSemMac() {
			$filtro = array("mac" => "null:","status"=>"A");

			return($this->cntb_conta_bandalarga->obtem($filtro));
		}

		public function obtemContasPorBanda($banda) {
			$filtro = array("status"=>"in:A::B","*OR*0" => array("upload_kbps" => $banda, "download_kbps" => $banda));			
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
			$contas = $this->cntb_conta->obtem($filtro);
			
			for($i=0;$i<count($contas);$i++) {
				$contas[$i] = $this->obtemContaPeloIdTipo($contas[$i]["id_conta"],$contas[$i]["tipo_conta"]);
			}
			return($contas);
		}


		/**
		 * obtemQtdeContasPorContrato()
		 * Retorna os emails de um contrato.
		 */
		public function obtemQtdeContasPorContrato($id_cliente_produto,$tipo) {
			return($this->cntb_conta->obtemQuantidadePorTipo($id_cliente_produto, $tipo));
		}

		/**
		 * Retorna a quantidade de contas agrupas por tipo
		 *
		 * @param integer $id_cliente_produto
		 * @return array
		 */
		public function obtemQtdeContasDeCadaTipo(){
			return $this->cntb_conta->obtemQuantidadeContasDeCadaTipo();
		}

		/**
		 * Retorna a quantidade de contas cortesia agrupas por tipo
		 *
		 * @return array
		 */
		public function obtemQtdeContasCortesiaDeCadaTipo(){
			return $this->cntb_conta->obtemQuantidadeContasDeCadaTipo(true);
		}

		/**
		 * Retorna lista de contas cortesia agrupas por tipo
		 *
		 * @return array
		 */
		public function obtemContasCortesiaDeCadaTipo($tipo_conta = false){
			return $this->cntb_conta->obtemContasDeCadaTipo(true,$tipo_conta);
		}


		/**
		 * Retorna uma lista de clientes com contas do tipo informado e
		 * status informado.
		 *
		 * @param integer $tipo_conta
		 * @param char $status
		 * @return array
		 */
		public function obtemClientesPorTipoConta($tipo_conta,$status = "A"){
			$rs =  $this->cntb_conta->obtemClientesPorTipoConta($tipo_conta,$status);
			$return = array();
			foreach($rs as $row){
				$id_cliente = $row["id_cliente"];
				$id_cliente_produto = $row["id_cliente_produto"];

				$return[$id_cliente]["nome_razao"] = $row["nome_razao"];
				$return[$id_cliente]["endereco"] = $row["endereco"];
				$return[$id_cliente]["fone_comercial"] = $row["fone_comercial"];

				$return[$id_cliente]["contas"][$id_cliente_produto]["data_contratacao"] = $row["data_contratacao"];
				$return[$id_cliente]["contas"][$id_cliente_produto]["id_produto"] = $row["id_produto"];
				$return[$id_cliente]["contas"][$id_cliente_produto]["nome_produto"] = $row["nome_produto"];
				$return[$id_cliente]["contas"][$id_cliente_produto]["tipo"] = $row["tipo"];
				$return[$id_cliente]["contas"][$id_cliente_produto]["username"] = $row["username"];
				$return[$id_cliente]["contas"][$id_cliente_produto]["dominio"] = $row["dominio"];
			}
			return $return;
		}

		public function obtemClientesPorProduto($id_produto,$status = "A"){
			$rs =  $this->cntb_conta->obtemClientesPorPorduto($id_produto,$status);
			$return = array();
			foreach($rs as $row){
				$id_cliente = $row["id_cliente"];
				$id_cliente_produto = $row["id_cliente_produto"];

				$return[$id_cliente]["nome_razao"] = $row["nome_razao"];
				$return[$id_cliente]["endereco"] = $row["endereco"];
				$return[$id_cliente]["fone_comercial"] = $row["fone_comercial"];

				$return[$id_cliente]["contas"][$id_cliente_produto]["data_contratacao"] = $row["data_contratacao"];
				$return[$id_cliente]["contas"][$id_cliente_produto]["id_produto"] = $row["id_produto"];
				$return[$id_cliente]["contas"][$id_cliente_produto]["nome_produto"] = $row["nome_produto"];
				$return[$id_cliente]["contas"][$id_cliente_produto]["tipo"] = $row["tipo"];
				$return[$id_cliente]["contas"][$id_cliente_produto]["username"] = $row["username"];
				$return[$id_cliente]["contas"][$id_cliente_produto]["dominio"] = $row["dominio"];
			}
			return $return;
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
		public function pesquisaClientesPorContas($textoPesquisa,$excluirCancelados="") {
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
						$contas = $this->cntb_conta_bandalarga->obtemPeloMAC($textoPesquisa,$excluirCancelados);
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
		public function cadastraEnderecoInstalacao($id_conta,$endereco,$complemento,$bairro,$id_cidade,$cep,$id_condominio_instalacao, $id_bloco_instalacao, $apto_instalacao, $id_cliente) {
			// TODO: Verificar se já existe outro endereço p/ esta conta. Caso exista tirar o endereço da conta antes de cadastrar a conta.


			$dados = array(
							"endereco" => $endereco,
							"complemento" => $complemento,
							"bairro" => $bairro,
							"id_cidade" => $id_cidade,
							"cep" => $cep,
							"id_cliente" => $id_cliente,
							"apto_instalacao" => $apto_instalacao
							);

			if( $dados["id_condominio_instalacao"] ) {
				$dados["id_condominio_instalacao"] = $id_condominio_instalacao;
			}
			if( $dados["id_bloco_instalacao"] ) {
				$dados["id_bloco_instalacao"] = $id_bloco_instalacao;
			}

							
			if( $id_conta ) {
				$dados["id_conta"] = $id_conta;
			}
			return($this->cntb_endereco_instalacao->insere($dados));

		}
		
		public function obtemEnderecoInstalacaoReferenciado($id_conta) {
			return ($this->cntb_endereco_instalacao->obtemEnderecoInstalacaoReferenciado($id_conta));
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
										$observacoes,$conta_mestre, $id_pop,$id_nas,$upload,$download,$mac,$endereco) {

			$nas = $this->equipamentos->obtemNAS($id_nas);
			$senhaCript = MCript::criptSenha($senha);

			// Dados comuns
			$dados = array(
							"username"				=> $username,
							"dominio" 				=> $dominio,
							"tipo_conta"			=> 'BL',
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

			$id_conta = $this->registraAlteracaoIP($id_conta, $dados["rede"], $dados["ipaddr"], "C");


			// Se o tipo do NAS for tcp/ip ou um nas PPPoE com outro padrão gera instrução p/ spool
			if( $status == "A" && ($nas["tipo_nas"] == "I" || ($nas["tipo_nas"] == "P" && $nas["padrao"] == "O")) ) {
				$this->spool->adicionaContaBandaLarga($id_nas,$id_conta,$username,$endereco,$mac,$upload,$download,$padrao);
			}

			return($id_conta);

		}

		// Funcionalidades comuns
		public function alteraConta($id_conta,$senha,$status,$observacoes="",$conta_mestre="") {

			if( $status == "C" || $status == "S" ) {
				$dados = array();
			} else {
				$dados = array("observacoes" => $observacoes,"conta_mestre" => $conta_mestre);

				if( $senha ) {
					$senhaCript = MCript::criptSenha($senha);
					$dados["senha"] = $senha;
					$dados["senha_cript"] = $senhaCript;
				}
			}
			if( $status ) {
				$dados["status"] = $status;
			}
			
			//echo "<pre>"; 
			//print_r($dados);
			//echo "</pre>"; 

			$this->cntb_conta->altera($dados,array("id_conta"=>$id_conta));

		}
		
		
		public function alteraDataAtivacao($id_conta, $data_ativacao) {
			$dados = array("data_ativacao" => $data_ativacao);
			$filtro = array("id_conta" => $id_conta);
			$this->cntb_conta->altera($dados, $filtro);
		}


		public function alteraDataInstalacao($id_conta, $data_instalacao) {
			$dados = array("data_instalacao" => $data_instalacao);
			$filtro = array("id_conta" => $id_conta);
			$this->cntb_conta->altera($dados, $filtro);
		}

		/**
		 * Altera uma conta de Banda Larga.
		 */
		public function alteraContaBandaLarga($id_conta,$senha,$status,$observacoes="",$conta_mestre="",
										$id_pop="",$id_nas="",$upload="",$download="",$mac="",$endereco="",$alterar_endereco = false) {

			$infoAtual = $this->obtemContaPeloId($id_conta);
			
			$nasAtual = $this->equipamentos->obtemNAS($infoAtual["id_nas"]);
			
			if( $infoAtual["id_nas"] != $id_nas ) {
				$id_nas = $id_nas ? $id_nas : $infoAtual["id_nas"];
			}
			
			$nasNovo = ($infoAtual["id_nas"] != $id_nas ? $this->equipamentos->obtemNAS($id_nas) : $nasAtual);


			if( $status != "C") {
				// Pegar os dados atuais p/ comparação
				$infoAtual = $this->obtemContaPeloId($id_conta);
				$nasAtual = $this->equipamentos->obtemNAS($infoAtual["id_nas"]);
				$nasNovo = ($infoAtual["id_nas"] != $id_nas ? $this->equipamentos->obtemNAS($id_nas) : $nasAtual);

				// Dados p/ básicos.
				$this->alteraConta($id_conta,$senha,$status,$observacoes,$conta_mestre);
				
				if( $status != 'S' ) {	// Código não executado em caso de suspensão.
					if( !$mac ) $mac = null;

					$dados = array("id_pop" => $id_pop, "upload_kbps" => $upload, "download_kbps" => $download, "mac" => $mac, "id_nas" => $id_nas );

					if( $infoAtual["id_nas"] != $id_nas || $alterar_endereco) {
						// Alteração de NAS - Alterar obrigatoriamente o endereço.
						// ou Alteração de endereço.

						// Atribuição automática.
						if( !$endereco ) {
							$endereco = $this->equipamentos->obtemEnderecoDisponivelNAS($id_nas);
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
				}
			}

			/**
			 * Se aplicável envia a instrução de remoção da conta no nas antigo p/ spool.
			 */
			if( $status != "A" || $nasAtual["tipo_nas"] == "I" || $nasAtual["padrao"] == "O" &&
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
				$this->spool->removeContaBandaLarga($infoAtual["id_nas"],$id_conta,$infoAtual["username"],$remEnd,$infoAtual["mac"],$nasAtual["padrao"]);
			}
			
			if( $status == "S" ) {
				// Alterações realizadas até o momento são suficientes para suspensão de cliente.
				return;
			}
			
			
			// CANCELAMENTO
			if( $status == "C" ) {
				// Libera os endereços IP e de Rede p/ uso em outro cliente.
				$dados = array("rede" => null, "ipaddr" => null, "status" => "C");
			}
			
			
			if( $dados["rede"] != $infoAtual["rede"] || $dados["ipaddr"] != $infoAtual["ipaddr"] ) {
				// Registra a alteração de IP.
				
				$this->registraAlteracaoIP($id_conta, $dados["rede"], $dados["ipaddr"], "A");
				
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
			
			if( $status == "A" && ($nasNovo["tipo_nas"] == "I" || ($nasNovo["tipo_nas"] == "P" && $nasNovo["padrao"] == "O")) ) {
				// echo "ADD SPOOL<br>\n";
				$this->spool->adicionaContaBandaLarga($nasNovo["id_nas"],$id_conta,$infoAtual["username"],$endereco,$mac,$upload,$download,$nasNovo["padrao"]);
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
							"tipo_conta"			=> 'D',
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
							"tipo_conta"			=> 'H',
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

		public function alteraContaHospedagem($id_conta,$senha,$status,$observacoes='',$conta_mestre='') {
			// Altera os dados comuns a todas as contas.
			$this->alteraConta($id_conta,$senha,$status,$observacoes,$conta_mestre);			
			// Não se altera nada além dos dados comuns na hospedagem.
		}

		public function obtemTiposConta(){
			return $this->cntb_conta->enumTiposConta();
		}

		public function obtemContasFaturasAtrasadas() {
			return $this->cntb_conta->obtemContasFaturasAtrasadas();
		}

		public function gravaLogBloqueioAutomatizado($id_cliente_produto, $tipo, $admin, $auto_obs="", $admin_obs="") {

			$dados = array(	"id_cliente_produto" => $id_cliente_produto,
							"data_hora" => "=now",
							"tipo" => $tipo,
							"admin" => $admin,
							"auto_obs" => $auto_obs,
							"admin_obs" => $admin_obs
						);

			$this->lgtb_bloqueio_automatizado->insere($dados);
		}


		public function gravaLogMudancaStatusConta($id_cliente_produto, $username, $dominio, $tipo_conta, $id_admin, $ip_admin=NULL, $operacao=NULL, $cod_operacao=NULL) {

			$dados = array(
							"id_cliente_produto" => $id_cliente_produto,
							"username" => $username,
							"dominio" => $dominio,
							"tipo_conta" => $tipo_conta,
							"data_hora" => "=now",
							"id_admin" => $id_admin,
							"operacao" => $operacao,
							"cod_operacao" => $cod_operacao
						);
			
			if( $ip_admin ) {
				$dados["ip_admin"] = $ip_admin;
			}

			$this->lgtb_status_conta->insere($dados);

		}


		public function obtemIdClienteProdutoPeloIdConta($id_conta) {
			return $this->cntb_conta->obtemIdClienteProdutoPeloIdConta($id_conta);
		}


		public function obtemContasPeloContrato($id_cliente_produto, $tipo=NULL) {
			return $this->cntb_conta->obtemContasPeloContrato($id_cliente_produto, $tipo);
		}
		
		public function obtemContasBloqueadasPeloContrato($id_cliente_produto, $tipo=NULL) {		
			$retorno = $this->cntb_conta->obtemContasBloqueadasPeloContrato($id_cliente_produto, $tipo);
			return $retorno;
		}	

		public function obtemBloqueiosDesbloqueios($periodo){
			$retorno = $this->cntb_conta->obtemBloqueiosDesbloqueios($periodo);
			//echo "<PRE>";
			//print_r($retorno);
			//echo "</PRE>";
			return $retorno;
		}

		public function obtemBloqueiosDesbloqueiosDetalhes($periodoAnoMes){
			$retorno = $this->cntb_conta->obtemBloqueiosDesbloqueiosDetalhes($periodoAnoMes);
			//echo "<PRE>";
			//print_r($retorno);
			//echo "</PRE>";
			return $retorno;
		}	

		
		/*

		public function eliminaConta($id_conta, $id_admin, $ipaddr) {
			$info = $this->obtemContaPeloId($id_conta);

			if( $info["tipo_conta"] == 'BL' ) {
				// Enviar instrução de remoção p/ spool
				$nas = $this->equipamentos->obtemNAS($info["id_nas"]);
				$remEnd = $info["rede"] ? $info["rede"] : $info["ipaddr"];
				$this->spool->removeContaBandaLarga($info["id_nas"],$id_conta,$info["username"],$remEnd,$info["mac"],$nas["padrao"]);
			}
			
			echo "<PRE>";
			print_r($info);
			echo "</PRE>";

			//$this->cntb_conta->remove(array("id_conta"=>$id_conta));
			
			//$this->eventos->registraEliminacaoConta($id_conta, $info["id_cliente_produto"],$ipaddr, $id_admin, $username="", $endereco="");

		} */
		
		public function registraLogRecuperacao($id_conta,$id_cliente,$id_cliente_produto,$conta_mestre,$id_admin) {
			$lgtb_recuperacao_email = VirtexPersiste::factory('lgtb_recuperacao_email');
			$dados = array("id_conta" => $id_conta, "id_cliente" => $id_cliente, "id_cliente_produto" => $id_cliente_produto, "conta_mestre" => $conta_mestre, "id_admin" => $id_admin);
			return($lgtb_recuperacao_email->insere($dados));
		}
	
		
		public function recuperaEmail($id_conta,$id_cliente_produto,$dadosLogin) {
			$conta = $this->obtemContaPeloId($id_conta);
			
			$this->registraLogRecuperacao($id_conta,$conta["id_cliente"],$conta["id_cliente_produto"],$conta["conta_mestre"],$dadosLogin["id_admin"]);			

			$cobranca = VirtexModelo::factory('cobranca');			
			$cliente_produto = $cobranca->obtemClienteProduto($id_cliente_produto);
			
			$dados = array("id_cliente" => $cliente_produto["id_cliente"], "id_cliente_produto" => $id_cliente_produto, "conta_mestre" => 'f', "status" => "A");
			$filtro = array("id_conta" => $id_conta);
			
			$this->cntb_conta->altera($dados,$filtro);
			
		
		}
		
		
		public function registraAlteracaoIP($id_conta,$rede,$ipaddr,$tipo) {
			// $this->registraAlteracaoIP($id_conta, $dados["rede"], $dados["ipaddr"], "A");
			
			$dados = array("datahora" => "=now", "id_conta" => $id_conta, "tipo" => $tipo);
			
			if( $rede ) {
				$dados["rede"] = $rede;
			}
			
			if( $ipaddr ) {
				$dados["ipaddr"] = $ipaddr;
			}
			
			//echo "<pre>"; 
			//print_r($dados);
			//echo "</pre>"; 
			
			if( $rede || $ipaddr ) {
				return($this->lgtb_alteracao_ip->insere($dados));
			}
			
			return 0;
		
		}
		
		public function obtemHistoricoAlteracaoIP($id_conta) {
			return($this->lgtb_alteracao_ip->obtem(array("id_conta" => $id_conta)));
		}

	}

