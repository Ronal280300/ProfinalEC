<?php
@include 'config.php';

if (isset($_POST['order_btn'])) {

    $name = $_POST['name'];
    $number = $_POST['number'];
    $email = $_POST['email'];
    $method = $_POST['method'];
    $flat = $_POST['flat'];


    $cart_query = mysqli_query($conn, "SELECT * FROM `cart`");
    $price_total = 0;
    if (mysqli_num_rows($cart_query) > 0) {
        while ($product_item = mysqli_fetch_assoc($cart_query)) {
            $product_name[] = $product_item['name'] . ' (' . $product_item['quantity'] . ') ';
            $product_price = $product_item['price'] * $product_item['quantity'];
            $price_total += $product_price;
        }
    }

    $total_product = implode(', ', $product_name);
    $detail_query = mysqli_query($conn, "INSERT INTO `order`(name, number, email, method, flat, total_products, total_price) VALUES('$name','$number','$email','$method','$flat','$total_product','$price_total')") or die('query failed');

    if ($cart_query && $detail_query) {
        echo "
        <div class='order-message-container'>
        <div class='message-container'>
           <h3>¡Gracias por su compra!</h3>
           <div class='order-detail'>
              <span>" . $total_product . "</span>
              <span class='total'>total: ₡" . number_format($price_total) . "/-</span>
           </div>
           <div class='customer-details'>
              <p>Nombre cliente: <span>" . $name . "</span></p>
              <p>Teléfono: <span>" . $number . "</span></p>
              <p>Email: <span>" . $email . "</span></p>
              <p>Dirección: <span>" . $flat . "</span></p>
              <p>Método de pago: <span>" . $method . "</span></p>
           </div>
              <a href='products.php' class='btn'>continuar comprando</a>
           </div>
        </div>
        ";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Factura</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'header.php'; ?>

<div class="container">

<section class="checkout-form">

   <h1 class="heading">Completar orden</h1>

   <form action="" method="post">

   <div class="display-order">
      <?php
         $select_cart = mysqli_query($conn, "SELECT * FROM `cart`");
         $total = 0;
         $grand_total = 0; // Inicializa $grand_total a 0
         if (mysqli_num_rows($select_cart) > 0) {
             while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                 $total_price = $fetch_cart['price'] * $fetch_cart['quantity'];
                 $grand_total += $total_price;
             
      ?>
      <span><?= $fetch_cart['name']; ?>(<?= $fetch_cart['quantity']; ?>)</span>
      <?php
         }
      }else{
         echo "<div class='display-order'><span>your cart is empty!</span></div>";
      }
      ?>
      <span class="grand-total"> Monto total: $<?= $grand_total; ?>/- </span>
   </div>

      <div class="flex">
         <div class="inputBox">
            <span >Nombre</span>
            <input type="text" placeholder="Ingrese su nombre" name="name" required>
         </div>
         <div class="inputBox">
            <span>Teléfono</span>
            <input type="number" placeholder="Ingrese su número de teléfono" name="number" required>
         </div>
         <div class="inputBox">
            <span>Correo Electrónico</span>
            <input type="email" placeholder="Ingrese su correo" name="email" required>
         </div>
         <div class="inputBox">
            <span>Método de pago</span>
            <select name="method">
               <option value="Efectivo" selected>Efectivo</option>
               <option value="Tarjeta crédito/debito">Tarjeta crédito/debito</option>
               <option value="Paypal">Paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Direccion 1</span>
            <input type="text" placeholder="Ingrese su dirección" name="flat" required>
      </div>
      <input type="submit" value="Completar orden" name="order_btn" class="btn">
   </form>

</section>

</div>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>