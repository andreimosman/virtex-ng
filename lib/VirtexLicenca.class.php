<?

	// Defini��o da Base de C�lculo da Licen�a
	if(!defined("VA_SALT")) define("VA_SALT","VA0200");

	/**
	 * Sistema de Licenciamento do VirtexAdmin
	 *
	 */
	class VirtexLicenca extends MLicenca {
	
		public function __construct() {
			parent::__construct("etc/virtex.lic",VA_SALT);
		}
		
		protected function verificaExpiracao($campo) {
			/**
			 * Se a licen�a n�o for valida ou n�o tiver a informa��o da expira��o retorna que expirou.
			 */
			if(!$this->isValid() || !@$campo) return true;
			@list($ano,$mes,$dia) = explode("-",chop(@$campo));

			// Se nXo tiver o dia, mXs ou ano retorna que expirou.
			if( !$dia || !$mes || !$ano ) return true;
			@list($a,$m,$d) = explode("-",date("Y-m-d"));

			$t_campo   = mktime(0,0,0,$mes,$dia,$ano);
			$t_sistema = mktime(0,0,0,$m,$d,$a);
			return( $t_sistema >= $t_campo );
		}


		/**
		 * Verifica se a licen�a expirou.
		 */
		public function expirou() {
			return($this->verificaExpiracao(@$this->lic["geral"]["expira_em"]));
		}
		
		/**
		 * Verifica se a licen�a congelou.
		 */
		public function congelou() {
			return($this->verificaExpiracao(@$this->lic["geral"]["congela_em"]));
		}

	
	
	}

