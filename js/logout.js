function logoutConfirmation() {
  var confirmLogout = confirm("Deseja realmente sair?");
  if (confirmLogout) {
    window.location.href = "logout.php"; // Redirecionar para a página de logout
  } else {
    // Ação de logout cancelada
  }
}