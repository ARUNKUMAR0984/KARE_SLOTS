<?php

$dbhost = "localhost";

$dbuser = "root";

$dbpass = "";

$dbname = "pdf_example";

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
    die("failed to connect");
}
