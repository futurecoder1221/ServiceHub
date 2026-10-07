<?php

require_once __DIR__ . "/../includes/db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];

    if ($name === "" || $email === "" || $phone === "" || $password === "") {

        $message = "Please fill all fields.";

    } else {

        $email = mysqli_real_escape_string($conn, $email);
        $name = mysqli_real_escape_string($conn, $name);
        $phone = mysqli_real_escape_string($conn, $phone);

        $check = mysqli_query(
            $conn,
            "SELECT id FROM users WHERE email = '$email'"
        );

        if (mysqli_num_rows($check) > 0) {

            $message = "This email is already registered.";

        } else {

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $sql = "INSERT INTO users
                    (name, email, phone, password, role)
                    VALUES
                    ('$name', '$email', '$phone', '$hashed_password', 'user')";

            if (mysqli_query($conn, $sql)) {

                $message = "Registration successful!";

            } else {

                $message = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account | ServiceHub</title>

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

        .register-box {
            width: 450px;
            max-width: 90%;
            margin: 60px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }

        .register-box h1 {
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
            color: #0284c7;
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

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .login-link a {
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


    <div class="register-box">

        <h1>Create Account</h1>

        <p class="intro">
            Register to use ServiceHub services.
        </p>

        <?php if ($message !== "") { ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>


        <form method="POST" action="">

            <div class="form-group">

                <label>Full Name</label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter your full name"
                    required
                >

            </div>


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

                <label>Phone Number</label>

                <input
                    type="text"
                    name="phone"
                    placeholder="Enter your phone number"
                    required
                >

            </div>


            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Create a password"
                    required
                >

            </div>


            <button type="submit">
                Create Account
            </button>

        </form>


        <div class="login-link">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </div>

    </div>

</body>

</html>