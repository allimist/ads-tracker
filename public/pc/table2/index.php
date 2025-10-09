<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <!-- Place inside <head> -->
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='40' fill='%23ff6600' /%3E%3Ctext x='50' y='57' font-size='50' text-anchor='middle' fill='white'%3E★%3C/text%3E%3C/svg%3E" type="image/svg+xml">
  <title>PC Robotop</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #ffffff;
      color: #000000;
      transition: background 0.3s, color 0.3s;
    }

    body.dark {
      background: #121212;
      color: #f1f1f1;
    }

    /* Simple switch style */
    .switch {
      position: fixed;
      top: 10px;
      right: 10px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    td:nth-child(3) {
      /*width: 300px;      /* preferred width */
      max-width: 230px;  /* cap */
      overflow: hidden;
    }
  </style>
</head>
<body>
  <div class="switch">
    <label>
      <input type="checkbox" id="nightModeToggle">
      🌙 Night Mode
    </label>
  </div>

  
  <?php

    // Database configuration
    require_once __DIR__ . '/../../../env.php';


    // Connect to MySQL
    $mysqli = new mysqli($host, $user, $pass, $dbname);

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Optional: Set charset
    $mysqli->set_charset("utf8mb4");

    // Check if 'code' parameter is provided
    if (!isset($_GET['code'])) {
        die('No code provided.');
    }

    $inputCode = $_GET['code'];

    // Query to get report_pass from globals
    $codeQuery = "SELECT `value` FROM `globals` WHERE `name` = 'report_pass' LIMIT 1";
    $codeResult = $mysqli->query($codeQuery);

    if (!$codeResult || $codeResult->num_rows === 0) {
        die('report_pass not found in database.');
    }

    $codeRow = $codeResult->fetch_assoc();
    $reportPass = $codeRow['value'];

    // Check if the provided code matches the one from the database
    if ($inputCode !== $reportPass) {
        die('Access denied.');
    }

    // Base query
    $query = "SELECT * FROM `pc`";

    // Build dynamic WHERE conditions from GET params (excluding 'code')
    $whereClauses = [];
    foreach ($_GET as $key => $value) {
        if ($key === 'code') continue; // skip the code param

        // Sanitize column name and value
        $column = $mysqli->real_escape_string($key);
        $val = $mysqli->real_escape_string($value);

        $whereClauses[] = "`$column` = '$val'";
    }

    // Append WHERE if conditions exist
    if (!empty($whereClauses)) {
        $query .= " WHERE " . implode(" AND ", $whereClauses);
    }

    // Append ordering and limit
    $query .= " ORDER BY `id` DESC LIMIT 100";

    // Run the query
    $result = $mysqli->query($query);

    if (!$result) {
        die("Query failed: " . $mysqli->error);
    }

    // Fetch column names dynamically
    $fields = $result->fetch_fields();

    echo '<h1>'.$dbname.' Postback table</h1>';
    // Output table headers
    echo '<table border="1"><tr>';
    foreach ($fields as $field) {
        echo '<th>' . htmlspecialchars($field->name) . '</th>';
    }
    echo '</tr>';

    // Output rows
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        foreach ($fields as $field) {
            $columnName = $field->name;
            echo '<td>' . htmlspecialchars($row[$columnName]) . '</td>';
        }
        echo '</tr>';
    }

    echo '</table>';

    // Close connection
    $mysqli->close();

  ?>

  <script>
    const toggle = document.getElementById('nightModeToggle');
    const body = document.body;

    // Load saved mode from localStorage
    if (localStorage.getItem('dark-mode') === 'enabled') {
      body.classList.add('dark');
      toggle.checked = true;
    }

    toggle.addEventListener('change', () => {
      if (toggle.checked) {
        body.classList.add('dark');
        localStorage.setItem('dark-mode', 'enabled');
      } else {
        body.classList.remove('dark');
        localStorage.setItem('dark-mode', 'disabled');
      }
    });
  </script>
</body>
</html>
