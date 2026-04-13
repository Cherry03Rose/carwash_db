<?php
require 'connect.php';
session_start();

// Redirect if not admin
if (!isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit;
}


// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ADD NEW SERVICE
    if (isset($_POST['add_service'])) {
        $stmt = $pdo->prepare("
            INSERT INTO services (name, description, price, duration, category, type)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            trim($_POST['name']),
            trim($_POST['description']),
            $_POST['price'],
            $_POST['duration'],
            $_POST['category'],
            $_POST['type']
        ]);
        $_SESSION['success'] = 'Service added successfully!';
    } 
    // UPDATE SERVICE
    elseif (isset($_POST['update_service'])) {
        $stmt = $pdo->prepare("
            UPDATE services
            SET name = ?, description = ?, price = ?, duration = ?, category = ?, type = ?
            WHERE id = ?
        ");
        $stmt->execute([
            trim($_POST['name']),
            trim($_POST['description']),
            $_POST['price'],
            $_POST['duration'],
            $_POST['category'],
            $_POST['type'],
            $_POST['service_id']
        ]);
        $_SESSION['success'] = 'Service updated successfully!';
    } 
    // DELETE SERVICE
    elseif (isset($_POST['delete_service'])) {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$_POST['service_id']]);
        $_SESSION['success'] = 'Service deleted successfully!';
    }

    // Redirect back
    header("Location: admin_service.php");
    exit;
}

// Fetch all services
$services = $pdo->query("SELECT * FROM services ORDER BY category, name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AutoWash - Admin Services</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS Libraries -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
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
        
        .dashboard-container {
            flex: 1;
            padding: 40px 60px;
        }
        
        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .table-responsive {
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        }
        
        .action-btns .btn {
            padding: 5px 10px;
            font-size: 14px;
            margin: 2px;
        }
        
        @media (max-width: 767px) {
            .dashboard-container {
                padding: 20px;
            }
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

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success'] ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span>&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
        <h2 class="text-center mb-4">Services Management</h2>
        
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success'] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <!-- Add New Service Form -->
        <div class="card mb-4">
    <div class="card-body">
        <h4 class="card-title">Add New Service</h4>
        <form method="POST">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Service Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Price (₱)</label>
                    <input type="number" name="price" step="0.01" min="0" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Duration (mins)</label>
                    <input type="number" name="duration" min="1" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2" required></textarea>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control" required>
                    <option value="Basic">Basic</option>
                    <option value="Premium">Premium</option>
                    <option value="Mechanical">Mechanical</option>
                </select>
            </div>
            <div class="form-group">
    <label>Type</label>
    <select name="type" class="form-control" required>
        <option value="service">Individual Service</option>
        <option value="package">Package Deal</option>
    </select>
</div>

            <button type="submit" name="add_service" class="btn btn-primary">Add Service</button>
        </form>
    </div>
</div>
        
        <!-- Services Table -->
        <div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Service Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Duration</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
            <tr>
                <td><?= $service['id'] ?></td>
                <td><?= htmlspecialchars($service['name']) ?></td>
                <td><?= htmlspecialchars($service['description']) ?></td>
                <td>₱<?= number_format($service['price'], 2) ?></td>
                <td><?= $service['duration'] ?> mins</td>
                <td><?= htmlspecialchars($service['category']) ?></td>
                <td>
                    <!-- Edit Button -->
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editModal<?= $service['id'] ?>">
                        <i class="fas fa-edit"></i>
                    </button>
                    
                    <!-- Delete Button -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                        <button type="submit" name="delete_service" class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>

<!-- Edit Service Modal -->
<div class="modal fade" id="editModal<?= $service['id'] ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                    <div class="form-group">
                        <label>Service Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($service['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" required><?= htmlspecialchars($service['description']) ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Price (₱)</label>
                            <input type="number" name="price" step="0.01" min="0" class="form-control" value="<?= $service['price'] ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Duration (mins)</label>
                            <input type="number" name="duration" min="1" class="form-control" value="<?= $service['duration'] ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" class="form-control" required>
                            <option value="Basic" <?= $service['category'] == 'Basic' ? 'selected' : '' ?>>Basic</option>
                            <option value="Premium" <?= $service['category'] == 'Premium' ? 'selected' : '' ?>>Premium</option>
                            <option value="Mechanical" <?= $service['category'] == 'Mechanical' ? 'selected' : '' ?>>Mechanical</option>
                        </select>
                    </div>
                    <!-- NEW TYPE DROPDOWN -->
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control" required>
                            <option value="service" <?= $service['type'] == 'service' ? 'selected' : '' ?>>Individual Service</option>
                            <option value="package" <?= $service['type'] == 'package' ? 'selected' : '' ?>>Package Deal</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="update_service" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-dismiss alerts after 3 seconds
setTimeout(() => {
    $('.alert').alert('close');
}, 3000);
</script>

</body>
</html>