-- _COBRANÇA PARA _FINANCEIRO_COBRANCA
UPDATE adtb_privilegio SET
	cod_priv = '_FINANCEIRO' || cod_priv,
	nome = 'Financeiro::' || nome
WHERE
	cod_priv ILIKE '_COBRANCA%';
------------------------------------------------------------------------

-- MODIFICA AS PERMISSÕES DE CONFIGURAÇÒES 
-- CONFIGURAÇÒES_EQUIPAMENTOS PARA _CADASTRO_EQUIPAMENTO
UPDATE adtb_privilegio SET 
	cod_priv = '_CADASTRO_EQUIPAMENTOS' ,		
	nome = 'Cadastro::Equipamentos'
WHERE
	cod_priv ILIKE '_CONFIGURACOES_EQUIPAMENTOS';
	
-- CONFIGURAÇÒES_PREFERENCIAS PARA _ADMINISTRACAO_PREFERENCIAS
UPDATE adtb_privilegio SET 
	cod_priv = '_ADMINISTRACAO_PREFERENCIAS' ,		
	nome = 'Administração::Preferências'
WHERE
	cod_priv ILIKE '_CONFIGURACOES_PREFERENCIAS';


-- CONFIGURAÇÒES_PREFERENCIAS_REGISTRO PARA _ADMINISTRACAO_PREFERENCIAS_REGISTRO
UPDATE adtb_privilegio SET 
	cod_priv = '_ADMINISTRACAO_PREFERENCIAS_REGISTRO' ,		
	nome = 'Administração::Preferências::Registro do Software'
WHERE
	cod_priv ILIKE '_CONFIGURACOES_PREFERENCIAS_REGISTRO';


-- CONFIGURAÇÒES_RELATORIOS PARA _CADASTRO_RELATORIOS
UPDATE adtb_privilegio SET 
	cod_priv = '_ADMINISTRACAO_PREFERENCIAS_REGISTRO' ,		
	nome = 'Administração::Preferências::Registro do Software'
WHERE
	cod_priv ILIKE '_CONFIGURACOES_PREFERENCIAS_REGISTRO';

------------------------------------------------------------------------------------

-- MODIFICA AS PERMISSÕES DE ADMINISTRACAO
-- ADMINISTRACAO_PLANOS PARA _CADASTRO_PLANOS
UPDATE adtb_privilegio SET 
	cod_priv = '_CADASTRO_PLANOS' ,		
	nome = 'Cadastro::Planos'
WHERE
	cod_priv ILIKE '_ADMINISTRACAO_PLANOS';

-- _ADMINISTRACAO_ADMINISTRADORES PARA _CADASTRO_ADMINISTRADORES
UPDATE adtb_privilegio SET 
	cod_priv = '_CADASTRO_ADMINISTRADORES' ,		
	nome = 'Cadastro::Administradores'
WHERE
	cod_priv ILIKE '_ADMINISTRACAO_ADMINISTRADORES';
------------------------------------------------------------------------------------