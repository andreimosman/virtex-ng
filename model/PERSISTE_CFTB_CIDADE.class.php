<?

	class PERSISTE_CFTB_CIDADE extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_cidade", "uf", "cidade", "disponivel");
			$this->_chave 		= "id_cidade";
			$this->_ordem 		= "cidade";
			$this->_tabela		= "cftb_cidade";
			$this->_sequence	= "clsq_id_cidade";
		}
		
		
		public function obtemCidadesDisponiveis() {
			return( $this->obtem(array("disponivel" => true)) );
		}
		
		public function obtemCidadesPorUF($uf) {
			return( $this->obtem(array("UF" => $uf)) );
		}

	}
