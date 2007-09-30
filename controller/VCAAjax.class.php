<?

	/**
	 * Utilizada para consultas via AJAX
	 *
	 * 
	 *
	 */
	class VCAAjax extends VirtexControllerAdmin {
		protected $query;
		
		protected $output;

		public function __construct() {
			parent::__construct();
			VirtexPersiste::setDebug(false);
		}
		
		public function init() {
			parent::init();
			// $this->_view = VirtexViewAdmin::factory(			
			$this->op 		= @$_REQUEST["op"];
			$this->query 	= @$_REQUEST["query"];
			$this->output	= @$_REQUEST["output"] ? $_REQUEST["output"] : "JSON";
		}
		
		
		protected function retorna($valor) {
			// TODO: output XML
			switch($this->output) {
				case 'JSON':
					echo MJson::encode($valor);
					break;
				case 'DEBUG':
					echo "<pre>";
					print_r($valor);
					echo "</pre>";
					break;
			}
		}
		
		protected function executa() {
		
			switch($this->op) {
				case 'contas':
					$this->executaContas();
					break;
			}
		
		}
		
		/**
		 * Funcionalidades relacionadas à contas.
		 */
		protected function executaContas() {
			$contas = VirtexModelo::factory('contas');

			switch($this->query) {
				/**
				 * op=contas
				 * query=usuario
				 * username=?
				 * dominio=?
				 * tipo_conta=?
				 *
				 * Retorna o usuário encontrado com as credencias fornecidas.
				 */
				case 'usuario':
					$username = @$_REQUEST["username"];
					$dominio = @$_REQUEST["dominio"];
					$tipo_conta = @$_REQUEST["tipo_conta"];
					
					$retorno = array();

					if( $username && $dominio && $tipo_conta ) {
						$retorno = $contas->obtemContaPeloUsername($username,$dominio,$tipo_conta);
					}
					
					// Escreve o retorno na tela de acordo com o formato especificado.
					$this->retorna($retorno);
					break;
					
				/**
				 * op=contas
				 * query=enderecoDisponivel
				 * endereco=?
				 * id_nas=?
				 *
				 * Utilizado para saber se um usuário pode utilizar um determinado endereco no nas fornecido.
				 *
				 * Retorna uma matriz associativa contendo um código seguido de uma mensagem e do endereço correto para cadastrar.
				 *  retorno.codigo 		retorno.mensagem								retorno.endereco
				 *
				 *		0					Endereço disponível.							(endereço válido)
				 *		1					Endereço não pertence ao NAS indicado.			(null)
				 *		2					Endereço não disponível.						(null)
				 *		3					Endereço incompatíve com tipo do NAS			(null)
				 *		254					NAS Inválido									(null)
				 *		255					Endereço Inválido.								(null)
				 *
				 * CONSIDERAÇÕES: Não chamar a função com endereço vazio.
				 */
				case 'enderecoDisponivel':
					$endereco 	= @$_REQUEST["endereco"];
					$id_nas		= @$_REQUEST["id_nas"];
					
					$retorno = array("codigo" => 255, "mensagem" => "Endereço Inválido.", "endereco" => null  );
					
					try {
						if( !$endereco ) throw new Exception("Endereço Inválido", 255);

						$equipamentos = VirtexModelo::factory('equipamentos');
						$nas = $equipamentos->obtemNAS($id_nas);
						
						if( !count($nas) ) throw new Exception("NAS Inválido.",254);

						@list($ip,$bits) = explode("/",$endereco);
						if( !$bits ) $bits = 32;
						
						if( $nas["tipo_nas"] != "I" && $bits != 32 ) throw new Exception("Endereço Incompatível com tipo do NAS.", 3);
						
						try {
							$ip = new MInet($ip."/".$bits);
						} catch( MException $e ) {
							throw new Exception("Endereço Inválido",255);
						}
						
						// Pega o endereço OK
						$addr = $equipamentos->enderecoPertenceAoNAS($id_nas,$endereco);
						
						if( !$addr ) throw new Exception("Endereço não pertence ao NAS indicado.", 1);
						
						$clientes = $contas->pesquisaClientesPorContas($addr);
						
						if( count($clientes) ) throw new Exception("Endereço não disponível.",2);
						
						$retorno = array("codigo" => 0, "mensagem" => "Endereço disponivel", "endereco" => $addr);
					
					} catch( Exception $e ) {
						$retorno = array("codigo" => $e->getCode(), "mensagem" => $e->getMessage(), "endereco" => null);
					}
										
					$this->retorna($retorno);
			}
		}
	}
?>
