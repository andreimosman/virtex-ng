<?

class PERSISTE_CNTB_CONTA extends VirtexPersiste {

	public static $BANDA_LARGA = "BL";
	public static $DISCADO = "D";
	public static $EMAIL = "E";
	public static $HOSPEDAGEM = "H";

	public function __construct($bd=null) {
		parent::__construct($bd);
		$this->_campos	 	= array("username","dominio","tipo_conta","senha","id_cliente","id_cliente_produto","id_conta",
										"senha_cript", "conta_mestre", "status", "observacoes"
										);
										$this->_chave 		= "id_conta"; // PK é username,dominio,tipo_conta só que jogando isso pegamos o id conta no retorno do insert
										$this->_ordem 		= "username,dominio,tipo_conta";
										$this->_tabela		= "cntb_conta";
										$this->_sequence	= "cnsq_id_conta";
											
										$this->_filtros 	= array("status" => "custom", "conta_mestre" => "bool");
											
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
	
	protected function _obtemContasDeCadaTipo($cortesia = false, $quantidade = false, $tipo_conta = false){
		$sql = " SELECT \n";
		if($quantidade){
			$sql.= "	cnt.tipo_conta,\n";
			$sql.= "	count(cnt.*) as num_contas\n";
		} else {
			$sql.= "	cl.id_cliente,\n";
			$sql.= "	cl.nome_razao,\n";
			$sql.= "	to_char(ct.data_contratacao,'DD/MM/YYYY') as data_contratacao,\n";
			$sql.= "	pr.id_produto,\n";
			$sql.= "	pr.nome as nome_produto,\n";
			$sql.= "	pr.tipo,\n";
			$sql.= "	cnt.username,\n";
			$sql.= "	cnt.dominio,\n";
			$sql.= "	cp.id_cliente_produto\n";
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
		
		if($cortesia){
			$sql.= "	AND pr.valor = 0 \n";
		}
		
		if($quantidade){
			$sql.= " GROUP BY \n";
			$sql.= "	cnt.tipo_conta \n";
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
}

?>
