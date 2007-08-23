<?

	class PERSISTE_CNTB_CONTA_BANDALARGA extends PERSISTE_CNTB_CONTA {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array_merge($this->_campos,array("id_pop","tipo_bandalarga","ipaddr","rede", 
																	"upload_kbps", "download_kbps", 
																	"mac", "id_nas", "ip_externo"
																	)
												);

			$this->_tabela		= "cntb_conta_bandalarga";
			$this->_filtros		= array_merge($this->_filtros,array("upload_kbps" => "number", "download_kbps" => "number"));

		}
		
		public function filtroCampo($campo,$valor) {
			$valor = parent::filtroCampo($campo,$valor);
			
			switch($campo) {
				case 'ipaddr':
				case 'mac':
					$retorno = $valor ? $valor : null;
					break;
				default:
					$retorno = $valor;
			}
			
			return($retorno);
		}
		
		public function obtemPeloMAC($mac) {
			$where = "WHERE mac = '".$this->bd->escape($mac)."' ";
			$sql = "SELECT " . implode(",",$this->_campos) . " FROM ". $this->_tabela . " " . $where;
			return $this->bd->obtemRegistros($sql);
		}
		
		public function obtemPeloEndereco($addr) {
			$where = "WHERE ";

			if( strstr($addr,"/") ) {
				$addr = $this->bd->escape($addr);
				$where .= " rede = '$addr' OR rede << '$addr' OR ipaddr << '$addr' ";
			} else {
				$addr = $this->bd->escape($addr);
				$where .= " rede >> '$addr' OR ipaddr = '$addr' ";			
			}
			//$this->obtem($where);
			
			
			$sql = "SELECT " . implode(",",$this->_campos) . " FROM ". $this->_tabela . " " . $where;
			return $this->bd->obtemRegistros($sql);
			
		}
		
	}

?>
