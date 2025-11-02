<?php
session_start();
include 'config/conn.php';
include 'header.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role  = 'admin';

    $stmt = $conn->prepare("INSERT INTO Adminusers (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $email, $pass, $role);
    if ($stmt->execute()) {
        $message = "<div class='alert success'>✅ Admin user added successfully!</div>";
    } else {
        $message = "<div class='alert error'>❌ Error adding admin user: " . $conn->error . "</div>";
    }
}
?>


<style>
/* === BASE STYLING === */


/* === TOPBAR === */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #1e4d2b;
  color: #fff;
  padding: 15px 20px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.topbar h1 {
  font-size: 20px;
  font-weight: 600;
}

/* === ALERTS === */
.alert {
  margin: 20px 0;
  padding: 15px;
  border-radius: 8px;
  font-weight: 500;
  text-align: center;
  transition: 0.4s ease;
}

.alert.success {
  background: #e7f9ed;
  color: #157a2d;
  border: 1px solid #157a2d;
}

.alert.error {
  background: #fdeaea;
  color: #a32020;
  border: 1px solid #a32020;
}

/* === FORM STYLING === */
.form {
  background: #fff;
  border-radius: 12px;
  padding: 30px;
  max-width: 500px;
  margin: 40px auto;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
}

.form:hover {
  transform: scale(1.01);
}

.form label {
  display: block;
  margin-bottom: 6px;
  color: #333;
  font-weight: 600;
}

.form input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  margin-bottom: 20px;
  transition: border-color 0.3s;
}

.form input:focus {
  border-color: #1e4d2b;
  outline: none;
}

/* === BUTTON === */
.btn {
  background: #1e4d2b;
  color: #fff;
  padding: 12px 20px;
  border-radius: 8px;
  border: none;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn:hover {
  background: #216c3f;
  transform: scale(1.03);
}

/* === RESPONSIVE === */
@media (max-width: 600px) {
  .form {
    padding: 20px;
    margin: 20px;
  }

  .topbar h1 {
    font-size: 18px;
  }
}
</style>


<main class="main flex-grow-1 p-3">
    <header class="topbar">
      <h1>Add New Admin</h1>
      <a href="admin_users.php" class="btn">← Back</a>
    </header>

    <section class="content">
      <?php echo $message; ?>
      <form method="post" class="form" id="addAdminForm">
        <label>Name</label>
        <input type="text" name="name" required placeholder="Enter full name">

        <label>Email</label>
        <input type="email" name="email" required placeholder="Enter email address">

        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter password">

        <button type="submit" class="btn">Add Admin</button>
      </form>
    </section>
  </main>


<script>
// Fade in effect
document.addEventListener('DOMContentLoaded', () => {
  document.body.style.opacity = 0;
  setTimeout(() => {
    document.body.style.transition = 'opacity 0.7s';
    document.body.style.opacity = 1;
  }, 100);
});

// Loading effect on submit
const form = document.getElementById('addAdminForm');
form.addEventListener('submit', () => {
  const btn = form.querySelector('.btn');
  btn.innerText = 'Adding...';
  btn.disabled = true;
});
</script>

