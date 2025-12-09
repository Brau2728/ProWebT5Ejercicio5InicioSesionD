document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const loginMessage = document.getElementById("loginMessage");
    const userNameElement = document.getElementById("userName"); // Agregar elemento para mostrar el nombre del usuario

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault();
        
        // Simulando la lógica de inicio de sesión exitoso (reemplazar con tu lógica real)
        const isSuccessfulLogin = true;
        const enteredUserName = document.getElementById("nombre").value; // Nombre ingresado por el usuario
        
        if (isSuccessfulLogin) {
            // Mostrar el mensaje de inicio de sesión exitoso y el nombre del usuario
            userNameElement.textContent = enteredUserName;
            loginMessage.style.display = "block";
        }
    });
});