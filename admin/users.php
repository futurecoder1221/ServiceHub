
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

require_once "../includes/db_connect.php";

/** @var mysqli $conn */

/* SAFE OUTPUT */

function user_e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

/* FETCH REGISTERED USERS */

$users = [];

$sql = "SELECT
            id,
            name,
            email,
            phone,
            role,
            created_at
        FROM users
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

$total_users = count($users);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Manage Users | ServiceHub Admin</title>

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
    max-width: 1250px;
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

/* USERS PANEL */

.users-panel {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(15,23,42,0.05);
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

.users-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.users-table th {
    background: #111827;
    color: white;
    padding: 15px;
    font-size: 14px;
    font-weight: bold;
}

.users-table td {
    padding: 16px 15px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.users-table tbody tr:nth-child(even) {
    background: #f8fafc;
}

.users-table tbody tr:hover {
    background: #eff6ff;
}

.users-table tbody tr:last-child td {
    border-bottom: none;
}

/* USER NAME */

.user-name {
    font-weight: bold;
    color: #0f172a;
}

/* ROLE BADGE */

.role-badge {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    background: #e0f2fe;
    color: #0369a1;
}

/* MOBILE USER CARDS */

.mobile-users {
    display: none;
}

.mobile-user-card {
    background: white;
    border: 1px solid #dbe3ee;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 17px;
}

.mobile-user-card:last-child {
    margin-bottom: 0;
}

.mobile-user-heading {
    background: #111827;
    color: white;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.mobile-user-heading strong {
    font-size: 15px;
    overflow-wrap: anywhere;
}

.mobile-user-heading .role-badge {
    flex-shrink: 0;
}

/* MOBILE DETAILS TABLE */

.user-details {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.user-details th,
.user-details td {
    padding: 13px 12px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
    line-height: 1.5;
    text-align: left;
    vertical-align: top;
    overflow-wrap: anywhere;
}

.user-details th {
    width: 33%;
    background: #f8fafc;
    color: #475569;
    font-weight: bold;
}

.user-details td {
    width: 67%;
    background: white;
    color: #1e293b;
}

.user-details tr:last-child th,
.user-details tr:last-child td {
    border-bottom: none;
}

/* EMPTY STATE */

.empty-state {
    text-align: center;
    padding: 35px 15px;
    color: #64748b;
    font-size: 15px;
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

    .desktop-users {
        display: none;
    }

    .mobile-users {
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

    .users-panel {
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

    .mobile-user-heading {
        padding: 14px 12px;
    }

    .mobile-user-heading strong {
        font-size: 14px;
    }

    .user-details th,
    .user-details td {
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

        <h1>Manage Users</h1>

        <p>
            View and manage registered users
            of your ServiceHub platform.
        </p>

    </header>

    <!-- SUMMARY -->

    <section class="summary">

        <div class="summary-card">

            <div class="icon">👥</div>

            <h3>Total Registered Users</h3>

            <div class="number">
                <?php echo $total_users; ?>
            </div>

        </div>

    </section>

    <!-- USERS LIST -->

    <section class="users-panel">

        <div class="panel-heading">

            <h2>Registered Users</h2>

            <p>
                View user information and account details.
            </p>

        </div>

        <?php if ($total_users > 0): ?>

            <!-- DESKTOP TABLE -->

            <div class="desktop-users">

                <table class="users-table">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Created At</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($users as $user): ?>

                            <tr>

                                <td>
                                    <?php echo (int)$user["id"]; ?>
                                </td>

                                <td class="user-name">
                                    <?php echo user_e($user["name"]); ?>
                                </td>

                                <td>
                                    <?php echo user_e($user["email"]); ?>
                                </td>

                                <td>
                                    <?php echo user_e($user["phone"]); ?>
                                </td>

                                <td>
                                    <span class="role-badge">
                                        <?php echo user_e($user["role"] ?: "User"); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo user_e($user["created_at"]); ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- MOBILE USER CARDS -->

            <div class="mobile-users">

                <?php foreach ($users as $user): ?>

                    <article class="mobile-user-card">

                        <div class="mobile-user-heading">

                            <strong>
                                User #<?php echo (int)$user["id"]; ?>
                            </strong>

                            <span class="role-badge">
                                <?php echo user_e($user["role"] ?: "User"); ?>
                            </span>

                        </div>

                        <table class="user-details">

                            <tbody>

                                <tr>
                                    <th scope="row">ID</th>
                                    <td>
                                        <?php echo (int)$user["id"]; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Name</th>
                                    <td>
                                        <?php echo user_e($user["name"]); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Email</th>
                                    <td>
                                        <?php echo user_e($user["email"]); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Phone</th>
                                    <td>
                                        <?php echo user_e($user["phone"]); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Role</th>
                                    <td>
                                        <span class="role-badge">
                                            <?php echo user_e($user["role"] ?: "User"); ?>
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Created At</th>
                                    <td>
                                        <?php echo user_e($user["created_at"]); ?>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty-state">
                No registered users found.
            </div>

        <?php endif; ?>

    </section>

</main>

</body>

</html>
