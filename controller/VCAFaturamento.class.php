<?

class VCAFaturamento extends VirtexControllerAdmin {

	protected $faturamento;

	public function __construct() {
		parent::__construct();
	}

	protected function init() {
		parent::init();

		$this->faturamento = VirtexModelo::factory('cobranca');
		$this->_view = VirtexViewAdmin::factory('faturamento');
	}

	protected function executa() {
		switch($this->_op) {
			case 'relatorios':
				$this->executaRelatorios();
			break;
		}
	}


	protected function executaRelatorios() {

		$relatorio = @$_REQUEST["relatorio"];


		if("previsao" == $relatorio){


		}elseif ("faturamento" == $relatorio){


		}elseif ("por_produto" == $relatorio){

			$ano_select = @$_REQUEST["ano_select"];
			$this->_view->atribui("ano_select", $ano_select);
			$this->_view->atribui("ano_select1", $ano_select1);
			$cobranca = VirtexModelo::factory("cobranca");
			$lista = $cobranca->obtemFaturamentoPorProduto($ano_select);
			$anos_fatura = $cobranca->obtemAnosFatura();
			$dados_bl = array();
			$dados_h = array();
			$dados_d = array();

			for($i=1; $i<=12; $i++) {
				$dados_bl[$i] = "0.00";
				$dados_h[$i] = "0.00";
				$dados_d[$i] = "0.00";
			}

			for($i=0; $i<count($lista); $i++) {

				$temp = $lista[$i];

				switch($temp["tipo"]) {

					case "BL":
						$dados_bl[$temp["mes"]] = $temp["valor_pago"];
						break;
					case "D":
						$dados_d[$temp["mes"]] = $temp["valor_pago"];
						break;
					case "H":
						$dados_h[$temp["mes"]] = $temp["valor_pago"];
						break;
					default:
						//FAZ NADa POR ENQUANTO
						break;
				}
			}

			$this->_view->atribui("ano_select",$ano_select);
			$this->_view->atribui("anos_fatura",$anos_fatura);
			$this->_view->atribui("dados_bl", $dados_bl);
			$this->_view->atribui("dados_h", $dados_h);
			$this->_view->atribui("dados_d", $dados_d);

		}elseif ("por_periodo" == $relatorio){

			$periodo = 12;
			$this->_view->atribui("periodo", $periodo);
			$cobranca = VirtexModelo::factory("cobranca");

			$lista = $cobranca->obtemFaturamentoPorPeriodo($periodo);
			$dados = array();
			$sumario = array("valor_documento" => 0, "valor_acrescimo" => 0, "valor_desconto" => 0, "valor_pago"=>0);

			for($i=0;$i<count($lista);$i++) {
				if( $lista[$i]["mes"] < 10 ) $lista[$i]["mes"] = "0".$lista[$i]["mes"];
				$dados[ $lista[$i]["ano"] . "-" . $lista[$i]["mes"] ] = $lista[$i];

				$sumario["valor_documento"] += $lista[$i]["valor_documento"];
				$sumario["valor_desconto"] += $lista[$i]["valor_desconto"];
				$sumario["valor_acrescimo"] += $lista[$i]["valor_acrescimo"];
				$sumario["valor_pago"] += $lista[$i]["valor_pago"];
			}

			$this->_view->atribui("sumario",$sumario);
			$dt = date("d/m/Y");

			for($i=0;$i<$periodo;$i++) {
				list($d,$m,$y) = explode("/",$dt);
				$dt = MData::adicionaMes($dt,-1);
				if( ! @$dados[ $y."-".$m ] ) {
					$dados[ $y."-".$m ] = array("ano" => $y, "mes" => $m);
				}
			}
			krsort($dados);
			$this->_view->atribui("lista", $dados);

		}
	}

}
?>