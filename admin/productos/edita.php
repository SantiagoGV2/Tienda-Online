<?php
require '../config/database.php';
require '../config/config.php';
require '../header.php';


if (!isset($_SESSION['user_type'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$db = new Database();
$con = $db->conectar();


$id = $_GET['id'];


$sql = $con->prepare("SELECT pro_nombre, pro_descripcion, pro_precio, pro_stock, cat_id FROM productos WHERE pro_id = ? AND pro_activo = 1");
$sql->execute([$id]);
$producto = $sql->fetch(PDO::FETCH_ASSOC);

$sql = ("SELECT cat_id, cat_nombre FROM categorias WHERE cat_activo = 1");
$resultado = $con->query($sql);
$categorias = $resultado->fetchAll(PDO::FETCH_ASSOC);

$ruta_img = '../../img/productos/' . $id . '/';
$imagenPrincipal = $ruta_img . 'principal2.jpg';

$resultado = $con->query("SELECT tal_id, tal_nombre FROM tallas");
$tallas = $resultado->fetchAll(PDO::FETCH_ASSOC);

$resultado = $con->query("SELECT col_id, col_nombre FROM colores");
$colores = $resultado->fetchAll(PDO::FETCH_ASSOC);

$sqlVariantes = $con->prepare("SELECT vari_id ,tal_id, col_id, vari_precio, vari_stock FROM pro_variantes WHERE pro_id = ?");
$sqlVariantes->execute([$id]);
$variantes = $sqlVariantes->fetchAll(PDO::FETCH_ASSOC);

?>
<style>
    .ck-editor__editable[role="textbox"] {
        min-height: 280px;
    }
</style>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<main>
    <div class="container-fluid px-4">
        <h2 class="mt-3">Modificar producto</h2>

        <form action="actualizar.php" method="post" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" id="nombre" value="<?php echo htmlspecialchars($producto['pro_nombre'], ENT_QUOTES); ?>" required autofocus />

            </div>

            <div class="mb-3">
                <label for="descripcion">Descripción</label>
                <textarea class="form-control" name="descripcion" id="editor"><?php echo $producto['pro_descripcion']; ?></textarea>
            </div>
            <div class="row mb-2">
                <div class="col-12 col-md-6">
                    <label for="categoria" class="form-label">Imagen Principal</label>
                    <input type="file" class="form-control" name="imagen_principal" id="imagen_principal" accept="img/jpeg" />
                </div>
                <div class="col-12 col-md-6">
                    <label for="otras_imagenes" class="form-label">Otras imagenes</label>
                    <input type="file" class="form-control" name="otras_imagenes" id="otras_imagenes" accept="img/jpeg" multiple />
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12 col-md-6">
                    <?php if (file_exists($imagenPrincipal)) { ?>
                        <img src="<?php echo $imagenPrincipal . '?id=' . time(); ?>" class="img-thumbnail my-3"><br>
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminaImagen('<?php echo $imagenPrincipal; ?>')"><i class="fa-solid fa-trash"></i> Eliminar</button>
                    <?php } ?>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" class="form-control" name="precio" id="precio " value="<?php echo $producto['pro_precio']; ?>" required />
                </div>
                <div class=" col mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control" name="stock" id="stock" value="<?php echo $producto['pro_stock']; ?>" required />
                </div>
            </div>
            <div class="row">
                <div class="col-4 mb-3">
                    <label for="categoria" class="form-label">Categoria</label>
                    <select class="form-select" name="categoria" id="categoria" value="<?php echo $producto['pro_nombre']; ?>" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($categorias as $categoria) { ?>
                            <option value="<?php echo $categoria['cat_id']; ?>" <?php if ($categoria['cat_id'] == $producto['cat_id']) echo 'selected'; ?>>
                                <?php echo $categoria['cat_nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <hr>
            <div class="row ">
                <div class="col-12 mb-3">
                    <h4 class="me-4">Variantes</h4>
                    <button type="button" class="btn btn-success btn-small" id="agrega-variante"> + Variante</button>
                </div>
            </div>
            <div id="contenido">
                <?php foreach ($variantes as $variante) { ?>
                    <div class="row mb-3">

                        <input type="hidden" name="id_variante[]" value="<?php echo $variante['vari_id']; ?>">

                        <div class="col">
                            <label class="form-label">Talla:</label>
                            <select class="form-select" name="talla[]">
                                <option value="">Seleccionar</option>
                                <?php foreach ($tallas as $talla) { ?>
                                    <option value="<?php echo $talla['tal_id']; ?>" <?php if ($talla['tal_id'] == $variante['tal_id']) echo 'selected'; ?>>
                                        <?php echo $talla['tal_nombre']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Color:</label>
                            <select class="form-select" name="color[]">
                                <option value="">Seleccionar</option>
                                <?php foreach ($colores as $color) { ?>
                                    <option value="<?php echo $color['col_id']; ?>" <?php if ($color['col_id'] == $variante['col_id']) echo 'selected'; ?>>
                                        <?php echo $color['col_nombre']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Precio:</label>
                            <input type="text" name="precio_variante[]" class="form-control" value="<?php echo $variante['vari_precio'] ?>">
                        </div>
                        <div class="col">
                            <label class="form-label">Stock:</label>
                            <input type="text" name="stock_variante[]" class="form-control" value="<?php echo $variante['vari_stock'] ?>">
                        </div>
                    </div>
                <?php } ?>
            </div>

            <template id="plantilla_variante">
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Talla:</label>
                        <select class="form-select" name="talla[]">
                            <option value="">Seleccionar</option>
                            <?php foreach ($tallas as $talla) { ?>
                                <option value="<?php echo $talla['tal_id']; ?>">
                                    <?php echo $talla['tal_nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col">
                        <label class="form-label">Color:</label>
                        <select class="form-select" name="color[]">
                            <option value="">Seleccionar</option>
                            <?php foreach ($colores as $color) { ?>
                                <option value="<?php echo $color['col_id']; ?>">
                                    <?php echo $color['col_nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col">
                        <label class="form-label">Precio:</label>
                        <input type="text" name="precio_variante[]" class="form-control">
                    </div>
                    <div class="col">
                        <label class="form-label">Stock:</label>
                        <input type="text" name="stock_variante[]" class="form-control">
                    </div>
                </div>
            </template>

            <button type="submit" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>
</main>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });

    function eliminaImagen(urlImagen) {
        let url = 'eliminar_imagen.php';
        let formData = new FormData();
        formData.append('urlImagen', urlImagen);
        fetch(url, {
            method: 'POST',
            body: formData,
        }).then((response) => {
            if (response.ok) {
                location.reload();
            }
        })
    }
    const btnVariante = document.getElementById('agrega-variante')
    btnVariante.addEventListener('click', agregaVariante);

    function agregaVariante() {
        const contenido = document.getElementById('contenido')
        const plantilla = document.getElementById('plantilla_variante').content.cloneNode(true)

        contenido.appendChild(plantilla);
    }
</script>

<?php
require '../footer.php';
?>