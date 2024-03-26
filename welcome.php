<?php
require_once("DBConnection.php");
session_start();
global $row;
if(!isset($_SESSION["sess_user"])){
  header("Location: index.php");
}
else{
}

ini_set('display_errors', true);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to KABU Student Leave Application System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            margin-bottom: 30px;
        }
        .button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #45a049;
        }
        .images {
            display: flex;
            justify-content: center;
            margin-top: 50px;
        }
        .images img {
            width: 200px;
            margin: 0 10px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to KABU Student Leave Application System</h1>
        <p>Dear Kabarak University student, thank you for signing up! You are now ready to use the KABU Student Leave Application System. Simply click the button below to proceed with your leave application.</p>
        <button class="button" onclick="location.href='leaveAplicationForm.php'">Apply for Leave</button>
        <div class="images">
            <img src="img/image 2.jpg" alt="Image 1">
            <img src="img/image 3.jpg" alt="Image 2">
            <img src="img/img 1.jpg" alt="Image 3">
        </div>
    </div>
</body>
</html>
