<?php

include('connect.php');

pg_query_params("insert into chart (name) values ($1)",array($_POST['name']));

?>
<html>
<head><title>New entry</title></head>
<body>
New entry into chart of accounts.
<a href="/chart.php">Return to accounts.</a>
</a></body></html>