<?
	class PERSISTE_CFTB_FORMA_PAGAMENTO extends VirtexPersiste {
		public function __construct() {
			parent::__construct();
			
			$this->_campos		= array("id_cobranca","nome_cobranca","disponivel");
			$this->_chave		= "id_cobranca";
			$this->_ordem		= "nome_cobranca";
			$this->_tabela		= "cftb_forma_pagamento";
			$this->_sequence	= "cfsq_id_cobranca";
			$this->_filtros		= array("disponivel" => "bool");
			
		}
		
		public function obtemFormasPagamentoDisponiveis() {
			return($this->obtem(array("disponivel"=>1)));
		}

	}

