<?

	/**
	 * Modelo de Helpdesk (camada de negócios)
	 *
	 * - Gerência de Grupos
	 * - Gerência de Administradores de grupos
	 */

	class MODELO_Helpdesk extends VirtexModelo {
	
		protected $hdtb_grupo;
		protected $hdtb_admin_grupo;
		
		protected $hdtb_chamado;
		protected $hdtb_chamado_historico;
		
		protected $hdtb_os;
		
		public function __construct() {
			parent::__construct();
			$this->hdtb_grupo 						= VirtexPersiste::factory("hdtb_grupo");
			$this->hdtb_admin_grupo 				= VirtexPersiste::factory("hdtb_admin_grupo");
			
			$this->hdtb_chamado						= VirtexPersiste::factory("hdtb_chamado");
			$this->hdtb_chamado_historico			= VirtexPersiste::factory("hdtb_chamado_historico");
			
			$this->hdtb_os 							= VirtexPersiste::factory("hdtb_os");
			
		}

		/****************
		 ****	GRUPOS
		 *******************************************/
		
		//Faz o cadastro de um novo grupo
		public function cadastraGrupo($nome, $descricao, $ativo='t', $id_grupo_pai="") {
			$dados = array("nome" => $nome, "descricao" => $descricao, "ativo" =>$ativo);
			$dados["id_grupo_pai"] = ($id_grupo_pai)?$id_grupo_pai:NULL;
			$this->hdtb_grupo->insere($dados);
		}
		
		
		//Faz a alteração de um grupo já cadastrado
		public function alteraGrupo($nome, $descricao, $ativo='t', $id_grupo_pai, $id_grupo) {
			$dados = array("nome" => $nome, "descricao" => $descricao, "ativo" =>$ativo, "id_grupo_pai" =>$id_grupo_pai);
			$dados["id_grupo_pai"] = ($id_grupo_pai)?$id_grupo_pai:NULL;
			$filtro = array("id_grupo" => $id_grupo);
			$this->hdtb_grupo->altera($dados, $filtro);
		}
		
		
		//Faz a listagem de grupos ordenada hierarquicamente
		protected function _obtemGrupos($parentId="",$nivel=0,$filtro=array()) {
			$filtro["id_grupo_pai"] = ($parentId?$parentId:"null:");

			$retorno = array();

			$registros = $this->hdtb_grupo->obtem($filtro);
			for($i=0;$i<count($registros);$i++) {
				$registros[$i]["nivel"] = $nivel;
				$retorno[] = $registros[$i];
				$child = $this->_obtemGrupos($registros[$i]["id_grupo"],$nivel+1);
				for($x=0;$x<count($child);$x++) {
					$retorno[] = $child[$x];
				}
			}
			return($retorno);	
		}
		
		//Faz a listagem de grupos
		public function obtemListaGrupos($filtro="",$parentId="") {
			$filtro = array();			
			return($this->_obtemGrupos($parentId,0,$filtro));
		}
		
		
		//Faz a listagem de grupo junto com o campo de numeração de usuarios
		public function obtemListaGruposComPopulacao() {
			return($this->hdtb_grupo->obtemListaGruposComPopulacao());
		}
		
		
		//Faz a pesquisa de um determinadod grupo por ID
		public function obtemGrupoPeloId($id_grupo) {
			$filtro = array("id_grupo" => $id_grupo);
			return $this->hdtb_grupo->obtemUnico($filtro);
		}
		
		
		//Faz a pesquisa de todos os administradores nao pertencentes ao grupo escolhido
		public function obtemListaAdminForaGrupo($id_grupo) {		
			return $this->hdtb_admin_grupo->obtemListaAdminForaGrupo($id_grupo);
		}
		
		
		//faz o cadastro de um novo administrador em um determinado grupo
		public function cadastraUsuarioGrupo($id_grupo, $id_admin, $admin, $ativo) {
			$dados = array("id_grupo" => $id_grupo, "id_admin" => $id_admin, "nome" =>$nome, "admin" => $admin, "ativo" => $ativo);
			$retorno = $this->hdtb_admin_grupo->insere($dados);
			return $retorno;
		}
		
		
		//faz a alteração de um administrador em um determinado grupo
		public function alteraUsuarioGrupo($id_grupo, $id_admin, $nome, $admin, $ativo) {
			$dados = array("id_grupo" => $id_grupo, "nome" =>$nome, "admin" => $admin, "ativo" => $ativo);
			$filtro = array("id_admin" => $id_admin);
			$retorno = $this->hdtb_admin_grupo->altera($dados, $filtro);
			return $retorno;
		}


		//Retorna a listagem de administradores inclusos no grupo
		public function obtemListaAdminGrupo($id_grupo=NULL, $id_admin=NULL) {
			return $this->hdtb_admin_grupo->obtemListaAdminGrupo($id_grupo, $id_admin);
		}

		//Retorna a listagem de grupos qual o administrador pertence
		public function obtemListaGruposPertencentesAdmin($id_admin, $ativo=NULL) {
			return $this->hdtb_admin_grupo->obtemListaGruposPertencentesAdmin($id_admin, $ativo);
		}

		
		/****************
		 ****	CHAMADOS
		 *******************************************/


		/**
		 * Abertura de chamado ou ocorrência.
		 */
		public function abreChamado($tipo,$criado_por,$id_grupo,$assunto,$descricao,$origem,$classificacao, $prioridade, $responsavel=null,$id_cliente=0,$id_cliente_produto=0,$id_conta=0,$id_cobranca=0,$id_servidor=0,$id_nas=0,$id_pop=0,$id_condominio=0,$id_bloco=0,$id_chamado_pai=null) {
			$dados = array("tipo" => $tipo, "criado_por" => $criado_por, "assunto" => $assunto, "descricao" => $descricao, "origem" => $origem, "classificacao" => $classificacao);
			

			if( $id_cliente ) $dados["id_cliente"] = $id_cliente;
			if( $id_cliente_produto ) $dados["id_cliente_produto"] = $id_cliente_produto;
			if( $id_conta ) $dados["id_conta"] = $id_conta;
			if( $id_cobranca ) $dados["id_cobranca"] = $id_cobranca;
			if( $id_servidor ) $dados["id_servidor"] = $id_servidor;
			if( $id_nas ) $dados["id_nas"] = $id_nas;
			if( $id_pop ) $dados["id_pop"] = $id_pop;
			if( $id_condominio ) $dados["id_condominio"] = $id_condominio;
			if( $id_bloco ) $dados["id_bloco"] = $id_bloco;
			if( $prioridade ) $dados["prioridade"] = $prioridade;
			if( $id_grupo ) $dados["id_grupo"] = $id_grupo;			
			
			// TODO: Registrar a abertura do chamado filho no chamado pai.
			if( $id_chamado_pai ) $dados["id_chamado_pai"] = $id_chamado_pai;
			
			$id_chamado = $this->hdtb_chamado->insere($dados);
			
			unset($dados);
			
			$titulo_historico = ($tipo == 'OC') ? "Registro de ocorrência" : "Abertura do chamado";
			
			$this->adicionaHistoricoChamado($id_chamado, $titulo_historico, "", $criado_por);
			
			if( $responsavel ) {
				$this->alteraResponsavelChamado($id_chamado,$criado_por,$responsavel);
			}
			
			$status_inicial = ($tipo == 'OC')? PERSISTE_HDTB_CHAMADO::$STATUS_FECHADO : PERSISTE_HDTB_CHAMADO::$STATUS_NOVO;
			
			$this->alteraStatus($id_chamado, $status_inicial, $criado_por);
			$this->alteraPrioridade($id_chamado, $prioridade, $criado_por);
			
			if( $id_chamado_pai ) {
			
				$titulo = "Criação de chamado filho";
				$comentarios = "ID: $id_chamado\nTítulo: $assunto";
			
				$this->adicionaHistoricoChamado($id_chamado_pai,$titulo,$comentarios, $criado_por);
			}
			
			
			return($id_chamado);
			
		}
		
		
		/**
		 * Reabre um chamado preaviamente fechadi
		 */
		 public function reabreChamado($id_chamado, $id_admin, $comentarios='') {
		 	$this->adicionaHistoricoChamado($id_chamado, 'Chamado reaberto', $comentarios, $id_admin);
		 	$this->alteraStatus($id_chamado, PERSISTE_HDTB_CHAMADO::$STATUS_ABERTO, $id_admin, '');
		 }
		
		
		/**
		 * Adicionar comentário ao chamado.
		 */
		
		public function adicionaHistoricoChamado($id_chamado,$titulo,$comentarios,$id_admin) { 
			$dados = array("id_chamado" => $id_chamado, "titulo" => $titulo, "id_admin" => $id_admin);
			if($comentarios) {
				$dados["comentarios"] = $comentarios;
			}
			
			return($this->hdtb_chamado_historico->insere($dados));
		}
		
		
		/** 
		 * Alteração do responsável pelo chamado.
		 *
		 *   Altera o responsavel e adiciona um comentário notificando a alteração.
		 *
		 */
		public function alteraResponsavelChamado($id_chamado,$id_admin,$id_novo_admin) {
			$admin = VirtexModelo::factory("administradores");
			$admin_responsavel = $admin->obtemAdminPeloId($id_novo_admin);
			$this->adicionaHistoricoChamado($id_chamado,'Chamado atribuído a "' . $admin_responsavel["admin"] . '" (' . $admin_responsavel["nome"] . ")", "", $id_admin);
			
			$responsavel = $admin_responsavel["id_admin"];
			
			$dados = array("responsavel" => $responsavel);
			$filtro = array("id_chamado" => $id_chamado);
			
			return($this->hdtb_chamado->altera($dados,$filtro));			
		}
		
		
		/** 
		 * Alteração da prioridade pelo chamado.
		 *
		 *   Altera a prioridade e adiciona um comentário notificando a alteração.
		 *
		 */
		public function alteraPrioridade($id_chamado,$nova_prioridade, $id_admin, $comentario="") {
		
			$prioridades = $this->obtemPrioridades();
			$this->adicionaHistoricoChamado($id_chamado,"Prioridade do chamado alterada para '" . $prioridades[$nova_prioridade] . "'", $comentario, $id_admin);
			$dados = array("prioridade" => $nova_prioridade);
			$filtro = array("id_chamado" => $id_chamado);
			
			return($this->hdtb_chamado->altera($dados,$filtro));			
		}
		
		
		/**
		 * Alteração de status do chamado.
		 */
		public function alteraStatus($id_chamado, $status, $id_admin, $comentario="") {

			$chamado = $this->obtemChamadoPeloId($id_chamado);
			
			if( !count($chamado) ) return 0;
			
			$listaStatus = $this->obtemStatusChamado();
			
			$antigo_status = @$listaStatus[$chamado["status"]];
			
			$titulo = "Status alterado de '" . $antigo_status . "' para '" . $listaStatus[$status]. "'";
			
			$this->adicionaHistoricoChamado($id_chamado, $titulo, $comentario, $id_admin);
			
			$dados = array("status" => $status);
			$filtro = array("id_chamado" => $id_chamado);
			
			return($this->hdtb_chamado->altera($dados,$filtro));
			
		}
		
		
		/**
		 * Finaliza o chamado
		 */
		 public function finalizaChamado($id_chamado, $resolvido=true, $id_admin, $comentario="") {
		 	
		 	$status = PERSISTE_HDTB_CHAMADO::$STATUS_RESOLVIDO;
		 	if(!$resolvido) {
		 		$status = PERSISTE_HDTB_CHAMADO::$STATUS_FECHADO;
		 	}
		 	
		 	$titulo="";
		 	
		 	if($status == PERSISTE_HDTB_CHAMADO::$STATUS_RESOLVIDO)
		 		$titulo = "Chamado Resolvido";
		 	else
		 		$titulo = "Chamado Fechado";
		 	
		 	
		 	$this->adicionaHistoricoChamado($id_chamado, $titulo, $comentario, $id_admin);

			$dados = array("status" => $status, "fechamento" => "=now");
		 	$filtro = array("id_chamado" => $id_chamado);	
		 	
		 	return($this->hdtb_chamado->altera($dados,$filtro));
		 }
		

		/**
		 * Lista o histórico de um chamado
		 */
		
		public function obtemHistoricoChamado($id_chamado) { 
			$filtro = array("id_chamado" => $id_chamado);			
			return($this->hdtb_chamado_historico->obtem($filtro));
		}
		
		
		/**
		 * Lista recursiva de chamados. Método utilizado por outros métodos com filtragens variadas.
		 */
		public function obtemArvoreChamados($id_chamado_pai=null,$nivel=0,$filtro=array()) {
			
			if($id_chamado_pai) {
				$filtro["id_chamado_pai"] = $id_chamado_pai;
			}
			
			$chamados = $this->hdtb_chamado->obtem($filtro);
			
			$retorno = array();
			
			for($i=0;$i<count($chamados);$i++) {
				$chamados[$i]["nivel"] = $nivel;
				$retorno[] = $chamados[$i];
				$retorno = array_merge($retorno,$this->obtemChamados($chamados[$i]["id_chamado"]),$nivel + 1);
			}
			
			return($retorno);

		}
		
		
		/**
		 * Lista de chamados pendentes pelo criador
		 */
		public function obtemChamadosPendentesPeloCriador($criado_por) {
			$filtro = array("status" => "!in:OK::F", "criado_por" => $criado_por);
			return($this->hdtb_chamado->obtem($filtro));
		}


		/**
		 * Lista de chamados pendentes pelo Responsável
		 */
		public function obtemChamadosPendentesPeloResponsavel($responsavel) {
			$filtro = array("status" => "!in:OK::F", "responsavel" => $responsavel);
			return($this->hdtb_chamado->obtem($filtro));
		}


		/**
		 * Lista de chamados pendentes pelo grupo
		 */
		public function obtemChamadosPendentesPeloGrupo($id_grupo) {
			$filtro = array("status" => "!in:OK::F", "id_grupo" => $id_grupo);
			return($this->hdtb_chamado->obtem($filtro));
		}
		
		
		/**
		 * Lista de chamados pendentes sem responsavel
		 */
		public function obtemChamadosPendentesSemResponsavel() {
			$filtro = array("status" => "!in:OK::F", "responsavel" => "null");
			return($this->hdtb_chamado->obtem($filtro));
		}


		/**
		 * Lista de chamados pendentes por cliente
		 */
		public function obtemChamadosPendentesPorCliente($id_cliente,$id_cliente_produto=null,$id_conta=null,$id_cobranca=null) {
			$filtro = array("status" => "!in:OK::F", "tipo" => "!in:OS", "id_cliente" => $id_cliente);
			
			if( $id_cliente_produto ) $filtro["id_cliente_produto"] = $id_cliente_produto;
			if( $id_conta ) $filtro["id_conta"] = $id_conta;
			if( $id_cobranca ) $filtro["id_cobranca"] = $id_cobranca;
			
			return($this->hdtb_chamado->obtem($filtro));
		}
		

		/**
		 * Lista de chamados finalizados por cliente
		 */
		public function obtemChamadosFinalizadosPorCliente($id_cliente,$id_cliente_produto=null,$id_conta=null,$id_cobranca=null) {
			$filtro = array("status" => "in:OK::F", "tipo"=>"!in:OS", "id_cliente" => $id_cliente);
			
			if( $id_cliente_produto ) $filtro["id_cliente_produto"] = $id_cliente_produto;
			if( $id_conta ) $filtro["id_conta"] = $id_conta;
			if( $id_cobranca ) $filtro["id_cobranca"] = $id_cobranca;
			
			return($this->hdtb_chamado->obtem($filtro));
		}


		/**
		 * Lista de chamados pendentes por condominio
		 */
		public function obtemChamadosPendentesPorCondominio($id_condominio, $id_bloco=null) {
			$filtro = array("status" => "!in:OK::F", "tipo" => "!in:OS", "id_condominio" => $id_condominio);
			if($id_bloco) $filtro["id_bloco"] = $id_bloco;
			return($this->hdtb_chamado->obtem($filtro));
		}
		

		/**
		 * Lista de chamados finalizados por condominio
		 */
		public function obtemChamadosFinalizadosPorCondominio($id_condominio, $id_bloco=null) {
			$filtro = array("status" => "in:OK::F", "tipo"=>"!in:OS", "id_condominio" => $id_condominio);
			if($id_bloco) $filtro["id_bloco"] = $id_bloco;
			return($this->hdtb_chamado->obtem($filtro));
		}


		/**
		 * Lista de chamados pendentes por equipamento
		 */
		public function obtemChamadosPendentesPorEquipamento($id_servidor=null,$id_nas=null,$id_pop=null) {
			$filtro = array("status" => "!in:OK::F");
			
			if( !$id_servidor && !$id_nas && !$id_po ) return(array());
			
			if( $id_servidor ) $filtro["id_servidor"] = $id_servidor;
			if( $id_nas ) $filtro["id_nas"] = $id_nas;
			if( $id_pop ) $filtro["id_pop"] = $id_pop;
			
			return($this->hdtb_chamado->obtem($filtro));
		}
		
		/**
		 * Obtem o chamado pelo ID.
		 */
		public function obtemChamadoPeloId($id_chamado) {
			return($this->hdtb_chamado->obtemUnico(array("id_chamado"=>$id_chamado)));
		}
		
		
		/**
		 * Obtem a lista de tipos de chamado.
		 */
		public function obtemTiposChamado() {
			return($this->hdtb_chamado->obtemTipos());
		}
		
		
		/**
		 * Obtem a lista das origens do chamado.
		 */
		public function obtemOrigensChamado() {
			return($this->hdtb_chamado->obtemOrigens());
		}
		
		
		/**
		 * Obtem a lista das classificações do chamado.
		 */
		public function obtemClassificacoesChamado() {
			return($this->hdtb_chamado->obtemClassificacoes());
		}
		
		
		/**
		 * Obtem a lista dos status do chamado.
		 */
		public function obtemStatusChamado() {
			return($this->hdtb_chamado->obtemStatus());
		}
		

		/**
		 * Lista de Ordem de serviço pendentes por chamado
		 */
		public function obtemOrdemPedidoPendentesPorChamado($id_chamado) {
			$filtro = array("status" => "!in:OK::F", "tipo" => "OS", "id_chamado_pai" => $id_chamado); 
			return($this->hdtb_chamado->obtem($filtro));
		}		
		

		/**
		 * Lista de Ordem de serviço finalizados por chamado
		 */
		public function obtemOrdemPedidoFinalizadasPorChamado($id_chamado) {
			$filtro = array("status" => "in:OK::F", "tipo" => "OS", "id_chamado_pai" => $id_chamado); 
			return($this->hdtb_chamado->obtem($filtro));
		}
		
		
		/**
		 * Lista de chamados pro periodo
		 */
		public function obtemOcorrenciasPorPeriodo($data_inicial, $data_final) {
			return($this->hdtb_chamado->obtemOcorrenciasPorPeriodo($data_inicial, $data_final));
		}


		/**
		 * Lista de chamados pendentes de todos os grupos que o usuário pertence
		 */
		
		public function obtemQuantidadeChamadosAbertosGruposUsuario($id_admin){
		 	return($this->hdtb_chamado->obtemQuantidadeChamadosAbertosGruposUsuario($id_admin));
		}
		
		
		//public function obtemQuantidadeChamadosAbertosGruposUsuario(){
		//	return($this->hdtb_chamado->obtemQuantidadeChamadosAbertosGruposUsuario());
		//}
		
		
		/****************
		 ****	ORDENS DE SERVIÇO
		 *******************************************/	

		/**
		 * Registra informaçòes da ordem de serviço
		 */
		public function registrarOrdemServico($id_chamado, $endereco_os, $complemento_os, $bairro_os, $cidade_os, $agendamento, $periodo) {
		
			$dados = array( 
				"id_chamado" => $id_chamado, 
				"datainicio" => "=now", 
				"endereco_os" => $endereco_os, 
				"complemento_os" => $complemento_os, 
				"bairro_os" => $bairro_os, 
				"cidade_os" => $cidade_os, 
				"agendamento" => $agendamento,
				"periodo" => $periodo
				);
		
			return($this->hdtb_os->insere($dados));
		
		}
		
		/**
		 * Registra informações da Visita Técnica
		 */
		 
		 public function registrarVisitaTecnica($id_chamado, $data_execucao, $horario_chegada, $horario_saida, $caracterizacao, $icmp_ip, $icmp_media, $icmp_minimo, $ftp_ip, $ftp_media, $ftp_minimo) {
		 	
		 	$dados = array(				
				"data_execucao" 	=> "$data_execucao",
				"horario_chegada" 	=> "$horario_chegada",
				"horario_saida" 	=> "$horario_saida",
				"caracterizacao" 	=> "$caracterizacao",
				"icmp_ip" 			=> "$icmp_ip",
				"icmp_media" 		=> "$icmp_media",
				"icmp_minimo" 		=> "$icmp_minimo",
				"ftp_ip" 			=> "$ftp_ip",
				"ftp_media" 		=> "$ftp_media",
				"ftp_minimo" 		=> "$ftp_minimo"
		 	);
		 	
		 	$filtro = array("id_chamado" => "$id_chamado");
		 	
		 	return($this->hdtb_chamado->altera($dados, $filtro));
		 	
		 }
		
		
		/**
		 * Retorna a ordem de Serviço pelo id do chamado
		 */
		 
		 public function obtemOrdemServicoPeloIdChamado($id_chamado) {
		 	return($this->hdtb_os->obtemUnico(array("id_chamado" => $id_chamado)));
		 }
		
		
		/**
		 * Retorna array com os períodos
		 */
		 
		public function obtemPeriodos() {
			return($this->hdtb_os->obtemPeriodos());			
		}
		
		/**
		 * Retorna array com as priodidades
		 */
		public function obtemPrioridades() {
			$prioridades = array(
				PERSISTE_HDTB_CHAMADO::$PRIORIDADE_NENHUMA		=>	"Nenhuma",
				PERSISTE_HDTB_CHAMADO::$PRIORIDADE_BAIXA		=>	"Baixa", 
				PERSISTE_HDTB_CHAMADO::$PRIORIDADE_MEDIA		=>	"Média",
				PERSISTE_HDTB_CHAMADO::$PRIORIDADE_ALTA			=>	"Alta",
				PERSISTE_HDTB_CHAMADO::$PRIORIDADE_URGENTE		=>	"Urgente"
			);
			
			return $prioridades;
		}
		
		/**
		 * Retorna array com as caracterizações
		 */		
		public function obtemCaracterizacao() {
			return($this->hdtb_chamado->obtemCaracterizacao());
		}
	}

?>
