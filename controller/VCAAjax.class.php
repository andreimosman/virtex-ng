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
		 * Funcionalidades relacionadas � contas.
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
				 * Retorna o usu�rio encontrado com as credencias fornecidas.
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
				 * Utilizado para saber se um usu�rio pode utilizar um determinado endereco no nas fornecido.
				 *
				 * Retorna uma matriz associativa contendo um c�digo seguido de uma mensagem e do endere�o correto para cadastrar.
				 *  retorno.codigo 		retorno.mensagem								retorno.endereco
				 *
				 *		0					Endere�o dispon�vel.							(endere�o v�lido)
				 *		1					Endere�o n�o pertence ao NAS indicado.			(null)
				 *		2					Endere�o n�o dispon�vel.						(null)
				 *		3					Endere�o incompat�ve com tipo do NAS			(null)
				 *		254					NAS Inv�lido									(null)
				 *		255					Endere�o Inv�lido.								(null)
				 *
				 * CONSIDERA��ES: N�o chamar a fun��o com endere�o vazio.
				 */
				case 'enderecoDisponivel':
					$endereco 	= @$_REQUEST["endereco"];
					$id_nas		= @$_REQUEST["id_nas"];
					
					$retorno = array("codigo" => 255, "mensagem" => "Endere�o Inv�lido.", "endereco" => null  );
					
					try {
						if( !$endereco ) throw new Exception("Endere�o Inv�lido", 255);

						$equipamentos = VirtexModelo::factory('equipamentos');
						$nas = $equipamentos->obtemNAS($id_nas);
						
						if( !count($nas) ) throw new Exception("NAS Inv�lido.",254);

						@list($ip,$bits) = explode("/",$endereco);
						if( !$bits ) $bits = 32;
						
						if( $nas["tipo_nas"] != "I" && $bits != 32 ) throw new Exception("Endere�o Incompat�vel com tipo do NAS.", 3);
						
						try {
							$ip = new MInet($ip."/".$bits);
						} catch( MException $e ) {
							throw new Exception("Endere�o Inv�lido",255);
						}
						
						// Pega o endere�o OK
						$addr = $equipamentos->enderecoPertenceAoNAS($id_nas,$endereco);
						
						if( !$addr ) throw new Exception("Endere�o n�o pertence ao NAS indicado.", 1);
						
						$clientes = $contas->pesquisaClientesPorContas($addr);
						
						if( count($clientes) ) throw new Exception("Endere�o n�o dispon�vel.",2);
						
						$retorno = array("codigo" => 0, "mensagem" => "Endere�o disponivel", "endereco" => $addr);
					
					} catch( Exception $e ) {
						$retorno = array("codigo" => $e->getCode(), "mensagem" => $e->getMessage(), "endereco" => null);
					}
										
					$this->retorna($retorno);
			}
		}
	}
?>
