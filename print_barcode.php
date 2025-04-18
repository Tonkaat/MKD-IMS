<?php
$page_title = 'Barcodes';
require_once('includes/load.php');

// Fetch all products with status_id = 1 (consumable)
$sql = "SELECT id, name, barcode FROM products WHERE categorie_id = '12'";
$result = $db->query($sql);
$products = [];

while ($row = $db->fetch_assoc($result)) {
    // If barcode is missing, generate one and update DB
    if (empty($row['barcode'])) {
        $barcode = uniqid(); // Or time() . rand(100, 999) for more variety
        $updateSql = "UPDATE products SET barcode = '{$barcode}' WHERE id = '{$row['id']}'";
        $db->query($updateSql);
        $row['barcode'] = $barcode;
    }

    $products[] = $row;
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="container my-2">
    <div class="d-flex justify-content-between align-items-center mb-3 text-primary">
        <h2 class="mb-0 fw-bold">
            <i class="bi bi-upc-scan me-2"></i>Generated Product Barcodes
        </h2>
    </div>
    
    <!-- Search Box -->
    <div class="mb-4">
        <div class="input-group">
            <span class="input-group-text bg-primary text-white">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="searchInput" class="form-control" placeholder="Search for products...">
            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                <i class="bi bi-x-circle"></i> Clear
            </button>
        </div>
    </div>
    
    <div class="row g-4" id="barcodeContainer">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 col-sm-6 barcode-item" data-name="<?= strtolower(htmlspecialchars($product['name'])) ?>">
                <div class="border rounded p-3 text-center shadow-sm h-100 barcode-box">
                    <h5 class="mb-2"><?= htmlspecialchars($product['name']) ?></h5>
                    <?php if (!empty($product['barcode'])): ?>
                        <svg class="barcode mb-2" id="barcode-<?= $product['id'] ?>"
                             data-barcode="<?= htmlspecialchars($product['barcode']) ?>"></svg>
                        <small class="text-muted d-block mb-3"><?= htmlspecialchars($product['barcode']) ?></small>
                        
                        <!-- Individual Print Button -->
                        <button class="btn btn-sm btn-outline-primary print-single-barcode" 
                                data-id="<?= $product['id'] ?>"
                                data-name="<?= htmlspecialchars($product['name']) ?>"
                                data-barcode="<?= htmlspecialchars($product['barcode']) ?>">
                            <i class="bi bi-printer"></i> Print This Barcode
                        </button>
                    <?php else: ?>
                        <p class="text-danger">No barcode available</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Single Barcode Print Modal -->
<div class="modal fade" id="singleBarcodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Print Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="singleBarcodePrint">
                <h4 id="modalProductName"></h4>
                <div id="modalBarcodeContainer"></div>
                <small class="text-muted" id="modalBarcodeText"></small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printSingleBarcode()">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (typeof JsBarcode === 'undefined') {
      console.error("JsBarcode library not loaded!");
      return;
    }
   
    // Generate all barcodes
    document.querySelectorAll("svg.barcode").forEach(function (svg) {
      try {
        const barcodeValue = svg.getAttribute("data-barcode");
        if (!barcodeValue) {
          console.error("No barcode value for:", svg);
          return;
        }
        JsBarcode(svg, barcodeValue, {
          format: "CODE128",
          displayValue: true,
          fontSize: 16,
          height: 60,
        });
      } catch (e) {
        console.error("Error generating barcode:", e);
      }
    });
    
    // Setup search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
      const searchText = this.value.toLowerCase().trim();
      filterProducts(searchText);
    });
    
    // Setup single barcode printing
    const modal = new bootstrap.Modal(document.getElementById('singleBarcodeModal'));
    
    document.querySelectorAll('.print-single-barcode').forEach(function(button) {
      button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        const barcode = this.getAttribute('data-barcode');
        
        document.getElementById('modalProductName').textContent = name;
        document.getElementById('modalBarcodeText').textContent = barcode;
        
        const container = document.getElementById('modalBarcodeContainer');
        container.innerHTML = '';
        
        const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.classList.add("barcode");
        container.appendChild(svg);
        
        JsBarcode(svg, barcode, {
          format: "CODE128",
          displayValue: true,
          fontSize: 16,
          height: 80,
        });
        
        modal.show();
      });
    });
  });
  
  function filterProducts(searchText) {
    const items = document.querySelectorAll('.barcode-item');
    let found = false;
    
    items.forEach(function(item) {
      const name = item.getAttribute('data-name');
      if (name.includes(searchText)) {
        item.style.display = '';
        found = true;
      } else {
        item.style.display = 'none';
      }
    });
    
    if (!found) {
      const container = document.getElementById('barcodeContainer');
      if (!document.getElementById('no-results')) {
        const noResults = document.createElement('div');
        noResults.id = 'no-results';
        noResults.className = 'col-12 text-center py-5';
        noResults.innerHTML = '<h4 class="text-muted"><i class="bi bi-search"></i> No products found</h4>';
        container.appendChild(noResults);
      }
    } else {
      const noResults = document.getElementById('no-results');
      if (noResults) {
        noResults.remove();
      }
    }
  }
  
  function clearSearch() {
    document.getElementById('searchInput').value = '';
    filterProducts('');
  }
  
  function printSingleBarcode() {
  const content = document.getElementById('singleBarcodePrint').innerHTML;
  const printWindow = window.open('', '_blank');
  
  printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Print Barcode</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          text-align: center;
          padding: 20px;
        }
        .barcode {
          max-width: 100%;
          height: auto;
        }
      </style>
    </head>
    <body>
      ${content}
      <script>
        window.onload = function() {
          window.print();
          setTimeout(function() { window.close(); }, 500);
        };
      </` + `script>
    </body>
    </html>
  `);
  
  printWindow.document.close();
}
</script>

<style>
  /* Add print media query to fix sidebar issues */
  @media print {
    /* Hide sidebar during print */
    .sidebar, 
    .navbar,
    .btn,
    .input-group,
    footer {
      display: none !important;
    }
    
    /* Ensure content takes full width when printing */
    body, 
    .container,
    .content-area {
      width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
    }
  }
  
  /* Styles for barcode boxes */
  .barcode-box {
    min-height: 200px;
    transition: all 0.3s ease;
  }
  
  .barcode-box:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
  }
  
  /* Style for the modal barcode */
  #modalBarcodeContainer svg {
    width: 100%;
    max-width: 300px;
    margin: 20px auto;
    display: block;
  }
  
  /* Search input styling */
  #searchInput:focus {
    box-shadow: none;
    border-color: #0d6efd;
  }
</style>
