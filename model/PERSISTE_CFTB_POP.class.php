<?

	class PERSISTE_CFTB_POP extends VirtexPersiste {
		
		public function __construct() {
			parent::__construct();
			
			$this->_campos		= array("id_pop","nome","info","tipo","id_pop_ap","status","ipaddr","snmp_rw_com","infoserver",
										"snmp_versao", "snmp_ro_com", "ativar_snmp", "ativar_monitoramento","id_servidor");
			$this->_chave 		= "id_pop";
			$this->_tabela 		= "cftb_pop";
			$this->_sequence 	= "cfsq_id_pop";
			$this->_ordem		= "nome";
			
			$this->_filtros		= array("id_pop_ap" => "custom", "status" => "custom", "ativar_snmp" => "bool",
										"ativar_monitoramento" => "bool");
			
		}
		
		public function obtemPopsDisponiveis() {
			// Retorna os POPs disponiveis que não são backbones
			return($this->obtem(array("status"=>'A',"tipo"=>"!=:B")));
		}
		
		public function filtraCampo($campo,$valor) {
		
			switch($campo) {
				case 'id_pop_ap':
					$retorno = $valor ? $valor : null;
					break;
				case 'status':
					$retorno = $valor ? $valor : 'A';
					break;
				default:
					$retorno = $valor;
			}
			
			return($retorno);
		
		}
	}

?>
