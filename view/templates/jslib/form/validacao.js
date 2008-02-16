/**
 * validacao.js
 * Contém várias funções de validação em JS
 */
 
 
 /**
  * estaVazio - Compara se o conteúdo de uma string é vazio(ou não contém nenhum caractere ou contém somente caracteres de espaço
  */
function estaVazio(string) {
	var reVazio = /^\s*$/;			//Texto vazio ou preenchido somente com caracteres de espaço
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
  * ipValido - Valida uma string de endereço IPv4
  */
function ipValido(string) {
	var reIP = /^([0-2]?[0-9]?[0-9]\.){3}([0-2]?[0-9]?[0-9])$/;

	if(!reIP.test(string)) {
		return false;
	} 

	partes = string.split(".");
	for(i=0; i<partes.length; i++) {
		if(parteInt(partes[i]) > 255 || parteInt(partes[i]) < 0) return false;
	}

	return true;
}

/**
 * macValido - Valida um endereço mac
 */
 
function macValido(string) {
	reMac = /^([0-9A-Fa-f]{2}:){5}([0-9A-Fa-f]{2})$/
	return reMac.test(string);
}



/**
 * valitaTextoEntrada - Valida a entrada de textos com a quantidade mínima de caracteres válidos excluindo os caracteres "%" e "*"
 * 	retorno:	true - Se houver uma quantidade maior ou a ao valor de numero a quantidade de caracteres válidos
 *			false - Se não satisfazer as condições.
 */

function validaTextoEntrada(texto, numero) {
	reEspecialChar = /([*%]|\s)/g;

	texto_temp = texto.replace(reEspecialChar, "");

	if(texto_temp.length >= numero) return true;

	return false;
}