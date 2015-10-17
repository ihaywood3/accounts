<?php

include('connect.php');

$result = pg_query_params("select pdf from txn where id=$1",array($_GET['id']));
$row = pg_fetch_result($result,"pdf");
$pdf = pg_unescape_bytea($row);

header("Content-Type: application/pdf");
echo $pdf;
 
?>

