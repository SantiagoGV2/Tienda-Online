<?php
require_once 'config/config.php';

$db = new Database();
$con = $db->conectar();

$idCategoria = $_GET['cat'] ?? '';
$orden = $_GET['orden'] ?? '';
$buscar = $_GET['q'] ?? '';

$filtro = '';

$orders = [
    'asc'=>'pro_nombre ASC',
    'desc'=>'pro_nombre DESC',
    'precio_alto'=>'pro_precio DESC',
    'precio_bajo'=>'pro_precio ASC',  
];

$order = $orders[$orden] ?? '';

if(!empty($order)){
    $order = "ORDER BY $order";
}

$params = [];

$query = "SELECT pro_id,pro_slug,pro_nombre,pro_precio,pro_stock FROM productos WHERE pro_activo=1 $order";

if($buscar != ''){
    $query .= "AND pro_nombre LIKE ?";
    $params[] = "%$buscar%";
}

if($idCategoria != ''){
    $query.= "AND cat_id=?";
    $params[] = $idCategoria;
}

$query = $con->prepare($query);

$query->execute($params);



$resultado = $query->fetchAll(PDO::FETCH_ASSOC);

$sqlCategorias = $con->prepare("SELECT cat_id, cat_nombre FROM categorias WHERE cat_activo=1");
$sqlCategorias->execute();
$categorias = $sqlCategorias->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php include 'menu.php';?>
    <main class="flex-shrink-0">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-3 mt-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            Categorias
                        </div>
                        <div class="list-group">
                            <a href="index.php" class="list-group-item list-group-item-action">
                                Todo
                            </a>
                            <?php foreach ($categorias as $categoria) { ?>
                                <a href="index.php?cat=<?php echo $categoria['cat_id']; ?>" class="list-group-item list-group-item-action <?php if ($idCategoria == $categoria['cat_id']) echo 'active'; ?>">
                                    <?php echo $categoria['cat_nombre']; ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-9">
                    <div class="row row-cols-1 row cols-sm-2 row-cols-md-3 justify-content-end g-4">
                        <div class="col mb-2">
                            <form action="index.php" id="ordenForm" method="get">

                                <input type="hidden" name="cat" id="cat" value="<?php echo $idCategoria; ?>">

                                <select name="orden" id="orden" class="form-select form-select-sm" onchange="submitForm()">
                                    <option value="">Ordenar Por:</option>
                                    <option value="precio_alto" <?php echo($orden === 'precio_alto') ?'Selected' : ''; ?> > Precios mas altos</option>
                                    <option value="precio_bajo" <?php echo($orden === 'precio_bajo') ?'Selected' : ''; ?>> Precios mas bajos</option>
                                    <option value="asc" <?php echo($orden === 'asc') ?'Selected' : ''; ?>> Nombre A-Z</option>
                                    <option value="desc" <?php echo($orden === 'desc') ?'Selected' : ''; ?>> Nombre Z-A</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="row row-cols-1 row cols-sm-2 row-cols-md-3 g-4">
                        <?php foreach ($resultado as $row) { ?>
                            <div class="col">
                                <div class="card shadow-sm">
                                    <?php
                                    $id = $row['pro_id'];
                                    $imagen = "img/productos/" . $id . "/principal2.jpg";
                                    if (!file_exists($imagen)) {
                                        $imagen = "img/no-photo.jpg";
                                    }
                                    ?>
                                    <img src="<?php echo $imagen; ?>" alt="zapatos">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $row['pro_nombre']; ?></h5>
                                        <p class="card-text"><?php echo MONEDA . number_format($row['pro_precio'], 2, '.', ','); ?></p>
                                        <p class="card-text">Cantidad: <?php echo $row['pro_stock']; ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="btn-group">
                                                <a href="details/<?php echo $row['pro_slug']; ?>" class="btn btn-primary">Detalles</a>

                                            </div>
                                            <button class="btn btn-outline-success" type="button" onclick="addProduct(<?php echo  $row['pro_id']; ?>, '<?php echo hash_hmac('sha1', $row['pro_id'], KEY_TOKEN); ?>')">Agregar al Carrito</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <style>
           .btn-wsp {
                position: fixed;
                width: 53px;
                height: 53px;
                line-height: 53px;
                bottom: 25px;
                right: 25px;
                background: #0df053;
                color: #fff;
                border-radius: 50px;
                font-size: 30px;
                box-shadow: 0px 1px 10px rgba(0, 0, 0, 0.3);
                z-index: 100;
            }
        </style>
        <div class="position-absolute top-0 start-0">
            <a href="https://api.whatsapp.com/send?phone=3113052266" class="btn-wsp d-flex justify-content-center align-items-center" target="_blank">
                <img src="img/whatsapp-line.png" alt="logo_what" />
            </a>
        </div>
    </main>

    <script src="js/main.js"></script>
    <script src="https://kit.fontawesome.com/1acde824b3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function addProduct(id, token) {
            let url = 'clases/carrito.php';
            let formData = new FormData();
            formData.append('id', id);
            formData.append('token', token);
            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors'
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        let = elemento = document.getElementById('num_cart');
                        elemento.innerHTML = data.numero;
                    } else {
                        alert("No hay suficientes existencias");
                    }
                })
        }

        function submitForm(){
            document.getElementById('ordenForm').submit();
        }
    </script>
</body>


</html>