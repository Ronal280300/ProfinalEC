<?php
//Se agrega la conexion a la base de datos SQL
@include 'config.php';

//este proceso verifica el envio que se hace en los campos del formulario 
//donde se obtienen los datos
if(isset($_POST['add_product'])){
   $p_name = $_POST['name'];
   $p_price = $_POST['price'];
   $p_image = $_FILES['image']['name'];
   $p_image_tmp_name = $_FILES['image']['tmp_name'];
   $p_image_folder = 'uploaded_img/'.$p_image;

   //inserta en la base de datos a la cual está conectada
   try {
      $insert_query = $conn->prepare("INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
      $insert_query->execute([$p_name, $p_price, $p_image]);

      move_uploaded_file($p_image_tmp_name, $p_image_folder);
      $message[] = 'Producto agregado con éxito';
   } catch (PDOException $e) {
      $message[] = 'Error al agregar el producto: ' . $e->getMessage();
   }
};
?>
<?php
// Realiza la conexion a la BD que en este caso es un PDO (PHP DATA OBJECTS) donde interactua con ella
try {
    $conn = new PDO("sqlsrv:server = tcp:prf-quickbites.database.windows.net,1433; Database = shop_db", "adminsql", "{Ronal-28}");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

//Este codigo lo que hace es el flujo que al presionar al boton eliminar, borra el producto
if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    
    try {
        $delete_query = $conn->prepare("DELETE FROM products WHERE id = :delete_id");
        $delete_query->bindParam(':delete_id', $delete_id, PDO::PARAM_INT);
        $delete_query->execute();

        header('location:index.php');
        $message[] = 'Producto eliminado';
    } catch (PDOException $e) {
        header('location:index.php');
        $message[] = 'Error al eliminar producto: ' . $e->getMessage();
    }
}

//Este codigo lo que hace es el flujo que al presionar al boton actualizar, actualiza los campos del producto
if(isset($_POST['update_product'])){
    $update_p_id = $_POST['update_id'];
    $update_p_name = $_POST['update_name'];
    $update_p_price = $_POST['update_price'];
    $update_p_image = $_FILES['update_image']['name'];
    $update_p_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_p_image_folder = 'uploaded_img/'.$update_p_image;

    try {
        $update_query = $conn->prepare("UPDATE products SET name = :update_name, price = :update_price, image = :update_image WHERE id = :update_id");
        $update_query->bindParam(':update_name', $update_p_name, PDO::PARAM_STR);
        $update_query->bindParam(':update_price', $update_p_price, PDO::PARAM_STR);
        $update_query->bindParam(':update_image', $update_p_image, PDO::PARAM_STR);
        $update_query->bindParam(':update_id', $update_p_id, PDO::PARAM_INT);
        $update_query->execute();

        move_uploaded_file($update_p_image_tmp_name, $update_p_image_folder);
        $message[] = 'Producto actualizado!';
        header('location:index.php');
    } catch (PDOException $e) {
        $message[] = 'Error al actualizar: ' . $e->getMessage();
        header('location:index.php');
    }
}
?>

<!-- Acá comienza la estructura visual de la página index -->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Panel Administrador</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
   <link rel="icon" href="images/carrito.png" type="image/x-icon">
</head>
<body>
   
<?php

if(isset($message)){
   foreach($message as $message){
      echo '<div class="message"><span>'.$message.'</span> <i class="fas fa-times" onclick="this.parentElement.style.display = none;"></i> </div>';
   };
};


?>
<!--Incluye el header que fue hecho en un file aparte en PHP -->
<?php include 'header.php'; ?>

<div class="container">

<section>
<!--Formulario con los datos de los productos-->
<form action="" method="post" class="add-product-form" enctype="multipart/form-data">
   <h3>Agregar Producto</h3>
   <input type="text" name="name" placeholder="Nombre Producto" class="box" required>
   <input type="number" name="price" min="0" placeholder="Precio Producto" class="box" required>
   <label for="file-upload" class="custom-file-upload" style="font-size:1.5rem">
  <i class="fas fa-cloud-upload"></i> Subir Imagen</label>
<input type="file" id="file-upload" name="image" accept="image/png, image/jpg, image/jpeg" class="boxin" required>
<span class="file-name">Ningún archivo seleccionado</span>

   <input type="submit" value="Agregar" name="add_product" class="btn">
</form>

</section>

<!--Tabla de detalles de los productos -->
<section class="display-product-table">
   <table>
      <thead>
         <th>Imagen Producto</th>
         <th>Nombre Producto</th>
         <th>Precio Producto</th>
         <th>Acción</th>
      </thead>
      <tbody>
         <?php
         // Get hacia la base de datos la cual la recorre y muestra los productos 
         $select_products = $conn->query("SELECT * FROM products");
         $rows = $select_products->fetchAll(PDO::FETCH_ASSOC);
         foreach ($rows as $row) {
         ?>
            <tr>
               <td><img src="uploaded_img/<?php echo $row['image']; ?>" height="100" alt=""></td>
               <td><?php echo $row['name']; ?></td>
               <td>₡<?php echo $row['price']; ?>/-</td>
               <td>
               <a href="index.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de que quieres eliminarlo?');"> <i class="fas fa-trash"></i> Eliminar </a>
                  </a>
                  <a href="index.php?edit=<?php echo $row['id']; ?>" class="update-btn"> <i class="fas fa-edit"></i> Actualizar </a>
                  </a>
               </td>
            </tr>
         <?php
         }
         ?>
      </tbody>
   </table>
</section>


<section class="edit-form-container">

<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:prf-quickbites.database.windows.net,1433; Database = shop_db", "adminsql", "{Ronal-28}");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    echo "Error connecting to SQL Server: " . $e->getMessage();
    die();
}

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_query = $conn->prepare("SELECT * FROM products WHERE id = :edit_id");
    $edit_query->bindParam(':edit_id', $edit_id, PDO::PARAM_INT);
    $edit_query->execute();

    if ($edit_query->rowCount() > 0) {
        $fetch_edit = $edit_query->fetch(PDO::FETCH_ASSOC);
?>
        <form action="" method="post" enctype="multipart/form-data">
            <img src="uploaded_img/<?php echo $fetch_edit['image']; ?>" height="200" alt="">
            <input type="hidden" name="update_p_id" value="<?php echo $fetch_edit['id']; ?>">
            <input type="text" class="box" required name="update_p_name" value="<?php echo $fetch_edit['name']; ?>">
            <input type="number" min="0" class="box" required name="update_p_price" value="<?php echo $fetch_edit['price']; ?>">
            <input type="file" class="box" required name="update_p_image" accept="image/png, image/jpg, image/jpeg">
            <input type="submit" value="Actualizar producto" name="update_product" class="btn">
            <input type="reset" value="cancelar" id="close-edit" class="option-btn">
        </form>
<?php
        echo "<script>document.querySelector('.edit-form-container').style.display = 'flex';</script>";
    }
}
?>

</section>
</div>

<!--Se agrega la conexion al file JS-->
<script src="js/script.js"></script>

</body>
</html>