<?
	/**
	 * Modelo de Produto. Especialização: Produto Hospedagem.
	 */
	class PERSISTE_PRTB_PRODUTO_HOSPEDAGEM extends PERSISTE_PRTB_PRODUTO {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			
			$this->_campos 		= array_merge($this->_campos, array("dominio", "franquia_em_mb", "valor_mb_adicional") );
			$this->_tabela		= "prtb_produto_hospedagem";
			$this->_filtros		= array_merge($this->_filtros, array("franquia_em_mb" => "number", "valor_mb_adicional" => "number") );
		}


		public static function enumDominio() {
			return(array("t" => "Sim", "f" => "Não"));
		}


	}

?>
