<?

class PERSISTE_CBTB_LOTE_COBRANCA extends VirtexPersiste {

	public function __construct($bd=null) {
		parent::__construct();

		$this->_campos 		= array("data_referencia", "data_geracao", "periodo", "id_admin", "id_remessa");
		$this->_chave 		= "id_remessa";
		$this->_ordem 		= "";
		$this->_tabela 		= "cbtb_lote_cobranca";
		$this->_sequence	= "cbsq_id_remessa";
		$this->_filtros		= array("data_referencia" => "date", "data_geracao" => "date", "id_admin" => "number", "id_remessa" => "number");

	}


}


?>