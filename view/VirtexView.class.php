<?

	/**
	 * Classe base para o sistema de views
	 *
	 *
	 * TODO: 
	 *
	 * - Definir visualização de erro padrão.
	 * - Definir visualizaçao de redirect.
	 * - Definir visualização p/ JSON (usado pelo Ajax).
	 * - Definir outputMode (p/ escolher por exemplo entre HTML, XML, Imagem ou JSON).
	 * - Tratar exceptions das sub-classes p/ gerar tela de erro.
	 */
	class VirtexView {
		protected $_tpl;
		protected $_tplPath;
		protected $_file;
		
		protected $_visualizacao;
		
		
		protected $_erroCodigo;
		protected $_erroMensagem;
		
		protected $_bag;
		
		protected $_redir;
		
		protected $_exibirNomeArquivo;

		protected function __construct() {
			$this->_tplPath = ".";
			$this->_exibirNomeArquivo = false;
			$this->init();		// Object
			$this->_tpl = MTemplate::getInstance($this->_tplPath);
			$this->_bag = array();
			$this->_redir = false;
			
			$this->atribuiErro();	// Zera o erro
			
		}
		
		protected function exibirNomeArquivo($bl) {
			$this->_exibirNomeArquivo = $bl;
		}
		
		public function atribuiErro($codigo=0,$mensagem="") {
			$this->_erroCodigo = $codigo;
			$this->_erroMensagem = $mensagem;
			
			$this->_tpl->atribui("erroCodigo",$this->_erroCodigo);
			$this->_tpl->atribui("erroMensagem",$this->_erroMensagem);
		}
		
		
		protected function init() {
			//$this->_tplPath = MUtils::getPwd() . "/view/templates";
			$this->_tplPath = "./view/templates";
		}
		
		public function exibe() {
			if( !$this->_redir && $this->_file ) {
				$this->_tpl->exibe($this->_file);
				if( $this->_exibirNomeArquivo ) {
					echo "<!-- " . $this->_file . "-->\n";
				}
			}
		}
		
		
		/**
		 * atribuiVisualizacao
		 *
		 * - Define o que será exibido. Ex: "listagem", "cadastro", "etc"
		 *
		 */
		
		public function atribuiVisualizacao($visualizacao = "index") {
			$this->_visualizacao = $visualizacao;
			
			if( $this->_visualizacao == "msgredirect" ) {
				$url = $this->obtem("url");
				
				$url .= (strstr($url,"?")?"&":"?")."extra=".md5(microtime());
				$this->atribui("url",$url);
				$this->_file = "BASE_msgredirect.html";
			}
			
		}
		
		public function obtemVisualizacao() {
			return($this->_visualizacao);
		}
		
		public static function simpleRedirect($url) {
			header("Location: " . $url);
		}
		
		public function redirect($url) {
			$this->_redir = true;
			self::simpleRedirect($url);
		}
		
		
		
		/**
		 * Variável do template
		 */
		public function atribui($variavel,$valor) {
			$this->_tpl->atribui($variavel,$valor);
			$this->_bag[$variavel] = $valor;
		}
		
		public function obtem($variavel) {
			return(@$this->_bag[$variavel]);
		}
	
	}
