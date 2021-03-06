//Biblioteca Ajax


//Inicia o Ajax e retorna uma referÍncia para o objetoo de manupulacao XML
function ObjetoXMLRequest() {
	var xhr;
	
	try {  
		xhr = new ActiveXObject('Msxml2.XMLHTTP');   
	} catch (e) {
		try {   
			xhr = new ActiveXObject('Microsoft.XMLHTTP');    
		} catch (e2) {
			try {  
				xhr = new XMLHttpRequest();     
			} catch (e3) {  
				xhr = false;   
			}
		}
	}
	
	return xhr;
}


//Retorna a aconsulta do ajax
function RetornaConsulta() {

	xhr = ObjetoXMLRequest();

	xhr.onreadystatechange = function() { 
		if(xhr.readyState == 4) {
			if(xhr.status  == 200) 
				return xhr.responseText; 
			else 
				return null;
		}
	}; 

	xhr.open(GET, "data.txt",  true); 
	xhr.send(null);
}