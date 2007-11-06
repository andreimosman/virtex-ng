
ALTER TABLE adtb_privilegio ADD COLUMN tem_leitura bool DEFAULT 't';
ALTER TABLE adtb_privilegio ADD COLUMN tem_gravacao bool DEFAULT 't';

ALTER TABLE adtb_privilegio ADD COLUMN descricao_leitura varchar(255);
ALTER TABLE adtb_privilegio ADD COLUMN descricao_gravacao varchar(255);


