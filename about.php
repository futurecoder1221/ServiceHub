<?php
session_start();

/* Login required */
if (!isset($_SESSION["user_id"])) {
    header("Location: user/login.php");
    exit();
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

```
<title>About Us | ServiceHub</title>

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

    /* Header */
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

    /* About */
    .about-section {
        padding: 70px 7%;
    }

    .about-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        align-items: center;
    }

    .about-text h2 {
        font-size: 34px;
        margin-bottom: 20px;
    }

    .about-text h2 span {
        color: #0284c7;
    }

    .about-text p {
        color: #666;
        line-height: 1.8;
        margin-bottom: 15px;
        font-size: 17px;
    }

    .about-box {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .about-box h3 {
        margin-bottom: 20px;
        font-size: 25px;
    }

    .about-box ul {
        list-style: none;
    }

    .about-box li {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
        color: #555;
    }

    .about-box li:last-child {
        border-bottom: none;
    }

    /* Mission */
    .mission {
        background: #f1f5f9;
        padding: 70px 7%;
        text-align: center;
    }

    .mission h2 {
        font-size: 34px;
        margin-bottom: 18px;
    }

    .mission p {
        max-width: 800px;
        margin: auto;
        color: #666;
        line-height: 1.8;
        font-size: 17px;
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

        .about-content {
            grid-template-columns: 1fr;
        }

        .page-header h1 {
            font-size: 36px;
        }
    }
</style>
```

</head>

<body>

```
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

    </ul>

</nav>


<!-- Page Header -->
<section class="page-header">

    <h1>
        About <span>ServiceHub</span>
    </h1>

    <p>
        Connecting customers with professional services in one simple platform.
    </p>

</section>


<!-- About Section -->
<section class="about-section">

    <div class="about-content">

        <div class="about-text">

            <h2>
                Making Service Booking <span>Simple</span>
            </h2>

            <p>
                ServiceHub is a professional service booking and management
                platform designed to make finding and requesting services easier.
            </p>

            <p>
                Customers can explore available services, submit their service
                requirements and track the status of their requests from one place.
            </p>

            <p>
                The platform also provides an organized system for administrators
                to manage services, users, bookings and customer messages.
            </p>

        </div>


        <div class="about-box">

            <h3>What We Provide</h3>

            <ul>
                <li>✓ Professional Service Listings</li>
                <li>✓ Easy Service Requests</li>
                <li>✓ Booking Status Tracking</li>
                <li>✓ Customer Account Management</li>
                <li>✓ Admin Management Dashboard</li>
                <li>✓ Organized Booking Records</li>
            </ul>

        </div>

    </div>

</section>


<!-- Mission -->
<section class="mission">

    <h2>Our Mission</h2>

    <p>
        Our mission is to provide a simple, organized and user-friendly
        platform where customers can easily discover professional services
        and submit their requirements while service providers can manage
        requests efficiently.
    </p>

</section>


<!-- Footer -->
<footer>

    <p>
        © 2026 ServiceHub. All Rights Reserved.
    </p>

</footer>
```

</body>
</html>
