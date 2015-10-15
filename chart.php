<html>
<head><title>Chart of Accounts</title></head>
<body>
<table>
<tr><th>ID</th><th>Name</th><th></th></tr>

<?php

include('connect.php');

$result = pg_query("select * from charts order by \"name\"");
while ($row = pg_fetch_assoc($result)) { 
?>
<tr><td><?= $row['id'] ?></td><td><?= $row['name'] ?></td>
<td>
<a href="/edit_chart.phpid=<?= $row['id'] ?>">Edit</a></td></tr>
    <?php 
}
?>
</table><p/>
<h2>New Account</h2>
<form action="/new_chart.php" method="post">
    Name:<input type="text" name="name" length="20"><p/>
<input type="submit">
</form>
</body>
</html>

