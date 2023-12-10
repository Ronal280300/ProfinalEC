<?php
@include 'config.php';

if (isset($_POST['update_update_btn'])) {
   $update_value = $_POST['update_quantity'];
   $update_id = $_POST['update_quantity_id'];
   $update_quantity_query = mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_value' WHERE id = '$update_id'");
   if ($update_quantity_query) {
      header('location:cart.php');
   }
}

if (isset($_GET['remove'])) {
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'");
   header('location:cart.php');
}

if (isset($_GET['delete_all'])) {
   mysqli_query($conn, "DELETE FROM `cart`");
   header('location:cart.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Carrito de Compras</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <section class="shopping-cart">
        <h1 class="heading">Carrito de Compras</h1>
        <table>
            <thead>
                <th>imagen</th>
                <th>nombre</th>
                <th>precio</th>
                <th>cantidad</th>
                <th>precio total</th>
                <th>acción</th>
            </thead>
            <tbody>
                <?php
                $select_cart = mysqli_query($conn, "SELECT * FROM `cart`");
                $grand_total = 0;
                if (mysqli_num_rows($select_cart) > 0) {
                    while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                        $price = floatval($fetch_cart['price']);
                        $quantity = intval($fetch_cart['quantity']);
                        $sub_total = $price * $quantity;
                        $grand_total += $sub_total;
                ?>
                        <tr>
                            <td><img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" height="100" alt=""></td>
                            <td><?php echo $fetch_cart['name']; ?></td>
                            <td>₡<?php echo number_format($price); ?>/-</td>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" name="update_quantity_id" value="<?php echo $fetch_cart['id']; ?>">
                                    <input type="number" name="update_quantity" min="1" value="<?php echo $quantity; ?>">
                                    <input type="submit" value="Actualizar" name="update_update_btn">
                                </form>
                            </td>
                            <td>₡<?php echo number_format($sub_total); ?>/-</td>
                            <td><a href="cart.php?remove=<?php echo $fetch_cart['id']; ?>" onclick="return confirm('¿eliminar artículo del carrito?')" class="delete-btn"> <i class="fas fa-trash"></i> Eliminar</a></td>
                        </tr>
                <?php
                    }
                }
                ?>
                <tr class="table-bottom">
                    <td><a href="products.php" class="option-btn" style="margin-top: 0;">Continuar compra</a></td>
                    <td colspan="3">Precio total</td>
                    <td>₡<?php echo number_format($grand_total); ?>/-</td>
                    <td><a href="cart.php?delete_all" onclick="return confirm('¿Estás seguro de que quieres eliminar todo?');" class="delete-btn"> <i class="fas fa-trash"></i> Eliminar todo</a></td>
                </tr>
            </tbody>
        </table>
        <div class="checkout-btn">
            <a href="checkout.php" class="btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>">Finalizar Compra</a>
        </div>
    </section>
</div>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
