<?

class PERSISTE_CBTB_REMESSA extends VirtexPersiste {

	public function __construct($bd=null) {
		parent::__construct();

		$this->_campos 		= array("id_remessa", "datahora", "arquivo");
		$this->_chave 		= "id_remessa";
		$this->_ordem 		= "datahora";
		$this->_tabela 		= "cbtb_remessa";
		$this->_sequence	= "";
		$this->_filtros		= array("datahora"=>"date");
	}	
	
}

?>