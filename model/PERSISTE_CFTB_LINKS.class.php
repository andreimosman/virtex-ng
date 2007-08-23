<?

	class PERSISTE_CFTB_LINKS extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_link","titulo", "url", "target", "descricao");
			$this->_chave 		= "id_link";
			$this->_ordem 		= "titulo";
			$this->_tabela		= "cftb_links";
			$this->_sequence	= "cftb_links_id_link_seq";
		}
		
		public static function obtemTargets() {
			$retorno = array();
			$retorno[] = array("id" => "_self", "nome" => "Mesma Janela");
			$retorno[] = array("id" => "_new", "nome" => "Nova Janela");
			
			return($retorno);
		}
	}

?>
