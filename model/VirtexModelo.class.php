<?

	/**
	 * Classe base para criação de Modelos (negocio)
	 */
	class VirtexModelo {
		protected $persiste;
		
		public function __construct() {
			$this->persiste = new VirtexPersiste();
		}
		
		public static function &factory($modelo) {
			VirtexPersiste::init();
			switch( $modelo ) {
				case 'clientes':
					return new MODELO_Clientes();
					break;
				case 'equipamentos':
					return new MODELO_Equipamentos();
					break;
				case 'preferencias':
					return new MODELO_Preferencias();
					break;
				case 'produtos':
					return new MODELO_Produtos();
					break;
				case 'cobranca':
					return new MODELO_Cobranca();
					break;
				case 'administradores':
					return new MODELO_Administracao();
					break;
				case 'spool':
					return new MODELO_Spool();
					break;
				default:
					throw new ExcecaoModeloInexistente();
					
			}
			
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
		
	}



?>
