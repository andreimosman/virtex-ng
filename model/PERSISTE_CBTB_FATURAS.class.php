<?

class PERSISTE_CBTB_FATURAS extends VirtexPersiste {

	public static $ABERTA = "A";
	public static $PAGA = "P";
	public static $PARCIAL = "R";
	public static $ESTORNADA = "E";
	public static $CANCELADA = "C";

	public function __construct($bd=null) {
		parent::__construct();

		$this->_campos 		= array("id_cliente_produto", "data", "descricao", "valor", "status", "observacoes", "reagendamento", "pagto_parcial", "data_pagamento", "desconto", "acrescimo", "valor_pago", "id_cobranca", "cod_barra", "anterior", "id_carne", "nosso_numero", "linha_digitavel", "nosso_numero_banco", "tipo_retorno", "email_aviso", "id_forma_pagamento");
		$this->_chave 		= "id_cobranca";
		$this->_ordem 		= "";
		$this->_tabela 		= "cbtb_faturas";
		$this->_sequence	= "cbtb_faturas_id_cobranca_seq";
		$this->_filtros		= array("id_cliente_produto"=>"number", "data"=>"date", "valor"=>"number", "reagendamento"=>"date", "pagto_parcial"=>"number", "data_pagamento"=>"date", "desconto"=>"number", "acrescimo"=>"number", "valor_pago"=>"number", "id_cobranca"=>"number", "anterior"=>"bool", "id_carne"=>"number", "nosso_numero_banco"=>"number", "tipo_retorno"=>"number", "email_aviso"=>"bool", "id_forma_pagamento"=>"number");

	}

	public function obtemFaturas ($id_cliente = "", $id_cliente_produto = "", $id_carne = "")
	{
		if (func_num_args () == 0 )
		return;

		$q = "SELECT f.descricao, f.valor, f.status,
				     to_char (f.data,'dd/mm/YYYY') as data,
				     data as data_orig,
				     to_char (f.data_pagamento,'dd/mm/YYYY') as data_pagamento,
				     to_char (f.reagendamento,'dd/mm/YYYY') as reagendamento,
				     f.valor_pago,
				     f.id_cliente_produto,
				     f.id_cobranca
				FROM cbtb_faturas f
			  INNER JOIN cbtb_cliente_produto p ON f.id_cliente_produto = p.id_cliente_produto
			  
				";

		$where = array ();

		if ($id_cliente)
		$where [] = 'p.id_cliente = ' . $this->bd->escape ($id_cliente);
			
		if ($id_cliente_produto)
		$where [] = 'f.id_cliente_produto = ' . $this->bd->escape ($id_cliente_produto);

		if ($id_carne)
		$where [] = 'f.id_carne = ' . $this->bd->escape ($id_carne);
			
		$q .= " WHERE " . implode (" AND ", $where);
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
		
		//echo $sql."<br><br>";
		
		/**
		$sql .= "  CASE WHEN ";
		$sql .= "     f.status = 'P' ";
		$sql .= "  THEN ";
		$sql .= "	   data_pagamento > data ";
		$sql .= "	ELSE ";
		$sql .= "   	CASE WHEN ";
		$sql .= "   		f.reagendamento IS NOT NULL ";
		$sql .= "   	THEN ";
		$sql .= "   		f.reagendamento >= CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '$periodo months' AND ";
		$sql .= "   		f.reagendamento <  CAST( EXTRACT(year from now()) || '-' ||EXTRACT(month from now()) ||'-01' as date) ";
		$sql .= "   	ELSE ";
		$sql .= "   		f.data >= CAST( EXTRACT(year from now() + INTERVAL '1 month') || '-' ||EXTRACT(month from now() + INTERVAL '1 month') ||'-01' as date) - INTERVAL '$periodo months' AND ";
		$sql .= "  		f.data < CAST( EXTRACT(year from now()) || '-' ||EXTRACT(month from now()) ||'-01' as date) ";
		$sql .= "   	END ";
		$sql .= "	END AND ";
		$sql .= "	(f.status != 'E' AND f.status != 'C') AND ctt.status = 'A' ";
		$sql .= "GROUP BY ano, mes ";
		$sql .= "ORDER BY ano, mes ";
		*/
		
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

}
?>