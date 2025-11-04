<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='40' fill='%23ff6600' /%3E%3Ctext x='50' y='57' font-size='50' text-anchor='middle' fill='white'%3E★%3C/text%3E%3C/svg%3E" type="image/svg+xml">
  <title>ICS Report - Robotop</title>
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
      margin: 80px auto 30px;
      font-size: 16px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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
    #chartContainer {
      width: 80%;
      margin: 20px auto 60px;
      height: 420px;
    }
    body.dark table {
      box-shadow: 0 4px 16px rgba(255, 255, 255, 0.08);
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

    // --- Query: LP CTR for current month ---
    $query = "
    SELECT 
    DATE(date) AS Date,
    COUNT(*) AS `Brand Clicks`,
    SUM(CASE WHEN brand = 'imagen' THEN 1 ELSE 0 END) AS `Imagen Brand Clicks`,
    ROUND(
        SUM(CASE WHEN brand = 'imagen' THEN 1 ELSE 0 END) / COUNT(*) * 100,
        2
    ) AS `Imagen Click Share`
FROM pc
WHERE event_type = 'brandclick'
  AND gclid IS NOT NULL
  AND gclid != ''
  AND date >= CURDATE() - INTERVAL 31 DAY
GROUP BY DATE(date)
ORDER BY DATE(date) DESC;


";



    $result = $mysqli->query($query);
    if (!$result) {
        die("Query failed: " . $mysqli->error);
    }

    $rows = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = [
                'date' => $row['Date'],
                'brandClicks' => (int) $row['Brand Clicks'],
                'imagenBrandClicks' => (int) $row['Imagen Brand Clicks'],
                'imagenClickShare' => (float) $row['Imagen Click Share']
            ];
        }
    }

    echo "<h1>ICS Report (31 days)</h1>";
    echo "<table>";
    echo "<tr><th>Date</th><th>Sessions</th><th>Clicks</th><th>LP CTR (%)</th></tr>";

    if (!empty($rows)) {
        foreach ($rows as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['brandClicks']) . "</td>";
            echo "<td>" . htmlspecialchars($row['imagenBrandClicks']) . "</td>";
            echo "<td>" . htmlspecialchars(number_format($row['imagenClickShare'], 2)) . "%</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No data for this month</td></tr>";
    }

    echo "</table>";

    echo "<div id='chartContainer'>";
    echo "<canvas id='clickShareChart'></canvas>";
    echo "</div>";

    $mysqli->close();
  ?>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const toggle = document.getElementById('nightModeToggle');
    const body = document.body;
    const chartContainer = document.getElementById('chartContainer');
    const chartData = <?php echo json_encode($rows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    if (localStorage.getItem('dark-mode') === 'enabled') {
      body.classList.add('dark');
      toggle.checked = true;
    }

    let chartInstance = null;

    const getOptions = (darkMode) => ({
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false
        },
        plugins: {
          legend: {
            labels: {
              color: darkMode ? '#f1f1f1' : '#333'
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                if (context.dataset.yAxisID === 'y1') {
                  return `${context.dataset.label}: ${context.parsed.y.toFixed(2)}%`;
                }
                return `${context.dataset.label}: ${context.parsed.y}`;
              }
            }
          }
        },
        scales: {
          x: {
            ticks: {
              color: darkMode ? '#f1f1f1' : '#333'
            },
            grid: {
              color: darkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
            }
          },
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Sessions / Clicks',
              color: darkMode ? '#f1f1f1' : '#333'
            },
            ticks: {
              color: darkMode ? '#f1f1f1' : '#333'
            },
            grid: {
              color: darkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
            }
          },
          y1: {
            beginAtZero: true,
            position: 'right',
            title: {
              display: true,
              text: 'LP CTR (%)',
              color: darkMode ? '#f1f1f1' : '#333'
            },
            ticks: {
              callback: value => `${value}%`,
              color: darkMode ? '#f1f1f1' : '#333'
            },
            grid: {
              drawOnChartArea: false
            }
          }
        }
      });

    const updateChartTheme = () => {
      if (!chartInstance) return;
      const darkMode = body.classList.contains('dark');
      chartInstance.options = getOptions(darkMode);
      chartInstance.update();
    };

    const createChart = () => {
      if (!Array.isArray(chartData) || chartData.length === 0) {
        if (chartContainer) {
          chartContainer.style.display = 'none';
        }
        return;
      }

      if (chartContainer) {
        chartContainer.style.display = '';
      }

      const sortedData = [...chartData].reverse();
      const labels = sortedData.map(row => row.date);
      const brandClicks = sortedData.map(row => row.brandClicks);
      const imagenBrandClicks = sortedData.map(row => row.imagenBrandClicks);
      const imagenClickShare = sortedData.map(row => row.imagenClickShare);

      const ctx = document.getElementById('clickShareChart').getContext('2d');
      const isDark = body.classList.contains('dark');

      const datasets = [
        {
          label: 'Sessions',
          data: brandClicks,
          borderColor: '#4e79a7',
          backgroundColor: 'rgba(78, 121, 167, 0.2)',
          tension: 0.3,
          yAxisID: 'y'
        },
        {
          label: 'Clicks',
          data: imagenBrandClicks,
          borderColor: '#f28e2b',
          backgroundColor: 'rgba(242, 142, 43, 0.2)',
          tension: 0.3,
          yAxisID: 'y'
        },
        {
          label: 'LP CTR (%)',
          data: imagenClickShare,
          borderColor: '#e15759',
          backgroundColor: 'rgba(225, 87, 89, 0.2)',
          tension: 0.3,
          yAxisID: 'y1'
        }
      ];

      chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets
        },
        options: getOptions(isDark)
      });

      updateChartTheme();
    };

    toggle.addEventListener('change', () => {
      if (toggle.checked) {
        body.classList.add('dark');
        localStorage.setItem('dark-mode', 'enabled');
      } else {
        body.classList.remove('dark');
        localStorage.setItem('dark-mode', 'disabled');
      }
      updateChartTheme();
    });

    if (toggle.checked) {
      body.classList.add('dark');
    }

    createChart();
  </script>
</body>
</html>
