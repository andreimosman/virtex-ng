<?

	class MODELO_Preferencias extends VirtexModelo {

		protected $cftb_banda;
		protected $cftb_cidade;
		protected $cftb_uf;
		protected $cftb_links;
		
		protected $dominio;

		protected $pftb_preferencia_geral;
		protected $pftb_preferencia_provedor;
		protected $pftb_preferencia_monitoracao;
		protected $pftb_preferencia_cobranca;
		protected $pftb_forma_pagamento;
		protected $pftb_modelo_contrato;
		protected $pftb_preferencia_helpdesk;
		
		protected $cacheCidades;
		
		/**
		 * Construtor do objeto.
		 * Instancía as dependênias.
		 */
		public function __construct() {
			parent::__construct();
			
			$this->cftb_banda = VirtexPersiste::factory("cftb_banda");
			$this->cftb_cidade = VirtexPersiste::factory("cftb_cidade");
			$this->cftb_uf = VirtexPersiste::factory("cftb_uf");
			$this->cftb_links = VirtexPersiste::factory("cftb_links");

			$this->dominio = VirtexPersiste::factory("dominio");

			$this->pftb_preferencia_geral		= VirtexPersiste::factory("pftb_preferencia_geral");
			$this->pftb_preferencia_provedor	= VirtexPersiste::factory("pftb_preferencia_provedor");
			$this->pftb_preferencia_monitoracao = VirtexPersiste::factory("pftb_preferencia_monitoracao");
			$this->pftb_preferencia_cobranca	= VirtexPersiste::factory("pftb_preferencia_cobranca");
			$this->pftb_forma_pagamento			= VirtexPersiste::factory("pftb_forma_pagamento");
			$this->pftb_modelo_contrato			= VirtexPersiste::factory("pftb_modelo_contrato");
			$this->pftb_preferencia_helpdesk	= VirtexPersiste::factory("pftb_preferencia_helpdesk");
			
			$this->cacheCidades 				= array();
		}
		
		/**
		 * Obtem lista de modelos de contrato.
		 */
		public function obtemListaModelosContrato($somenteDisponiveis=false) {
			$filtro = array();
			
			if($somenteDisponiveis) {
				$filtro["disponivel"];
			}
			
			return($this->pftb_modelo_contrato->obtem($filtro));
		}
		
		/**
		 * Obtem o modelo de contrato padrão pra determinado tipo.
		 */
		public function obtemModeloContratoPadrao($tipo) {
			$filtro = array("tipo" => $tipo, "padrao" => "t", "disponivel" => "t");
			return($this->pftb_modelo_contrato->obtemUnico($filtro));
		}
		
		/**
		 * Obtem o modelo de contrato especificado pelo id.
		 */
		public function obtemModeloContrato($id_modelo_contrato) {
			$filtro = array("id_modelo_contrato" => $id_modelo_contrato);
			return($this->pftb_modelo_contrato->obtemUnico($filtro));
		}
		
		public function obtemTiposContrato() {
			return($this->pftb_modelo_contrato->obtemTiposModelo());
		}
		
		public function cadastraModeloContrato($tipo,$descricao,$padrao,$disponivel) {
			if( $padrao == "t" ) {
				$this->pftb_modelo_contrato->altera(array("padrao" => "f"),array("tipo"=>$tipo));
				$disponivel = "t";
			}
			
			return($this->pftb_modelo_contrato->insere(array("descricao"=>$descricao,"disponivel" => $disponivel,"tipo" => $tipo, "padrao" =>$padrao)));
		}
		
		public function atualizaModeloContrato($id_modelo_contrato,$tipo,$descricao,$padrao,$disponivel) {
		
			if( !$tipo ) {
				$info = $this->obtemModeloContrato($id_modelo_contrato);
				$tipo = $info["tipo"];
			}
		
		
			$dados = array("tipo" => $tipo, "descricao" => $descricao);
			
			if( $padrao == 't' ) {
				$this->pftb_modelo_contrato->altera(array("padrao" => "f"),array("tipo"=>$tipo));
				$disponivel = 't';
			}
			
			if( $padrao ) {
				$dados["padrao"] = $padrao;
			}
			
			if( $disponivel ) {
				$dados["disponivel"] = $disponivel;
			}
			
			return($this->pftb_modelo_contrato->altera($dados,array("id_modelo_contrato" => $id_modelo_contrato)));
			
		}
		
		
		/**
		 * Obtem lista de bandas.
		 */
		public function obtemListaBandas() {
			return($this->cftb_banda->obtem());		
		}

		public function obtemBanda($id) {
			$filtro = array("id" => $id);
			return($this->cftb_banda->obtemUnico($filtro));		
		}

		
		/**
		 * Atualiza banda.
		 */
		public function atualizaBanda($id,$valor,$descricao) {
			$dados=array("id" => $valor, "banda" => $descricao);
			return($this->cftb_banda->altera($dados,array("id"=>$id)));
		}
		
		/**
		 * Cadastra banda.
		 */
		public function cadastraBanda($valor,$descricao) {
			$dados=array("id"=>$valor,"banda"=>$descricao);
			return($this->cftb_banda->insere($dados));
		}
		
		/**
		 * Exclui banda.
		 */
		public function excluiBanda($id) {
			return($this->cftb_banda->exclui(array("id" => $id)));
		}
		
		





		/**
		 * Obtem lista de cidades disponíveis.
		 */
		public function obtemListaCidadesDisponiveis() {
			return($this->cftb_cidade->obtemCidadesDisponiveis());
		}
		
		/**
		 * Obtem lista de cidades pelo estado (unidade federativa).
		 */
		public function obtemListaCidadesPorUF($uf) {
			return($this->cftb_cidade->obtemCidadesPorUF($uf));
		}
		
		/**
		 * Obtem uma cidade pelo id.
		 */
		public function obtemCidadePeloID($id) {
			if( !@$this->cacheCidades[$id] ) {
				$this->cacheCidades[$id] = $this->cftb_cidade->obtemUnico(array("id_cidade" => $id));
			}
		
			return(@$this->cacheCidades[$id]);
		}
		
		/**
		 * Busca de cidades pelo nome.
		 */
		public function pesquisaCidadesPeloNome($nome) {
			$nome = preg_replace("/[ÁáÃãÂâÀàÄä]/i","a",$nome);
			$nome = preg_replace("/[ÉéÈèÊêËë]/i","e",$nome);
			$nome = preg_replace("/[ÍíÌìÏï]/i","i",$nome);
			$nome = preg_replace("/[ÓóÕõÒòÖö]/i","o",$nome);
			$nome = preg_replace("/[ÚúÙùÜü]/i","u",$nome);
			$nome = preg_replace("/[ ]{2}/i"," ",$nome);
			
			$nome = str_replace("*","%",$nome);
			$uf="";
			if( strstr($nome,"-") ) {
				list($nome,$uf) = explode("-",$nome,2);
			}
			
			$filtro = array();
			
			if($uf) {
				$filtro["cidade"] = '%:'.$nome;
				$filtro["uf"] = strtoupper($uf);
			} else {
				$filtro = array("cidade" => "%:%".$nome."%");
			}
			
			return($this->cftb_cidade->obtem($filtro));
			

		}
		
		/**
		 * Altera a disponibilidade de uma cidade.
		 */
		public function atualizaDisponibilidadeCidade($id,$disponivel) {
			// echo "aDC: $id, $disponibilidade<br>\n";
			return($this->cftb_cidade->altera(array("disponivel" => $disponivel),array("id_cidade" => $id)));
		}
		
		
		
		/**
		 * Obtem lista de estados (unidades federativas).
		 */
		public function obtemListaUF() {
			return($this->cftb_uf->obtem());
		}
		
		
		
		/**
		 * TODO!!!
		 * Retorna uma lista de domínios.
		 * Caso seja informado o id do cliente o sistema retornará os domínios do provedor + os domínios do cliente.
		 * Se $todos for true o sistema ignorará o id do cliente e retornará TODOS os domínios.
		 */
		public function obtemListaDominios($id_cliente=0,$todos=false) {
			return $this->dominio->obtemListaDominios($id_cliente,$todos);			
		}
		
		/**
		 * Cadastra um domínio.
		 */
		public function cadastraDominio($dominio,$id_cliente,$provedor,$status,$dominio_provedor) {
			$this->verificaClienteProvedor();
			
			// Insere Domínio.
			$dados = array("dominio" => strtolower(trim($dominio)),"id_cliente" => $id_cliente, 
							"provedor" => $provedor,"status" => $status,"dominio_provedor" => $dominio_provedor);
			return($this->dominio->insere($dados));
			
		}
		
		/**
		 * Retorna informações sobre um único domínio.
		 */
		public function obtemDominio($dominio) {
			return($this->dominio->obtemUnico(array("dominio" => trim(strtolower($dominio)))));
		}
		
		/**
		 * Atualiza um dominio.
		 */
		
		public function atualizaDominio($dominio,$dados) {
			if( @$dados["dominio"]) {
				unset($dados["dominio"]);	// Não deixa alterar o dominio em si.
			}
			return($this->dominio->altera($dados,array("dominio" => strtolower(trim($dominio)))));
		}
		
		
		
		/**
		 * Verifica se o cliente provedor (id_cliente=1) existe.
		 * Caso não exista o sistema cria um cliente com id 1 e o nome especificado (nome padrão: "(PROVEDOR)").
		 * Utilizado por todas as funções que dependam (no banco) do id_provedor.
		 */
		protected function verificaClienteProvedor($nome = "") {
			$id_provedor = 1;
			
			// TODO: Verificar se o cliente existe.
			$clientes = VirtexModelo::factory("clientes");			
			// ID 1 reservado para o provedor.
			$info_cliente = $clientes->obtemPeloId($id_provedor);

			if( !count($info_cliente) ) {
				// Inserir o cliente 1 (provedor)
				// TODO: criar opção em MPersiste pra forçar a definiçao do id_cliente sem pegar da sequence.
				//       *** inserindo normalmente e conferindo o ID de retorno do insert pra fazer um update "forçado" para o id_cliente=1
				//echo "INSERT CLIENTE PROVEDOR!!!<br>\n";
				
				$clientes->cadastra(array("nome"=> "", "data_cadastro" => "=now"));
				
			} 

			// Update do nome do cliente para o novo nome.
			// echo "UPDATE CLIENTE PROVEDOR!!!!<br>\n";
			$this->alteraClienteProvedor($nome);
			
		}
		
		protected function alteraClienteProvedor($nome="",$endereco="",$localidade="",$cep="",$cnpj="",$fone="") {
			$id_provedor = 1;
			
			// echo "NOME: $nome<br>\n";
			
			$clientes = VirtexModelo::factory("clientes");
			$nome = trim("(PROVEDOR) " . trim(strtoupper($nome)));			
			
			$dados = array("nome_razao" => $nome,"tipo_pessoa"=>"J","provedor"=>"t");
			
			if( $endereco ) {
				$dados["endereco"] = substr($endereco,0,50);
			}
			
			if( $cep ) {
				$dados["cep"] = substr($cep,0,10);
			}
			
			if( $cnpj ) {
				$dados["cpf_cnpj"] = $cnpj;
			}
			
			if( $fone ) {
				$dados["fone_comercial"] = $fone;
			}
			
			$clientes->altera($id_provedor,$dados);
			unset($clientes);
			
		}
		
		
		/**
		 * Obtem as preferências gerais
		 */
		public function obtemPreferenciasGerais() {
			return($this->pftb_preferencia_geral->obtemUnico(array("id_provedor" => 1)));
		}
		
		/**
		 * Atualiza as preferências gerais
		 */
		public function atualizaPreferenciasGerais($dominio_padrao,$nome,$radius_server,$hosp_server,$hosp_ns1,$hosp_ns2,$hosp_uid,$hosp_gid,$hosp_base,$mail_server,$mail_uid,$mail_gid,$email_base,$pop_host,$smtp_host,$agrupar) {
			$id_provedor = 1;


			// Verifica se o cliente provedor existe, caso não existe cria-o com o nome especificado.
			//$this->verificaClienteProvedor($nome);
			
			$info_dominio = $this->obtemDominio($dominio_padrao);
			
			if( !count($info_dominio) ) {
				// Inserir domínio como domínio provedor.
				// echo "INSERT DOMINIO!!!<br>\n";
				$this->cadastraDominio($dominio_padrao,$id_provedor,"t","A","t");
			} else {
				if( $info_dominio["dominio_provedor"] != "t" || $info_dominio["provedor"] != "t" || $info_dominio["id_cliente"] != 1) {
					// Se o domínio não for do provedor o sistema deverá "puxar" o para o provedor (update)
					$dados_dominio = array("dominio_provedor" => "t", "provedor" => "t", "id_cliente" => 1);
					$this->atualizaDominio($dominio_padrao,$dados_dominio);

					// Tira da memória.
					unset($dados_dominio);
				}
			}

			// Verifica se o cliente provedor existe, caso não existe cria-o com o nome especificado.
			$this->verificaClienteProvedor($nome);


			// Tira da memória.
			unset($info_dominio);			
			
			// Dados.
			$dados = array("id_provedor" => $id_provedor, "dominio_padrao" => $dominio_padrao, "nome" => $nome,
							"radius_server" => $radius_server, "hosp_server" => $hosp_server, "hosp_ns1" => $hosp_ns1,
							"hosp_ns2" => $hosp_ns2, "hosp_uid" => $hosp_uid, "hosp_gid" => $hosp_gid, "hosp_base" => $hosp_base,
							"mail_server" => $mail_server, "mail_uid" => $mail_uid, "mail_gid" => $mail_gid,
							"email_base" => $email_base, "pop_host" => $pop_host, "smtp_host" => $smtp_host,"agrupar" => $agrupar);
			
			$info = $this->obtemPreferenciasGerais();
			
			if( !count($info) ) {
				// Insert
				$retorno = $this->pftb_preferencia_geral->insere($dados);
				//echo "INSERT PREFERENCIA GERAL!!!<br>\n";
			} else {
				// Update
				$retorno = $this->pftb_preferencia_geral->altera($dados,array("id_provedor"=>1));
			}
			
			return($retorno);
			
		}
		
		
		
		
		
		public function obtemPreferenciasProvedor() {
			return($this->pftb_preferencia_provedor->obtemUnico(array("id_provedor" => 1)));
		}
		
		public function atualizaPreferenciasProvedor($endereco,$localidade,$cep,$cnpj,$fone) {
			$id_provedor = 1;
			
			$this->verificaClienteProvedor();
			
			// Pega o nome do banco.
			$info = $this->obtemPreferenciasGerais();
			$nome = @$info["nome"];
			
			unset($info);
			
			$this->alteraClienteProvedor($nome,$endereco,$localidade,$cep,$cnpj,$fone);
			
			$prefProv = $this->obtemPreferenciasProvedor();
			if( !count($prefProv) ) {
				$this->pftb_preferencia_provedor->insere(array("id_provedor" => $id_provedor));
			}
			
			$dados = array("endereco" => $endereco, "localidade" => $localidade, "cep" => $cep, "cnpj" => $cnpj,"fone" => $fone);
			return($this->pftb_preferencia_provedor->altera($dados,array("id_provedor"=>1)));
		
			// echo "ATUALIZANDO PREFERENCIAS PROVEDOR!!!";
		}
		
		
		/**
		 * Obtem as configurações de cobrança
		 */
		
		public function obtemPreferenciasCobranca() {
			return($this->pftb_preferencia_cobranca->obtemUnico(array("id_provedor" => 1)));
		}
		
		public function obtemTiposPagamento() {
			return($this->pftb_preferencia_cobranca->obtemTiposPagamento());
		}
		
		public function atualizaPreferenciasCobranca($tx_juros,$multa,$dia_venc,$pagamento,$carencia,$path_contrato,$observacoes,$enviar_email,$email_remetente,$mensagem_email,$dias_minimo_cobranca) {
			
			if( !$enviar_email ) {
				$enviar_email = 'f';
			}
			
			$dados = array("id_provedor"=>1,"tx_juros" => $tx_juros, "multa" => $multa, "dia_venc" => $dia_venc,"pagamento" => $pagamento, "carencia" => $carencia, "path_contrato" => $path_contrato, "observacoes" => $observacoes, "enviar_email" => $enviar_email, "email_remetente" => $email_remetente, "mensagem_email" => $mensagem_email, "dias_minimo_cobranca" => $dias_minimo_cobranca);
			
			$info = $this->obtemPreferenciasCobranca();
			
			if(!count($info)) {
				// Insere
				$retorno = $this->pftb_preferencia_cobranca->insere($dados);
			} else {
				// Update
				$retorno = $this->pftb_preferencia_cobranca->altera($dados,array("id_provedor"=>1));
			}
			
			return($retorno);
			
		}
		
		public function obtemTiposCobranca() {
			return($this->pftb_forma_pagamento->obtemTiposCobranca());
		}
		
		public function obtemFormasPagamento($disponivel='') {
			$filtro=array();
			if( $disponivel ) {
				$filtro["disponivel"] = $disponivel;	
			}
			return($this->pftb_forma_pagamento->obtem($filtro));
		}
		
		public function obtemFormasPagamentoGerarCobranca() {
			$filtro=array("tipo_cobranca"=>"DA", "tipo_cobranca" =>"BL", "carne" => "false");
			
			return($this->pftb_forma_pagamento->obtem($filtro));
		}
		
		public function obtemFormaPagamento($id_forma_pagamento) {
			$forma = $this->pftb_forma_pagamento->obtemUnico(array("id_forma_pagamento"=>$id_forma_pagamento));
			
			
			// TESTE (dados rogério)
			/**
			$forma["banco"] = "341";
			$forma["agencia"] = "210";
			$forma["conta"] = "71130";
			$forma["dv_conta"] = "5";
			$forma["carteira"] = "112";
			*/
			

			// Link para impressão externa			
			if( $forma["banco"] == "341" && $forma["carteira"] == "112" ) {
				$ag = MBanco::padZero($forma["agencia"],4);
				$cc = MBanco::padZero($forma["conta"],5);
				$dv = MBanco::padZero($forma["dv_conta"],1);
				$ctr = MBanco::padZero($forma["carteira"],3);				
				
				$linkSuf = $ag . $cc . $dv . $ctr;
				
				$forma["linkEmDia"] = "https://ww2.itau.com.br/bloqueto/Valida.aspx?tipo=01&CB=".$linkSuf;					
				$forma["linkAtrazado"] = "https://ww2.itau.com.br/bloqueto/Valida.aspx?tipo=02&CB=".$linkSuf;
				
			}
		
		
			return($forma);
		}
		
		public function obtemTiposFormaPagamento() {
			return($this->pftb_forma_pagamento->obtemTiposCobranca());
		}
		
		public function obtemListaBancos() {
			return($this->pftb_forma_pagamento->obtemBancos());
		}
		
		public function atualizaFormaPagamento($id_forma_pagamento,$disponivel,$nossonumero_inicial,$nossonumero_final) {
			
			$info = $this->obtemFormaPagamento($id_forma_pagamento);
			$atual = (int)$info["nossonumero_atual"];
			if( $atual < $nossonumero_inicial ) {
				$atual = $nossonumero_inicial;
			}

			if( $atual > $nossonumero_final ) {
				// Se passou do fim não está mais disponível.
				$disponivel = "f";
			}
			
			$dados = array("disponivel"=>$disponivel, "nossonumero_inicial" => $nossonumero_inicial, "nossonumero_final" => $nossonumero_final,"nossonumero_atual" => $atual);
			
			return($this->pftb_forma_pagamento->altera($dados,array("id_forma_pagamento"=>$id_forma_pagamento)));
		}
		
		public function cadastraFormaPagamento($dados) {
		
			switch($dados["tipo_cobranca"]) {
				case 'BL':
					//echo "Boleto<br>\n";
					break;
				case 'DA':
					//echo "Débito Automático<br>\n";
					$dados["carne"] = "f";
					break;
				case 'MO':
					//echo "Manual/Outro<br>\n";
					$dados["codigo_banco"] = null;
					break;
				case 'PC':
					$dados["codigo_banco"] = 104;
					$dados["carne"] = "t";
					//echo "PagContas<br>\n";
					break;
			}
			//echo "DADOS:<pre>";
			//print_r($dados);
			//echo "</pre>";
			return($this->pftb_forma_pagamento->insere($dados));
			
		
		}
		
		
		
		
		
		
		
		
		
		/**
		 * Obtem as configurações do monitoramento.
		 */
		public function obtemMonitoramento() {
			$dados = $this->pftb_preferencia_monitoracao->obtemUnico(array("id_provedor" => 1));
			//echo "<pre>";
			//print_r($dados);
			//echo "</pre>";
			
		
			return($dados);
		}
		
		/**
		 * Atualiza as configurações de monitoramento.
		 */
		public function atualizaMonitoramento($dados) {
			// Verifica se o registro existe.
			$monitor = $this->obtemMonitoramento();
			
			if( !count($monitor) ) {
				// $dados = array("id_provedor"=>1,"emails"=>"",a);
				$dados["id_provedor"] = 1;
				$this->pftb_preferencia_monitoracao->insere($dados);
			}
		
			return($this->pftb_preferencia_monitoracao->altera($dados,array("id_provedor"=>1)));
		}
		
		
		
		
		/**
		 * Retorna a lista de links externos.
		 */
		public function obtemListaLinks() {
			return($this->cftb_links->obtem());
		}
		
		/**
		 * Retorna a lista de possíveis targets para os links externos.
		 */
		public function obtemListaTargetsLink() {
			return($this->cftb_links->obtemTargets());
		}
		
		/**
		 * Obtem um link externo pelo id.
		 */
		public function obtemLinkPeloId($id) {
			return($this->cftb_links->obtemUnico(array("id_link"=>$id)));
		}
		
		/**
		 * Atualiza um link externo.
		 */
		public function atualizaLink($id,$titulo,$url,$descricao,$target) {
			$dados=array("titulo" => $titulo,"url" => $url, "descricao" => $descricao, "target" => $target);
			return($this->cftb_links->altera($dados,array("id_link"=>$id)));
		}
		
		/**
		 * Cadastra um link externo.
		 */
		public function cadastraLink($titulo,$url,$descricao,$target) {
			$dados=array("titulo" => $titulo,"url" => $url, "descricao" => $descricao, "target" => $target);
			return($this->cftb_links->insere($dados));
		}
		
		/**
		 * Exclui um link externo.
		 */
		public function excluiLink($id) {
			return($this->cftb_links->exclui(array("id_link" => $id)));
		}
		
		
		/**
		 * Obtem as configuraçòes de helpdesk
		 */
		public function obtemPreferenciasHelpdesk() {
		
			$resultado = $this->pftb_preferencia_helpdesk->obtemUnico(array("id_preferencia" => 1));
			if(!$resultado) { 
				$this->pftb_preferencia_helpdesk->insere(array("id_preferencia" => 1));
				$resultado = $this->pftb_preferencia_helpdesk->obtemUnico(array("id_preferencia" => 1));
			}
			
			return $resultado;
		}
		
		
		/**
		 * Altera as configurações de helpdesk 
		 */
		public function alteraPreferenciasHelpdesk($limite_tempo_reabertura_chamado) {
			$dados = array(	"limite_tempo_reabertura_chamado"=>$limite_tempo_reabertura_chamado);
			return ($this->pftb_preferencia_helpdesk->altera($dados, array("id_preferencia" => 1)));
		}
		
		
	}


?>
