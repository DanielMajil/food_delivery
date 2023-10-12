<?php
// Database configuration
$servername = "localhost";
$username = "your_db_user";
$password = "your_db_password";
$dbname = "query_db"; // Change to your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to select all queries from the table
$sql = "SELECT * FROM queries";

$result = $conn->query($sql);

// Export queries to an SQL file
if ($result->num_rows > 0) {
    $export_filename = "exported_queries.sql"; // Specify the filename
    $export_file = fopen($export_filename, "w");

    while ($row = $result->fetch_assoc()) {
        $query_text = $row["query_text"];
        fwrite($export_file, $query_text . "\n");
    }

    fclose($export_file);
    echo "Queries exported to $export_filename.";
} else {
    echo "No queries found.";
}

// Close the database connection
$conn->close();
?>
