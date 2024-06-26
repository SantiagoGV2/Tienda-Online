<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasarela de Pagos</title>

    <script
      src="https://www.paypal.com/sdk/js?client-id=AVQJJbuq4AtcsgjWZSCR58NL_gTLHTsIjM-HX5FkBXklLBlrKIKGrIVSfM6QeKdXIhu5AWROIPKC83Go&buyer-country=US&currency=USD"
    ></script>
      
</head>
<body>

    <div id="paypal-button-container"></div>

    <script>
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
                            value: 100
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                actions.order.capture().then(function (detalles){
                    window.location.href="completado.html"
                });
            },

            onCancel:function(data) {
                alert("Pago cancelado");
                console.log(data)
            }
            
        }).render('#paypal-button-container');
    </script>
</body>
</html>
