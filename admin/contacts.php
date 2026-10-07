<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

<title>ServiceHub - Manage Contacts</title>

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

.back {
    color: white;
    text-decoration: none;
    background-color: #555;
    padding: 10px 18px;
    border-radius: 5px;
}

.container {
    width: 95%;
    margin: 35px auto;
}

.box {
    background-color: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #222;
    color: white;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

</style>

</head>

<body>


<div class="navbar">

<h2>ServiceHub Admin Panel</h2>

<a href="dashboard.php" class="back">
Back to Dashboard
</a>

</div>



<div class="container">

<div class="box">


<h1>Manage Contacts</h1>


<table>


<tr>

<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Subject</th>
<th>Message</th>
<th>Created At</th>

</tr>



<?php

$sql = "SELECT * FROM contacts ORDER BY id DESC";

$result = mysqli_query($conn, $sql);


if ($result && mysqli_num_rows($result) > 0) {


    while ($row = mysqli_fetch_assoc($result)) {


?>

<tr>

<td><?php echo $row["id"]; ?></td>

<td><?php echo $row["name"]; ?></td>

<td><?php echo $row["email"]; ?></td>

<td><?php echo $row["phone"]; ?></td>

<td><?php echo $row["subject"]; ?></td>

<td><?php echo $row["message"]; ?></td>

<td><?php echo $row["created_at"]; ?></td>

</tr>


<?php

    }

}

else {

echo "<tr><td colspan='7'>No contacts found.</td></tr>";

}

?>


</table>


</div>

</div>


</body>

</html>
