<html>
<head><title>Edit/New Account</title></head>
<body>

<form method="post" action="/save_ledger.php" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
<?php

include('connect.php');

function acct_field($acct_no,$selected=0,$amount="")
{
	echo "<tr><td><select name=\"acct" . $acct_no . "\">";
	$result = pg_query("select * from chart");
	echo "<option value=\"0\">None";
	error_log('$selected is ' . $selected);
	while ($row = pg_fetch_assoc($result))
	{
	  echo "<option value=\"" . $row['id'] . "\"";
	  error_log("row id for " . $row['name'] . " is " . $row['id'],4);
	  if ($row['id'] == $selected)
	    {
	      echo " selected";
	      error_log("SELECTED",4);
	    }
	  echo ">" . $row['name'];
	}
	echo "</select></td><td>";
	echo "<input type=\"text\" name=\"amount" . $acct_no . "\" value=\"" . $amount . "\" length=10></td></tr>";
}

$id = $_GET['id'];
echo "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\">";

if ($id != "new")
  {
    
    $result = pg_query_params("select to_char(entered,'dd/mm/yy') as entered, \"comment\", pdf is not null as has_pdf from txn where id=$1",array($id));
    $row = pg_fetch_assoc($result);
    $comment = $row['comment'];
    $has_pdf = $row['has_pdf'];
    $entered = $row['entered'];
    $new = false;
  }
else
  {
    $new = true;
    if (isset($_GET['comment']))
      {
	$comment = $_GET['comment'];
      }
    else
      $comment = "";
    if (isset($_GET['entered']))
      {
	$entered = $_GET['entered'];
      }
    else
      $entered = "";
    $has_pdf = "f";
  }
?>

Transaction Date: <input type="date" name="entered" length=10 value="<?= $entered ?>"><p/>
  Comment: <input type="text" name="comment" value="<?= $comment ?>" length=30><p/>
  Receipt: 
<input type="file" name="receipt">
<?php
  if ($has_pdf == "t")
    {
      echo "&nbsp;<a href=\"/get_pdf.php?id=" . $id . "\">(Existing)</a>";
    }
?>
<p/>
<table>
<?php

  if (! $new)
    {
      // we have some splits to edit in the DB
      $acct_no = 1;
      $result = pg_query_params("select * from split,chart where fk_txn = $1 and fk_chart = chart.id",array($id));
      while ($row = pg_fetch_assoc($result))
	{
	  acct_field($acct_no,$row['id'],$row['amount']);
	  $acct_no++;
	}
      acct_field($acct_no); // an a spare to add another split if required
    }
  else
    {
      if (isset($_GET["acct1"]))
	{
	  error_log("GET acct1=". intval($_GET['acct1']),4);
	  acct_field(1,$_GET['acct1'],$_GET['amount1']);
	} else {
	  acct_field(1);
	}
      acct_field(2);
      acct_field(3);
    }
?>
</table>
<p/>
<input type="submit"><input type="reset">
</form>
</body>
</html>
