<?php
include 'header.php';

// === VALIDATE ID PARAM ===
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("<div class='alert alert-danger text-center mt-5'><i class='fa fa-exclamation-circle'></i> Invalid shipment ID</div>");
}

// === FETCH SHIPMENT RECORD ===
$stmt = $conn->prepare("SELECT * FROM fedex_shipments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$shipment = $result->fetch_assoc();

if (!$shipment) {
    die("<div class='alert alert-warning text-center mt-5'><i class='fa fa-info-circle'></i> Shipment not found</div>");
}
?>

<!-- Custom Page Styling -->
<style>
    .page-header {
        background: linear-gradient(90deg, #004d40, #00695c);
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }
    .page-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 20px;
    }
    .page-header a {
        color: white;
        text-decoration: none;
        font-weight: 500;
    }
    .page-header a:hover {
        color: #ffd700;
        text-decoration: underline;
    }
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .card-header {
        background-color: #004d40;
        color: white;
        font-weight: 600;
        font-size: 16px;
    }
    .detail-label {
        font-weight: 600;
        color: #004d40;
    }
    .detail-value {
        color: #333;
    }
    pre {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        font-size: 14px;
        overflow-x: auto;
    }
    .badge-status {
        font-size: 14px;
        padding: 8px 12px;
        border-radius: 20px;
        text-transform: capitalize;
    }
    .btn-gold {
        background: #d4af37;
        color: white;
        font-weight: 600;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    .btn-gold:hover {
        background: #c79c28;
        color: white;
        transform: translateY(-2px);
    }
</style>



<!-- Main Content -->
<div class="container-fluid mb-5">
    <!-- Shipment Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h4><i class="fa fa-truck me-2"></i> Shipment Details</h4>
            <i class="fa fa-box me-2"></i> Order: <?php echo htmlspecialchars($shipment['order_id']); ?>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><span class="detail-label">Shipping Method:</span> 
                       <span class="detail-value"><?php echo htmlspecialchars($shipment['shipping_method']); ?></span></p>
                    <p><span class="detail-label">Country:</span> 
                       <span class="detail-value"><?php echo htmlspecialchars($shipment['country']); ?></span></p>
                    <p><span class="detail-label">Postal Code:</span> 
                       <span class="detail-value"><?php echo htmlspecialchars($shipment['postal_code']); ?></span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Weight:</span> 
                       <span class="detail-value"><?php echo htmlspecialchars($shipment['weight']); ?> kg</span></p>
                    <p><span class="detail-label">Rate:</span> 
                       <span class="detail-value">$<?php echo htmlspecialchars($shipment['rate']); ?></span></p>
                    <p><span class="detail-label">Created At:</span> 
                       <span class="detail-value"><?php echo htmlspecialchars($shipment['created_at']); ?></span></p>
                </div>
            </div>

            <?php
                $status = strtolower($shipment['status']);
                $badgeClass = match($status) {
                    'pushed' => 'bg-primary',
                    'delivered' => 'bg-success',
                    'failed' => 'bg-danger',
                    'pending' => 'bg-warning text-dark',
                    default => 'bg-secondary'
                };
            ?>
            <p>
                <span class="detail-label">Status:</span>
                <span class="badge badge-status <?php echo $badgeClass; ?>">
                    <?php echo ucfirst($status); ?>
                </span>
            </p>
        </div>
    </div>

    <!-- API Request -->
    <div class="card mb-4">
        <div class="card-header"><i class="fa fa-paper-plane me-2"></i> API Request</div>
        <div class="card-body">
            <pre><?php echo htmlspecialchars($shipment['api_request'] ?: 'No request data'); ?></pre>
        </div>
    </div>

    <!-- API Response -->
    <div class="card mb-4">
        <div class="card-header"><i class="fa fa-reply me-2"></i> API Response</div>
        <div class="card-body">
            <pre><?php echo htmlspecialchars($shipment['api_response'] ?: 'No response data'); ?></pre>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center mb-4">
        <a href="dashboard.php" class="btn btn-gold"><i class="fa fa-arrow-left me-1"></i> Back to All Shipments</a>
    </div>
</div>

<!-- Optional Animation Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, i * 150);
    });
});
</script>

<?php include 'footer.php'; ?>
