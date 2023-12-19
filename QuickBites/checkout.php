<?php
//Conexion a la base de datos
@include 'config.php';

//Hace el flujo a la base de datos para guardar los campos del form
if (isset($_POST['order_btn'])) {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $email = $_POST['email'];
    $method = $_POST['method'];
    $flat = $_POST['flat'];

    //Obtiene los productos finales del cliente a facturar
    $cart_query = $conn->query("SELECT * FROM cart");
    $price_total = 0;
    $product_name = array();

    if ($cart_query->rowCount() > 0) {
        while ($product_item = $cart_query->fetch(PDO::FETCH_ASSOC)) {
            $product_name[] = $product_item['name'] . ' (' . $product_item['quantity'] . ') ';
            $product_price = $product_item['price'] * $product_item['quantity'];
            $price_total += $product_price;
        }
    }
    //Se concatena el total con el nombre de los productos
    $total_product = implode(', ', $product_name);

    //Se hace la inserción de los datos en la tabla orden
    try {
        $detail_query = $conn->prepare("INSERT INTO orden (name, number, email, method, flat, total_products, total_price) VALUES (:name, :number, :email, :method, :flat, :total_product, :price_total)");
        $detail_query->bindParam(':name', $name, PDO::PARAM_STR);
        $detail_query->bindParam(':number', $number, PDO::PARAM_STR);
        $detail_query->bindParam(':email', $email, PDO::PARAM_STR);
        $detail_query->bindParam(':method', $method, PDO::PARAM_STR);
        $detail_query->bindParam(':flat', $flat, PDO::PARAM_STR);
        $detail_query->bindParam(':total_product', $total_product, PDO::PARAM_STR);
        $detail_query->bindParam(':price_total', $price_total, PDO::PARAM_STR);
        $detail_query->execute();
    } catch (PDOException $e) {
        die('Query failed: ' . $e->getMessage());
    }
}
   //Muestra la factura finales con los campos guardados del cliente más los productos
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
?>

<!--Estructura visual del apartado de la facturación-->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Factura</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="icon" href="images/carrito.png" type="image/x-icon">

</head>
<body>

<?php include 'header.php'; ?>

<div class="container">

<section class="checkout-form">

   <h1 class="heading">Completar orden</h1>

   <form action="" method="post">

   <div class="display-order">

<?php
//Consulta a la base de datos para obtener lo que esta en la tabla carrito
$select_cart = $conn->query("SELECT * FROM cart");
$rows = $select_cart->fetchAll(PDO::FETCH_ASSOC);
$grand_total = 0; // Inicializa $grand_total a 0

//Recorre los campos del precio total y la cantidad para luego mostrarlos
if ($rows) {
    foreach ($rows as $fetch_cart) {
        $total_price = $fetch_cart['price'] * $fetch_cart['quantity'];
        $grand_total += $total_price;

        // Muestra información sobre el producto en el carrito dentro del bucle
        echo "<span>{$fetch_cart['name']} ({$fetch_cart['quantity']})</span>";
    }}else {
    echo "<div class='display-order'><span>¡Tu carrito está vacío!</span></div>";
     }

?>
      <!--Forms con los campos a llenar del cliente-->
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
<script src="js/script.js"></script>
   
</body>
</html>