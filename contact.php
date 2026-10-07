```php
<?php

session_start();



if (!isset($_SESSION["user_id"])) {
    header("Location: user/login.php?redirect=../services.php");
    exit();
}

require_once "includes/db_connect.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "includes/db_connect.php";

/** @var mysqli $conn */

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $subject = trim($_POST["subject"]);
    $contact_message = trim($_POST["message"]);

    if ($name == "" || $email == "" || $subject == "" || $contact_message == "") {

        $message = "Please fill in all required fields.";
        $message_type = "error";

    } else {

        $sql = "INSERT INTO contacts (name, email, phone, subject, message)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $name,
                $email,
                $phone,
                $subject,
                $contact_message
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Your message has been sent successfully!";
                $message_type = "success";

            } else {

                $message = "Message could not be sent. Please try again.";
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);

        } else {

            $message = "Something went wrong. Please try again.";
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

    <title>Contact Us | ServiceHub</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f8f9fc;
            color: #222;
        }

        /* Navbar */

        nav {
            background: #111827;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 28px;
            font-weight: bold;
        }

        .logo span {
            color: #38bdf8;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 28px;
            align-items: center;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        nav ul li a:hover {
            color: #38bdf8;
        }

        .login-btn {
            border: 1px solid #38bdf8;
            padding: 9px 18px;
            border-radius: 6px;
        }

        /* Page Header */

        .page-header {
            background: linear-gradient(135deg, #eef7ff, #ffffff);
            text-align: center;
            padding: 70px 20px;
        }

        .page-header h1 {
            font-size: 45px;
            margin-bottom: 15px;
        }

        .page-header h1 span {
            color: #0284c7;
        }

        .page-header p {
            color: #666;
            font-size: 18px;
        }

        /* Contact Section */

        .contact-section {
            padding: 70px 7%;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 35px;
        }

        /* Contact Info */

        .contact-info {
            background: #111827;
            color: white;
            padding: 40px;
            border-radius: 12px;
        }

        .contact-info h2 {
            font-size: 30px;
            margin-bottom: 15px;
        }

        .contact-info > p {
            color: #d1d5db;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .info-item {
            margin-bottom: 25px;
        }

        .info-item h3 {
            margin-bottom: 7px;
            color: #38bdf8;
        }

        .info-item p {
            color: #d1d5db;
        }

        /* Contact Form */

        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .contact-form h2 {
            font-size: 30px;
            margin-bottom: 25px;
        }

        .form-message {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 13px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #0284c7;
        }

        .form-group textarea {
            height: 140px;
            resize: vertical;
        }

        .submit-btn {
            background: #0284c7;
            color: white;
            border: none;
            padding: 13px 25px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: #0369a1;
        }

        /* Footer */

        footer {
            background: #111827;
            color: white;
            text-align: center;
            padding: 25px;
        }

        /* Responsive */

        @media (max-width: 800px) {

            nav {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }

            .contact-container {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 36px;
            }

        }

    </style>

</head>

<body>


    <!-- Navbar -->

    <nav>

        <div class="logo">
            Service<span>Hub</span>
        </div>

        <ul>

            <li>
                <a href="index.php">Home</a>
            </li>

            <li>
                <a href="services.php">Services</a>
            </li>

            <li>
                <a href="about.php">About</a>
            </li>

            <li>
                <a href="index.php#how-it-works">How It Works</a>
            </li>

            <li>
                <a href="contact.php">Contact</a>
            </li>


            <?php if (isset($_SESSION["user_id"])) { ?>

                <li>
                    <a href="user/dashboard.php">
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="includes/logout.php" class="login-btn">
                        Logout
                    </a>
                </li>

            <?php } else { ?>

                <li>
                    <a href="user/login.php" class="login-btn">
                        Login
                    </a>
                </li>

                <li>
                    <a href="user/register.php">
                        Register
                    </a>
                </li>

            <?php } ?>

        </ul>

    </nav>


    <!-- Page Header -->

    <section class="page-header">

        <h1>
            Contact <span>Us</span>
        </h1>

        <p>
            Have a question or need help? Send us a message.
        </p>

    </section>


    <!-- Contact Section -->

    <section class="contact-section">

        <div class="contact-container">


            <!-- Contact Information -->

            <div class="contact-info">

                <h2>Get In Touch</h2>

                <p>
                    We are here to help. Contact ServiceHub for questions,
                    service information or support.
                </p>

                <div class="info-item">

                    <h3>📍 Address</h3>

                    <p>
                        ServiceHub Office, Lahore, Pakistan
                    </p>

                </div>


                <div class="info-item">

                    <h3>📧 Email</h3>

                    <p>
                        support@servicehub.com
                    </p>

                </div>


                <div class="info-item">

                    <h3>📞 Phone</h3>

                    <p>
                        +92 300 1234567
                    </p>

                </div>


                <div class="info-item">

                    <h3>🕐 Working Hours</h3>

                    <p>
                        Monday - Friday | 9:00 AM - 6:00 PM
                    </p>

                </div>

            </div>


            <!-- Contact Form -->

            <div class="contact-form">

                <h2>Send a Message</h2>


                <?php if ($message != "") { ?>

                    <div class="form-message <?php echo $message_type; ?>">

                        <?php echo htmlspecialchars($message); ?>

                    </div>

                <?php } ?>


                <form action="contact.php" method="POST">

                    <div class="form-group">

                        <label for="name">
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Enter your name"
                            value="<?php echo isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : ""; ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="email">
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                            value="<?php echo isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : ""; ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="phone">
                            Phone Number
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            placeholder="Enter your phone number"
                            value="<?php echo isset($_POST["phone"]) ? htmlspecialchars($_POST["phone"]) : ""; ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label for="subject">
                            Subject
                        </label>

                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            placeholder="Enter subject"
                            value="<?php echo isset($_POST["subject"]) ? htmlspecialchars($_POST["subject"]) : ""; ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="message">
                            Message
                        </label>

                        <textarea
                            id="message"
                            name="message"
                            placeholder="Write your message..."
                            required
                        ><?php echo isset($_POST["message"]) ? htmlspecialchars($_POST["message"]) : ""; ?></textarea>

                    </div>


                    <button type="submit" class="submit-btn">
                        Send Message
                    </button>

                </form>

            </div>

        </div>

    </section>


    <!-- Footer -->

    <footer>

        <p>
            © 2026 ServiceHub. All Rights Reserved.
        </p>

    </footer>


</body>

</html>
```

