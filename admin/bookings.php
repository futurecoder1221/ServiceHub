```php
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

require_once "../includes/db_connect.php";

/** @var mysqli $conn */

$message = "";

/* Update Booking Status */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_status"])) {

    $booking_id = $_POST["booking_id"];
    $status = $_POST["status"];

    $sql = "UPDATE bookings SET status = ? WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $status,
            $booking_id
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Booking status updated successfully.";
        } else {
            $message = "Error updating booking status.";
        }

        mysqli_stmt_close($stmt);

    } else {

        $message = "Database query error.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>ServiceHub - Manage Bookings</title>

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

        .back:hover {
            background-color: #444;
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
            overflow-x: auto;
        }

        h1 {
            margin-top: 0;
        }

        .message {
            background-color: #dcfce7;
            color: #166534;
            padding: 12px 15px;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            min-width: 950px;
        }

        th,
        td {
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

        .status-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .status-form select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .update-btn {
            padding: 8px 12px;
            background-color: #0284c7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .update-btn:hover {
            background-color: #0369a1;
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


        <?php if ($message != "") { ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>


        <div class="box">

            <h1>Manage Bookings</h1>


            <table>

                <tr>

                    <th>ID</th>
                    <th>User ID</th>
                    <th>Service</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Message</th>
                    <th>Status</th>

                </tr>


                <?php

                $sql = "SELECT bookings.*, services.name AS service_name
                        FROM bookings
                        LEFT JOIN services
                        ON bookings.service_id = services.id
                        ORDER BY bookings.id DESC";

                $result = mysqli_query($conn, $sql);


                if ($result && mysqli_num_rows($result) > 0) {

                    while ($row = mysqli_fetch_assoc($result)) {

                ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($row["id"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["user_id"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["service_name"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["name"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["phone"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["booking_date"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["booking_time"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["message"]); ?>
                    </td>

                    <td>

                        <form method="POST" class="status-form">

                            <input
                                type="hidden"
                                name="booking_id"
                                value="<?php echo $row["id"]; ?>"
                            >

                            <select name="status">

                                <option value="Pending"
                                    <?php
                                    if ($row["status"] == "Pending") {
                                        echo "selected";
                                    }
                                    ?>>
                                    Pending
                                </option>

                                <option value="Confirmed"
                                    <?php
                                    if ($row["status"] == "Confirmed") {
                                        echo "selected";
                                    }
                                    ?>>
                                    Confirmed
                                </option>

                                <option value="Completed"
                                    <?php
                                    if ($row["status"] == "Completed") {
                                        echo "selected";
                                    }
                                    ?>>
                                    Completed
                                </option>

                                <option value="Cancelled"
                                    <?php
                                    if ($row["status"] == "Cancelled") {
                                        echo "selected";
                                    }
                                    ?>>
                                    Cancelled
                                </option>

                            </select>

                            <button
                                type="submit"
                                name="update_status"
                                class="update-btn"
                            >
                                Update
                            </button>

                        </form>

                    </td>

                </tr>

                <?php

                    }

                } else {

                    echo "<tr><td colspan='9'>No bookings found.</td></tr>";

                }

                ?>

            </table>

        </div>

    </div>


</body>

</html>
```
