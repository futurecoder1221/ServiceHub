
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

require_once "../includes/db_connect.php";

/** @var mysqli $conn */

/* SAFE OUTPUT FUNCTION */

function contact_e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

/* FETCH CONTACT MESSAGES */

$contacts = [];
$load_error = false;

$sql = "SELECT
            id,
            name,
            email,
            phone,
            subject,
            message,
            created_at
        FROM contacts
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {
        $contacts[] = $row;
    }

    mysqli_free_result($result);

} else {

    $load_error = true;

}

$total_contacts = count($contacts);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Manage Contacts | ServiceHub Admin</title>

<style>

/* GENERAL */

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
    color: white;
    padding: 18px 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
}

.logo {
    color: white;
    font-size: 24px;
    font-weight: bold;
    text-decoration: none;
    white-space: nowrap;
}

.logo span {
    color: #38bdf8;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 12px;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
    padding: 12px 17px;
    border-radius: 8px;
    text-align: center;
    transition: background 0.2s;
}

.dashboard-btn {
    background: #334155;
}

.dashboard-btn:hover {
    background: #475569;
}

.logout-btn {
    background: #dc2626;
}

.logout-btn:hover {
    background: #b91c1c;
}

/* MAIN CONTAINER */

.container {
    width: 94%;
    max-width: 1300px;
    margin: 30px auto 55px;
}

/* PAGE HEADING */

.page-heading {
    background: linear-gradient(135deg, #111827, #1e3a5f);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 24px;
}

.page-heading h1 {
    font-size: 29px;
    margin-bottom: 10px;
}

.page-heading p {
    color: #cbd5e1;
    font-size: 15px;
    line-height: 1.6;
}

/* SUMMARY */

.summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 25px;
}

.summary-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 13px;
    padding: 23px;
    box-shadow: 0 4px 15px rgba(15,23,42,0.05);
    min-width: 0;
}

.summary-card .icon {
    font-size: 27px;
    margin-bottom: 13px;
}

.summary-card h3 {
    font-size: 15px;
    color: #64748b;
    margin-bottom: 10px;
}

.summary-card .number {
    font-size: 33px;
    font-weight: bold;
    color: #0f172a;
}

/* CONTACTS PANEL */

.contacts-panel {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(15,23,42,0.05);
    min-width: 0;
}

.panel-heading {
    margin-bottom: 23px;
}

.panel-heading h2 {
    font-size: 23px;
    margin-bottom: 8px;
}

.panel-heading p {
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
}

/* DESKTOP TABLE */

.contacts-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    text-align: left;
}

.contacts-table th {
    background: #111827;
    color: white;
    padding: 14px 10px;
    font-size: 13px;
    font-weight: bold;
}

.contacts-table td {
    padding: 15px 10px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 13px;
    line-height: 1.6;
    vertical-align: top;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.contacts-table tbody tr:nth-child(even) {
    background: #f8fafc;
}

.contacts-table tbody tr:hover {
    background: #eff6ff;
}

.contacts-table tbody tr:last-child td {
    border-bottom: none;
}

.contacts-table th:nth-child(1) {
    width: 5%;
}

.contacts-table th:nth-child(2) {
    width: 13%;
}

.contacts-table th:nth-child(3) {
    width: 17%;
}

.contacts-table th:nth-child(4) {
    width: 12%;
}

.contacts-table th:nth-child(5) {
    width: 14%;
}

.contacts-table th:nth-child(6) {
    width: 25%;
}

.contacts-table th:nth-child(7) {
    width: 14%;
}

.contact-name {
    font-weight: bold;
    color: #0f172a;
}

.contact-message {
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    word-break: break-word;
}

/* MOBILE CONTACT CARDS */

.mobile-contacts {
    display: none;
}

.mobile-contact-card {
    background: white;
    border: 1px solid #dbe3ee;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 17px;
    min-width: 0;
}

.mobile-contact-card:last-child {
    margin-bottom: 0;
}

.mobile-contact-heading {
    background: #111827;
    color: white;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.mobile-contact-heading strong {
    font-size: 15px;
    overflow-wrap: anywhere;
}

.message-badge {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    background: #e0f2fe;
    color: #0369a1;
    font-size: 12px;
    font-weight: bold;
    flex-shrink: 0;
}

/* MOBILE DETAILS TABLE */

.contact-details {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.contact-details th,
.contact-details td {
    padding: 13px 12px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
    line-height: 1.6;
    text-align: left;
    vertical-align: top;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.contact-details th {
    width: 31%;
    background: #f8fafc;
    color: #475569;
    font-weight: bold;
}

.contact-details td {
    width: 69%;
    background: white;
    color: #1e293b;
}

.contact-details tr:last-child th,
.contact-details tr:last-child td {
    border-bottom: none;
}

.mobile-message {
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    word-break: break-word;
}

/* EMPTY / ERROR STATES */

.empty-state {
    text-align: center;
    padding: 35px 15px;
    color: #64748b;
    font-size: 15px;
    line-height: 1.7;
}

.error-state {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
    border-radius: 10px;
    padding: 18px;
    font-size: 14px;
    line-height: 1.6;
}

/* TABLET */

@media (max-width: 900px) {

    .navbar {
        flex-wrap: wrap;
    }

    .summary {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

}

/* MOBILE */

@media (max-width: 768px) {

    .desktop-contacts {
        display: none;
    }

    .mobile-contacts {
        display: block;
    }

    .container {
        margin-top: 22px;
    }

    .page-heading {
        padding: 25px 20px;
    }

    .page-heading h1 {
        font-size: 25px;
    }

    .contacts-panel {
        padding: 17px;
    }

    .panel-heading h2 {
        font-size: 21px;
    }

}

/* SMALL PHONES */

@media (max-width: 480px) {

    .navbar {
        flex-direction: column;
        align-items: stretch;
        padding: 16px 5%;
        gap: 15px;
    }

    .logo {
        font-size: 22px;
        text-align: center;
    }

    .nav-links {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 9px;
    }

    .nav-links a {
        font-size: 13px;
        padding: 13px 8px;
    }

    .summary {
        grid-template-columns: 1fr;
        gap: 13px;
    }

    .summary-card {
        padding: 20px;
    }

    .summary-card .number {
        font-size: 30px;
    }

    .mobile-contact-heading {
        padding: 14px 12px;
    }

    .mobile-contact-heading strong {
        font-size: 14px;
    }

    .contact-details th,
    .contact-details td {
        padding: 12px 9px;
        font-size: 13px;
    }

}

</style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar">

    <a href="dashboard.php" class="logo">
        Service<span>Hub</span> Admin
    </a>

    <div class="nav-links">

        <a href="dashboard.php" class="dashboard-btn">
            Dashboard
        </a>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </div>

</nav>

<!-- MAIN CONTENT -->

<main class="container">

    <!-- PAGE HEADING -->

    <header class="page-heading">

        <h1>Manage Contacts</h1>

        <p>
            View contact messages and inquiries
            received through your ServiceHub website.
        </p>

    </header>

    <!-- SUMMARY -->

    <section class="summary">

        <div class="summary-card">

            <div class="icon">📩</div>

            <h3>Total Contact Messages</h3>

            <div class="number">
                <?php echo $total_contacts; ?>
            </div>

        </div>

    </section>

    <!-- CONTACT MESSAGES -->

    <section class="contacts-panel">

        <div class="panel-heading">

            <h2>Received Messages</h2>

            <p>
                Review inquiries submitted through
                the Contact Us form.
            </p>

        </div>

        <?php if ($load_error): ?>

            <div class="error-state">
                Contact messages could not be loaded.
                Please try again later.
            </div>

        <?php elseif ($total_contacts > 0): ?>

            <!-- DESKTOP TABLE -->

            <div class="desktop-contacts">

                <table class="contacts-table">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Created At</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($contacts as $contact): ?>

                            <tr>

                                <td>
                                    <?php echo (int)$contact["id"]; ?>
                                </td>

                                <td class="contact-name">
                                    <?php echo contact_e($contact["name"]); ?>
                                </td>

                                <td>
                                    <?php echo contact_e($contact["email"]); ?>
                                </td>

                                <td>
                                    <?php echo contact_e($contact["phone"]); ?>
                                </td>

                                <td>
                                    <?php echo contact_e($contact["subject"]); ?>
                                </td>

                                <td>
                                    <div class="contact-message"><?php echo contact_e($contact["message"]); ?></div>
                                </td>

                                <td>
                                    <?php echo contact_e($contact["created_at"]); ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- MOBILE CONTACT CARDS -->

            <div class="mobile-contacts">

                <?php foreach ($contacts as $contact): ?>

                    <article class="mobile-contact-card">

                        <div class="mobile-contact-heading">

                            <strong>
                                Message #<?php echo (int)$contact["id"]; ?>
                            </strong>

                            <span class="message-badge">
                                Inquiry
                            </span>

                        </div>

                        <table class="contact-details">

                            <tbody>

                                <tr>
                                    <th scope="row">ID</th>
                                    <td>
                                        <?php echo (int)$contact["id"]; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Name</th>
                                    <td>
                                        <?php echo contact_e($contact["name"]); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Email</th>
                                    <td>
                                        <?php echo contact_e($contact["email"]); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Phone</th>
                                    <td>
                                        <?php echo contact_e($contact["phone"]); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Subject</th>
                                    <td>
                                        <?php echo contact_e($contact["subject"]); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Message</th>
                                    <td>
                                        <div class="mobile-message"><?php echo contact_e($contact["message"]); ?></div>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Received</th>
                                    <td>
                                        <?php echo contact_e($contact["created_at"]); ?>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty-state">
                No contact messages found yet.
            </div>

        <?php endif; ?>

    </section>

</main>

</body>

</html>
