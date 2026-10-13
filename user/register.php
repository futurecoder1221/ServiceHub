
<?php

require_once __DIR__ . "/../includes/db_connect.php";

/** @var mysqli $conn */

$message = "";
$message_type = "";

$name = "";
$email = "";
$phone = "";

/* SAFE OUTPUT */

function register_e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

/* REGISTRATION PROCESS */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (
        $name === "" ||
        $email === "" ||
        $phone === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (strlen($name) > 100) {

        $message = "Full name is too long.";
        $message_type = "error";

    } elseif (strlen($email) > 254) {

        $message = "Email address is too long.";
        $message_type = "error";

    } elseif (strlen($phone) > 30) {

        $message = "Phone number is too long.";
        $message_type = "error";

    } elseif (strlen($password) < 8) {

        $message = "Password must be at least 8 characters long.";
        $message_type = "error";

    } elseif ($password !== $confirm_password) {

        $message = "Password and Confirm Password do not match.";
        $message_type = "error";

    } else {

        /* CHECK EXISTING EMAIL */

        $check_sql = "SELECT id
                      FROM users
                      WHERE email = ?
                      LIMIT 1";

        $check_stmt = mysqli_prepare($conn, $check_sql);

        if (!$check_stmt) {

            $message = "Unable to process registration. Please try again.";
            $message_type = "error";

        } else {

            mysqli_stmt_bind_param(
                $check_stmt,
                "s",
                $email
            );

            mysqli_stmt_execute($check_stmt);

            $check_result = mysqli_stmt_get_result($check_stmt);

            if ($check_result && mysqli_num_rows($check_result) > 0) {

                $message = "This email is already registered.";
                $message_type = "error";

            } else {

                /* HASH PASSWORD */

                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                /* INSERT NEW USER */

                $insert_sql = "INSERT INTO users
                               (name, email, phone, password, role)
                               VALUES (?, ?, ?, ?, 'user')";

                $insert_stmt = mysqli_prepare($conn, $insert_sql);

                if ($insert_stmt) {

                    mysqli_stmt_bind_param(
                        $insert_stmt,
                        "ssss",
                        $name,
                        $email,
                        $phone,
                        $hashed_password
                    );

                    if (mysqli_stmt_execute($insert_stmt)) {

                        $message = "Registration successful! You can now log in.";
                        $message_type = "success";

                        $name = "";
                        $email = "";
                        $phone = "";

                    } else {

                        if (mysqli_stmt_errno($insert_stmt) == 1062) {

                            $message = "This email is already registered.";

                        } else {

                            $message = "Registration failed. Please try again.";

                        }

                        $message_type = "error";
                    }

                    mysqli_stmt_close($insert_stmt);

                } else {

                    $message = "Unable to process registration. Please try again.";
                    $message_type = "error";

                }
            }

            mysqli_stmt_close($check_stmt);
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

<title>Create Account | ServiceHub</title>

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

/* NAVBAR */

.navbar {
    background: #111827;
    padding: 18px 6%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.logo {
    color: white;
    text-decoration: none;
    font-size: 27px;
    font-weight: bold;
    white-space: nowrap;
}

.logo span {
    color: #38bdf8;
}

.home-link {
    color: white;
    background: #334155;
    text-decoration: none;
    padding: 11px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    text-align: center;
    transition: background 0.2s;
}

.home-link:hover {
    background: #475569;
}

/* REGISTRATION SECTION */

.register-section {
    width: 100%;
    display: flex;
    justify-content: center;
    padding: 45px 20px 65px;
}

.register-box {
    width: 100%;
    max-width: 500px;
    background: white;
    padding: 38px;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 35px rgba(15,23,42,0.08);
}

/* FORM HEADER */

.register-icon {
    width: 62px;
    height: 62px;
    background: #e0f2fe;
    color: #0369a1;
    border-radius: 16px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 29px;
    margin: 0 auto 20px;
}

.register-box h1 {
    text-align: center;
    font-size: 28px;
    color: #0f172a;
    margin-bottom: 11px;
}

.intro {
    text-align: center;
    color: #64748b;
    font-size: 14px;
    line-height: 1.7;
    margin-bottom: 28px;
}

/* MESSAGES */

.message {
    padding: 14px 16px;
    border-radius: 9px;
    font-size: 14px;
    line-height: 1.6;
    text-align: center;
    margin-bottom: 23px;
}

.message-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
}

.message-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.message-success a {
    display: inline-block;
    margin-top: 8px;
    color: #166534;
    font-weight: bold;
    text-decoration: underline;
}

/* FORM */

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 9px;
    font-size: 14px;
    font-weight: bold;
    color: #334155;
}

.form-group input {
    display: block;
    width: 100%;
    min-width: 0;
    padding: 14px 15px;
    border: 1px solid #cbd5e1;
    border-radius: 9px;
    background: white;
    color: #0f172a;
    font-size: 15px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group input::placeholder {
    color: #94a3b8;
}

.form-group input:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2,132,199,0.12);
}

.password-hint {
    color: #64748b;
    font-size: 12px;
    line-height: 1.5;
    margin-top: 7px;
}

/* REGISTER BUTTON */

.register-btn {
    display: block;
    width: 100%;
    padding: 15px;
    background: #0284c7;
    color: white;
    border: none;
    border-radius: 9px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 6px;
    transition: background 0.2s;
}

.register-btn:hover {
    background: #0369a1;
}

.register-btn:focus-visible,
.home-link:focus-visible,
.login-link a:focus-visible {
    outline: 3px solid #38bdf8;
    outline-offset: 3px;
}

/* LOGIN LINK */

.login-link {
    text-align: center;
    margin-top: 25px;
    padding-top: 23px;
    border-top: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 14px;
    line-height: 1.7;
}

.login-link a {
    color: #0284c7;
    text-decoration: none;
    font-weight: bold;
}

.login-link a:hover {
    text-decoration: underline;
}

/* FOOTER */

.register-footer {
    text-align: center;
    margin-top: 24px;
    font-size: 12px;
    color: #94a3b8;
}

/* MOBILE */

@media (max-width: 600px) {

    .navbar {
        padding: 16px 5%;
    }

    .logo {
        font-size: 23px;
    }

    .home-link {
        padding: 11px 15px;
        font-size: 13px;
    }

    .register-section {
        padding: 30px 16px 45px;
    }

    .register-box {
        padding: 28px 22px;
        border-radius: 14px;
    }

    .register-box h1 {
        font-size: 25px;
    }

    .register-icon {
        width: 56px;
        height: 56px;
        font-size: 26px;
    }

    .form-group input {
        padding: 14px;
        font-size: 16px;
    }

}

@media (max-width: 360px) {

    .navbar {
        gap: 10px;
    }

    .logo {
        font-size: 21px;
    }

    .home-link {
        padding: 10px 12px;
    }

    .register-box {
        padding: 25px 17px;
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

    <a href="../index.php" class="home-link">
        Home
    </a>

</nav>

<!-- REGISTRATION FORM -->

<main class="register-section">

    <div class="register-box">

        <div class="register-icon" aria-hidden="true">
            👤
        </div>

        <h1>Create Account</h1>

        <p class="intro">
            Join ServiceHub and start booking
            professional services with ease.
        </p>

        <?php if ($message !== ""): ?>

            <div
                class="message message-<?php echo register_e($message_type); ?>"
                role="alert"
            >

                <?php echo register_e($message); ?>

                <?php if ($message_type === "success"): ?>

                    <div>
                        <a href="login.php">
                            Go to Login
                        </a>
                    </div>

                <?php endif; ?>

            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <!-- FULL NAME -->

            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    value="<?php echo register_e($name); ?>"
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
                    placeholder="Enter your email address"
                    value="<?php echo register_e($email); ?>"
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
                    value="<?php echo register_e($phone); ?>"
                    maxlength="30"
                    autocomplete="tel"
                    required
                >

            </div>

            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

                <p class="password-hint">
                    Use at least 8 characters for your password.
                </p>

            </div>

            <!-- CONFIRM PASSWORD -->

            <div class="form-group">

                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Re-enter your password"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

            </div>

            <!-- SUBMIT -->

            <button type="submit" class="register-btn">
                Create Account
            </button>

        </form>

        <div class="login-link">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </div>

        <p class="register-footer">
            ServiceHub &mdash; Your Trusted Service Platform
        </p>

    </div>

</main>

</body>

</html>
