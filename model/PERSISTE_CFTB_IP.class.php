<?

	class PERSISTE_CFTB_IP extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("ipaddr");
			$this->_chave 		= "ipaddr";
			$this->_ordem 		= "ipaddr";
			$this->_tabela		= "cftb_ip";
			$this->_sequence	= "";
			$this->_filtros		= array("ipaddr" => "inet");
		}
		
		public function obtemPeloNAS($id_nas) {
			// Join com CFTB_REFE E CFTB_NAS_REDES
			$sql = "SELECT i.ipaddr,r.id_rede,r.rede,r.tipo_rede FROM cftb_ip i, cftb_rede r INNER JOIN cftb_nas_rede nr ON(r.rede = nr.rede) WHERE nr.id_nas = '".$this->bd->escape($id_nas)."' AND i.ipaddr << r.rede ORDER BY r.rede";
			return($this->bd->obtemRegistros($sql));
			
			
		}
		
		
	}
		
?>
