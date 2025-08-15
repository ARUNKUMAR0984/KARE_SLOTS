<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
include("connection.php");
include("functions.php");

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_data = check_login($con);
} else {
    // If the user is not logged in, redirect to the login page
    header("Location: login2.php");
    exit(); // Make sure to stop script execution after the redirect
}

// Check if the logout button is clicked
if (isset($_POST['logout'])) {
    // Unset the session variable
    unset($_SESSION['user_id']);
    // Redirect to the login page after logout
    header("Location: login2.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KARE SLOTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <LINK  rel="icon" href="kare_logo-removebg-preview.png" type="image/X - icon">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
    <style>
      .overflow-container {
    overflow-y: scroll;
}</style>
    <style>
        *{
            
font-family: 'Poppins', sans-serif;
        }
        .h-font{
            font-family: 'Merienda', cursive;
        }
        </style>
    <style type="text/css">
      * {
         box-sizing: border-box;
      }
      html, body {
         margin: 0;
         padding: 0;
      }
      body {
         display: flex;
         min-height: 100vh;
         flex-direction: column;
      }
      main {
         flex: 1 0 auto;
      }
      footer {
         color: #fff;
         background: #333;
         position: relative; /* required to position the copyright at the bottom */
         font-size: 80%;
      }
      .footer-copyright {
         width: 100%;
         height: 40px;
         background: #111;
         /* positions the copyright at the bottom of the footer */
         padding: 10px;
         position: absolute;
         bottom: 0px;
         left: 0px;
      }
      .footer-body {
         margin-bottom: 40px;
         padding: 30px;
      }
      .footer-body > div:first-child {
         font-size: 150%;
      }
      .footer-body ul {
         list-style-type: none;
         margin: 0px;
         padding: 0px;
         text-align: center;
      }
      .footer-body li > a {
         color: white;
         text-decoration: none;
         margin-bottom: 7px;
      }
   </style>
   <style>
        /* Container styles */
        .image-container {
            position: relative;
            width: 500px;
            cursor: pointer;
            overflow: hidden;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        /* Image styles */
        .image-container img {
            width: 100%;
            transition: transform 0.3s;
        }

        /* Dropdown container styles */
        .dropdown-container {
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            border: 1px solid #ccc;
            border-top: none;
            padding: 10px;
            width: 100%;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s, transform 0.3s;
            border-radius: 0 0 8px 8px;
        }

        /* Hover effect */
        .image-container:hover img {
            transform: scale(1.1);
        }

        .image-container:hover .dropdown-container {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
      <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600&display=swap');

*{
    font-family: 'Poppins', sans-serif;
    margin: 0; padding: 0;
    box-sizing: border-box;
    outline: none; border: none;
    text-decoration: none;
    text-transform: capitalize;
    transition: .2s linear;
}



.container .heading{
    text-align: center;
    padding-bottom: 15px;
    color: black;
    text-shadow: 0 5px 10px rgba(0,0,0,.2);
    font-size: 50px;
}

.container .box-container{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap:15px;

}
.contaier.box-container .box{
    box-shadow: 0 5px 10px rgba(0,0,0,.2);
    border-radius:5px;
    background:#fff;
    text-align:center;
    padding:30px 20px;

}

.contaier.box-container .box img{

    height: 10px;
   
}
.contaier.box-container .box h3{
    color: #444;
    font-size: 22px;
    padding:10px 0
}
.container .box-container .box:hover{
    box-shadow: 0 10px 15px rgba(0,0,0,.3);
    transform: scale(1.05);
}

@media(max-width:768px){
    .contaier{
        padding: 20px;
    }
}

.container.box-container.section{
    padding: 20px;
    width:1280 px;
    margin: 70px auto;
    
}
.container.box-container.section ul{
    display: flex;
    margin-bottom: 10px;
}
section ul li{
    list-style: none;
    background: #eee;
    padding: 8px 20px;
    margin:5px;
    letter-spacing: 1px;
    cursor: pointer;
}
section ul li.active
{
    background: #03a9f4;
    color: #fff;
}

    </style>
    <style>
        /* Add your CSS styling here */
        .about-container {
            padding: 20px;
            background-color: #f2f2f2;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: 20px;
        }

        h1 {
            font-size: 24px;
            color: #333;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            color: #555;
        }
    </style>
   
        
</head>
<body>
<marquee>HELLO,<?php echo $user_data['user_name'];?> WELCOME TO KALASALINGAM UNIVERSITY</marquee>

<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">KARE SLOTS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="#About">ABOUT US</a>
        </li>
        <li class="nav-item">
        <a class="nav-link me-2" href="#venues">VENUES</a>
        </li>
        <li class="nav-item">
        <a class="nav-link me-2" href="#clubs">CLUBS & EVENTS</a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link me-2" href="bookedslots.php">MY BOOKINGS</a>
        </li>
      </ul>
      <div class="d-flex">
      <i class="fa-solid fa-user"></i>
      <form method="post" style="display: inline;">
    <input type="submit" name="logout" value="Logout" class="btn btn-primary">
</form>

        <span style="margin-right: 10px;"></span>
        <a class="btn btn-primary" href="signup.php" role="button">Signup</a>

    </div>
    </div>
  </div>
</nav>


<div class="container-fluid">
<div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide" ><img src="kalasalingam.png" class="w-100 d-block" style="height: 300px;"></div>
      <div class="swiper-slide"><img src="medium_rsz_kalasalingam_university.jpg" class="w-100 d-block" style="height: 300px;"></div>
      <div class="swiper-slide"><img src="KLU-Overview.jpg" class="w-100 d-block" style="height: 300px;"></div>
      <div class="swiper-slide"><img src="Kalasalingam-University-Virudhunagar2.jpg" class="w-100 d-block" style="height: 300px;"></div>
      <div class="swiper-slide"><img src="Kalasalingam university.jpg" class="w-100 d-block" style="height: 300px;"></div>
    </div>

    <div class="swiper-pagination"></div>
  </div>
</div>
  <br><br><br><br>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
    var swiper = new Swiper(".mySwiper", {
      spaceBetween: 30,
      centeredSlides: true,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });
  </script>


                    

                    

<section id="venues">
<div class="container">
<div class="container-fluid">
<h1 class="heading me-5 fw-bold fs-3 h-font"> </center>VENUES</center></h1><br><br>
<div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide" ><a href="calender.php">
                        <img src="INDOOR STADIUM.jpeg" alt="" width="425px" height="160px" class="w-100 d-block" style="height: 300px;">
                    </a><center><br>
                        <h3 class="me-5 fw-bold fs-3 h-font">INDOOR STADIUM</h3></center> </div>
      <div class="swiper-slide"><a href="seminarhallvenues.php">
                        <img src="seminar hall.jpeg" alt="" width="425px" height="160px" class="w-100 d-block" style="height: 300px;">
                    </a>
                    <br>
                    <center><h3 class="me-5 fw-bold fs-3 h-font">SEMINAR HALL</h3></center></div>
      <div class="swiper-slide"><a href="auditoriumcalender.php">
                        <img src="AUDITORIUM.jpg" alt="" width="425px" height="160px"class="w-100 d-block" style="height: 300px;">
                    </a>
                    <br><br>
                    <center><h3 class="me-5 fw-bold fs-3 h-font">AUDITORIUM</h3></center> </div>
      
    </div>
<br><br>
    <div class="swiper-pagination"></div>
  </div>
</div>
</div>
    <br><br><br>
    <script>
    var swiper = new Swiper(".mySwiper", {
      spaceBetween: 30,
      centeredSlides: true,
      autoplay: {
        delay: 3500,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });
  </script>
</section>

<section id="clubs">
<div class="container">
<div class="container-fluid">
<h1 class="heading me-5 fw-bold fs-3 h-font"> </center>CLUBS & EVENTS </center></h1><br><br>
<div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide" ><a href="TAMIL MANDRAM EVENTS.php">
                        <img src="Tamil mandram.jpeg" alt="" width="425px" height="160px" class="w-100 d-block" style="height: 300px;">
                    </a><center><br>
                        <h3 class="me-5 fw-bold fs-3 h-font">தமிழ் மன்றம்</h3></center> 
      
    </div>
    
    
<br><br>
    <div class="swiper-pagination"></div>
  </div>
</div>
</div>
    <br><br><br>
    <script>
    var swiper = new Swiper(".mySwiper", {
      spaceBetween: 30,
      centeredSlides: true,
      autoplay: {
        delay: 3500,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });
  </script>
</section>
<section id="About">
    <div class="about-container">
        <h1>About Kalasalingam Academy of Research and Education (KARE)</h1>
        <p>Kalasalingam Academy of Research and Education (KARE) (Deemed to be University), formerly Arulmigu Kalasalingam College of Engineering, was established in 1984 by Kalvivallal Thiru T. Kalasalingam under the trust Kalasalingam and Anandam Ammal Charities. Thiru T. Kalasalingam was a freedom fighter and philanthropist. KARE is located at the pristine foothills of scenic Western Ghats of southern Tamil Nadu.</p>
        <p>The college obtained the Deemed to be University status in 2006 and has been serving the society for thirty-seven long years, catering to the needs of students from all walks of society. KARE offers UG programmes, PG programmes, and Ph.D. programmes in various disciplines of Engineering, Science, Technology, and Humanities. It is the first institution in India to introduce a special B.Tech programme in engineering for differently-abled (speech and hearing impaired) students.</p>
        <p>The institution has been re-accredited by NAAC with ‘A’ grade with a CGPA of 3.11. Six UG programmes have been accredited by NBA under Tier-1. KARE continues to do indefatigable work in getting projects and research centers. It has received DST funding to establish the National Center for Advance Research in Discrete Mathematics. KARE has state-of-the-art IRC with splendid high-end instruments for advanced research in material sciences and life sciences.</p>
        <p>Multistoried separate hostels with plenty of facilities provide accommodation to thousands of students. The institution has spent an exorbitant sum to create a world-class swimming pool and indoor auditorium for sports. Furthermore, KARE gives utmost importance to Intra-mural and Extra-mural activities for the holistic development of the students.</p>
    </div>
</section>
<div class="d-flex">
        <a class="btn" href="#" role="button">Back to Top</a>
        

    </div>

<footer>
      <div class="footer-body">
         <div> Kalasalingam University </div>
         <div>
            DEEMED TO BE UNIVERSITY UNDER SECTION 3 OF UGC ACT 1956 ANAND NAGAR, KRISHNANKOIL-626126, TAMIL NADU,INDIA. 
            <ul>
               <li><a href="#"> About </a></li>
               <li><a href="#"> Contact </a></li>
               <li><a href="#"> Terms & Conditions </a></li>
               <li><a href="#"> Privacy Policy </a></li>
            </ul>
         </div>
      </div>
      <div class="footer-copyright">
         © Copyright goes here. 
      </div>
   </footer>

   <script>
    // Get references to the link and dropdown
const venuesLink = document.getElementById("venues-link");
const venuesDropdown = document.getElementById("venues-dropdown");

// Toggle the dropdown when the link is clicked
venuesLink.addEventListener("click", function (event) {
    event.preventDefault(); // Prevent the link from navigating

    // Toggle the dropdown's visibility
    if (venuesDropdown.style.display === "block") {
        venuesDropdown.style.display = "none";
    } else {
        venuesDropdown.style.display = "block";
    }
});


    </script>
</body>
</html>