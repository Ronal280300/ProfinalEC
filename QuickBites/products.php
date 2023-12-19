<?php
//Se agrega la conexion a la base de datos
include 'config.php';
//Toma los campos del producto en la BD
if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = 1;

//Realiza la consulta a la tabla del carrito 
    $select_cart = $conn->prepare("SELECT * FROM cart WHERE name = :product_name");
    $select_cart->bindParam(':product_name', $product_name);
    $select_cart->execute();

//Realiza la insercion a la tabla y muestra el mensaje de que el producto fue añadido
    if ($select_cart->rowCount() > 0) {
        $message[] = 'El producto ya fue añadido';
    } else {
        $insert_product = $conn->prepare("INSERT INTO cart (name, price, image, quantity) VALUES (:product_name, :product_price, :product_image, :product_quantity)");
        $insert_product->bindParam(':product_name', $product_name);
        $insert_product->bindParam(':product_price', $product_price);
        $insert_product->bindParam(':product_image', $product_image);
        $insert_product->bindParam(':product_quantity', $product_quantity);

        if ($insert_product->execute()) {
            $message[] = 'Producto añadido con éxito!';
        } else {
            $message[] = 'Error al añadir el producto';
        }
    }
}

?>
<!--Estructura visual del apartado de los productos-->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Productos</title>
   <link rel="icon" href="images/carrito.png" type="image/x-icon">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php
if(isset($message)){
   foreach($message as $message){
      echo '<div class="message"><span>'.$message.'</span> <i class="fas fa-times" onclick="this.parentElement.style.display = none;"></i> </div>';
   };
};
?>

<?php include 'header.php'; ?>

<div class="container">

<section class="products">

   <h1 class="heading">Productos Seleccionados</h1>

   <div class="box-container">
   <?php
   // Get hacia la base de datos la cual la recorre y muestra los productos 
   $select_products = $conn->query("SELECT * FROM products");
   $rows = $select_products->fetchAll(PDO::FETCH_ASSOC);
   foreach ($rows as $fetch_product) { 
   ?>
   <!--Forms de los campos de los productos-->
      <form action="" method="post">
         <div class="box">
            <img src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="">
            <h3><?php echo $fetch_product['name']; ?></h3>
            <div class="price">₡<?php echo $fetch_product['price']; ?>/-</div>
            <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
            <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
            <input type="submit" class="btn" value="Añadir" name="add_to_cart">
         </div>
      </form>
   <?php
   }
   ?>
</div>

<script src="js/script.js"></script>

</body>
</html>