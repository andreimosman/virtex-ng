DROP SEQUENCE evsq_id_evento;
CREATE SEQUENCE evsq_id_evento;


DROP TABLE evtb_evento;

CREATE TABLE evtb_evento (
	id_evento int not null,
	datahora timestamp default now(),
	tipo varchar(10),
	id_admin int,
	confirmado_por_senha boolean,
	natureza varchar(20),
	ipaddr inet,
	descricao text,
	id_conta int,
	id_cobranca int,
	id_cliente_produto int,
	primary key(id_evento)
);

ALTER TABLE evtb_evento ADD  CONSTRAINT evtb_evento_id_admin_fkey FOREIGN KEY (id_admin)
      REFERENCES adtb_admin (id_admin) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE evtb_evento ADD  CONSTRAINT evtb_evento_id_cliente_produto_fkey FOREIGN KEY (id_cliente_produto)
      REFERENCES cbtb_cliente_produto (id_cliente_produto) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE evtb_evento ADD  CONSTRAINT evtb_evento_id_cobranca_fkey FOREIGN KEY (id_cobranca)
      REFERENCES cbtb_faturas (id_cobranca) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT;
-- ALTER TABLE evtb_evento ADD  CONSTRAINT evtb_evento_id_conta_fkey FOREIGN KEY (id_conta)
--       REFERENCES cntb_conta (id_conta) MATCH SIMPLE
--      ON UPDATE RESTRICT ON DELETE RESTRICT;
 


CREATE INDEX evix_evento_datahora ON evtb_evento(datahora);
CREATE INDEX evix_evento_datahora_natureza ON evtb_evento(datahora,natureza);
CREATE INDEX evix_evento_datahora_natureza_tipo ON evtb_evento(datahora,natureza,tipo);
CREATE INDEX evix_evento_admin_darahora ON evtb_evento(id_admin,datahora);
CREATE INDEX evix_evento_admin_darahora_tipo ON evtb_evento(id_admin,datahora,tipo);
CREATE INDEX evix_evento_admin_datahora_natureza ON evtb_evento(id_admin,datahora,natureza);
CREATE INDEX evix_evento_admin_datahora_natureza_tipo ON evtb_evento(id_admin,datahora,natureza,tipo);
