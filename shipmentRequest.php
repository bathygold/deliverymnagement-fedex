<?php
session_start();
include 'config/conn.php';
include 'header.php';

// Get search query if any
$q = trim($_GET['q'] ?? '');

// Fetch only pending or testing shipments
if ($q !== '') {
    $stmt = $conn->prepare("SELECT id, order_id, shipping_method, postal_code, country, weight, rate, api_request, api_response, status, created_at 
                            FROM fedex_shipments 
                            WHERE (status = 'pending' OR status = 'testing')
                            AND (order_id LIKE ? OR country LIKE ? OR status LIKE ?)
                            ORDER BY created_at DESC");
    $param = "%{$q}%";
    $stmt->bind_param("sss", $param, $param, $param);
} else {
    $stmt = $conn->prepare("SELECT id, order_id, shipping_method, postal_code, country, weight, rate, api_request, api_response, status, created_at 
                            FROM fedex_shipments 
                            WHERE status = 'pending' OR status = 'testing'
                            ORDER BY created_at DESC");
}
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="main flex-grow-1 p-3">
  <header class="topbar d-flex justify-content-between align-items-center mb-4">
    <form method="get" class="d-flex w-50">
      <input type="search" name="q" class="form-control me-2" placeholder="Search order, country or status" value="<?php echo htmlspecialchars($q); ?>">
      <button type="submit" class="btn btn-gold"><i class="fa fa-search"></i></button>
    </form>
    <div class="user-info fw-bold text-dark">
      Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
    </div>
  </header>

  <section class="content">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="text-dark fw-bold">Pending / Testing Shipments</h2>
      <button id="pushSelected" class="btn btn-gold"><i class="fa fa-upload"></i> Push Selected</button>
    </div>

    <div class="table-responsive">
      <table id="shipmentsTable" class="table table-striped table-hover table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>Order ID</th>
            <th>Country</th>
            <th>Weight</th>
            <th>Rate</th>
            <th>Status</th>
            <th>Created</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
          <tr data-id="<?php echo $row['id']; ?>">
            <td><input type="checkbox" class="rowCheckbox" value="<?php echo $row['id']; ?>"></td>
            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
            <td><?php echo htmlspecialchars($row['country']); ?></td>
            <td><?php echo htmlspecialchars($row['weight']); ?></td>
            <td><?php echo htmlspecialchars($row['rate']); ?></td>
            <td>
              <span class="badge bg-<?php echo $row['status'] === 'pending' ? 'warning' : ($row['status'] === 'testing' ? 'info' : 'secondary'); ?>">
                <?php echo htmlspecialchars($row['status']); ?>
              </span>
            </td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td>
              <a href="shipment_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                <i class="fa fa-eye"></i> View
              </a>
              <a href="push.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-gold pushBtn">
                <i class="fa fa-paper-plane"></i> Push
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<!-- Include DataTables and styling -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
  // Initialize DataTable
  $('#shipmentsTable').DataTable({
    pageLength: 10,
    ordering: true,
    responsive: true,
    language: {
      search: "_INPUT_",
      searchPlaceholder: "Search shipments..."
    }
  });

  // Select all checkbox
  $('#selectAll').on('change', function() {
    $('.rowCheckbox').prop('checked', $(this).prop('checked'));
  });

  // Push button with ID
  $('.pushBtn').on('click', function(e) {
    e.preventDefault();
    const id = $(this).attr('href').split('id=')[1];
    if (confirm('Are you sure you want to push shipment ID ' + id + '?')) {
      window.location.href = 'push.php?id=' + id;
    }
  });
});
</script>

<style>
.table thead th {
  background-color: #1e4d2b !important;
  color: white;
}

.btn-gold {
  background-color: #d4af37;
  color: white;
  border: none;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-gold:hover {
  background-color: #c49a26;
  color: #fff;
  transform: scale(1.05);
}

.badge {
  padding: 6px 10px;
  border-radius: 8px;
  font-size: 13px;
}

@media (max-width: 768px) {
  .table-responsive {
    overflow-x: auto;
  }
}
</style>

<?php include 'footer.php'; ?>
