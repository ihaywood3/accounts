<?php

include('connect.php');

pg_query_params("update chart set \"name\"=$1 where id=$2",array($_POST['name'],$_POST['id']));

?>
<html>
<head><title>New entry</title></head>
<body>
Updated entry chart of accounts.
<a href="/chart.php">Return to accounts.</a>
</a></body></html>