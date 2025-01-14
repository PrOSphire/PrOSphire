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

// Fetch transactions for dropdown
$transactions_result = $conn->query("SELECT transaction_id, payment_method, status FROM transactions");
$transactions = [];
while ($row = $transactions_result->fetch_assoc()) {
    $transactions[] = [
        'transaction_id' => $row['transaction_id'],
        'payment_method' => $row['payment_method'],
        'status' => $row['status'],
    ];
}

// Handle Create
if (isset($_POST['create'])) {
    $transaction_id = $conn->real_escape_string($_POST['transaction_id']);
    $product_id = $conn->real_escape_string($_POST['product_id']);
    $quantity = $conn->real_escape_string($_POST['quantity']);
    $unit_price = $conn->real_escape_string($_POST['unit_price']);

    $sql = "INSERT INTO transaction_details (transaction_id, product_id, quantity, unit_price)
            VALUES ('$transaction_id', '$product_id', '$quantity', '$unit_price')";
    if (!$conn->query($sql)) {
        die("Error adding transaction detail: " . $conn->error);
    }
}

// Handle Update
if (isset($_POST['update'])) {
    $transaction_detail_id = $conn->real_escape_string($_POST['transaction_detail_id']);
    $transaction_id = $conn->real_escape_string($_POST['transaction_id']);
    $product_id = $conn->real_escape_string($_POST['product_id']);
    $quantity = $conn->real_escape_string($_POST['quantity']);
    $unit_price = $conn->real_escape_string($_POST['unit_price']);

    $sql = "UPDATE transaction_details 
            SET transaction_id='$transaction_id', product_id='$product_id', quantity='$quantity', unit_price='$unit_price'
            WHERE transaction_detail_id='$transaction_detail_id'";
    if (!$conn->query($sql)) {
        die("Error updating transaction detail: " . $conn->error);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $transaction_detail_id = $conn->real_escape_string($_GET['delete']);
    $sql = "DELETE FROM transaction_details WHERE transaction_detail_id='$transaction_detail_id'";
    if (!$conn->query($sql)) {
        die("Error deleting transaction detail: " . $conn->error);
    }
}

// Fetch all transaction details
$sql = "SELECT td.transaction_detail_id, td.transaction_id, td.product_id, td.quantity, td.unit_price, td.subtotal, 
        p.name AS product_name 
        FROM transaction_details td 
        LEFT JOIN products p ON td.product_id = p.product_id";
$transaction_details_result = $conn->query($sql);

if (!$transaction_details_result) {
    die("Error retrieving transaction details: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Inventory - Transaction Details</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        input[type="text"], input[type="number"], select {
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
        td {
            background-color: #fff;
        }
        .btn-icon {
            color: #333;
            text-decoration: none;
            font-size: 1.2rem;
            margin: 0 5px;
            cursor: pointer;
        }
        .btn-icon:hover {
            transform: scale(1.2);
        }
        .btn-delete {
            color: red;
        }
        .btn-edit {
            color: #007BFF;
        }
    </style>
</head>
<body>

<h1>POS Inventory - Transaction Details</h1>

<h2>Add Transaction Detail</h2>
<form method="POST" action="">
    <select name="transaction_id" required>
        <option value="">Select Transaction</option>
        <?php if (!empty($transactions)): ?>
            <?php foreach ($transactions as $transaction): ?>
                <option value="<?= $transaction['transaction_id'] ?>">
                    <?= "ID: {$transaction['transaction_id']} - {$transaction['payment_method']} ({$transaction['status']})" ?>
                </option>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="">No Transactions Available</option>
        <?php endif; ?>
    </select>
    <select name="product_id" required>
        <option value="">Select Product</option>
        <?php foreach ($products as $product_id => $product_name): ?>
            <option value="<?= $product_id ?>"><?= $product_name ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" name="quantity" placeholder="Quantity" required>
    <input type="number" step="0.01" name="unit_price" placeholder="Unit Price" required>
    <input type="submit" name="create" value="Add Transaction Detail">
</form>

<h2>Transaction Details List</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Transaction ID</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Subtotal</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $transaction_details_result->fetch_assoc()): ?>
            <tr>
                <form method="POST" action="">
                    <input type="hidden" name="transaction_detail_id" value="<?= $row['transaction_detail_id'] ?>">
                    <td><?= $row['transaction_detail_id'] ?></td>
                    <td>
                        <select name="transaction_id">
                            <?php foreach ($transactions as $transaction): ?>
                                <option value="<?= $transaction['transaction_id'] ?>" <?= $row['transaction_id'] == $transaction['transaction_id'] ? 'selected' : '' ?>>
                                    <?= "ID: {$transaction['transaction_id']} - {$transaction['payment_method']} ({$transaction['status']})" ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="product_id">
                            <?php foreach ($products as $product_id => $product_name): ?>
                                <option value="<?= $product_id ?>" <?= $row['product_id'] == $product_id ? 'selected' : '' ?>>
                                    <?= $product_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="number" name="quantity" value="<?= $row['quantity'] ?>" required></td>
                    <td><input type="number" step="0.01" name="unit_price" value="<?= $row['unit_price'] ?>" required></td>
                    <td><?= $row['subtotal'] ?></td>
                    <td>
                        <button type="submit" name="update" class="btn-icon"><i class="fas fa-save"></i></button>
                        <a class="btn-icon btn-delete" href="?delete=<?= $row['transaction_detail_id'] ?>"><i class="fas fa-trash"></i></a>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
