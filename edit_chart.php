<?php

include("connect.php");

$result = pg_query_params("select \"name\" from chart where id=$1",array($_GET['id']));
$row = pg_fetch_assoc($result);
$name = $row['name']
?>

<html>
  <head>
     <title>Edit Account</title>
  </head>
  <body>
    <form method="post" action="update_chart.php">
      Name: <input type="text" name="name" value="<?= $name ?>" length=20><p/>
      <input type="submit">
      <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
    </form>
  </body>
</html>
