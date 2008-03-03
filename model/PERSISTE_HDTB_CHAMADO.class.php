<?


	class PERSISTE_HDTB_CHAMADO extends VirtexPersiste {
	
	
		public static $TIPO_OCORRENCIA 		= 'OC';
		public static $TIPO_CHAMADO 		= 'CH';
		public static $TIPO_ORDEM_SERVICO	= 'OS';	/** UTILIZADO PRA VINCULAR UMA OS A UM CHAMADO */
		
		public static $ORIGEM_FONE			= 'F';	/** Telefone */
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
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_chamado", "abertura", "fechamento", "tipo", "criado_por", "id_grupo", 
										"assunto", "descricao", "status", 
										
										"origem", "classificacao",
										"responsavel",
										
										"id_chamado_pai",
										
										"id_cliente", "id_cliente_produto", "id_conta", "id_cobranca",
										
										"id_servidor", "id_nas", "id_pop"
										
										);
			$this->_chave 		= "id_chamado";
			$this->_ordem 		= "datahora DESC";
			$this->_tabela		= "hdtb_chamado";
			$this->_sequence	= "hdsq_id_chamado";	
			//$this->_filtros		= array("ativo" => "boolean");
		}
		
		public static function obtemTipos() {
			return(array(self::$TIPO_OCORRENCIA => "Ocorrência",self::$TIPO_CHAMADO => "Chamado", self::$TIPO_ORDEM_SERVICO => "Ordem de Serviço"));
		}
		
		public static function obtemOrigens() {
			return(array(self::$ORIGEM_FONE => "Telefone", self::$ORIGEM_MAIL => "Email", self::$ORIGEM_CHAT => "Chat", self::$ORIGEM_SISTEMA => "Sistema", self::$ORIGEM_OUTROS => "Outros"));
		}
		
		public static function obtemClassificacoes() {
			return(array(self::$CLASSIFICACAO_INT => "Interno", self::$CLASSIFICACAO_EXT => "Externo"));
		}
		
		public static function obtemStatus() {
			return(array(
					self::$STATUS_NOVO => "Novo",
					self::$STATUS_ABERTO => "Aberto",
					self::$STATUS_PENDENTE => "Pendente"
					self::$STATUS_PENDENTE_CLI => "Pentente Cliente",
					self::$STATUS_FECHADO => "Fechado"
					self::$STATUS_RESOLVIDO => "Resolvido",
				));
		}
		
	
	}

?>
