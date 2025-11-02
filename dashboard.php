<?php

include 'header.php';

?>
    <!-- MAIN -->
    <main class="main flex-grow-1 p-3">
      <header class="topbar d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="mb-0">Dashboard</h3>
          <div class="small-muted">Overview of shipments & activity</div>
        </div>
        <div class="text-end">
          <div class="fw-bold">Welcome, <?php echo js_safe($_SESSION['user']['name']); ?></div>
          <div class="small-muted">Admin</div>
        </div>
      </header>

      <section class="content">
        <!-- summary cards -->
        <div class="row g-3 mb-4">
          <div class="col-12 col-md-4 col-lg-2">
            <div class="card card-hero p-3">
              <div class="d-flex align-items-center">
                <div class="me-3">
                  <div class="p-2 bg-light rounded-circle" style="width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa fa-box fa-lg text-gold"></i>
                  </div>
                </div>
                <div>
                  <div class="small-muted">Total Shipments</div>
                  <div class="h5 fw-bold"><?php echo number_format($totalShipments); ?></div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4 col-lg-2">
            <div class="card card-hero p-3">
              <div class="d-flex align-items-center">
                <div class="me-3">
                  <div class="p-2 bg-light rounded-circle" style="width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa fa-truck-fast fa-lg text-gold"></i>
                  </div>
                </div>
                <div>
                  <div class="small-muted">Pending</div>
                  <div class="h5 fw-bold"><?php echo $statusCounts['pending']; ?></div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4 col-lg-2">
            <div class="card card-hero p-3">
              <div class="d-flex align-items-center">
                <div class="me-3">
                  <div class="p-2 bg-light rounded-circle" style="width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa fa-upload fa-lg text-gold"></i>
                  </div>
                </div>
                <div>
                  <div class="small-muted">Pushed</div>
                  <div class="h5 fw-bold"><?php echo $statusCounts['pushed']; ?></div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4 col-lg-2">
            <div class="card card-hero p-3">
              <div class="d-flex align-items-center">
                <div class="me-3">
                  <div class="p-2 bg-light rounded-circle" style="width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa fa-check fa-lg text-gold"></i>
                  </div>
                </div>
                <div>
                  <div class="small-muted">Delivered</div>
                  <div class="h5 fw-bold"><?php echo $statusCounts['delivered']; ?></div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4 col-lg-2">
            <div class="card card-hero p-3">
              <div class="d-flex align-items-center">
                <div class="me-3">
                  <div class="p-2 bg-light rounded-circle" style="width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa fa-exclamation-triangle fa-lg text-gold"></i>
                  </div>
                </div>
                <div>
                  <div class="small-muted">Failed</div>
                  <div class="h5 fw-bold"><?php echo $statusCounts['failed']; ?></div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-lg-2">
            <div class="card card-hero p-3">
              <div class="small-muted">Total Revenue</div>
              <div class="h5 fw-bold">₦ <?php echo $totalRevenue; ?></div>
              <div class="small-muted">Total Weight: <?php echo $totalWeight; ?> kg</div>
            </div>
          </div>
        </div>

        <!-- charts -->
        <div class="row g-3 mb-4">
          <div class="col-12 col-lg-7">
            <div class="card p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Shipments by Country</h5>
                <div class="small-muted">Top 10</div>
              </div>
              <div class="chart-card">
                <canvas id="barCountry" style="width:100%;max-height:360px;"></canvas>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-5">
            <div class="card p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Status Distribution</h5>
                <div class="small-muted">All time</div>
              </div>
              <div class="chart-card">
                <canvas id="pieStatus" style="width:100%;max-height:360px;"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- latest shipments -->
        <div class="card p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">Latest Shipments</h5>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">View all</a>
          </div>
          <div class="table-responsive">
            <table id="latestTable" class="table table-striped table-bordered">
              <thead class="table-light">
                <tr>
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
                <?php foreach ($latest as $r): ?>
                <tr>
                  <td><?php echo js_safe($r['order_id']); ?></td>
                  <td><?php echo js_safe($r['country']); ?></td>
                  <td><?php echo js_safe($r['weight']); ?></td>
                  <td><?php echo js_safe($r['rate']); ?></td>
                  <td><span class="badge bg-<?php echo ($r['status'] === 'delivered') ? 'success' : (($r['status'] === 'failed') ? 'danger' : 'secondary'); ?>"><?php echo js_safe($r['status']); ?></span></td>
                  <td><?php echo js_safe($r['created_at']); ?></td>
                  <td>
                    <a href="shipment_details.php?id=<?php echo (int)$r['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                    <a href="push.php" data-id="<?php echo (int)$r['id']; ?>" class="btn btn-sm btn-gold pushBtn">Push</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

      </section>
    </main>
    
    
    <?php

include 'footer.php';

?>
    
   