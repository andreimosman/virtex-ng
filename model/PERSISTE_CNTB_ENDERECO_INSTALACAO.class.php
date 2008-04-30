<?

	class PERSISTE_CNTB_ENDERECO_INSTALACAO extends VirtexPersiste {
		
		public function __construct($bd=null) {
			parent::__construct();
			
			$this->_campos		= array("id_endereco_instalacao","id_conta","endereco","complemento","bairro","id_cidade","cep","id_cliente", "id_condominio_instalacao", "id_bloco_instalacao", "apto_instalacao");
			$this->_chave 		= "id_endereco_instalacao";
			$this->_ordem		= "id_endereco_instalacao DESC";
			$this->_tabela		= "cntb_endereco_instalacao";
			$this->_sequence	= "cnsq_id_endereco_instalacao";
			
			$this->_filtros		= array("id_endereco_instalacao" => "numeric", "id_conta" => "numeric", "id_cidade" => "numeric", "id_cliente" => "numeric");
		
		}
		
		public function obtemEnderecoInstalacaoReferenciado($id_conta) {
		
			$sql  = "select endc.*, cnd.nome as conodminio_nome, cndb.nome, cid.cidade, cid.uf as bloco_nome from cntb_endereco_instalacao endc ";
			$sql .= "LEFT OUTER JOIN catb_condominio cnd ON endc.id_condominio_instalacao = cnd.id_condominio ";
			$sql .= "LEFT OUTER JOIN catb_condominio_bloco cndb ON endc.id_bloco_instalacao = cndb.id_bloco ";
			$sql .= "LEFT OUTER JOIN cftb_cidade cid ON endc.id_cidade = cid.id_cidade ";
			$sql .= "WHERE endc.id_conta = '$id_conta' ORDER BY id_endereco_instalacao desc limit 1 ";
	
			return($this->bd->obtemUnicoRegistro($sql));
		
		}
	
	
	
	}
	
?>