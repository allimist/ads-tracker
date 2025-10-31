<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='40' fill='%23ff6600' /%3E%3Ctext x='50' y='57' font-size='50' text-anchor='middle' fill='white'%3E★%3C/text%3E%3C/svg%3E" type="image/svg+xml">
  <title>LP CTR Report - xxxx</title>
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
    .switch {
      position: fixed;
      top: 10px;
      right: 10px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    table {
      border-collapse: collapse;
      width: 80%;
      margin: 80px auto;
      font-size: 16px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px 12px;
      text-align: center;
    }
    th {
      background-color: #f2f2f2;
    }
    body.dark th {
      background-color: #222;
    }
    h1 {
      text-align: center;
      margin-top: 60px;
    }
  </style>
</head>
<body>
  <div class="switch">
    <label>
      <input type="checkbox" id="nightModeToggle"> 🌙 Night Mode
    </label>
  </div>

  <?php
    // --- Database configuration ---
    require_once __DIR__ . '/../../env.php';


    // Connect to MySQL
    $mysqli = new mysqli($host, $user, $pass, $dbname);
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    $mysqli->set_charset("utf8mb4");

    // --- Security check ---
    if (!isset($_GET['code'])) {
        die('No code provided.');
    }

    $inputCode = $_GET['code'];
    $codeQuery = "SELECT `value` FROM `globals` WHERE `name` = 'report_pass' LIMIT 1";
    $codeResult = $mysqli->query($codeQuery);

    if (!$codeResult || $codeResult->num_rows === 0) {
        die('report_pass not found in database.');
    }

    $codeRow = $codeResult->fetch_assoc();
    $reportPass = $codeRow['value'];

    if ($inputCode !== $reportPass) {
        die('Access denied.');
    }

    // --- Query: LP CTR for 31 ---
    $query = "
    SELECT 
        DATE(s.date) AS Date,
        COUNT(DISTINCT s.id) AS Sessions,
        COUNT(DISTINCT p.id) AS Clicks,
        ROUND((COUNT(DISTINCT p.id) / COUNT(DISTINCT s.id)) * 100, 2) AS CTR
    FROM se s
    LEFT JOIN pc p 
        ON s.gclid = p.gclid 
        AND DATE(s.date) = DATE(p.date)
        AND p.event_type = 'brandclick'
    WHERE s.gclid IS NOT NULL
      AND s.gclid != ''
      AND s.date >= CURDATE() - INTERVAL 31 DAY
    GROUP BY DATE(s.date)
    ORDER BY DATE(s.date) DESC
";




    $result = $mysqli->query($query);
    if (!$result) {
        die("Query failed: " . $mysqli->error);
    }

    echo "<h1>Landing Page CTR Report (31 days)</h1>";
    echo "<table>";
    echo "<tr><th>Date</th><th>Sessions</th><th>Clicks</th><th>LP CTR (%)</th></tr>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Sessions']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Clicks']) . "</td>";
            echo "<td>" . htmlspecialchars($row['CTR']) . "%</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No data for this month</td></tr>";
    }

    echo "</table>";

    $mysqli->close();
  ?>

  <script>
    const toggle = document.getElementById('nightModeToggle');
    const body = document.body;

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
