<?php
$mysqli = new mysqli('localhost', 'root', '', 'pdf_example');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_POST["submit"])) {
    // Check if file was uploaded without errors
    if (isset($_FILES["pdf"]) && $_FILES["pdf"]["error"] == 0) {
        $pdf = file_get_contents($_FILES["pdf"]["tmp_name"]);
        $pdfName = $_FILES["pdf"]["name"];

        // Prepare and execute the SQL query to insert the PDF into the database
        $stmt = $mysqli->prepare("INSERT INTO pdf_files (file_name, file_data) VALUES (?, ?)");

        if (!$stmt) {
            die("Error: " . $mysqli->error);
        }

        // Bind parameters using bind_param
        $stmt->bind_param('ss', $pdfName, $pdf); // 'ss' indicates two string parameters

        if ($stmt->execute()) {
            echo "PDF uploaded and inserted into the database.";
        } else {
            echo "Error uploading PDF: " . $stmt->error;
        }

        $stmt->close(); // Close the prepared statement
    } else {
        echo "Error uploading PDF.";
    }
}

$mysqli->close(); // Close the database connection when done
?>

<!DOCTYPE html>
<html>
<head>
    <title>PDF Upload</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        Select PDF to upload:
        <input type="file" name="pdf" id="pdf">
        <input type="submit" value="Upload PDF" name="submit">
    </form>
</body>
</html>