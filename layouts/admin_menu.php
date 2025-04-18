<!-- Sidebar Navigation -->
<ul class="nav flex-column">
  <!-- Dashboard -->
  <li class="nav-item mb-2">
    <a class="nav-link d-flex align-items-center text-dark" href="admin.php">
      <i class="bi bi-house-door-fill me-2 fs-5 text-primary"></i>
      <span>Dashboard</span>
    </a>
  </li>
  <!-- User Management -->
  <li class="nav-item mb-2">
    <a class="nav-link d-flex align-items-center text-dark" href="users.php">
      <i class="bi bi-person-fill me-2 fs-5 text-primary"></i>
      <span>User Management</span>
    </a>
  </li>
  <!-- Inventory Management -->
  <li class="nav-item mb-2">
    <a class="nav-link d-flex align-items-center text-dark submenu-toggle collapsed" href="#inventorySubmenu" aria-expanded="false">
      <i class="bi bi-box-seam me-2 fs-5 text-primary"></i>
      <span>Inventory Management</span>
      <i class="bi bi-chevron-down ms-auto submenu-indicator"></i>
    </a>
    <div class="submenu" id="inventorySubmenu">
      <ul class="nav flex-column ms-3 mt-2">
        <li class="nav-item"><a class="nav-link py-2" href="product.php">View Inventory</a></li>
        <li class="nav-item"><a class="nav-link py-2" href="categorie.php">Categories</a></li>
      </ul>
    </div>
  </li>
  <!-- Reports -->
  <li class="nav-item mb-2">
    <a class="nav-link d-flex align-items-center text-dark submenu-toggle collapsed" href="#reportsSubmenu" aria-expanded="false">
      <i class="bi bi-clipboard-data-fill me-2 fs-5 text-primary"></i>
      <span>Reports</span>
      <i class="bi bi-chevron-down ms-auto submenu-indicator"></i>
    </a>
    <div class="submenu" id="reportsSubmenu">
      <ul class="nav flex-column ms-3 mt-2">
        <li class="nav-item"><a class="nav-link py-2" href="generate_report.php">Generate Report</a></li>
        <li class="nav-item"><a class="nav-link py-2" href="report_history.php">View Report History</a></li>
      </ul>
    </div>
  </li>

  <!-- Settings -->
  <li class="nav-item mb-2">
    <a class="nav-link d-flex align-items-center text-dark" href="print_barcode.php">
      <i class="bi bi-upc me-2 fs-4 text-primary"></i>
      <span>Barcodes</span>
    </a>
  </li>
</ul>


<script>
document.addEventListener('DOMContentLoaded', function() {
  // Simple direct toggle functionality
  const submenuToggles = document.querySelectorAll('.submenu-toggle');
  
  submenuToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Get the target submenu
      const targetId = this.getAttribute('href');
      const targetSubmenu = document.querySelector(targetId);
      
      // Toggle submenu directly
      if (targetSubmenu) {
        // Toggle the show class
        targetSubmenu.classList.toggle('show');
        
        // Update the toggle button state
        const isExpanded = targetSubmenu.classList.contains('show');
        this.classList.toggle('collapsed', !isExpanded);
        this.setAttribute('aria-expanded', isExpanded);
      }
    });
  });
  
  // Highlight current page in menu
  const currentPage = window.location.pathname.split('/').pop();
  if (currentPage) {
    const activeLink = document.querySelector(`.sidebar a[href="${currentPage}"]`);
    if (activeLink) {
      activeLink.classList.add('active');
      
      // If in submenu, show parent
      const parentSubmenu = activeLink.closest('.submenu');
      if (parentSubmenu) {
        parentSubmenu.classList.add('show');
        const parentToggle = document.querySelector(`[href="#${parentSubmenu.id}"]`);
        if (parentToggle) {
          parentToggle.classList.remove('collapsed');
          parentToggle.setAttribute('aria-expanded', 'true');
        }
      }
    }
  }
});

</script>