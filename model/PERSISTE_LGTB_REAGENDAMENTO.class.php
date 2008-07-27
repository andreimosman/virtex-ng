<?


	class PERSISTE_LGTB_REAGENDAMENTO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct();

			$this->_campos 		= array("id_reagendamento","id_cliente_produto","data","admin","data_reagendamento","data_para_reagendamento");
			$this->_chave 		= "id_reagendamento";
			$this->_ordem 		= "";
			$this->_tabela 		= "lgtb_reagendamento";
			$this->_sequence	= "lgtb_reagendamento_id_reagendamento_seq";
			$this->_filtros		= array("id_reagendamento" => "number", "id_cliente_produto" => "number", 
										"data"=>"date", "admin"=>"number", "data_reagendamento"=>"date", 
										"data_para_reagendamento" => "date" );

		}
		
		public function obtemReagendamento(){
		
			$sql  = "SELECT ";
			$sql .= "	r.id_reagendamento, r.data, r.data_reagendamento, r.data_para_reagendamento, r.id_cliente_produto, cl.nome_razao, ";
			$sql .= "	cl.id_cliente, r.id_cliente_produto, r.admin as cod_admin, ad.admin as admin, f.valor, c.username ";
			$sql .= "FROM ";
			$sql .= "	lgtb_reagendamento r INNER JOIN cbtb_cliente_produto cp ON (r.id_cliente_produto = cp.id_cliente_produto) ";
			$sql .= "   INNER JOIN cltb_cliente cl ON (cl.id_cliente = cp.id_cliente) ";
			$sql .= "   INNER JOIN adtb_admin ad ON (ad.id_admin = r.admin) ";
			$sql .= "   INNER JOIN cbtb_faturas f ON (f.id_cliente_produto = r.id_cliente_produto AND f.data = r.data) ";
			$sql .= "   INNER JOIN cntb_conta c ON (c.id_cliente_produto = r.id_cliente_produto) ";
			$sql .= "ORDER BY ";
			$sql .= "	r.id_reagendamento DESC ";

			return ($this->bd->obtemRegistros($sql));
		}
	
	
	}



?>
