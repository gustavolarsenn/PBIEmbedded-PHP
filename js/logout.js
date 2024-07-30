function logoutConfirmation() {
  var confirmLogout = confirm("Deseja realmente sair?");
  if (confirmLogout) {
    $(document).ready(function(event){
          // event.preventDefault();
          var request = {
              url: "../../controllers/UsuarioController.php",
              method: 'GET',
              data: [{
                  name: 'action',
                  value: 'logout'
              }],
              dataType: 'json'
          };
          console.log("Logging out...")
          $.ajax(request).done(function(response) {
            const error = document.getElementById('error-message');
            if (response.error) {
              error.innerHTML = response.error;
            } else {
              console.log("Logged out...");
              location.assign('/login.php');
            }
          });
          })
  } else {
  }
}