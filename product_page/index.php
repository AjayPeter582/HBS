<?php
session_start();

$products = json_decode(file_get_contents('products.json'), true);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['favourites'])) {
    $_SESSION['favourites'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product = array_values(array_filter($products, fn($p) => $p['_id'] == $product_id))[0] ?? null;
    
    if ($product) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1; 
        } else {
            $_SESSION['cart'][$product_id] = $product;
            $_SESSION['cart'][$product_id]['quantity'] = 1; 
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';

    if (isset($_POST['increase_quantity']) && isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    }

    if (isset($_POST['decrease_quantity']) && isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] -= 1;
        if ($_SESSION['cart'][$product_id]['quantity'] <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }
    }

    if (isset($_POST['remove_from_cart']) && isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_fav'])) {
    $product_id = $_POST['product_id'];
    if (!in_array($product_id, $_SESSION['favourites'])) {
        $_SESSION['favourites'][] = $product_id;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Page</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        nav {
            background-color: #333;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav button, nav a {
            background-color: #ff6600;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }
        .container {
            display: flex;
            padding: 20px;
            gap: 20px;
        }
        .products {
            flex: 3;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .product {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .product img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .product button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            margin: 5px;
        }
        .cart {
            flex: 1;
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            min-width: 250px;
            max-width: 300px;
            height: fit-content;
        }
        .cart button {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <nav>
        <button>Cart (<?php echo count($_SESSION['cart']); ?>)</button>
        <a href="favourite.php">Favourites</a>
    </nav>
    <div class="container">
        <div class="products">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <h3><?php echo $product['name']; ?></h3>
                    <p><?php echo $product['description']; ?></p>
                    <p>Price: $<?php echo $product['price']; ?> </p>
                    <p>Color: <?php echo $product['color']; ?></p>
                    <p>Rating: <?php echo $product['rating']; ?>/5</p>
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['_id']; ?>">
                        <button type="submit" name="add_to_cart">Add to Cart</button>
                        <button type="submit" name="add_to_fav">Add to Favourites</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart" id="cart">
            <h2>Cart</h2>
            <ul>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <li>
                            <?php echo htmlspecialchars($item['name']); ?> - 
                            $<?php echo htmlspecialchars($item['price'] * $item['quantity']); ?>
                            (<?php echo isset($item['quantity']) ? $item['quantity'] : 1; ?>)
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $item['_id']; ?>">
                                <button type="submit" name="increase_quantity">+</button>
                                <button type="submit" name="decrease_quantity">-</button>
                                <button type="submit" name="remove_from_cart">Remove</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No items in cart</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>
