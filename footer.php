 
  </div>

  <!-- SCRIPTS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Your app script (sidebar toggle, push handlers etc.) -->
  <script src="assets/js/app.js"></script>

  <script>
    // Data for charts from PHP arrays
    const countryLabels = <?php echo json_encode($countries, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
    const countryValues = <?php echo json_encode($countryCounts); ?>;
    const pieLabels = <?php echo json_encode($pieLabels); ?>;
    const pieValues = <?php echo json_encode($pieValues); ?>;

    // Initialize DataTable for latest shipments
    $(document).ready(function() {
      $('#latestTable').DataTable({
        pageLength: 5,
        lengthChange: false,
        searching: false,
        info: false,
        ordering: true,
      });
    });

    // Bar chart (shipments by country)
    const barCtx = document.getElementById('barCountry').getContext('2d');
    new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: countryLabels,
        datasets: [{
          label: 'Shipments',
          data: countryValues,
          backgroundColor: 'rgba(197,162,29,0.85)',
          borderColor: 'rgba(197,162,29,1)',
          borderWidth: 1
        }]
      },
      options: {
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: true, ticks: { precision:0 } }
        },
        plugins: { legend: { display: false } }
      }
    });

    // Pie chart (status)
    const pieCtx = document.getElementById('pieStatus').getContext('2d');
    new Chart(pieCtx, {
      type: 'pie',
      data: {
        labels: pieLabels,
        datasets: [{
          data: pieValues,
          backgroundColor: [
            '#F1C40F', // gold-ish
            '#6C757D', // grey
            '#28A745', // success
            '#DC3545', // danger
            '#343A40'  // dark
          ]
        }]
      },
      options: { maintainAspectRatio: false }
    });
  </script>
</body>
</html>
