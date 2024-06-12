<?php
require 'config/database.php';
require 'config/config.php';


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}



$db = new Database();
$con = $db->conectar();

$hoy = date('Y-m-d');
$lunes = date('Y-m-d', strtotime('monday this week', strtotime($hoy)));
$domingo = date('Y-m-d', strtotime('sunday this week', strtotime($hoy)));

$fechaInicial = new DateTime($lunes);
$fechaFinal = new DateTime($domingo);

$diasVentas = [];

for ($i = $fechaInicial; $i <= $fechaFinal; $i->modify('+1 day')) {
    $diasVentas[] = totalDia($con, $i->format('Y-m-d'));
}

$diasVentas = implode(',', $diasVentas); //cambiar nuestro array 

///////////////////////

$listaProductos = productosMasVendidos($con, $lunes, $domingo);
$nombreProductos = [];
$cantidadProductos = [];

foreach ($listaProductos as $producto) {
    $nombreProductos[] = $producto['det_nombre'];
    $cantidadProductos[] = $producto['cantidad'];
}

$nombreProductos = implode("','", $nombreProductos); 
$cantidadProductos = implode(',', $cantidadProductos); 


function totalDia($con, $fecha)
{
    $sql = "SELECT IFNULL(SUM(com_total), 0) AS total FROM compras WHERE DATE(com_fecha) = '$fecha' AND com_status LIKE 'COMPLETED'";
    $resultado = $con->query($sql);
    $row = $resultado->fetch(PDO::FETCH_ASSOC);

    return $row['total'];
}
function productosMasVendidos($con, $fechaInicial, $fechaFinal)
{
    $sql = "SELECT SUM(dc.det_cantidad) AS cantidad, dc.det_nombre FROM detallecompras AS dc INNER JOIN compras AS c ON dc.com_id = c.com_id WHERE DATE(c.com_fecha) BETWEEN '$fechaInicial' AND '$fechaFinal' GROUP BY dc.pro_id, dc.det_nombre ORDER BY SUM(dc.det_cantidad) DESC LIMIT 5";
    $resultado = $con->query($sql);
    return $resultado->fetchAll(PDO::FETCH_ASSOC);
}

include 'header.php';

?>
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>

        <div class="row">
            <div class="col-6">
                <div class="card mb-4">
                    <div class="card-header">
                        Ventas de la semana
                    </div>
                    <div class="card-body">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="card mb-4">
                    <div class="card-header">
                        Productos mas vendidos de la semana
                    </div>
                    <div class="card-body">
                        <canvas id="chart-productos"></canvas>
                    </div>
                </div>
            </div>


        </div>

    </div>
</main>
<script>
    const ctx = document.getElementById('myChart'); //se toma el id

    new Chart(ctx, { //se llama a la bibilioteca char ctx(elemento donde vamos agrgar nuestra grafica)
        type: 'bar', //tipó de grafica
        data: {
            labels: ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'], //Etiquetas
            datasets: [{
                label: 'Totales', //titulo
                data: [<?php echo $diasVentas; ?>], //datos de cada etiqueta
                backgroundColor: [
                    'rgba(18, 225, 42, 0.8)',
                    'rgba(18, 225, 4, 0.8)',
                    'rgba(18, 225, 14, 0.8)',
                    'rgba(18, 225, 2, 0.8)',
                    'rgba(18, 225, 88, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                   

                ],
                borderWidth: 1
            }]
        },
        //diseño
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    const ctxProductos = document.getElementById('chart-productos'); //se toma el id

    let chartProd = new Chart(ctxProductos, { //se llama a la bibilioteca char ctx(elemento donde vamos agrgar nuestra grafica)
        type: 'pie', //tipó de grafica
        data: {
            labels: ['<?php echo $nombreProductos; ?>'], //Etiquetas
            datasets: [{
                data: [<?php echo $cantidadProductos; ?>], //datos de cada etiqueta
                borderWidth: 1
            }]
        },
        //diseño
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<?php include 'footer.php'; ?>