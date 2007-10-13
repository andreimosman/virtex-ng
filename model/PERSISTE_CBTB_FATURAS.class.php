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

		
		public function enumStatusFatura(){
			return array( 
							PERSISTE_CBTB_FATURAS::$ABERTA => "Aberta",
							PERSISTE_CBTB_FATURAS::$PAGA => "Paga",
							PERSISTE_CBTB_FATURAS::$PARCIAL => "Parcial",
							PERSISTE_CBTB_FATURAS::$ESTORNADA => "Estornada",
							PERSISTE_CBTB_FATURAS::$CANCELADA => "Cancelada" );
		}
		
	}

?>