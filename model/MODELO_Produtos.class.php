<?

	class MODELO_Produtos extends VirtexModelo {
		protected $prtb_produto;
		protected $prtb_produto_bandalarga;
		protected $prtb_produto_discado;
		protected $prtb_produto_hospedagem;
		
		public function __construct() {
			parent::__construct();
			$this->prtb_produto = VirtexPersiste::factory("prtb_produto");
			$this->prtb_produto_bandalarga = VirtexPersiste::factory("prtb_produto_bandalarga");
			$this->prtb_produto_discado = VirtexPersiste::factory("prtb_produto_discado");
			$this->prtb_produto_hospedagem = VirtexPersiste::factory("prtb_produto_hospedagem");
		}
		
		
		protected function obtemPlanos($filtro=array(),$tipo='') {
			$tipo = trim($tipo);
			switch($tipo){
				case 'BL':
					$r = $this->prtb_produto_bandalarga->obtem($filtro);
					break;
				case 'D':
					$r = $this->prtb_produto_discado->obtem($filtro);
					break;
				case 'H':
					$r = $this->prtb_produto_hospedagem->obtem($filtro);
					break;
				default:
					$r = $this->prtb_produto->obtem($filtro);
					break;
			}
			return($r);		
		}
		
		public function obtemPlanoPeloId($id) {
			$filtro = array("id_produto" => $id);
			$r = $this->obtemPlanos($filtro);

			if( !count($r) ) return array();
			
			$r = $this->obtemPlanos($filtro,$r[0]["tipo"]);
			
			return(count($r)?$r[0]:array());
			
		}
		
		public function obtemListaPlanos($tipo='',$disponivel='') {
			$filtro = array();
			
			if( $tipo ) {
				$filtro["tipo"] = $tipo;
			}
			
			if( $disponivel ) {
				$filtro["disponivel"] = $disponivel == 't' || $dispinivel == 1 || $disponivel == 's' ? $disponivel : 'f';	// Filtro p/ boolean
			}
		
			return($this->obtemPlanos($filtro,$tipo));
		}
		
		protected function cadastraPlanoBL($dados) {
			return($this->prtb_produto_bandalarga->insere($dados));
		}
		
		protected function cadastraPlanoD($dados) {
			return($this->prtb_produto_discado->insere($dados));
		}
		
		protected function cadastraPlanoH($dados) {
			return($this->prtb_produto_hospedagem->insere($dados));
		}
		
		public function cadastraPlano($dados) {
			$ret = 0;
			unset($dados["id_produto"]);	// Id Será gerado
			
			$dados["tipo"] = trim(@$dados["tipo"]);
			switch(@$dados["tipo"]) {
				case 'BL':
					$ret = $this->cadastraPlanoBL($dados);
					break;
				case 'D':
					$ret = $this->cadastraPlanoD($dados);
					break;
				case 'H':
					$ret = $this->cadastraPlanoH($dados);
					break;
				default:
					throw new ModeloExcecao(255,"Tipo de plano desconhecido");
			}
			return($ret);
		}





		protected function alteraPlanoBL($id,$dados) {
			return($this->prtb_produto_bandalarga->altera($dados,array("id_produto" => $id)));
		}
		
		protected function alteraPlanoD($id,$dados) {
			return($this->prtb_produto_discado->altera($dados,array("id_produto" => $id)));
		}
		
		protected function alteraPlanoH($id,$dados) {
			return($this->prtb_produto_hospedagem->altera($dados,array("id_produto" => $id)));
		}

		public function alteraPlano($id,$dados) {
			$ret = 0;
			unset($dados["id_produto"]);	// Id vem pelo $id, não pelo $dados
			
			if( !$id ) {
				throw new ModeloExcessao(255,"Identificação do plano desconhecida");
			} else {
				$planos = $this->obtemPlanoPeloId($id);
				if( !count($planos) ) {
					throw new ModeloExcessao(255,"Identificação do plano desconhecida");
				}
			}
			
			$dados["tipo"] = trim(@$dados["tipo"]);
			switch(@$dados["tipo"]) {
				case 'BL':
					$ret = $this->alteraPlanoBL($id,$dados);
					break;
				case 'D':
					$ret = $this->alteraPlanoD($id,$dados);
					break;
				case 'H':
					$ret = $this->alteraPlanoH($id,$dados);
					break;
				default:
					throw new ModeloExcecao(255,"Tipo de plano desconhecido");
			}
			return($ret);		
		}
			
	}
	
?>
