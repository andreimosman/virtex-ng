<?

	/**
	 * Modelo para manipula��o de instru��es via spool.
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
		public static $HOSPEDAGEM		= "H";
		
		/**
		 * Opera��es
		 */
		public static $ADICIONAR		= "a";
		public static $REMOVER			= "x";
		
		/**
		 * Status
		 */
		public static $ST_AGUARDANDO 	= "A";
		public static $ST_ERRO			= "ERR";
		public static $ST_OK			= "OK";
		
		/**
		 * Separador de parametros
		 */
		public static $SEPARADOR_PARAMETROS	= "::";
		
		
		
		
		
		
		public function __construct() {
			parent::__construct();		
			$this->sptb_spool = VirtexPersiste::factory("sptb_spool");
		}

		/**
		 * Adiciona uma instru��o de configura��o de rede de infraestrutura.
		 *
		 */
		public function adicionaRedeInfraestrutura($id_nas,$id_rede,$rede) {
			return($this->insereInstrucaoAdicao($id_nas,$self::$INFRAESTRUTURA,$id_rede,$rede));
		}
		
		/**
		 * Adiciona uma instru��o de remo��o de rede de infraestrutura.
		 */
		public function removeRedeInfraestrutura($id_nas,$id_rede,$rede) {
			return($this->insereInstrucaoRemocao($id_nas,$self::$INFRAESTRUTURA,$id_rede,$rede));
		}
		
		/**
		 * Adiciona uma instru��o de configura��o de uma conta banda larga.
		 * Usado somente para contas TCP/IP.
		 */
		public function adicionaContaBandaLarga($id_nas,$id_conta,$username,$endereco,$mac,$upload,$download,$padrao="") {
			$parametros = implode(self::$SEPARADOR_PARAMETROS,array($username,$endereco,$mac,$padrao,$upload,$download));
			return($this->insereInstrucaoAdicao($id_nas,$self::$BANDA_LARGA,$id_conta,$parametros));
		}
		
		/**
		 * Adiciona uma instru��o de remo��o de uma conta banda larga.
		 * Usado somente para contas TCP/IP.
		 */
		public function removeContaBandaLarga($id_nas,$id_conta,$username,$endereco,$mac,$padrao="") {
			$parametros = implode(self::$SEPARADOR_PARAMETROS,array($username,rede,mac,$padrao));
			return($this->insereInstrucaoRemocao($id_nas,self::$BANDA_LARGA,$id_conta,$parametros));
		}
		
		/**
		 * Adiciona uma instru��o de configura��o de e-mail.
		 */
		public function adicionaContaEmail($servidor_email,$id_conta,$username,$dominio) {
			$parametros = implode(self::$SEPARADOR_PARAMETROS,array($username,$dominio);
			return($this->insereInstrucaoAdicao($servidor_email,$self::$EMAIL,$id_conta,$parametros));
		}
		
		/**
		 * Adiciona uma instru��o de remo��o de conta de e-mail.
		 */
		public function removeContaEmail($servidor_email,$id_conta,$username,$dominio) {
			$parametros = implode(self::$SEPARADOR_PARAMETROS,array($username,$dominio);
			return($this->insereInstrucaoRemocao($servidor_email,$self::$EMAIL,$id_conta,$parametros));
		}
		
		/**
		 * Adiciona uma instru��o de configura��o de hospedagem.
		 */
		public function adicionaContaHospedagem($servidor_hospedagem,$id_conta,$username,$dominio="",$ns1="",$ns2="", $ns2_master="") {
			$tipo_hospedagem = $dominio ? "D" : "U";	// Hospedagem pode ser de dom�nio ou de usu�rio.
			if( $hospedagem == "D" ) {
				// Hospedagem tipo dom�nio.
				if( $ns1 ) {
					// Cadastrar instru��o de DNS prim�rio
					if( $ns2 && !$ns2_master ) {
						// Se tiver configurado NS2 e n�o definido o DNS MASTER, o NS1 ser� o DNS MASTER do NS2
						$ns2_master = $ns1;
					}
					$this->adicionaDNSPrimario($ns1,$id_conta,$dominio);
				}
				
				// NS2 Precisa de MASTER.
				if( $ns2 && $ns2_master ) {
					// Cadastrar instru��o de DNS secund�rio
					$this->adicionaDNSSecundario($ns2,$id_conta,$dominio,$ns2_master);				
				}
				
				// Montar parametros p/ hospedagem de dom�nio.
				$parametros = implode(self::$SEPARADOR_PARAMETROS,array($tipo_hospedagem,$dominio));
			} else {
				// Montar parametros p/ hospedagem de usu�rio.
				$parametros = implode(self::$SEPARADOR_PARAMETROS,array($tipo_hospedagem,$username));
			}
			
			return($this->insereInstrucaoAdicao($servidor_hospedagem,$self::$HOSPEDAGEM,$id_conta,$parametros));

		}
		
		/**
		 * Adiciona uma instru��o p/ remo��o de conta de hospedagem.
		 */
		public function removeContaHospedagem($servidor_hospedagem,$id_conta,$username,$dominio="",$ns1="",$ns2="") {
			$tipo_hospedagem = $dominio ? "D" : "U";	// Hospedagem pode ser de dom�nio ou de usu�rio.
			if( $hospedagem == "D" ) {

				if( $ns2 ) {
					$this->removeDNSSecunradio($ns2,$id_conta,$dominio);
				}

				if( $ns1 ) {
					$this->removeDNSPrimario($ns1,$id_conta,$dominio);
				}

				// Montar parametros p/ hospedagem de dom�nio.
				$parametros = implode(self::$SEPARADOR_PARAMETROS,array($tipo_hospedagem,$dominio));
			} else {
				// Montar parametros p/ hospedagem de usu�rio.
				$parametros = implode(self::$SEPARADOR_PARAMETROS,array($tipo_hospedagem,$username));			
			}
			
			return($this->insereInstrucaoRemocao($servidor_hospedagem,$self::$HOSPEDAGEM,$id_conta,$parametros));
		}
		
		/**
		 * Adiciona instru��o de configura��o de DNS Prim�rio.
		 */
		public function adicionaDNSPrimario($servidor_dns,$id_conta,$dominio) {
			$parametros = $dominio;
			return($this->insereInstrucaoAdicao($servidor_dns,$self::$DNS_PRIMARIO,$id_conta,$parametros));
		}
		
		/**
		 * Adiciona uma instru��o para remo��o de DNS Prim�rio.
		 */
		public function removeDNSPrimario($servidor_dns,$id_conta,$dominio) {
			$parametros = $dominio;
			return($this->insereInstrucaoRemocao($servidor_dns,$self::$DNS_PRIMARIO,$id_conta,$parametros));
		}
		
		/**
		 * Adiciona instru��o de configura��o de DNS Secund�rio.
		 */
		public function adicionaDNSSecundario($servidor_dns,$id_conta,$dominio,$master) {
			$parametros = implode(self::$SEPARADOR_PARAMETROS,array($dominio,$master));
			return($this->insereInstrucaoAdicao($servidor_dns,$self::$DNS_SECUNDARIO,$id_conta,$parametros));
		}
		
		/**
		 * Adiciona uma instru��o para remo��o de DNS Secund�rio.
		 */
		public function removeDNSSecundario($servidor_dns,$id_conta,$dominio) {
			$parametros = $dominio;
			return($this->insereInstrucaoRemocao($servidor_dns,$self::$DNS_SECUNDARIO,$id_conta,$parametros));
		}

		/**
		 * Adiciona uma instru��o gen�rica na spool
		 */
		protected function insereInstrucao($destino,$tipo,$id,$op,$parametros) {
			$dados = array(
							"destino" => $destino,
							"tipo" => $tipo,
							"id" => $id,
							"op" => $op,
							"parametros" => $parametros,
							"status" => self::$ST_AGUARDANDO
						);
			return($this->sptb_spool->insere($dados));
		}

		/**
		 * Adiciona uma instru��o de Adi��o na spool.
		 */
		protected function insereInstrucaoAdicao($destino,$tipo,$id,$parametros) {
			return($this->insereInstrucao($destino,$tipo,$id,self::$ADICIONAR,$parametros));
		}
		
		/**
		 * Adiciona uma instru��o de Remo��o na spool.
		 */
		protected function insereInstrucaoRemocao($destino,$tipo,$id,$parametros) {
			return($this->insereInstrucao($destino,$tipo,$id,self::$REMOVER,$parametros));
		}
		
		/**
		 * Lista a spool
		 */
		
		public function obtemInstrucoesSpool($target,$tipo,$status="") {
			$filtro = array("target" => $target, "tipo" => $tipo);
			if( $status ) {
				$filtro["status"] = $status;
			}
			
			$fila = $this->sptb_spool->obtem($filtro);
			
			for($i=0;$i<count($fila);$i++) {
				$parametros = $this->decompoeParametros($tipo,$op,$fila[$i]["parametros"]);
				$fila[$i]["parametros"] = $parametros;
			}
			
			return($fila);

		}
		
		/**
		 * Decomp�e os parametros em uma matriz associativa.
		 * (este processo joga a responsabilidade da interpreta��o dos parametros p/ a spool ao inv�s da aplica��o).
		 */
		protected function $this->decompoeParametros($tipo,$op,$parametros) {
			$param = explode(self::$SEPARADOR_PARAMETROS,$parametros);
			$retorno = array();
			
			switch($tipo) {
				case self::$INFRAESTRUTURA:
					$retorno["rede"] = @$param[1];
					break;
				
				case self::$BANDA_LARGA:
					$retorno["username"] 		= @$param[0];
					$retorno["endereco"] 		= @$param[1];
					$retorno["mac"] 			= @$param[2];
					$retorno["padrao"]			= @$param[3]
					
					if( $op == self::$ADICIONAR ) {
						$retorno["upload"]		= @$param[4];
						$retorno["download"]	= @$param[5];
					}
					
					break;
					
				case self::$EMAIL:
					$retorno["username"] 		= @$param[0];
					$retorno["dominio"] 		= @$param[1];
					break;
				
				case self::$HOSPEDAGEM:
					$retorno["tipo_hospedagem"]	= @$param[0];
					$retorno[ ($retorno["tipo_hospedagem"] == "D" ? "dominio" : "username") ] = @$param[1];
					break;
					
				case self::$DNS_PRIMARIO:
					$retorno["dominio"]			= @$param[0];
					break;
					
				case self::$DNS_SECUNDARIO:
					$retorno["dominio"]			= @$param[0];
					if( $op == self::$ADICIONAR ) {
						$retorno["master"]		= @$param[1];
					}
					break;
			
			
			}
			
			return($retorno);
		
		}
	
	}

?>
