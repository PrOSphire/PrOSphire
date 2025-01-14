<?php
$servername = "localhost";
$username = "root";
$password = ""; // Default password for XAMPP
$dbname = "pos_system";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Update
if (isset($_POST['update'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $price = $conn->real_escape_string($_POST['price']);
    $quantity = $conn->real_escape_string($_POST['quantity']);

    $sql = "UPDATE products SET name='$name', price='$price', quantity='$quantity' WHERE id=$id";
    if (!$conn->query($sql)) {
        die("Error updating product: " . $conn->error);
    }
}

// Handle Create
if (isset($_POST['create'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $price = $conn->real_escape_string($_POST['price']);
    $quantity = $conn->real_escape_string($_POST['quantity']);

    $sql = "INSERT INTO products (name, price, quantity) VALUES ('$name', '$price', '$quantity')";
    if (!$conn->query($sql)) {
        die("Error adding product: " . $conn->error);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $conn->real_escape_string($_GET['delete']);
    $sql = "DELETE FROM products WHERE id=$id";
    if (!$conn->query($sql)) {
        die("Error deleting product: " . $conn->error);
    }
}

// Fetch all products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if (!$result) {
    die("Error retrieving products: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
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
        input[type="text"], input[type="number"] {
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
            animation: fadeIn 1s;
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
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @media (max-width: 768px) {
            table {
                font-size: 0.9rem;
            }
            input[type="text"], input[type="number"], input[type="submit"] {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<h1>Point of Sale - Product Management</h1>

<h2>Add Product</h2>
<form method="POST" action="">
    <input type="text" name="name" placeholder="Product Name" required>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <input type="number" name="quantity" placeholder="Quantity" required>
    <input type="submit" name="create" value="Add Product">
</form>

<h2>Product List</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr data-id="<?= $row['id'] ?>">
                <td><?= $row['id'] ?></td>
                <td contenteditable="false"><?= $row['name'] ?></td>
                <td contenteditable="false"><?= $row['price'] ?></td>
                <td contenteditable="false"><?= $row['quantity'] ?></td>
                <td>
                    <i class="fas fa-edit btn-icon btn-edit"></i>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="name">
                        <input type="hidden" name="price">
                        <input type="hidden" name="quantity">
                        <button type="submit" name="update" class="btn-icon btn-save" style="display:none;"><i class="fas fa-save"></i></button>
                    </form>
                    <a class="btn-icon btn-delete" href="?delete=<?= $row['id'] ?>"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            row.querySelectorAll('td[contenteditable="false"]').forEach(cell => cell.setAttribute('contenteditable', 'true'));
            row.querySelector('.btn-edit').style.display = 'none';
            row.querySelector('.btn-save').style.display = 'inline';
        });
    });

    document.querySelectorAll('.btn-save').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const name = row.querySelector('td:nth-child(2)').innerText;
            const price = row.querySelector('td:nth-child(3)').innerText;
            const quantity = row.querySelector('td:nth-child(4)').innerText;

            const form = this.closest('form');
            form.querySelector('input[name="name"]').value = name;
            form.querySelector('input[name="price"]').value = price;
            form.querySelector('input[name="quantity"]').value = quantity;
        });
    });
</script>

</body>
</html>
