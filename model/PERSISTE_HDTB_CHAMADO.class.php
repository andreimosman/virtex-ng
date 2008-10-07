<?


	class PERSISTE_HDTB_CHAMADO extends VirtexPersiste {
	
	
		public static $TIPO_OCORRENCIA 		= 'OC';
		public static $TIPO_CHAMADO 		= 'CH';
		public static $TIPO_ORDEM_SERVICO	= 'OS';	/** UTILIZADO PRA VINCULAR UMA OS A UM CHAMADO */
		
		public static $ORIGEM_FONE			= 'F';	/** Telefone */
		public static $ORIGEM_OPERADOR		= 'OP';	/** Operador */
		public static $ORIGEM_MAIL			= 'E';	/** Email */
		public static $ORIGEM_CONTATO		= 'CS';	/** Contato direto com atendente in site */
		public static $ORIGEM_CHAT			= 'CH';	/** Chat */
		public static $ORIGEM_SISTEMA		= 'S';	/** Evento gerado via sistema (monitoramento e afins) */
		public static $ORIGEM_OUTROS		= 'O';	/** Outros (não classificado) */
		
		public static $CLASSIFICACAO_INT	= 'IN';
		public static $CLASSIFICACAO_EXT	= 'EX';
		
		public static $STATUS_NOVO			= 'N';
		public static $STATUS_ABERTO		= 'A';
		public static $STATUS_PENDENTE  	= 'P';
		public static $STATUS_PENDENTE_CLI	= 'PC';
		public static $STATUS_RESOLVIDO		= 'OK';
		public static $STATUS_FECHADO		= 'F';
		
		public static $PRIORIDADE_NENHUMA	= 'N';
		public static $PRIORIDADE_BAIXA		= 'B';
		public static $PRIORIDADE_MEDIA		= 'M';
		public static $PRIORIDADE_ALTA		= 'A';
		public static $PRIORIDADE_URGENTE	= 'U';
		
		public static $CARATER_REDE_PROVEDOR 		= 'P';	/** Provedor */
		public static $CARATER_PROBLEMA_CLIENTE		= 'C';	/** Cliente */
		public static $CARATER_NAO_IDENTIFIDADO		= 'N';	/** Não encontrado problema */		
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_chamado", "abertura", "fechamento", "tipo", "criado_por", "id_grupo", "id_chamado_pai", 
										"assunto", "descricao", "status", 
										
										"origem", "classificacao","prioridade", 
										"responsavel",
										
										"id_chamado_pai",
										
										"id_cliente", "id_cliente_produto", "id_conta", "id_cobranca",
										
										"id_servidor", "id_nas", "id_pop", "id_condominio", "id_bloco",
										
										 "icmp_ip", "icmp_media", "icmp_minimo", "ftp_ip", "ftp_media", "ftp_minimo",
										 
										 "horario_saida", "horario_chegada", "caracterizacao", "data_execucao",
										 
										 "id_classe"
										
										);
			$this->_chave 		= "id_chamado";
			$this->_ordem 		= "abertura DESC";
			$this->_tabela		= "hdtb_chamado";
			$this->_sequence	= "hdsq_id_chamado";	
			$this->_filtros		= array("ativo" => "boolean", "icmp_media" => "number", "icmp_minimo" => "number", "ftp_media" => "number", "ftp_minimo" => "number", "data_execucao" => "date");
		}
		
		public static function obtemTipos() {
			return(array(self::$TIPO_OCORRENCIA => "Ocorrência",self::$TIPO_CHAMADO => "Chamado", self::$TIPO_ORDEM_SERVICO => "Ordem de Serviço"));
		}
		
		public static function obtemOrigens() {
			return(array(self::$ORIGEM_FONE => "Telefone", self::$ORIGEM_MAIL => "Email", self::$ORIGEM_CHAT => "Chat", self::$ORIGEM_SISTEMA => "Sistema", self::$ORIGEM_OUTROS => "Outros"));
		}
		
		public static function obtemClassificacoes() {
			return(array(self::$CLASSIFICACAO_INT => "Interno", self::$CLASSIFICACAO_EXT => "Visível para o cliente"));
		}
		
		public static function obtemStatus() {
			return(array(
					self::$STATUS_NOVO => "Novo",
					self::$STATUS_ABERTO => "Aberto",
					self::$STATUS_PENDENTE => "Pendente",
					self::$STATUS_PENDENTE_CLI => "Pentente Cliente",
					self::$STATUS_FECHADO => "Fechado",
					self::$STATUS_RESOLVIDO => "Resolvido",
				));
		}
		

		public function obtemChamadosPorPeriodo($data_inicial, $data_final) {
					
			$sql  = "SELECT ";
			$sql .= "	cha.*, grp.*, agr.*, cli.id_cliente ";
			$sql .= "FROM  ";
			$sql .= "	hdtb_chamado cha ";
			$sql .= "	LEFT OUTER JOIN hdtb_grupo grp ON cha.id_grupo = grp.id_grupo ";
			$sql .= "	LEFT OUTER JOIN hdtb_admin_grupo agr ON agr.id_admin = cha.responsavel ";
			$sql .= "	LEFT OUTER JOIN cltb_cliente cli ON cli.id_cliente = cha.id_cliente ";
			$sql .= "WHERE ";
			$sql .= "	(tipo LIKE '" . self::$TIPO_OCORRENCIA . "' OR tipo = '" .self::$TIPO_CHAMADO. "')";
			$sql .= "	AND abertura BETWEEN '$data_inicial' AND CAST('$data_final' AS DATE) + INTERVAL '1 DAY' ORDER BY ABERTURA DESC ";
			
			return($this->bd->obtemRegistros($sql));
			
		}
		
		public static function obtemCaracterizacao() {
			return(array(
					self::$CARATER_REDE_PROVEDOR 	=> 'Rede Provedor',
					self::$CARATER_PROBLEMA_CLIENTE	=> 'Problema Cliente/Assinante',
					self::$CARATER_NAO_IDENTIFIDADO	=> 'Não foi identificado nenhum problema'
				));
		}
		
		
		public function obtemQuantidadeChamadosAbertosGruposUsuario($id_admin=0){
		
			$sql  = "SELECT count(cha.*) AS chamados ";
			$sql .= "FROM hdtb_chamado cha ";
			$sql .= "	INNER JOIN (SELECT grp.id_grupo FROM hdtb_grupo grp INNER JOIN hdtb_admin_grupo gra ON gra.id_grupo = grp.id_grupo AND gra.id_admin = $id_admin) grps ON grps.id_grupo = cha.id_grupo ";
			$sql .= "WHERE cha.status NOT IN('F', 'OK') ";
			
			return ($this->bd->obtemUnicoRegistro($sql));
		}
	
	}

