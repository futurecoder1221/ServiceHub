
<?php

session_start();

require_once __DIR__ . "/includes/db_connect.php";

/** @var mysqli $conn */

$message = "";
$message_type = "";

$name = "";
$email = "";
$phone = "";
$subject = "";
$contact_message = "";

/* SAFE OUTPUT */

function contact_e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

/* CONTACT FORM SUBMISSION */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $contact_message = trim($_POST["message"] ?? "");

    if (
        $name === "" ||
        $email === "" ||
        $subject === "" ||
        $contact_message === ""
    ) {

        $message = "Please fill in all required fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (
        strlen($name) > 100 ||
        strlen($email) > 254 ||
        strlen($phone) > 30 ||
        strlen($subject) > 200
    ) {

        $message = "One or more fields are too long.";
        $message_type = "error";

    } else {

        $sql = "INSERT INTO contacts
                (name, email, phone, subject, message)
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

                /* CLEAR FORM AFTER SUCCESS */

                $name = "";
                $email = "";
                $phone = "";
                $subject = "";
                $contact_message = "";

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

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Contact Us | ServiceHub</title>

<style>

/* GENERAL */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

html,
body {
    width: 100%;
    max-width: 100%;
}

body {
    background: #f8f9fc;
    color: #222;
    min-height: 100vh;
    overflow-x: hidden;
}

/* PAGE HEADER */

.page-header {
    background: linear-gradient(135deg, #eef7ff, #ffffff);
    text-align: center;
    padding: 70px 20px;
}

.page-header h1 {
    font-size: 45px;
    margin-bottom: 15px;
    color: #111827;
}

.page-header h1 span {
    color: #0284c7;
}

.page-header p {
    color: #64748b;
    font-size: 18px;
    line-height: 1.7;
}

/* CONTACT SECTION */

.contact-section {
    padding: 70px 7%;
}

.contact-container {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.5fr);
    gap: 35px;
    align-items: start;
}

/* CONTACT INFO */

.contact-info {
    background: #111827;
    color: white;
    padding: 40px;
    border-radius: 12px;
    min-width: 0;
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

.info-item:last-child {
    margin-bottom: 0;
}

.info-item h3 {
    margin-bottom: 7px;
    color: #38bdf8;
    font-size: 16px;
}

.info-item p {
    color: #d1d5db;
    line-height: 1.7;
    overflow-wrap: anywhere;
}

/* CONTACT FORM */

.contact-form {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    min-width: 0;
}

.contact-form h2 {
    font-size: 30px;
    margin-bottom: 25px;
    color: #0f172a;
}

/* MESSAGES */

.form-message {
    padding: 14px 16px;
    border-radius: 8px;
    margin-bottom: 22px;
    font-weight: bold;
    font-size: 14px;
    line-height: 1.6;
    overflow-wrap: anywhere;
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

/* FORM FIELDS */

.form-group {
    margin-bottom: 19px;
}

.form-group label {
    display: block;
    margin-bottom: 9px;
    font-weight: bold;
    font-size: 14px;
    color: #334155;
}

.form-group input,
.form-group textarea {
    display: block;
    width: 100%;
    min-width: 0;
    padding: 14px 15px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 15px;
    color: #0f172a;
    background: white;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2,132,199,0.12);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #94a3b8;
}

.form-group textarea {
    height: 145px;
    resize: vertical;
    line-height: 1.6;
}

/* SUBMIT BUTTON */

.submit-btn {
    display: inline-block;
    background: #0284c7;
    color: white;
    border: none;
    padding: 14px 27px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.2s;
}

.submit-btn:hover {
    background: #0369a1;
}

.submit-btn:focus-visible {
    outline: 3px solid #38bdf8;
    outline-offset: 3px;
}

/* FOOTER */

footer {
    background: #111827;
    color: white;
    text-align: center;
    padding: 25px 15px;
    font-size: 14px;
}

/* TABLET */

@media (max-width: 900px) {

    .contact-section {
        padding: 50px 5%;
    }

    .contact-container {
        gap: 22px;
    }

    .contact-info,
    .contact-form {
        padding: 28px;
    }

}

/* MOBILE */

@media (max-width: 700px) {

    .page-header {
        padding: 48px 18px;
    }

    .page-header h1 {
        font-size: 35px;
    }

    .page-header p {
        font-size: 16px;
    }

    .contact-section {
        padding: 35px 16px 50px;
    }

    .contact-container {
        grid-template-columns: minmax(0, 1fr);
        gap: 22px;
    }

    .contact-info,
    .contact-form {
        padding: 27px 22px;
    }

    .contact-info h2,
    .contact-form h2 {
        font-size: 25px;
    }

    .form-group input,
    .form-group textarea {
        font-size: 16px;
    }

    .submit-btn {
        width: 100%;
        padding: 15px;
    }

}

@media (max-width: 360px) {

    .contact-info,
    .contact-form {
        padding: 24px 17px;
    }

    .page-header h1 {
        font-size: 31px;
    }

}

</style>

</head>

<body>

<!-- COMMON NAVBAR -->

<?php require __DIR__ . "/includes/navbar.php"; ?>


<!-- PAGE HEADER -->

<section class="page-header">

    <h1>
        Contact <span>Us</span>
    </h1>

    <p>
        Have a question or need help? Send us a message.
    </p>

</section>


<!-- CONTACT SECTION -->

<main class="contact-section">

    <div class="contact-container">

        <!-- CONTACT INFORMATION -->

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


        <!-- CONTACT FORM -->

        <div class="contact-form">

            <h2>Send a Message</h2>

            <?php if ($message !== ""): ?>

                <div
                    class="form-message <?php echo contact_e($message_type); ?>"
                    role="alert"
                >
                    <?php echo contact_e($message); ?>
                </div>

            <?php endif; ?>

            <form action="contact.php" method="POST">

                <!-- NAME -->

                <div class="form-group">

                    <label for="name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter your name"
                        value="<?php echo contact_e($name); ?>"
                        maxlength="100"
                        autocomplete="name"
                        required
                    >

                </div>

                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        value="<?php echo contact_e($email); ?>"
                        maxlength="254"
                        autocomplete="email"
                        required
                    >

                </div>

                <!-- PHONE -->

                <div class="form-group">

                    <label for="phone">
                        Phone Number
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="Enter your phone number"
                        value="<?php echo contact_e($phone); ?>"
                        maxlength="30"
                        autocomplete="tel"
                    >

                </div>

                <!-- SUBJECT -->

                <div class="form-group">

                    <label for="subject">
                        Subject
                    </label>

                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        placeholder="Enter subject"
                        value="<?php echo contact_e($subject); ?>"
                        maxlength="200"
                        required
                    >

                </div>

                <!-- MESSAGE -->

                <div class="form-group">

                    <label for="message">
                        Message
                    </label>

                    <textarea
                        id="message"
                        name="message"
                        placeholder="Write your message..."
                        required
                    ><?php echo contact_e($contact_message); ?></textarea>

                </div>

                <!-- SUBMIT -->

                <button type="submit" class="submit-btn">
                    Send Message
                </button>

            </form>

        </div>

    </div>

</main>


<!-- FOOTER -->

<footer>

    <p>
        &copy; 2026 ServiceHub. All Rights Reserved.
    </p>

</footer>

</body>

</html>

