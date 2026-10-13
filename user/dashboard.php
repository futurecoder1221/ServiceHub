
<?php

require_once __DIR__ . "/../includes/check_login.php";
require_once __DIR__ . "/../includes/db_connect.php";

/** @var mysqli $conn */

function dashboard_e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

$user_id = (int) $_SESSION["user_id"];
$user_name = $_SESSION["user_name"] ?? "User";

/* GET USER BOOKINGS */

$bookings = [];

$sql = "SELECT
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

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $bookings[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
}

/* BOOKING COUNTS */

$total_bookings = count($bookings);
$pending_bookings = 0;
$completed_bookings = 0;

foreach ($bookings as $booking) {

    $status = strtolower(
        trim((string)($booking["status"] ?? ""))
    );

    if ($status === "pending") {
        $pending_bookings++;
    }

    if ($status === "completed") {
        $completed_bookings++;
    }
}

/* STATUS CSS CLASS */

function dashboard_status_class($status) {

    $status = strtolower(trim((string)$status));

    $allowed = [
        "pending",
        "confirmed",
        "completed",
        "cancelled"
    ];

    return in_array($status, $allowed, true)
        ? "status-" . $status
        : "";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>User Dashboard | ServiceHub</title>

<style>

/* GENERAL */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html,
body {
    width: 100%;
    max-width: 100%;
}

body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    color: #1e293b;
    min-height: 100vh;
    overflow-x: hidden;
}

/* MAIN */

.container {
    width: 90%;
    max-width: 1150px;
    margin: 35px auto 60px;
}

/* WELCOME */

.welcome {
    background: linear-gradient(120deg, #111827, #334155);
    color: white;
    padding: 38px;
    border-radius: 15px;
    margin-bottom: 30px;
}

.welcome h1 {
    font-size: 30px;
    margin-bottom: 12px;
    overflow-wrap: anywhere;
}

.welcome p {
    font-size: 15px;
    line-height: 1.7;
    color: #cbd5e1;
    margin-bottom: 8px;
}

.booking-btn {
    display: inline-block;
    margin-top: 18px;
    background: #0284c7;
    color: white;
    text-decoration: none;
    font-weight: bold;
    font-size: 15px;
    padding: 14px 24px;
    border-radius: 8px;
    text-align: center;
}

.booking-btn:hover {
    background: #0369a1;
}

/* SECTION HEADINGS */

.section-heading {
    margin-bottom: 20px;
}

.section-heading h2 {
    font-size: 24px;
    margin-bottom: 8px;
}

.section-heading p {
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
}

/* SUMMARY CARDS */

.cards {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
    margin-bottom: 35px;
}

.card {
    background: white;
    padding: 27px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    min-width: 0;
}

.card-icon {
    font-size: 27px;
    margin-bottom: 15px;
}

.card h3 {
    font-size: 16px;
    color: #64748b;
    margin-bottom: 12px;
}

.card-number {
    font-size: 34px;
    font-weight: bold;
    color: #0f172a;
}

/* BOOKINGS AREA */

.booking-area {
    background: white;
    padding: 22px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

/* DESKTOP TABLE */

.booking-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.booking-table th {
    background: #111827;
    color: white;
    padding: 15px;
    font-size: 14px;
}

.booking-table td {
    padding: 15px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
    overflow-wrap: anywhere;
}

.booking-table tbody tr:hover {
    background: #f8fafc;
}

.booking-table tbody tr:last-child td {
    border-bottom: none;
}

/* STATUS BADGES */

.status {
    display: inline-block;
    padding: 7px 13px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    background: #e2e8f0;
    color: #334155;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-confirmed {
    background: #dbeafe;
    color: #1d4ed8;
}

.status-completed {
    background: #dcfce7;
    color: #166534;
}

.status-cancelled {
    background: #fee2e2;
    color: #991b1b;
}

/* MOBILE BOOKING CARDS */

.mobile-bookings {
    display: none;
}

.mobile-booking-card {
    background: white;
    border: 1px solid #dbe3ee;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 16px;
}

.mobile-booking-card:last-child {
    margin-bottom: 0;
}

.mobile-booking-header {
    background: #111827;
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.mobile-booking-header strong {
    font-size: 15px;
}

.mobile-booking-header .status {
    flex-shrink: 0;
}

.mobile-booking-details {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.mobile-booking-details th,
.mobile-booking-details td {
    padding: 13px 12px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
    text-align: left;
    vertical-align: top;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.mobile-booking-details th {
    width: 34%;
    background: #f8fafc;
    color: #475569;
}

.mobile-booking-details td {
    width: 66%;
    color: #1e293b;
}

.mobile-booking-details tr:last-child th,
.mobile-booking-details tr:last-child td {
    border-bottom: none;
}

/* EMPTY STATE */

.no-booking {
    text-align: center;
    padding: 30px 15px;
    color: #64748b;
}

.no-booking p {
    margin-bottom: 15px;
}

/* TABLET */

@media (max-width: 900px) {

    .cards {
        gap: 14px;
    }
}

/* MOBILE */

@media (max-width: 600px) {

    .container {
        width: 92%;
        margin: 24px auto 45px;
    }

    .welcome {
        padding: 28px 22px;
        margin-bottom: 30px;
    }

    .welcome h1 {
        font-size: 24px;
    }

    .welcome p {
        font-size: 14px;
    }

    .booking-btn {
        display: block;
        width: 100%;
        padding: 15px;
    }

    .section-heading h2 {
        font-size: 22px;
    }

    .cards {
        grid-template-columns: 1fr;
        gap: 16px;
        margin-bottom: 30px;
    }

    .card {
        padding: 25px;
    }

    .booking-area {
        padding: 15px;
    }

    /* HIDE DESKTOP TABLE */

    .desktop-bookings {
        display: none;
    }

    /* SHOW MOBILE CARDS */

    .mobile-bookings {
        display: block;
    }
}

@media (max-width: 360px) {

    .mobile-booking-details th,
    .mobile-booking-details td {
        padding: 11px 9px;
        font-size: 13px;
    }
}

</style>

</head>

<body>

<!-- COMMON NAVBAR -->

<?php require __DIR__ . "/../includes/navbar.php"; ?>


<!-- MAIN CONTENT -->

<main class="container">

    <!-- WELCOME -->

    <section class="welcome">

        <h1>
            Welcome, <?php echo dashboard_e($user_name); ?>! 👋
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

    </section>


    <!-- SUMMARY HEADING -->

    <div class="section-heading">

        <h2>Booking Overview</h2>

        <p>
            Track your service requests and their progress.
        </p>

    </div>


    <!-- SUMMARY CARDS -->

    <section class="cards">

        <div class="card">

            <div class="card-icon">📋</div>

            <h3>Total Bookings</h3>

            <div class="card-number">
                <?php echo $total_bookings; ?>
            </div>

        </div>


        <div class="card">

            <div class="card-icon">⏳</div>

            <h3>Pending Bookings</h3>

            <div class="card-number">
                <?php echo $pending_bookings; ?>
            </div>

        </div>


        <div class="card">

            <div class="card-icon">✅</div>

            <h3>Completed Bookings</h3>

            <div class="card-number">
                <?php echo $completed_bookings; ?>
            </div>

        </div>

    </section>


    <!-- MY BOOKINGS -->

    <section>

        <div class="section-heading">

            <h2>My Bookings</h2>

            <p>
                Review your bookings and their current status.
            </p>

        </div>

        <div class="booking-area">

            <?php if ($total_bookings > 0): ?>

                <!-- DESKTOP TABLE -->

                <div class="desktop-bookings">

                    <table class="booking-table">

                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($bookings as $booking): ?>

                                <?php
                                    $status = $booking["status"] ?? "";
                                    $status_class = dashboard_status_class($status);
                                ?>

                                <tr>

                                    <td>
                                        <?php echo dashboard_e($booking["service_name"] ?: "Service unavailable"); ?>
                                    </td>

                                    <td>
                                        <?php echo dashboard_e($booking["booking_date"]); ?>
                                    </td>

                                    <td>
                                        <?php echo dashboard_e($booking["booking_time"]); ?>
                                    </td>

                                    <td>
                                        <span class="status <?php echo $status_class; ?>">
                                            <?php echo dashboard_e($status); ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <!-- MOBILE BOOKING CARDS -->

                <div class="mobile-bookings">

                    <?php foreach ($bookings as $booking): ?>

                        <?php
                            $status = $booking["status"] ?? "";
                            $status_class = dashboard_status_class($status);
                            $booking_id = (int)$booking["id"];
                        ?>

                        <article class="mobile-booking-card">

                            <div class="mobile-booking-header">

                                <strong>
                                    Booking #<?php echo $booking_id; ?>
                                </strong>

                                <span class="status <?php echo $status_class; ?>">
                                    <?php echo dashboard_e($status); ?>
                                </span>

                            </div>

                            <table class="mobile-booking-details">

                                <tbody>

                                    <tr>
                                        <th scope="row">Service</th>
                                        <td>
                                            <?php echo dashboard_e($booking["service_name"] ?: "Service unavailable"); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th scope="row">Date</th>
                                        <td>
                                            <?php echo dashboard_e($booking["booking_date"]); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th scope="row">Time</th>
                                        <td>
                                            <?php echo dashboard_e($booking["booking_time"]); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th scope="row">Status</th>
                                        <td>
                                            <span class="status <?php echo $status_class; ?>">
                                                <?php echo dashboard_e($status); ?>
                                            </span>
                                        </td>
                                    </tr>

                                </tbody>

                            </table>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="no-booking">

                    <p>You have no bookings yet.</p>

                    <a href="booking.php" class="booking-btn">
                        Book Your First Service
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

</body>
</html>
