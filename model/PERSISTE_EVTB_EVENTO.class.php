<?

	/**
	 * adtb_admin
	 * Cadastro de usuários do sistema
	 */
	class PERSISTE_EVTB_EVENTO extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id_evento", "datahora", "tipo", "id_admin", "natureza", "ipaddr", "confirmado_por_senha", "descricao","id_conta","id_cliente_produto","id_cobranca");
			$this->_chave           = "id_evento";
			$this->_ordem           = "datahora desc";
			$this->_tabela          = "evtb_evento";
			$this->_sequence        = "evsq_id_evento";
		}

	

		public function obtemEventos($filtro=array(),$limite=20) {
			$sql = "
			SELECT
			   e.id_evento, e.datahora, e.tipo, e.id_admin, e.confirmado_por_senha, e.natureza, 
			   e.ipaddr, e.descricao, e.id_conta, e.id_cobranca, e.id_cliente_produto,
			   e.id_evento, 
			   ad.id_admin as admin_id_admin, ad.admin as admin_admin, ad.status as admin_status, 
			   ad.nome as admin_nome, ad.email as admin_email,
			   ctt.data_contratacao as contrato_data_contratacao,
			   ctt.valor_produto as contrato_valor_produto,
			   ctt.status as contrato_status,
			   cl.nome_razao as cliente_nome_razao,
			   cl.id_cidade as cliente_id_cidade, 
			   cid.cidade as cliente_cidade, cid.uf as cliente_uf,
			   cl.id_cliente, 

			   f.id_cobranca as fatura_id_cobranca,
			   f.data as fatura_data,
			   f.descricao as fatura_descricao, 
			   f.valor as fatura_valor, 
			   f.status as fatura_status, 
			   f.desconto as fatura_desconto, 
			   f.acrescimo as fatura_acrescimo,
			   cnt.username as conta_username,
			   cnt.dominio as conta_dominio,
			   cnt.tipo_conta as conta_tipo_conta,
			   cnt.status as conta_status 
			FROM
			   evtb_evento e
			   LEFT OUTER JOIN adtb_admin ad ON e.id_admin = ad.id_admin
			   LEFT OUTER JOIN cbtb_cliente_produto cp ON e.id_cliente_produto = cp.id_cliente_produto
			   LEFT OUTER JOIN cbtb_contrato ctt ON e.id_cliente_produto = ctt.id_cliente_produto
			   LEFT OUTER JOIN prtb_produto p ON p.id_produto = ctt.id_produto
			   LEFT OUTER JOIN cltb_cliente cl ON cp.id_cliente = cl.id_cliente
			   LEFT OUTER JOIN cftb_cidade cid ON cl.id_cidade = cid.id_cidade
			   LEFT OUTER JOIN cbtb_faturas f ON e.id_cobranca = f.id_cobranca
			   LEFT OUTER JOIN cntb_conta cnt ON e.id_conta = cnt.id_conta
			";

			$nFiltro = array();
			// echo "<pre>"; 
			// print_r($filtro);
			while(list($vr,$vl) = each($filtro)) {
				if( !strstr($vr,'*') ) {
					$vr = "e." . $vr;
				}

				$nFiltro[$vr] = $vl;
			}

			if( !empty($nFiltro) ) {
				$sql .= $this->_where($nFiltro);
			}
			
			$sql .= " ORDER BY e.id_evento DESC ";
			
			// echo "LIMITE: $limite\n";
			
			if( $limite ) {
				$sql .= " LIMIT $limite ";
			}

			// print_r($sql);
			return($this->bd->obtemRegistros($sql));
		}
		


	}


