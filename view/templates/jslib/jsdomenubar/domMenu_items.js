
// {{{ domMenu_keramik: data

domMenu_data.set('domMenu_keramik', new Hash(


/**
 * HOME
 */

    1, new Hash(
       'contents', 'Home',
       'uri', 'admin-home.php',
       'target', 'conteudo'
    ),

/**
 * MENU CLIENTES
 */



    2, new Hash(
        'contents', 'Clientes',
        'uri', '',
        'statusText', '',
        1, new Hash(
            'contents', 'Cadastro',
            'uri', 'admin-clientes.php?op=cadastro',
            'target', 'conteudo'
        ),
        
        2, new Hash(
            'contents', 'Pesquisa',
            'uri', 'admin-clientes.php?op=pesquisa',
            'target', 'conteudo'
        ),
        
        3, new Hash(
            'contents', 'Relatórios&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            'uri', '',
            1, new Hash(
                'contents', 'Lista Geral',
                'uri', 'admin-clientes.php?op=relatorios&relatorio=lista_geral',
                'statusText', '',
                'target', 'conteudo'
            ),
            2, new Hash(
                'contents', 'Clientes por Cidade',
                'uri', 'admin-clientes.php?op=relatorios&relatorio=cliente_cidade',
                'target', 'conteudo'
            )

        ),
        
	4, new Hash(
	    'contents', '<hr class="separador">'
        ),        

        5, new Hash(
            'contents', 'Eliminar',
            'uri', 'admin-clientes.php?op=eliminar',
            'target', 'conteudo'
        )
    ),
    
/**
 * MENU COBRANÇA
 */
 
    3, new Hash(
        'contents', 'Cobrança',
        'uri', '',

	1, new Hash(
	    'contents', 'Bloqueios',
	    'uri', 'admin-cobranca.php?op=bloqueios',
	    'target', 'conteudo'
        ),
        
	2, new Hash(
	    'contents', 'Retorno Banco',
	    'uri', 'admin-cobranca.php?op=retorno',
	    'target', 'conteudo'
        ),
        
	3, new Hash(
	    'contents', 'Emitir Boletos',
	    'uri', 'admin-cobranca.php?op=boleto',
	    'target', 'conteudo'
        ),
        
	4, new Hash(
	    'contents', '<hr class="separador">'
        ),
	
        5, new Hash(
		'contents', 'Relatórios&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
		'uri', '',
            
		1, new Hash(
		    'contents', 'Atrasos',
		    'uri', 'admin-cobranca.php?op=relatorios&relatorio=atrasos',
		    'target', 'conteudo'
		),
		2, new Hash(
		    'contents', 'Reagendamentos',
		    'uri', 'admin-cobranca.php?op=relatorios&relatorio=reagendamentos',
		    'target', 'conteudo'
		),
		3, new Hash(
		    'contents', 'Bloqueios e Desbloqueios',
		    'uri', 'admin-cobranca.php?op=relatorios&relatorio=bloqueios_desbloqueios',
		    'target', 'conteudo'
		),

		4, new Hash(
		    'contents', '<hr class="separador">'
		),             

            
		5, new Hash(
		    'contents', 'Emails de Cobrança',
		    'uri', 'admin-cobranca.php?op=relatorios&relatorio=emails_cobranca',
		    'target', 'conteudo'
		),

		6, new Hash(
		    'contents', '<hr class="separador">'
		),

		7, new Hash(
		    'contents', 'Cortesias',
		    'uri', 'admin-cobranca.php?op=relatorios&relatorio=cortesias',
		    'target', 'conteudo'
		),

		8, new Hash(
		    'contents', '<hr class="separador">'
		),        

		9, new Hash(
		    'contents', 'Adesões',
		    'uri', 'admin-cobranca.php?op=relatorios&relatorio=adesoes',
		    'target', 'conteudo'
		),
		10, new Hash(
		    'contents', 'Cancelamentos',
		    'uri', 'admin-cobranca.php?op=relatorios&relatorio=cancelamentos',
		    'target', 'conteudo'
		),
		11, new Hash(
		    'contents', 'Evolução (crescimento)',
		    'uri', 'admin-cobranca.php?op=relatorios&relatorio=evolucao',
		    'target', 'conteudo'
		),

		12, new Hash(
		    'contents', '<hr class="separador">'
		),

		13, new Hash(
		    'contents', 'Cliente Por Produto',
		    'uri', 'admin-cobranca.php?op=relatorios&relatorio=cliente_produto',
		    'target', 'conteudo'
		),
		14, new Hash(
		    'contents', 'Cliente Por Tipo Produto',
		    'uri', 'admin-cobranca.php?op=relatorios&relatorio=cliente_tipo_produto',
		    'target', 'conteudo'
		) 
	
	)
    ),
    


/**
 * MENU SUPORTE
 */
 
    4, new Hash(
        'contents', 'Suporte',
        'uri', '',
        1, new Hash(
		'contents', 'Gráficos',
		'uri', 'admin-suporte.php?op=graficos',
		'target', 'conteudo'
        ),
        2, new Hash(
		'contents', 'Monitoramento',
		'uri', 'admin-suporte.php?op=monitoramento',
		'target', 'conteudo'
        ),
        
	3, new Hash(
		'contents', '<hr class="separador">'
        ),        
        
        4, new Hash(
		'contents', 'Ferramentas',
		'uri', '',
		
		1, new Hash(
		    'contents', 'ARP',
		    'uri', 'admin-suporte.php?op=ferramentas&ferramenta=arp',
		    'target', 'conteudo'
		),
		2, new Hash(
		    'contents', 'PING',
		    'uri', 'admin-suporte.php?op=ferramentas&ferramenta=ping',
		    'target', 'conteudo'
		),
		3, new Hash(
		    'contents', '<hr class="separador">'
		),
		4, new Hash(
		    'contents', 'Calculadora de IP',
		    'uri', 'admin-suporte.php?op=ferramentas&ferramenta=ipcalc',
		    'target', 'conteudo'
		)

        ),
        
	5, new Hash(
		'contents', '<hr class="separador">'
        ),        
        
        6, new Hash(
            'contents', 'Relatórios&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            'uri', '',

		1, new Hash(
		    'contents', 'Clientes por Banda',
		    'uri', 'admin-suporte.php?op=relatorios&relatorio=banda',
		    'target', 'conteudo'
		),
		2, new Hash(
		    'contents', 'Clientes sem MAC',
		    'uri', 'admin-suporte.php?op=relatorios&relatorio=cliente_sem_mac',
		    'target', 'conteudo'
		)       
        )
    ),



/**
 * MENU CONFIGURAÇÕES
 */
 
    5, new Hash(
	'contents', 'Configurações',
	'uri', '',
        1, new Hash(
		'contents', 'Equipamentos',
		'uri', '',

		1, new Hash(
		    'contents', 'Servidores',
		    'uri', 'admin-configuracoes.php?op=equipamentos&tela=servidores',
		    'target', 'conteudo'
		),
		2, new Hash(
		    'contents', 'POPs',
		    'uri', 'admin-configuracoes.php?op=equipamentos&tela=pops',
		    'target', 'conteudo'
		),
		3, new Hash(
		    'contents', 'NAS',
		    'uri', 'admin-configuracoes.php?op=equipamentos&tela=nas',
		    'target', 'conteudo'
		)
            
        ),
        2, new Hash(
		'contents', 'Preferências',
		'uri', '',

		1, new Hash(
		    'contents', 'Resumo',
		    'uri', 'admin-configuracoes.php?op=preferencias&tela=resumo',
		    'target', 'conteudo'
		),
		2, new Hash(
		    'contents', '<hr class="separador">'
		),
		3, new Hash(
		    'contents', 'Preferências Gerais',
		    'uri', 'admin-configuracoes.php?op=preferencias&tela=geral',
		    'target', 'conteudo'
		) ,
		4, new Hash(
		    'contents', 'Preferências Provedor',
		    'uri', 'admin-configuracoes.php?op=preferencias&tela=provedor',
		    'target', 'conteudo'
		),
		5, new Hash(
		    'contents', 'Preferências Cobrança',
		    'uri', 'admin-configuracoes.php?op=preferencias&tela=cobranca',
		    'target', 'conteudo'
		),
		6, new Hash(
		    'contents', '<hr class="separador">'
		),

		7, new Hash(
		    'contents', 'Modelos de Contrato',
		    'uri', 'admin-configuracoes.php?op=preferencias&tela=modelo',
		    'target', 'conteudo'
		),
		8, new Hash(
		    'contents', '<hr class="separador">'
		),
		9, new Hash(
		    'contents', 'Cidades',
		    'uri', 'admin-configuracoes.php?op=preferencias&tela=cidades',
		    'target', 'conteudo'
		),

		10, new Hash(
		    'contents', 'Faixas de Banda',
		    'uri', 'admin-configuracoes.php?op=preferencias&tela=banda',
		    'target', 'conteudo'
		),
		11, new Hash(
		    'contents', 'Monitoramento',
		    'uri', 'admin-configuracoes.php?op=preferencias&tela=monitoramento',
		    'target', 'conteudo'
		),
		12, new Hash(
		    'contents', 'Links Externos',
		    'uri', 'admin-configuracoes.php?op=preferencias&tela=links',
		    'target', 'conteudo'
		),

		13, new Hash(
		    'contents', '<hr class="separador">'
		),
		14, new Hash(
		    'contents', 'Registro do Software',
		    'uri', 'admin-configuracoes.php?op=preferencias&tela=registro',
		    'target', 'conteudo'
		)
        ),
        
	3, new Hash(
		'contents', '<hr class="separador">'
        ),
        
        4, new Hash(
		'contents', 'Relatórios&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
		'uri', '',
		
		1, new Hash(
		    'contents', 'Carga por AP',
		    'uri', 'admin-configuracoes.php?op=relatorios&relatorio=carga&tipo=ap',
		    'target', 'conteudo'
		),
		2, new Hash(
		    'contents', 'Carga por POP',
		    'uri', 'admin-configuracoes.php?op=relatorios&relatorio=carga&tipo=pop',
		    'target', 'conteudo'
		),
		3, new Hash(
		    'contents', 'Carga por NAS',
		    'uri', 'admin-configuracoes.php?op=relatorios&relatorio=carga&tipo=nas',
		    'target', 'conteudo'
		),		
		4, new Hash(
		    'contents', 'Clientes por AP',
		    'uri', 'admin-configuracoes.php?op=relatorios&relatorio=carga&tipo=clientes_ap',
		    'target', 'conteudo'
		)

        )
    ),

    
/**
 * MENU ADMINISTRAÇÃO
 */
 
    6, new Hash(
	'contents', 'Administração',
	'uri', '',
	
        1, new Hash(
		'contents', 'Alterar Minha Senha',
		'uri', 'admin-administracao.php?op=altsenha',
		'target', 'conteudo'
            
        ),
        
	2, new Hash(
		'contents', 'Administradores',
		'uri', 'admin-administracao.php?op=administradores&tela=listagem',
		'target', 'conteudo'
        ),
        
	3, new Hash(
		'contents', '<hr class="separador">'
        ),
        
 	4, new Hash(
		'contents', 'Planos',
		'uri', 'admin-administracao.php?op=planos&tela=listagem',
		'target', 'conteudo'
        ),
        
	5, new Hash(
		'contents', '<hr class="separador">'
        ),
        
        6, new Hash(
		'contents', 'Ferramentas&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
		'uri', '',

		1, new Hash(
		    'contents', 'Backup & Restrote',
		    'uri', 'admin-administracao.php?op=backup',
		    'target', 'conteudo'
		)
        ),
        
	7, new Hash(
		'contents', '<hr class="separador">'
        ),
        
        8, new Hash(
		'contents', 'Relatórios&nbsp;',
		'uri', '',
		
		1, new Hash(
		    'contents', 'Logs dos Administradores',
		    'uri', 'admin-administracao.php?op=relatorios&relatorio=logs_admin',
		    'target', 'conteudo'
		)
        )
    ),
    

    /**
     * MENU FATURAMENTO
     */

    7, new Hash(
	'contents', 'Faturamento',
	'uri', '',
	
        1, new Hash(
		'contents', 'Relatórios&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
		'uri', '',
        
		1, new Hash(
			'contents', 'Faturamento',
			'uri', 'admin-faturamento.php?op=relatorios&relatorio=faturamento',
			'target', 'conteudo'
		),

		2, new Hash(
			'contents', 'Faturamento Corporativo',
			'uri', 'admin-faturamento.php?op=relatorios&relatorio=corporativo',
			'target', 'conteudo'
		),
		
		3, new Hash(
			'contents', 'Faturamento Por Produto',
			'uri', 'admin-faturamento.php?op=relatorios&relatorio=porproduto',
			'target', 'conteudo'
		),		

		4, new Hash(
			'contents', '<hr class="separador">'
		),

		5, new Hash(
			'contents', 'Previsão de Faturamento',
			'uri', 'admin-faturamento.php?op=relatorios&relatorio=precisao',
			'target', 'conteudo'
		)
        )
    ),


    /**
     * MENU SAIR 
     */
    
    8, new Hash(
       'contents', 'Sair',
       'uri', 'javascript:window.location="admin-login.php"'
    )   

));

// }}}
// {{{ domMenu_keramik: settings

domMenu_settings.set('domMenu_keramik', new Hash(
    'menuBarWidth', '0%',
    'menuBarClass', 'keramik_menuBar',
    'menuElementClass', 'keramik_menuElement',
    'menuElementHoverClass', 'keramik_menuElementHover',
    'menuElementActiveClass', 'keramik_menuElementHover',
    'subMenuBarClass', 'keramik_subMenuBar',
    'subMenuElementClass', 'keramik_subMenuElement',
    'subMenuElementHoverClass', 'keramik_subMenuElementHover',
    'subMenuElementActiveClass', 'keramik_subMenuElementHover',
    'subMenuMinWidth', 'auto',
    'horizontalSubMenuOffsetX', -5,
    'horizontalSubMenuOffsetY', 3,
    'distributeSpace', false,
    'openMouseoverMenuDelay', -1,
    'openMousedownMenuDelay', 0,
    'closeClickMenuDelay', 0,
    'closeMouseoutMenuDelay', -1,
    'expandMenuArrowUrl', 'view/templates/imagens/arrow.gif'
));

// }}}
// {{{ domMenu_BJ: data

domMenu_data.set('domMenu_BJ', domLib_clone(domMenu_data.get('domMenu_keramik')));

// }}}
// {{{ domMenu_BJ: settings

domMenu_settings.set('domMenu_BJ', new Hash(
    'menuBarWidth', '0%',
    'menuBarClass', 'BJ_menuBar',
    'menuElementClass', 'BJ_menuElement',
    'menuElementHoverClass', 'BJ_menuElementHover',
    'menuElementActiveClass', 'BJ_menuElementActive',
    'subMenuBarClass', 'BJ_subMenuBar',
    'subMenuElementClass', 'BJ_subMenuElement',
    'subMenuElementHoverClass', 'BJ_subMenuElementHover',
    'subMenuElementActiveClass', 'BJ_subMenuElementHover',
    'subMenuMinWidth', 'auto',
    'distributeSpace', false,
    'openMouseoverMenuDelay', -1,
    'openMousedownMenuDelay', 0,
    'closeClickMenuDelay', 0,
    'closeMouseoutMenuDelay', -1,
    'expandMenuArrowUrl', 'view/templates/imagens/arrow.gif'
));

// }}}
// {{{ domMenu_vertical: data

domMenu_data.set('domMenu_vertical', domLib_clone(domMenu_data.get('domMenu_main')));

// }}}
// {{{ domMenu_vertical: settings

domMenu_settings.set('domMenu_vertical', new Hash(
    'axis', 'vertical',
    'verticalSubMenuOffsetX', -2,
    'verticalSubMenuOffsetY', -1,
    'horizontalSubMenuOffsetX', 1,
    'baseUri', 'http://www.mojavelinux.com'
));

// }}}
// {{{ domMenu_lvertical: data

domMenu_data.set('domMenu_lvertical', domLib_clone(domMenu_data.get('domMenu_main')));

// }}}
// {{{ domMenu_lvertical: settings

domMenu_settings.set('domMenu_lvertical', new Hash(
    'axis', 'vertical',
    'verticalSubMenuOffsetX', -1,
    'verticalSubMenuOffsetY', -1,
    'horizontalSubMenuOffsetX', 1,
    'horizontalSubMenuOffsetY', 0,
    'expandMenuArrowUrl', 'larrow.gif',
	'horizontalExpand', 'west',
	'subMenuMinWidth', 'auto',
    'baseUri', 'http://www.mojavelinux.com'
));

// }}}
