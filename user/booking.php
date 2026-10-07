```php
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/db_connect.php";

/** @var mysqli $conn */

$message = "";
$message_type = "";

$user_id = $_SESSION["user_id"];

$name = "";
$phone = "";
$booking_date = "";
$booking_time = "";
$booking_message = "";

/*
   Get service selected from Services page
   Example:
   booking.php?service_id=1
*/
$selected_service = $_GET["service_id"] ?? "";


/* Handle booking form */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $service_id = $_POST["service_id"] ?? "";
    $booking_date = $_POST["booking_date"] ?? "";
    $booking_time = $_POST["booking_time"] ?? "";
    $name = trim($_POST["name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $booking_message = trim($_POST["message"] ?? "");

    $selected_service = $service_id;


    /* Validation */

    if (
        empty($service_id) ||
        empty($booking_date) ||
        empty($booking_time) ||
        empty($name) ||
        empty($phone)
    ) {

        $message = "Please fill in all required fields.";
        $message_type = "error";

    } else {

        $sql = "INSERT INTO bookings
                (user_id, service_id, booking_date, booking_time, message, status, name, phone)
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

            if (mysqli_stmt_execute($stmt)) {

                $message = "Booking submitted successfully!";
                $message_type = "success";

                /* Clear form after successful booking */

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

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Book a Service - ServiceHub</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        .container {
            width: 500px;
            max-width: 90%;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #222;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #444;
        }

        .success {
            color: green;
            background-color: #eaf7ea;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }

        .error {
            color: #b00020;
            background-color: #fdeaea;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Book a Service</h1>


    <?php if ($message != "") { ?>

        <p class="<?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </p>

    <?php } ?>


    <form method="POST">


        <!-- Service -->

        <label>Service</label>

        <select name="service_id" required>

            <option value="">
                Select Service
            </option>


            <?php

            $result = mysqli_query(
                $conn,
                "SELECT id, name FROM services WHERE status = 'Active'"
            );

            if ($result) {

                while ($row = mysqli_fetch_assoc($result)) {

            ?>

                    <option
                        value="<?php echo $row["id"]; ?>"
                        <?php
                        if ((string)$selected_service === (string)$row["id"]) {
                            echo "selected";
                        }
                        ?>
                    >

                        <?php echo htmlspecialchars($row["name"]); ?>

                    </option>

            <?php

                }

            }

            ?>

        </select>


        <!-- Name -->

        <label>Name</label>

        <input
            type="text"
            name="name"
            value="<?php echo htmlspecialchars($name); ?>"
            required
        >


        <!-- Phone -->

        <label>Phone</label>

        <input
            type="text"
            name="phone"
            value="<?php echo htmlspecialchars($phone); ?>"
            required
        >


        <!-- Booking Date -->

        <label>Booking Date</label>

        <input
            type="date"
            name="booking_date"
            value="<?php echo htmlspecialchars($booking_date); ?>"
            required
        >


        <!-- Booking Time -->

        <label>Booking Time</label>

        <input
            type="time"
            name="booking_time"
            value="<?php echo htmlspecialchars($booking_time); ?>"
            required
        >


        <!-- Message -->

        <label>Message</label>

        <textarea
            name="message"
            rows="4"
        ><?php echo htmlspecialchars($booking_message); ?></textarea>


        <!-- Submit -->

        <button type="submit">
            Submit Booking
        </button>


    </form>

</div>

</body>

</html>
```

