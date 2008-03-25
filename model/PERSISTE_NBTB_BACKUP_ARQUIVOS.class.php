<?

	class PERSISTE_NBTB_BACKUP_ARQUIVOS extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_arquivo", "id_backup","arquivo", "conteudo", "status");
			$this->_chave 		= "id_arquivo";
			$this->_ordem 		= "id_arquivo DESC";
			$this->_tabela		= "nbtb_backup_arquivos";
			$this->_sequence	= "nbsq_id_arquivo";
			$this->_filtros		= array("id_backup" => "number", "id_arquivo" => "number");
		}
		
		public function obtemTiposConteudo() {
			return( array("D" => "Dados", "E" => "Estrutura", "C" => "Configurações") );
		}
		
	}
		
?>
