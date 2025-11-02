<?php
session_start();
include 'config/conn.php';

// Turn on error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Please enter both email and password.";
    } else {
        // Prepare query
        $stmt = $conn->prepare("SELECT id, name, email, password FROM Adminusers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

// 
            
            if ($user) {
            // Set session
            $_SESSION['user'] = [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email']
            ];

           echo '<script>window.location.href = "dashboard.php";</script>';
   
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login | FedEx Dashboard</title>
<link rel="stylesheet" href="assets/css/style.css">

</head>
<body class="auth-page">
  <div class="auth-card">
    <div class="logo-space"></div>
    <h2>Login</h2>

    <?php if (!empty($errors)): ?>
      <div class="alert"><?php echo implode('<br>', $errors); ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Email</label>
      <input type="email" name="email" placeholder="Enter your email" required>

      <label>Password</label>
      <input type="password" name="password" placeholder="Enter your password" required>

      <button type="submit" class="btn">Login</button>
    </form>

    <p class="muted">Default: admin@example.com / Password123!</p>
  </div>
</body>
</html>
