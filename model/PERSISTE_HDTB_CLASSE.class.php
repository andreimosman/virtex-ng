<?

	class PERSISTE_HDTB_CLASSE extends VirtexPersiste {

		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_classe", "nome", "descricao", "id_classe_pai",
										"usar_em_chamado", "usar_em_os"
										);
			$this->_chave 		= "id_classe";
			$this->_ordem 		= "nome";
			$this->_tabela		= "hdtb_classe";
			$this->_sequence	= "hdsq_id_classe";	
			$this->_filtros		= array("usar_em_chamado" => "boolean", "usar_em_os" => "boolean","id_classe" => "number","id_classe_pai" => "custom");

		}
		
		public function filtraCampo($campo,$valor) {
			switch($campo) {
				case 'id_classe_pai':
					$retorno = $valor ? $valor : null;
					break;
				
				default:
					$retorno = $valor;
					break;

			}
			
			return($retorno);
		}
	
	}
	
?>
