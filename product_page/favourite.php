<?php
session_start();

$products = json_decode(file_get_contents('products.json'), true);

if (!isset($_SESSION['favourites'])) {
    $_SESSION['favourites'] = [];
}

$favourite_products = array_filter($products, fn($p) => in_array($p['_id'], $_SESSION['favourites']));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_fav'])) {
    $product_id = $_POST['product_id'];
    $_SESSION['favourites'] = array_diff($_SESSION['favourites'], [$product_id]); 
    header("Location: favourite.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favourites</title>
    <style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

nav {
    background: #333;
    padding: 10px;
    text-align: center;
}

nav a {
    color: white;
    text-decoration: none;
    font-size: 18px;
    font-weight: bold;
    transition: color 0.3s;
}

nav a:hover {
    color: #ffcc00;
}

.container {
    width: 90%;
    max-width: 1000px;
    margin: 20px auto;
    text-align: center;
}

h2 {
    color: #333;
    margin-bottom: 20px;
}

.products {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    justify-content: center;
}

.product {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease-in-out;
}

.product:hover {
    transform: translateY(-5px);
}

.product img {
    width: 100%;
    height: auto;
    border-radius: 5px;
}

.product h3 {
    color: #333;
    font-size: 20px;
    margin: 10px 0;
}

.product p {
    color: #666;
    font-size: 16px;
    margin: 5px 0;
}

button {
    background: #d9534f;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}

button:hover {
    background: #c9302c;
}

    </style>
</head>
<body>
    <nav>
        <a href="index.php">Back to Products</a>
    </nav>
    <div class="container">
        <h2>Your Favourites</h2>
        <div class="products">
            <?php if (!empty($favourite_products)): ?>
                <?php foreach ($favourite_products as $item): ?>
                    <div class="product">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                        <h3><?php echo $item['name']; ?></h3>
                        <p><?php echo $item['description']; ?></p>
                        <p>Price: $<?php echo $item['price']; ?></p>
                        <p>Color: <?php echo $item['color']; ?></p>
                        <p>Rating: <?php echo $item['rating']; ?>/5</p>
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $item['_id']; ?>">
                            <button type="submit" name="remove_fav">Remove from Favourites</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No favourite items.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
