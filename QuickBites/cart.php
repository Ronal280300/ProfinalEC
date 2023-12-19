<?php
//Se agrega la conexion hacia la BD
@include 'config.php';

//Flujo que actualiza los valores de los productos en el carrito
if (isset($_POST['update_update_btn'])) {
    $update_value = $_POST['update_quantity'];
    $update_id = $_POST['update_quantity_id'];

    try {
        $update_quantity_query = $conn->prepare("UPDATE cart SET quantity = :update_value WHERE id = :update_id");
        $update_quantity_query->bindParam(':update_value', $update_value, PDO::PARAM_INT);
        $update_quantity_query->bindParam(':update_id', $update_id, PDO::PARAM_INT);
        $update_quantity_query->execute();

        header('location:cart.php');
    } catch (PDOException $e) {
        die('Query failed: ' . $e->getMessage());
    }
}

//Flujo que elimina los valores de los productos en el carrito
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];

    try {
        $remove_query = $conn->prepare("DELETE FROM cart WHERE id = :remove_id");
        $remove_query->bindParam(':remove_id', $remove_id, PDO::PARAM_INT);
        $remove_query->execute();

        header('location:cart.php');
    } catch (PDOException $e) {
        die('Query failed: ' . $e->getMessage());
    }
}

if (isset($_GET['delete_all'])) {
    try {
        $delete_all_query = $conn->query("DELETE FROM cart");
        header('location:cart.php');
    } catch (PDOException $e) {
        die('Query failed: ' . $e->getMessage());
    }
}
?>
<!-- Estructura visual del carrito de compras -->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Carrito de Compras</title>
   <link rel="icon" href="images/carrito.png" type="image/x-icon">

   <!-- font awesome link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
         // Get hacia la base de datos la cual la recorre y muestra los productos en el carrito
         $select_cart = $conn->query("SELECT * FROM cart");
         $rows = $select_cart->fetchAll(PDO::FETCH_ASSOC);
         $grand_total = 0;
         foreach ($rows as $fetch_cart) {
            $price = $fetch_cart['price']; // Toma el precio está en la columna 'price'
            $quantity = $fetch_cart['quantity']; // Toma la cantidad que está en la columna 'quantity'
            $sub_total = $price * $quantity;
            $grand_total += $sub_total;
            ?>
            <tr>
                <td><img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" height="100" alt=""></td>
                <td><?php echo $fetch_cart['name']; ?></td>
                <!--Muesta el precio de cada producto-->
                <td>₡<?php echo number_format($price); ?>/-</td>
                <td>
                    <!--Actualiza la cantidad y el total-->
                    <form action="" method="post">
                        <input type="hidden" name="update_quantity_id" value="<?php echo $fetch_cart['id']; ?>">
                        <input type="number" name="update_quantity" min="1" value="<?php echo $quantity; ?>">
                        <input type="submit" value="Actualizar" name="update_update_btn">
                    </form>
                </td>
                <td>₡<?php echo number_format($sub_total); ?>/-</td>
                <!--Elimina productos en el carrito-->
                <td><a href="cart.php?remove=<?php echo $fetch_cart['id']; ?>" onclick="return confirm('¿eliminar artículo del carrito?')" class="delete-btn"> <i class="fas fa-trash"></i> Eliminar</a></td>
            </tr>
            <?php
        }
         ?>      <!--Muestra el total-->
                <tr class="table-bottom">
                    <td><a href="products.php" class="option-btn" style="margin-top: 0;">Continuar compra</a></td>
                    <td colspan="3">Precio total</td>
                    <td>₡<?php echo number_format($grand_total); ?>/-</td>
                    <!--Muestra el botón que elimina todos los productos que estan en el carrito-->
                    <td><a href="cart.php?delete_all" onclick="return confirm('¿Estás seguro de que quieres eliminar todo?');" class="delete-btn"> <i class="fas fa-trash"></i> Eliminar todo</a></td>
                </tr>
            </tbody>
        </table>
        <div class="checkout-btn">
        <a href="checkout.php" class="btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>">Finalizar Compra</a>
        </div>
    </section>
</div>

<!-- Conexion a JS-->
<script src="js/script.js"></script>

</body>
</html>
