<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

require_once "../includes/db_connect.php";

/** @var mysqli $conn */

?>

<!DOCTYPE html>
<html>

<head>

<title>ServiceHub Admin Dashboard</title>

<style>

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
}

.navbar {
    background-color: #222;
    color: white;
    padding: 18px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar h2 {
    margin: 0;
}

.logout {
    color: white;
    text-decoration: none;
    background-color: #e74c3c;
    padding: 10px 18px;
    border-radius: 5px;
}

.container {
    width: 90%;
    margin: 40px auto;
}

.welcome {
    background-color: white;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.card {

    background-color: white;
    padding: 25px;
    text-align: center;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);

}

.card h2 {
    margin: 10px 0;
}

.card a {

    text-decoration: none;
    color: white;
    background-color: #222;
    padding: 10px 20px;
    border-radius: 5px;
    display: inline-block;

}

</style>

</head>


<body>


<div class="navbar">

<h2>ServiceHub Admin Panel</h2>

<a href="logout.php" class="logout">
Logout
</a>

</div>



<div class="container">


<div class="welcome">

<h1>
Welcome, <?php echo $_SESSION["admin_name"]; ?>!
</h1>

<p>
Manage your ServiceHub website from this dashboard.
</p>

</div>



<div class="cards">


<div class="card">

<h2>Services</h2>

<p>Manage salon services</p>

<a href="services.php">
Open
</a>

</div>



<div class="card">

<h2>Bookings</h2>

<p>View customer bookings</p>

<a href="bookings.php">
Open
</a>

</div>



<div class="card">

<h2>Users</h2>

<p>Manage registered users</p>

<a href="users.php">
Open
</a>

</div>



<div class="card">

<h2>Contacts</h2>

<p>View customer messages</p>

<a href="contacts.php">
Open
</a>

</div>



</div>


</div>


</body>

</html>