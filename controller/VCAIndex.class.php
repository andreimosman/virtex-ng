<?

	class VCAIndex extends VirtexControllerAdmin {

		public function __construct() {
			parent::__construct();
		}

		protected function init() {
			// Inicializações da SuperClasse
			parent::init();
			$this->_view = VirtexViewAdmin::factory("index");

		}

		protected function executa() {
		
		
		
			$podeAmortizar = $this->requirePrivGravacao("_FINANCEIRO_COBRANCA_AMORTIZACAO",false);
			
			$this->_view->atribui("podeAmortizar",$podeAmortizar);

		
		
			$cfg_geral = $this->_cfg->config["geral"];
			$desenvolvimento = @$cfg_geral["desenvolvimento"];

			$dadosLogin = $this->_login->obtem("dados");
			$this->_view->atribui("dadosLogin",$dadosLogin);
		
			$permissao_monitoramento = $this->requirePrivLeitura("_SUPORTE_MONITORAMENTO",false);
			$this->_view->atribui("permissao_monitoramento", $permissao_monitoramento);

			$target = "conteudo";

			$menu = new MMenu(0);

			//
			// Home
			//
			$menu->addItem("Home", "admin-home.php", $target);

			//
			// Menu de Clientes
			//
			$menuClientes = new MMenu();
			$menuClientes->addItem("Inclusão", "admin-clientes.php?op=cadastro", $target);
			$menuClientes->addItem("Pesquisa", "admin-clientes.php?op=pesquisa", $target);

			// Submenu Relatórios
			$menuClientes->addSeparator();
			$menuClientesRelatorios = new MMenu();
			$menuClientesRelatorios->addItem("Lista Geral","admin-clientes.php?op=relatorios&relatorio=lista_geral",$target);
			$menuClientesRelatorios->addItem("Clientes por Cidade","admin-clientes.php?op=relatorios&relatorio=cliente_cidade",$target);
			$menuClientes->addSubmenu("Relatórios", $menuClientesRelatorios);

			// $menuClientes->addSeparator();
			// $menuClientes->addItem("Eliminar","admin-clientes.php?op=eliminar", $target);

			$menu->addSubmenu("Clientes",$menuClientes);
			

			//
			// Menu de Suporte
			//

			$menuSuporte = new MMenu();
			$menuSuporte->addItem("Helpdesk", "admin-suporte.php?op=helpdesk", $target);
			$menuSuporte->addItem("Gráficos", "admin-suporte.php?op=graficos", $target);
			$menuSuporte->addItem("Monitoramento", "admin-suporte.php?op=monitoramento", $target);

			$menuSuporte->addSeparator();
			$menuSuporte->addItem("Links Externos", "admin-suporte.php?op=links", $target);

			$menuSuporte->addSeparator();
			$menuSuporteFerramentas = new MMenu();
			$menuSuporteFerramentas->addItem("ARP", "admin-suporte.php?op=ferramentas&ferramenta=arp", $target);
			$menuSuporteFerramentas->addItem("PING", "admin-suporte.php?op=ferramentas&ferramenta=ping", $target);
			$menuSuporteFerramentas->addSeparator();
			$menuSuporteFerramentas->addItem("Calculadora IP", "admin-suporte.php?op=ferramentas&ferramenta=ipcalc", $target);
			$menuSuporte->addSubmenu("Ferramentas", $menuSuporteFerramentas);

			$menuSuporte->addSeparator();
			$menuSuporteRelatorios = new MMenu();
			$menuSuporteRelatorios->addItem("Clientes por Banda","admin-suporte.php?op=relatorios&relatorio=banda", $target);
			$menuSuporteRelatorios->addItem("Clientes sem MAC","admin-suporte.php?op=relatorios&relatorio=cliente_sem_mac", $target);
			$menuSuporteRelatorios->addSeparator();
			
			$menuSuporteRelatoriosHelpdesk = new MMenu();
			$menuSuporteRelatoriosHelpdesk->addItem("Chamados/Ocorrências", "admin-suporte.php?op=relatorios&relatorio=helpdesk&tipo=ocorrencias", $target);
			
			$menuSuporteRelatorios->addSubMenu("Helpdesk", $menuSuporteRelatoriosHelpdesk);
			
			$menuSuporte->addSubmenu("Relatórios", $menuSuporteRelatorios);

			$menu->addSubmenu("Suporte",$menuSuporte);

			//
			// Menu de Cadastro
			//

			$menuCadastro = new MMenu();

			$menuCadastro->addItem("Condomínios", "admin-cadastro.php?op=condominios&tela=listagem", $target);
			$menuCadastroEquipamentos = new MMenu();
			$menuCadastroEquipamentos->addItem("Servidores", "admin-cadastro.php?op=equipamentos&tela=servidores", $target);
			$menuCadastroEquipamentos->addItem("POPs", "admin-cadastro.php?op=equipamentos&tela=pops", $target);
			$menuCadastroEquipamentos->addItem("NAS", "admin-cadastro.php?op=equipamentos&tela=nas", $target);			
			$menuCadastro->addSubmenu("Equipamentos", $menuCadastroEquipamentos);

			$menuCadastro->addSeparator();
			$menuCadastro->addItem("Administradores", "admin-cadastro.php?op=administradores&tela=listagem", $target);
			
			
			$menuCadastro->addSeparator();
			$menuCadastro->addItem("Planos", "admin-cadastro.php?op=planos&tela=listagem", $target);
			if($desenvolvimento) $menuCadastro->addItem("Produtos", "admin-cadastro.php?op=produtos&tela=listagem", $target);
			
			$menuCadastroServicos	= new MMenu();
			if($desenvolvimento) $menuCadastroServicos->addItem("Contratados (comprado)", "admin-cadastro.php?op=servicos&tela=contratados", $target);
			if($desenvolvimento) $menuCadastroServicos->addItem("Fornecido (vendido)", "admin-cadastro.php?op=servicos&tela=fornecido", $target);
			if($desenvolvimento) $menuCadastro->addSubmenu("Serviços", $menuCadastroServicos);			


			
			if($desenvolvimento) $menuCadastro->addSeparator();
			if($desenvolvimento) $menuCadastro->addItem("Centros de Custo", "admin-cadastro.php?op=centrodecustos&tela=listagem", $target);
			if($desenvolvimento) $menuCadastro->addItem("Plano de Contas", "admin-cadastro.php?op=planodecontas&tela=listagem", $target);

			
			$menuCadastro->addSeparator();
			$menuCadastroRelatorios	= new MMenu();
			$menuCadastroRelatoriosEquipamentos = new MMenu();
			$menuCadastroRelatoriosEquipamentos->addItem("Carga por AP", "admin-cadastro.php?op=relatorios&relatorio=carga&tipo=ap", $target);
			$menuCadastroRelatoriosEquipamentos->addItem("Carga por POP", "admin-cadastro.php?op=relatorios&relatorio=carga&tipo=pop", $target);
			$menuCadastroRelatoriosEquipamentos->addItem("Carga por NAS", "admin-cadastro.php?op=relatorios&relatorio=carga&tipo=nas", $target);			
			$menuCadastroRelatoriosEquipamentos->addItem("IPs dos POPs", "admin-cadastro.php?op=relatorios&relatorio=pop_ip", $target);
			$menuCadastroRelatorios->addSubmenu("Equipamentos",$menuCadastroRelatoriosEquipamentos);			
			
			
			$menuCadastroRelatoriosCondominio = new MMenu();
			$menuCadastroRelatoriosCondominioInstalado = new MMenu();
			$menuCadastroRelatoriosCondominioInstalado->addItem("Lista de condomínios instalados", "admin-cadastro.php?op=relatorios&relatorio=condominios&tipo=instalado", $target);			
			$menuCadastroRelatoriosCondominio->addSubMenu("Instalados", $menuCadastroRelatoriosCondominioInstalado);			
			$menuCadastroRelatorios->addSubMenu("Condominios", $menuCadastroRelatoriosCondominio);
			
			
			$menuCadastro->addSubmenu("Relatórios", $menuCadastroRelatorios);
			
			$menu->addSubmenu("Cadastro", $menuCadastro);

			//
			// Menu de Financeiro
			//

			$menuFinanceiro = new MMenu();
			
			
			if($desenvolvimento) $menuFinanceiro->addItem("Contas a Pagar", "admin-financeiro.php?op=relatorios&relatorio=faturamento", $target);
			if($desenvolvimento) $menuFinanceiro->addItem("Contas a Receber", "admin-financeiro.php?op=relatorios&relatorio=faturamento", $target);

			if($desenvolvimento) $menuFinanceiro->addSeparator();
			$menuFinanceiroCobranca = new MMenu();
			$menuFinanceiroCobranca->addItem("Bloqueios", "admin-financeiro.php?op=bloqueios", $target);

			$menuFinanceiroCobranca->addSeparator();
			$menuFinanceiroCobranca->addItem("Amortização", "admin-financeiro.php?op=amortizacao", $target);

			//$menuFinanceiroCobranca->addSeparator();
			$menuFinanceiroCobranca->addItem("Gerar Cobrança/Boletos", "admin-financeiro.php?op=gerar_cobranca", $target);
			$menuFinanceiroCobranca->addItem("Troca de Arquivos", "admin-financeiro.php?op=arquivos",$target);
			
			$menuFinanceiroCobranca->addSeparator();
			$menuFinanceiroCobranca->addItem("Renovação de Contratos", "admin-financeiro.php?op=renovar_contrato", $target);
			$menuFinanceiroCobranca->addItem("Central de Impressão", "admin-financeiro.php?op=impressao", $target);
			

			// $menuFinanceiroCobranca->addSeparator();
			$menuFinanceiroRelatoriosCobranca = new MMenu();
			$menuFinanceiroRelatoriosCobranca->addItem("Atrasos", "admin-financeiro.php?op=relatorios_cobranca&relatorio=atrasos", $target);
			$menuFinanceiroRelatoriosCobranca->addItem("Reagendamentos", "admin-financeiro.php?op=relatorios_cobranca&relatorio=reagendamentos", $target);
			$menuFinanceiroRelatoriosCobranca->addItem("Bloqueios e Desbloqueios", "admin-financeiro.php?op=relatorios_cobranca&relatorio=bloqueios_desbloqueios", $target);
			$menuFinanceiroRelatoriosCobranca->addSeparator();
			$menuFinanceiroRelatoriosCobranca->addItem("Emails de Cobrança", "admin-financeiro.php?op=relatorios_cobranca&relatorio=emails_cobranca", $target);
			$menuFinanceiroRelatoriosCobranca->addSeparator();
			$menuFinanceiroRelatoriosCobranca->addItem("Cortesias", "admin-financeiro.php?op=relatorios_cobranca&relatorio=cortesias", $target);
			$menuFinanceiroRelatoriosCobranca->addSeparator();
			$menuFinanceiroRelatoriosCobranca->addItem("Adesões", "admin-financeiro.php?op=relatorios_cobranca&relatorio=adesoes", $target);
			$menuFinanceiroRelatoriosCobranca->addItem("Cancelamentos", "admin-financeiro.php?op=relatorios_cobranca&relatorio=cancelamentos", $target);
			$menuFinanceiroRelatoriosCobranca->addItem("Evolução", "admin-financeiro.php?op=relatorios_cobranca&relatorio=evolucao", $target);
			$menuFinanceiroRelatoriosCobranca->addSeparator();
			$menuFinanceiroRelatoriosCobranca->addItem("Clientes por Produto", "admin-financeiro.php?op=relatorios_cobranca&relatorio=cliente_produto", $target);
			$menuFinanceiroRelatoriosCobranca->addItem("Clientes por Tipo Produto", "admin-financeiro.php?op=relatorios_cobranca&relatorio=cliente_tipo_produto", $target);
			$menuFinanceiroRelatoriosCobranca->addSeparator();
			$menuFinanceiroRelatoriosCobranca->addItem("Inadimplencia", "admin-financeiro.php?op=relatorios_cobranca&relatorio=inadimplencia", $target);
			$menuFinanceiroRelatoriosCobranca->addItem("Recebimentos por Periodo", "admin-financeiro.php?op=relatorios_cobranca&relatorio=recebimentos_periodo", $target);
			$menuFinanceiroRelatoriosCobranca->addSeparator();
			$menuFinanceiroRelatoriosCobranca->addItem("Novos Contratos/Cidade", "admin-financeiro.php?op=relatorios_cobranca&relatorio=novos_contratos_cidade", $target);
			// $menuFinanceiroCobranca->addSubmenu("Relatórios",$menuFinanceiroCobrancaRelatorios);

			$menuFinanceiro->addSubmenu("Cobrança",$menuFinanceiroCobranca);
			
			// $menuFinanceiroFaturamento = new MMenu();
			$menuFinanceiroRelatoriosFaturamento = new MMenu();
			$menuFinanceiroRelatoriosFaturamento->addItem("Faturamento Anual", "admin-financeiro.php?op=relatorios_faturamento&relatorio=faturamento", $target);
			$menuFinanceiroRelatoriosFaturamento->addItem("Faturamento por Produto ", "admin-financeiro.php?op=relatorios_faturamento&relatorio=por_produto", $target);
			$menuFinanceiroRelatoriosFaturamento->addItem("Faturamento por Período ", "admin-financeiro.php?op=relatorios_faturamento&relatorio=por_periodo", $target);
			$menuFinanceiroRelatoriosFaturamento->addSeparator();
			$menuFinanceiroRelatoriosFaturamento->addItem("Previsão de Faturamento", "admin-financeiro.php?op=relatorios_faturamento&relatorio=previsao", $target);
			//$menuFinanceiroFaturamento->addSubmenu("Relatorios", $menuFinanceiroFaturamentoRelatorios);
			
			// $menuFinanceiro->addSubmenu("Faturamento", $menuFinanceiroFaturamento);
			
			$menu->addSubmenu("Financeiro", $menuFinanceiro);


			
			$menuFinanceiro->addSeparator();
			$menuFinanceiroRelatorios = new MMenu();
			$menuFinanceiroRelatorios->addItem("Fluxo de Caixa", "admin-financeiro.php?op=relatorios&relatorio=faturamento", $target);
			$menuFinanceiroRelatorios->addSubmenu("Faturamento", $menuFinanceiroRelatoriosFaturamento );
			$menuFinanceiroRelatorios->addSubmenu("Cobrança", $menuFinanceiroRelatoriosCobranca );
			
			
			
			
			
			
			$menuFinanceiro->addSubmenu("Relatórios", $menuFinanceiroRelatorios);
			
			
			//
			// Menu de Comercial
			//
			
			$menuComercial = new MMenu();
			if($desenvolvimento) $menuComercial->addItem("Promoções", "admin-administracao.php?op=altsenha", $target);
			
			if($desenvolvimento) $menu->addSubmenu("Comercial", $menuComercial);
			

			//
			// Menu de Administração
			//

			$menuAdministracao = new MMenu();
			$menuAdministracao->addItem("Alterar Minha Senha", "admin-administracao.php?op=altsenha", $target);
			
			$menuAdministracaoPreferencias 	= new MMenu();
			if($desenvolvimento) $menuAdministracaoPreferencias->addItem("Central do Assinante","admin-administracao.php?op=preferencias&tela=resumo", $target);
			if($desenvolvimento) $menuAdministracaoPreferencias->addItem("E-mails","admin-administracao.php?op=preferencias&tela=resumo", $target);
			if($desenvolvimento) $menuAdministracaoPreferencias->addSeparator();
			$menuAdministracaoPreferencias->addItem("Resumo","admin-administracao.php?op=preferencias&tela=resumo", $target);
			$menuAdministracaoPreferencias->addSeparator();
			$menuAdministracaoPreferencias->addItem("Preferências Gerais","admin-administracao.php?op=preferencias&tela=geral", $target);
			$menuAdministracaoPreferencias->addItem("Preferências Provedor","admin-administracao.php?op=preferencias&tela=provedor", $target);
			$menuAdministracaoPreferencias->addItem("Preferências Cobrança","admin-administracao.php?op=preferencias&tela=cobranca", $target);
			$menuAdministracaoPreferencias->addItem("Preferências Helpdesk","admin-administracao.php?op=preferencias&tela=helpdesk", $target);
			$menuAdministracaoPreferencias->addSeparator();
			$menuAdministracaoPreferencias->addItem("Modelos de Contrato","admin-administracao.php?op=preferencias&tela=modelos", $target);
			$menuAdministracaoPreferencias->addSeparator();
			$menuAdministracaoPreferencias->addItem("Cidades","admin-administracao.php?op=preferencias&tela=cidades", $target);
			$menuAdministracaoPreferencias->addItem("Faixas de Banda","admin-administracao.php?op=preferencias&tela=banda", $target);
			$menuAdministracaoPreferencias->addItem("Monitoramento","admin-administracao.php?op=preferencias&tela=monitoramento", $target);
			$menuAdministracaoPreferencias->addItem("Links Externos","admin-administracao.php?op=preferencias&tela=links", $target);
			$menuAdministracaoPreferencias->addSeparator();
			$menuAdministracaoPreferencias->addItem("Registro do Software","admin-administracao.php?op=preferencias&tela=registro", $target);
			$menuAdministracao->addSubmenu("Preferências", $menuAdministracaoPreferencias);


			$menuAdministracao->addSeparator();
			$menuAdministracaoFerramentas = new MMenu();
			// $menuAdministracaoFerramentas->addItem("Envio de E-mails", "admin-administracao.php?op=ferramentas&ferramenta=backup", $target);
			$menuAdministracaoFerramentas->addItem("Backup & Restore", "admin-administracao.php?op=ferramentas&ferramenta=backup", $target);
			$menuAdministracao->addSubmenu("Ferramentas", $menuAdministracaoFerramentas);

			$menuAdministracaoBancoDados = new MMenu();
			if($desenvolvimento) $menuAdministracaoBancoDados->addItem("Eliminar Cliente", "admin-administracao.php?op=bancodados&eliminar=cliente", $target);
			if($desenvolvimento) $menuAdministracaoBancoDados->addItem("Eliminar Contrato", "admin-administracao.php?op=bancodados&eliminar=contrato", $target);
			if($desenvolvimento) $menuAdministracaoBancoDados->addItem("Eliminar Conta", "admin-administracao.php?op=bancodados&eliminar=conta", $target);
			if($desenvolvimento) $menuAdministracao->addSubmenu("Banco de Dados", $menuAdministracaoBancoDados);


			$menuAdministracao->addSeparator();
			$menuAdministracaoRelatorios = new MMenu();
			$menuAdministracaoRelatorios->addItem("Log de Eventos", "admin-administracao.php?op=relatorios&relatorio=eventos", $target);
			$menuAdministracao->addSubmenu("Relatórios",$menuAdministracaoRelatorios);		
			
			$menu->addSubmenu("Administração", $menuAdministracao);

			//
			// Logout
			//
			$menu->addItem("Sair", "admin-login.php", "_top");


			// $menu->printRecursive();
			$this->_view->atribui("jsMenuItens", $menu->jsOutput());
			
		}

	}

