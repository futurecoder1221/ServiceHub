<?php
require_once "../includes/check_login.php";
require_once "../includes/db_connect.php";

/** @var mysqli $conn */

$user_id = $_SESSION["user_id"];


/* TOTAL BOOKINGS */

$total_sql = "SELECT COUNT(*) AS total 
              FROM bookings 
              WHERE user_id = ?";

$total_stmt = mysqli_prepare($conn, $total_sql);
mysqli_stmt_bind_param($total_stmt, "i", $user_id);
mysqli_stmt_execute($total_stmt);

$total_result = mysqli_stmt_get_result($total_stmt);
$total_row = mysqli_fetch_assoc($total_result);

$total_bookings = $total_row["total"];


/* PENDING BOOKINGS */

$pending_sql = "SELECT COUNT(*) AS total 
                FROM bookings 
                WHERE user_id = ? 
                AND status = 'Pending'";

$pending_stmt = mysqli_prepare($conn, $pending_sql);
mysqli_stmt_bind_param($pending_stmt, "i", $user_id);
mysqli_stmt_execute($pending_stmt);

$pending_result = mysqli_stmt_get_result($pending_stmt);
$pending_row = mysqli_fetch_assoc($pending_result);

$pending_bookings = $pending_row["total"];


/* COMPLETED BOOKINGS */

$completed_sql = "SELECT COUNT(*) AS total 
                  FROM bookings 
                  WHERE user_id = ? 
                  AND status = 'Completed'";

$completed_stmt = mysqli_prepare($conn, $completed_sql);
mysqli_stmt_bind_param($completed_stmt, "i", $user_id);
mysqli_stmt_execute($completed_stmt);

$completed_result = mysqli_stmt_get_result($completed_stmt);
$completed_row = mysqli_fetch_assoc($completed_result);

$completed_bookings = $completed_row["total"];


/* MY BOOKINGS */

$booking_sql = "SELECT 
                    bookings.id,
                    services.name AS service_name,
                    bookings.booking_date,
                    bookings.booking_time,
                    bookings.status
                FROM bookings
                LEFT JOIN services 
                    ON bookings.service_id = services.id
                WHERE bookings.user_id = ?
                ORDER BY bookings.id DESC";

$stmt = mysqli_prepare($conn, $booking_sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>

<head>

    <title>ServiceHub - Dashboard</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            color: #222;
        }

        /* NAVBAR */

        .navbar {
            background-color: #222;
            color: white;
            padding: 18px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .dashboard-text {
            font-size: 14px;
            color: #ddd;
        }

        .logout {
            color: white;
            text-decoration: none;
            background-color: #e74c3c;
            padding: 9px 17px;
            border-radius: 6px;
        }

        .logout:hover {
            background-color: #c0392b;
        }

        /* MAIN */

        .container {
            width: 88%;
            max-width: 1100px;
            margin: 35px auto;
        }

        /* WELCOME */

        .welcome {
            background: linear-gradient(135deg, #222, #444);
            color: white;
            padding: 35px;
            border-radius: 14px;
            margin-bottom: 25px;
        }

        .welcome h1 {
            margin: 0 0 10px;
            font-size: 30px;
        }

        .welcome p {
            margin: 6px 0;
            color: #ddd;
        }

        .booking-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background-color: white;
            color: #222;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .booking-btn:hover {
            background-color: #eee;
        }

        /* SUMMARY CARDS */

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .card h3 {
            margin: 0 0 10px;
            color: #666;
            font-size: 15px;
        }

        .card-number {
            font-size: 32px;
            font-weight: bold;
            color: #222;
        }

        /* BOOKINGS */

        .section-title {
            font-size: 23px;
            margin-bottom: 15px;
        }

        .booking-area {
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
        }

        .booking-table th {
            background-color: #222;
            color: white;
            padding: 13px;
            text-align: left;
        }

        .booking-table td {
            padding: 13px;
            border-bottom: 1px solid #eee;
        }

        .booking-table tr:last-child td {
            border-bottom: none;
        }

        /* STATUS */

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            background-color: #fff3cd;
            color: #856404;
            font-size: 13px;
            font-weight: bold;
        }

        .no-booking {
            text-align: center;
            padding: 25px;
            color: #777;
        }

        /* MOBILE */

        @media (max-width: 700px) {

            .navbar {
                padding: 15px 20px;
            }

            .dashboard-text {
                display: none;
            }

            .container {
                width: 94%;
            }

            .welcome {
                padding: 25px;
            }

            .welcome h1 {
                font-size: 25px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .booking-area {
                overflow-x: auto;
            }

            .booking-table {
                min-width: 600px;
            }

        }

    </style>

</head>

<body>


    <!-- NAVBAR -->

    <div class="navbar">

        <div class="logo">
            ServiceHub
        </div>

        <div class="nav-right">

            <span class="dashboard-text">
                User Dashboard
            </span>

            <a href="../includes/logout.php" class="logout">
                Logout
            </a>

        </div>

    </div>


    <!-- MAIN CONTENT -->

    <div class="container">


        <!-- WELCOME -->

        <div class="welcome">

            <h1>
                Welcome to ServiceHub! 👋
            </h1>

            <p>
                Manage your service bookings easily from your dashboard.
            </p>

            <p>
                Choose a service and book it according to your requirements.
            </p>

            <a href="booking.php" class="booking-btn">
                + Book a Service
            </a>

        </div>


        <!-- SUMMARY CARDS -->

        <div class="cards">

            <div class="card">

                <h3>
                    Total Bookings
                </h3>

                <div class="card-number">
                    <?php echo $total_bookings; ?>
                </div>

            </div>


            <div class="card">

                <h3>
                    Pending
                </h3>

                <div class="card-number">
                    <?php echo $pending_bookings; ?>
                </div>

            </div>


            <div class="card">

                <h3>
                    Completed
                </h3>

                <div class="card-number">
                    <?php echo $completed_bookings; ?>
                </div>

            </div>

        </div>


        <!-- MY BOOKINGS -->

        <h2 class="section-title">
            My Bookings
        </h2>


        <div class="booking-area">

            <?php if (mysqli_num_rows($result) > 0) { ?>

                <table class="booking-table">

                    <tr>

                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>

                    </tr>


                    <?php while ($booking = mysqli_fetch_assoc($result)) { ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($booking["service_name"] ?? "Service"); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($booking["booking_date"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($booking["booking_time"]); ?>
                            </td>

                            <td>

                                <span class="status">
                                    <?php echo htmlspecialchars($booking["status"]); ?>
                                </span>

                            </td>

                        </tr>

                    <?php } ?>

                </table>

            <?php } else { ?>

                <div class="no-booking">

                    <p>
                        You have no bookings yet.
                    </p>

                    <a href="booking.php" class="booking-btn">
                        Book Your First Service
                    </a>

                </div>

            <?php } ?>

        </div>

    </div>


</body>

</html>

<?php

mysqli_stmt_close($total_stmt);
mysqli_stmt_close($pending_stmt);
mysqli_stmt_close($completed_stmt);
mysqli_stmt_close($stmt);

?>