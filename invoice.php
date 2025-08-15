<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require("C:\\xampp\\htdocs\\KARE_WEBSITE2\\fpdf186\\fpdf.php"); // Include the FPDF library


if (isset($_POST["invoice"])) {
    // Sample booking data
    $booking = [
        'user_name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'register_number' => '12345',
        'venue' => '8TH BLOCK SEMINAR HALL',
        'date' => '2023-10-30',
        'timeslot' => '09:00 AM - 11:00 AM',
        'purpose' => 'Meeting',
        'requirements' => 'Projector, Microphone',
    ];

    // Create a new PDF document
    $pdf = new FPDF();
    $pdf->AddPage();

    $pdf->Image('kalasalingam.png', 10, 10, 175);

    // Set font
    $pdf->SetFont('Arial', 'B', 16);

    // Title
    $pdf->Cell(0, 80, 'Booking Confirmation Invoice', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Applicant Details', 0, 1);
    // User information
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 10, 'User Name: ', 1);
    $pdf->Cell(100, 10, $booking['user_name'], 1);
    $pdf->Ln();
    $pdf->Cell(30, 10, 'Email: ', 1);
    $pdf->Cell(100, 10, $booking['email'], 1);
    $pdf->Ln();
    $pdf->Cell(30, 10, 'Register No: ', 1);
    $pdf->Cell(100, 10, $booking['register_number'], 1);
    $pdf->Ln();

    // Booking details table
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Booking Details', 0, 1);

    // Create a table for booking details
    $pdf->Cell(30, 10, 'Venue', 1);
    $pdf->Cell(100, 10, $booking['venue'], 1);
    $pdf->Ln();
    $pdf->Cell(30, 10, 'Date', 1);
    $pdf->Cell(100, 10, $booking['date'], 1);
    $pdf->Ln();
    $pdf->Cell(30, 10, 'Time Slot', 1);
    $pdf->Cell(100, 10, $booking['timeslot'], 1);
    $pdf->Ln();
    $pdf->Cell(30, 10, 'Purpose', 1);
    $pdf->Cell(100, 10, $booking['purpose'], 1);
    $pdf->Ln();
    $pdf->Cell(30, 10, 'Requirements', 1);
    $pdf->Cell(100, 10, $booking['requirements'], 1);
    $pdf->Ln();

    // Add the digital signature
    $pdf->Cell(290, 65, 'Digital Signature ', 0, 1, 'C');
    $pdf->Image('green tick.png', 150, 200, 15);

    // Output the PDF to the browser
    $pdf->Output('invoice.pdf', 'I');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Invoice</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <!-- Add the new CSS code here -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }

        .user-details {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
        }

        .booking-details {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-table th, .invoice-table td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        .invoice-table th {
            background-color: #333;
            color: #fff;
        }

        .signature {
            text-align: center;
            font-weight: bold;
        }

        .signature img {
            width: 60px;
            height: 60px;
            margin-top: 10px;
        }
    </style>
    <!-- Add the new CSS code here -->
</head>
<body>
    <form method="post" action="">
        <button class="btn btn-primary" name="invoice" id="invoice">Generate Invoice</button>
    </form>
</body>
</html>

