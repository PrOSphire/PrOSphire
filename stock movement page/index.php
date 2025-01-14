<?php
$servername = "localhost";
$username = "root";
$password = ""; // Default password for XAMPP
$dbname = "pos_inventory";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products for dropdown
$products_result = $conn->query("SELECT product_id, name FROM products");
$products = [];
while ($row = $products_result->fetch_assoc()) {
    $products[$row['product_id']] = $row['name'];
}

// Handle Create
if (isset($_POST['create'])) {
    $product_id = $conn->real_escape_string($_POST['product_id']);
    $quantity_changed = $conn->real_escape_string($_POST['quantity_changed']);
    $movement_type = $conn->real_escape_string($_POST['movement_type']);

    $sql = "INSERT INTO stock_movements (product_id, quantity_changed, movement_type)
            VALUES ('$product_id', '$quantity_changed', '$movement_type')";
    if (!$conn->query($sql)) {
        die("Error adding stock movement: " . $conn->error);
    }
}

// Fetch all stock movements
$sql = "SELECT sm.stock_id, sm.product_id, sm.quantity_changed, sm.movement_type, sm.created_at, 
        p.name AS product_name 
        FROM stock_movements sm 
        LEFT JOIN products p ON sm.product_id = p.product_id";
$stock_movements_result = $conn->query($sql);

if (!$stock_movements_result) {
    die("Error retrieving stock movements: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Inventory - Stock Movements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background: #f0f2f5;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="number"], select {
            padding: 10px;
            margin: 5px 0;
            width: calc(100% - 22px);
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
    </style>
</head>
<body>

<h1>POS Inventory - Stock Movements</h1>

<h2>Add Stock Movement</h2>
<form method="POST" action="">
    <select name="product_id" required>
        <option value="">Select Product</option>
        <?php foreach ($products as $product_id => $product_name): ?>
            <option value="<?= $product_id ?>"><?= $product_name ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" name="quantity_changed" placeholder="Quantity Changed" required>
    <select name="movement_type" required>
        <option value="">Select Movement Type</option>
        <option value="sale">Sale</option>
        <option value="restock">Restock</option>
    </select>
    <input type="submit" name="create" value="Add Stock Movement">
</form>

<h2>Stock Movements List</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Quantity Changed</th>
            <th>Movement Type</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $stock_movements_result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['stock_id'] ?></td>
                <td><?= $row['product_name'] ?></td>
                <td><?= $row['quantity_changed'] ?></td>
                <td><?= ucfirst($row['movement_type']) ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
