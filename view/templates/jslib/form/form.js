/**
 *
 * TODO:
 *
 *  - Jogar funções em um .js para todos os campos
 *
 */
 
/**
 * Funções 
 */

function formOnBlur(obj) {
	obj.className="fieldWithoutFocus";
}
function formOnFocus(obj) {
	obj.className="fieldWithFocus";
}

/**
 * Funçoes para combo box
 */


/**
 * AlimentarCombo - Alimenta uma combo de acordo com a matriz passada.
 * Argumentos:
 *	camponome - ID da combo que vai ser preenchida
 *	itens - Matriz a ser usada para preencher a combo
 *	campo_chave - Nome da chave da matriz a ser utilizada como valor(valor de cada item)
 *	campo_valor - Nome da chave da matriz a ser utilizada como rótulo(nome de cada item)
 *	primeiralinha - Titulo do primeiro item da lista
 *	primeiralinhavalor - Valor do primeiro item da lista
 */
 
function AlimentarCombo(camponome, itens, campo_chave, campo_valor, primeiralinha, primeiralinhavalor, valor_selecionado) {
	campo = document.getElementById(camponome);
	
	//Se a referencia para o campo desejado nao for valido
	if(!campo) return;
	
	//Termina com todo o conteudo interno do campo
	ApagarItensCombo(camponome);
	
	//Decide se tera uma opção como conteudo "--Selecione--" dentro da listagem
	if (primeiralinha) {
		opcao = document.createElement("option");
		opcao.text = primeiralinha;
		opcao.value = (primeiralinhavalor) ? primeiralinhavalor : "";
		
		try {
			campo.add(opcao,null); // standards compliant
		} catch(ex) {
			campo.add(opcao); // IE only
		}
		
		campo.selectedIndex=0;	//Seleciona o primeiro ítem da lista
	}
	
	for(i=0; i < itens.length; i++) {
		opcao = document.createElement("option");
		opcao.text = eval("itens[" + i + "]." + campo_valor);
		opcao.value = eval("itens[" + i + "]." + campo_chave);		
		try {
			campo.add(opcao,null); // standards compliant
		} catch(ex) {
			campo.add(opcao); // IE only
		}
	}
	
	if(valor_selecionado) {
		campo.value = valor_selecionado;
	}
}


/**
 * ApagarItensCombo
 *	Agaga todos os itens de uma combobox distinta
 */
function ApagarItensCombo(id_combo, primeiro_item, primeiro_item_valor) {
	combo = document.getElementById(id_combo);
	
	if(!combo) {
		alert("O campo expecificado nao existe");
		return;
	}
	
	while(combo.length > 0) {
		combo.remove(0);
	}
	
	//Se quiser adicionar um item a lista
	if(primeiro_item) {
		opcao = document.createElement("option");
		opcao.text = primeiro_item;
		opcao.value = (primeiro_item_valor)?primeiro_item_valor:"";
		try {
			campo.add(opcao,null); // standards compliant
		} catch(ex) {
			campo.add(opcao); // IE only
		}
		combo.selectedIndex=0;
	}
}
 