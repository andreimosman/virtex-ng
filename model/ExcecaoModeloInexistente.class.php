<?
	class ExcecaoModeloInexistente extends ExcecaoModelo {
		public function __construct() {
			parent::__construct(255,"Modelo desconhecido");
		}
	
	}
