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

			$sql  = "SELECT cob.id_remessa, cob.data_geracao, cob.periodo, cob.data_referencia, cob.id_admin, ";
			$sql .= "(SELECT  count(*) FROM cbtb_lote_fatura fat WHERE fat.id_remessa = cob.id_remessa) as qtfaturas ";
			$sql .= "FROM cbtb_lote_cobranca cob ";
			$sql .= "ORDER BY id_remessa DESC ";
			$sql .= "LIMIT $quantidade";

	echo $sql;
			return ($this->bd->obtemRegistros($sql));
	}

	public function obtemDadosLote($id_remessa) {
			$sql  = "SELECT id_remessa, data_geracao, periodo, data_referencia, id_admin ";
			$sql .= "FROM cbtb_lote_cobranca ";
			$sql .= " WHERE id_remessa='$id_remessa' ";

			return ($this->bd->obtemRegistros($sql));
	}


	public function obtemListaFaturasLote($id_lote) {
		$sql  = "SELECT ";
		$sql .= "	fat.data, fat.valor, prd.nome, clt.nome_razao ";
		$sql .= "FROM ";
		$sql .= "	(cbtb_lote_fatura lfat INNER JOIN cbtb_faturas fat ON lfat.id_cobranca = fat.id_cobranca) ";
		$sql .= "		INNER JOIN (cbtb_cliente_produto clpr INNER JOIN prtb_produto prd ON prd.id_produto = clpr.id_produto) ON fat.id_cliente_produto = clpr.id_cliente_produto ";
		$sql .= "			INNER JOIN cltb_cliente clt ON clt.id_cliente = clpr.id_cliente ";
		$sql .= "WHERE ";
		$sql .= "lfat.id_remessa = $id_lote ";

		return($this->bd->obtemRegistros($sql));
	}

}


?>