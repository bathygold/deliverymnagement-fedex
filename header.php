<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
require_once __DIR__ . '/config/conn.php'; // adjust path if necessary

// --- Summary metrics ---
// Total shipments
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM fedex_shipments");
$stmt->execute();
$totalShipments = $stmt->get_result()->fetch_assoc()['cnt'] ?? 0;
$stmt->close();

// Count by status
$statusCounts = ['pending' => 0, 'pushed' => 0, 'delivered' => 0, 'failed' => 0, 'other' => 0];
$stmt = $conn->prepare("SELECT status, COUNT(*) AS cnt FROM fedex_shipments GROUP BY status");
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    $s = strtolower($r['status']);
    if (array_key_exists($s, $statusCounts)) $statusCounts[$s] = (int)$r['cnt'];
    else $statusCounts['other'] += (int)$r['cnt'];
}
$stmt->close();

// Total weight & total revenue (rate)
$stmt = $conn->prepare("SELECT COALESCE(SUM(weight),0) AS total_weight, COALESCE(SUM(rate),0) AS total_revenue FROM fedex_shipments");
$stmt->execute();
$tot = $stmt->get_result()->fetch_assoc();
$totalWeight = number_format((float)$tot['total_weight'], 2);
$totalRevenue = number_format((float)$tot['total_revenue'], 2);
$stmt->close();

// Shipments by country (top 10)
$stmt = $conn->prepare("SELECT country, COUNT(*) AS cnt FROM fedex_shipments GROUP BY country ORDER BY cnt DESC LIMIT 10");
$stmt->execute();
$byCountryRes = $stmt->get_result();
$countries = [];
$countryCounts = [];
while ($r = $byCountryRes->fetch_assoc()) {
    $countries[] = $r['country'] ?: 'Unknown';
    $countryCounts[] = (int)$r['cnt'];
}
$stmt->close();

// Status distribution for pie chart
$pieLabels = [];
$pieValues = [];
foreach ($statusCounts as $k => $v) {
    $pieLabels[] = ucfirst($k);
    $pieValues[] = $v;
}

// Latest 10 shipments
$stmt = $conn->prepare("SELECT id, order_id, country, weight, rate, status, created_at FROM fedex_shipments ORDER BY created_at DESC LIMIT 10");
$stmt->execute();
$latestRes = $stmt->get_result();
$latest = [];
while ($r = $latestRes->fetch_assoc()) $latest[] = $r;
$stmt->close();

// sanitize for JS
function js_safe($val) {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard summary - Boutique Shipping</title>

  <!-- Bootstrap + DataTables + Chart.js + FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Your custom style (make sure file exists) -->
  <link rel="stylesheet" href="assets/css/style.css">

  <style>
    /* Extra styling for summary cards inside this file to ensure attractive look */
    .card-hero { border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .text-gold { color: #d4af37; }
    .badge-pill { border-radius: 999px; padding: .45rem .7rem; font-weight:600; }
    .small-muted { font-size: .85rem; color:#6b6b6b; }
    /* responsive chart containers */
    .chart-card { min-height: 280px; display:flex; align-items:center; justify-content:center; }
  </style>
</head>
<body>
  <div class="app d-flex">
    <!-- SIDEBAR -->
    <aside class="sidebar bg-dark text-white" id="sidebar">
      <div class="brand text-center py-3">
        <div style="width:64px;height:64px;background:#fff;border-radius:12px;margin:0 auto;"></div>
        <h4 class="text-gold mt-2">mjibeauty-fashion</h4>
      </div>
      <nav class="nav flex-column p-3">
        <a href="dashboard.php" class="nav-link active text-gold"><i class="fa fa-tachometer-alt me-2"></i> Dashboard</a>
        <a href="shipmentRequest.php" class="nav-link text-white"><i class="fa fa-table me-2"></i>  Shipments Orders</a>
        <a href="#" class="nav-link text-white"><i class="fa fa-upload me-2"></i> Pushed</a>
        <a href="#" class="nav-link text-white"><i class="fa fa-check me-2"></i> Delivered</a>
        <a href="#" class="nav-link text-white"><i class="fa fa-clock me-2"></i> Pending</a>
        <a href="#" class="nav-link text-white"><i class="fa fa-times me-2"></i> Failed</a>
        <a href="admin_users.php" class="nav-link text-white"><i class="fa fa-users-cog me-2"></i> Admin Users</a>
        <a href="logout.php" class="nav-link text-white mt-3"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
      </nav>
      <button id="toggleSidebar" class="collapse-btn btn btn-gold m-3"><i class="fa fa-bars"></i></button>
    </aside>
