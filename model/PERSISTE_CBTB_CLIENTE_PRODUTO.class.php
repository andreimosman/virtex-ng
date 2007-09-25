<?
	class PERSISTE_CBTB_CLIENTE_PRODUTO extends VirtexPersiste {
	
		public function __construct($bd=null) {
  		parent::__construct();
  		
			$this->_campos 		= array("id_cliente_produto", "id_cliente", "id_produto", "dominio", "excluido");
			$this->_chave 		= "id_cliente_produto";
			$this->_ordem		= "";
			$this->_tabela		= "cbtb_cliente_produto";
			$this->_sequence	= "cbsq_id_cliente_produto";
			$this->_filtros 	= array("id_cliente_produto" => "number", "id_cliente" => "number", "id_produto" => "number", "excluído" => "bool");
		}


		public function obtemContratos ($id_cliente,$status="",$tipo="")
		{
			$q = "SELECT to_char (c.data_contratacao,'dd/mm/YYYY') as data_contratacao, 
				     c.vigencia, 
				     to_char (c.data_renovacao,'dd/mm/YYYY') as data_renovacao,      
				     c.valor_produto as valor,
				     p.nome as nome_produto,
				     c.tipo_produto,
				     c.status,
				     c.id_cliente_produto,
				     c.id_forma_pagamento
				FROM cbtb_cliente_produto cp 
			  INNER JOIN cbtb_contrato c ON cp.id_cliente_produto = c.id_cliente_produto
			  INNER JOIN prtb_produto p ON p.id_produto = cp.id_produto
			       WHERE cp.id_cliente = " . $this->bd->escape ($id_cliente);
			
			if( $status ) {
				$q .= " AND c.status = '".$status."' ";
			}
			
			if( $tipo ) {
				$q .= " AND p.tipo = '" . $tipo . "' ";
			}
   		
			$res = $this->bd->obtemRegistros ($q);
			return ($res);
		}
	}
?>