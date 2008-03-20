<?

class PERSISTE_CNTB_CONTA extends VirtexPersiste {

	public static $BANDA_LARGA = "BL";
	public static $DISCADO = "D";
	public static $EMAIL = "E";
	public static $HOSPEDAGEM = "H";

	public function __construct($bd=null) {
		parent::__construct($bd);
		$this->_campos	 	= array("username","dominio","tipo_conta","senha","id_cliente","id_cliente_produto","id_conta",
										"senha_cript", "conta_mestre", "status", "observacoes", "data_instalacao", "data_ativacao"
										);
										$this->_chave 		= "id_conta"; // PK é username,dominio,tipo_conta só que jogando isso pegamos o id conta no retorno do insert
										$this->_ordem 		= "username,dominio,tipo_conta";
										$this->_tabela		= "cntb_conta";
										$this->_sequence	= "cnsq_id_conta";

										$this->_filtros 	= array("status" => "custom", "conta_mestre" => "bool", "data_instalacao" => "date", "data_ativacao" => "date");

	}

	protected function preenchePeriodo($arr,$periodo) {
		$data = date("d/m/Y");
		for($i=0;$i<$periodo;$i++) {

			list($d,$m,$a) = explode("/",$data);

			$chave = "$a-$m";

			if( !@$arr[$chave] ) {
				$arr[$chave] = array("mes"=>$m,"ano"=>$a);
			} else {
				$arr[$chave]["mes"] = $m;
				$arr[$chave]["ano"] = $a;
			}

			$data = MData::adicionaMes($data,-1);

		}

		krsort($arr);

		return($arr);
	}

	public function filtroCampo($campo,$valor) {

		switch($campo) {
			case 'status':
				/**
				 * Status Validos:
				 * 		'A'	=> Ativo (default)
				 *		'B' => Bloqueado
				 * 		'C' => Cancelado
				 *		'S'	=> Suspenso (falta de pagamento)
				 */

				$retorno = $valor ? $valor : 'A';
				break;

			default:
				$retorno = $valor;
		}

		return($retorno);
	}

	public function obtemQuantidadePorTipo($id_cliente_produto,$tipo){
		$sql = "select count(*) as num_contas from cntb_conta where tipo_conta = '$tipo' and id_cliente_produto = $id_cliente_produto";
		return $this->bd->obtemUnicoRegistro($sql);
	}

	/**
	 * Obtem a quantidade de contas agrupas por tipo
	 *
	 * @param boolean $cortesia  retornar apenas as contas cortesias
	 * @return unknown
	 */
	public function obtemQuantidadeContasDeCadaTipo($cortesia = false){
		return $this->_obtemContasDeCadaTipo($cortesia,true);
	}

	/**
	 * Obtem lista de contas agrupoas por tipo
	 *
	 * @param boolean $cortesia  retornar apenas as contas cortesias
	 * @return unknown
	 */
	public function obtemContasDeCadaTipo($cortesia = false, $tipo_conta = false){
		return $this->_obtemContasDeCadaTipo($cortesia,false, $tipo_conta);
	}

	public function obtemClientesPorTipoConta($tipo_conta,$status){
		return $this->_obtemContasDeCadaTipo(false,false,$tipo_conta,false,$status);
	}

	public function obtemClientesPorPorduto($id_produto,$status){
		return $this->_obtemContasDeCadaTipo(false,false,false,$id_produto,$status);
	}

	protected function _obtemContasDeCadaTipo($cortesia = false, $quantidade = false, $tipo_conta = false, $produto = false, $status = false){
		$sql = " SELECT \n";
		if($quantidade){
			$sql.= "	cnt.tipo_conta,\n";
			$sql.= "	count(cnt.*) as num_contas\n";
		} else {
			$sql.= "	cl.id_cliente,\n";
			$sql.= "	cl.nome_razao,\n";
			$sql.= "	cl.endereco,\n";
			$sql.= "	cl.fone_comercial,\n";
			$sql.= "	to_char(ct.data_contratacao,'DD/MM/YYYY') as data_contratacao,\n";  //
			$sql.= "	pr.id_produto,\n";
			$sql.= "	pr.nome as nome_produto,\n";
			$sql.= "	pr.tipo,\n";
			$sql.= "	cnt.username,\n";
			$sql.= "	cnt.dominio,\n";
			$sql.= "	cp.id_cliente_produto, \n";
			$sql.= "	pr.id_produto\n";
		}
		$sql.= " FROM \n";
		$sql.= "	cntb_conta cnt\n";
		$sql.= "	, prtb_produto pr \n";
		$sql.= "	, cltb_cliente cl \n";
		$sql.= "	, cbtb_cliente_produto cp \n";
		$sql.= "	, cbtb_contrato ct \n";
		$sql.= " WHERE \n";
		$sql.= "	cl.id_cliente = cp.id_cliente \n";
		$sql.= "	AND pr.id_produto = cp.id_produto \n";
		$sql.= "	AND ct.id_cliente_produto = cp.id_cliente_produto \n";
		$sql.= "	AND cnt.id_cliente_produto = cp.id_cliente_produto \n";

		$sql.= "	AND cnt.tipo_conta = pr.tipo \n";

		$sql.= "	AND cnt.conta_mestre is true \n";

		if($tipo_conta){
			$sql.= "	AND cnt.tipo_conta = '$tipo_conta' \n";
		}

		if($produto) {
			$sql.= "	AND pr.id_produto = '$produto' \n";
		}

		if($status){
			$sql.= "	AND ct.status = '$status' \n";
		}

		if($cortesia){
			$sql.= "	AND pr.valor = 0 \n";
		}

		if($quantidade){
			$sql.= " GROUP BY \n";
			$sql.= "	cnt.tipo_conta \n";
		} else {
			$sql.= " ORDER BY \n";
			$sql.= "	cl.nome_razao \n";
		}
		return $this->bd->obtemRegistros($sql);
	}


	public function enumTiposConta(){
		return array(
						PERSISTE_CNTB_CONTA::$BANDA_LARGA => "Banda Larga",
						PERSISTE_CNTB_CONTA::$DISCADO => "Acesso Discado",
						PERSISTE_CNTB_CONTA::$EMAIL => "E-Mail",
						PERSISTE_CNTB_CONTA::$HOSPEDAGEM => "Hospedagem",
					);
	}


	public function obtemContasFaturasAtrasadas() {

		$sql  = "SELECT \r\n";
		$sql .= "	cliente.nome_razao \r\n";
		$sql .= "	, produto.nome \r\n";
		$sql .= "	, conta.id_conta \r\n";
		$sql .= "	, conta.id_cliente_produto \r\n";
		$sql .= "	, conta.tipo_conta \r\n";
		$sql .= "	, conta.username as contas \r\n";
		$sql .= "	, count(fatura.*) as faturas_atraso \r\n";
		$sql .= "	, sum(fatura.valor) as fatura_valor \r\n";
		$sql .= "FROM \r\n";
		$sql .= "	cntb_conta as conta \r\n";
		$sql .= "	, cltb_cliente as cliente \r\n";
		$sql .= "	, cbtb_faturas as fatura \r\n";
		$sql .= "	, cbtb_cliente_produto as cliente_produto \r\n";
		$sql .= "	, prtb_produto as produto \r\n";
		$sql .= "WHERE \r\n";
		$sql .= "	conta.id_cliente = cliente.id_cliente \r\n";
		$sql .= "	AND conta.id_cliente_produto = cliente_produto.id_cliente_produto \r\n";
		$sql .= "	AND fatura.id_cliente_produto = fatura.id_cliente_produto \r\n";
		$sql .= "	AND produto.id_produto = cliente_produto.id_produto \r\n";
		$sql .= "	AND conta.tipo_conta NOT LIKE 'E' ";
		$sql .= "	AND ( fatura.reagendamento is null and fatura.data > now() - interval '2 days' - interval '30 days' and fatura.status not in ('P','E','C') \r\n";
		$sql .= "		OR   fatura.reagendamento is null and fatura.data > now() - interval '2 days' - interval '30 days' and fatura.status not in ('P','E','C') ) \r\n";
		$sql .= "GROUP BY cliente.nome_razao, produto.nome, conta.id_conta, conta.tipo_conta, conta.username, conta.id_cliente_produto \r\n";

		return $this->bd->obtemRegistros($sql);

	}

	public function obtemIdClienteProdutoPeloIdConta($id_conta) {

		$sql  = "SELECT \n\r ";
		$sql .= "	id_cliente_produto \n\r ";
		$sql .= "FROM cntb_conta ";
		$sql .= "WHERE id_conta = $id_conta ";

		$retorno = $this->bd->obtemUnicoRegistro($sql);
		return $retorno["id_cliente_produto"];

	}

	public function obtemContasPeloContrato($id_cliente_produto, $tipo=NULL) {

		$sql  = "SELECT ";
		$sql .= "	conta.username, conta.dominio, conta.tipo_conta, conta.senha, ";
		$sql .= "	conta.id_cliente, conta.id_cliente_produto, conta.id_conta, ";
		$sql .= "	conta.senha_cript ,conta.conta_mestre, conta.status, conta.observacoes ";
		$sql .= "FROM ";
		$sql .= "	cbtb_contrato contrato INNER JOIN cntb_conta conta ON conta.id_cliente_produto = contrato.id_cliente_produto ";
		$sql .= "WHERE ";
		$sql .= "	contrato.id_cliente_produto = $id_cliente_produto ";
		$sql .= "	AND conta.status NOT IN ('C','S') ";

		if($tipo) {
			$sql .= "	AND conta.tipo_conta LIKE '$tipo' ";
		}

		return ($this->bd->obtemRegistros($sql));
	}
	
	
	public function obtemContasBloqueadasPeloContrato($id_cliente_produto, $tipo=NULL) {
	
		$sql  = "SELECT ";
		$sql .= "	conta.username, conta.dominio, conta.tipo_conta, conta.senha, ";
		$sql .= "	conta.id_cliente, conta.id_cliente_produto, conta.id_conta, ";
		$sql .= "	conta.senha_cript ,conta.conta_mestre, conta.status, conta.observacoes ";
		$sql .= "FROM ";
		$sql .= "	cbtb_contrato contrato INNER JOIN cntb_conta conta ON conta.id_cliente_produto = contrato.id_cliente_produto ";
		$sql .= "WHERE ";
		$sql .= "	contrato.id_cliente_produto = $id_cliente_produto ";
		$sql .= "	AND conta.status IN ('S') ";
		
		if($tipo) {
			$sql .= "	AND conta.tipo_conta LIKE '$tipo' ";
		}
		
		return $this->bd->obtemRegistros($sql);
	}
	

	public function obtemBloqueiosDesbloqueios($intervalo) {

		$intsql = $intervalo - 1;

			$sql  = "SELECT ";
			$sql .= "	count(*) as num_contratos, ";
			$sql .= "	EXTRACT(year from ba.data_hora) as ano, ";
			$sql .= "	EXTRACT(month from ba.data_hora) as mes ";
			$sql .= "FROM ";
			$sql .= "   lgtb_bloqueio_automatizado ba ";
			$sql .= " WHERE ";
			$sql .= "	ba.data_hora between now() - INTERVAL '$intsql months' AND now() ";
			$sql .= "GROUP BY ano, mes ";
			$sql .= "ORDER BY ano, mes ";

			$retorno = array();

		$bloqueio_desbloqueio = $this->bd->obtemRegistros($sql);

		for($i=0;$i<count($bloqueio_desbloqueio);$i++) {
			if( $bloqueio_desbloqueio[$i]["mes"] < 10 ) $bloqueio_desbloqueio[$i]["mes"] = "0".$bloqueio_desbloqueio[$i]["mes"];
			$retorno[ $bloqueio_desbloqueio[$i]["ano"] . "-" . $bloqueio_desbloqueio[$i]["mes"] ] = $bloqueio_desbloqueio[$i];
		}

		$retorno = $this->preenchePeriodo($retorno,$intervalo);
		//echo $retorno;
		return ($retorno);
		}

		public function obtemBloqueiosDesbloqueiosDetalhes($periodoAnoMes) {

			list($ano,$mes) = explode("-",$periodoAnoMes);
			if( $mes < 10 ) {
				$mes = "0".$mes;
			}
			$data1 = '01/'.$mes."/".$ano;
			$data2 = MData::adicionaMes($data1,1);
			$data1 = MData::ptBR_to_ISO($data1);
			$data2 = MData::ptBR_to_ISO($data2);

			$sql  = "SELECT ";
			$sql .= "	EXTRACT(year from ba.data_hora) as ano, ";
			$sql .= "	EXTRACT(month from ba.data_hora) as mes, ";
			$sql .= "	EXTRACT(day from ba.data_hora) as dia, ";
			$sql .= "	ba.data_hora, ba.id_cliente_produto, p.id_produto, cl.nome_razao, p.nome, ba.admin, con.username, cl.id_cliente ";
			$sql .= "FROM ";
			$sql .= "   lgtb_bloqueio_automatizado ba INNER JOIN cbtb_cliente_produto cp ON (ba.id_cliente_produto = cp.id_cliente_produto) ";
			$sql .= "   INNER JOIN cltb_cliente cl ON (cl.id_cliente = cp.id_cliente) ";
			$sql .= "   INNER JOIN prtb_produto p ON (cp.id_produto = p.id_produto) ";
			$sql .= "   INNER JOIN cntb_conta con ON (cp.id_cliente_produto = con.id_cliente_produto) ";
			$sql .= "WHERE ";
			$sql .= "	ba.data_hora >='$data1' AND ba.data_hora <'$data2' ";
			$sql .= "ORDER BY ba.data_hora ";
			//echo $sql;
			return ($this->bd->obtemRegistros($sql));

		}


}



?>
