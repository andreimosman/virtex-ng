<?

	class PERSISTE_PFTB_PREFERENCIA_HELPDESK extends VirtexPersiste {
	
		public function __construct() {
			parent::__construct();
			
			$this->_campos 		= array("id_preferencia", "limite_tempo_reabertura_chamado");
			$this->_chave		= "id_preferencia";
			$this->_tabela		= "pftb_preferencia_helpdesk";
			$this->_ordem		= "id_preferencia";
		}
		
	}
?>
