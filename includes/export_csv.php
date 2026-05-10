<?php
require_once 'session.php';
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'export_users':
        if ($_SESSION['role'] !== 'super_admin') die("Unauthorized.");
        $filename = "users_export_" . date('Y-m-d') . ".csv";
        $query = "SELECT u.id, u.name, u.email, u.role, u.registration_no, u.identity_no, u.is_active FROM users u";
        exportToCSV($pdo, $query, $filename);
        break;

    case 'export_events':
        $filename = "events_export_" . date('Y-m-d') . ".csv";
        $query = "SELECT e.id, e.title, e.event_date, e.location, c.name as society_name FROM events e LEFT JOIN clubs c ON e.club_id = c.id";
        exportToCSV($pdo, $query, $filename);
        break;

    case 'export_finance':
        if (!in_array($_SESSION['role'], ['super_admin', 'finance_manager', 'society_head'])) die("Unauthorized.");
        $filename = "finance_export_" . date('Y-m-d') . ".csv";
        $query = "SELECT f.id, c.name as society_name, f.amount, f.type, f.description, f.record_date, f.status FROM finance_records f LEFT JOIN clubs c ON f.club_id = c.id";
        exportToCSV($pdo, $query, $filename);
        break;

    default:
        die("Invalid action.");
}

function exportToCSV($pdo, $query, $filename) {
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($data)) {
        die("No data to export.");
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    $output = fopen('php://output', 'w');

    // Add CSV Header
    fputcsv($output, array_keys($data[0]));

    // Add Data Rows
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
?>
