<?

	class PERSISTE_PFTB_MODELO_CONTRATO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_modelo_contrato","descricao","data_upload","disponivel","tipo","padrao","disponivel");
			$this->_chave 		= "id_modelo_contrato";
			$this->_ordem 		= "tipo,descricao";
			$this->_tabela		= "pftb_modelo_contrato";
			$this->_sequence	= "pfsq_id_modelo_contrato";
			$this->_filtros		= array("id_modelo_contrato" => "number", "data_upload" => "timestamp","tipo" => "custom", "padrao" => "bool", "disponivel" => "bool");
		}
		
		public function obtemTiposModelo() {
			return(array("BL" => "Banda Larga", "D" => "Discado", "H" => "Hospedagem"));
		}
		
	}
		
