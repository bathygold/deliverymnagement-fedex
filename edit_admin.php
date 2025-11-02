<?php
session_start();
include 'config/conn.php';
include 'header.php';

$id = (int)$_GET['id'];
$message = '';

$stmt = $conn->prepare("SELECT * FROM Adminusers WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role  = 'admin';
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    $update = $conn->prepare("UPDATE Adminusers SET name=?, email=?, password=?, role=? WHERE id=?");
    $update->bind_param("ssssi", $name, $email, $password, $role, $id);
    if ($update->execute()) {
        $message = "<div class='alert success'>✅ Admin updated successfully!</div>";
    } else {
        $message = "<div class='alert error'>❌ Error updating admin: " . $conn->error . "</div>";
    }
}
?>


<style>
/* ==== GLOBAL STYLES ==== */



/* ==== TOPBAR ==== */
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

/* ==== ALERTS ==== */
.alert {
  margin: 20px 0;
  padding: 15px;
  border-radius: 8px;
  font-weight: 500;
  text-align: center;
}

.alert.success {
  background: #e6f9ed;
  color: #1a7f37;
  border: 1px solid #1a7f37;
}

.alert.error {
  background: #fdeaea;
  color: #a32020;
  border: 1px solid #a32020;
}

/* ==== FORM ==== */
.form {
  background: #fff;
  border-radius: 12px;
  padding: 30px;
  max-width: 500px;
  margin: 40px auto;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

/* ==== BUTTON ==== */
.btn {
  background: #1e4d2b;
  color: #fff;
  padding: 12px 20px;
  border-radius: 8px;
  text-decoration: none;
  border: none;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn:hover {
  background: #216c3f;
  transform: scale(1.02);
}

/* ==== RESPONSIVE ==== */
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
      <h1>Edit Admin</h1>
      <a href="admin_users.php" class="btn">← Back</a>
    </header>

    <section class="content">
      <?php echo $message; ?>
      <form method="post" class="form" id="editAdminForm">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>New Password (leave blank to keep current)</label>
        <input type="password" name="password" placeholder="••••••••">

        <button type="submit" class="btn">Update Admin</button>
      </form>
    </section>
  </main>


<script>
// Smooth fade-in animation
document.addEventListener('DOMContentLoaded', () => {
  document.body.style.opacity = 0;
  setTimeout(() => {
    document.body.style.transition = "opacity 0.8s";
    document.body.style.opacity = 1;
  }, 100);
});

// Form submission alert animation
const form = document.getElementById('editAdminForm');
form.addEventListener('submit', () => {
  const btn = form.querySelector('.btn');
  btn.innerText = "Updating...";
  btn.disabled = true;
});
</script>
