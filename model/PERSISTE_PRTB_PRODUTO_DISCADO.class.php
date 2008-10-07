<?

	/**
	 * Modelo de Produtos. Especialização: Produto de Acesso Discado
	 */
	class PERSISTE_PRTB_PRODUTO_DISCADO extends PERSISTE_PRTB_PRODUTO {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			
			$this->_campos 		= array_merge($this->_campos, array("franquia_horas", "permitir_duplicidade", "valor_hora_adicional") );			
			$this->_tabela		= "prtb_produto_discado";
			$this->_filtros		= array_merge($this->_filtros, array("franquia_horas" => "number", "permitir_duplicidade" => bool, "valor_hora_adicional" => "number"));
		}
		
		public static function enumPermitirDuplicidade() {
			return(array("t" => "Sim", "f" => "Não"));
		}

	}

