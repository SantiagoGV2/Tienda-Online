<?php
require 'config/config.php';
require 'vendor/autoload.php';

use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;

SDK::setAccessToken("TEST-4548260847395468-051607-89c1dd079a744e2a99e2264508063ee3-1770793831");

$db = new Database();
$con = $db->conectar();

$productos_mp = array();
$preference = new Preference();

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
$lista_carrito = array();
$total = 0;

if ($productos != null) {
    foreach ($productos as $clave => $cantidad) {
        $sql = $con->prepare("SELECT pro_id, pro_nombre, pro_precio, $cantidad AS pro_stock FROM productos WHERE pro_id=? AND pro_activo=1");
        $sql->execute([$clave]);
        $producto = $sql->fetch(PDO::FETCH_ASSOC);
        if ($producto) {
            $lista_carrito[] = $producto;

            // Calcular subtotal y total
            $subtotal = $producto['pro_precio'] * $cantidad;
            $total += $subtotal;
            $precio_usd = $subtotal * EXCHANGE_RATE;

            // Crear elemento de Mercado Pago
            $item = new Item();
            $item->id = $producto['pro_id'];
            $item->title = $producto['pro_nombre'];
            $item->quantity = $cantidad;
            $item->unit_price = $producto['pro_precio'];
            $item->currency_id = "COP";
            array_push($productos_mp, $item);
        }
    }

    // Guardar el total en la sesión para su uso posterior
    $_SESSION['carrito']['total'] = $total;

    // Guardar la preferencia de Mercado Pago
    $preference->items = $productos_mp;
    $preference->back_urls = array(
        "success" => "http://localhost/Tienda-2.1/clases/captura_mp.php",
        "failure" => "http://localhost/Tienda-2.1/fallo.php",
    );
    $preference->auto_return = "approved";

    try {
        $preference->save();
        $preferenceId = $preference->id;
    } catch (Exception $e) {
        echo 'Error al guardar la preferencia: ' . $e->getMessage();
        var_dump($e->getTrace());
        exit;
    }
} else {
    header("Location:index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo CLIENT_ID; ?>&buyer-country=US&currency=<?php echo CURRENCY; ?>"></script>
</head>
<body>
   <?php include 'menu.php';?>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <h4>Detalles de Pago</h4>
                    <div id="wallet_container"></div>
                </div>
                
                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($lista_carrito)) {
                                    echo '<tr><td colspan="5" class="text-center"><b>Lista Vacia</b></td></tr>';
                                } else {
                                    foreach ($lista_carrito as $producto) {
                                        $_id = $producto['pro_id'];
                                        $nombre = $producto['pro_nombre'];
                                        $precio = $producto['pro_precio'];
                                        $cantidad = $producto['pro_stock'];
                                        $subtotal = $precio * $cantidad;
                                ?>
                                        <tr>
                                            <td><?php echo $nombre; ?></td>
                                            <td><?php echo MONEDA . number_format($subtotal, 2, '.', ','); ?></td>
                                        </tr>
                                <?php }
                                } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <p class="h3 text-end" id="total"><?php echo MONEDA . number_format($total, 2, '.', ',') ?></p>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // PayPal
        paypal.Buttons({
            style: {
                color: 'blue',
                shape: 'pill',
                label: 'pay'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: (<?php echo $total ?> * <?php echo EXCHANGE_RATE; ?>).toFixed(2) // Convertir a USD y limitar a dos decimales
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                let URL = 'clases/captura.php';
                actions.order.capture().then(function(detalles) {
                    console.log(detalles);
                    let url = 'clases/captura.php';
                    return fetch(url, {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            detalles: detalles
                        })
                    }).then(function(response) {
                        window.location.href = "completado.php?key=" + detalles['id'];
                    });
                });
            },

            onCancel: function(data) {
                alert("Pago cancelado");
                console.log(data);
            }
        }).render('#paypal-button-container');

        // MercadoPago
        if (typeof MercadoPago !== 'undefined') {
            const mp = new MercadoPago('TEST-304b6a13-cc1b-422f-906c-09c5873b2c5d', {
                locale: 'es-CO'
            });
            const bricksBuilder = mp.bricks();

            console.log("Preference ID:", '<?php echo $preferenceId; ?>');

            bricksBuilder.create("wallet", "wallet_container", {
                initialization: {
                    preferenceId: '<?php echo $preferenceId; ?>'
                },
                customization: {
                    texts: {
                        valueProp: 'smart_option'
                    }
                }
            }).then(brick => {
                console.log("Brick creado correctamente", brick);
            }).catch(error => {
                console.error("Error al crear el brick", error);
            });
        } else {
            console.error("SDK de MercadoPago no se ha cargado correctamente.");
        }
    });
</script>
</body>
</html>
