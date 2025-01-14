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

// Handle Create
if (isset($_POST['create'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);

    $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
    if (!$conn->query($sql)) {
        die("Error adding category: " . $conn->error);
    }
}

// Handle Update
if (isset($_POST['update'])) {
    $category_id = $conn->real_escape_string($_POST['category_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);

    $sql = "UPDATE categories SET name='$name', description='$description' WHERE category_id='$category_id'";
    if (!$conn->query($sql)) {
        die("Error updating category: " . $conn->error);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $category_id = $conn->real_escape_string($_GET['delete']);
    $sql = "DELETE FROM categories WHERE category_id='$category_id'";
    if (!$conn->query($sql)) {
        die("Error deleting category: " . $conn->error);
    }
}

// Fetch all categories
$sql = "SELECT * FROM categories";
$categories_result = $conn->query($sql);

if (!$categories_result) {
    die("Error retrieving categories: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Inventory - Categories</title>
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
        input[type="text"], textarea {
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
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }
        .card h3 {
            margin: 0 0 10px;
            color: #007BFF;
        }
        .card p {
            margin: 0 0 20px;
            color: #555;
        }
        .actions {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
        }
        .btn-icon {
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .btn-icon:hover {
            color: #007BFF;
        }
        .btn-delete {
            color: red;
        }
    </style>
</head>
<body>

<h1>POS Inventory - Categories</h1>

<h2>Add Category</h2>
<form method="POST" action="">
    <input type="text" name="name" placeholder="Category Name" required>
    <textarea name="description" placeholder="Category Description" required></textarea>
    <input type="submit" name="create" value="Add Category">
</form>

<h2>Category List</h2>
<div class="card-container">
    <?php while ($row = $categories_result->fetch_assoc()): ?>
        <div class="card">
            <form method="POST" action="">
                <input type="hidden" name="category_id" value="<?= $row['category_id'] ?>">
                <h3>
                    <input type="text" name="name" value="<?= $row['name'] ?>" disabled style="border: none; background: transparent; color: #007BFF; font-weight: bold;">
                </h3>
                <p>
                    <textarea name="description" disabled style="border: none; background: transparent; color: #555;"><?= $row['description'] ?></textarea>
                </p>
                <div class="actions">
                    <i class="fas fa-edit btn-icon btn-edit"></i>
                    <i class="fas fa-save btn-icon btn-save" style="display: none;"></i>
                    <a class="btn-icon btn-delete" href="?delete=<?= $row['category_id'] ?>"><i class="fas fa-trash"></i></a>
                </div>
            </form>
        </div>
    <?php endwhile; ?>
</div>

<script>
    document.querySelectorAll('.btn-edit').forEach(editBtn => {
        editBtn.addEventListener('click', function () {
            const card = this.closest('.card');
            const inputs = card.querySelectorAll('input, textarea');
            const saveBtn = card.querySelector('.btn-save');
            const editBtn = card.querySelector('.btn-edit');

            // Enable inputs
            inputs.forEach(input => input.disabled = false);

            // Toggle edit and save icons
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline';
        });
    });

    document.querySelectorAll('.btn-save').forEach(saveBtn => {
        saveBtn.addEventListener('click', function () {
            const card = this.closest('.card');
            const form = card.querySelector('form');

            // Submit the form
            form.submit();
        });
    });
</script>

</body>
</html>
