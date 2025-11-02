<?php
session_start();
include 'config/conn.php';
include 'header.php';

// Protect admin access
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

// Fetch all admin users
$result = $conn->query("SELECT id, name, email, role, created_at FROM Adminusers WHERE role='admin' ORDER BY created_at DESC");
?>


<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>

.topbar {
    background: #2c2c2c;
    color: #fff;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 3px solid #d4af37;
}
.topbar h1 {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 600;
}
.topbar .btn-gold {
    background-color: #d4af37;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 10px 16px;
    font-size: 0.9rem;
    transition: background 0.3s ease;
}
.topbar .btn-gold:hover {
    background-color: #b7952b;
}
.card {
    margin: 2rem auto;
    background: #fff;
    border-radius: 10px;
    border: none;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
.table thead {
    background: #2c2c2c;
    color: #fff;
}
.table-hover tbody tr:hover {
    background-color: #fff8e1;
}
.btn-sm {
    font-size: 0.8rem;
    border-radius: 6px;
}
.btn-darkgrey {
    background-color: #2c2c2c;
    color: #fff;
}
.btn-darkgrey:hover {
    background-color: #444;
}
@media (max-width: 768px) {
    .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>




    
    <section class="container mt-4">
        
        <header class="topbar">
      <h1><i class="fa fa-users"></i> Admin Users</h1>
      <a href="add_admin.php" class="btn-gold"><i class="fa fa-user-plus"></i> Add Admin</a>
    </header>
    
      <div class="card p-3">
        <div class="table-responsive">
          <table id="adminTable" class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th><i class="fa fa-user"></i> Name</th>
                <th><i class="fa fa-envelope"></i> Email</th>
                <th><i class="fa fa-shield"></i> Role</th>
                <th><i class="fa fa-calendar"></i> Created</th>
                <th><i class="fa fa-cog"></i> Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                    <span class="badge bg-secondary text-uppercase"><?php echo htmlspecialchars($row['role']); ?></span>
                </td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td>
                  <a href="edit_admin.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-darkgrey">
                    <i class="fa fa-edit"></i> Edit
                  </a>
                </td>
              </tr>
              <?php endwhile; ?>
              <?php else: ?>
              <tr><td colspan="6" class="text-center text-muted">No admin users found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>



<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
  $('#adminTable').DataTable({
    "pageLength": 10,
    "order": [[0, "desc"]],
    "language": {
      "search": "🔍 Search:",
      "paginate": { "previous": "«", "next": "»" }
    }
  });
});
</script>

