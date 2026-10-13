
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

require_once "../includes/db_connect.php";

/** @var mysqli $conn */

function booking_e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

function booking_status_class($status) {
    switch (strtolower((string)$status)) {
        case "confirmed":
            return "confirmed";
        case "completed":
            return "completed";
        case "cancelled":
            return "cancelled";
        default:
            return "pending";
    }
}

$allowed_statuses = [
    "Pending",
    "Confirmed",
    "Completed",
    "Cancelled"
];

if (empty($_SESSION["bookings_csrf"])) {
    $_SESSION["bookings_csrf"] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION["bookings_csrf"];
$message = "";
$message_type = "success";

/* UPDATE BOOKING STATUS */

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["update_status"])
) {

    $submitted_token = $_POST["csrf_token"] ?? "";

    $booking_id = filter_var(
        $_POST["booking_id"] ?? null,
        FILTER_VALIDATE_INT
    );

    $status = $_POST["status"] ?? "";

    if (
        !is_string($submitted_token) ||
        !hash_equals($csrf_token, $submitted_token)
    ) {
        $message = "Invalid request. Refresh the page and try again.";
        $message_type = "error";

    } elseif (
        !$booking_id ||
        $booking_id < 1 ||
        !in_array($status, $allowed_statuses, true)
    ) {
        $message = "Invalid booking or status.";
        $message_type = "error";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE bookings SET status = ? WHERE id = ?"
        );

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $status,
                $booking_id
            );

            try {

                if (mysqli_stmt_execute($stmt)) {

                    $_SESSION["bookings_flash"] =
                        "Booking status updated successfully.";

                    mysqli_stmt_close($stmt);

                    header("Location: bookings.php");
                    exit();

                } else {
                    $message = "Could not update the booking.";
                    $message_type = "error";
                }

            } catch (mysqli_sql_exception $ex) {
                $message = "Database error while updating booking.";
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);

        } else {
            $message = "Database query error.";
            $message_type = "error";
        }
    }
}

/* SUCCESS MESSAGE */

if (isset($_SESSION["bookings_flash"])) {
    $message = $_SESSION["bookings_flash"];
    unset($_SESSION["bookings_flash"]);
}

/* FETCH ALL BOOKINGS */

$bookings = [];

$sql = "SELECT
            bookings.*,
            services.name AS service_name
        FROM bookings
        LEFT JOIN services
            ON bookings.service_id = services.id
        ORDER BY bookings.id DESC";

$result = mysqli_query($conn, $sql);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }

} else {
    $message = "Could not load bookings.";
    $message_type = "error";
}

/* BOOKING COUNTS */

$counts = [
    "Pending" => 0,
    "Confirmed" => 0,
    "Completed" => 0,
    "Cancelled" => 0
];

foreach ($bookings as $booking) {

    foreach ($allowed_statuses as $option) {

        if (
            strcasecmp(
                trim((string)($booking["status"] ?? "")),
                $option
            ) === 0
        ) {
            $counts[$option]++;
            break;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Bookings | ServiceHub</title>

<style>

* {
    box-sizing: border-box;
}

html,
body {
    margin: 0;
    max-width: 100%;
    font-family: Arial, sans-serif;
}

body {
    background: #f1f5f9;
    color: #1e293b;
    overflow-x: hidden;
}

/* NAVBAR */

.navbar {
    background: #111827;
    padding: 18px 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.brand {
    color: white;
    text-decoration: none;
    font-size: 23px;
    font-weight: bold;
}

.brand span {
    color: #38bdf8;
}

.nav-actions {
    display: flex;
    gap: 10px;
}

.nav-link {
    color: white;
    background: #334155;
    padding: 12px 16px;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    text-align: center;
}

.logout {
    background: #dc2626;
}

/* MAIN CONTENT */

.container {
    width: 94%;
    max-width: 1320px;
    margin: 30px auto 55px;
}

.hero {
    background: linear-gradient(135deg, #111827, #1e3a5f);
    color: white;
    padding: 30px;
    border-radius: 16px;
    margin-bottom: 24px;
}

.hero h1 {
    font-size: 29px;
    margin: 0 0 10px;
}

.hero p {
    color: #cbd5e1;
    line-height: 1.6;
    margin: 0;
}

/* SUMMARY CARDS */

.stats {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 15px;
    margin-bottom: 24px;
}

.stat {
    min-width: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 13px;
    padding: 20px;
    box-shadow: 0 3px 12px rgba(15,23,42,0.04);
}

.stat p {
    margin: 0 0 12px;
    color: #64748b;
    font-size: 14px;
    font-weight: bold;
}

.stat strong {
    font-size: 28px;
    color: #0f172a;
}

/* BOOKINGS PANEL */

.panel {
    min-width: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 15px;
    padding: 24px;
    box-shadow: 0 3px 12px rgba(15,23,42,0.04);
}

.panel h2 {
    font-size: 23px;
    margin: 0 0 20px;
}

/* MESSAGES */

.message {
    border-radius: 9px;
    padding: 14px 16px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: bold;
}

.message.success {
    background: #dcfce7;
    color: #166534;
}

.message.error {
    background: #fee2e2;
    color: #991b1b;
}

/* DESKTOP TABLE */

.table-wrap {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
}

.desktop-table {
    width: 100%;
    min-width: 1000px;
    border-collapse: collapse;
}

.desktop-table th {
    background: #111827;
    color: white;
    text-align: left;
    font-size: 13px;
    padding: 14px;
    white-space: nowrap;
}

.desktop-table td {
    font-size: 14px;
    line-height: 1.5;
    padding: 14px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: top;
    overflow-wrap: anywhere;
}

.desktop-table tbody tr:nth-child(even) {
    background: #f8fafc;
}

.desktop-table td:nth-child(3) {
    min-width: 145px;
}

.desktop-table td:nth-child(8) {
    min-width: 135px;
    max-width: 220px;
}

/* STATUS COLORS */

.status-badge {
    display: inline-block;
    padding: 7px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 10px;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.confirmed {
    background: #dbeafe;
    color: #1d4ed8;
}

.status-badge.completed {
    background: #dcfce7;
    color: #166534;
}

.status-badge.cancelled {
    background: #fee2e2;
    color: #b91c1c;
}

/* STATUS UPDATE FORM */

.status-form {
    display: flex;
    flex-direction: column;
    gap: 9px;
    min-width: 135px;
}

.status-form select,
.status-form button {
    width: 100%;
    border-radius: 8px;
    font-size: 14px;
    padding: 11px;
    font-family: inherit;
}

.status-form select {
    border: 1px solid #cbd5e1;
    background: white;
    color: #1e293b;
}

.status-form button {
    background: #0284c7;
    border: none;
    color: white;
    font-weight: bold;
    cursor: pointer;
}

.status-form button:hover {
    background: #0369a1;
}

/* MOBILE TABLES HIDDEN ON DESKTOP */

.mobile-list {
    display: none;
}

.empty {
    text-align: center;
    padding: 25px;
    color: #64748b;
}

/* TABLET */

@media (max-width: 1000px) {

    .stats {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

}

/* MOBILE */

@media (max-width: 768px) {

    .container {
        margin-top: 20px;
    }

    .hero {
        padding: 23px 19px;
    }

    .hero h1 {
        font-size: 24px;
    }

    .stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .stat {
        padding: 17px;
    }

    .stat strong {
        font-size: 25px;
    }

    .panel {
        padding: 16px;
    }

    .panel h2 {
        font-size: 21px;
    }

    /* Hide desktop table */

    .table-wrap {
        display: none;
    }

    /* Show mobile booking tables */

    .mobile-list {
        display: block;
    }

    .booking-block {
        border: 1px solid #dbe3ee;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 18px;
        background: white;
    }

    .booking-block:last-child {
        margin-bottom: 0;
    }

    .booking-heading {
        background: #111827;
        color: white;
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        font-weight: bold;
    }

    .booking-heading .status-badge {
        margin: 0;
    }

    /* Two-column booking details */

    .detail-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .detail-table th,
    .detail-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: top;
        font-size: 14px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .detail-table th {
        width: 34%;
        background: #f8fafc;
        color: #475569;
        text-align: left;
        font-weight: bold;
        white-space: normal;
    }

    .detail-table td {
        width: 66%;
        background: white;
        color: #1e293b;
    }

    .detail-table tr:last-child th,
    .detail-table tr:last-child td {
        border-bottom: none;
    }

    .detail-table .status-form {
        min-width: 0;
        width: 100%;
    }

    .detail-table .status-form select,
    .detail-table .status-form button {
        font-size: 15px;
        padding: 12px 7px;
    }

}

/* SMALL PHONES */

@media (max-width: 480px) {

    .navbar {
        flex-direction: column;
        align-items: stretch;
        padding: 16px 5%;
    }

    .brand {
        text-align: center;
        font-size: 22px;
    }

    .nav-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .nav-link {
        font-size: 13px;
        padding: 13px 9px;
    }

    .stat p {
        font-size: 12px;
    }

    .stat strong {
        font-size: 24px;
    }

    .booking-heading {
        font-size: 14px;
    }

    .detail-table th,
    .detail-table td {
        font-size: 13px;
        padding: 11px 9px;
    }

}

</style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar">

    <a href="dashboard.php" class="brand">
        Service<span>Hub</span> Admin
    </a>

    <div class="nav-actions">

        <a class="nav-link" href="dashboard.php">
            Dashboard
        </a>

        <a class="nav-link logout" href="logout.php">
            Logout
        </a>

    </div>

</nav>

<main class="container">

    <!-- HEADING -->

    <header class="hero">

        <h1>Manage Bookings</h1>

        <p>
            View customer bookings and update their status.
        </p>

    </header>

    <!-- MESSAGE -->

    <?php if ($message !== ""): ?>

        <div class="message <?php echo booking_e($message_type); ?>">
            <?php echo booking_e($message); ?>
        </div>

    <?php endif; ?>

    <!-- SUMMARY -->

    <section class="stats">

        <div class="stat">
            <p>Total Bookings</p>
            <strong><?php echo count($bookings); ?></strong>
        </div>

        <?php foreach ($allowed_statuses as $option): ?>

            <div class="stat">

                <p><?php echo booking_e($option); ?></p>

                <strong>
                    <?php echo $counts[$option]; ?>
                </strong>

            </div>

        <?php endforeach; ?>

    </section>

    <!-- BOOKINGS -->

    <section class="panel">

        <h2>Customer Bookings</h2>

        <?php if (count($bookings) === 0): ?>

            <p class="empty">No bookings found.</p>

        <?php else: ?>

            <!-- DESKTOP 9-COLUMN TABLE -->

            <div class="table-wrap">

                <table class="desktop-table">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Service</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Message</th>
                            <th>Status / Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($bookings as $booking): ?>

                            <?php
                                $id = (int)$booking["id"];
                                $current = $booking["status"] ?? "Pending";
                            ?>

                            <tr>

                                <td><?php echo $id; ?></td>

                                <td>
                                    <?php echo booking_e($booking["user_id"]); ?>
                                </td>

                                <td>
                                    <?php echo booking_e($booking["service_name"] ?: "Service unavailable"); ?>
                                </td>

                                <td>
                                    <?php echo booking_e($booking["name"]); ?>
                                </td>

                                <td>
                                    <?php echo booking_e($booking["phone"]); ?>
                                </td>

                                <td>
                                    <?php echo booking_e($booking["booking_date"]); ?>
                                </td>

                                <td>
                                    <?php echo booking_e($booking["booking_time"]); ?>
                                </td>

                                <td>
                                    <?php echo booking_e($booking["message"]); ?>
                                </td>

                                <td>

                                    <span class="status-badge <?php echo booking_status_class($current); ?>">
                                        <?php echo booking_e($current); ?>
                                    </span>

                                    <form
                                        method="POST"
                                        action="bookings.php"
                                        class="status-form"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?php echo booking_e($csrf_token); ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="booking_id"
                                            value="<?php echo $id; ?>"
                                        >

                                        <select
                                            name="status"
                                            aria-label="Status for booking <?php echo $id; ?>"
                                        >

                                            <?php foreach ($allowed_statuses as $option): ?>

                                                <option
                                                    value="<?php echo booking_e($option); ?>"
                                                    <?php echo strcasecmp((string)$current, $option) === 0 ? "selected" : ""; ?>
                                                >
                                                    <?php echo booking_e($option); ?>
                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                        <button
                                            type="submit"
                                            name="update_status"
                                            value="1"
                                        >
                                            Update
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- MOBILE BOOKING TABLES -->

            <div class="mobile-list">

                <?php foreach ($bookings as $booking): ?>

                    <?php
                        $id = (int)$booking["id"];
                        $current = $booking["status"] ?? "Pending";
                    ?>

                    <article class="booking-block">

                        <div class="booking-heading">

                            <span>
                                Booking #<?php echo $id; ?>
                            </span>

                            <span class="status-badge <?php echo booking_status_class($current); ?>">
                                <?php echo booking_e($current); ?>
                            </span>

                        </div>

                        <table class="detail-table">

                            <tbody>

                                <tr>
                                    <th scope="row">ID</th>
                                    <td><?php echo $id; ?></td>
                                </tr>

                                <tr>
                                    <th scope="row">User ID</th>
                                    <td><?php echo booking_e($booking["user_id"]); ?></td>
                                </tr>

                                <tr>
                                    <th scope="row">Service</th>
                                    <td><?php echo booking_e($booking["service_name"] ?: "Service unavailable"); ?></td>
                                </tr>

                                <tr>
                                    <th scope="row">Name</th>
                                    <td><?php echo booking_e($booking["name"]); ?></td>
                                </tr>

                                <tr>
                                    <th scope="row">Phone</th>
                                    <td><?php echo booking_e($booking["phone"]); ?></td>
                                </tr>

                                <tr>
                                    <th scope="row">Date</th>
                                    <td><?php echo booking_e($booking["booking_date"]); ?></td>
                                </tr>

                                <tr>
                                    <th scope="row">Time</th>
                                    <td><?php echo booking_e($booking["booking_time"]); ?></td>
                                </tr>

                                <tr>
                                    <th scope="row">Message</th>
                                    <td>
                                        <?php echo booking_e($booking["message"] ?: "No message"); ?>
                                    </td>
                                </tr>

                                <tr>

                                    <th scope="row">Status / Action</th>

                                    <td>

                                        <span class="status-badge <?php echo booking_status_class($current); ?>">
                                            <?php echo booking_e($current); ?>
                                        </span>

                                        <form
                                            method="POST"
                                            action="bookings.php"
                                            class="status-form"
                                        >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php echo booking_e($csrf_token); ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="booking_id"
                                                value="<?php echo $id; ?>"
                                            >

                                            <select
                                                name="status"
                                                aria-label="Status for booking <?php echo $id; ?>"
                                            >

                                                <?php foreach ($allowed_statuses as $option): ?>

                                                    <option
                                                        value="<?php echo booking_e($option); ?>"
                                                        <?php echo strcasecmp((string)$current, $option) === 0 ? "selected" : ""; ?>
                                                    >
                                                        <?php echo booking_e($option); ?>
                                                    </option>

                                                <?php endforeach; ?>

                                            </select>

                                            <button
                                                type="submit"
                                                name="update_status"
                                                value="1"
                                            >
                                                Update Status
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

</body>
</html>
