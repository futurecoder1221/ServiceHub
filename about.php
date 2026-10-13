
<?php

session_start();

/* LOGIN REQUIRED */

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

    <title>About Us | ServiceHub</title>

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
            max-width: 750px;
            margin: 0 auto;
        }

        /* ABOUT SECTION */

        .about-section {
            padding: 70px 7%;
        }

        .about-content {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 50px;
            align-items: center;
        }

        /* ABOUT TEXT */

        .about-text {
            min-width: 0;
        }

        .about-text h2 {
            font-size: 34px;
            margin-bottom: 20px;
            color: #111827;
            line-height: 1.3;
        }

        .about-text h2 span {
            color: #0284c7;
        }

        .about-text p {
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 15px;
            font-size: 17px;
        }

        /* WHAT WE PROVIDE */

        .about-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            min-width: 0;
        }

        .about-box h3 {
            margin-bottom: 20px;
            font-size: 25px;
            color: #111827;
        }

        .about-box ul {
            list-style: none;
            padding: 0;
        }

        .about-box li {
            padding: 13px 0;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
            font-size: 16px;
            line-height: 1.6;
        }

        .about-box li:last-child {
            border-bottom: none;
        }

        /* MISSION */

        .mission {
            background: #f1f5f9;
            padding: 70px 7%;
            text-align: center;
        }

        .mission h2 {
            font-size: 34px;
            margin-bottom: 18px;
            color: #111827;
        }

        .mission p {
            max-width: 800px;
            margin: 0 auto;
            color: #64748b;
            line-height: 1.8;
            font-size: 17px;
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

            .about-section {
                padding: 55px 5%;
            }

            .about-content {
                gap: 25px;
            }

            .about-text h2 {
                font-size: 29px;
            }

            .about-box {
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

            .about-section {
                padding: 45px 18px;
            }

            .about-content {
                grid-template-columns: minmax(0, 1fr);
                gap: 30px;
            }

            .about-text h2 {
                font-size: 28px;
                margin-bottom: 18px;
            }

            .about-text p {
                font-size: 16px;
                line-height: 1.8;
            }

            .about-box {
                padding: 27px 22px;
            }

            .about-box h3 {
                font-size: 23px;
            }

            .about-box li {
                font-size: 15px;
            }

            .mission {
                padding: 50px 18px;
            }

            .mission h2 {
                font-size: 28px;
            }

            .mission p {
                font-size: 16px;
                line-height: 1.8;
            }

            footer {
                padding: 22px 15px;
            }

        }

        @media (max-width: 360px) {

            .page-header h1 {
                font-size: 31px;
            }

            .about-text h2 {
                font-size: 25px;
            }

            .about-box {
                padding: 24px 17px;
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
            About <span>ServiceHub</span>
        </h1>

        <p>
            Connecting customers with professional services
            in one simple platform.
        </p>

    </section>


    <!-- ABOUT SECTION -->

    <main class="about-section">

        <div class="about-content">

            <!-- ABOUT TEXT -->

            <div class="about-text">

                <h2>
                    Making Service Booking <span>Simple</span>
                </h2>

                <p>
                    ServiceHub is a professional service booking and
                    management platform designed to make finding and
                    requesting services easier.
                </p>

                <p>
                    Customers can explore available services, submit
                    their service requirements and track the status
                    of their requests from one place.
                </p>

                <p>
                    The platform also provides an organized system
                    for administrators to manage services, users,
                    bookings and customer messages.
                </p>

            </div>


            <!-- WHAT WE PROVIDE -->

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

    </main>


    <!-- OUR MISSION -->

    <section class="mission">

        <h2>Our Mission</h2>

        <p>
            Our mission is to provide a simple, organized and
            user-friendly platform where customers can easily
            discover professional services and submit their
            requirements while service providers can manage
            requests efficiently.
        </p>

    </section>


    <!-- FOOTER -->

    <footer>

        <p>
            &copy; 2026 ServiceHub. All Rights Reserved.
        </p>

    </footer>


</body>
</html>
