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
		
		/**
		 * Carnês sem confirmação de impressao.
		 */
		 
		// INSERT INTO cbtb_carne_impressao VALUES(nextval('cbsq_id_impressao'),5778,1,now(),'');
		// 5778 5777 5776
		public function obtemCarnesSemConfirmacao($id_carne="") {
			$q = "
SELECT
   c.id_carne, c.data_geracao, c.status, c.id_cliente_produto, c.valor, c.vigencia, c.id_cliente,
   cl.nome_razao, cid.cidade, cid.uf, p.nome as produto, f.faturas_abertas 
FROM
   cbtb_carne c 
   LEFT OUTER JOIN cbtb_carne_impressao ci ON c.id_carne = ci.id_carne
   INNER JOIN cltb_cliente cl ON cl.id_cliente = c.id_cliente
   INNER JOIN cftb_cidade cid ON cl.id_cidade = cid.id_cidade 
   INNER JOIN cbtb_contrato ctt ON ctt.id_cliente_produto = c.id_cliente_produto
   INNER JOIN prtb_produto p ON ctt.id_produto = p.id_produto  
   INNER JOIN (
   	SELECT 
   	   id_carne, count(id_cobranca) as faturas_abertas
   	FROM
   	   cbtb_faturas
   	WHERE
   	   status = 'A'
   	   AND id_carne is not null
   	GROUP BY
   	   id_carne
   	   
   
   ) f ON f.id_carne = c.id_carne
   
WHERE
   c.status = 'A' AND ctt.status = 'A' AND ci.id_carne is null AND c.valor > 0 
			";
			
			if( $id_carne ) {
				$q .= " AND id_carne = $id_carne ";
			}
			
			$q .= " ORDER BY c.data_geracao ";

			return($this->bd->obtemRegistros($q));
			   
			   
		}
		
	}
?>
