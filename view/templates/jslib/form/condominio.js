/*******************************************************
 ****            FUNÇÕES ESPECÍFICAS                ****
 *******************************************************/
 
function AjustarCamposFormulario(formulario, indice, condominio) {

	formref = document.getElementById(formulario);

	for (i=0; i<formref.length; i++) {
		idstring = formref[i].id;
		if(idstring) {
			estring = idstring;
			estring += " = document.getElementById('" + idstring + "')";
			//alert(estring);
			eval (estring);
		}
	}

	if(indice == -1) {
	
		endereco.disabled = false;
		complemento.disabled = false;
		bairro.disabled = false;
		cep.disabled = false;
		id_cidade_combo.disabled = false;
		
		endereco.value = "";
		complemento.value = "";
		bairro.value = "";
		cep.value = "";
		id_cidade_combo.selectedIndex = 0;
		id_cidade.value = id_cidade_combo.value;
		id_bloco.disabled = true;
		ApagarItensCombo('id_bloco', '-- SELECIONE --');
		
	} else {
	
		endereco.readonly = true;
		complemento.readonly = true;
		bairro.readonly = true;
		cep.readonly = true;
		id_cidade_combo.disabled = true;
		id_bloco.disabled = false
		
		endereco.value = condominio[indice].endereco;
		complemento.value = condominio[indice].complemento;
		bairro.value = condominio[indice].bairro;
		cep.value = condominio[indice].cep;
		id_cidade_combo.value = condominio[indice].id_cidade;
		id_cidade.value = condominio[indice].id_cidade;
		
		AtualizaComboBloco('id_bloco', condominio[indice].id_condominio);
	}
	
}


function AjustarCamposFormularioContratoCobranca(formulario, indice, condominio) {

	formref = document.getElementById(formulario);
	
	if(!formref) {
		alert("Referencia ao formulário " + formulario + " não existe");
	}

	for (i=0; i<formref.length; i++) {
		idstring = formref[i].id;
		if(idstring) {
			estring = idstring;
			estring += " = document.getElementById('" + idstring + "')";
			//alert(estring);
			eval (estring);
		}
	}

	if(indice == -1) {
	
		endereco_cobranca.disabled = false;
		complemento_cobranca.disabled = false;
		bairro_cobranca.disabled = false;
		cep_cobranca.disabled = false;
		id_cidade_cobranca_combo.disabled = false;
		
		endereco_cobranca.value = "";
		complemento_cobranca.value = "";
		bairro_cobranca.value = "";
		cep_cobranca.value = "";
		id_cidade_cobranca_combo.selectedIndex = 0;
		id_cidade_cobranca.value = id_cidade_cobranca_combo.value;
		id_bloco_cobranca.disabled = true;
		ApagarItensCombo('id_bloco_cobranca', '-- SELECIONE --');
		
	} else {
		endereco_cobranca.readonly = true;
		complemento_cobranca.readonly = true;
		bairro_cobranca.readonly = true;
		cep_cobranca.readonly = true;
		id_cidade_cobranca_combo.disabled = true;
		id_bloco_cobranca.disabled = false
		
		endereco_cobranca.value = condominio[indice].endereco;
		complemento_cobranca.value = condominio[indice].complemento;
		bairro_cobranca.value = condominio[indice].bairro;
		cep_cobranca.value = condominio[indice].cep;
		id_cidade_cobranca_combo.value = condominio[indice].id_cidade;
		id_cidade_cobranca.value = condominio[indice].id_cidade;
		
		AtualizaComboBloco('id_bloco_cobranca', condominio[indice].id_condominio);
	}
	
}


function AjustarCamposFormularioContratoInstalacao(formulario, indice, condominio) {

	formref = document.getElementById(formulario);
	
	if(!formref) {
		alert("Referencia ao formulário " + formulario + " não existe");
	}

	for (i=0; i<formref.length; i++) {
		idstring = formref[i].id;
		if(idstring) {
			estring = idstring;
			estring += " = document.getElementById('" + idstring + "')";
			//alert(estring);
			eval (estring);
		}
	}

	if(indice == -1) {
	
		endereco_instalacao.disabled = false;
		complemento_instalacao.disabled = false;
		bairro_instalacao.disabled = false;
		cep_instalacao.disabled = false;
		id_cidade_instalacao_combo.disabled = false;
		
		endereco_instalacao.value = "";
		complemento_instalacao.value = "";
		bairro_instalacao.value = "";
		cep_instalacao.value = "";
		id_cidade_instalacao_combo.selectedIndex = 0;
		id_cidade_instalacao.value = id_cidade_instalacao_combo.value;
		id_bloco_instalacao.disabled = true;
		ApagarItensCombo('id_bloco_instalacao', '-- SELECIONE --');
		
	} else {
		endereco_instalacao.readonly = true;
		complemento_instalacao.readonly = true;
		bairro_instalacao.readonly = true;
		cep_instalacao.readonly = true;
		id_cidade_instalacao_combo.disabled = true;
		id_bloco_instalacao.disabled = false
		
		endereco_instalacao.value = condominio[indice].endereco;
		complemento_instalacao.value = condominio[indice].complemento;
		bairro_instalacao.value = condominio[indice].bairro;
		cep_instalacao.value = condominio[indice].cep;
		id_cidade_instalacao_combo.value = condominio[indice].id_cidade;
		id_cidade_instalacao.value = condominio[indice].id_cidade;
		
		AtualizaComboBloco('id_bloco_instalacao', condominio[indice].id_condominio, null,  true);
	}
	
}


function AjustarCamposFormularioContaCadastro(formulario, indice, condominio) {

	formref = document.getElementById(formulario);
	
	if(!formref) {
		alert("Referencia ao formulário " + formulario + " não existe");
	}

	for (i=0; i<formref.length; i++) {
		idstring = formref[i].id;
		if(idstring) {
			estring = idstring;
			estring += " = document.getElementById('" + idstring + "')";
			//alert(estring);
			eval (estring);
		}
	}

	if(indice == -1) {
	
		endereco_instalacao.disabled = false;
		complemento_instalacao.disabled = false;
		bairro_instalacao.disabled = false;
		cep_instalacao.disabled = false;
		id_cidade_instalacao_combo.disabled = false;
		
		endereco_instalacao.value = "";
		complemento_instalacao.value = "";
		bairro_instalacao.value = "";
		cep_instalacao.value = "";
		id_cidade_instalacao_combo.selectedIndex = 0;
		id_cidade_instalacao.value = id_cidade_instalacao_combo.value;
		id_bloco_instalacao.disabled = true;
		ApagarItensCombo('id_bloco_instalacao', '-- SELECIONE --');
		
	} else {
		endereco_instalacao.readonly = true;
		complemento_instalacao.readonly = true;
		bairro_instalacao.readonly = true;
		cep_instalacao.readonly = true;
		id_cidade_instalacao_combo.disabled = true;
		id_bloco_instalacao.disabled = false
		
		endereco_instalacao.value = condominio[indice].endereco;
		complemento_instalacao.value = condominio[indice].complemento;
		bairro_instalacao.value = condominio[indice].bairro;
		cep_instalacao.value = condominio[indice].cep;
		id_cidade_instalacao_combo.value = condominio[indice].id_cidade;
		id_cidade_instalacao.value = condominio[indice].id_cidade;
		
		AtualizaComboBloco('id_bloco_instalacao', condominio[indice].id_condominio, null,  true);
	}
	
}



/**
 * Funções AJAX
 */
function AtualizaComboBloco(nome_id_campo, id_condominio, id_bloco, somente_ativos) { 

	xhr = ObjetoXMLRequest();
	
	if(!id_condominio) return;
	
	campo = document.getElementById(nome_id_campo);
	if (!campo) {
		alert("O campo sinalizado para ser atualizado não existe");
		return;
	}
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4) {			
			itens = xhr.responseText;
			//alert(itens);
			AlimentarCombo(nome_id_campo, eval(itens), "id_bloco", "nome", "--SELECIONE--");
			
			if(id_bloco) {
				campo.value=id_bloco;
			}
			
		} 
	}
	
	status_ativo = "admin-ajax.php?op=condominio_blocos&id_condominio=" + id_condominio;
	if(somente_ativos) {
		status_ativo += "&ativo=1";
	}
	//alert(status_ativo);
	xhr.open("GET", status_ativo, true);
	xhr.send(null);
}
