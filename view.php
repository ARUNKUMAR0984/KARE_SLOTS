<?php
$mysqli = new mysqli('localhost', 'root', '', 'pdf_example');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $mysqli->prepare("SELECT file_name, file_data FROM pdf_files WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($filename, $pdfData);
                $stmt->fetch();

                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . $filename . '"');

                echo $pdfData;

                $stmt->close(); // Close the prepared statement
            } else {
                echo "PDF not found.";
            }
        } else {
            echo "Error fetching PDF: " . $stmt->error;
        }
    } else {
        echo "Error preparing statement: " . $mysqli->error;
    }
} else {
    echo "Invalid request.";
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer</title>
</head>
<body>
    <a href="view_pdf.php?id=1" target="_blank">Download PDF 1</a>
    <a href="view_pdf.php?id=2" target="_blank">Download PDF 2</a>
    <!-- Add more links for other PDFs -->
</body>
</html>

