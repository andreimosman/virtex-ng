<?

	/**
	 * Classe de gerencia de PPP (pppoe e pptp)
	 *
	 * /etc/ppp/ppp.linkup:
	 *
	 *
	 * pppoe-in:
	 *  ! /usr/local/bin/php /mosman/virtex/app/bin/vtx-ppp.php -U -i INTERFACE -a HISADDR -u USER -p PROCESSID
	 * 
	 *
	 * /etc/ppp/ppp.linkdown:
	 *
	 *
	 * pppoe-in:
	 *  ! /usr/local/bin/php /mosman/virtex/app/bin/vtx-ppp.php -D -i INTERFACE -u USER 
	 *
	 */
	class VAPPPPP extends VirtexApplication {
	

		public static $FW_SUB_BASERULE			= 2000;
		public static $FW_IP_BASERULE			= 10000;
		public static $FW_IP_BASEPIPE_IN		= 18000;
		public static $FW_IP_BASEPIPE_OUT		= 26000;

		public static $FW_PPPoE_BASERULE		= 34000;
		public static $FW_PPPoE_BASEPIPE_IN		= 42000;
		public static $FW_PPPoE_BASEPIPE_OUT	= 50000;
		
		protected $atuador;



		/**
		 * Opções de execução.
		 */

		protected $who;
		protected $linkup;
		protected $linkdown;
		protected $kick;
		protected $rc;

		/**
		 * Parâmetros gerais.
		 */
		protected $username;
		protected $hisaddr;
		protected $interface;
		protected $pid;
		
		// Modelos
		protected $contas;
		protected $equipamentos;
		protected $preferencias;
		
		// Preferencias
		protected $dominio_padrao;
		
		public function __construct() {
			parent::__construct();

			$this->atuador = new Atuador();


			$this->contas			= VirtexModelo::factory('contas');
			$this->equipamentos		= VirtexModelo::factory('equipamentos');
			$this->preferencias		= VirtexModelo::factory('preferencias');
			
			$prefs = $this->preferencias->obtemPreferenciasGerais();
			
			$this->dominio_padrao	= $prefs["dominio_padrao"];


		}
		
		protected function selfConfig() {
			$this->_shortopts	= "WUDKRIi:a:u:p:";


			$this->who 				= 0;
			$this->kick				= 0;
			$this->linkup			= 0;
			$this->linkdown			= 0;
			$this->rc				= 0;
			$this->info				= 0;

			$this->username			= "";
			$this->hisaddr 			= "";
			$this->interface		= "";
			$this->pid				= 0;

		}
		
		protected function configuraExecucao() {
			// Configura a execução com base nos parametros recebidos.
			
			/**
			 * Varre as opções e linha de comando
			 */
			foreach($this->options as $op) {
				$opcao = @$op[0];
				$param = @$op[1];
			
				
				switch($opcao) {
				
					/**
					 * Opções de Execução.
					 */
				
					case 'W':
						$this->who = 1;
						break;
					
					case 'U':
						$this->linkup = 1;
						break;
					
					case 'D':
						$this->linkdown = 1;
						break;
						
					case 'K':
						$this->kick = 1;
						break;
						
					case 'R':
						$this->rc = 1;
						break;
						
					case 'I':
						$this->info = 1;
						break;
					
					/**
					 * Parâmetros Gerais.
					 */
					
					case 'u':
						$this->username = $param;
						break;
					
					case 'a':
						$this->hisaddr = $param;
						break;
						
					case 'i':
						$this->interface = $param;
						break;
						
					case 'p':
						// Somente para UP
						$this->pid = $param;
						break;
						
				}
				
				
			}
			
			
		}
		
		/**
		 * Execução do aplicativo.
		 */
		public function executa() {
			$this->configuraExecucao();
			
			if( $this->info ) {
				return($this->print_info());
			}
			
			if( $this->who ) {
				return($this->print_who());
			}
			
			if( $this->kick ) {
				return($this->do_kick());
			}
			
			if( $this->rc ) {
				return($this->do_rc());
			}
			
			
			if( ($this->linkup && $this->linkdown) || (!$this->linkup && !$this->linkdown) ) {
				// É preciso ter SOMENTE linkup ou linkdown definido, não pode ser ambos ou nenhum.
				echo "\n    APLICATIVO NÃO CONFIGURADO CORRETAMENTE !!!\n\n";
				return(-1);
			}
			
			
			return($this->do_link());
			
		}
		
		public function print_info() {
			return(0);
		}
		
		public function print_who() {
			return(0);
		}
		
		public function do_kick() {
			return(0);
		}
		
		public function do_rc() {
			return(0);
		}
		
		public function do_link() {

			if( 
				($this->linkup && (!$this->interface || !$this->username || !$this->hisaddr || !$this->pid)) ||
				($this->linkdown && (!$this->username || !$this->interace))
			) {
				echo "\n     O APLICATIVO NÃO RECEBEU DADOS SUFICIENTES !!!\n\n";
				return(-1);
			}
			
			try {
				$infoUsuario = $this->contas->obtemContaPeloUsername($this->username,$this->dominio_padrao,"BL");
				if( !count($infoUsuario) ) throw new Exception("Conexão não autorizada.");
				$infoNAS = $this->equipamentos->obtemNAS($infoUsuario["id_nas"]);
				if( !count($infoNAS) ) throw new Exception("Conexão nÃo autorizada.");


				if( $infoNAS["tipo_nas"] == "I" ) {
					// NAS DO TIPO IP. ERRO.
					throw new Exception("Conexão não autorizada.");
				} elseif( $infoNAS["tipo_nas"] == "P" ) {
					// echo "P";
					if( !$this->pppoe[ $infoUsuario["id_nas"] ] ) {
						// NAS NÃO TÁ HABILITADO
						throw new Exception("Conexão não autorizada.");
					}					
					
				} elseif( $infoNAS["tipo_nas"] == "T" ) {
					if( !$this->pptp[ $infoUsuario["id_nas"] ] ) {
						// NAS NÃO TÁ HABILITADO
						throw new Exception("Conexão não autorizada.");
					}
				}
				
				$this->atuador->processaInstrucaoBandaLarga($infoUsuario["id_nas"],$this->interface,@$infoUsuario["id_conta"],($this->linkup ? MODELO_Spool::$ADICIONAR : MODELO_Spool::$REMOVER),@$infoUsuario["username"],$infoUsuario["ipaddr"],@$infoUsuario["mac"],@$infoNAS["padrao"],@$infoUsuario["upload_kbps"],@$infoUsuario["download_kbps"],$this->fator[$id_nas]);
				
			} catch(Exception $e) {
				echo "\n    " .  strtoupper($e->getMessage()) . " !!!\n\n";
				return(-1);
			}
			
			return(0);
			
		}
		
	}
	
?>
