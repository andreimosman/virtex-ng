<?

	/**
	 * Modelo para manipulação de instruções via spool.
	 */
	class MODELO_Spool extends VirtexModelo {
		
		protected $sptb_spool;
		
		/**
		 * Tipos de spool.
		 */

		public static $EMAIL 			= "E";
		public static $BANDA_LARGA 		= "BL";
		public static $INFRAESTRUTURA	= "IF";
		public static $DNS_PRIMARIO		= "N1";
		public static $DNS_SECUNDARIO 	= "N2";
		
		/**
		 * Operações
		 */
		public static $ADICIONAR		= "a";
		public static $REMOVER		= "x";
		
		/**
		 * Status
		 */
		public static $ST_AGUARDANDO 	= "A";
		public static $ST_ERRO			= "ERR";
		public static $ST_OK			= "OK";
		
		
		
		
		
		
		public function __construct() {
			parent::__construct();		
			$this->sptb_spool = VirtexPersiste::factory("sptb_spool");
		}
		

		/**
		 * Adiciona uma instrução de configuração de rede de infraestrutura.
		 *
		 */
		public function adicionaRedeInfraestrutura($id_nas,$id_rede,$rede) {
			$dados = array("destino" => $id_nas, "tipo" => self::$INFRAESTRUTURA, "id" => $id_rede, "op" => self::$ADICIONAR, "parametros" => $rede, "status" => self::$ST_AGUARDANDO);
			$this->sptb_spool->insere($dados);
		}
		
		/**
		 * Adiciona uma instrução de remoção de rede de infraestrutura.
		 * (função dummy!!! estará disponível em versões futuras)
		 */
		public function removeRedeInfraestrutura($id_nas,$id_rede,$rede) {
		
		}
		
		
		
		
		
	
	
	
	
	}

?>
