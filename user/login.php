<?php

session_start();

require_once __DIR__ . "/../includes/db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $email = mysqli_real_escape_string($conn, $email);

    $sql = "SELECT id, name, email, password, role
            FROM users
            WHERE email = '$email'
            LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];

            header("Location: dashboard.php");
            exit;

        } else {

            $message = "Incorrect password.";

        }

    } else {

        $message = "Account not found.";

    }
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
            font-family: Arial, sans-serif;
        }

        body {
            background: #eef7ff;
            min-height: 100vh;
        }

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

        nav a {
            color: white;
            text-decoration: none;
        }

        .login-box {
            width: 430px;
            max-width: 90%;
            margin: 70px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }

        .login-box h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        .intro {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }

        .message {
            text-align: center;
            color: #dc2626;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #0284c7;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #0284c7;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #0369a1;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .register-link a {
            color: #0284c7;
            text-decoration: none;
            font-weight: bold;
        }

    </style>

</head>

<body>

    <nav>

        <div class="logo">
            Service<span>Hub</span>
        </div>

        <a href="../index.php">Home</a>

    </nav>


    <div class="login-box">

        <h1>Welcome Back</h1>

        <p class="intro">
            Login to manage your ServiceHub requests.
        </p>

        <?php if ($message !== "") { ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>


        <form method="POST">

            <div class="form-group">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>


            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>


            <button type="submit">
                Login
            </button>

        </form>


        <div class="register-link">

            Don't have an account?

            <a href="register.php">
                Create Account
            </a>

        </div>

    </div>

</body>

</html>