<?

	class PERSISTE_CFTB_REDE extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_rede", "rede", "tipo_rede");
			$this->_chave 		= "id_rede";
			$this->_ordem 		= "rede";
			$this->_tabela		= "cftb_rede";
			$this->_sequence	= "cfsq_id_rede";
			$this->_filtros		= array("id_rede" => "number", "rede" => "cidr", "tipo_rede" => "custom");
		}
		
		public function obtemPeloNAS($id_nas) {
			// Join com CFTB_NAS_REDES
			
			$sql = "SELECT r.id_rede,r.rede,r.tipo_rede FROM cftb_rede r INNER JOIN cftb_nas_rede nr ON(r.rede = nr.rede) WHERE nr.id_nas = '".$this->bd->escape($id_nas)."' ORDER BY r.rede";
			return($this->bd->obtemRegistros($sql));
			
			
		}
		
		/**
		 * Verifica se a rede indicada não se sobrepõe (pertence a ou contem) a nenhuma rede cadastrada.
		 */
		public function obtemAssociacoes($rede) {
			$sql = "SELECT r.rede FROM cftb_rede r WHERE rede << '$rede' or rede >> '$rede' or rede = '$rede'";
			return($this->bd->obtemRegistros($sql));
		}
		
	}
		
?>
