
SELECT setval('adsq_id_admin',(SELECT max(id_admin)+1 FROM adtb_admin));
SELECT setval('adsq_id_priv',(SELECT max(id_priv)+1 FROM adtb_privilegio));
SELECT setval('cbsq_id_carne',1);
SELECT setval('cbsq_id_cliente_produto',(SELECT max(id_cliente_produto)+1 FROM cbtb_cliente_produto));
SELECT setval('cbsq_id_endereco_cobranca',(SELECT max(id_endereco_cobranca)+1 FROM cbtb_endereco_cobranca));
SELECT setval('cfsq_id_cidade',(SELECT max(id_cidade)+1 FROM cftb_cidade));
SELECT setval('cftb_links_id_link_seq',(SELECT max(id_link)+1 FROM cftb_links));
SELECT setval('cfsq_id_nas',(SELECT max(id_nas)+1 FROM cftb_nas));
SELECT setval('cfsq_id_pop',(SELECT max(id_pop)+1 FROM cftb_pop));
SELECT setval('cfsq_id_rede',(SELECT max(id_rede)+1 FROM cftb_rede));
SELECT setval('cfsq_servidor_id_servidor',(SELECT max(id_servidor)+1 FROM cftb_servidor));
SELECT setval('clsq_id_cliente',(SELECT max(id_cliente)+1 FROM cltb_cliente));


SELECT setval('cnsq_id_conta',(SELECT max(id_conta)+1 FROM cntb_conta));
SELECT setval('cnsq_id_endereco_instalacao',(SELECT max(id_endereco_instalacao)+1 FROM cntb_endereco_instalacao));
SELECT setval('cxsq_id_fluxo',(SELECT max(id_fluxo)+1 FROM cxtb_fluxo));

SELECT setval('pfsq_id_forma_pagamento',(SELECT max(id_forma_pagamento)+1 FROM pftb_forma_pagamento));
SELECT setval('pfsq_id_modelo_contrato',(SELECT max(id_modelo_contrato)+1 FROM pftb_modelo_contrato));
SELECT setval('prsq_id_produto',(SELECT max(id_produto)+1 FROM prtb_produto));

spsq_id_spool
