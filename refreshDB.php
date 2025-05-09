<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$sqlFile = __DIR__ . '/Extras/table-data.txt'; // Full path to your SQL file

try {
  // Connect to MySQL server
  $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Read SQL file
  if (!file_exists($sqlFile)) {
    die("SQL file not found: $sqlFile");
  }

  $sqlContent = file_get_contents($sqlFile);

  // Split into individual queries
  $queries = [];
  $currentQuery = '';

  foreach (explode("\n", $sqlContent) as $line) {
    $trimmedLine = trim($line);

    // Skip empty lines and comments
    if (empty($trimmedLine)) continue;
    if (strpos($trimmedLine, '--') === 0) continue;

    $currentQuery .= ' ' . $trimmedLine;

    // If line ends with semicolon, it's a complete query
    if (substr($trimmedLine, -1) === ';') {
      $queries[] = $currentQuery;
      $currentQuery = '';
    }
  }

  // Execute all queries
  foreach ($queries as $query) {
    try {
      $pdo->exec($query);
    } catch (PDOException $e) {
      // Log error but continue with next query
      error_log("Error executing query: " . $e->getMessage());
      error_log("Failed query: " . $query);
    }
  }

  echo "Database refreshed successfully! Redirecting...";
  header("Refresh: 3; url=index.php"); // Redirect after 3 seconds
  exit();
} catch (PDOException $e) {
  die("Database error: " . $e->getMessage());
}
