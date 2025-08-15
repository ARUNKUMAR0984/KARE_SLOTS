<?php
session_start();
include("connection.php");
include("functions.php");
$user_data = check_login($con);
$msg = ""; // Initialize the message variable
$servername = "localhost";
$username = "root";
$password = "";
$database = "tamilmandram";
$msg = '';

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$totalAvailableTickets = 100;

// Function to get the available tickets count from the database
function getAvailableTicketsCount($conn) {
    $sql = "SELECT COUNT(*) FROM users"; // Change the table name if needed
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_row()) {
        return $row[0];
    } else {
        return 0; // Default to 0 if there's an error
    }
}

// Get the available tickets count
$availableTicketsCount = $totalAvailableTickets - getAvailableTicketsCount($conn);

if ($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['form_submitted'])) {
    $_POST['form_submitted'] = true; // Set a flag to indicate the form has been submitted

    // Check if the user is logged in (you can modify this part as needed)
    if (isset($_SESSION['user_id'])) {
        // User is logged in, proceed with ticket booking
        $numTickets = isset($_POST['num_tickets']) ? (int)$_POST['num_tickets'] : 0;

        if ($numTickets > 0) {
            // Initialize an array to store ticket details
            $tickets = array();

            for ($i = 0; $i < $numTickets; $i++) {
                $name = $_POST['name'][$i];
                $registerNumber = $_POST['register_number'][$i];
                $degree = $_POST['degree'][$i];
                $academicYear = $_POST['academic_year'][$i];
                $section = $_POST['section'][$i];
                $totalCost = 150; // 1 Rupee per ticket (you can adjust the cost as needed)
                $email = $_POST['email'][$i]; // Get the email value from the form
                $phoneNumbers = $_POST['phone_number']; // Get the phone number values from the form

                // Inside your for loop where you process each ticket
                $phoneNumber = $phoneNumbers[$i];

                // Calculate and store the total cost in the database
                $sql = "INSERT INTO users (name, register_number, degree, academic_year, section, email, phone_number, total_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("sssssssi", $name, $registerNumber, $degree, $academicYear, $section, $email, $phoneNumber, $totalCost);
                    if ($stmt->execute()) {
                        if (empty($registerNumber)) {
                            echo "Register number cannot be empty for Ticket " . ($i + 1);
                            continue; // Skip this iteration of the loop
                        }
                        // Data inserted successfully
                        // Store ticket details in an array
                        $tickets[] = [
                            'Name' => $name,
                            'Register Number' => $registerNumber,
                            'Degree' => $degree,
                            'Academic Year' => $academicYear,
                            'Section' => $section,
                            'Email' => $email, // Include email in the array
                            'Phone Number' => $phoneNumber, // Include phone number in the array
                            'Total Cost' => $totalCost,
                        ];

                        // Display the confirmation message for each ticket
                    } else {
                        echo "Error executing the prepared statement: " . $stmt->error;
                    }

                    $stmt->close(); // Close the prepared statement
                }
                $msg .= "<div class='alert alert-success'>Tickets are Booked Successfully </div>";
            }

            // Calculate the total cost for all tickets
            $totalCostAll = $numTickets * 150; // 1 Rupee per ticket
        } else {
            $msg = "<div class='alert alert-danger' style='color: white;'>Not enough available tickets.</div>";
        }
    } else {
        // User is not logged in, redirect or display a message
        $msg = "<div class='alert alert-danger' style='color: white;'>You must be logged in to book tickets.</div>";
        // Redirect to a login page or display a message to login
        // ...
    }
    header("Location: tamilmandramavail.php"); // Replace with your thank you page URL
    exit();
}

?>
<!-- Rest of your HTML code -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tickets</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <LINK  rel="icon" href="kare_logo-removebg-preview.png" type="image/X - icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
    
        h1 {
            text-align: center;
            margin-top: 20px;
        }
    
        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
    
        label {
            display: block;
            margin-top: 10px;
        }
    
        input[type="number"],
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 16px;
        }
    
        button[type="button"],
        button[type="submit"] {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 3px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
    
        button[type="button"]:hover,
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    
        #ticket_fields {
            margin-top: 20px;
        }
    
        .ticket {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
    
        #total_cost {
            font-weight: bold;
            color: #007BFF;
        }
    
        .alert {
            padding: 10px;
            margin-top: 10px;
            border-radius: 3px;
            font-weight: bold;
        }
    
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
    
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
    
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
    <style>
        /* Style the marquee container */
        .marquee-container {
            background-color: #333; /* Background color */
            color: #fff; /* Text color */
            padding: 10px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="marquee-container">
        <marquee class="marquee-content"><strong>HELLO, <?php echo $user_data['user_name']; ?> BOOK YOUR TICKETS FOR MIRTH</strong></marquee>
    </div>
<div class="container">
    <div class="form-box">
        <h1>Book Tickets</h1>
        <h3><p>Available Tickets: <?php echo $availableTicketsCount; ?></p></h3>
        <a class="btn btn-primary" href="index.php" id="button">Home</a>
        <a class="btn btn-primary" href="TAMIL MANDRAM EVENTS.php" id="button">BACK TO EVENTS</a><br><br>
        <?php echo $msg; ?>
        <form method="post" autocomplete="off">
            <label for="num_tickets">Number of Tickets:</label>
            <input type="number" id="num_tickets" name="num_tickets" min="1" max="100" required>
            <button type="button" id="generate_fields">BOOK TICKETS</button>
            <div id="ticket_fields"></div>
            <p>Total Cost: <span id="total_cost">0</span> Rs</p>
            
        </form>
        
    </div>
</div>
<div id="success_message" class="alert alert-success" style="display: none;">
    Booking successful for all tickets
</div>
<!-- Include your JavaScript and stylesheets here -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Generate input fields based on the number of tickets
        $("#generate_fields").click(function () {
            var numTickets = parseInt($("#num_tickets").val());

            // Ensure the number of tickets is not greater than 100
            if (numTickets > 100) {
                numTickets = 100; // Set it to the maximum allowed (100)
                $("#num_tickets").val(numTickets); // Update the input field
            }

            var ticketFields = $("#ticket_fields");
            ticketFields.empty();

            for (var i = 1; i <= numTickets; i++) {
                ticketFields.append(
                    `<div class="ticket">
                        <h3>Ticket ${i}</h3>
                        <label for="name${i}">Name:</label>
                        <input type="text" id="name${i}" name="name[]" required>
                        <label for="register_number${i}">Register Number:</label>
                        <input type="text" id="register_number${i}" name="register_number[]" required>
                        <label for="email${i}">Email:</label>
                        <input type="text" id="email${i}" name="email[]" required>
                        <label for="degree${i}">Degree:</label>
                        <input type="text" id="degree${i}" name="degree[]" required>
                        <label for="academic_year${i}">Academic Year:</label>
                        <select id="academic_year${i}" name="academic_year[]" required>
                            <option value="1st year">1st year</option>
                            <option value="2nd year">2nd year</option>
                            <option value="3rd year">3rd year</option>
                            <option value="4th year">4th year</option>
                        </select>
                        <label for="section${i}">Section:</label>
                        <input type="text" id="section${i}" name="section[]" required>
                        <label for="phone_number${i}">Phone Number:</label>
                        <input type="text" id="phone_number${i}" name="phone_number[]" required>
                        <button type="submit" class="btn btn-primary" id="submit">PAY NOW</button>
                        
                    </div>`
                );
            }
        });

        // Calculate and display the total cost
        $("#num_tickets").on("input", function () {
            var numTickets = parseInt($(this).val());
            var totalCost = numTickets * 150; // 1 Rupee per ticket
            $("#total_cost").text(totalCost);
        });

        // Inside your script, listen for form submission
        document.querySelector('form').addEventListener('submit', function (e) {
            const registerNumbers = document.querySelectorAll('input[name="register_number[]"]');
            for (const registerNumberField of registerNumbers) {
                if (registerNumberField.value.trim() === '') {
                    alert('Please fill out all register number fields.');
                    e.preventDefault(); // Prevent form submission
                    return;
                }
            }
        });
    });
    
</script>

</body>
</html>
