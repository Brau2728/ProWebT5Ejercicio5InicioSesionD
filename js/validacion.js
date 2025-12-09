function validarForm(form1){

    // Comparacion de Password        **********************
	if(form1.pas_password.value != form1.pas_password2.value)
    { //Validacion de campo instituto vacio
        alert('Las contraseñas no coinciden'); // envia mensaje
	    form1.pas_password.focus(); //enviar el cursor al campo de instituto 
		return false; //termina la funcion validarForm
	} 
	
	return true;
}

