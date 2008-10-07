<?

	class PERSISTE_CFTB_NAS_REDE extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("rede", "id_nas");
			$this->_chave 		= "rede,id_nas";
			$this->_ordem 		= "rede";
			$this->_tabela		= "cftb_nas_rede";
			$this->_sequence	= "";
			$this->_filtros		= array("id_nas" => "number", "rede" => "cidr");
		}
		
		public function enderecoPertenceAoNAS($id_nas,$endereco) {

			$sql  = "SELECT ";
			$sql .= "   nr.rede, tipo_nas ";
			$sql .= "FROM ";
			$sql .= "   cftb_nas_rede nr INNER JOIN cftb_nas n ON (n.id_nas = nr.id_nas) ";
			$sql .= "WHERE ";			
			$sql .= "   (nr.rede = '$endereco' ";
			$sql .= "   OR nr.rede >> '$endereco' ) ";
			$sql .= "   AND nr.id_nas = '$id_nas' ";
			
			$r = $this->bd->obtemUnicoRegistro($sql);
			
			if( !count($r) ) 
				$retorno = null;
			else
				$retorno = $r["tipo_nas"] == "I" ? $r["rede"] : $endereco ;
			
			return($retorno);
		
		}
		
	}
		
