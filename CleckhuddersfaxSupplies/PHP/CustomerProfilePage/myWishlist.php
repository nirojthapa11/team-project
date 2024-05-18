<?php
// Include the dbConnect.php file
include '../../partials/dbConnect.php';

// Retrieve the customer ID from the GET request
$customerId = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

// Check if customer ID is correctly retrieved
if ($customerId === 0) {
    echo "Error: Customer ID is missing or invalid.";
    exit;
}

// Create a new Database instance
$db = new Database();

// Call the getProductFromWishlist() method to retrieve wishlist items
$wishlistItems = $db->getProductFromWishlist($customerId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wishlist</title>
    <link rel="stylesheet" href="myWishlist.css">
    <link rel="stylesheet" href="../HeaderPage/head.css">
    <link rel="stylesheet" href="../FooterPage/footer.css">
    <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div><?php include('../HeaderPage/head.php');?></div>
    <div class="wrapper">
        <div class="sidebar">
            <ul>
                <li><a href="customerProfile.php"><i class="fas fa-user"></i>My Profile</a></li>
                <li><a href="#"><i class="fas fa-cart-shopping"></i>My Orders</a></li>
                <li><a href="myWishlist.php"><i class="fas fa-heart"></i>My Wishlist</a></li>
                <li><a href="#"><i class="fas fa-money-bill"></i></i>Payment</a></li>
                <li><a href="#"><i class="fas fa-home"></i>Back to Home</a></li>
            </ul> 
        </div>
        <div class="main_content">
            <div class="hr">My Wishlist</div>  
            <table class="table wishlist-table">
                <thead>
                    <tr>
                        <th scope="col">Product Image</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Price</th>
                        <th scope="col">Stock Status</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Category</th>
                        <th scope="col">Shop Name</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wishlistItems as $item): 
                        // Get the base64 image data for the product
                        $product_id = $item['PRODUCT_ID'];
                        $imageBase64 = $db->getProductImage($item['PRODUCT_ID']);
                        var_dump($imageBase64);
                        
                    ?>
                        <tr>
                            <td class="img-container">
                                <?php 
                                if ($imageBase64) {
                                    echo '<img src="data:image/jpeg;base64,' . $imageBase64 . '" alt="' . htmlspecialchars($item['PRODUCT_NAME']) . '" style="width: 100%; height: auto;">';
                                } else {
                                    echo '<img src="../Image/path_to_placeholder_image.jpg" alt="' . htmlspecialchars($item['PRODUCT_NAME']) . ' Image" style="width: 100%; height: auto;">';
                                }
                                ?>
                            </td>
                            <td class="td-product-name"><?php echo htmlspecialchars($item['PRODUCT_NAME']); ?></td>
                            <td class="td-price"><?php echo htmlspecialchars($item['PRICE']); ?></td>
                            <td class="td-stock"><?php echo htmlspecialchars($item['STOCK']); ?></td>
                            <td class="th-quantity">
                                <input type="number" value="1" min="1" class="form-control text-center">
                            </td>
                            <td class="td-category"><?php echo htmlspecialchars($item['CATEGORY_NAME']); ?></td>
                            <td class="td-shop-name"><?php echo htmlspecialchars($item['SHOP_NAME']); ?></td>
                            <td class="td-actions">
                                <button type="button" class="btn btn-danger btn-sm">Remove</button>
                                <button type="button" class="btn btn-primary btn-sm">Add to Cart</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('../FooterPage/footer.php');?>

    <script src="../HeaderPage/head.js"></script>
</body>
</html>
