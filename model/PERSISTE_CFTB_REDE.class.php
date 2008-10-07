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
		
		public function obtemPeloNAS($id_nas,$tipo_rede="") {
			// Join com CFTB_NAS_REDES
			
			$sql = "SELECT r.id_rede,r.rede,r.tipo_rede FROM cftb_rede r INNER JOIN cftb_nas_rede nr ON(r.rede = nr.rede) WHERE nr.id_nas = '".$this->bd->escape($id_nas)."' ";
			if( $tipo_rede ) {
				$sql .= " AND r.tipo_rede = '".$tipo_rede."' ";
			}
			
			$sql .= " ORDER BY r.rede";
			return($this->bd->obtemRegistros($sql));
			
			
		}
		
		/**
		 * Verifica se a rede indicada não se sobrepõe (pertence a ou contem) a nenhuma rede cadastrada.
		 */
		public function obtemAssociacoes($rede) {
			$sql = "SELECT r.rede FROM cftb_rede r WHERE rede << '$rede' or rede >> '$rede' or rede = '$rede'";
			return($this->bd->obtemRegistros($sql));
		}
		
		/**
		 * Obtem o próximo endereço de rede disponível para utilização no cliente.
		 * Retorna null caso não existam mais endereços disponíveis
		 */
		public function obtemEnderecoDisponivel($id_nas) {
			$sql  = "SELECT ";
			$sql .= "   rede ";
			$sql .= "FROM  ";
			$sql .= "   (SELECT ";
			$sql .= "      r.rede, cbl.rede as rede_utilizada ";
			$sql .= "   FROM ";
			$sql .= "      cftb_rede r INNER JOIN cftb_nas_rede nr ON(r.rede = nr.rede) ";
			$sql .= "      LEFT OUTER JOIN cntb_conta_bandalarga cbl ON(r.rede = cbl.rede) ";
			$sql .= "   WHERE ";
			$sql .= "      nr.id_nas = '".$this->bd->escape($id_nas)."' ";
			$sql .= "	   AND r.tipo_rede = 'C' ";
			$sql .= "   ORDER BY ";
			$sql .= "      nr.rede DESC ";
			$sql .= "   ) r  ";
			$sql .= "WHERE  ";
			$sql .= "   rede_utilizada is null ";
			$sql .= "LIMIT 1 ";
			
			$rede = $this->bd->obtemUnicoRegistro($sql);
			
			return(@$rede["rede"]);
		}
		
	}
		
