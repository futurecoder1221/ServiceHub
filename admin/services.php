```php
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

require_once "../includes/db_connect.php";

/** @var mysqli $conn */

$message = "";

/* Delete Service */
if (isset($_GET["delete"])) {

    $id = $_GET["delete"];

    $sql = "DELETE FROM services WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: services.php");
        exit();
    }
}

/* Add Service */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_service"])) {

    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $category = $_POST["category"];
    $status = $_POST["status"];

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

        if (mysqli_stmt_execute($stmt)) {
            $message = "Service added successfully.";
        } else {
            $message = "Error adding service.";
        }

        mysqli_stmt_close($stmt);
    }
}

/* Update Service */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_service"])) {

    $id = $_POST["id"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $category = $_POST["category"];
    $status = $_POST["status"];

    $sql = "UPDATE services
            SET name = ?, description = ?, price = ?, category = ?, status = ?
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

        if (mysqli_stmt_execute($stmt)) {
            $message = "Service updated successfully.";
        } else {
            $message = "Error updating service.";
        }

        mysqli_stmt_close($stmt);
    }
}

/* Get service for editing */
$edit_service = null;

if (isset($_GET["edit"])) {

    $id = $_GET["edit"];

    $sql = "SELECT * FROM services WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $edit_service = mysqli_fetch_assoc($result);
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>ServiceHub - Manage Services</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        .navbar {
            background-color: #222;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .back {
            color: white;
            text-decoration: none;
            background-color: #555;
            padding: 10px 18px;
            border-radius: 5px;
        }

        .container {
            width: 90%;
            margin: 35px auto;
        }

        .box {
            background-color: white;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        h1 {
            margin-top: 0;
        }

        label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            box-sizing: border-box;
        }

        textarea {
            height: 80px;
            resize: vertical;
        }

        button {
            margin-top: 18px;
            padding: 11px 22px;
            background-color: #222;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #444;
        }

        .message {
            color: green;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #222;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .active {
            color: green;
            font-weight: bold;
        }

        .inactive {
            color: red;
            font-weight: bold;
        }

        .edit {
            color: white;
            background-color: #3498db;
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .delete {
            color: white;
            background-color: #e74c3c;
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

    </style>

</head>

<body>

    <div class="navbar">

        <h2>ServiceHub Admin Panel</h2>

        <a href="dashboard.php" class="back">
            Back to Dashboard
        </a>

    </div>

    <div class="container">

        <?php if ($message != "") { ?>

            <div class="box">
                <p class="message"><?php echo $message; ?></p>
            </div>

        <?php } ?>

        <?php if ($edit_service == null) { ?>

            <div class="box">

                <h1>Add New Service</h1>

                <form method="POST">

                    <label>Service Name</label>
                    <input type="text" name="name" required>

                    <label>Description</label>
                    <textarea name="description" required></textarea>

                    <label>Price</label>
                    <input type="number" name="price" step="0.01" required>

                    <label>Category</label>
                    <input type="text" name="category" required>

                    <label>Status</label>

                    <select name="status" required>

                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>

                    </select>

                    <button type="submit" name="add_service">
                        Add Service
                    </button>

                </form>

            </div>

        <?php } else { ?>

            <div class="box">

                <h1>Edit Service</h1>

                <form method="POST">

                    <input type="hidden"
                           name="id"
                           value="<?php echo $edit_service["id"]; ?>">

                    <label>Service Name</label>

                    <input type="text"
                           name="name"
                           value="<?php echo htmlspecialchars($edit_service["name"]); ?>"
                           required>

                    <label>Description</label>

                    <textarea name="description"
                              required><?php echo htmlspecialchars($edit_service["description"]); ?></textarea>

                    <label>Price</label>

                    <input type="number"
                           name="price"
                           step="0.01"
                           value="<?php echo $edit_service["price"]; ?>"
                           required>

                    <label>Category</label>

                    <input type="text"
                           name="category"
                           value="<?php echo htmlspecialchars($edit_service["category"]); ?>"
                           required>

                    <label>Status</label>

                    <select name="status" required>

                        <option value="active"
                            <?php if ($edit_service["status"] == "active") echo "selected"; ?>>
                            Active
                        </option>

                        <option value="inactive"
                            <?php if ($edit_service["status"] == "inactive") echo "selected"; ?>>
                            Inactive
                        </option>

                    </select>

                    <button type="submit" name="update_service">
                        Update Service
                    </button>

                    <a href="services.php"
                       style="margin-left:10px;">
                        Cancel
                    </a>

                </form>

            </div>

        <?php } ?>

        <div class="box">

            <h1>Services List</h1>

            <table>

                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

                <?php

                $sql = "SELECT * FROM services ORDER BY id DESC";

                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {

                    while ($row = mysqli_fetch_assoc($result)) {

                ?>

                <tr>

                    <td><?php echo $row["id"]; ?></td>

                    <td><?php echo htmlspecialchars($row["name"]); ?></td>

                    <td><?php echo htmlspecialchars($row["description"]); ?></td>

                    <td><?php echo htmlspecialchars($row["price"]); ?></td>

                    <td><?php echo htmlspecialchars($row["category"]); ?></td>

                    <td>

                        <?php

                        if ($row["status"] == "active") {
                            echo "<span class='active'>Active</span>";
                        } else {
                            echo "<span class='inactive'>Inactive</span>";
                        }

                        ?>

                    </td>

                    <td>

                        <a class="edit"
                           href="services.php?edit=<?php echo $row["id"]; ?>">
                            Edit
                        </a>

                        <a class="delete"
                           href="services.php?delete=<?php echo $row["id"]; ?>"
                           onclick="return confirm('Are you sure you want to delete this service?');">
                            Delete
                        </a>

                    </td>

                </tr>

                <?php

                    }

                } else {

                    echo "<tr><td colspan='7'>No services found.</td></tr>";

                }

                ?>

            </table>

        </div>

    </div>

</body>

</html>
```

