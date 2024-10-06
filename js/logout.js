function logoutConfirmation() {
  var confirmLogout = confirm("Deseja realmente sair?");
  if (confirmLogout) {
    $(document).ready(function(event){
          // event.preventDefault();
          var request = {
              url: "/controllers/UsuarioController.php?action=logout",
              method: 'GET',
          };
          $.ajax(request).done(function(response) {
            const error = document.getElementById('error-message');
            if (response.error) {
              console.log(response.error)
              error.innerHTML = response.error;
            } else {
              location.assign('/login.php');
            }
          });
          })
  } else {
  }
}