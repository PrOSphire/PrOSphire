<?php
// Define database connection variables
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

// Fetch users and products for dropdowns
$users_result = $conn->query("SELECT user_id, username FROM users");
$users = [];
while ($row = $users_result->fetch_assoc()) {
    $users[$row['user_id']] = $row['username'];
}

$products_result = $conn->query("SELECT product_id, name FROM products");
$products = [];
while ($row = $products_result->fetch_assoc()) {
    $products[$row['product_id']] = $row['name'];
}

// Handle Create
if (isset($_POST['create'])) {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $product_id = $conn->real_escape_string($_POST['product_id']);
    $quantity = $conn->real_escape_string($_POST['quantity']);

    $sql = "INSERT INTO cart (user_id, product_id, quantity) 
            VALUES ('$user_id', '$product_id', '$quantity')";
    if (!$conn->query($sql)) {
        die("Error adding to cart: " . $conn->error);
    }
}

// Fetch cart items
$sql = "SELECT c.cart_id, c.user_id, c.product_id, c.quantity, u.username, p.name AS product_name 
        FROM cart c 
        LEFT JOIN users u ON c.user_id = u.user_id 
        LEFT JOIN products p ON c.product_id = p.product_id";
$cart_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Inventory - Cart</title>
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

<h1>POS Inventory - Cart Management</h1>

<h2>Add Item to Cart</h2>
<form method="POST" action="">
    <select name="user_id" required>
        <option value="">Select User</option>
        <?php foreach ($users as $user_id => $username): ?>
            <option value="<?= $user_id ?>"><?= $username ?></option>
        <?php endforeach; ?>
    </select>
    <select name="product_id" required>
        <option value="">Select Product</option>
        <?php foreach ($products as $product_id => $product_name): ?>
            <option value="<?= $product_id ?>"><?= $product_name ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" name="quantity" placeholder="Quantity" required>
    <input type="submit" name="create" value="Add to Cart">
</form>

<h2>Cart Items</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Product</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $cart_result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['cart_id'] ?></td>
                <td><?= $row['username'] ?></td>
                <td><?= $row['product_name'] ?></td>
                <td><?= $row['quantity'] ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
