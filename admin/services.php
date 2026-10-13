
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

require_once "../includes/db_connect.php";

/** @var mysqli $conn */

if (empty($_SESSION["services_csrf"])) {
    $_SESSION["services_csrf"] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION["services_csrf"];
$message = "";
$message_type = "success";
$edit_service = null;

function escape_html($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

/* Handle Add, Update and Delete */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $submitted_token = $_POST["csrf_token"] ?? "";

    if (!hash_equals($csrf_token, $submitted_token)) {
        $message = "Invalid request. Please refresh the page.";
        $message_type = "error";
    } else {

        /* Delete Service */

        if (isset($_POST["delete_service"])) {

            $id = filter_var(
                $_POST["id"] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                $message = "Invalid service ID.";
                $message_type = "error";
            } else {

                $stmt = mysqli_prepare(
                    $conn,
                    "DELETE FROM services WHERE id = ?"
                );

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "i", $id);

                    try {
                        if (mysqli_stmt_execute($stmt)) {
                            $message = mysqli_stmt_affected_rows($stmt) > 0
                                ? "Service deleted successfully."
                                : "Service not found.";
                        } else {
                            $message = "Unable to delete service.";
                            $message_type = "error";
                        }
                    } catch (mysqli_sql_exception $e) {
                        $message = "This service may have existing bookings. It cannot be deleted. You can mark it Inactive instead.";
                        $message_type = "error";
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    $message = "Unable to prepare delete request.";
                    $message_type = "error";
                }
            }
        }

        /* Add or Update Service */

        if (isset($_POST["add_service"]) ||
            isset($_POST["update_service"])) {

            $is_update = isset($_POST["update_service"]);

            $id = $is_update
                ? filter_var(
                    $_POST["id"] ?? null,
                    FILTER_VALIDATE_INT
                )
                : null;

            $name = trim($_POST["name"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $category = trim($_POST["category"] ?? "");
            $price_input = $_POST["price"] ?? "";
            $status = strtolower(trim($_POST["status"] ?? ""));

            if (
                $name === "" ||
                $description === "" ||
                $category === "" ||
                $price_input === "" ||
                !is_numeric($price_input) ||
                !is_finite((float)$price_input) ||
                (float)$price_input < 0 ||
                !in_array($status, ["active", "inactive"], true) ||
                ($is_update && (!$id || $id < 1))
            ) {
                $message = "Please enter valid service details.";
                $message_type = "error";
            } else {

                $price = (float)$price_input;

                if ($is_update) {

                    $sql = "UPDATE services
                            SET name = ?,
                                description = ?,
                                price = ?,
                                category = ?,
                                status = ?
                            WHERE id = ?";

                    $stmt = mysqli_prepare($conn, $sql);

                    if ($stmt) {
                        mysqli_stmt_bind_param(
                            $stmt,
                            "ssdssi",
                            $name,
                            $description,
                            $price,
                            $category,
                            $status,
                            $id
                        );
                    }

                } else {

                    $sql = "INSERT INTO services
                            (name, description, price, category, status)
                            VALUES (?, ?, ?, ?, ?)";

                    $stmt = mysqli_prepare($conn, $sql);

                    if ($stmt) {
                        mysqli_stmt_bind_param(
                            $stmt,
                            "ssdss",
                            $name,
                            $description,
                            $price,
                            $category,
                            $status
                        );
                    }
                }

                if ($stmt) {

                    try {
                        if (mysqli_stmt_execute($stmt)) {

                            $_SESSION["services_flash"] = $is_update
                                ? "Service updated successfully."
                                : "Service added successfully.";

                            mysqli_stmt_close($stmt);

                            header("Location: services.php");
                            exit();

                        } else {
                            $message = "Unable to save service.";
                            $message_type = "error";
                        }
                    } catch (mysqli_sql_exception $e) {
                        $message = "Unable to save service. Please check the details.";
                        $message_type = "error";
                    }

                    mysqli_stmt_close($stmt);

                } else {
                    $message = "Database query error.";
                    $message_type = "error";
                }
            }
        }
    }
}

/* Success message after redirect */

if (isset($_SESSION["services_flash"])) {
    $message = $_SESSION["services_flash"];
    $message_type = "success";
    unset($_SESSION["services_flash"]);
}

/* Load service for editing */

if (isset($_GET["edit"])) {

    $id = filter_var(
        $_GET["edit"],
        FILTER_VALIDATE_INT
    );

    if ($id && $id > 0) {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT * FROM services WHERE id = ?"
        );

        if ($stmt) {

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $edit_service = mysqli_fetch_assoc($result) ?: null;

            mysqli_stmt_close($stmt);
        }
    }

    if (!$edit_service) {
        $message = "Service not found.";
        $message_type = "error";
    }
}

/* Load all services */

$services = [];
$result = mysqli_query(
    $conn,
    "SELECT * FROM services ORDER BY id DESC"
);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $services[] = $row;
    }
}

$total_services = count($services);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Services | ServiceHub</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* NAVBAR */

        .navbar {
            background: #111827;
            color: white;
            padding: 18px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .brand {
            font-size: 23px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .brand span {
            color: #38bdf8;
        }

        .nav-actions {
            display: flex;
            gap: 10px;
        }

        .nav-link {
            display: inline-block;
            color: white;
            background: #334155;
            text-decoration: none;
            padding: 11px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        .nav-link:hover {
            background: #475569;
        }

        .logout {
            background: #dc2626;
        }

        .logout:hover {
            background: #b91c1c;
        }

        /* PAGE */

        .container {
            width: 92%;
            max-width: 1250px;
            margin: 35px auto 60px;
        }

        .page-heading {
            background: linear-gradient(
                135deg,
                #111827,
                #1e3a5f
            );
            color: white;
            padding: 32px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .page-heading h1 {
            margin: 0 0 10px;
            font-size: 29px;
        }

        .page-heading p {
            margin: 0;
            color: #cbd5e1;
            line-height: 1.6;
        }

        .panel {
            background: white;
            padding: 28px;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 18px rgba(15,23,42,0.04);
        }

        .panel-title {
            font-size: 23px;
            color: #0f172a;
            margin: 0 0 22px;
        }

        /* FORM */

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0,1fr));
            gap: 20px;
        }

        .form-group {
            min-width: 0;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 9px;
        }

        input,
        textarea,
        select {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            padding: 13px 14px;
            font-size: 16px;
            font-family: Arial, sans-serif;
            background: white;
            color: #1e293b;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2,132,199,0.12);
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
        }

        .btn {
            display: inline-block;
            padding: 13px 20px;
            border-radius: 9px;
            border: none;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .btn-primary {
            background: #0284c7;
            color: white;
        }

        .btn-primary:hover {
            background: #0369a1;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .btn-edit {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .btn-delete {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* MESSAGES */

        .message {
            padding: 16px 18px;
            border-radius: 10px;
            margin-bottom: 22px;
            font-size: 14px;
            font-weight: bold;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* SERVICES TABLE */

        .list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .list-header h2 {
            margin: 0;
            font-size: 23px;
        }

        .count {
            background: #e0f2fe;
            color: #0369a1;
            padding: 9px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #111827;
            color: white;
            padding: 15px;
            text-align: left;
            font-size: 13px;
            white-space: nowrap;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            vertical-align: middle;
            overflow-wrap: anywhere;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .description-cell {
            max-width: 260px;
            line-height: 1.5;
        }

        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #fee2e2;
            color: #b91c1c;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .actions .btn {
            padding: 9px 13px;
            font-size: 13px;
        }

        .delete-form {
            margin: 0;
        }

        .empty-state {
            text-align: center;
            padding: 35px 15px;
            color: #64748b;
        }

        /* MOBILE CARDS */

        .mobile-services {
            display: none;
        }

        .service-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 13px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .service-card h3 {
            margin: 0 0 12px;
            font-size: 19px;
            color: #0f172a;
            overflow-wrap: anywhere;
        }

        .service-info {
            margin: 12px 0;
            font-size: 14px;
            line-height: 1.6;
            overflow-wrap: anywhere;
        }

        .service-info strong {
            color: #475569;
        }

        .service-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 20px;
        }

        .service-actions .btn,
        .service-actions .delete-form,
        .service-actions .delete-form button {
            width: 100%;
        }

        /* RESPONSIVE */

        @media (max-width: 768px) {

            .navbar {
                flex-wrap: wrap;
                padding: 17px 5%;
            }

            .brand {
                font-size: 22px;
            }

            .container {
                width: 94%;
                margin-top: 22px;
            }

            .page-heading {
                padding: 25px 20px;
            }

            .page-heading h1 {
                font-size: 25px;
            }

            .panel {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 17px;
            }

            .full-width {
                grid-column: auto;
            }

            .desktop-table {
                display: none;
            }

            .mobile-services {
                display: block;
            }

        }

        @media (max-width: 480px) {

            .navbar {
                flex-direction: column;
                align-items: stretch;
            }

            .brand {
                text-align: center;
            }

            .nav-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .nav-link {
                padding: 13px 9px;
                font-size: 13px;
            }

            .page-heading h1 {
                font-size: 23px;
            }

            .panel {
                padding: 17px;
            }

            .panel-title,
            .list-header h2 {
                font-size: 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
            }

            .service-card {
                padding: 17px;
            }

        }

    </style>

</head>

<body>

    <!-- NAVBAR -->

    <nav class="navbar">

        <a href="dashboard.php" class="brand">
            Service<span>Hub</span> Admin
        </a>

        <div class="nav-actions">

            <a href="dashboard.php" class="nav-link">
                Dashboard
            </a>

            <a href="logout.php" class="nav-link logout">
                Logout
            </a>

        </div>

    </nav>

    <main class="container">

        <!-- PAGE HEADING -->

        <section class="page-heading">

            <h1>Manage Services</h1>

            <p>
                Add new services, update existing details,
                and manage their availability.
            </p>

        </section>

        <?php if ($message !== ""): ?>

            <div class="message <?php echo escape_html($message_type); ?>">
                <?php echo escape_html($message); ?>
            </div>

        <?php endif; ?>

        <!-- ADD / EDIT FORM -->

        <section class="panel">

            <h2 class="panel-title">
                <?php echo $edit_service ? "Edit Service" : "Add New Service"; ?>
            </h2>

            <form method="POST" action="services.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php echo escape_html($csrf_token); ?>"
                >

                <?php if ($edit_service): ?>

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo (int)$edit_service["id"]; ?>"
                    >

                <?php endif; ?>

                <div class="form-grid">

                    <div class="form-group">

                        <label for="name">Service Name</label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            maxlength="255"
                            value="<?php echo escape_html($edit_service["name"] ?? ""); ?>"
                            placeholder="e.g. Web Development"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="category">Category</label>

                        <input
                            type="text"
                            id="category"
                            name="category"
                            maxlength="255"
                            value="<?php echo escape_html($edit_service["category"] ?? ""); ?>"
                            placeholder="e.g. Development"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="price">Price (PKR)</label>

                        <input
                            type="number"
                            id="price"
                            name="price"
                            min="0"
                            step="0.01"
                            value="<?php echo escape_html($edit_service["price"] ?? ""); ?>"
                            placeholder="Enter service price"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="status">Status</label>

                        <?php
                            $selected_status = strtolower(
                                $edit_service["status"] ?? "active"
                            );
                        ?>

                        <select id="status" name="status" required>

                            <option
                                value="active"
                                <?php echo $selected_status === "active" ? "selected" : ""; ?>
                            >
                                Active
                            </option>

                            <option
                                value="inactive"
                                <?php echo $selected_status === "inactive" ? "selected" : ""; ?>
                            >
                                Inactive
                            </option>

                        </select>

                    </div>

                    <div class="form-group full-width">

                        <label for="description">Description</label>

                        <textarea
                            id="description"
                            name="description"
                            placeholder="Describe the service..."
                            required
                        ><?php echo escape_html($edit_service["description"] ?? ""); ?></textarea>

                    </div>

                </div>

                <div class="form-actions">

                    <?php if ($edit_service): ?>

                        <button
                            type="submit"
                            name="update_service"
                            value="1"
                            class="btn btn-primary"
                        >
                            Save Changes
                        </button>

                        <a href="services.php" class="btn btn-secondary">
                            Cancel Editing
                        </a>

                    <?php else: ?>

                        <button
                            type="submit"
                            name="add_service"
                            value="1"
                            class="btn btn-primary"
                        >
                            + Add Service
                        </button>

                    <?php endif; ?>

                </div>

            </form>

        </section>

        <!-- SERVICES LIST -->

        <section class="panel">

            <div class="list-header">

                <h2>Services List</h2>

                <span class="count">
                    <?php echo $total_services; ?> Total
                </span>

            </div>

            <?php if ($total_services === 0): ?>

                <div class="empty-state">
                    No services found. Add your first service above.
                </div>

            <?php else: ?>

                <!-- DESKTOP TABLE -->

                <div class="desktop-table table-wrap">

                    <table>

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($services as $service): ?>

                                <?php
                                    $id = (int)$service["id"];
                                    $is_active = strtolower(
                                        $service["status"]
                                    ) === "active";
                                ?>

                                <tr>

                                    <td><?php echo $id; ?></td>

                                    <td>
                                        <strong>
                                            <?php echo escape_html($service["name"]); ?>
                                        </strong>
                                    </td>

                                    <td class="description-cell">
                                        <?php echo escape_html($service["description"]); ?>
                                    </td>

                                    <td>
                                        Rs. <?php echo number_format((float)$service["price"], 2); ?>
                                    </td>

                                    <td>
                                        <?php echo escape_html($service["category"]); ?>
                                    </td>

                                    <td>

                                        <span class="status <?php echo $is_active ? "status-active" : "status-inactive"; ?>">
                                            <?php echo $is_active ? "Active" : "Inactive"; ?>
                                        </span>

                                    </td>

                                    <td>

                                        <div class="actions">

                                            <a
                                                href="services.php?edit=<?php echo $id; ?>"
                                                class="btn btn-edit"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="services.php"
                                                class="delete-form"
                                                onsubmit="return confirm('Are you sure you want to delete this service?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?php echo escape_html($csrf_token); ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?php echo $id; ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    name="delete_service"
                                                    value="1"
                                                    class="btn btn-delete"
                                                >
                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <!-- MOBILE SERVICE CARDS -->

                <div class="mobile-services">

                    <?php foreach ($services as $service): ?>

                        <?php
                            $id = (int)$service["id"];
                            $is_active = strtolower(
                                $service["status"]
                            ) === "active";
                        ?>

                        <article class="service-card">

                            <h3>
                                <?php echo escape_html($service["name"]); ?>
                            </h3>

                            <span class="status <?php echo $is_active ? "status-active" : "status-inactive"; ?>">
                                <?php echo $is_active ? "Active" : "Inactive"; ?>
                            </span>

                            <p class="service-info">
                                <strong>Service ID:</strong>
                                <?php echo $id; ?>
                            </p>

                            <p class="service-info">
                                <strong>Category:</strong>
                                <?php echo escape_html($service["category"]); ?>
                            </p>

                            <p class="service-info">
                                <strong>Price:</strong>
                                Rs. <?php echo number_format((float)$service["price"], 2); ?>
                            </p>

                            <p class="service-info">
                                <strong>Description:</strong>
                                <?php echo escape_html($service["description"]); ?>
                            </p>

                            <div class="service-actions">

                                <a
                                    href="services.php?edit=<?php echo $id; ?>"
                                    class="btn btn-edit"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="services.php"
                                    class="delete-form"
                                    onsubmit="return confirm('Are you sure you want to delete this service?');"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?php echo escape_html($csrf_token); ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo $id; ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="delete_service"
                                        value="1"
                                        class="btn btn-delete"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

</body>

</html>


