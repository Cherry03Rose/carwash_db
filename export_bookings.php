<?php
session_start();
include 'connect.php';

// Verify admin access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

try {
    // Fetch all bookings data
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
    ORDER BY b.preferred_date DESC, b.preferred_time DESC";

    $stmt = $pdo->query($sql);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_GET['format'] === 'excel') {
        // Export to Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=bookings_export_' . date('Y-m-d') . '.xls');
        
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Customer Name</th><th>Email</th><th>Contact</th><th>Plate No.</th><th>Car Size</th><th>Date</th><th>Time</th><th>Services</th><th>Total Price</th><th>Status</th></tr>";
        
        foreach ($bookings as $row) {
            $services = $row['primary_service'];
            if (!empty($row['additional_services'])) {
                $services .= " + " . $row['additional_services'];
            }
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['booking_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['plate_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['car_size']) . "</td>";
            echo "<td>" . htmlspecialchars($row['preferred_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['preferred_time']) . "</td>";
            echo "<td>" . htmlspecialchars($services) . "</td>";
            echo "<td>" . number_format($row['total_price'], 2) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit();
    } elseif ($_GET['format'] === 'pdf') {
        // Export to PDF (requires TCPDF or similar library)
       require_once 'vendor/autoload.php';
        
        $pdf = new TCPDF();
        $pdf->SetTitle('Bookings Export');
        $pdf->AddPage();
        
        // Add header
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Bookings Export - ' . date('Y-m-d'), 0, 1, 'C');
        $pdf->Ln(10);
        
        // Add table
        $pdf->SetFont('helvetica', '', 10);
        
        $html = '<table border="1" cellpadding="4">
            <tr>
                <th width="20">ID</th>
                <th width="50">Customer</th>
                <th width="50">Email</th>
                <th width="30">Contact</th>
                <th width="30">Plate No.</th>
                <th width="25">Car Size</th>
                <th width="25">Date</th>
                <th width="25">Time</th>
                <th width="60">Services</th>
                <th width="25">Price</th>
                <th width="25">Status</th>
            </tr>';
        
        foreach ($bookings as $row) {
            $services = $row['primary_service'];
            if (!empty($row['additional_services'])) {
                $services .= " + " . $row['additional_services'];
            }
            
            $html .= '<tr>
                <td>' . $row['booking_id'] . '</td>
                <td>' . $row['customer_name'] . '</td>
                <td>' . $row['customer_email'] . '</td>
                <td>' . $row['contact_number'] . '</td>
                <td>' . $row['plate_number'] . '</td>
                <td>' . $row['car_size'] . '</td>
                <td>' . $row['preferred_date'] . '</td>
                <td>' . $row['preferred_time'] . '</td>
                <td>' . $services . '</td>
                <td>' . number_format($row['total_price'], 2) . '</td>
                <td>' . $row['status'] . '</td>
            </tr>';
        }
        
        $html .= '</table>';
        
        $pdf->writeHTML($html, true, false, false, false, '');
        $pdf->Output('bookings_export_' . date('Y-m-d') . '.pdf', 'D');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error exporting data: " . $e->getMessage();
    header("Location: admin_bookings.php");
    exit();
}
?>