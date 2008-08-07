<?

class PERSISTE_CBTB_FATURAS extends VirtexPersiste {

	public static $ABERTA = "A";
	public static $PAGA = "P";
	public static $PARCIAL = "R";
	public static $ESTORNADA = "E";
	public static $CANCELADA = "C";

	public function __construct($bd=null) {
		parent::__construct();

		$this->_campos 		= array("id_cliente_produto", "data", "descricao", "valor", "status", "observacoes", "reagendamento", "pagto_parcial", "data_pagamento", "desconto", "acrescimo", "valor_pago", "id_cobranca", "cod_barra", "anterior", "id_carne", "nosso_numero", "linha_digitavel", "nosso_numero_banco", "tipo_retorno", "email_aviso", "id_forma_pagamento", "id_remessa", "id_retorno");
		$this->_chave 		= "id_cobranca";
		$this->_ordem 		= "";
		$this->_tabela 		= "cbtb_faturas";
		$this->_sequence	= "cbtb_faturas_id_cobranca_seq";
		$this->_filtros		= array("id_cliente_produto"=>"number", "data"=>"date", "valor"=>"number", "reagendamento"=>"date", "pagto_parcial"=>"number", "data_pagamento"=>"date", "desconto"=>"number", "acrescimo"=>"number", "valor_pago"=>"number", "id_cobranca"=>"number", "anterior"=>"bool", "id_carne"=>"number", "nosso_numero_banco"=>"number", "tipo_retorno"=>"number", "email_aviso"=>"bool", "id_forma_pagamento"=>"number", "id_remessa"=>"number");

	}
	
	public function obtemFaturasPorRetorno($id_retorno) {
		return($this->obtemFaturas("","","",$id_retorno));
	}

	public function obtemFaturas ($id_cliente = "", $id_cliente_produto = "", $id_carne = "", $id_retorno = "")
	{
	
		if (func_num_args () == 0 )
		return;

		$q = "SELECT f.descricao, f.valor, f.status,
					 f.data as dt_ordem, 
				     to_char (f.data,'dd/mm/YYYY') as data,
				     data as data_orig,
				     to_char (f.data_pagamento,'dd/mm/YYYY') as data_pagamento,
				     to_char (f.reagendamento,'dd/mm/YYYY') as reagendamento,
				     f.valor_pago,
				     f.id_cliente_produto,
				     f.id_cobranca,
				     f.id_retorno,
				     cl.id_cliente, cl.nome_razao, cid.id_cidade, cid.cidade, cid.uf, p.nome as produto
				FROM cbtb_faturas f
			  INNER JOIN cbtb_cliente_produto cp ON f.id_cliente_produto = cp.id_cliente_produto
			  INNER JOIN cltb_cliente cl ON cl.id_cliente = cp.id_cliente
			  INNER JOIN prtb_produto p ON p.id_produto = cp.id_produto 
			  INNER JOIN cftb_cidade cid ON cl.id_cidade = cid.id_cidade 

				";

		$where = array ();

		if ($id_cliente)
		$where [] = 'cp.id_cliente = ' . $this->bd->escape ($id_cliente);

		if ($id_cliente_produto)
		$where [] = 'f.id_cliente_produto = ' . $this->bd->escape ($id_cliente_produto);

		if ($id_carne)
		$where [] = 'f.id_carne = ' . $this->bd->escape ($id_carne);
		
		$where [] = "f.status <> 'E' ";
		$where [] = "f.valor > 0 ";
		
		if( $id_retorno ) {
			$where[] = "f.id_retorno = " . $this->bd->escape($id_retorno);
		}

		$q .= " WHERE " . implode (" AND ", $where);
		
		$q .= " ORDER BY dt_ordem DESC ";
		
		//echo "Q: $q<br>\n";
		return ($this->bd->obtemRegistros ($q));
	}

	public function obtemFaturasAtrasadasPorPeriodo($periodo){
		$sql  = "SELECT ";
		$sql .= "	count(*) as num_contratos, ";
		$sql .= "	EXTRACT(year from f.data) as ano, ";
		$sql .= "	EXTRACT(month from f.data) as mes ";
		$sql .= "FROM ";
		$sql .= "	cbtb_faturas f INNER JOIN cbtb_contrato ctt USING(id_cliente_produto)  ";
		$sql .= "";
		$sql .= "WHERE ";
		$sql .= "   f.status not in ('E', 'C') AND CASE WHEN f.status = 'P' THEN f.data_pagamento > f.data ELSE f.data < now() END ";
		$sql .= "GROUP BY ano, mes ";
		$sql .= "ORDER BY ano, mes ";
		//echo $sql;
		return ($this->bd->obtemRegistros ($sql));
	}


	public function enumStatusFatura(){
		return array(
		PERSISTE_CBTB_FATURAS::$ABERTA => "Aberta",
		PERSISTE_CBTB_FATURAS::$PAGA => "Paga",
		PERSISTE_CBTB_FATURAS::$PARCIAL => "Parcial",
		PERSISTE_CBTB_FATURAS::$ESTORNADA => "Estornada",
		PERSISTE_CBTB_FATURAS::$CANCELADA => "Cancelada" );
	}


public function obtemFaturasAtrasadasDetalhes($periodo){

		// $periodo recebe $mes/$ano;

		list($ano,$mes) = explode("-",$periodo);

		if( $mes < 10 ) {
			$mes = "0".$mes;
		}


		$data1 = '01/'.$mes."/".$ano;
		$data2 = MData::adicionaMes($data1,1);

		$data1 = MData::ptBR_to_ISO($data1);
		$data2 = MData::ptBR_to_ISO($data2);

		$sql  = "SELECT ";
		$sql .= "	EXTRACT(year from f.data) as ano, ";
		$sql .= "	EXTRACT(month from f.data) as mes, ";
		$sql .= "	EXTRACT(day from f.data) as dia, ";
		$sql .= "   f.data, f.data_pagamento, f.id_cliente_produto, cl.id_cliente, p.id_produto, f.valor, f.status, f.descricao, cl.nome_razao, ";
		$sql .= "   CASE WHEN f.status = 'P' THEN 'Pago com atrazo' ELSE 'Em Atraso' END as strstatus ";
		$sql .= "FROM ";
		$sql .= "	cbtb_faturas f INNER JOIN cbtb_cliente_produto cp ON (f.id_cliente_produto = cp.id_cliente_produto) ";
		$sql .= "   INNER JOIN cbtb_contrato ctt ON (cp.id_cliente_produto = ctt.id_cliente_produto) ";
		$sql .= "   INNER JOIN cltb_cliente cl ON (cl.id_cliente = cp.id_cliente) ";
		$sql .= "   INNER JOIN prtb_produto p ON (cp.id_produto = p.id_produto) ";
		$sql .= "WHERE ";
		$sql .= "   f.data >=  '$data1' AND f.data < '$data2' AND f.status not in ('E','C') AND ";
		$sql .= "   CASE WHEN f.status = 'P' THEN f.data_pagamento > f.data ELSE f.data < now() END ";
		$sql .= "ORDER BY f.data DESC";


		$retorno = $this->bd->obtemRegistros ($sql);

		return ($retorno);
	}


	public function obtemAnosFatura() {

		$sql  = "SELECT DISTINCT ";
		$sql .= "	EXTRACT(YEAR FROM data) as ano ";
		$sql .= "FROM ";
		$sql .= "	cbtb_faturas ";

		return  $this->bd->obtemRegistros($sql);
	}


	public function obtemFaturasPorPeriodoSemCodigoBarra($data_referencia, $periodo) {

			$sql  = "SELECT id_cobranca FROM cbtb_faturas ";
			$sql .= "WHERE ";
			$sql .= "	cod_barra IS NULL ";


			if($periodo == "PQ") {		//PRRIMEIRA QUINZENA
				$sql .= " AND data BETWEEN '$data_referencia-01' AND '$data_referencia-15' ";
			} else if ($periodo == "SQ") {	//SEGUNDA QUINZENA
				$sql .= " AND data BETWEEN '$data_referencia-16' AND DATE '$data_referencia-1' + INTERVAL '1 MONTH' - INTERVAL '1 DAY' ";
			} else { 		// MES COMPLETO
				$sql .= " AND data BETWEEN '$data_referencia-1' AND DATE '$data_referencia-1' + INTERVAL '1 MONTH' - INTERVAL '1 DAY' ";
			}

			return ($this->bd->obtemRegistros($sql));

	}


	public function obtemFaturasPorPeriodoParaRemessa($data_referencia, $periodo) {

			$sql  = "SELECT * FROM cbtb_faturas ";
			$sql .= "WHERE ";
			$sql .= "	nosso_numero IS NULL ";
			$sql .= "	AND id_remessa IS NULL ";
			$sql .= "	AND id_forma_pagamento = 9999";


			if($periodo == "PQ") {		//PRRIMEIRA QUINZENA
				$sql .= " AND data BETWEEN '$data_referencia-01' AND '$data_referencia-15' ";
			} else if ($periodo == "SQ") {	//SEGUNDA QUINZENA
				$sql .= " AND data BETWEEN '$data_referencia-16' AND DATE '$data_referencia-1' + INTERVAL '1 MONTH' - INTERVAL '1 DAY' ";
			} else { 		// MES COMPLETO
				$sql .= " AND data BETWEEN '$data_referencia-1' AND DATE '$data_referencia-1' + INTERVAL '1 MONTH' - INTERVAL '1 DAY' ";
			}
			
			$sql .= " AND status = 'A' " ; // Somente faturas em aberto.

			return ($this->bd->obtemRegistros($sql));

	}


	public function obtemFaturaPorContratoPeriodo($data_referencia, $periodo, $id_contrato) {

		$sql  = "SELECT id_cobranca FROM cbtb_faturas ";
		$sql .= "WHERE id_cliente_produto = '$id_contrato'";

		if($periodo == "PQ") {		//PRRIMEIRA QUINZENA
			$sql .= " AND data BETWEEN '$data_referencia-01' AND '$data_referencia-15' ";
		} else if ($periodo == "SQ") {	//SEGUNDA QUINZENA
			$sql .= " AND data BETWEEN '$data_referencia-16' AND DATE '$data_referencia-1' + INTERVAL '1 MONTH' - INTERVAL '1 DAY' ";
		} else { 		// MES COMPLETO
			$sql .= " AND data BETWEEN '$data_referencia-1' AND DATE '$data_referencia-1' + INTERVAL '1 MONTH' - INTERVAL '1 DAY' ";
		}

		//echo "SQL: $sql<br>\n";
		return ($this->bd->obtemUnicoRegistro($sql));
	}	


	public	function obtemFaturasPorPeriodoSemCodigoBarraPorTipoPagamento($data_referencia, $periodo,$id_forma_pagamento) {

		$sql  = "SELECT id_cobranca FROM cbtb_faturas ";
		$sql .= "WHERE ";
		$sql .= "	cod_barra IS NULL ";


		if($periodo == "PQ") {		//PRRIMEIRA QUINZENA
			$sql .= " AND data BETWEEN '$data_referencia-01' AND '$data_referencia-15' ";
		} else if ($periodo == "SQ") {	//SEGUNDA QUINZENA
			$sql .= " AND data BETWEEN '$data_referencia-16' AND DATE '$data_referencia-1' + INTERVAL '1 MONTH' - INTERVAL '1 DAY' ";
		} else { 		// MES COMPLETO
			$sql .= " AND data BETWEEN '$data_referencia-1' AND DATE '$data_referencia-1' + INTERVAL '1 MONTH' - INTERVAL '1 DAY' ";
		}

		if ($id_forma_pagamento){

			$sql .= " AND id_forma_pagamento='$id_forma_pagamento' ";

		}

		///echo "SQL: $sql<br>\n";


		return ($this->bd->obtemRegistros($sql));
	}

	public function obtemFaturasPorRemessa($id_remessa) {
			$sql  = " SELECT ";
			$sql .="   r.id_remessa, f.id_cobranca, cl.nome_razao, f.status, f.id_cliente_produto,f.observacoes, f.data, f.id_forma_pagamento, f.valor, f.id_cobranca, f.linha_digitavel, f.cod_barra, cp.id_cliente ";
			$sql .=" FROM ";
			$sql .="   cbtb_lote_fatura r INNER JOIN cbtb_faturas f ON (f.id_cobranca = r.id_cobranca) ";
			$sql .="   INNER JOIN cbtb_cliente_produto cp ON (f.id_cliente_produto = cp.id_cliente_produto) ";
			$sql .="   INNER JOIN cltb_cliente cl ON (cp.id_cliente = cl.id_cliente) ";
			$sql .=" WHERE ";
			$sql .="   r.id_remessa = $id_remessa ";
			$retorno = $this->bd->obtemRegistros($sql);
			$hoje = date("Y-m-d");
			
			for($i=0;$i<count($retorno);$i++) {
			

				$diff = MData::diff($hoje,$retorno[$i]["data"]);
				if( $retorno[$i]["status"] == "E" ) {
					$strstatus = "Estornada";
				} elseif( $retorno[$i]["status"] == "C" ) {
					$strstatus = "Cancelada";
				} elseif( $retorno[$i]["status"] == "P" ) {
					if( $data_pagamento ) {
						$diff2 = MData::diff($retorno[$i]["data"],$retorno[$i]["data_pagamento"]);
						$strstatus = $diff2 > 0 ? "Paga" : "Paga com atrazo";
					} else {
						$strstatus == "Paga";
					}
				} else {
					if( $diff < 0 ) {
						$dias = $diff * -1;
						$strstatus = "Em atrazo (" . $dias . " " . ($dias == 1?"dia":"dias") . ")";
					} else {
						$dias = $diff == 0 ? "hoje" : $diff == "1" ? "amanhã" : $diff . " dias";
						$strstatus = "A vencer (" . $dias . ") ";
					}
				}
				
				$retorno[$i]["strstatus"] = $strstatus;
				
			}
			
			return ($retorno);
	
	}
	
	public function obtemFaturasPorRemessaGeraBoleto($id_remessa) {
	
			$sql  = " SELECT ";
			$sql .="   r.id_remessa, f.id_cobranca, f.status, f.id_cliente_produto,f.observacoes, f.data, f.id_forma_pagamento, f.valor, f.id_cobranca, f.linha_digitavel, f.cod_barra, cp.id_cliente, fp.carteira, fp.convenio, fp.agencia, fp.conta, f.nosso_numero, cl.nome_razao, cc.nome_produto, cc.descricao_produto, cl.endereco, cl.complemento, cd.cidade, uf.estado, cl.cep, cl.cpf_cnpj ";
			$sql .=" FROM ";
			$sql .="   cbtb_lote_fatura r INNER JOIN cbtb_faturas f ON (f.id_cobranca = r.id_cobranca) ";
			$sql .="   INNER JOIN cbtb_cliente_produto cp ON (f.id_cliente_produto = cp.id_cliente_produto) ";
			$sql .="   INNER JOIN cbtb_contrato cc ON (f.id_cliente_produto = cc.id_cliente_produto) ";
			$sql .="   INNER JOIN cltb_cliente cl ON (cp.id_cliente = cl.id_cliente) ";
			$sql .="   INNER JOIN pftb_forma_pagamento fp ON (f.id_forma_pagamento = fp.id_forma_pagamento) ";
			$sql .="   INNER JOIN cftb_cidade cd ON (cl.id_cidade = cd.id_cidade) ";
			$sql .="   INNER JOIN cftb_uf uf ON (cd.uf = uf.uf) ";
			$sql .=" WHERE ";
			$sql .="   id_remessa = $id_remessa ";
	
			////echo $sql;
	
			return ($this->bd->obtemRegistros($sql));
	
	}

	public function InsereCodigoBarraseLinhaDigitavel($codigo_barra,$linha_digitavel,$id_cobranca){

		$dados = array( "cod_barra" => $codigo_barra, "linha_digitavel" => $linha_digitavel );
		$filtro = array("id_cobranca" => $id_cobranca );

		/*echo $codigo_barra;
		echo $linha_digitavel;
		echo $id_cobranca;
		echo "oie";*/

		return($this->altera($dados,$filtro));

	}

	public function obtemFaturamentoPorPeriodo($meses) {
		$meses -= 1;
		$sql = "SELECT ";
		$sql .=   "extract('year' from data) as ano, extract('month' from data) as mes, ";
		$sql .=   "sum(valor) as valor_documento, sum(desconto) as valor_desconto, ";
		$sql .=   "sum(acrescimo) as valor_acrescimo, sum(valor_pago) as valor_pago ";
		$sql .= "FROM ";
		$sql .=   "cbtb_faturas f ";
		$sql .= "WHERE ";
		$sql .= "  f.status = 'P' AND f.data >= date_trunc('month',now() - interval '$meses month') AND f.data < now() ";
		$sql .= "GROUP BY ";
		$sql .= "  extract('year' from data), extract('month' from data)";
		$sql .= "ORDER BY ";
		$sql .= "  extract('year' from data), extract('month' from data)";

		//echo $sql;

		return ($this->bd->obtemRegistros($sql));
	}

	public function obtemFaturamentoPorMes($ano,$mes) {

		$data_ini = $ano . "-" . $mes . "-01";
		$data_fim = MData::ptBR_to_ISO(MData::adicionaMes($data_ini,1));

		$sql = "SELECT ";
		$sql .=   "data as periodo, ";
		$sql .=   "sum(valor) as valor_documento, sum(desconto) as valor_desconto, ";
		$sql .=   "sum(acrescimo) as valor_acrescimo, sum(valor_pago) as valor_pago ";
		$sql .= "FROM ";
		$sql .=   "cbtb_faturas f ";
		$sql .= "WHERE ";
		$sql .= "  f.status = 'P' AND f.data >= '$data_ini' AND f.data < '$data_fim' ";
		$sql .= "GROUP BY ";
		$sql .= "  data ";
		$sql .= "ORDER BY ";
		$sql .= "  data ";
		
		echo "SQL: $sql<br>\n"; 

		return ($this->bd->obtemRegistros($sql));
	}
	
	
	protected function sqlFaturasPagas() {
		$sql  = "SELECT ";
		$sql .= "  id_cliente_produto, data_pagamento, data, id_cobranca, ";
		$sql .= "  CASE WHEN id_retorno is null THEN valor_pago ELSE 0 END as vlb, ";
		$sql .= "  CASE WHEN id_retorno is not null THEN valor_pago ELSE 0 END as vlr ";
		$sql .= "FROM ";
		$sql .= "  cbtb_faturas xxx ";
		$sql .= "WHERE  ";
		$sql .= "  xxx.status = 'P' ";
		return($sql);
	}
	
	protected function sqlRecebimentosMes($mes,$ano) {
		$data_ini = $ano . "-" . $mes . "-01";
		$data_fim = MData::ptBR_to_ISO(MData::adicionaMes($data_ini,1));
		$sql = $this->sqlFaturasPagas();
		$sql .= "  AND data_pagamento >= '$data_ini' AND data_pagamento < '$data_fim' ";
		return($sql);
	}

	protected function sqlRecebimentosDia($data) {
		$sql = $this->sqlFaturasPagas();
		$sql .= "  AND data_pagamento = '$data'";
		return($sql);
	}
	
	protected function sqlRecebimentosPeriodo($meses) { 
		$meses -= 1;
		$meses *= -1;
		
		$dataBase = date("Y-m-01");
		$data_ini = MData::adicionaMes($dataBase,$meses);

		$sql = $this->sqlFaturasPagas();
		$sql .= "  AND data_pagamento >= '$data_ini' AND data_pagamento < now() ";
		
		
		$sqlRet  = "     SELECT ";
		$sqlRet .= "        CAST( extract('year' from data_pagamento) || '-' || extract('month' from data_pagamento) || '-01' as date) as data_pagamento, ";
		$sqlRet .= "        id_cliente_produto, x.vlb, x.vlr";
		$sqlRet .= "     FROM ( $sql ) x ";
		

		return($sqlRet);
		

		
	}

	protected function sqlFaturamentoMes($mes,$ano) {
		$data_ini = $ano . "-" . $mes . "-01";
		$data_fim = MData::ptBR_to_ISO(MData::adicionaMes($data_ini,1));
		$sql = $this->sqlFaturasPagas();
		$sql .= "  AND data >= '$data_ini' AND data < '$data_fim' ";
		return($sql);
	}

	protected function sqlFaturamentoDia($data) {
		$sql = $this->sqlFaturasPagas();
		$sql .= "  AND data = '$data'";
		return($sql);
	}
	
	protected function sqlFaturamentoPeriodo($meses) { 
		$meses -= 1;
		$meses *= -1;
		
		$dataBase = date("Y-m-01");
		$data_ini = MData::adicionaMes($dataBase,$meses);

		$sql = $this->sqlFaturasPagas();
		$sql .= "  AND data >= '$data_ini' AND data < now() ";
		
		
		$sqlRet  = "     SELECT ";
		$sqlRet .= "        CAST( extract('year' from data) || '-' || extract('month' from data) || '-01' as date) as data, ";
		$sqlRet .= "        id_cliente_produto, x.vlb, x.vlr";
		$sqlRet .= "     FROM ( $sql ) x ";
		

		return($sqlRet);
		

		
	}
	
	/**
	 * Base para faturamento e recebimentos
	 */
	protected function obtemFatRec($tp,$tipo,$data) {
		if( $tp == 'F' ) {
			// Faturamento
			$campo_base = 'data';
		} else {
			$campo_base = 'data_pagamento';
		}

		$sql = "SELECT ";
		if( $tipo == 'diario' ) {
			$sql .= "   f." . $campo_base . " as periodo, cid.cidade as cidade, cl.nome_razao, p.nome as produto, f.id_cliente_produto, data, id_cobranca, cl.id_cliente, ";
			$sql .= "   f.vlb as valor_pago_balcao, ";
			$sql .= "   f.vlr as valor_pago_retorno ";
		} else {
			$sql .= "   f. " . $campo_base ." as periodo, cid.cidade as cidade,   ";
			$sql .= "   sum(f.vlb) as valor_pago_balcao, ";
			$sql .= "   sum(f.vlr) as valor_pago_retorno ";
		}
		$sql .= "FROM ";
		$sql .= "   (" ;

		switch($tipo) {
			case 'mensal':
				$data = MData::ptBR_to_ISO($data);
				list($a,$m,$d) = explode("-",$data);
				$sql .= $tp == 'F' ? $this->sqlFaturamentoMes($m,$a) : $this->sqlRecebimentosMes($m,$a);
				break;

			case 'anual':
				$sql .= $tp == 'F' ? $this->sqlFaturamentoPeriodo(12) : $this->sqlRecebimentosPeriodo(12);
				break;

			case 'diario':
				$sql .= $tp == 'F' ? $this->sqlFaturamentoDia : $this->sqlRecebimentosDia($data);
				break;

		}

		$sql .=   ") f ";
		$sql .=   "   INNER JOIN cbtb_cliente_produto cp ON cp.id_cliente_produto = f.id_cliente_produto  ";
		$sql .=   "   INNER JOIN cltb_cliente cl ON cl.id_cliente = cp.id_cliente ";
		$sql .=   "   INNER JOIN cftb_cidade cid ON cl.id_cidade = cid.id_cidade ";
		$sql .=   "   INNER JOIN prtb_produto p ON cp.id_produto = p.id_produto ";
		if( $tipo != 'diario' ) {
			$sql .=   "GROUP BY ";
			$sql .=   "   f." . $campo_base . ", cid.cidade ";
		}
		$sql .=   "ORDER BY ";
		$sql .=   "   f." . $campo_base . ", cid.cidade ";

		//echo "<pre>SQL: \n$sql\n"; 
		$retorno =$this->bd->obtemRegistros($sql);
		
		return($retorno);
		


	}

	public function obtemFaturamento($tipo="anual",$data='') {
		return($this->obtemFatRec("F",$tipo,$data));
	}
	
	
	public function obtemRecebimentos($tipo="anual",$data='') {
		return($this->obtemFatRec("R",$tipo,$data));
	}



	public function obtemFaturamentoPorProduto($ano_select) {
		if ($ano_select){
			$ano_select1 += $ano_select++;
		}
		$sql = "SELECT ";
		$sql .=   "p.tipo, extract('year' from f.data) as ano, extract('month' from f.data) as mes, ";
		$sql .=   "sum(f.valor) as valor_documento, sum(f.desconto) as valor_desconto, ";
		$sql .=   "sum(f.acrescimo) as valor_acrescimo, sum(f.valor_pago) as valor_pago ";
		$sql .= "FROM ";
		$sql .=   "cbtb_faturas f ";
		$sql .=   "INNER JOIN cbtb_cliente_produto cp ON cp.id_cliente_produto = f.id_cliente_produto ";
		$sql .=   "INNER JOIN prtb_produto p ON cp.id_produto = p.id_produto ";
		$sql .= "WHERE ";
		$sql .=   "f.status = 'P' AND f.data >= '$ano_select1-01-01' ";
		$sql .= "AND ";
		$sql .=   "f.data < '$ano_select-01-01' ";
		$sql .= "GROUP BY ";
		$sql .=   "p.tipo, extract('year' from data), extract('month' from data)";

		return ($this->bd->obtemRegistros($sql));
	}
	
	public function obtemFaturamentoComparativo($ano){
		
		

		$data_inicio = $ano."-01-01";
		$data_final = $ano."-12-31";

		
		
		$sSQL  = "SELECT "; 
		$sSQL .= "SUM(valor_pago) as faturamento, ";
		$sSQL .= "EXTRACT(day from data_pagamento) as dia, ";  
		$sSQL .= "EXTRACT(month from data_pagamento) as mes, ";  
		$sSQL .= "EXTRACT(year from data_pagamento) as ano   ";
		$sSQL .= "FROM   ";
		$sSQL .= "cbtb_faturas   ";
		$sSQL .= "WHERE   ";
		$sSQL .= "status = 'P' ";
		$sSQL .= "AND data_pagamento BETWEEN   ";
		$sSQL .= "CAST( '$data_inicio' as date)  ";
		$sSQL .= "AND CAST( '$data_final' as date )  ";
		$sSQL .= "GROUP BY   ";
		$sSQL .= "ano, mes, dia  ";
		$sSQL .= "ORDER BY   ";
		$sSQL .= "dia, mes, ano  ASC";
		
		
		
		return ($this->bd->obtemRegistros($sSQL));
	
	}

	public function obtemPrevisaoFaturamento($ano_select) {

		if ($ano_select){
			$ano_select1 = $ano_select+1;
		}

		$sql  = "SELECT ";
		$sql .= "extract('year' from f.data) as ano, extract('month' from f.data) as mes, ";
		$sql .= "extract('day' from f.data) as dia,  ";
		$sql .= "sum(f.valor) as valor_documento, sum(f.desconto) as valor_desconto, ";
		$sql .= "sum(f.acrescimo) as valor_acrescimo, sum(f.valor_pago) as valor_pago ";
		$sql .= "FROM ";
		$sql .= "   cbtb_faturas f  ";
		$sql .= "WHERE ";
		$sql .= "   f.status not in ('E','C') AND f.data >= '$ano_select-01-01' AND f.data < '$ano_select1-01-01' ";
		$sql .= "GROUP BY ";
		$sql .= "   extract('year' from data), extract('month' from data), extract('day' from data) ";
		//echo $sql;
		return ($this->bd->obtemRegistros($sql));

	}
	
	public function obtemNumeroFaturasPendentes($id_cliente_produto) {
		$sql .= "SELECT count(*) as num_faturas FROM cbtb_faturas WHERE id_cliente_produto = $id_cliente_produto AND status not in ('P','E','C') AND valor > 0";
		$retorno = $this->bd->obtemUnicoRegistro($sql);
		return($retorno["num_faturas"]);
	}
	
	public function obtemDiaVencimento($id_cliente_produto) {
		$sql = "select avg(extract('day' from data)) as dia from cbtb_faturas where id_cliente_produto = $id_cliente_produto";
		$retorno = $this->bd->obtemUnicoRegistro($sql);
		return((int)@$retorno["dia"]);
	}
	
	
}

?>