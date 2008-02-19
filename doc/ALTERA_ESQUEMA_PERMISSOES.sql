-- _COBRAN�A PARA _FINANCEIRO_COBRANCA
UPDATE adtb_privilegio SET
	cod_priv = '_FINANCEIRO' || cod_priv,
	nome = 'Financeiro::' || nome
WHERE
	cod_priv ILIKE '_COBRANCA%';
------------------------------------------------------------------------

-- MODIFICA AS PERMISS�ES DE CONFIGURA��ES 
-- CONFIGURA��ES_EQUIPAMENTOS PARA _CADASTRO_EQUIPAMENTO
UPDATE adtb_privilegio SET 
	cod_priv = '_CADASTRO_EQUIPAMENTOS' ,		
	nome = 'Cadastro::Equipamentos'
WHERE
	cod_priv ILIKE '_CONFIGURACOES_EQUIPAMENTOS';
	
-- CONFIGURA��ES_PREFERENCIAS PARA _ADMINISTRACAO_PREFERENCIAS
UPDATE adtb_privilegio SET 
	cod_priv = '_ADMINISTRACAO_PREFERENCIAS' ,		
	nome = 'Administra��o::Prefer�ncias'
WHERE
	cod_priv ILIKE '_CONFIGURACOES_PREFERENCIAS';


-- CONFIGURA��ES_PREFERENCIAS_REGISTRO PARA _ADMINISTRACAO_PREFERENCIAS_REGISTRO
UPDATE adtb_privilegio SET 
	cod_priv = '_ADMINISTRACAO_PREFERENCIAS_REGISTRO' ,		
	nome = 'Administra��o::Prefer�ncias::Registro do Software'
WHERE
	cod_priv ILIKE '_CONFIGURACOES_PREFERENCIAS_REGISTRO';


-- CONFIGURA��ES_RELATORIOS PARA _CADASTRO_RELATORIOS
UPDATE adtb_privilegio SET 
	cod_priv = '_ADMINISTRACAO_PREFERENCIAS_REGISTRO' ,		
	nome = 'Administra��o::Prefer�ncias::Registro do Software'
WHERE
	cod_priv ILIKE '_CONFIGURACOES_PREFERENCIAS_REGISTRO';

------------------------------------------------------------------------------------

-- MODIFICA AS PERMISS�ES DE ADMINISTRACAO
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