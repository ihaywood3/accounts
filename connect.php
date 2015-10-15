<?php

pg_connect("dbname=accounts");

function select_acct(acct_name,selected=0)
{
	echo "<select name=\"" . $acct_name . "\">";
	$result = pg_query("select * from charts");
	while ($row = pg_fetch_assoc($result))
	{
		echo "<option value=\"" . $row['id'] . "\""
		if ($row['id'] == $selected)
		{
			echo " selected";
		}
		echo ">" . $row['name'];
	}
	echo "</select>";
}

?>