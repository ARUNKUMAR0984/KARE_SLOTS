<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);

$mysqli = new mysqli('localhost', 'root', '', 'slot2_pa');

// Check the database connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Define an array of faculty names and corresponding venues (you can add more faculty names and venues)
$faculties = [
    "Ms. V.S.VETRI SELVI" => "10202",
    "MR. R.MARISELVAN" => "10201",
    "MR. C.SIVAMURUGAN" => "7312",
    "MR. V.MANIKANDAN" => "7311",
    "Mrs. LOYOLA JASMINE" => "7310",
    "DR. K.VIVEKRABINSON" => "7309",
];

// Function to check if a student has already booked a faculty for a specific course
function hasStudentBookedFaculty($mysqli, $studentId, $courseName) {
    $query = "SELECT * FROM users WHERE Register_number = ? AND coursename = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $studentId, $courseName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Initialize an array to store available seats
$availableSeats = [];

foreach ($faculties as $facultyName => $venue) {
    // Fetch available seats for each faculty
    $selectSeatsQuery = "SELECT Available_Seats FROM faculties WHERE Faculty_Name = ?";
    $selectSeatsStmt = $mysqli->prepare($selectSeatsQuery);
    $selectSeatsStmt->bind_param('s', $facultyName);

    if (!$selectSeatsStmt) {
        die('Error preparing the query: ' . $mysqli->error);
    }

    if (!$selectSeatsStmt->execute()) {
        die('Error executing the query: ' . $selectSeatsStmt->error);
    }

    $selectSeatsStmt->bind_result($seats);

    if (!$selectSeatsStmt->fetch()) {
        // Handle the case where no result was fetched
        echo "Error fetching results for faculty: $facultyName";
        // You can set $seats to a default value, e.g., 0
        $seats = 60;
    }

    $selectSeatsStmt->close();
    $availableSeats[$facultyName] = $seats;
}

if (isset($_POST['submit'])) {
    $name = $user_data['user_name'];
    $email = $user_data['email'];
    $Facultyname = $_POST["faculty_name"];
    $CourseName = $_POST["course_name"];
    $CourseCode = $_POST["course_code"];
    $slot = $_POST["slot"];
    $phonenumber = isset($_POST['phonenumber']) ? $_POST['phonenumber'] : '';
    $venue = $_POST["venue"];
    $registerNumber = $user_data['register_number'];

    if (hasStudentBookedFaculty($mysqli, $registerNumber, $CourseName)) {
        echo "You have already booked a faculty for this course.";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO users (Student_Name, Facultyname, coursename, course_code, email, venue, phonenumber, Register_number,slot) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
        $stmt->bind_param('sssssssss', $name, $Facultyname, $CourseName, $CourseCode, $email, $venue, $phonenumber, $registerNumber,$slot);

        if ($stmt->execute()) {
            $updateSeatsQuery = "UPDATE faculties SET Available_Seats = Available_Seats - 1 WHERE Faculty_Name = ?";
            $updateSeatsStmt = $mysqli->prepare($updateSeatsQuery);
            $updateSeatsStmt->bind_param('s', $Facultyname);
            $updateSeatsStmt->execute();

            header("Location: Slot1-pa.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <title>Available venues</title>
    <LINK  rel="icon" href="kare_logo-removebg-preview.png" type="image/x-icon">
    <style>
        /* Style the marquee container */
        .marquee-container {
            background-color: #333;
            color: #fff;
            padding: 10px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }
    </style>
    <style>
        /* Define styles for the list */
        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 10px;
            background-color: #f0f0f0;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        /* Define styles for the hover effect */
        li:hover {
            background-color: #3498db;
            color: #fff;
        }
        .booked-slots {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .booked-slots th,
        .booked-slots td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .booked-slots th {
            background-color: #333;
            color: #fff;
        }

        .booked-slots tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="marquee-container">
        <marquee class="marquee-content"><strong>HELLO, <?php echo $user_data['user_name']; ?> CHOOSE YOUR PREDICTIVE ANALYSIS FACULTY</strong></marquee>
    </div>
    <br><br>

    <CENTER><a class="btn btn-primary" href="index.php" id="button">Home</a></CENTER>
    <center><h3><strong>AVAILABLE FACULTIES</strong></h3></center><br><br>

    <table class="booked-slots">
        <thead>
            <tr>
                <th>S.No</th>
                <th>COURSE NAME</th>
                <th>COURSE CODE</th>
                <th>FACULTY NAME</th>
                <th>VENUE</th>
                <th>Available Seats</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $serialNumber = 1;
        foreach ($faculties as $facultyName => $venue) {
        ?>
            <tr>
                <td><strong><?php echo $serialNumber; ?></strong></td>
                <td><strong>PREDICTIVE ANALYSIS</strong></td>
                <td><strong>213 CSE 2301</strong></td>
                <td><strong><?php echo $facultyName; ?></strong></td>
                <td><strong><?php echo $venue; ?></strong></td>
                <td><strong>
                    <?php echo $availableSeats[$facultyName]; ?></strong>
                </td>
                <td>
                    <button type="button" class="btn btn-primary book" data-toggle="modal" data-target="#myModal"
                        data-faculty-name="<?php echo $facultyName; ?>"
                        data-course-name="PREDICTIVE ANALYSIS"
                        data-course-code="213 CSE 2301"
                        data-venue="<?php echo $venue; ?>">BOOK
                    </button>
                </td>
            </tr>
        <?php
            $serialNumber++;
        }
        ?>
        </tbody>
    </table>

    <!-- Hidden modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">BOOKING</h4>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div id="errorAlert" class="alert alert-danger" style="display: none;"></div>
                        <div class="form-group">
                            <label for="StudentName">Student Name:</label>
                            <input required type="text" readonly name="name" id="name" class="form-control" value="<?php echo $user_data['user_name']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="StudentRegister">Register Number:</label>
                            <input required type="text" readonly name="registernumber" id="registernumber" class="form-control" value="<?php echo $user_data['register_number']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="">Email</label>
                            <input required type="email" readonly name="email" id="email" class="form-control" value="<?php echo $user_data['email']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="StudentRegister">SLOT:</label>
                            <input required type="text" readonly name="slot" id="slot" class="form-control" value="Slot 2">
                        </div>
                        <div class="form-group">
                            <label for="facultyName">Faculty Name:</label>
                            <input required type="text" readonly name="faculty_name" class="form-control" id="facultyNameDisplay">
                        </div>
                        <div class="form-group">
                            <label for="courseName">Course Name:</label>
                            <input required type="text" readonly name="course_name" class="form-control" id="courseNameDisplay">
                        </div>
                        <div class "form-group">
                            <label for="courseCode">Course Code:</label>
                            <input required type="text" readonly name="course_code" class="form-control" id="courseCodeDisplay">
                        </div>
                        <div class="form-group">
                            <label for="phonenumber">Phone Number:</label>
                            <input type="phonenumber" id="phonenumber" placeholder="Enter your phone number" class="form-control" readonly name="phonenumber" value="<?php echo $user_data['phonenumber']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="venue">Venue:</label>
                            <input required type="text" readonly name="venue" class="form-control"  id="venueDisplay">
                        </div>
                        <button class="btn btn-primary" type="submit" name="submit" id="bookButton">Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add your JavaScript code to update the modal -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".book").click(function () {
                var facultyName = $(this).data('faculty-name');
                var courseName = $(this).data('course-name');
                var courseCode = $(this).data('course-code');
                var venue = $(this).data('venue');

                $("#facultyNameDisplay").val(facultyName);
                $("#courseNameDisplay").val(courseName);
                $("#courseCodeDisplay").val(courseCode);
                $("#venueDisplay").val(venue);
            });

            $("#bookButton").click(function () {
                // Handle the booking logic here, e.g., sending a request to the server
            });
        });
    </script>
</body>
</html>