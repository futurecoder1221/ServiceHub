
<?php
session_start();

require_once "../includes/db_connect.php";

/** @var mysqli $conn */

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $sql = "SELECT * FROM admins WHERE email = ? AND status = 'active' LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($admin = mysqli_fetch_assoc($result)) {

            if (password_verify($password, $admin["password"])) {

                session_regenerate_id(true);

                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["admin_name"] = $admin["name"];
                $_SESSION["admin_email"] = $admin["email"];

                header("Location: dashboard.php");
                exit();

            } else {
                $error = "Invalid email or password.";
            }

        } else {
            $error = "Invalid email or password.";
        }

        mysqli_stmt_close($stmt);

    } else {
        $error = "Database query error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | ServiceHub</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(
                135deg,
                #e0f2fe,
                #f8fafc,
                #eef2ff
            );
            color: #1e293b;
            min-height: 100vh;
        }

        /* HEADER */

        .header {
            background: #111827;
            padding: 20px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .logo span {
            color: #38bdf8;
        }

        .back-btn {
            color: white;
            text-decoration: none;
            background: #334155;
            padding: 11px 17px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            transition: 0.2s;
        }

        .back-btn:hover {
            background: #475569;
        }

        /* LOGIN SECTION */

        .login-section {
            min-height: calc(100vh - 75px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 45px 20px;
        }

        .login-box {
            width: 100%;
            max-width: 440px;
            background: white;
            padding: 42px 36px;
            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(15,23,42,0.10);
            border: 1px solid #e2e8f0;
        }

        .login-icon {
            width: 65px;
            height: 65px;
            background: #e0f2fe;
            border-radius: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            font-size: 30px;
        }

        .login-box h1 {
            text-align: center;
            font-size: 27px;
            margin-bottom: 10px;
            color: #0f172a;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* FORM */

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 9px;
            color: #334155;
        }

        input {
            display: block;
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: 16px;
            font-family: Arial, sans-serif;
            outline: none;
            background: #fff;
            transition: 0.2s;
        }

        input:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2,132,199,0.12);
        }

        .login-button {
            display: block;
            width: 100%;
            background: #0284c7;
            color: white;
            border: none;
            border-radius: 9px;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }

        .login-button:hover {
            background: #0369a1;
        }

        .error {
            background: #fef2f2;
            color: #b91c1c;
            padding: 13px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .security-note {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 13px;
            text-align: center;
            line-height: 1.6;
        }

        /* MOBILE RESPONSIVE */

        @media (max-width: 600px) {

            .header {
                padding: 17px 5%;
            }

            .logo {
                font-size: 22px;
            }

            .back-btn {
                font-size: 13px;
                padding: 11px 12px;
            }

            .login-section {
                min-height: calc(100vh - 70px);
                padding: 30px 16px;
                align-items: center;
            }

            .login-box {
                padding: 32px 23px;
                border-radius: 14px;
            }

            .login-box h1 {
                font-size: 25px;
            }

            .subtitle {
                font-size: 14px;
            }

            input {
                padding: 15px;
                font-size: 16px;
            }

            .login-button {
                padding: 16px;
                font-size: 16px;
            }

        }

        @media (max-width: 360px) {

            .logo {
                font-size: 20px;
            }

            .back-btn {
                font-size: 12px;
                padding: 10px;
            }

            .login-box {
                padding: 28px 18px;
            }

        }

    </style>

</head>

<body>

    <!-- HEADER -->

    <header class="header">

        <a href="../index.php" class="logo">
            Service<span>Hub</span>
        </a>

        <a href="../index.php" class="back-btn">
            ← Back to Website
        </a>

    </header>

    <!-- LOGIN FORM -->

    <main class="login-section">

        <div class="login-box">

            <div class="login-icon">
                🔐
            </div>

            <h1>Admin Login</h1>

            <p class="subtitle">
                Welcome back! Sign in to manage
                your ServiceHub platform.
            </p>

            <?php if ($error != "") { ?>

                <div class="error">
                    <?php
                    echo htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        "UTF-8"
                    );
                    ?>
                </div>

            <?php } ?>

            <form method="POST" action="">

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter admin email"
                        autocomplete="username"
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

                <button
                    type="submit"
                    class="login-button"
                >
                    Login to Dashboard →
                </button>

            </form>

            <p class="security-note">
                🔒 Authorized administrators only.
            </p>

        </div>

    </main>

</body>

</html>
