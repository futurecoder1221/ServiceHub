
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/db_connect.php";

/** @var mysqli $conn */

function booking_escape($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

$message = "";
$message_type = "";

$user_id = (int) $_SESSION["user_id"];

$name = "";
$phone = "";
$booking_date = "";
$booking_time = "";
$booking_message = "";

$selected_service = $_GET["service_id"] ?? "";

/* GET ACTIVE SERVICES */

$services = [];

$service_result = mysqli_query(
    $conn,
    "SELECT id, name FROM services
     WHERE status = 'Active'
     ORDER BY name ASC"
);

if ($service_result) {
    while ($row = mysqli_fetch_assoc($service_result)) {
        $services[] = $row;
    }
}

/* HANDLE BOOKING FORM */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $selected_service = $_POST["service_id"] ?? "";

    $booking_date = $_POST["booking_date"] ?? "";
    $booking_time = $_POST["booking_time"] ?? "";

    $name = trim($_POST["name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $booking_message = trim($_POST["message"] ?? "");

    $valid_service = false;

    foreach ($services as $service) {
        if (
            (string)$selected_service ===
            (string)$service["id"]
        ) {
            $valid_service = true;
            break;
        }
    }

    if (
        !$valid_service ||
        $booking_date === "" ||
        $booking_time === "" ||
        $name === "" ||
        $phone === ""
    ) {

        $message = "Please fill in all required fields and select a valid service.";
        $message_type = "error";

    } elseif (
        !DateTime::createFromFormat("!Y-m-d", $booking_date) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $booking_date)
    ) {

        $message = "Please select a valid booking date.";
        $message_type = "error";

    } elseif (
        !preg_match('/^\d{2}:\d{2}$/', $booking_time) ||
        !DateTime::createFromFormat("!H:i", $booking_time)
    ) {

        $message = "Please select a valid booking time.";
        $message_type = "error";

    } else {

        $service_id = (int)$selected_service;

        $sql = "INSERT INTO bookings
                (
                    user_id,
                    service_id,
                    booking_date,
                    booking_time,
                    message,
                    status,
                    name,
                    phone
                )
                VALUES (?, ?, ?, ?, ?, 'Pending', ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "iisssss",
                $user_id,
                $service_id,
                $booking_date,
                $booking_time,
                $booking_message,
                $name,
                $phone
            );

            try {

                if (mysqli_stmt_execute($stmt)) {

                    $message = "Your booking has been submitted successfully!";
                    $message_type = "success";

                    $name = "";
                    $phone = "";
                    $booking_date = "";
                    $booking_time = "";
                    $booking_message = "";
                    $selected_service = "";

                } else {

                    $message = "Booking failed. Please try again.";
                    $message_type = "error";
                }

            } catch (mysqli_sql_exception $ex) {

                $message = "Unable to save your booking. Please try again.";
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);

        } else {

            $message = "Unable to process booking. Please try again.";
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Book a Service | ServiceHub</title>

<style>

/* GENERAL */

* {
    box-sizing: border-box;
}

html,
body {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    color: #1e293b;
    overflow-x: hidden;
}

/* NAVBAR */

.navbar {
    width: 100%;
    background: #111827;
    padding: 17px 5%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.logo {
    font-size: 24px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

.logo span {
    color: #38bdf8;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 12px;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
    background: #334155;
    padding: 11px 16px;
    border-radius: 8px;
    text-align: center;
    transition: 0.2s;
}

.nav-links a:hover {
    background: #475569;
}

/* MAIN LAYOUT */

.page {
    width: 100%;
    max-width: 1150px;
    margin: 35px auto 55px;
    padding: 0 20px;
}

.page-heading {
    text-align: center;
    margin-bottom: 27px;
}

.page-heading h1 {
    font-size: 32px;
    color: #0f172a;
    margin: 0 0 10px;
}

.page-heading p {
    color: #64748b;
    font-size: 15px;
    line-height: 1.6;
    margin: 0;
}

/* BOOKING CONTAINER */

.booking-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(0, 0.9fr);
    gap: 25px;
    align-items: start;
}

.booking-card {
    min-width: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 6px 24px rgba(15,23,42,0.06);
}

.booking-card h2 {
    font-size: 22px;
    margin: 0 0 8px;
    color: #111827;
}

.form-description {
    font-size: 14px;
    color: #64748b;
    line-height: 1.6;
    margin: 0 0 25px;
}

/* FORM */

.form-group {
    min-width: 0;
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 9px;
    font-size: 14px;
    font-weight: bold;
    color: #334155;
}

.required {
    color: #dc2626;
}

.form-group input,
.form-group select,
.form-group textarea {
    display: block;
    width: 100%;
    min-width: 0;
    max-width: 100%;
    padding: 13px 14px;
    border: 1px solid #cbd5e1;
    border-radius: 9px;
    background: white;
    color: #1e293b;
    font-family: inherit;
    font-size: 15px;
    outline: none;
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2,132,199,0.10);
}

.form-group textarea {
    resize: vertical;
    min-height: 110px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.form-row .form-group {
    min-width: 0;
}

.helper-text {
    display: block;
    margin-top: 7px;
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
}

/* SUBMIT BUTTON */

.submit-btn {
    display: block;
    width: 100%;
    padding: 15px 18px;
    background: #0284c7;
    color: white;
    border: none;
    border-radius: 9px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.2s;
}

.submit-btn:hover {
    background: #0369a1;
}

/* ALERTS */

.alert {
    padding: 14px 16px;
    border-radius: 9px;
    margin-bottom: 22px;
    font-size: 14px;
    line-height: 1.6;
    font-weight: bold;
    overflow-wrap: anywhere;
}

.alert.success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* INFORMATION PANEL */

.info-card {
    min-width: 0;
    background: linear-gradient(145deg, #111827, #1e3a5f);
    border-radius: 16px;
    padding: 27px;
    color: white;
}

.info-card h3 {
    font-size: 21px;
    margin: 0 0 13px;
}

.info-card > p {
    font-size: 14px;
    color: #cbd5e1;
    line-height: 1.7;
    margin: 0 0 22px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 13px;
    padding: 15px 0;
    border-top: 1px solid rgba(255,255,255,0.13);
}

.info-number {
    width: 34px;
    height: 34px;
    min-width: 34px;
    border-radius: 50%;
    background: #38bdf8;
    color: #0f172a;
    font-size: 14px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
}

.info-item strong {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
}

.info-item p {
    color: #cbd5e1;
    margin: 0;
    font-size: 13px;
    line-height: 1.6;
}

.dashboard-link {
    display: block;
    text-decoration: none;
    text-align: center;
    margin-top: 23px;
    padding: 13px;
    border-radius: 8px;
    border: 1px solid #38bdf8;
    color: #e0f2fe;
    font-size: 14px;
    font-weight: bold;
}

.dashboard-link:hover {
    background: rgba(56,189,248,0.12);
}

/* TABLET */

@media (max-width: 850px) {

    .booking-layout {
        grid-template-columns: 1fr;
    }

    .info-card {
        padding: 24px;
    }

}

/* MOBILE */

@media (max-width: 600px) {

    .navbar {
        padding: 16px;
        flex-direction: column;
        align-items: stretch;
    }

    .logo {
        font-size: 22px;
        text-align: center;
    }

    .nav-links {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .nav-links a {
        font-size: 13px;
        padding: 12px 8px;
    }

    .page {
        margin-top: 25px;
        margin-bottom: 35px;
        padding: 0 14px;
    }

    .page-heading h1 {
        font-size: 25px;
    }

    .page-heading p {
        font-size: 14px;
    }

    .booking-layout {
        grid-template-columns: minmax(0, 1fr);
        gap: 18px;
    }

    .booking-card {
        padding: 21px 17px;
        border-radius: 12px;
    }

    .booking-card h2 {
        font-size: 20px;
    }

    .form-row {
        grid-template-columns: minmax(0, 1fr);
        gap: 0;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 16px;
        padding: 13px 12px;
    }

    .info-card {
        padding: 22px 18px;
        border-radius: 12px;
    }

    .info-card h3 {
        font-size: 20px;
    }

}

</style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar">

    <a href="../index.php" class="logo">
        Service<span>Hub</span>
    </a>

    <div class="nav-links">

        <a href="dashboard.php">
            My Dashboard
        </a>

        <a href="../services.php">
            All Services
        </a>

    </div>

</nav>

<!-- PAGE -->

<main class="page">

    <header class="page-heading">

        <h1>Book a Service</h1>

        <p>
            Choose your preferred service and submit
            your booking request in a few simple steps.
        </p>

    </header>

    <div class="booking-layout">

        <!-- BOOKING FORM -->

        <section class="booking-card">

            <h2>Booking Details</h2>

            <p class="form-description">
                Please complete the form below.
                Fields marked with * are required.
            </p>

            <?php if ($message !== ""): ?>

                <div class="alert <?php echo booking_escape($message_type); ?>">
                    <?php echo booking_escape($message); ?>
                </div>

            <?php endif; ?>

            <form method="POST" action="booking.php">

                <!-- SERVICE -->

                <div class="form-group">

                    <label for="service_id">
                        Select Service
                        <span class="required">*</span>
                    </label>

                    <select
                        name="service_id"
                        id="service_id"
                        required
                    >

                        <option value="">
                            Choose a service
                        </option>

                        <?php foreach ($services as $service): ?>

                            <option
                                value="<?php echo (int)$service["id"]; ?>"
                                <?php
                                if (
                                    (string)$selected_service ===
                                    (string)$service["id"]
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                <?php echo booking_escape($service["name"]); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- NAME AND PHONE -->

                <div class="form-row">

                    <div class="form-group">

                        <label for="name">
                            Full Name
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Enter your full name"
                            value="<?php echo booking_escape($name); ?>"
                            maxlength="100"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="phone">
                            Phone Number
                            <span class="required">*</span>
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            placeholder="Enter your phone number"
                            value="<?php echo booking_escape($phone); ?>"
                            maxlength="25"
                            required
                        >

                    </div>

                </div>

                <!-- DATE AND TIME -->

                <div class="form-row">

                    <div class="form-group">

                        <label for="booking_date">
                            Booking Date
                            <span class="required">*</span>
                        </label>

                        <input
                            type="date"
                            id="booking_date"
                            name="booking_date"
                            value="<?php echo booking_escape($booking_date); ?>"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="booking_time">
                            Booking Time
                            <span class="required">*</span>
                        </label>

                        <input
                            type="time"
                            id="booking_time"
                            name="booking_time"
                            value="<?php echo booking_escape($booking_time); ?>"
                            required
                        >

                    </div>

                </div>

                <!-- MESSAGE -->

                <div class="form-group">

                    <label for="message">
                        Additional Message
                        <span style="font-weight:normal;color:#64748b;">
                            (Optional)
                        </span>
                    </label>

                    <textarea
                        name="message"
                        id="message"
                        rows="4"
                        placeholder="Describe your requirements or add any important details..."
                    ><?php echo booking_escape($booking_message); ?></textarea>

                    <span class="helper-text">
                        Share any specific requirements for your service.
                    </span>

                </div>

                <!-- SUBMIT -->

                <button
                    type="submit"
                    class="submit-btn"
                >
                    Submit Booking Request
                </button>

            </form>

        </section>

        <!-- INFORMATION PANEL -->

        <aside class="info-card">

            <h3>How Booking Works</h3>

            <p>
                Follow these simple steps to request
                a service through ServiceHub.
            </p>

            <div class="info-item">

                <div class="info-number">1</div>

                <div>
                    <strong>Choose a Service</strong>
                    <p>
                        Select the service you need
                        from the available options.
                    </p>
                </div>

            </div>

            <div class="info-item">

                <div class="info-number">2</div>

                <div>
                    <strong>Submit Your Request</strong>
                    <p>
                        Enter your details and preferred
                        booking date and time.
                    </p>
                </div>

            </div>

            <div class="info-item">

                <div class="info-number">3</div>

                <div>
                    <strong>Track Your Booking</strong>
                    <p>
                        View your booking status
                        from your user dashboard.
                    </p>
                </div>

            </div>

            <a href="dashboard.php" class="dashboard-link">
                View My Bookings
            </a>

        </aside>

    </div>

</main>

</body>

</html>


