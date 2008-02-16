<?

	/**
	 * Modelo de cadastro (camada de negócios)
	 *
	 * - Gerência de Equipamentos
	 * - Gerência de Relatórios
	 * - Gerência de Administradores
	 * - Gerência de Planos
	 * - Gerência de Serviços
	 * - Gerência de Produtos
	 * - Gerência de Centros de Custo
	 * - Gerência de Plano de Contas
	 * - Gerência de Condomínios
	 */

	class MODELO_Cadastro extends VirtexModelo {

		//Situação do condomínio
		public static $SITUACAO_INSTALADO			=	"I";
		public static $SITUACAO_INSTALADO_PARCIAL	= 	"P";
		public static $SITUACAO_NAO_INSTALADO		= 	"N";
		
		
		protected $catb_condominio;
		protected $catb_condominio_bloco;

		public function __construct() {
			parent::__construct();
			$this->catb_condominio 						= VirtexPersiste::factory("catb_condominio");
			$this->catb_condominio_bloco				= VirtexPersiste::factory("catb_condominio_bloco");
		}
		
		
		/**
		 * obtemSituacoes
		 * 		Retorna um array de possíveis situações do condomínio
		 */
		public function obtemSituacoes() {
		
			$retorno = array();
			
			$retorno[self::$SITUACAO_INSTALADO] 			= "Instalado";
			$retorno[self::$SITUACAO_INSTALADO_PARCIAL] 	= "Instalado Parcial";
			$retorno[self::$SITUACAO_NAO_INSTALADO] 		= "Não Instalado";
			
			return $retorno;
		
		}
		
		/**********************************************************************************
		 ***																			***
		 ***				FUNÇÕES DE CONDOMÍNIO E BLOCO DE CONDOMÍNIO					***
		 ***																			***
		 **********************************************************************************/
		
		/**
		 *	CONDOMÍNIO
		 */
		public function cadastrarCondominio($nome, $endereco, $complemento, $bairro, $id_cidade, $cep, $fone, $quantidade_edificios, $situacao, $data_instalacao, $data_ativacao, $sindico_nome, $sindico_fone, $zelador_nome, $zelador_fone, $observacoes) {
			return($this->catb_condominio->cadastrarCondominio($nome, $endereco, $complemento, $bairro, $id_cidade, $cep, $fone, $quantidade_edificios, $situacao, $data_instalacao, $data_ativacao, $sindico_nome, $sindico_fone, $zelador_nome, $zelador_fone, $observacoes));
		}
		
		public function alterarCondominio ($id_condominio, $nome, $endereco, $complemento, $bairro, $id_cidade, $cep, $fone, $quantidade_edificios, $situacao, $data_instalacao, $data_ativacao, $sindico_nome, $sindico_fone, $zelador_nome, $zelador_fone, $observacoes) {
			$this->catb_condominio->alterarCondominio ($id_condominio, $nome, $endereco, $complemento, $bairro, $id_cidade, $cep, $fone, $quantidade_edificios, $situacao, $data_instalacao, $data_ativacao, $sindico_nome, $sindico_fone, $zelador_nome, $zelador_fone, $observacoes);
		}
		
		public function obtemCondominio($id_condominio=NULL, $ativo=false) {
			return($this->catb_condominio->obtemCondominio($id_condominio, $ativo));
		}
		
		
		/**
		 *	BLOCO/PRÉDIO
		 */		
		public function cadastrarCondominioBloco ($id_condominio, $nome, $numero_andares, $apartamentos_andar, $total_apartamentos, $situacao, $id_pop, $observacoes) {
			return $this->catb_condominio_bloco->cadastrarCondominioBloco ($id_condominio, $nome, $numero_andares, $apartamentos_andar, $total_apartamentos, $situacao, $id_pop, $observacoes);
		}
		
		public function alterarCondominioBloco ($id_bloco, $nome, $numero_andares, $apartamentos_andar, $total_apartamentos, $situacao, $id_pop, $observacoes){
			$this->catb_condominio_bloco->alterarCondominioBloco ($id_bloco, $nome, $numero_andares, $apartamentos_andar, $total_apartamentos, $situacao, $id_pop, $observacoes);
		}

		public function obtemCondominioBloco($id_condominio=NULL, $id_bloco=NULL, $ativo=false) {
			return $this->catb_condominio_bloco->obtemCondominioBloco($id_condominio, $id_bloco, $ativo);
		}

	}

?>