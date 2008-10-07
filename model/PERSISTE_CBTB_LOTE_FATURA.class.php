<?

class PERSISTE_CBTB_LOTE_FATURA extends VirtexPersiste {

	public function __construct($bd=null) {
		parent::__construct();

		$this->_campos 		= array("id_remessa", "id_cobranca");
		$this->_chave 		= "";
		$this->_ordem 		= "";
		$this->_tabela 		= "cbtb_lote_fatura";
		$this->_sequence	= "";
		$this->_filtros		= array("id_remessa" => "number", "id_cobranca" => "number");
		
		

	}

}

