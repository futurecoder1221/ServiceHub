
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ServiceHub | Professional Services</title>

    <style>

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
            overflow-x: hidden;
        }

        body {
            background: #f8f9fc;
            color: #222;
        }

        /* Hero */

        .hero {
            min-height: 560px;
            display: flex;
            align-items: center;
            padding: 70px 7%;
            background: linear-gradient(135deg, #eef7ff, #ffffff);
        }

        .hero-content {
            max-width: 650px;
        }

        .hero h1 {
            font-size: 52px;
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .hero h1 span {
            color: #0284c7;
        }

        .hero p {
            font-size: 19px;
            line-height: 1.7;
            color: #555;
            margin-bottom: 30px;
        }

        .hero-buttons a {
            display: inline-block;
            text-decoration: none;
            padding: 14px 25px;
            border-radius: 7px;
            margin-right: 10px;
            font-weight: bold;
        }

        .primary-btn {
            background: #0284c7;
            color: white;
        }

        .secondary-btn {
            border: 2px solid #0284c7;
            color: #0284c7;
        }

        /* Why Choose Us */

        .section {
            padding: 70px 7%;
            text-align: center;
        }

        .section h2 {
            font-size: 34px;
            margin-bottom: 12px;
        }

        .section-intro {
            color: #666;
            margin-bottom: 40px;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .feature-card .icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            margin-bottom: 10px;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Services */

        .services-section {
            background: #f1f5f9;
        }

        .services {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .service-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: left;
            box-shadow: 0 5px 20px rgba(0,0,0,0.07);
        }

        .service-card h3 {
            margin-bottom: 12px;
        }

        .service-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 18px;
        }

        .service-card a {
            color: #0284c7;
            text-decoration: none;
            font-weight: bold;
        }

        /* How It Works */

        .steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .step {
            padding: 25px;
        }

        .step-number {
            width: 55px;
            height: 55px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: #0284c7;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: bold;
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

            .hero {
                min-height: auto;
                padding: 60px 6%;
                text-align: center;
            }

            .hero-content {
                max-width: 100%;
                width: 100%;
            }

            .hero h1 {
                font-size: 36px;
                line-height: 1.2;
            }

            .hero p {
                font-size: 17px;
                line-height: 1.6;
            }

            .hero-buttons {
                display: flex;
                flex-direction: column;
                gap: 12px;
                align-items: center;
            }

            .hero-buttons a {
                width: 100%;
                max-width: 280px;
                margin-right: 0;
                text-align: center;
            }

            .section {
                padding: 50px 6%;
            }

            .section h2 {
                font-size: 28px;
            }

            .section-intro {
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 30px;
            }

            .features,
            .services,
            .steps {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .feature-card,
            .service-card {
                padding: 25px 20px;
            }

            footer {
                padding: 22px 15px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {

            .hero h1 {
                font-size: 31px;
            }

            .hero p {
                font-size: 16px;
            }

            .section h2 {
                font-size: 26px;
            }
        }

    </style>

</head>

<body>

    <!-- Common Navbar -->

    <?php require __DIR__ . "/includes/navbar.php"; ?>


    <!-- Hero Section -->

    <section class="hero">

        <div class="hero-content">

            <h1>
                Professional Services.
                <span>Simple Booking.</span>
            </h1>

            <p>
                Find the right service for your needs, submit your request,
                and manage your bookings easily through ServiceHub.
            </p>

            <div class="hero-buttons">

                <a href="services.php" class="primary-btn">
                    Explore Services
                </a>

                <a href="user/booking.php" class="secondary-btn">
                    Book a Service
                </a>

            </div>

        </div>

    </section>


    <!-- Why Choose Us -->

    <section class="section">

        <h2>Why Choose ServiceHub?</h2>

        <p class="section-intro">
            Everything you need to request and manage professional services.
        </p>

        <div class="features">

            <div class="feature-card">

                <div class="icon">🔎</div>

                <h3>Easy Service Search</h3>

                <p>
                    Browse different services and find the one that matches
                    your requirements.
                </p>

            </div>


            <div class="feature-card">

                <div class="icon">📅</div>

                <h3>Simple Booking</h3>

                <p>
                    Submit your service request with your preferred date,
                    time and requirements.
                </p>

            </div>


            <div class="feature-card">

                <div class="icon">📊</div>

                <h3>Track Your Request</h3>

                <p>
                    Check your booking status and stay updated about your
                    service request.
                </p>

            </div>

        </div>

    </section>


    <!-- Popular Services -->

    <section class="section services-section">

        <h2>Popular Services</h2>

        <p class="section-intro">
            Explore some of the professional services available on ServiceHub.
        </p>

        <div class="services">

            <div class="service-card">

                <h3>💻 Website Development</h3>

                <p>
                    Professional websites for businesses, portfolios and
                    personal projects.
                </p>

                <a href="services.php">
                    View Service →
                </a>

            </div>


            <div class="service-card">

                <h3>🎨 Graphic Design</h3>

                <p>
                    Creative designs for logos, social media, branding and
                    promotional material.
                </p>

                <a href="services.php">
                    View Service →
                </a>

            </div>


            <div class="service-card">

                <h3>📱 UI/UX Design</h3>

                <p>
                    Clean and user-friendly designs for websites and mobile
                    applications.
                </p>

                <a href="services.php">
                    View Service →
                </a>

            </div>

        </div>

    </section>


    <!-- How It Works -->

    <section class="section" id="how-it-works">

        <h2>How It Works</h2>

        <p class="section-intro">
            Get your required service in three simple steps.
        </p>

        <div class="steps">

            <div class="step">

                <div class="step-number">1</div>

                <h3>Create Account</h3>

                <p>
                    Register your account on ServiceHub.
                </p>

            </div>


            <div class="step">

                <div class="step-number">2</div>

                <h3>Select Service</h3>

                <p>
                    Choose a service and submit your booking request.
                </p>

            </div>


            <div class="step">

                <div class="step-number">3</div>

                <h3>Track Status</h3>

                <p>
                    Follow your request until it is completed.
                </p>

            </div>

        </div>

    </section>


    <!-- Footer -->

    <footer>

        <p>
            &copy; 2026 ServiceHub. All Rights Reserved.
        </p>

    </footer>

</body>

</html>


