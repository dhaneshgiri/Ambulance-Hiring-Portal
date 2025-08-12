<?php
include('includes/dbconnection.php');

echo "<h2>Testing Driver Information Connection</h2>";

// Test 1: Check ambulance table
echo "<h3>Ambulance Table Data:</h3>";
$ambulance_query = mysqli_query($con, "SELECT AmbRegNum, DriverName, DriverContactNumber FROM tblambulance");
while ($amb = mysqli_fetch_array($ambulance_query)) {
    echo "AmbRegNum: " . $amb['AmbRegNum'] . " | Driver: " . $amb['DriverName'] . " | Contact: " . $amb['DriverContactNumber'] . "<br>";
}

// Test 2: Check ambulance hiring table
echo "<h3>Ambulance Hiring Table Data:</h3>";
$hiring_query = mysqli_query($con, "SELECT BookingNumber, AmbulanceRegNo, Status FROM tblambulancehiring WHERE AmbulanceRegNo IS NOT NULL");
while ($hire = mysqli_fetch_array($hiring_query)) {
    echo "Booking: " . $hire['BookingNumber'] . " | Ambulance: " . $hire['AmbulanceRegNo'] . " | Status: " . $hire['Status'] . "<br>";
}

// Test 3: Test the JOIN query
echo "<h3>JOIN Query Test:</h3>";
$join_query = mysqli_query($con, "SELECT 
    tblambulancehiring.BookingNumber,
    tblambulancehiring.AmbulanceRegNo,
    tblambulancehiring.Status,
    tblambulance.DriverName,
    tblambulance.DriverContactNumber
    FROM tblambulancehiring 
    LEFT JOIN tblambulance ON tblambulance.AmbRegNum = tblambulancehiring.AmbulanceRegNo  
    WHERE tblambulancehiring.AmbulanceRegNo IS NOT NULL");

while ($join = mysqli_fetch_array($join_query)) {
    echo "Booking: " . $join['BookingNumber'] . " | Ambulance: " . $join['AmbulanceRegNo'] . " | Driver: " . $join['DriverName'] . " | Contact: " . $join['DriverContactNumber'] . " | Status: " . $join['Status'] . "<br>";
}
?> 