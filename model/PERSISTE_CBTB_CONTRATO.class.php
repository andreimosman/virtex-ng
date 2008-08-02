<?

class PERSISTE_CBTB_CONTRATO extends VirtexPersiste {

	public static $ATIVO = "A";
	public static $CANCELADO = "C";
	public static $MIGRADO = "M";

	public function __construct($bd=null) {
		parent::__construct();

		$this->_campos 		= array("id_cliente_produto", "data_contratacao", "vigencia", "data_renovacao", "valor_contrato", "id_cobranca", "status", "tipo_produto", "valor_produto", "num_emails", "quota_por_conta", "tx_instalacao", "comodato", "valor_comodato", "desconto_promo", "periodo_desconto", "hosp_dominio", "hosp_franquia_em_mb", "hosp_valor_mb_adicional", "disc_franquia_horas", "disc_permitir_duplicidade", "disc_valor_hora_adicional", "bl_banda_upload_kbps", "bl_banda_download_kbps", "bl_franquia_trafego_mensal_gb", "bl_valor_trafego_adicional_gb", "cod_banco", "carteira", "agencia", "num_conta", "convenio", "cc_vencimento", "cc_numero", "cc_operadora", "db_banco", "db_agencia", "db_conta", "vencimento", "carencia", "data_alt_status", "id_produto", "nome_produto", "descricao_produto", "disponivel", "vl_email_adicional", "permitir_outros_dominios", "email_anexado", "numero_contas", "valor_estatico",
										"da_cod_banco", "da_carteira", "da_convenio", "da_agencia", "da_num_conta", "bl_cod_banco", "bl_carteira", "bl_convenio", "bl_agencia", "bl_num_conta", "id_forma_pagamento","pagamento","migrado_para","migrado_em","migrado_por","id_modelo_contrato","aceito", "data_aceite", "vl_parcelas_instalacao", "num_parcelas_instalacao");
		$this->_chave 		= "id_cliente_produto";
		$this->_ordem 		= "id_cliente_produto DESC";
		$this->_tabela 		= "cbtb_contrato";
		$this->_sequence	= "";
		$this->_filtros		= array("id_cliente_produto"=>"number", "data_contratacao"=>"date", "vigencia"=>"number", "data_renovacao"=>"date", "valor_contrato"=>"number", "id_cobranca"=>"number", "valor_produto"=>"number", "num_emails"=>"number", "quota_por_conta"=>"number", "tx_instalacao"=>"number", "comodato"=>"bool", "valor_comodato"=>"number", "desconto_promo"=>"number", "periodo_desconto"=>"number", "hosp_dominio"=>"bool", "hosp_franquia_em_mb"=>"number", "hosp_valor_mb_adicional"=>"number", "disc_franquia_horas"=>"number", "disc_permitir_duplicidade"=>"bool", "disc_valor_hora_adicional"=>"number", "bl_banda_upload_kbps"=>"number", "bl_banda_download_kbps"=>"number", "bl_franquia_trafego_mensal_gb"=>"number", "bl_valor_trafego_adicional_gb"=>"number", "cod_banco"=>"number", "agencia"=>"number", "num_conta"=>"number", "convenio"=>"number", "db_banco"=>"number", "db_agencia"=>"number", "db_conta"=>"number", "vencimento"=>"number", "carencia"=>"number", "data_alt_status"=>"date", "id_produto"=>"number",
      									"disponivel"=>"bool", "vl_email_adicional"=>"number", "permitir_outros_dominios"=>"bool", "email_anexado"=>"bool", "numero_contas"=>"number", "valor_estatico"=>"bool",
      									"da_cod_banco"=>"number", "da_convenio"=>"number", "da_agencia"=>"number", "da_num_conta"=>"number", "bl_cod_banco"=>"number", "bl_convenio"=>"number", "bl_agencia"=>"number", "bl_num_conta"=>"number", "id_forma_pagamento"=>"number", "migrado_para"=>"number","migrado_em"=>"date", "id_modelo_contrato"=>"number", "vl_parcelas_instalacao" => "number", "num_parcelas_instalacao" => "number");
		$this->_chave 		= "id_cliente_produto";

	}


	/**
	 * Preenche o período.
	 */
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

	public function obtemAdesoesPorPeriodo($intervalo){
		//TODO: Essa query não está correta e deve ser refeita. O retorno dessa função deve ser a quantidade de novo contratos por tipo de produto

		$intsql = $intervalo - 1;

		$sql = " SELECT \n";
		$sql.= " 	count(*) as num_contratos, \n";
		$sql.= " 	trim(c.tipo_produto) as tipo_produto, \n";
		$sql.= "	EXTRACT( 'month' FROM data_contratacao(c.id_cliente_produto)) as mes, \n";
		$sql.= "	EXTRACT( 'year' FROM data_contratacao(c.id_cliente_produto)) as ano, \n";
		$sql.= " 	tipo_produto \n";
		$sql.= " FROM  \n";
		$sql.= " 	cbtb_contrato c \n";
		$sql.= " WHERE \n";
		$sql.= "	data_contratacao(c.id_cliente_produto) < now() AND data_contratacao(c.id_cliente_produto) > now() - INTERVAL '$intsql months' \n";
		$sql.= " GROUP BY \n";
		$sql.= "	c.tipo_produto, \n";
		$sql.= "	ano, \n";
		$sql.= "	mes \n";
		$sql.= " ORDER BY \n";
		$sql.= "	c.tipo_produto, \n";
		$sql.= "	ano, \n";
		$sql.= "	mes  \n";

		$adesoes = $this->bd->obtemRegistros($sql);
		$retorno = array();


		for($i=0;$i<count($adesoes);$i++) {
			if( $adesoes[$i]["mes"] < 10 ) $adesoes[$i]["mes"] = "0".$adesoes[$i]["mes"];
			$adesoes[$i]["tipo_produto"] = trim($adesoes[$i]["tipo_produto"]);
			$retorno[ $adesoes[$i]["ano"] . "-" . $adesoes[$i]["mes"] ][ $adesoes[$i]["tipo_produto"] ] = $adesoes[$i];
		}

		$retorno = $this->preenchePeriodo($retorno,$intervalo);

		//echo "<pre>";
		//print_r($retorno);
		//echo "</pre>";


		return( $retorno );

	}

	public function obtemCanceladosPorPeriodo($intervalo){

		$intsql = $intervalo - 1;

		$sql = " SELECT \n";
		$sql.= " 	count(*) as num_contratos, \n";
		$sql.= " 	trim(tipo_produto) as tipo_produto, \n";
		$sql.= "	EXTRACT( 'month' FROM data_alt_status) as mes, \n";
		$sql.= "	EXTRACT( 'year' FROM data_alt_status) as ano \n";
		$sql.= " FROM \n";
		$sql.= "	cbtb_contrato \n";
		$sql.= " WHERE \n";
 		$sql.= "	data_alt_status between now() - INTERVAL '$intsql months' AND now() \n";
		$sql.= "	AND status = '".PERSISTE_CBTB_CONTRATO::$CANCELADO."' \n";
		$sql.= " GROUP BY \n";
		$sql.= "	tipo_produto, \n";
		$sql.= "	ano, \n";
		$sql.= "	mes \n";
		$sql.= " ORDER BY \n";
		$sql.= "	tipo_produto, \n";
		$sql.= "	ano, \n";
		$sql.= "	mes  \n";

		$cancelamentos = $this->bd->obtemRegistros($sql);

		$retorno = array();

		for($i=0;$i<count($cancelamentos);$i++) {
			if( $cancelamentos[$i]["mes"] < 10 ) $cancelamentos[$i]["mes"] = "0".$cancelamentos[$i]["mes"];
			$cancelamentos[$i]["tipo_produto"] = trim($cancelamentos[$i]["tipo_produto"]);
			$retorno[ $cancelamentos[$i]["ano"] . "-" . $cancelamentos[$i]["mes"] ][ $cancelamentos[$i]["tipo_produto"] ] = $cancelamentos[$i];
		}

		$retorno = $this->preenchePeriodo($retorno,$intervalo);

		//echo "<pre>";
		//print_r($retorno);
		//echo "</pre>";


		return ($retorno);
	}

	public function obtemEvolucao($periodo) {
		$adesoes = $this->obtemAdesoesPorPeriodo($periodo);
		$cancelamentos = $this->obtemCanceladosPorPeriodo($periodo);

		$retorno = array();

		foreach( $adesoes as $chave => $adesao ) {
			// echo "CHAVE: $chave<br>\n";
			list($retorno[$chave]["ano"],$retorno[$chave]["mes"]) = explode("-",$chave);

			// Banda Larga
			$retorno[$chave]["BL"]["adesoes"] = (int) @$adesoes[$chave]["BL"]["num_contratos"];
			$retorno[$chave]["BL"]["cancelamentos"] = (int) @$cancelamentos[$chave]["BL"]["num_contratos"];
			$retorno[$chave]["BL"]["evolucao"] = $retorno[$chave]["BL"]["adesoes"] - $retorno[$chave]["BL"]["cancelamentos"];

			// Discado
			$retorno[$chave]["D"]["adesoes"] = (int) @$adesoes[$chave]["D"]["num_contratos"];
			$retorno[$chave]["D"]["cancelamentos"] = (int) @$cancelamentos[$chave]["D"]["num_contratos"];
			$retorno[$chave]["D"]["evolucao"] = $retorno[$chave]["D"]["adesoes"] - $retorno[$chave]["D"]["cancelamentos"];

			// Hospedagem
			$retorno[$chave]["H"]["adesoes"] = (int) @$adesoes[$chave]["H"]["num_contratos"];
			$retorno[$chave]["H"]["cancelamentos"] = (int) @$cancelamentos[$chave]["H"]["num_contratos"];
			$retorno[$chave]["H"]["evolucao"] = $retorno[$chave]["H"]["adesoes"] - $retorno[$chave]["H"]["cancelamentos"];

			// TOTAIS
			$retorno[$chave]["total"]["adesoes"] = $retorno[$chave]["BL"]["adesoes"] + $retorno[$chave]["D"]["adesoes"] + $retorno[$chave]["H"]["adesoes"];
			$retorno[$chave]["total"]["cancelamentos"] = $retorno[$chave]["BL"]["cancelamentos"] + $retorno[$chave]["D"]["cancelamentos"] + $retorno[$chave]["H"]["cancelamentos"];
			$retorno[$chave]["total"]["evolucao"] = $retorno[$chave]["total"]["adesoes"] - $retorno[$chave]["total"]["cancelamentos"];

		}

		//echo "<pre>";
		//print_r($retorno);
		//echo "</pre>";

		return($retorno);

	}


	public function obtemContratosFaturasAtrasadas() {


		$sql  = "SELECT ";
		$sql .= "	cliente.nome_razao  ";
		$sql .= "	, produto.nome  ";
		$sql .= "	, contrato.id_cliente_produto  ";
		$sql .= "	, contrato.tipo_produto  ";
		$sql .= "	, (SELECT count(nconta.*) FROM cntb_conta nconta WHERE nconta.id_cliente_produto = contrato.id_cliente_produto AND nconta.tipo_conta = contrato.tipo_produto) as contas ";
		$sql .= "	, (SELECT count(faturas.*)  ";
		$sql .= "		FROM cbtb_faturas faturas  ";
		$sql .= "			WHERE 	faturas.reagendamento is null and faturas.data > now() - interval '10 days' - interval '30 days'  ";
		$sql .= "				and faturas.status not in ('P','E','C') OR faturas.reagendamento is null and faturas.data > now() - interval '2 days' - interval '30 days' and faturas.status not in ('P','E','C')  ";
		$sql .= "				AND faturas.id_cliente_produto = contrato.id_cliente_produto) as faturas_atraso ";
		$sql .= "	, (SELECT sum(faturas.valor)  ";
		$sql .= "		FROM cbtb_faturas faturas  ";
		$sql .= "			WHERE 	faturas.reagendamento is null and faturas.data > now() - interval '10 days' - interval '30 days'  ";
		$sql .= "				and faturas.status not in ('P','E','C') OR faturas.reagendamento is null and faturas.data > now() - interval '2 days' - interval '30 days' and faturas.status not in ('P','E','C')  ";
		$sql .= "				AND faturas.id_cliente_produto = contrato.id_cliente_produto) as fatura_valor ";
		$sql .= "FROM 	cntb_conta as conta  ";
		$sql .= "	, cbtb_contrato as contrato  ";
		$sql .= "	, cltb_cliente as cliente  ";
		$sql .= "	, cbtb_cliente_produto as cliente_produto  ";
		$sql .= "	, prtb_produto as produto  ";
		$sql .= "WHERE  ";
		$sql .= "	conta.id_cliente = cliente.id_cliente  ";
		$sql .= "	AND contrato.id_cliente_produto = cliente_produto.id_cliente_produto  ";
		$sql .= "	AND conta.id_cliente_produto = contrato.id_cliente_produto  ";
		$sql .= "	AND produto.id_produto = cliente_produto.id_produto  ";
		$sql .= "	AND conta.status NOT IN ('C', 'S')  ";
		$sql .= "GROUP BY  ";
		$sql .= "	contrato.tipo_produto ";
		$sql .= "	, cliente.nome_razao ";
		$sql .= "	, produto.nome ";
		$sql .= "	, contrato.id_cliente_produto ";

		return $this->bd->obtemRegistros($sql);

	}

	public function obtemContratosFaturasAtrasadasBloqueios($carencia,$tempo_banco=2,$id_cliente_produto="",$contasAtivas = true, $inadimplentes = false) {

	$sql .= "SELECT  ";
	$sql .= "	cl.id_cliente, cl.nome_razao, cl.id_cidade, cid.cidade, f.id_cliente_produto, p.nome as produto, count(f.id_cobranca) as faturas,  ";
	$sql .= "	p.tipo, sum(CASE WHEN f.valor is NULL then 0 else f.valor END) - sum(CASE WHEN f.desconto is null THEN 0 ELSE f.desconto END) + sum(CASE WHEN f.acrescimo is null THEN 0 ELSE f.acrescimo END) as valor_devido, cnt.num_contas ";
	$sql .= "FROM  ";
	$sql .= "   cbtb_faturas f ";
	$sql .= "	INNER JOIN cbtb_cliente_produto cp ON f.id_cliente_produto = cp.id_cliente_produto  ";
	$sql .= "	INNER JOIN cltb_cliente cl ON cp.id_cliente = cl.id_cliente  ";
	$sql .= "	INNER JOIN cftb_cidade cid ON cl.id_cidade = cid.id_cidade ";
	$sql .= "	INNER JOIN prtb_produto p ON cp.id_produto = p.id_produto  ";
	$sql .= "	INNER JOIN ( ";
	$sql .= "		SELECT  ";
	$sql .= "			cp.id_cliente_produto, count(cn.id_conta) as num_contas ";
	$sql .= "		FROM  ";
	$sql .= "			cntb_conta cn  ";
	$sql .= "			INNER JOIN cbtb_cliente_produto cp ON cn.id_cliente_produto = cp.id_cliente_produto  ";
	$sql .= "			INNER JOIN prtb_produto p ON cp.id_produto = p.id_produto ";
	$sql .= "		WHERE  ";
	$sql .= "			cn.tipo_conta = p.tipo ";

	if( $inadimplentes ) {
		$sql .= "   AND cn.status = 'S' ";
	} else {
		if( $contasAtivas ) {
			$sql .= "	AND cn.status not in ('C','S')  ";
		} else {
			$sql .= "	AND cn.status != 'C'  ";
		}
	}

	$sql .= "		GROUP BY ";
	$sql .= "			cp.id_cliente_produto ";
	$sql .= "	) cnt ON cnt.id_cliente_produto = cp.id_cliente_produto ";
	$sql .= "WHERE  ";
	//$sql .= "	(f.reagendamento is null AND f.data < now() + interval '5 days' ) OR (f.reagendamento is not null AND f.data < now() + interval '5 days' + interval '20 days' ) AND f.status not in ('P','E','C')  ";
	
	
	if( !$tempo_banco ) $tempo_banco = 2;
	
	$diasSemReagendamento = $carencia + $tempo_banco;
	$diasComReagendamento = $tempo_banco;
	
	$sql .= "  ((f.reagendamento is null AND f.data < now() - interval '$diasSemReagendamento days' ) OR (f.reagendamento is not null AND f.reagendamento < now() - interval '$diasComReagendamento days')) AND f.status not in ('P','E','C') ";
	
	if( $id_cliente_produto ) {
		$sql .= " AND f.id_cliente_produto = $id_cliente_produto ";
	}
	
	$sql .= " AND f.valor > 0 "; // Desconsidera faturas cortesia
	
	$sql .= "GROUP BY  ";
	$sql .= "	f.id_cliente_produto, cl.id_cliente, cl.nome_razao, cl.id_cidade, cid.cidade, p.nome, p.tipo, cnt.num_contas ";
	
	$sql .= "ORDER BY faturas DESC ";
	
	// echo "SQL: $sql\n";
	

	// echo $sql;

		/*$sql  = "SELECT ";
		$sql .= "	cliente.nome_razao  ";
		$sql .= "	, produto.nome  ";
		$sql .= "	, contrato.id_cliente_produto  ";
		$sql .= "	, contrato.tipo_produto  ";
		$sql .= "	, (SELECT count(nconta.*) FROM cntb_conta nconta WHERE nconta.id_cliente_produto = contrato.id_cliente_produto AND nconta.tipo_conta = contrato.tipo_produto) as contas ";
		$sql .= "	, (SELECT count(faturas.*)  ";
		$sql .= "		FROM cbtb_faturas faturas  ";
		$sql .= "			WHERE 	faturas.reagendamento is null and faturas.data > now() - interval '10 days' - interval '30 days'  ";
		$sql .= "				and faturas.status not in ('P','E','C') OR faturas.reagendamento is null and faturas.data > now() - interval '2 days' - interval '30 days' and faturas.status not in ('P','E','C')  ";
		$sql .= "				AND f.id_cliente_produto = contrato.id_cliente_produto) as faturas_atraso ";
		$sql .= "	, (SELECT sum(faturas.valor)  ";
		$sql .= "		FROM cbtb_faturas faturas  ";
		$sql .= "			WHERE 	faturas.reagendamento is null and faturas.data > now() - interval '10 days' - interval '30 days'  ";
		$sql .= "				and faturas.status not in ('P','E','C') OR faturas.reagendamento is null and faturas.data > now() - interval '2 days' - interval '30 days' and faturas.status not in ('P','E','C')  ";
		$sql .= "				AND faturas.id_cliente_produto = co.id_cliente_produto) as fatura_valor ";
		$sql .= "FROM 	cntb_conta as conta  ";
		$sql .= "	, cbtb_contrato as contrato  ";
		$sql .= "	, cltb_cliente as cliente  ";
		$sql .= "	, cbtb_cliente_produto as cliente_produto  ";
		$sql .= "	, prtb_produto as produto  ";
		$sql .= "WHERE  ";
		$sql .= "	conta.id_cliente = cliente.id_cliente  ";
		$sql .= "	AND contrato.id_cliente_produto = cliente_produto.id_cliente_produto  ";
		$sql .= "	AND conta.id_cliente_produto = contrato.id_cliente_produto  ";
		$sql .= "	AND produto.id_produto = cliente_produto.id_produto  ";
		$sql .= "	AND conta.status NOT IN ('C', 'S')  ";
		$sql .= "GROUP BY  ";
		$sql .= "	contrato.tipo_produto ";
		$sql .= "	, cliente.nome_razao ";
		$sql .= "	, produto.nome ";
		$sql .= "	, contrato.id_cliente_produto ";*/

		return $this->bd->obtemRegistros($sql);

	}

	public function obtemContrato($id_cliente_produto) {
		$sql  = "SELECT ";
		$sql .= "	id_cliente_produto, tipo_produto ";
		$sql .= "FROM cbtb_contrato ";
		$sql .= "WHERE id_cliente_produto = $id_cliente_produto ";
		//echo "$sql";
		return $this->bd->obtemUnicoRegistro($sql);
	}
	
	public function obtemContratosRenovacao() {
		
		$sql  = "SELECT ";
		$sql .= "   cl.id_cliente, cl.nome_razao, ";
		$sql .= "	ctt.data_renovacao, ctt.valor_contrato, ";
		$sql .= "	p.nome, cp.id_cliente_produto, ctt.status ";
		$sql .= "FROM ";
		$sql .= "	cbtb_cliente_produto cp INNER JOIN cltb_cliente cl ON (cp.id_cliente = cl.id_cliente) ";
		$sql .= "   INNER JOIN prtb_produto p ON (cp.id_produto = p.id_produto) ";
		$sql .= "	INNER JOIN cbtb_contrato ctt ON (cp.id_cliente_produto = ctt.id_cliente_produto) ";
		$sql .= "WHERE ";
		$sql .= "	ctt.status = 'A' ";
		$sql .= "	AND ctt.data_renovacao <= now() + interval '30 day' ";
		$sql .= "ORDER BY ctt.data_renovacao ASC ";
		
				
		return($this->bd->obtemRegistros($sql));
	
	}
	

}

?>