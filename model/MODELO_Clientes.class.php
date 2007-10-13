<?

	/**
	 * Modelo de Clientes (camada de neg�cios)
	 * 
	 * Considera��es l�gicas:
	 *
	 * - Cliente � obrigatoriamente pessoa f�sica ou jur�dica
	 * - Pessoa f�sica tem CPF
	 * - Pessoa jur�dica tem CNPJ
	 * - Somente um cliente por CPF ou CNPJ
	 * - Valida��o de CPF e CNPJ
	 * - Formata��o da exibi��o para os campos dos telefones, cpf e cnpj
	 * - Insert somente de n�meros para cpf, cnpj e telefones.
	 * - Criar v�rios m�todos "obtem" para cada consulta espec�fica do sistema.(ex: obtemPeloID, obtemPelosDocumentos)
	 */
	class MODELO_Clientes extends VirtexModelo {
		protected $cltb_cliente;
		protected $cftb_cidade;
		
		/**
		 * Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->cltb_cliente = VirtexPersiste::factory("cltb_cliente");
			$this->cftb_cidade  = VirtexPersiste::factory("cftb_cidade");
		}
		
		protected function _stripCPF_CNPJ($cpf_cnpj) {
			return(str_replace("/","",str_replace(".","",str_replace("-","",str_replace(" ","",$cpf_cnpj)))));
		}
		
		protected function verificaCPF_CNPJ($cpf_cnpj,$id_cliente=0) {
			
			$filtro = array("cpf_cnpj" => $this->_stripCPF_CNPJ($cpf_cnpj));
			if( $id_cliente ) {
				$filtro["id_cliente"] = "!=:$id_cliente";
			}

			$cliente = $this->cltb_cliente->obtemUnico($filtro);

			return(@$cliente["id_cliente"]);

		}
		
		/**
		 * Clientes por Cidade
		 */
		
		public function obtemClientesPorCidade($id_cidade) {
			$filtro = array("id_cidade" => $id_cidade);
			return($this->cltb_cliente->obtem($filtro));
		}
		
		public function countClientesPorCidade() {
			return($this->cltb_cliente->countClientesPorCidade());
		}
		
		/**
		 * Cadastra um cliente
		 */
		public function cadastra($dados) {
			/**
			 * N�o � permitido duplicidade do campo cpf_cnpj
			 */			
			if( $this->verificaCPF_CNPJ($dados["cpf_cnpj"]) ) {
				throw new ExcecaoModelo(255,"J� existe outro cliente cadastrado com este CPF/CNPJ");
			}
			
			$dados["cpf_cnpj"] = $this->_stripCPF_CNPJ($dados["cpf_cnpj"]);

			return($this->cltb_cliente->insere($dados));
		}
		
		/**
		 * Altera os dados de um cliente
		 */
		public function altera($id,$dados) {
			/**
			 * N�o � permitido duplicidade do campo cpf_cnpj
			 */
			if( $id != 1 ) {		// N�o valida CPF_CNPJ p/ id_provedor
				if( $this->verificaCPF_CNPJ($dados["cpf_cnpj"],$id) ) {
					throw new ExcecaoModelo(255,"J� existe outro cliente cadastrado com este CPF/CNPJ");
				}

				
			}
			if( @$dados["cpf_cnpj"] ) {
				$dados["cpf_cnpj"] = $this->_stripCPF_CNPJ($dados["cpf_cnpj"]);
			}
			$this->cltb_cliente->altera($dados,array("id_cliente" => $id));
		}
		
		/**
		 * Obtem um ou mais clientes de acordo com a condicao
		 *
		 *
		 * TODO: 
		 *  - Valida��o das condi��es
		 *  - Exception
		 */
		public function obtem($condicao) {
			$retorno = $this->cltb_cliente->obtem($condicao);
			return($retorno);
		}
		
		public function obtemPeloId($id) {
			$retorno = $this->obtem(array("id_cliente"=>$id));
			if( count($retorno) ) {
				$retorno = $retorno[0];
			}
			return($retorno);
		}
		
		public function obtemPeloNome($nome) {
			return $this->obtem(array("nome_razao"=>'%:%'.$nome.'%'));
		}
		
		public function obtemPelosDocumentos($docto) {
			$condicao["*OR*0"] = array("cpf_cnpj" => $this->_stripCPF_CNPJ($docto), "rg_inscr" => $docto);
			return($this->obtem($condicao));
		}
		
		public function obtemUltimos($maxClientes=15) {
			return($this->cltb_cliente->obtem(array(),"id_cliente DESC",$maxClientes));
		}
		
		
		
		public function pesquisaClientesPorConta($textoPesquisa) {
			$contas = VirtexModelo::factory("contas");
			return($contas->pesquisaClientesPorContas($textoPesquisa));
		}
		
		
		
		
		
		
		
		
		
		public function listaTipoPessoa() {
			return $this->cltb_cliente->listaTipoPessoa();
		}

		public function listaStatusCliente() {
			return $this->cltb_cliente->listaStatusCliente();
		}

		public function listaDiaPagamento() {
			return $this->cltb_cliente->listaDiaPagamento();
		}
		
		public function listaCidades() {
			return $this->cftb_cidade->obtemCidadesDisponiveis();
		}
		
	}

?>
