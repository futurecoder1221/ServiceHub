
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

require_once "../includes/db_connect.php";

$adminName = htmlspecialchars(
    $_SESSION["admin_name"] ?? "Admin",
    ENT_QUOTES,
    "UTF-8"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | ServiceHub</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            max-width: 100%;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        /* Navbar */

        .navbar {
            background: #111827;
            color: white;
            padding: 18px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
        }

        .brand {
            font-size: 23px;
            font-weight: bold;
        }

        .brand span {
            color: #38bdf8;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-actions a {
            display: inline-block;
            text-decoration: none;
            color: white;
            font-size: 14px;
            font-weight: 600;
            padding: 11px 16px;
            border-radius: 7px;
            text-align: center;
            transition: 0.2s;
        }

        .home-btn {
            background: #334155;
        }

        .home-btn:hover {
            background: #475569;
        }

        .logout-btn {
            background: #dc2626;
        }

        .logout-btn:hover {
            background: #b91c1c;
        }

        /* Main Container */

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 35px auto 60px;
        }

        /* Welcome Section */

        .welcome {
            background: linear-gradient(
                120deg,
                #111827,
                #334155
            );
            color: white;
            padding: 38px;
            border-radius: 15px;
            margin-bottom: 35px;
        }

        .welcome h1 {
            font-size: 30px;
            margin-bottom: 12px;
            overflow-wrap: anywhere;
        }

        .welcome p {
            font-size: 15px;
            color: #cbd5e1;
            line-height: 1.7;
        }

        /* Section Heading */

        .section-heading {
            margin-bottom: 22px;
        }

        .section-heading h2 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .section-heading p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
        }

        /* Dashboard Cards */

        .cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 28px 20px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;

            display: flex;
            flex-direction: column;
            align-items: flex-start;

            min-width: 0;
            transition: 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.10);
        }

        .card-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;

            background: #e0f2fe;
            border-radius: 12px;
            font-size: 25px;
            margin-bottom: 20px;
        }

        .card h3 {
            font-size: 19px;
            margin-bottom: 12px;
        }

        .card p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 24px;
            flex-grow: 1;
        }

        .card a {
            display: inline-block;
            background: #0284c7;
            color: white;
            text-decoration: none;

            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .card a:hover {
            background: #0369a1;
        }

        /* Tablet Layout */

        @media (max-width: 1000px) {

            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

        }

        /* Mobile Layout */

        @media (max-width: 600px) {

            .navbar {
                padding: 18px 5%;
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }

            .brand {
                font-size: 23px;
                text-align: center;
            }

            .nav-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .nav-actions a {
                font-size: 14px;
                padding: 13px 10px;
            }

            .container {
                width: 92%;
                margin: 25px auto 45px;
            }

            .welcome {
                padding: 28px 22px;
                margin-bottom: 30px;
            }

            .welcome h1 {
                font-size: 24px;
            }

            .welcome p {
                font-size: 14px;
            }

            .section-heading h2 {
                font-size: 22px;
            }

            .section-heading p {
                font-size: 14px;
            }

            /* ONE CARD PER ROW */

            .cards {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .card {
                padding: 25px;
                border-radius: 13px;
            }

            .card-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
                margin-bottom: 16px;
            }

            .card h3 {
                font-size: 20px;
                margin-bottom: 10px;
            }

            .card p {
                font-size: 15px;
                margin-bottom: 20px;
            }

            .card a {
                width: 100%;
                text-align: center;
                padding: 14px;
                font-size: 15px;
            }

        }

        /* Very Small Phones */

        @media (max-width: 360px) {

            .brand {
                font-size: 20px;
            }

            .nav-actions a {
                font-size: 13px;
            }

            .welcome h1 {
                font-size: 22px;
            }

            .card {
                padding: 22px;
            }

        }

    </style>

</head>

<body>

    <!-- Navbar -->

    <header class="navbar">

        <div class="brand">
            Service<span>Hub</span> Admin
        </div>

        <div class="nav-actions">

            <a href="../index.php" class="home-btn">
                View Website
            </a>

            <a href="logout.php" class="logout-btn">
                Logout
            </a>

        </div>

    </header>

    <!-- Main Content -->

    <main class="container">

        <!-- Welcome -->

        <section class="welcome">

            <h1>
                Welcome, <?php echo $adminName; ?>! 👋
            </h1>

            <p>
                Manage your ServiceHub platform,
                monitor bookings, and organize your
                services from one dashboard.
            </p>

        </section>

        <!-- Management Heading -->

        <div class="section-heading">

            <h2>Management Overview</h2>

            <p>
                Select a section below to manage your platform.
            </p>

        </div>

        <!-- Dashboard Cards -->

        <div class="cards">

            <!-- Services -->

            <div class="card">

                <div class="card-icon">🛠️</div>

                <h3>Services</h3>

                <p>
                    Add, update and manage professional
                    services available on ServiceHub.
                </p>

                <a href="services.php">
                    Manage Services →
                </a>

            </div>

            <!-- Bookings -->

            <div class="card">

                <div class="card-icon">📅</div>

                <h3>Bookings</h3>

                <p>
                    View customer booking requests
                    and update their status.
                </p>

                <a href="bookings.php">
                    View Bookings →
                </a>

            </div>

            <!-- Users -->

            <div class="card">

                <div class="card-icon">👥</div>

                <h3>Users</h3>

                <p>
                    View and manage registered
                    ServiceHub users.
                </p>

                <a href="users.php">
                    Manage Users →
                </a>

            </div>

            <!-- Contacts -->

            <div class="card">

                <div class="card-icon">✉️</div>

                <h3>Contacts</h3>

                <p>
                    Read customer messages submitted
                    through the contact form.
                </p>

                <a href="contacts.php">
                    View Messages →
                </a>

            </div>

        </div>

    </main>

</body>

</html>
