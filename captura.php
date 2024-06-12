<?php

require 'config/config.php';

   $payment = $_GET['payment_id'];
   $status = $_GET['status'];
   $payment = $_GET['payment_type'];
   $order_id = $_GET['merchant_order_id'];  

   echo "<h3>Pago Exitoso</h3>";

   echo "<h3>Payment ID: $payment</h3>";
   echo "<h3>Status: $status</h3>";
   echo "<h3>Payment Type: $payment</h3>";
   echo "<h3>Order ID: $order_id</h3>";

   unset($_SESSION['carrito']);
