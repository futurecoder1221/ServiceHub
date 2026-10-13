
<?php

session_start();

/* LOGIN CHECK */

if (!isset($_SESSION["user_id"])) {
    header("Location: user/login.php?redirect=../services.php");
    exit();
}

/* DATABASE CONNECTION */

require_once __DIR__ . "/includes/db_connect.php";

/** @var mysqli $conn */

/* LOAD ACTIVE SERVICES */

$sql = "SELECT id, name, description, price, category
        FROM services
        WHERE status = 'Active'
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

/* SAFE OUTPUT */

function service_e($value) {
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

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Services | ServiceHub</title>

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
    max-width: 650px;
    margin: 0 auto;
}

/* SERVICES SECTION */

.services-section {
    padding: 70px 7%;
    width: 100%;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 25px;
}

/* SERVICE CARD */

.service-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #e2e8f0;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    min-width: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 28px rgba(0,0,0,0.1);
}

.service-icon {
    font-size: 45px;
    margin-bottom: 15px;
}

.service-card h2 {
    font-size: 23px;
    margin-bottom: 15px;
    color: #0f172a;
    line-height: 1.4;
    overflow-wrap: anywhere;
}

.service-card p {
    color: #64748b;
    line-height: 1.7;
    margin-bottom: 20px;
    font-size: 15px;
    overflow-wrap: anywhere;
    white-space: pre-line;
}

/* PRICE */

.price {
    font-weight: bold;
    margin-top: auto;
    margin-bottom: 22px;
    color: #0f172a;
    font-size: 17px;
    overflow-wrap: anywhere;
}

/* REQUEST BUTTON */

.request-btn {
    display: inline-block;
    background: #0284c7;
    color: white;
    text-decoration: none;
    padding: 13px 22px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
    transition: background 0.2s;
    max-width: 100%;
    text-align: center;
}

.request-btn:hover {
    background: #0369a1;
}

.request-btn:focus-visible {
    outline: 3px solid #38bdf8;
    outline-offset: 3px;
}

/* EMPTY STATE */

.no-services {
    grid-column: 1 / -1;
    text-align: center;
    color: #64748b;
    font-size: 17px;
    line-height: 1.7;
    padding: 45px 25px;
    background: white;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

/* FOOTER */

footer {
    background: #111827;
    color: white;
    text-align: center;
    padding: 25px 15px;
    margin-top: 20px;
    font-size: 14px;
}

/* TABLET */

@media (max-width: 1000px) {

    .services-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .services-section {
        padding: 55px 5%;
    }

}

/* MOBILE */

@media (max-width: 600px) {

    .page-header {
        padding: 48px 18px;
    }

    .page-header h1 {
        font-size: 34px;
    }

    .page-header p {
        font-size: 16px;
    }

    .services-section {
        padding: 35px 16px 50px;
    }

    .services-grid {
        grid-template-columns: minmax(0, 1fr);
        gap: 20px;
    }

    .service-card {
        padding: 27px 21px;
        border-radius: 12px;
    }

    .service-card h2 {
        font-size: 21px;
    }

    .service-card p {
        font-size: 14px;
    }

    .price {
        font-size: 16px;
    }

    .request-btn {
        width: 100%;
        padding: 14px;
    }

    footer {
        padding: 22px 15px;
    }

}

@media (max-width: 360px) {

    .page-header h1 {
        font-size: 30px;
    }

    .service-card {
        padding: 24px 16px;
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
        Our <span>Services</span>
    </h1>

    <p>
        Explore professional services available
        through ServiceHub.
    </p>

</section>


<!-- SERVICES SECTION -->

<main class="services-section">

    <div class="services-grid">

        <?php if ($result && mysqli_num_rows($result) > 0): ?>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <?php

                $name = service_e($row["name"]);
                $description = service_e($row["description"]);
                $price = service_e($row["price"]);
                $category = strtolower(
                    (string)($row["category"] ?? "")
                );

                /* SERVICE ICONS */

                if (strpos($category, "web") !== false) {

                    $icon = "💻";

                } elseif (strpos($category, "graphic") !== false) {

                    $icon = "🎨";

                } elseif (
                    strpos($category, "ui") !== false ||
                    strpos($category, "ux") !== false
                ) {

                    $icon = "📱";

                } elseif (strpos($category, "marketing") !== false) {

                    $icon = "📢";

                } elseif (
                    strpos($category, "writing") !== false ||
                    strpos($category, "content") !== false
                ) {

                    $icon = "✍️";

                } elseif (
                    strpos($category, "support") !== false ||
                    strpos($category, "it") !== false
                ) {

                    $icon = "🛠️";

                } else {

                    $icon = "⭐";

                }

                ?>

                <article class="service-card">

                    <div class="service-icon" aria-hidden="true">
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
                        href="user/booking.php?service_id=<?php echo (int)$row["id"]; ?>"
                        class="request-btn"
                    >
                        Request Service
                    </a>

                </article>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="no-services">
                No services are currently available.
            </div>

        <?php endif; ?>

    </div>

</main>


<!-- FOOTER -->

<footer>

    <p>
        &copy; 2026 ServiceHub. All Rights Reserved.
    </p>

</footer>

</body>

</html>


