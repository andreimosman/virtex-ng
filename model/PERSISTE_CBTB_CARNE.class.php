<?

	class PERSISTE_CBTB_CARNE extends VirtexPersiste {
	
		public static $PADRAO_VIRTEX 		= '';
		public static $PADRAO_OUTROSERVIDOR 	= 'O';	
	
		public function __construct() {
			parent::__construct();
			
			$this->_campos 		= array("id_carne", "data_geracao", "status", "id_cliente_produto", "valor", "vigencia", "id_cliente");
			$this->_chave		= "id_carne";
			$this->_tabela		= "cbtb_carne";
			$this->_ordem		= "data_geracao";
			$this->_sequence	= "cbsq_id_carne";
			$this->_filtros		= array("id_cliente" => "number", "id_cliente_produto" => "number", "valor" => "number","vigencia" => "number", "data_geracao"=>"date");
			
		}
				
		/*
		 * retorna array com os carnes do contrato especificado como parametro.
		 */
		public function obtemCarnes ($id_cliente_produto)
		{
			$q = "SELECT c.id_carne,
				     to_char (c.data_geracao,'dd/mm/YYYY') as data_geracao,
				     c.status,
				     c.valor,
				     c.vigencia,
				     (SELECT COUNT(*) FROM cbtb_faturas WHERE id_cliente_produto = " . $this->bd->escape ($id_cliente_produto) . ") as total_fatura
			        FROM cbtb_carne c
			       WHERE id_cliente_produto = " . $this->bd->escape ($id_cliente_produto);
			
			$res = $this->bd->obtemRegistros ($q);
			return ($res);
		}
		
	}
?>
