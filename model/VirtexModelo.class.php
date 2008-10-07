<?

	/**
	 * Classe base para criação de Modelos (negocio)
	 */
	class VirtexModelo {
		protected $persiste;
		
		protected static $modelosInstanciados;
		
		public function __construct() {
			$this->persiste = new VirtexPersiste();
		}
		
		public static function &factory($modelo) {
			VirtexPersiste::init();
			
			if( !self::$modelosInstanciados ) {
				self::$modelosInstanciados = array();
			}
			
			if( !@self::$modelosInstanciados[$modelo] ) {

				switch( $modelo ) {
					case 'clientes':
						self::$modelosInstanciados[$modelo] = new MODELO_Clientes();
						break;
					case 'equipamentos':
						self::$modelosInstanciados[$modelo] = new MODELO_Equipamentos();
						break;
					case 'preferencias':
						self::$modelosInstanciados[$modelo] = new MODELO_Preferencias();
						break;
					case 'produtos':
						self::$modelosInstanciados[$modelo] = new MODELO_Produtos();
						break;
					case 'cobranca':
						self::$modelosInstanciados[$modelo] = new MODELO_Cobranca();
						break;
					case 'administradores':
						self::$modelosInstanciados[$modelo] = new MODELO_Administracao();
						break;
					case 'spool':
						self::$modelosInstanciados[$modelo] = new MODELO_Spool();
						break;
					case 'contas':
						self::$modelosInstanciados[$modelo] = new MODELO_Contas();
						break;
					case 'eventos':
						self::$modelosInstanciados[$modelo] = new MODELO_Eventos();
						break;
					case 'cadastro':
						self::$modelosInstanciados[$modelo] = new MODELO_Cadastro();
						break;
					case 'helpdesk': 
						self::$modelosInstanciados[$modelo] = new MODELO_Helpdesk();
						break;
					case 'radius':
						self::$modelosInstanciados[$modelo] = new MODELO_Radius();
						break;
					case 'caixa':
						self::$modelosInstanciados[$modelo] = new MODELO_FluxoCaixa();
						break;
					case 'backup':
						self::$modelosInstanciados[$modelo] = new MODELO_Backup();
						break;
					default:
						throw new ExcecaoModeloInexistente();

				}

			}
			
			return(self::$modelosInstanciados[$modelo]);
			
		}
		
		public function begin() {
			$this->persiste->begin();
		}
		
		public function commit() {
			$this->persiste->commit();
		}
		
		public function rollback() {
			$this->persiste->rollback();
		}
		
		//
	}


