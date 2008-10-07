<?

	class PERSISTE_HDTB_OS extends VirtexPersiste {
	
	
		public static $PERIODO_MANHA = 'M';
		public static $PERIODO_TARDE = 'T';
		public static $PERIODO_NOITE = 'N';
				
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array( "id_chamado", "datainicio", "datafim", "endereco_os", "complemento_os", 
										 "bairro_os", "cidade_os", "agendamento", "periodo", "id_classe"
										);
										
			$this->_chave 		= "id_chamado";
			$this->_tabela		= "hdtb_os";
			$this->_filtros		= array("agendamento" => "date");
		}
		
		
		
		public static function obtemPeriodos() {
			return(array(
					self::$PERIODO_MANHA => "Manhã",
					self::$PERIODO_TARDE => "Tarde",
					self::$PERIODO_NOITE => "Noite"
				));
		}
		
		
	
	}

