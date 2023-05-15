<link rel="stylesheet" href="style.css">

<?php
session_start();
// sätt värdena för att inte få errors
$usernameform = '';
$passwordform = '';
$required_admin_level = 10;
if (isset($_POST['usernameform']) and isset($_POST['passwordform'])) {
  $usernameform = $_POST['usernameform'];
  $passwordform = $_POST['passwordform'];
}

// koppla till databasen
$host = "localhost";
$user = "root";
$password = "";
$database = "phpmedadmin";
$conn = mysqli_connect($host, $user, $password, $database);

// kolla användarnamn och lösen
$md5password = md5($passwordform);
$stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
$stmt->bind_param("ss", $usernameform, $md5password);
$stmt->execute();
$result = $stmt->get_result();


// kolla om frågan returnerade svar
if ($result->num_rows == 1) {
  $row = $result->fetch_assoc();
  // om användaren är admin
  if ($row['adminlevel'] >= $required_admin_level) {
    $_SESSION['isadmin'] = true;
  }
  // logged in är true även om användaren inte är admin
  $_SESSION['logged_in'] = true;
  header('Location: index.php');
}

?>

<!DOCTYPE html>
<html>

<head>
  <title>Login</title>
</head>

<body>
  <div id="formcontainer">
    <form id="loginform" method="post" action="login.php">
      <label for="usernameform">Username:</label>
      <input type="text" name="usernameform" id="usernameform" required>
      <label for="passwordform">Password:</label>
      <input type="password" name="passwordform" id="passwordform" required>
      <button id="post_create" type="submit">Login</button>
    </form>
  </div>
</body>

</html>