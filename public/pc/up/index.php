<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// DB config
require_once __DIR__ . '/../../../env.php';


// Connect
$mysqli = new mysqli($host, $user, $pass, $dbname);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");

if (!isset($_GET['code'])) {
    die('No code provided.');
}

$inputCode = $_GET['code'];

// Query to get report_pass from globals
$codeQuery = "SELECT `value` FROM `globals` WHERE `name` = 'upload_pass' LIMIT 1";
$codeResult = $mysqli->query($codeQuery);

if (!$codeResult || $codeResult->num_rows === 0) {
    die('upload_pass not found in database.');
}

$codeRow = $codeResult->fetch_assoc();
$reportPass = $codeRow['value'];

//echo 'reportPass:' . $reportPass . '<br>' ; 
//echo 'inputCode:' . $inputCode; die;

// Check if the provided code matches the one from the database
if ($inputCode !== $reportPass) {
    die('Access denied.');
}

// Set headers to force CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="google_ads_conversions.csv"');







// Open output stream
$output = fopen('php://output', 'w');

// Write CSV header compatible with Google Ads
fputcsv($output, [
    'Google Click ID',
    'Conversion Name',
    'Conversion Time',
    'Conversion Value',
    'Currency Code'
]);

// Query latest 100 records
//$query = "SELECT gclid, date, revenue FROM `pc` WHERE gclid IS NOT NULL AND gclid != '' ORDER BY id DESC LIMIT 100";

$query = "
    SELECT gclid, date, revenue, brand , event_name
    FROM `pc` 
    WHERE 
        record_source = 'postback'
        AND gclid IS NOT NULL 
        AND gclid != '' 
        AND `date` >= NOW() - INTERVAL 48 HOUR 
    ORDER BY id DESC
";

$result = $mysqli->query($query);

// Default conversion name and currency
$conversionName = 'offline_conversions'; // or 'Purchase', 'Signup', etc.
$currencyCode = 'USD';

while ($row = $result->fetch_assoc()) {
    // Format date as required: YYYY-MM-DD HH:MM:SS+0000
    $datetime = date('Y-m-d H:i:s+0000', strtotime($row['date']));

    if(!empty($row['event_name'])){
        fputcsv($output, [
            $row['gclid'],
            $row['event_name'],
            $datetime,
            $row['revenue'],
            $currencyCode
        ]);
    } else {
        fputcsv($output, [
            $row['gclid'],
            ($row['brand']=='Hone')? 'offline_leads' : $conversionName,
            $datetime,
            $row['revenue'],
            $currencyCode
        ]);
    }

    
}

fclose($output);
$mysqli->close();
exit;