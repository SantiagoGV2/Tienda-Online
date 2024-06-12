<?php

require 'vendor/autoload.php';

use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;


SDK::setAccessToken('TEST-4548260847395468-051607-89c1dd079a744e2a99e2264508063ee3-1770793831');

$preference = new Preference();

$item = new Item();
$item->id = "010";
$item->title = 'Producto CDP';
$item->quantity = 1;
$item->unit_price = 1500;
$item->currency_id = "COP";

$preference->items = array($item);

// Configurar las back_urls
$preference->back_urls = array(
  "success" => "http://localhost/Tienda-2/captura.php",
  "failure" => "http://localhost/Tienda-2/fallo.php",
);

// Configurar auto_return para redirigir automáticamente al usuario a la URL de éxito si el pago es aprobado
$preference->auto_return = "approved";

try {
  $preference->save();
  $preferenceId = $preference->id;
  $initPoint = $preference->init_point;
  $sandboxInitPoint = $preference->sandbox_init_point;
} catch (Exception $e) {
  echo 'Error: ' . $e->getMessage();
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>

<body>

  <h3>Mercado Pago</h3>


    <a id="checkout-button" href="<?php echo $sandboxInitPoint; ?>" target="_blank" ><img style="width: 15%;" 
    class="rounded rounded-3 border border-primary" 
    src="https://www.boutiqueautomovil.com.ar/wp-content/uploads/2019/05/logo-mercadopago.png" /></a>
  

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof MercadoPago !== 'undefined') {
        const mp = new MercadoPago('TEST-304b6a13-cc1b-422f-906c-09c5873b2c5d', {
          locale: 'es-CO'
        });
        const bricksBuilder = mp.bricks();

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