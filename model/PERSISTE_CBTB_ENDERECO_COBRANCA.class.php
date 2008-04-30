<?

	class PERSISTE_CBTB_ENDERECO_COBRANCA extends VirtexPersiste {
		
		public function __construct($bd=null) {
			parent::__construct();
			
			$this->_campos		= array("id_endereco_cobranca","id_cliente_produto","endereco","complemento","bairro","id_cidade","cep","id_cliente", "id_condominio_cobranca", "id_bloco_cobranca", "apto_cobranca");
			$this->_chave 		= "id_endereco_cobranca";
			$this->_ordem		= "id_endereco_cobranca DESC";
			$this->_tabela		= "cbtb_endereco_cobranca";
			$this->_sequence	= "cbsq_id_endereco_cobranca";
			
			$this->_filtros		= array("id_endereco_cobranca" => "numeric", "id_cliente_produto" => "numeric", "id_cidade" => "numeric", "id_cliente" => "numeric");
		
		}
		
		public function obtemEnderecoCobrancaReferenciado($id_cliente_produto) {
		
			$sql  = "select endc.*, cnd.nome as conodminio_nome, cndb.nome as bloco_nome, cid.cidade, cid.uf, cnt.id_conta \n";
			$sql .= "from cbtb_endereco_cobranca endc \n";
			$sql .= "INNER JOIN cbtb_cliente_produto clp ON clp.id_cliente_produto = endc.id_cliente_produto \n";
			$sql .= "INNER JOIN cntb_conta cnt ON clp.id_cliente_produto = cnt.id_cliente_produto \n";
			$sql .= "LEFT OUTER JOIN catb_condominio cnd ON endc.id_condominio_cobranca = cnd.id_condominio \n";
			$sql .= "LEFT OUTER JOIN catb_condominio_bloco cndb ON endc.id_bloco_cobranca = cndb.id_bloco \n";
			$sql .= "LEFT OUTER JOIN cftb_cidade cid ON endc.id_cidade = cid.id_cidade \n";
			$sql .= "WHERE clp.id_cliente_produto = $id_cliente_produto ORDER BY id_endereco_cobranca desc limit 1 ";
			
			return $this->bd->obtemUnicoRegistro($sql);
		}
	
	
	
	}
	
?>