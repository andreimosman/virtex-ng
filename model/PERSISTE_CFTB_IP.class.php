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


		public function obtemEnderecoDisponivel($id_nas) {
			$sql  = "SELECT ";
			$sql .= "   i.ipaddr ";
			$sql .= "FROM ";
			$sql .= "   cftb_rede r INNER JOIN cftb_nas_rede nr ON(r.rede = nr.rede) ";
			$sql .= "   INNER JOIN cftb_ip i ON(i.ipaddr << r.rede) ";
			$sql .= "   LEFT OUTER JOIN cntb_conta_bandalarga cbl ON(i.ipaddr = cbl.ipaddr) ";
			$sql .= "WHERE ";
			$sql .= "   nr.id_nas = '".$this->bd->escape($id_nas)."' ";
			$sql .= "   AND cbl.ipaddr is null ";
			$sql .= "ORDER BY ";
			$sql .= "   i.ipaddr DESC ";
			$sql .= "LIMIT ";
			$sql .= "   1 ";
			
			$ip = $this->bd->obtemUnicoRegistro($sql);
			
			return(@$ip["ipaddr"]);
		}

	}
		
?>
