<?php
require_once("DBConnection.php");
session_start();
global $row;
if(!isset($_SESSION["sess_user"])){
  header("Location: index.php");
}
else{
?>

<?php 
  $reasonErr = $absenceErr = "";
  global $leaveApplicationValidate;
  if(isset($_POST['submit'])){
    if(empty($_POST['absence'])){
      $absenceErr = "Please select absence type";
      $leaveApplicationValidate = false;
    }
    else{
      $absence = mysqli_real_escape_string($conn,$_POST['absence']);
      $leaveApplicationValidate = true;
    }

    if(empty($_POST['fromdate'])){
      $fromdateErr = "Please Enter starting date";
      $leaveApplicationValidate = false;
    }
    else{
      $fromdate = mysqli_real_escape_string($conn,$_POST['fromdate']);
      $leaveApplicationValidate = true;
    }

    if(empty($_POST['todate'])){
      $todateErr = "Please Enter ending date";
      $leaveApplicationValidate = false;
    }
    else{
      $todate = mysqli_real_escape_string($conn,$_POST['todate']);
      $leaveApplicationValidate = true;
    }

    // Calculate end date after three months
    $datetime = new DateTime($fromdate);
    $datetime->add(new DateInterval('P3M'));
    $end_date = $datetime->format('Y-m-d');

    $reason = mysqli_real_escape_string($conn,$_POST['reason']);
    if(empty($reason)){
      $reasonErr = "Please give reason for the leave in detail";
      $leaveApplicationValidate = false;
    }
    else{
      $absencePlusReason = $absence." : ".$reason;
      $leaveApplicationValidate = true;
    }
    
    $status = "Pending";
    
    if($leaveApplicationValidate){
      // Check if file is uploaded successfully
      if(isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/"; // Directory where the file will be stored
        $target_file = $target_dir . basename($_FILES["picture"]["name"]); // File path
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["picture"]["tmp_name"]);
        if($check !== false) {
          $uploadOk = 1;
        } else {
          echo "File is not an image.";
          $uploadOk = 0;
        }
        
        // Check file size
        if ($_FILES["picture"]["size"] > 500000) {
          echo "Sorry, your file is too large.";
          $uploadOk = 0;
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
          echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
          $uploadOk = 0;
        }
        
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
          echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
          if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
            // Insert the file path into the database
            $picture_path = $target_file;
            // Insert data into the database
            $username = $_SESSION["sess_user"];
            $eid_query = mysqli_query($conn,"SELECT id FROM users WHERE name='".$username."'");
            $row = mysqli_fetch_array($eid_query);
            $query = "INSERT INTO leaves(eid, ename, descr, fromdate, todate, status, picture_path) VALUES({$row['id']},'{$username}','$absencePlusReason', '$fromdate', '$todate', '$status', '$picture_path')";
            $execute = mysqli_query($conn,$query);
            if($execute){
              echo '<script>alert("Leave Application Submitted. Please wait for approval status!")</script>';
            }
            else{
              echo "Query Error : " . $query . "<br>" . mysqli_error($conn);;
            }
          } else {
            echo "Sorry, there was an error uploading your file.";
          }
        }
      } else {
        echo "No file uploaded.";
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
  <link rel="stylesheet" href="css/style.css">
  <title>KABU STUDENT ONLINE LEAVE APPLICATION</title>
  <style>
    h1 {
      text-align: center;
      font-size: 2.5em;
      font-weight: bold;
      padding-top: 1em;
      margin-bottom: -0.5em;
    }

    form {
      padding: 40px;
    }

    input,
    textarea {
      margin: 5px;
      font-size: 1.1em !important;
      outline: none;
    }

    label {
      margin-top: 2em;
      font-size: 1.1em !important;
    }

    label.form-check-label {
      margin-top: 0px;
    }

    #err {
      display: none;
      padding: 1.5em;
      padding-left: 4em;
      font-size: 1.2em;
      font-weight: bold;
      margin-top: 1em;
    }

    table{
      width: 90% !important;
      margin: 1.5rem auto !important;
      font-size: 1.1em !important;
    }

    .error{
      color: #FF0000;
    }

    /* Style for Other text area */
    #otherReason {
      display: none;
    }
  </style>

  <script>
    const validate = () => {

      let desc = document.getElementById('leaveDesc').value;
      let radio = document.querySelectorAll('input[name="absence"]:checked');
      let errDiv = document.getElementById('err');

      let checkedValue = [];
      for (let i = 0; i < radio.length; i++) {
          checkedValue.push(radio[i].value);
      }

      let errMsg = [];

      if (desc === "") {
        errMsg.push("Please enter the reason and date of leave");
      }

      if(checkedValue.length < 1){
        errMsg.push("Please select the type of Leave");
      }

      if (errMsg.length > 0) {
        errDiv.style.display = "block";
        let msgs = "";

        for (let i = 0; i < errMsg.length; i++) {
          msgs += errMsg[i] + "<br/>";
        }

        errDiv.innerHTML = msgs;
        scrollTo(0, 0);
        return;
      }
    }

    // Function to toggle visibility of textarea when Other option is selected
    const toggleOtherReason = () => {
      let otherReason = document.getElementById('otherReason');
      let otherRadio = document.getElementById('Other');
      if (otherRadio.checked) {
        otherReason.style.display = 'block';
      } else {
        otherReason.style.display = 'none';
      }
    }

    // Function to calculate end date after selecting start date
    const calculateEndDate = () => {
      let startDate = document.getElementById('fromdate').value;
      let endDate = new Date(startDate);
      endDate.setMonth(endDate.getMonth() + 3);
      let formattedEndDate = endDate.toISOString().split('T')[0];
      document.getElementById('todate').value = formattedEndDate;
    }
  </script>

</head>

<body>
  <!--Navbar-->
  <nav class="navbar header-nav navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"> KABU STUDENT ONLINE LEAVE APPLICATION</a>
      <ul class="nav justify-content-end">
           
            <li class="nav-item">
                <a class="nav-link" href="myhistory.php" style="color:white;">My Leave History</a>
            </li>
            <li class="nav-item">
            <button id="logout" onclick="window.location.href='logout.php';">Logout</button>
            </li>
            </ul>

      
    </div>
  </nav>


  <h1>Leave Application</h1>

  <div class="container">
    <div class="alert alert-danger" id="err" role="alert">
    </div>
  
    <form method="POST" enctype="multipart/form-data"> <!-- Add enctype attribute for file upload -->
      
  
      <label><b>Select Leave Type :</b></label>
      <!-- error message if type of absence isn't selected -->
      <span class="error"><?php echo "&nbsp;".$absenceErr ?></span><br/>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="absence" id="Sick" value="Sick">
        <label class="form-check-label" for="Sick">
          Sick
        </label>
      </div>
        <!-- Remaining radio buttons -->
        <div class="form-check">
    <input class="form-check-input" type="radio" name="absence" id="Casual" value="Casual">
    <label class="form-check-label" for="Casual">
        Casual
    </label>
</div>
<div class="form-check">
    <input class="form-check-input" type="radio" name="absence" id="Vacation" value="Vacation">
    <label class="form-check-label" for="Vacation">
        Vacation
    </label>
</div>
<div class="form-check">
    <input class="form-check-input" type="radio" name="absence" id="Bereavement" value="Bereavement">
    <label class="form-check-label" for="Bereavement">
        Bereavement
    </label>
</div>
      <div class="form-check">
    <input class="form-check-input" type="radio" name="absence" id="TimeOffWithoutPay" value="Time off without pay">
    <label class="form-check-label" for="TimeOffWithoutPay">
        Time off without pay
    </label>
      <!-- Remaining radio buttons -->
      <!-- Modify for Maternity/Paternity to call calculateEndDate() function -->
      <div class="form-check">
    <input class="form-check-input" type="radio" name="absence" id="MaternityPaternity" value="Maternity / Paternity" onchange="calculateEndDate()">
    <label class="form-check-label" for="MaternityPaternity">
        Maternity / Paternity
    </label>
</div>
<div class="form-check">
    <input class="form-check-input" type="radio" name="absence" id="Other" value="Other" onclick="toggleOtherReason()">
    <label class="form-check-label" for="Other">
        Other
    </label>
</div>

<!-- Text area for Other reason -->
<div id="otherReason">
  <textarea class="form-control" name="otherReasonText" id="otherReasonText" rows="4" placeholder="Enter other reason..."></textarea>
</div>
<!-- Add other radio buttons here -->

      <div class="mb-3 ">
        <label for="dates"><b>From -</b></label>
        <input type="date" name="fromdate" id="fromdate" onchange="calculateEndDate()">
  
        <label for="dates"><b>To -</b></label>
        <input type="date" name="todate" id="todate">
      </div>
  
      <div class="mb-3">
        
        <label for="leaveDesc" class="form-label"><b>Please mention reasons for your leave days :</b></label>
        <!-- error message if reason of the leave is not given -->
        <span class="error"><?php echo "&nbsp;".$reasonErr ?></span>
        <textarea class="form-control" name="reason" id="leaveDesc" rows="4" placeholder="Enter Here..."></textarea>
      </div>

<!-- Picture upload input -->
<div class="form-group">
    <label for="picture"><b>Upload Evidence:</b></label>
    <input type="file" name="picture" class="form-control-file" id="picture">
</div>

<!-- Submit button -->
<input type="submit" name="submit" value="Submit Leave Request" class="btn btn-success" onclick="validate();">


  <footer class="footer navbar navbar-expand-lg navbar-light bg-light" style="color:white;">
    <div>
    <p class="text-center">&copy; <?php echo date("Y"); ?>KABU STUDENT ONLINE LEAVE APPLICATION</p>
      <p class="text-center">www.kabarak.ac.ke</p>
    </div>
  </footer>

</body>

</html>

<?php
}

ini_set('display_errors', true);
error_reporting(E_ALL);
?>
