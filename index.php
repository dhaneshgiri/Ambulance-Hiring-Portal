<?php
session_start();
//error_reporting(0);
include('includes/dbconnection.php');
include('includes/distanceCalculator.php');

if(isset($_POST['submit'])) {
    $bookingnum = mt_rand(100000000, 999999999);
    $pname = $_POST['pname'];
    $rname = $_POST['rname'];
    $phone = $_POST['phone'];
    $ambulancetype = $_POST['ambulancetype'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $message = $_POST['message'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
   
    // Get nearest available ambulance
    $nearestAmbulance = getNearestAmbulance($con, $latitude, $longitude, $ambulancetype);
    
    if ($nearestAmbulance) {
        $ambulanceRegNo = $nearestAmbulance['AmbRegNum'];
        $status = 'Assigned';
        
        // Update ambulance status
        mysqli_query($con, "UPDATE tblambulance SET Status='Assigned' WHERE AmbRegNum='$ambulanceRegNo'");
    } else {
        $ambulanceRegNo = null;
        $status = 'New';
    }

    $query = mysqli_query($con, "INSERT INTO tblambulancehiring (BookingNumber, PatientName, RelativeName, RelativeConNum, AmbulanceType, Address, City, State, Message, Status, AmbulanceRegNo, Latitude, Longitude) VALUES ('$bookingnum', '$pname', '$rname', '$phone', '$ambulancetype', '$address', '$city', '$state', '$message', '$status', '$ambulanceRegNo', '$latitude', '$longitude')");

    if ($query) {
        if ($nearestAmbulance) {
            // Add tracking history
            mysqli_query($con, "INSERT INTO tbltrackinghistory(BookingNumber, AmbulanceRegNum, Remark, Status) VALUES ('$bookingnum', '$ambulanceRegNo', 'Ambulance assigned automatically based on proximity', 'Assigned')");
            echo "<script>alert('Your request has been sent successfully. Your Booking Number is: $bookingnum. The nearest available ambulance has been assigned.');</script>";
        } else {
            echo "<script>alert('Your request has been sent successfully. Your Booking Number is: $bookingnum. No ambulances are currently available.');</script>";
        }
        echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  
  <title>Emergency Ambulance Hiring Portal || Home Page</title>
  
  <!-- Base font size and inheritance -->
  <style>
    :root {
      font-size: 16px;
    }
    body {
      font-size: 0.95rem;
    }
    /* Ensure all elements inherit font size */
    * {
      font-size: inherit;
    }
    /* Navigation and header elements */
    .navbar a,
    .navbar-nav .nav-link,
    .navbar-brand,
    .topbar a,
    .topbar span,
    .header .logo a,
    .header .nav-menu > li > a,
    .header .nav-menu .drop-down > a,
    .header .nav-menu .drop-down ul a {
      font-size: 0.95rem !important;
    }
    /* Fix map-related font size issues */
    .leaflet-container,
    .leaflet-control,
    .leaflet-popup,
    .leaflet-tooltip,
    .leaflet-popup-content,
    .leaflet-popup-content-wrapper {
      font-size: 0.95rem !important;
    }
    /* Ensure form elements maintain proper size */
    input, select, button, textarea {
      font-size: 0.95rem !important;
    }
    /* Ensure headings maintain proper size */
    h1 {
      font-size: 1.5rem !important;
    }
    h2 {
      font-size: 1.25rem !important;
    }
    h3 {
      font-size: 1.1rem !important;
    }
    h4 {
      font-size: 1rem !important;
    }
    h5, h6 {
      font-size: 0.95rem !important;
    }
    /* Fix any Bootstrap overrides */
    .form-control,
    .btn,
    .form-select {
      font-size: 0.95rem !important;
    }
    /* Section titles */
    .section-title h2 {
      font-size: 1.5rem !important;
    }
    /* Navigation and buttons */
    .btn-get-started,
    .cta-btn {
      font-size: 1rem !important;
    }
    /* Contact info in header */
    .contact-info a,
    .contact-info i,
    .contact-info span {
      font-size: 0.95rem !important;
    }
    /* Social media links */
    .social-links a {
      font-size: 0.95rem !important;
    }
    /* Hire Ambulance section specific styles */
    #appointment .section-title h2,
    .carousel-item h2,
    #cta h3,
    .btn-get-started,
    .cta-btn {
      font-size: 1.5rem !important;
    }
    /* Form labels and placeholders */
    #appointment label,
    #appointment input::placeholder,
    #appointment select::placeholder {
      font-size: 0.95rem !important;
    }
    /* Submit button in form */
    #appointment button[type="submit"] {
      font-size: 1rem !important;
      padding: 8px 18px;
    }
  </style>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

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

  <!-- =======================================================
  * Template Name: Medicio
  * Updated: Jan 29 2024 with Bootstrap v5.3.2
  * Template URL: https://bootstrapmade.com/medicio-free-bootstrap-theme/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

  <!-- Add Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  
  <!-- Add Leaflet JavaScript -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  
  <!-- Add custom styles for the map -->
  <style>
    #map {
      height: 400px;
      width: 100%;
      margin-bottom: 20px;
      border-radius: 8px;
    }
    .location-info {
      margin-top: 10px;
      padding: 10px;
      background-color: #f8f9fa;
      border-radius: 4px;
      font-size: 1rem !important;
    }
  </style>
</head>

<body>

 <?php include_once('includes/header.php');?>

  <!-- ======= Hero Section ======= -->
  <section id="hero">
    <div id="heroCarousel" data-bs-interval="5000" class="carousel slide carousel-fade" data-bs-ride="carousel">

      <ol class="carousel-indicators" id="hero-carousel-indicators"></ol>

      <div class="carousel-inner" role="listbox">

        <!-- Slide 1 -->
        <div class="carousel-item active" style="background-image: url(assets/img/slide/slide-1.jpg)">
          <div class="container">
            <h2>Hire an Ambulance in Minutes — Track, Book, and Stay Safe</span></h2>
  
            <a href="#appointment" class="btn-get-started scrollto">Hire Ambulance</a>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="carousel-item" style="background-image: url(assets/img/slide/slide-2.jpg)">
          <div class="container">
            <h2>Hire an Ambulance in Minutes — Track, Book, and Stay Safe</h2>
        
            <a href="#appointment" class="btn-get-started scrollto">Hire Ambulance</a>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="carousel-item" style="background-image: url(assets/img/slide/slide-3.jpg)">
          <div class="container">
            <h2>Hire an Ambulance in Minutes — Track, Book, and Stay Safe</h2>
            <a href="#appointment" class="btn-get-started scrollto">Hire Ambulance</a>
          </div>
        </div>

      </div>

      <a class="carousel-control-prev" href="#heroCarousel" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
      </a>

      <a class="carousel-control-next" href="#heroCarousel" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
      </a>

    </div>
  </section><!-- End Hero -->

  <main id="main">

    <!-- ======= Featured Services Section ======= -->
    <section id="featured-services" class="featured-services">
      <div class="container" data-aos="fade-up">

            </div>
          </div>

        </div>

      </div>
    </section><!-- End Featured Services Section -->

    <!-- ======= Cta Section ======= -->
    <section id="cta" class="cta">
      <div class="container" data-aos="zoom-in">

        <div class="text-center">
          <h3>In an emergency? Need help now?</h3>
          <a class="cta-btn scrollto" href="#appointment">Hire an Ambulance</a>
        </div>

      </div>
    </section><!-- End Cta Section -->
    
  <!-- ======= Appointment Section ======= -->
    <section id="appointment" class="appointment section-bg">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Hire an Ambulance</h2>
        </div>

        <form action="" method="post" role="form" class="form-control" data-aos="fade-up" data-aos-delay="100">
          <div class="row" style="padding-top:20px">
            <div class="col-md-4 form-group">
              <input type="text" name="pname" class="form-control" id="pname" placeholder="Enter Patient Name" required>
            </div>
            <div class="col-md-4 form-group">
              <input type="text" name="rname" class="form-control" id="rname" placeholder="Enter Relative Name" required>
            </div>
           
            <div class="col-md-4 form-group mt-3 mt-md-0">
              <input type="tel" class="form-control" name="phone" id="phone" placeholder="Enter Relative Phone Number" 
                pattern="[0-9]{10}" 
                maxlength="10" 
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 form-group mt-3">
              <select name="ambulancetype" id="ambulancetype" class="form-select">
                <option value="">Select Type of Ambulance</option>
                <option value="1">Basic Life Support (BLS) Ambulances</option>
                <option value="2"> Advanced Life Support (ALS) Ambulances</option>
                <option value="3">Non-Emergency Patient Transport Ambulances</option>
              </select>
            </div>
          </div>
           <div class="row">
            <div class="col-md-12 form-group mt-3">
              <div id="map"></div>
              <div class="location-info">
                <p>Selected Location: <span id="selected-location">Click on the map to select your location</span></p>
                <p>Coordinates: <span id="coordinates">Not selected</span></p>
                <button type="button" id="getLocationBtn" class="btn btn-primary mt-2">
                  <i class="fas fa-location-arrow"></i> Get Current Location
                </button>
                <span id="locationStatus" class="ms-2"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 form-group mt-3">
              <input type="text" name="address" class="form-control" id="address" placeholder="Enter your address" required>
            </div>
          </div>
          <div class="row" style="padding-top:20px">
            <div class="col-md-4 form-group">
              <input type="text" name="city" class="form-control" id="city" placeholder="Enter City">
            </div>
            <div class="col-md-4 offset-md-4 form-group text-end d-flex align-items-center justify-content-end">
              <button type="submit" name="submit" class="btn btn-primary" style="min-width: 100px; padding: 8px 18px; font-size: 1rem;">Submit</button>
            </div>
          </div>
        </form>
    </section><!-- End Appointment Section -->

    <!-- ======= About Us Section ======= -->
    <section id="about" class="about">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>About Us</h2>
          <?php

$ret=mysqli_query($con,"select * from tblpage where PageType='aboutus' ");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {

?>
          <p><?php  echo $row['PageDescription'];?></p><?php } ?>
        </div>

      

      </div>
    </section><!-- End About Us Section -->



    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
      <div class="container">

        <div class="section-title">
          <h2>Contact</h2>
          <p>In case of emergencies or for booking an ambulance, we're here to assist you 24/7. Reach out to us via phone, email, or the contact form below for immediate support. Your safety and timely assistance are our top priorities—let us help you when it matters most!  </p>
        </div>

      </div>

  

      <div class="container">

        <div class="row mt-5">

          <div class="col-lg-12">

             <div class="row">
              <?php 
 $query=mysqli_query($con,"select * from  tblpage where PageType='contactus'");
 while ($row=mysqli_fetch_array($query)) {


 ?>
              <div class="col-md-12">
                <div class="info-box">
                  <i class="bx bx-map"></i>
                  <h3>Our Address</h3>
                  <p><?php  echo $row['PageDescription'];?></p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-box mt-4">
                  <i class="bx bx-envelope"></i>
                  <h3>Email Us</h3>
                  <p><?php  echo $row['Email'];?></p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-box mt-4">
                  <i class="bx bx-phone-call"></i>
                  <h3>Call Us</h3>
                  <p><?php  echo $row['MobileNumber'];?></p>
                </div>
              </div><?php } ?>
            </div>

         

        </div>

      </div>
    </section><!-- End Contact Section -->

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

  <script>
    // Initialize the map
    var map = L.map('map').setView([20.5937, 78.9629], 5); // Center on India

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add a marker that will be updated when user clicks
    var marker = L.marker([0, 0], {
      draggable: true
    }).addTo(map);

    // Initialize variables for coordinates
    var latitude = 0;
    var longitude = 0;

    // Function to update location display
    function updateLocationDisplay(lat, lng) {
      // Update coordinates
      latitude = lat;
      longitude = lng;
      
      // Update display
      document.getElementById('coordinates').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
      
      // Reverse geocode to get address
      fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('selected-location').textContent = data.display_name;
          document.getElementById('address').value = data.display_name;
          
          // Extract city and state from address components
          const addressParts = data.display_name.split(',');
          if (addressParts.length >= 2) {
            // Usually the city is the second-to-last part 
            const state = addressParts[addressParts.length - 1].trim();
            const city = addressParts[addressParts.length - 2].trim();
            
            document.getElementById('city').value = city;
            
          }
        });
    }

    // Function to get current location
    function getCurrentLocation() {
      const locationStatus = document.getElementById('locationStatus');
      locationStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting your location...';
      
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Update map view
            map.setView([lat, lng], 15);
            
            // Update marker
            marker.setLatLng([lat, lng]);
            
            // Update location display
            updateLocationDisplay(lat, lng);
            
            locationStatus.innerHTML = '<i class="fas fa-check text-success"></i> Location captured successfully!';
            setTimeout(() => {
              locationStatus.innerHTML = '';
            }, 3000);
          },
          function(error) {
            let errorMessage = '';
            switch(error.code) {
              case error.PERMISSION_DENIED:
                errorMessage = 'Please allow location access to use this feature.';
                break;
              case error.POSITION_UNAVAILABLE:
                errorMessage = 'Location information is unavailable.';
                break;
              case error.TIMEOUT:
                errorMessage = 'The request to get your location timed out.';
                break;
              default:
                errorMessage = 'An unknown error occurred.';
                break;
            }
            locationStatus.innerHTML = `<i class="fas fa-exclamation-triangle text-danger"></i> ${errorMessage}`;
          },
          {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
          }
        );
      } else {
        locationStatus.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> Geolocation is not supported by your browser.';
      }
    }

    // Add click handler for the Get Current Location button
    document.getElementById('getLocationBtn').addEventListener('click', getCurrentLocation);

    // Handle map click
    map.on('click', function(e) {
      var lat = e.latlng.lat;
      var lng = e.latlng.lng;
      
      // Update marker position
      marker.setLatLng([lat, lng]);
      
      // Update location display
      updateLocationDisplay(lat, lng);
    });

    // Handle marker drag
    marker.on('dragend', function(e) {
      var lat = marker.getLatLng().lat;
      var lng = marker.getLatLng().lng;
      
      // Update location display
      updateLocationDisplay(lat, lng);
    });

    // Add hidden input fields for coordinates
    document.querySelector('form').addEventListener('submit', function(e) {
      var latInput = document.createElement('input');
      latInput.type = 'hidden';
      latInput.name = 'latitude';
      latInput.value = latitude;
      
      var lngInput = document.createElement('input');
      lngInput.type = 'hidden';
      lngInput.name = 'longitude';
      lngInput.value = longitude;
      
      this.appendChild(latInput);
      this.appendChild(lngInput);
    });

    // Add this to your existing script section
    document.getElementById('phone').addEventListener('input', function(e) {
        // Remove any non-digit characters
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Limit to 10 digits
        if (this.value.length > 10) {
            this.value = this.value.slice(0, 10);
        }
        
        // Add validation message
        const phoneError = document.getElementById('phoneError');
        if (this.value.length !== 10) {
            if (!phoneError) {
                const error = document.createElement('div');
                error.id = 'phoneError';
                error.className = 'text-danger';
                error.textContent = 'Phone number must be exactly 10 digits';
                this.parentNode.appendChild(error);
            }
        } else {
            const phoneError = document.getElementById('phoneError');
            if (phoneError) {
                phoneError.remove();
            }
        }
    });

    // Add form validation
    document.querySelector('form').addEventListener('submit', function(e) {
      const phone = document.getElementById('phone');
      if (phone.value.length !== 10) {
        e.preventDefault();
        alert('Please enter a valid 10-digit phone number');
        phone.focus();
        return;
      }

      // If city and state are empty but we have coordinates, allow submission
      const city = document.getElementById('city').value;
      const state = document.getElementById('state').value;
      if ((!city || !state) && (latitude === 0 && longitude === 0)) {
        e.preventDefault();
        alert('Please either use the map to select your location or fill in the city  manually');
        return;
      }
    });
  </script>

  <!-- Add Google Maps JavaScript API -->
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap">
  </script>

</body>

</html>
</html>