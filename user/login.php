
<?php

session_start();

require_once __DIR__ . "/../includes/db_connect.php";

/** @var mysqli $conn */

$message = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $message = "Please enter your email and password.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } else {

        $sql = "SELECT id, name, email, password, role
                FROM users
                WHERE email = ?
                LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) === 1) {

                $user = mysqli_fetch_assoc($result);

                if (password_verify($password, $user["password"])) {

                    session_regenerate_id(true);

                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["user_name"] = $user["name"];
                    $_SESSION["user_email"] = $user["email"];
                    $_SESSION["user_role"] = $user["role"];

                    mysqli_stmt_close($stmt);

                    header("Location: dashboard.php");
                    exit();

                } else {

                    $message = "Incorrect email or password.";

                }

            } else {

                $message = "Incorrect email or password.";

            }

            mysqli_stmt_close($stmt);

        } else {

            $message = "Unable to process login. Please try again.";

        }
    }
}

function login_e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login | ServiceHub</title>

<style>

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

/* LOGIN PAGE */

.login-section {
    width: 100%;
    min-height: calc(100vh - 76px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 45px 20px;
}

.login-box {
    width: 100%;
    max-width: 460px;
    background: white;
    padding: 38px;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 35px rgba(15,23,42,0.08);
}

/* LOGIN HEADER */

.login-icon {
    width: 62px;
    height: 62px;
    background: #e0f2fe;
    color: #0369a1;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 29px;
    margin: 0 auto 20px;
}

.login-box h1 {
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
    margin-bottom: 29px;
}

/* ERROR MESSAGE */

.message {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    padding: 13px 15px;
    border-radius: 9px;
    font-size: 14px;
    line-height: 1.5;
    text-align: center;
    margin-bottom: 22px;
}

/* FORM */

.form-group {
    margin-bottom: 21px;
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

/* LOGIN BUTTON */

.login-btn {
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
    transition: background 0.2s;
}

.login-btn:hover {
    background: #0369a1;
}

.login-btn:focus-visible,
.home-link:focus-visible,
.register-link a:focus-visible {
    outline: 3px solid #38bdf8;
    outline-offset: 3px;
}

/* REGISTER LINK */

.register-link {
    text-align: center;
    margin-top: 25px;
    padding-top: 23px;
    border-top: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 14px;
    line-height: 1.7;
}

.register-link a {
    color: #0284c7;
    text-decoration: none;
    font-weight: bold;
}

.register-link a:hover {
    text-decoration: underline;
}

/* FOOTER NOTE */

.login-footer {
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

    .login-section {
        min-height: auto;
        padding: 30px 16px 45px;
        align-items: flex-start;
    }

    .login-box {
        padding: 28px 22px;
        border-radius: 14px;
    }

    .login-box h1 {
        font-size: 25px;
    }

    .intro {
        font-size: 14px;
    }

    .login-icon {
        width: 56px;
        height: 56px;
        font-size: 26px;
    }

    .form-group input {
        padding: 14px;
        font-size: 16px;
    }

    .login-btn {
        padding: 15px;
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

    .login-box {
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

<!-- LOGIN FORM -->

<main class="login-section">

    <div class="login-box">

        <div class="login-icon" aria-hidden="true">
            🔐
        </div>

        <h1>Welcome Back</h1>

        <p class="intro">
            Sign in to your ServiceHub account
            to manage your bookings and services.
        </p>

        <?php if ($message !== ""): ?>

            <div class="message" role="alert">
                <?php echo login_e($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email address"
                    value="<?php echo login_e($email); ?>"
                    autocomplete="username"
                    maxlength="254"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >

            </div>

            <button type="submit" class="login-btn">
                Login to Account
            </button>

        </form>

        <div class="register-link">

            Don't have an account?

            <a href="register.php">
                Create Account
            </a>

        </div>

        <p class="login-footer">
            ServiceHub &mdash; Your Trusted Service Platform
        </p>

    </div>

</main>

</body>

</html>
