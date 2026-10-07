```php
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: user/login.php?redirect=../services.php");
    exit();
}

require_once "includes/db_connect.php";

/** @var mysqli $conn */

$sql = "SELECT id, name, description, price, category
        FROM services
        WHERE status = 'Active'
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Services | ServiceHub</title>

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

        /* Services */

        .services-section {
            padding: 70px 7%;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .service-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-icon {
            font-size: 45px;
            margin-bottom: 15px;
        }

        .service-card h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        .service-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .price {
            font-weight: bold;
            margin-bottom: 20px;
            color: #222;
        }

        .request-btn {
            display: inline-block;
            background: #0284c7;
            color: white;
            text-decoration: none;
            padding: 11px 22px;
            border-radius: 6px;
            font-weight: bold;
        }

        .request-btn:hover {
            background: #0369a1;
        }

        .no-services {
            text-align: center;
            color: #666;
            font-size: 18px;
            padding: 40px;
            background: white;
            border-radius: 12px;
        }

        /* Footer */

        footer {
            background: #111827;
            color: white;
            text-align: center;
            padding: 25px;
            margin-top: 20px;
        }

        /* Responsive */

        @media (max-width: 900px) {

            .services-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            nav {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }

            .services-grid {
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
            Our <span>Services</span>
        </h1>

        <p>
            Explore professional services available through ServiceHub.
        </p>

    </section>


    <!-- Services -->

    <section class="services-section">

        <div class="services-grid">

            <?php

            if ($result && mysqli_num_rows($result) > 0) {

                while ($row = mysqli_fetch_assoc($result)) {

                    $name = htmlspecialchars($row["name"]);
                    $description = htmlspecialchars($row["description"]);
                    $price = htmlspecialchars($row["price"]);
                    $category = strtolower($row["category"]);

                    /* Service icons */

                    if (strpos($category, "web") !== false) {
                        $icon = "💻";
                    } elseif (strpos($category, "graphic") !== false) {
                        $icon = "🎨";
                    } elseif (strpos($category, "ui") !== false || strpos($category, "ux") !== false) {
                        $icon = "📱";
                    } elseif (strpos($category, "marketing") !== false) {
                        $icon = "📢";
                    } elseif (strpos($category, "writing") !== false || strpos($category, "content") !== false) {
                        $icon = "✍️";
                    } elseif (strpos($category, "support") !== false || strpos($category, "it") !== false) {
                        $icon = "🛠️";
                    } else {
                        $icon = "⭐";
                    }

            ?>

                    <div class="service-card">

                        <div class="service-icon">
                            <?php echo $icon; ?>
                        </div>

                        <h2>
                            <?php echo $name; ?>
                        </h2>

                        <p>
                            <?php echo $description; ?>
                        </p>

                        <div class="price">
                            Starting from $<?php echo $price; ?>
                        </div>

                        <a
                            href="user/booking.php?service_id=<?php echo $row["id"]; ?>"
                            class="request-btn"
                        >
                            Request Service
                        </a>

                    </div>

            <?php

                }

            } else {

            ?>

                <div class="no-services">
                    No services are currently available.
                </div>

            <?php } ?>

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

