<?

/**
 * Modelo de Produtos
 */
class PERSISTE_PRTB_PRODUTO extends VirtexPersiste {
	protected static $_mapaTabelas = array("BL" => "prtb_produto_bandalarga", "D" => "prtb_produto_discado", "H" => prtb_produto_hospedagem);

	public function __construct($bd=null) {
		parent::__construct($bd);
		$this->_campos	 	= array("id_produto", "nome", "descricao", "tipo", "valor", "disponivel", "num_emails", "quota_por_conta", "vl_email_adicional",
										"permitir_outros_dominios", "email_anexado", "numero_contas", "comodato", "valor_comodato", "desconto_promo", 
										"periodo_desconto", "tx_instalacao", "valor_estatico", "comissao", "comissao_migracao", "modelo_contrato"
										);
										$this->_chave 		= "id_produto";
										$this->_ordem 		= "nome";
										$this->_tabela		= "prtb_produto";
										$this->_sequence	= "prsq_id_produto";
										$this->_filtros		= array("id_produto" => "number", "valor" => "number", "num_emails" => "number", "disponivel" => "bool",
										"quota_por_conta" => "number", "vl_email_adicional" => "number", "permitir_outros_dominios" => "bool", 
										"numero_contas" => "custom", "valor_comodato" => "number", "desconto_promo" => "number",
										"periodo_desconto" => "number", "tx_instalacao" => "number", "valor_estatico" => "bool", "comissao" => "number", "comissao_migracao" => "number", "modelo_contrato"=>"number");
	}

	public static function &factoryByType($tipo) {
		return(self::factory(self::$_mapaTabelas[$tipo]));
	}


	public function filtroCampo($campo,$valor) {
		$valor = parent::filtroCampo($campo,$valor);
			
		switch($campo) {
				
			case 'numero_contas':
				if( !((int)$numero_contas) ) {
					$retorno = "1";
				}
				break;
			default:
				$retorno = $valor;
				
		}
			
		return($retorno);

	}

	/**
	 * Enums (utilizado nos selects)
	 */

	public static function enumDisponivel() {
		return(array("t" => "Sim", "f" => "Não"));
	}

	public static function enumPermitirOutrosDominios() {
		return(array("t" => "Sim", "f" => "Não"));
	}

	public function obtemQtdeContratosPorProduto(){
		$sql = " SELECT \n";
		$sql.= "	p.id_produto, \n";
		$sql.= "	p.nome, \n";
		$sql.= "	p.tipo, \n";
		$sql.= "	p.valor, \n";
		$sql.= "	p.disponivel, \n";
		$sql.= "	( SELECT count(cp.id_produto) as num_contratos FROM cbtb_contrato cp where cp.id_produto = p.id_produto and cp.status = 'A'  ) AS num_contratos  \n";
		$sql.= " FROM  \n";
		$sql.= "	prtb_produto p \n";
		$sql.= "ORDER BY  \n";
		$sql.= "	num_contratos DESC \n";
			
		return $this->bd->obtemRegistros($sql);
	}

	public function obtemQtdeContratosPorTipoDeProduto(){
		$sql = "SELECT \n";
		$sql.= "	p.tipo, \n";
		$sql.= "	sum(  \n";
		$sql.= "		( SELECT count(cp.id_produto) as num_contratos FROM cbtb_contrato cp where cp.id_produto = p.id_produto and cp.status = 'A'  ) \n";
		$sql.= "	) AS num_contratos  \n";
		$sql.= " FROM   \n";
		$sql.= "	prtb_produto p \n";
		$sql.= "GROUP BY \n";
		$sql.= "	tipo  \n";
		$sql.= "ORDER BY   \n";
		$sql.= "	num_contratos DESC \n";
			
		return $this->bd->obtemRegistros($sql);
	}
}

