<?php
include 'connect.php';
session_start();

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle errors from add_booking.php
$errorMessage = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$successMessage = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

// Count total bookings (filtered or not depending on your logic)
$totalStmt = $pdo->query("SELECT COUNT(*) FROM bookings");
$totalBookings = $totalStmt->fetchColumn();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>AutoWash - Car Wash Website Template</title>
<meta content="width=device-width, initial-scale=1.0" name="viewport">

<!-- CSS Libraries -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
  body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(to bottom right, #f0f4f8, #e6ecf2);
}
.nav-bar {
    background: #202C45;
}
.nav-bar .nav-link {
    color: #ffffff;
    padding: 12px 20px;
    font-weight: 500;
    letter-spacing: 1px;
    display: block;
    border-left: 4px solid transparent;
}
.nav-bar .nav-link:hover,
.nav-bar .nav-link.active {
    color: #E81C2E;
    background-color: rgba(255,255,255,0.05);
    border-left: 4px solid #E81C2E;
    border-radius: 4px;
    text-decoration: none;
}
.nav-bar .btn-custom {
    background: #ffffff;
    color: #202C45;
    border: none;
    padding: 10px 30px;
    border-radius: 60px;
    font-weight: 500;
    transition: ease-out 0.5s;
    box-shadow: inset 0 0 0 0 #E81C2E;
    margin-bottom: 30px;
}
.nav-bar .btn-custom:hover {
    background: #E81C2E;
    color: #ffffff;
    box-shadow: inset 200px 0 0 0 #E81C2E;
}

.table-smaller-rows td,
.table-smaller-rows th {
    padding-top: 4px;
    padding-bottom: 4px;
    vertical-align: middle;
}
/* Ensure all selects look uniform */
.table select.form-control {
  min-width: 110px;
  max-width: 110px;
  padding: 0.2rem 0.5rem;
  font-size: 14px;
}

/* Same for buttons */
.table button.btn {
  padding: .25rem .6rem;
  font-size: 14px;
}
@media (max-width: 767px) {
  .d-flex.justify-content-between.flex-wrap > * {
    width: 100%;
  }
}

.status-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
}

/* Booking status styles (customize as needed) */
.status-pending {
  background-color: #fff3cd;
  color: #856404;
}
.status-approved {
  background-color: #d1ecf1;
  color: #0c5460;
}
.status-completed {
  background-color: #d4edda;
  color: #155724;
}
.status-cancelled {
  background-color: #f8d7da;
  color: #721c24;
}
</style>

</style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <nav class="nav-bar d-flex flex-column px-4 pt-5" style="width: 250px; min-height: 100vh;">
        <h4 class="text-white font-weight-bold mb-4">MENU</h4>
        <div class="admin-profile d-flex align-items-center mb-4">
            <div class="profile-circle bg-white text-dark d-flex align-items-center justify-content-center mr-3"
                style="width: 45px; height: 45px; border-radius: 50%; font-weight: bold;">
                <?php
                if (!empty($_SESSION['user_photo'])) {
                    echo '<img src="'.htmlspecialchars($_SESSION['user_photo']).'" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
                } else {
                    $nameParts = explode(' ', $_SESSION['user_name']);
                    $initials = strtoupper(substr($nameParts[0],0,1).(isset($nameParts[1])?substr($nameParts[1],0,1):''));
                    echo $initials;
                }
                ?>
            </div>
            <div class="text-white">
                <div style="font-weight:600;"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></div>
                <div style="font-size:13px;">Administrator</div>
            </div>
        </div>
        <a href="admin_booking_summary.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='admin_booking_summary.php'?'active':''; ?>">Dashboard</a>
        <a href="admin_bookings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='admin_bookings.php'?'active':''; ?>">Bookings</a>
        <a href="admin_service.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='admin_service.php'?'active':''; ?>">Services</a>
        <a href="admin_payment.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='admin_payment.php'?'active':''; ?>">Payments</a>
        <div class="mt-auto pt-4">
            <a href="logout.php" class="btn btn-custom w-100">Logout</a>
        </div>
    </nav>
    <div class="container mt-5">
  <div id="status-alert"></div>

  <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
        <?= $_SESSION['success'] ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

        <h2 class="text-center mb-4">Scheduled Bookings</h2>

<?php
include 'connect.php';


// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle errors from add_booking.php
$errorMessage = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$successMessage = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

try {
    $limit = 10;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $limit;

    // Initialize filters
    $where = [];
    $params = [];

    // Get search term (lowercase and trimmed)
    $search = strtolower(trim($_GET['search'] ?? ''));

    // Search filter
    if (!empty($search)) {
        $where[] = "(LOWER(u.name) LIKE :search 
                  OR LOWER(u.email) LIKE :search 
                  OR LOWER(v.plate_number) LIKE :search 
                  OR LOWER(s.name) LIKE :search)";
        $params[':search'] = "%$search%";
    }

    // Status filter
    if (!empty($_GET['status']) && in_array($_GET['status'], ['Pending', 'Approved', 'Completed', 'Cancelled'])) {
        $where[] = "b.status = :status";
        $params[':status'] = $_GET['status'];
    }

    // Date range filters
    if (!empty($_GET['date_from'])) {
        $where[] = "b.preferred_date >= :date_from";
        $params[':date_from'] = $_GET['date_from'];
    }
    if (!empty($_GET['date_to'])) {
        $where[] = "b.preferred_date <= :date_to";
        $params[':date_to'] = $_GET['date_to'];
    }

    // Combine filters into WHERE clause
    $whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

    // Count total bookings (with filters)
    $totalStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN vehicles v ON b.vehicle_id = v.id
        JOIN services s ON b.service_id = s.id
        $whereSQL
    ");
    $totalStmt->execute($params);
    $totalBookings = $totalStmt->fetchColumn();
    $totalPages = ceil($totalBookings / $limit);

    // Fetch paginated data
    $sql = "
        SELECT
            b.id AS booking_id,
            u.name AS customer_name,
            u.email AS customer_email,
            u.contact_number,
            v.plate_number,
            v.type AS car_size,
            b.preferred_date,
            b.preferred_time,
            s.name AS primary_service,
            s.price AS service_price,
            b.total_price,
            b.status,
            (
                SELECT GROUP_CONCAT(s2.name SEPARATOR ', ') 
                FROM booking_services bs 
                JOIN services s2 ON bs.service_id = s2.id
                WHERE bs.booking_id = b.id AND bs.service_id != b.service_id
            ) AS additional_services
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN vehicles v ON b.vehicle_id = v.id
        JOIN services s ON b.service_id = s.id
        $whereSQL
        ORDER BY b.preferred_date DESC, b.preferred_time DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);

    // Bind search & filter parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    // Bind pagination
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $bookings = [];
    $totalBookings = 0;
    $totalPages = 1;
}
?>
<!-- Top Action Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
  <!-- Left side: Export Buttons -->
  <div class="mb-2">
    <a href="export_bookings.php?format=excel" class="btn btn-success mr-2 mb-2">
      <i class="fas fa-file-excel"></i> Export Excel
    </a>
    <a href="export_bookings.php?format=pdf" class="btn btn-danger mb-2">
      <i class="fas fa-file-pdf"></i> Export PDF
    </a>
  </div>

  <!-- Wrapper with alignment and spacing -->
  <div class="d-flex flex-wrap justify-content-between align-items-start mb-4 px-2">
    <!-- Left: Total Bookings -->
    <div class="mb-2" style="font-weight: bold; font-size: 1rem; margin-top:10px; margin-right: 55px;">
      Total Bookings: <?= $totalBookings ?>
    </div>

    <!-- Right: Filter Form aligned to table -->
    <div class="flex-grow-1 text-right">
      <form id="filterForm" method="GET" class="form-inline d-inline-flex flex-wrap justify-content-end">
        <div class="input-group mr-2 mb-2">
          <input type="text" name="search" class="form-control" placeholder="Search..."
       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
          <div class="input-group-append">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
          </div>
        </div>

        <select name="status" class="form-control mr-2 mb-2" style="width:140px;">
          <option value="">All Status</option>
          <option value="Pending" <?= ($_GET['status'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending</option>
          <option value="Approved" <?= ($_GET['status'] ?? '') == 'Approved' ? 'selected' : '' ?>>Approved</option>
          <option value="Completed" <?= ($_GET['status'] ?? '') == 'Completed' ? 'selected' : '' ?>>Completed</option>
          <option value="Cancelled" <?= ($_GET['status'] ?? '') == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>

        <input type="date" name="date_from" class="form-control mr-2 mb-2"
               value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">

        <input type="date" name="date_to" class="form-control mr-2 mb-2"
               value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">

      <button type="button" class="btn mb-2"
        style="background-color: #444444; color: white; border: none;"
        onclick="window.location.href='admin_addbooking_form.php'">
  <i class="fas fa-plus"></i> Add Booking
</button>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('#filterForm input, #filterForm select').forEach(el => {
  el.addEventListener('change', () => document.getElementById('filterForm').submit());
});
</script>

<div class="table-responsive">
<table class="table table-bordered table-striped text-center align-middle small">
<thead class="thead-dark">
<tr>
  <th style="width:40px;">ID</th>
  <th style="width:160px;">Customer Name</th>
  <th style="width:180px;">Contact Info</th>
  <th style="width:110px;">Plate No.</th>
  <th style="width:100px;">Car Size</th>
  <th style="width:130px;">Preferred Date</th>
  <th style="width:140px;">Service Needed</th>
  <th style="width:110px;">Total Price</th>
  <th style="width:110px;">Status</th>
  <th style="width:180px;">Actions</th>
</tr>
</thead>

<tbody>
<?php if ($bookings): foreach ($bookings as $row): ?>
<tr>
    <td><?= htmlspecialchars($row['booking_id'] ?? 'N/A') ?></td>
    <td><?= htmlspecialchars($row['customer_name'] ?? 'N/A') ?></td>
    <td>
        <?= htmlspecialchars($row['customer_email'] ?? 'N/A') ?><br>
        <?= htmlspecialchars($row['contact_number'] ?? 'N/A') ?>
    </td>
    <td><?= htmlspecialchars($row['plate_number'] ?? 'N/A') ?></td>
    <td><?= htmlspecialchars($row['car_size'] ?? 'N/A') ?></td>
    <td>
        <?= htmlspecialchars($row['preferred_date'] ?? 'N/A') ?><br>
        <?= htmlspecialchars($row['preferred_time'] ?? '') ?>
    </td>
   <td>
    <?= htmlspecialchars($row['primary_service'] ?? 'N/A') ?>
    <?php if (!empty($row['additional_services'])): ?>
        <br><small>+ <?= htmlspecialchars($row['additional_services']) ?></small>
    <?php endif; ?>
</td>
    <td>₱<?= number_format($row['total_price'] ?? 0, 2) ?></td>
   <td>
  <span class="status-badge status-<?= strtolower($row['status']) ?>">
    <?= ucfirst(strtolower($row['status'])) ?>
  </span>
</td>
    <!-- Change this part of the table row -->
<td class="text-center">
  <form class="update-status-form form-inline d-inline-flex">
   <input type="hidden" name="booking_id" value="<?= htmlspecialchars($row['booking_id'] ?? '') ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

 <select name="status" class="form-control">
 <option value="Pending" <?= strtolower($row['status']) == 'pending' ? 'selected' : '' ?>>Pending</option>
<option value="Approved" <?= strtolower($row['status']) == 'approved' ? 'selected' : '' ?>>Approved</option>
<option value="Completed" <?= strtolower($row['status']) == 'completed' ? 'selected' : '' ?>>Completed</option>
<option value="Cancelled" <?= strtolower($row['status']) == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
</select>
    <button type="submit" class="btn btn-sm btn-primary">Update</button>
  </form>
</td>


</tr>
<?php endforeach; else: ?>
<tr><td colspan="10">No records found.</td></tr>
<?php endif; ?>
</tbody>

</table>
</div>

<!-- Pagination Links -->
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-auto">
      <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
          <?php if ($page > 1): ?>
            <li class="page-item">
              <a class="page-link" style="color: #444444;" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Previous</a>
            </li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
              <a class="page-link"
                 style="color: #444444; <?= $i == $page ? 'background-color: #444444; border-color: #444444; color: white;' : '' ?>"
                 href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                <?= $i ?>
              </a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item">
              <a class="page-link" style="color: #444444;" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </div>
</div>
</div>
</div>
<!-- Add Booking Modal -->
<div class="modal fade" id="addBookingModal" tabindex="-1" role="dialog" aria-labelledby="addBookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="admin_addbooking.php" method="post" class="modal-content">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <div class="modal-header">
        <h5 class="modal-title" id="addBookingModalLabel">Add New Booking</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body row">
        <div class="form-group col-md-6">
          <label>Full Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group col-md-6">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group col-md-6">
          <label>Contact Number <small class="text-muted">(e.g. 09876767231)</small></label>
          <input type="text" name="contact_number" class="form-control" pattern="\d{11}" maxlength="11" minlength="11" placeholder="e.g. 09876767231" required>
        </div>
        <div class="form-group col-md-6">
          <label>Plate Number</label>
          <input type="text" name="plate_number" class="form-control" required>
        </div>
        <div class="form-group col-md-6">
          <label>Preferred Date</label>
          <input type="date" name="preferred_date" class="form-control" required>
        </div>
        <div class="form-group col-md-6">
          <label>Preferred Time</label>
          <select name="preferred_time" class="form-control" required>
            <option value="">Select Time</option>
            <?php
            // Generate time slots from 8:00 AM to 5:00 PM
            for ($hour = 8; $hour <= 17; $hour++) {
                $time = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
                echo "<option value='$time'>$time</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-group col-md-6">
          <label>Car Size</label>
          <select name="car_size" class="form-control" required>
            <option value="">Select Size</option>
            <option value="Small">Small</option>
            <option value="Medium">Medium</option>
            <option value="Large">Large</option>
          </select>
        </div>
        <div class="form-group col-md-6">
          <label>Payment Method</label>
          <select name="payment_method" class="form-control" required>
            <option value="">Select Method</option>
            <option value="cash">Cash</option>
            <option value="gcash">GCash</option>
            <option value="card">Credit/Debit Card</option>
          </select>
        </div>
        <div class="form-group col-md-12">
          <label>Service Needed <small class="text-muted">(Select one or more)</small></label>
          <div class="row" id="servicesContainer">
            <?php
            // Fetch services from database
            $services = $pdo->query("SELECT * FROM services")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($services as $service) {
                echo '<div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input service-checkbox" type="checkbox" 
                            name="service_needed[]" value="'.$service['id'].'" 
                            id="service_'.$service['id'].'" data-price="'.$service['price'].'">
                        <label class="form-check-label" for="service_'.$service['id'].'">
                            '.htmlspecialchars($service['name']).' (₱'.number_format($service['price'], 2).')
                        </label>
                    </div>
                </div>';
            }
            ?>
          </div>
        </div>
        <div class="form-group col-md-6">
          <label>Estimated Total Price</label>
          <input type="text" class="form-control" id="estimatedTotal" value="₱0.00" readonly>
        </div>
        <div class="form-group col-md-6">
          <label>Status</label>
          <select name="status" class="form-control" required>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save Booking</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>


<script>
document.querySelectorAll('.update-status-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const select = form.querySelector('select');
        const newStatus = select.value;

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        }

        fetch('update_status.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const alertBox = document.getElementById('status-alert');
            if (data.success) {
                // ✅ Show success message
                alertBox.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show" style="text-align: center;" role="alert">
                        ${data.message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                `;

                // Update the status cell beside dropdown (assuming 9th column)
                const statusCell = form.closest('tr').querySelector('td:nth-child(9)');
                if (statusCell) {
                    statusCell.innerHTML = `
                        <span class="status-badge status-${newStatus.toLowerCase()}">
                            ${capitalize(newStatus)}
                        </span>
                    `;
                }

                // Force the dropdown to reflect the updated value (prevents reset)
                select.value = newStatus;

            } else {
                // Show error message
                alertBox.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert">
                        ${data.message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                `;
            }

            // Auto-hide alert after 3 seconds
            setTimeout(() => {
                alertBox.innerHTML = '';
            }, 3000);
        })
        .catch(error => {
            console.error('Error:', error);
            const alertBox = document.getElementById('status-alert');
            alertBox.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert">
                    An unexpected error occurred.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
            `;
            setTimeout(() => {
                alertBox.innerHTML = '';
            }, 3000);
        });
    });
});
</script>



</body>
</html>