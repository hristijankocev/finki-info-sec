<?php
// Connect to the DB

$conn = pg_connect("host=localhost port=5432 dbname=info-sec user=postgres password=test123A!");

// Check connection
if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
    return;
}
