<html>
  <head><title>Accounts Search</title></head>
  <body>
<?php

include('connect.php');
include('date.php');


if (isset($_GET['query']))
  {
    $query = $_GET['query'];
    $date1 = $_GET['date1'];
    $date2 = $_GET['date2'];
    $acct = $_GET['acct'];
  } else 
  {
    $query = "none";
    $date1 = "";
    $date2 = "";
    $acct = "";
  }
?>
<form action="/accounts.php" method="get">
  Account:<input type="text" length="20" name="acct" value="<?= $acct ?>"><p/>
  Start: <input type="text" length="10" name="date1" value="<?= $date1 ?>"><p/>
  End: <input type="text" length="10" name="date2" value="<?= $date2 ?>"><p/>
  Type: <input type="radio" name="query" id="ledger" value="ledger"
<?php
  if ($query == "ledger") { echo "checked"; } 
?> ><label for="ledger"> Ledger</label>&nbsp;
       <input type="radio" name="query" value="totals" id="totals"
<?php
  if ($query == "totals") { echo "checked"; } ?> >
<label for="totals">Totals</label><p/>

<input type="submit">
</form>
<?php
 

  if ($query != "none")
    {
 $date1 = parse_date($date1); 
$date2 = parse_date($date2);
     echo "<table>";
      if ($query == 'ledger') 
	{
	  echo "<tr><th>Date</th><th>Subaccount</th><th>Amount</th><th>Comment</th><th>Other</th></tr>";
	  $last_date = null;
	  $sql = <<<"EOT"
select to_char(entered,'dd Mon YYYY') as date_display, 
       entered, "comment",
       pdf is not null as has_pdf,
       amount,
       fk_txn,
       fk_chart,
       "name",
       1 as bank_type
from chart,split,txn where 
       (chart.name = $1 or chart.name like $1 || '/%') and 
       chart.id = split.fk_chart and
       txn.id = split.fk_txn and 
       entered >= $2 and 
       entered <= $3 
union
       select
	    to_char("date",'dd Mon YYYY'),
	    "date", "comment",
	    false,
	    amount,
	    statement.id,
	    fk_chart,
	    "name",
	case when exists (
			  select 1 from txn,split where statement.fk_chart = split.fk_chart and 
                                                        split.fk_txn = txn.id and 
			                                split.amount = statement.amount and
			                                abs(extract(epoch from statement."date")-extract(epoch from txn.entered)) < 604800
			  ) then 2 else 3 end
       from
	    statement, chart
       where
            statement.fk_chart = chart.id and 
	    (chart.name = $1 or chart.name like $1 || '/%') and 
	    "date" >= $2 and "date" <= $3
       order by entered, bank_type, fk_txn
EOT;
	  $result = pg_query_params ($sql, array($acct,$date1,$date2));
	  while ($row = pg_fetch_assoc($result))
	    {
	      switch ($row['bank_type'])
		{
		case 1: 
		  echo "<tr>";
		  break;
		case 2:
		  echo "<tr style=\"background-color: lightgreen; font-size:x-small\">";
		  break;
		case 3:
		  echo "<tr style=\"background-color: pink\">";
		  break;
		}
	      ?>
		<td><?= $row['date_display'] ?></td>
		<td><?= $row['name'] ?></td>
		<td><?= $row['amount'] ?></td>
		<td><?= htmlspecialchars($row['comment']) ?></td>
		<?php
		   if ($row['bank_type'] == 1)
		     {
		       $first_row = TRUE;
		       $result2 = pg_query_params("select amount,fk_txn,fk_chart,\"name\" from split,chart where split.fk_chart = chart.id and split.fk_txn = $1 and split.fk_chart <> $2",array($row['fk_txn'],$row['fk_chart']));
		       while ($row2 = pg_fetch_assoc($result2))
			 {
			   if (! $first_row)
			     {
			       echo "</tr><tr><td></td><td></td><td></td><td></td>";
			     }
			   echo "<td>" . $row2['name'] . "</td><td>" . $row2['amount'] . "</td>";
			   if ($first_row)
			     {
			       echo "<td><a href=\"/edit_ledger.php?id=" . $row['fk_txn'] . "\">Edit</a> ";
			       if ($row['has_pdf'] == 't')
				 {
				   echo "<a href=\"/get_pdf.php?id=" . $row['fk_txn'] . "\">Receipt</a>";
				 }
			       echo "</td>";
			       $first_row = FALSE;
			     }
			   else
			     {
			       echo "<td></td>";
			     }
			 }
		     }
	      elseif ($row['bank_type'] == 3)
		     { // bank reconciliation
		       echo "<td><a href=\"/edit_ledger.php?id=new&acct1=" . urlencode($row['fk_chart']) . "&amount1=" . urlencode($row['amount']) . "&comment=" . urlencode(strtolower($row['comment'])) . "&entered=" . urlencode($row['entered']) . "\">Create Entry</a></td>";
		     }
	      echo "</tr>";
	    }
	}
      elseif ($query == "totals")
	{
	  $result = pg_query_params("select \"name\",total_acct(\"name\",$2,$3) as total from chart where \"name\" = $1 or \"name\" ilike $1 || '/%' order by \"name\"",array($acct,$date1,$date2));
	  while ($row = pg_fetch_assoc($result))
	    {
	      echo "<tr><td>" . $row['name'] . "</td><td>" . $row['total'] . "</td></tr>";
	    }
	}
      echo "</table>";
    }
	  
?>
<p/>
<a href="/edit_ledger.php?id=new">New Ledger Entry</a>
</body></html>
		    