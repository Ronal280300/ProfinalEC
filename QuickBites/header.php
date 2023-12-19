 <!--Header de la página donde se encuentran las opciones para navegar-->
<header class="header">
   <div class="flex">
      <a href="index.php" class="logo">QuickBites Express</a>
      <link rel="icon" href="images/carrito.png" type="image/x-icon">

      <nav class="navbar" >
         <a href="index.php">Agregar</a>
         <a href="products.php">Ver Productos</a>
      </nav>
 <?php

 //Conexion a la base de datos donde contabiliza los productos seleccionados por el usuario
 try {
   $conn = new PDO("sqlsrv:server = tcp:prf-quickbites.database.windows.net,1433; Database = shop_db", "adminsql", "{Ronal-28}");
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   $select_rows = $conn->query("SELECT SUM(quantity) AS total_quantity FROM cart");
   $result = $select_rows->fetch(PDO::FETCH_ASSOC);
   $total_quantity = $result['total_quantity'] ?? 0;
} catch (PDOException $e) {
   echo "Error: " . $e->getMessage();
}
?>
    <!--Se define la cantidad de productos seleccionados al carrito y se actualiza en el icono-->
   <a href="cart.php" class="cart">Carrito <span><?php echo $total_quantity; ?></span> </a>
   <div id="menu-btn" class="fas fa-bars"></div>

   </div>

</header>