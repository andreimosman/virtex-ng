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


	public function obtemUltimasRemessas($quantidade) {
			$sql  = "SELECT id_remessa, data_geracao, periodo, data_referencia, id_admin ";
			$sql .= "FROM cbtb_lote_cobranca ";
			$sql .= "ORDER BY id_remessa DESC ";
			$sql .= "LIMIT $quantidade";
	
			return ($this->bd->obtemRegistros($sql));
	}
	
	public function obtemDadosLote($id_remessa) {
			$sql  = "SELECT id_remessa, data_geracao, periodo, data_referencia, id_admin ";
			$sql .= "FROM cbtb_lote_cobranca ";
			$sql .= " WHERE id_remessa='$id_remessa' ";
	
			return ($this->bd->obtemRegistros($sql));
	}


}


?>