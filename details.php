<?php
require 'config/config.php';


// Suponiendo que KEY_TOKEN esté definida en config.php
$db = new Database();
$con = $db->conectar();


$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if ($slug == '') {
    echo 'Error al procesar la peticion1';
    exit;
} else {

    //$token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

    //if ($token == $token_tmp) {

        $sql = $con->prepare("SELECT count(pro_id) FROM productos WHERE pro_slug=? AND pro_activo=1");
        $sql->execute([$slug]);

        if ($sql->fetchColumn() > 0) {
            $sql = $con->prepare("SELECT pro_id,pro_nombre,pro_descripcion, pro_precio, pro_stock, pro_activo FROM productos WHERE pro_slug=? AND pro_activo=1 LIMIT 1");
            $sql->execute([$slug]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);

            $id = $row['pro_id'];
            $nombre = $row['pro_nombre'];
            $descripcion = $row['pro_descripcion'];
            $precio = $row['pro_precio'];
            $cantidad = $row['pro_stock'];
            $dir_images = "img/productos/" . $id . "/";

            $ruta_img = $dir_images . 'principal2.jpg';

            if (!file_exists($ruta_img)) {
                $ruta_img = "img/no-photo.png";
            }

            $imagenes = array();
            if (file_exists($dir_images)) {
                $dir = dir($dir_images);

                while (($archivo = $dir->read()) != false) {
                    if ($archivo != 'principal2.jpg' && (strpos($archivo, 'jpg') || strpos($archivo, 'jpeg'))) {
                        $imagenes[] = $dir_images . $archivo;
                    }
                }
                $dir->close();

                $sqlTallas = $con->prepare("SELECT DISTINCT t.tal_id, t.tal_nombre FROM pro_variantes AS pv 
                INNER JOIN tallas AS t ON pv.tal_id = t.tal_id 
                WHERE pv.pro_id = ?");
                $sqlTallas->execute([$id]);
                $tallas = $sqlTallas->fetchAll(PDO::FETCH_ASSOC);

                $sqlColores = $con->prepare("SELECT DISTINCT c.col_id, c.col_nombre FROM pro_variantes AS pv 
                INNER JOIN colores AS c ON pv.col_id = c.col_id 
                WHERE pv.pro_id = ?");
                $sqlColores->execute([$id]);
                $colores = $sqlColores->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            echo 'Error al procesar la peticion2';
            exit;
        }
    /*} else {
        echo 'Error al procesar la peticion3';
        exit;
    }*/
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <base href="<?php echo SITE_URL; ?>/">
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body>
    <?php include 'menu.php'; ?>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-md-6 order-md-1">
                    <img src="<?php echo $ruta_img; ?>" class="d-block w-100">
                </div>
                <div class="col-md-6 order-md-2">
                    <h2><?php echo $nombre; ?></h2>
                    <h2><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></h2>
                    <p class="lead"><?php echo $descripcion ?></p>

                    <div class="row g-2">

                        <?php if ($tallas) { ?>
                            <div class="col-3 my-3">
                                <label for="tallas" class="form-label">Tallas</label>
                                <select class="form-select form-select-lg" name="tallas" id="tallas" onchange="cargarColores()">
                                    <?php foreach ($tallas as $talla) { ?>
                                        <option value="<?php echo $talla['tal_id'] ?>"><?php echo $talla['tal_nombre'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <?php if ($colores) { ?>
                            <div class="col-3 my-3" id="div-colores">
                                <label for="colores" class="form-label">Colores</label>
                                <select class="form-select form-select-lg" name="colores" id="colores">
                                    <?php foreach ($colores as $color) { ?>
                                        <option value="<?php echo $color['col_id'] ?>"><?php echo $color['col_nombre'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>



                    <div class="col-3 my-3">
                        Cantidad: <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" max="10" value="1">
                    </div>

                    <div class="col-3 my-3">
                        <input class="form-control" id="nuevo_precio">
                    </div>

                    <div class="d-grid gap-3 col-10 mx-auto">
                        <button class="btn btn-primary" type="button">Comprar Ahora</button>
                        <button class="btn btn-outline-primary" type="button" onclick="addProduct(<?php echo $id; ?>, cantidad.value)">Agregar al Carrito</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
       
        function addProduct(id,cantidad,token = '') {
            let url = './clases/carrito.php';
            let formData = new FormData();
            formData.append('id', id);
            formData.append('cantidad', cantidad);
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
                    }else{
                        alert("No hay suficientes existencias")
                    }
                })
        }

        const cbxTallas = document.getElementById('tallas');
        cargarColores();

        const cbxColores = document.getElementById('colores');
        cbxColores.addEventListener('change', cargarVariante, false);

        function cargarColores() {
            let idTalla = 0;
            if (document.getElementById('tallas')) {
                idTalla = document.getElementById('tallas').value;
            }


            const cbxColores = document.getElementById('colores');
            const divColores = document.getElementById('div-colores');

            let url = './clases/productosAjax.php';
            let formData = new FormData();
            formData.append('pro_id', <?php echo $id ?>);
            formData.append('tal_id', idTalla);
            formData.append('action', 'buscarColoresPorTalla');
            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors'
                }).then(response => response.json())
                .then(data => {
                    if (data.colores != '') {
                        divColores.style.display = 'block';
                        cbxColores.innerHTML = data.colores;
                    } else {
                        divColores.style.display = 'none';
                        cbxColores.innerHTML = '';
                        cbxColores.value = 0;
                    }
                    cargarVariante();
                });
        }

        function cargarVariante() {

            let idTalla = 0;

            if (document.getElementById('tallas')) {
                idTalla = document.getElementById('tallas').value;
            }

            let idColor = 0;
            if (document.getElementById('colores')) {
                idColor = document.getElementById('colores').value;
            }
            let url = './clases/productosAjax.php';
            let formData = new FormData();
            formData.append('pro_id', <?php echo $id ?>);

            if (idTalla != 0 && idTalla != '') {
                formData.append('tal_id', idTalla);
            }
            if (idColor != 0 && idColor != '') {
                formData.append('col_id', idColor);
            }

            formData.append('action', 'buscaIdVariante');

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors',
                }).then(response => response.json())
                .then(data => {
                    if (data.variante != '') {
                        document.getElementById('nuevo_precio').value = data.variante.vari_precio;
                    } else {
                        document.getElementById('nuevo_precio').value = 'no encontrado';
                    }
                });
        }
    </script>
</body>

</html>