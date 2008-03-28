/**
 * validacao.js
 * Cont�m v�rias fun��es de valida��o em JS
 */
 
 
 /**
  * estaVazio - Compara se o conte�do de uma string � vazio(ou n�o cont�m nenhum caractere ou cont�m somente caracteres de espa�o
  */
function estaVazio(string) {
	var reVazio = /^\s*$/;			//Texto vazio ou preenchido somente com caracteres de espa�o
	return reVazio.test(string);
}

/**
 * emailValido - Valida uma string de email
 */
function emailValido(string) {
	//var reEmail = /^[a-zA-Z]+[a-zA-Z0-9._]*@[a-zA-Z0-9._]+\.[a-zA-Z0-9._]{2,}$/;
	var reEmail = /^[a-zA-Z]+[a-zA-Z0-9._]*@[a-zA-Z0-9._]+\.[a-zA-Z0-9._]{2,}$/;
	return reEmail.test(string);
}
 
 /**
  * ipValido - Valida uma string de endere�o IPv4
  */
function ipValido(string) {
	var reIP = /^([0-2]?[0-9]?[0-9]\.){3}([0-2]?[0-9]?[0-9])$/;

	if(!reIP.test(string)) {
		return false;
	} 

	partes = string.split(".");
	for(i=0; i<partes.length; i++) {
		if(parseInt(partes[i]) > 255 || parseInt(partes[i]) < 0) return false;
	}

	return true;
}

/**
 * macValido - Valida um endere�o mac
 */
 
function macValido(string) {
	reMac = /^([0-9A-Fa-f]{2}:){5}([0-9A-Fa-f]{2})$/
	return reMac.test(string);
}



/**
 * valitaTextoEntrada - Valida a entrada de textos com a quantidade m�nima de caracteres v�lidos excluindo os caracteres "%" e "*"
 * 	retorno:	true - Se houver uma quantidade maior ou a ao valor de numero a quantidade de caracteres v�lidos
 *			false - Se n�o satisfazer as condi��es.
 */

function validaTextoEntrada(texto, numero) {
	reEspecialChar = /([*%]|\s)/g;

	texto_temp = texto.replace(reEspecialChar, "");

	if(texto_temp.length >= numero) return true;

	return false;
}


/**
 * validaData - Valida uma data data
 * retorno:	true - se a adata for v�lida
 *		false - se a data for inv�lida
 */
function validaData(data_entrada) {
	reData = /^((0[1-9]|[12]\d)\/(0[1-9]|1[0-2])|30\/(0[13-9]|1[0-2])|31\/(0[13578]|1[02]))\/\d{4}$/;
	return reData.test(data_entrada);
}


/**
 * validaUsername - valida uma string de username
 * retorno:	true - se o username for v�lido
 *		false - se o username for inv�lido
 */
 
function validaUsernameString(username) {
	reUsername = /^[a-z0-9\._]+$/;
	return reUsername.test(username);
}
