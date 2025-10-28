<?php
// export.php?code=12345&date_from=2025-10-01&date_to=2025-10-28

require_once __DIR__ . '/../../../env.php';

// Connect to MySQL
$mysqli = new mysqli($host, $user, $pass, $dbname);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");

// Check password
if (!isset($_GET['code'])) {
    die('No code provided.');
}
$inputCode = $_GET['code'];
$codeQuery = "SELECT `value` FROM `globals` WHERE `name` = 'report_pass' LIMIT 1";
$codeResult = $mysqli->query($codeQuery);
if (!$codeResult || $codeResult->num_rows === 0) {
    die('report_pass not found.');
}
$reportPass = $codeResult->fetch_assoc()['value'];
if ($inputCode !== $reportPass) {
    die('Access denied.');
}

// Set the table name
$tableName = 'pc';

// Build query with optional date filter
$whereClauses = [];
if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
    $date_from = $mysqli->real_escape_string($_GET['date_from']);
    $whereClauses[] = "`date` >= '$date_from'";
}
if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
    $date_to = $mysqli->real_escape_string($_GET['date_to']);
    $whereClauses[] = "`date` <= '$date_to'";
}

$whereSQL = '';
if (!empty($whereClauses)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
}

$query = "SELECT * FROM `$tableName` $whereSQL ORDER BY `date` DESC";
$result = $mysqli->query($query);
if (!$result) {
    die("Query failed: " . $mysqli->error);
}

// Set headers to trigger file download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $tableName . '_export.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Output column headers
$fields = $result->fetch_fields();
$headers = array_map(fn($f) => $f->name, $fields);
fputcsv($output, $headers);

// Output data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$mysqli->close();
exit;
?>
