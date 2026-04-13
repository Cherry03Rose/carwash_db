<?php
include 'connect.php';
session_start();

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle errors from other pages
$errorMessage = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);

$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>AutoWash - Payment Management</title>
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
}
.status-pending {
    background-color: #fff3cd;
    color: #856404;
}
.status-paid {
    background-color: #d4edda;
    color: #155724;
}
.status-failed {
    background-color: #f8d7da;
    color: #721c24;
}
.status-refunded {
    background-color: #e2e3e5;
    color: #383d41;
}
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
    echo '<img src="' . htmlspecialchars($_SESSION['user_photo']) . '" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
} else {
    $nameParts = explode(' ', $_SESSION['user_name'] ?? '');
    $initials = strtoupper(
        substr($nameParts[0] ?? '', 0, 1) . 
        (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '')
    );
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
      <?php if (!empty($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show w-100 d-flex justify-content-center" role="alert">
        <div class="text-center" style="width: 100%;">
            <?= htmlspecialchars($successMessage) ?>
        </div>
        <button type="button" class="close position-absolute" style="right: 1rem; top: 0.7rem;" data-dismiss="alert" aria-label="Close">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($errorMessage)): ?>
    <div class="alert alert-danger alert-dismissible fade show w-100 d-flex justify-content-center" role="alert">
        <div class="text-center" style="width: 100%;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
        <button type="button" class="close position-absolute" style="right: 1rem; top: 0.7rem;" data-dismiss="alert" aria-label="Close">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>
<div id="payment-status-alert"></div>
        <h2 class="text-center mb-4">Payment Records</h2>

        <?php
        try {
            $limit = 10;
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $offset = ($page - 1) * $limit;

            // Build WHERE clause with filters
            $where = [];
            $params = [];

            // Search filter
            if (!empty($_GET['search'])) {
                $where[] = "(u.name LIKE :search OR u.email LIKE :search OR v.plate_number LIKE :search OR p.transaction_id LIKE :search)";
                $params[':search'] = "%".trim($_GET['search'])."%";
            }

            // Status filter
            if (!empty($_GET['status']) && in_array($_GET['status'], ['pending', 'paid', 'failed', 'refunded'])) {
                $where[] = "p.status = :status";
                $params[':status'] = $_GET['status'];
            }

            // Payment method filter
            if (!empty($_GET['method']) && in_array($_GET['method'], ['cash', 'gcash', 'card'])) {
                $where[] = "p.method = :method";
                $params[':method'] = $_GET['method'];
            }

            // Date range filter
            if (!empty($_GET['date_from'])) {
                $where[] = "p.created_at >= :date_from";
                $params[':date_from'] = $_GET['date_from'];
            }
            if (!empty($_GET['date_to'])) {
                $where[] = "p.created_at <= :date_to";
                $params[':date_to'] = $_GET['date_to'];
            }

            $whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

            // Count total records
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM payments p
                JOIN bookings b ON p.booking_id = b.id
                JOIN users u ON b.user_id = u.id
                JOIN vehicles v ON b.vehicle_id = v.id
                $whereSQL");
            $countStmt->execute($params);
            $totalRecords = $countStmt->fetchColumn();
            $totalPages = ceil($totalRecords / $limit);

            // Fetch paginated data
            $sql = "
            SELECT
                p.id AS payment_id,
                p.amount,
                p.method,
                p.status,
                p.transaction_id,
                p.created_at,
                p.paid_at,
                b.id AS booking_id,
                u.name AS customer_name,
                u.email AS customer_email,
                u.contact_number,
                v.plate_number,
                b.total_price AS booking_amount,
                b.status AS booking_status
            FROM payments p
            JOIN bookings b ON p.booking_id = b.id
            JOIN users u ON b.user_id = u.id
            JOIN vehicles v ON b.vehicle_id = v.id
            $whereSQL
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset";

            $stmt = $pdo->prepare($sql);
            
            // Bind all parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $payments = [];
            $totalPages = 1;
        }

  $totalBookingStmt = $pdo->prepare("SELECT COUNT(DISTINCT b.id) FROM bookings b
    LEFT JOIN payments p ON p.booking_id = b.id
    JOIN users u ON b.user_id = u.id
    JOIN vehicles v ON b.vehicle_id = v.id
    $whereSQL
");
$totalBookingStmt->execute($params);
$totalBookings = $totalBookingStmt->fetchColumn();

?>

        <!-- Top Action Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
    <!-- Left side: Export Buttons -->
    <div class="mb-2">
        <a href="export_payments.php?format=excel" class="btn btn-success mr-2 mb-2">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <a href="export_payments.php?format=pdf" class="btn btn-danger mb-2">
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
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                      <!-- ✅ NEW STATUS FILTER -->
        <select name="status" class="form-control mr-2 mb-2" style="min-width: 130px;">
            <option value="">All Status</option>
            <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="paid" <?= ($_GET['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
            <option value="failed" <?= ($_GET['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
            <option value="refunded" <?= ($_GET['status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Refunded</option>
        </select>

                <input type="date" name="date_from" class="form-control mr-2 mb-2" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                <input type="date" name="date_to" class="form-control mb-2" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
            </form>
        </div>
    </div>
</div>
        <script>
        document.querySelectorAll('#filterForm input, #filterForm select').forEach(el => {
            el.addEventListener('change', ()=>document.getElementById('filterForm').submit());
        });
        </script>

        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle small">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:60px;">ID</th>
                        <th style="width:160px;">Customer</th>
                        <th style="width:110px;">Plate No.</th>
                        <th style="width:100px;">Booking ID</th>
                        <th style="width:120px;">Amount</th>
                        <th style="width:120px;">Method</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:150px;">Transaction ID</th>
                        <th style="width:150px;">Payment Date</th>
                        <th style="width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($payments): foreach ($payments as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['payment_id'] ?? 'N/A') ?></td>
                            <td>
                                <?= htmlspecialchars($row['customer_name'] ?? 'N/A') ?><br>
                                <small><?= htmlspecialchars($row['customer_email'] ?? '') ?></small>
                            </td>
                            <td><?= htmlspecialchars($row['plate_number'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['booking_id'] ?? 'N/A') ?></td>
                            <td>₱<?= number_format($row['amount'] ?? 0, 2) ?></td>
                            <td><?= htmlspecialchars(ucfirst($row['method'] ?? 'N/A')) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($row['status'] ?? '') ?>">
                                    <?= htmlspecialchars(ucfirst($row['status'] ?? 'N/A')) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['transaction_id'] ?? 'N/A') ?></td>
                            <td>
                                <?= !empty($row['paid_at']) ? date('M j, Y h:i A', strtotime($row['paid_at'])) : 'N/A' ?>
                            </td>
                            <td class="text-center">
                            <?php
                                $isCancelled = strtolower($row['booking_status']) === 'cancelled';
                                $disabled = $isCancelled ? 'disabled' : '';
                                $paymentStatus = strtolower($row['status'] ?? '');
                                    if ($row['booking_status'] === 'cancelled' && $row['status'] !== 'failed') {
                                    $pdo->prepare("UPDATE payments SET status = 'failed' WHERE id = ?")->execute([$row['payment_id']]);
                                    $row['status'] = 'failed'; // Sync display with DB
                                }

                            ?>
                            <form action="update_payment_status.php" method="post" class="update-payment-form form-inline d-inline-flex">
                                <input type="hidden" name="payment_id" value="<?= htmlspecialchars($row['payment_id'] ?? '') ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                
                                <select name="status" class="form-control form-control-sm mr-1" <?= $disabled ?>>
                                    <option value="pending" <?= $paymentStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="paid" <?= $paymentStatus === 'paid' ? 'selected' : '' ?>>Paid</option>
                                    <option value="failed" <?= $paymentStatus === 'failed' ? 'selected' : '' ?>>Failed</option>
                                    <option value="refunded" <?= $paymentStatus === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                                </select>

                                <button type="submit" class="btn btn-sm btn-primary" <?= $disabled ?>>Update</button>

                                <?php if ($isCancelled): ?>
                                    <small class="text-danger d-block mt-1">Booking is cancelled.</small>
                                <?php endif; ?>
                            </form>
                        </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="10">No payment records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" style="color: #444444;" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" 
                       style="color: #444444; <?= $i == $page ? 'background-color: #444444; border-color: #444444; color: white;' : '' ?>"
                       href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                       <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
                <?php if($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" style="color: #444444;" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<script>
$(document).ready(function () {
    $('body').on('submit', 'form.update-payment-form', function (e) {
        e.preventDefault();

        const form = $(this);
        const button = form.find('button');
        const select = form.find('select');
        const originalText = button.html();

        // Loading spinner
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: 'update_payment_status.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function (response) {
                button.prop('disabled', false).html(originalText);

                const alertBox = document.getElementById('payment-status-alert');
                if (response.success) {
                    // Show centered success message
                    alertBox.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" style="text-align: center;" role="alert">
                            ${response.message}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                    `;

                    setTimeout(() => {
                        alertBox.innerHTML = '';
                    }, 3000);

                    // ✅ Update status badge
                    const updatedStatus = response.status || select.val();
                    const statusCell = form.closest('tr').find('td:nth-child(7)');
                    statusCell.html(`
                        <span class="status-badge status-${updatedStatus}">
                            ${updatedStatus.charAt(0).toUpperCase() + updatedStatus.slice(1)}
                        </span>
                    `);

                    // ✅ Update payment date if paid
                    if (updatedStatus === 'paid' && response.paid_at) {
                        form.closest('tr').find('td:nth-child(9)').text(response.paid_at);
                    }

                    // ✅ Update transaction ID in column 8 if present
                    if (response.transaction_id) {
                        form.closest('tr').find('td:nth-child(8)').text(response.transaction_id);
                    }

                    // ✅ Disable select and button if needed
                    select.val(updatedStatus);
                    if (response.disable) {
                        select.prop('disabled', true);
                        button.prop('disabled', true);
                    }
                } else {
                    // Show error
                    alertBox.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert">
                            ${response.message}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                    `;
                    setTimeout(() => {
                        alertBox.innerHTML = '';
                    }, 3000);
                }
            },
            error: function (xhr, status, error) {
                const alertBox = document.getElementById('payment-status-alert');
                console.error('AJAX Error:', error);
                alertBox.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" style="text-align: center;" role="alert">
                        An error occurred while updating the payment status.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                `;
                setTimeout(() => {
                    alertBox.innerHTML = '';
                }, 3000);
                button.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>


</body>
</html>