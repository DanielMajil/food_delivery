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

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query_text = $_POST["query_text"];

    // SQL statement to insert the query into the table
    $sql = "INSERT INTO queries (query_text) VALUES ('$query_text')";

    if ($conn->query($sql) === TRUE) {
        echo "Query inserted successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
