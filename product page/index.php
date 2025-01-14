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

// Fetch categories for dropdown
$categories_result = $conn->query("SELECT * FROM categories");
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[$row['category_id']] = $row['name'];
}

// Handle Create
if (isset($_POST['create'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $stock_quantity = $conn->real_escape_string($_POST['stock_quantity']);
    $category_id = $conn->real_escape_string($_POST['category_id']);

    $sql = "INSERT INTO products (name, description, price, stock_quantity, category_id) 
            VALUES ('$name', '$description', '$price', '$stock_quantity', '$category_id')";
    if (!$conn->query($sql)) {
        die("Error adding product: " . $conn->error);
    }
}

// Handle Update
if (isset($_POST['update'])) {
    $product_id = $conn->real_escape_string($_POST['product_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $stock_quantity = $conn->real_escape_string($_POST['stock_quantity']);
    $category_id = $conn->real_escape_string($_POST['category_id']);

    $sql = "UPDATE products 
            SET name='$name', description='$description', price='$price', stock_quantity='$stock_quantity', category_id='$category_id' 
            WHERE product_id='$product_id'";
    if (!$conn->query($sql)) {
        die("Error updating product: " . $conn->error);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $product_id = $conn->real_escape_string($_GET['delete']);
    $sql = "DELETE FROM products WHERE product_id='$product_id'";
    if (!$conn->query($sql)) {
        die("Error deleting product: " . $conn->error);
    }
}

// Fetch all products
$sql = "SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, c.name AS category_name, p.category_id 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id";
$products_result = $conn->query($sql);

if (!$products_result) {
    die("Error retrieving products: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Inventory - Products</title>
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
        }
        h2 {
            color: #444;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="number"], select, textarea {
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
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<h1>POS Inventory - Products</h1>

<h2>Add Product</h2>
<form method="POST" action="">
    <input type="text" name="name" placeholder="Product Name" required>
    <textarea name="description" placeholder="Product Description" required></textarea>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <input type="number" name="stock_quantity" placeholder="Stock Quantity" required>
    <select name="category_id" required>
        <option value="">Select Category</option>
        <?php foreach ($categories as $category_id => $category_name): ?>
            <option value="<?= $category_id ?>"><?= $category_name ?></option>
        <?php endforeach; ?>
    </select>
    <input type="submit" name="create" value="Add Product">
</form>

<h2>Product List</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $products_result->fetch_assoc()): ?>
            <tr data-id="<?= $row['product_id'] ?>">
                <form method="POST" action="">
                    <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                    <td><?= $row['product_id'] ?></td>
                    <td><input type="text" name="name" value="<?= $row['name'] ?>" disabled></td>
                    <td><input type="text" name="description" value="<?= $row['description'] ?>" disabled></td>
                    <td><input type="number" step="0.01" name="price" value="<?= $row['price'] ?>" disabled></td>
                    <td><input type="number" name="stock_quantity" value="<?= $row['stock_quantity'] ?>" disabled></td>
                    <td>
                        <select name="category_id" disabled>
                            <?php foreach ($categories as $category_id => $category_name): ?>
                                <option value="<?= $category_id ?>" <?= $row['category_id'] == $category_id ? 'selected' : '' ?>>
                                    <?= $category_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <i class="fas fa-edit btn-icon btn-edit"></i>
                        <button type="submit" name="update" class="btn-icon btn-save hidden"><i class="fas fa-save"></i></button>
                        <a class="btn-icon btn-delete" href="?delete=<?= $row['product_id'] ?>"><i class="fas fa-trash"></i></a>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
    document.querySelectorAll('.btn-edit').forEach(editBtn => {
        editBtn.addEventListener('click', function () {
            const row = this.closest('tr');
            const inputs = row.querySelectorAll('input, select');
            const saveBtn = row.querySelector('.btn-save');
            const editBtn = row.querySelector('.btn-edit');

            // Enable inputs
            inputs.forEach(input => input.disabled = false);

            // Show save button, hide edit button
            saveBtn.classList.remove('hidden');
            editBtn.classList.add('hidden');
        });
    });
</script>

</body>
</html>
