<?php
session_start();
//error_reporting(0);
include('includes/dbconnection.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
 

  <title>Emergancy Ambulance Hiring Portal || Tracking Page</title>

  <!-- Add Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  
  <!-- Add Leaflet JavaScript -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    #ambulanceMap {
      height: 400px;
      width: 100%;
      margin: 20px 0;
      border-radius: 8px;
    }
    .location-info {
      margin-top: 10px;
      padding: 10px;
      background-color: #f8f9fa;
      border-radius: 4px;
    }
  </style>
</head>

<body>

  <?php include_once('includes/header.php');?>

  <main id="main">

    <!-- ======= Breadcrumbs Section ======= -->
    <section class="breadcrumbs">
      <div class="container">

        <div class="d-flex justify-content-between align-items-center">
          <h2>Ambulance Tracking</h2>
          <ol>
            <li><a href="index.php" href="#hero">Home</a></li>
            <li>Ambulance Tracking</li>
          </ol>
        </div>

      </div>
    </section><!-- End Breadcrumbs Section -->

    <section class="inner-page">
      <div class="container">
       <?php
$id = $_GET['id'];   
$bookingnum = $_GET['bookingnum'];

// First check if the request exists
$ret = mysqli_query($con, "SELECT 
    tblambulancehiring.*, 
    tblambulance.AmbRegNum, 
    tblambulance.DriverName, 
    tblambulance.DriverContactNumber 
    FROM tblambulancehiring 
    LEFT JOIN tblambulance ON tblambulance.AmbRegNum = tblambulancehiring.AmbulanceRegNo  
    WHERE tblambulancehiring.ID = '$id' AND tblambulancehiring.BookingNumber = '$bookingnum'");

if(mysqli_num_rows($ret) == 0) {
    echo "<script>alert('Request not found.'); window.location.href='ambulance-tracking.php';</script>";
    exit();
}

while ($row = mysqli_fetch_array($ret)) {
   $arnum = $row['AmbulanceRegNo'];
   
   // Debug: Print the retrieved data
   echo "<!-- Debug Info: 
   AmbulanceRegNo: " . $row['AmbulanceRegNo'] . "
   AmbRegNum: " . $row['AmbRegNum'] . "
   DriverName: " . $row['DriverName'] . "
   DriverContactNumber: " . $row['DriverContactNumber'] . "
   Status: " . $row['Status'] . "
   -->";
   
   // Always try to get driver information directly from ambulance table if AmbulanceRegNo exists
   if ($row['AmbulanceRegNo'] != null && $row['AmbulanceRegNo'] != '') {
       $driverQuery = mysqli_query($con, "SELECT DriverName, DriverContactNumber FROM tblambulance WHERE AmbRegNum = '" . mysqli_real_escape_string($con, $row['AmbulanceRegNo']) . "'");
       if ($driverRow = mysqli_fetch_array($driverQuery)) {
           $row['DriverName'] = $driverRow['DriverName'];
           $row['DriverContactNumber'] = $driverRow['DriverContactNumber'];
           echo "<!-- Direct query executed - DriverName: " . $driverRow['DriverName'] . " -->";
       } else {
           echo "<!-- No driver found for AmbulanceRegNo: " . $row['AmbulanceRegNo'] . " -->";
       }
   }
?>
<table border="1" class="table table-bordered mg-b-0">
    <tr align="center">
        <th colspan="6" style="font-size:20px;color:blue;text-align: center;">
            View Request Details of #<?php echo $row['BookingNumber']; ?></th>
        
    </tr>
    <tr>
        <th>Patient Name</th>
        <td><?php echo $row['PatientName']; ?></td>
        <th>Relative Name</th>
        <td><?php echo $row['RelativeName']; ?></td>
    </tr>
    <tr>
    <th>Relative Contact Number</th>
    <td><?php  echo $row['RelativeConNum'];?></td>
    <th>Hiring Date</th>
    <td><?php  echo $row['HiringDate'];?></td>
    
  </tr>
  <tr>
    <th>Hiring Time</th>
    <td><?php  echo $row['HiringTime'];?></td>
     <th>Booking Date</th>
     <td><?php  echo $row['BookingDate'];?></td>
     <tr>
        <tr>
    <th>Address</th>
    <td><?php  echo $row['Address'];?></td>
    <th>City</th>
    <td><?php  echo $row['City'];?></td>
  </tr>
   <tr>
    <th>State</th>
    <td><?php  echo $row['State'];?></td>
    <th>Message</th>
    <td><?php  echo $row['Message'];?></td>
  </tr>
    <!-- Display other request details -->

    <?php
    $atype = $row['AmbulanceType'];  
    $ambulanceTypeText = "";
    switch ($atype) {
        case "1":
            $ambulanceTypeText = "Basic Life Support (BLS) Ambulances";
            break;
        case "2":
            $ambulanceTypeText = "Advanced Life Support (ALS) Ambulances";
            break;
        case "3":
            $ambulanceTypeText = "Non-Emergency Patient Transport Ambulances";
            break;
        
        default:
            $ambulanceTypeText = "Unknown";
            break;
    }
    ?>
    <tr>
        <th>Ambulance Type</th>
        <td colspan="3"><?php echo $ambulanceTypeText; ?></td>
    </tr>
    <!-- Display assigned ambulance information -->
    <?php if ($row['AmbulanceRegNo'] != null && $row['AmbulanceRegNo'] != ''){ ?>
    <tr>
        <th>Assigned Ambulance</th>
        <td colspan="3"><?php echo $row['AmbulanceRegNo']; ?></td>
    </tr>
    <?php } ?>
    <!-- Display other request details -->

    <?php if ($row['Remark'] != ''): ?>
    <tr>
        <th>Remark</th>
        <td><?php echo $row['Remark']; ?></td>
        <?php if ($row['Status'] != ''): ?>
        <th>Status</th>
        <td><?php echo $row['Status']; ?></td>
        <?php endif; ?>
    </tr>
  
    </tr>
    <?php endif; ?>
     <?php if ($row['AmbulanceRegNo'] != null && $row['AmbulanceRegNo'] != ''){ ?>
    <tr>     
       <th>Driver Name</th>
        <td><?php echo ($row['DriverName'] != null && $row['DriverName'] != '') ? $row['DriverName'] : 'Driver information not available'; ?></td>
         <th>Driver Contact Number</th>
        <td><?php echo ($row['DriverContactNumber'] != null && $row['DriverContactNumber'] != '') ? $row['DriverContactNumber'] : 'Contact not available'; ?></td>
      </tr>
 <?php }else {?>
  <tr>     
       <th>Driver Name</th>
        <td>Not Assigned Yet</td>
         <th>Driver Contact Number</th>
        <td>Not Assigned Yet</td>
      </tr><?php }?>
</table>

<?php if ($row['Status'] != '' && $row['Status'] != 'Reached' && $row['Status'] != 'Rejected') { ?>
<div class="row mt-4">
    <div class="col-12">
        <h4>Ambulance Location</h4>
        <div id="ambulanceMap"></div>
        <div class="location-info">
            <p>Current Status: <span id="ambulanceStatus"><?php echo $row['Status']; ?></span></p>
            <p>Last Updated: <span id="lastUpdated">Loading...</span></p>
        </div>
    </div>
</div>

<script>
    // Initialize the map
    var map = L.map('ambulanceMap').setView([<?php echo $row['Latitude']; ?>, <?php echo $row['Longitude']; ?>], 13);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add markers for patient and ambulance
    var patientMarker = L.marker([<?php echo $row['Latitude']; ?>, <?php echo $row['Longitude']; ?>], {
        icon: L.divIcon({
            className: 'patient-marker',
            html: '<i class="fas fa-user-circle fa-2x text-danger"></i>',
            iconSize: [30, 30]
        })
    }).addTo(map);

    var ambulanceMarker = L.marker([<?php echo $row['Latitude']; ?>, <?php echo $row['Longitude']; ?>], {
        icon: L.divIcon({
            className: 'ambulance-marker',
            html: '<i class="fas fa-ambulance fa-2x text-primary"></i>',
            iconSize: [30, 30]
        })
    }).addTo(map);

    // Add legend
    var legend = L.control({position: 'bottomright'});
    legend.onAdd = function(map) {
        var div = L.DomUtil.create('div', 'info legend');
        div.innerHTML = '<div><i class="fas fa-user-circle text-danger"></i> Patient Location</div>' +
                       '<div><i class="fas fa-ambulance text-primary"></i> Ambulance Location</div>';
        return div;
    };
    legend.addTo(map);

    // Function to update ambulance location
    function updateAmbulanceLocation() {
        fetch('get-ambulance-location.php?bookingnum=<?php echo $row['BookingNumber']; ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    ambulanceMarker.setLatLng([data.latitude, data.longitude]);
                    document.getElementById('ambulanceStatus').textContent = data.status;
                    document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString();
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Update location every 30 seconds
    updateAmbulanceLocation();
    setInterval(updateAmbulanceLocation, 30000);
</script>
<?php } ?>

<?php
}
?>

<?php 
  $bookingnum=$_GET['bookingnum'];
$query1=mysqli_query($con,"SELECT Remark,Status,UpdationDate,BookingNumber,AmbulanceRegNum FROM tbltrackinghistory

    where BookingNumber='$bookingnum'");
$count=mysqli_num_rows($query1);
if($count>0){
     ?>
 <div class="col-12">
        <table class="table table-bordered" border="1" width="100%">
                                        <tr>
                                            <th colspan="6" style="text-align:center;">Tracking History</th>
                                        </tr>
                                        <tr>
                                            <th>Remark</th>
                                            <th>Status</th>
                                            <th>Ambulance Registration Number </th>
                                            <th>Action Date</th>
                                        </tr>
<?php 
while($row1=mysqli_fetch_array($query1))
{
?>  

<tr>
<td><?php echo htmlentities($row1['Remark']);?></td>
                 <td> <?php   $pstatus=$row1['Status'];  
                 if($pstatus==""){ ?>
<span>New</span>
 <?php } elseif($pstatus=="Assigned"){ ?>
<span>Assigned</span>
 <?php } elseif($pstatus=="On the way"){ ?>
<span>On the Way</span>
 <?php } elseif($pstatus=="Pickup"){ ?>
<span>Patient Pick</span>
 <?php } elseif($pstatus=="Reached"){ ?>
<span>Patient Reached Hospital</span>
 <?php } elseif($pstatus=="Rejected"){ ?>
<span>Rejected</span>

<?php } ?>
</td>
<td><?php echo htmlentities($row1['AmbulanceRegNum']);?></td>
<td><?php echo htmlentities($row1['UpdationDate']);?></td>
             
</tr>
<?php } ?>

</table>
<?php } ?>
      </div>
    </section>

  </main><!-- End #main -->

<?php include_once('includes/footer.php');?>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>