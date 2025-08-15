<?php

require_once("C:\\xampp\\htdocs\\KARE_WEBSITE2\\api.php"); // Including your database connection file

$sql = "SELECT * FROM users"; // Replace with your table name
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = array(); // Create an empty array to store fetched data
    while ($row = $result->fetch_assoc()) {
        $data[] = $row; // Append each fetched row to the $data array
    }
    echo json_encode($data); // Encode the $data array into JSON and echo the result
} else {
    echo "No data found"; // If no rows were fetched, echo a message
}
?>
