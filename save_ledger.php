<?php

include('connect.php');
include('date.php');

if ($_POST['id'] == "new")
  {
    $result = pg_query_params("insert into txn (\"comment\",entered) values ($1,$2) returning (id)",array($_POST['comment'],parse_date($_POST['entered'])));
    $id = pg_fetch_result($result,'id');
  }
else
  {
    $id = $_POST['id'];
    pg_query_params("update txn set entered=$1,comment=$2 where id=$3",array(parse_date($_POST['entered']),$_POST['comment'],$id));
    pg_query_params("delete from split where fk_txn=$1",array($id));
  }

$acct_no = 1;
$balance = 0;
while (isset($_POST["acct" . $acct_no]))
  {
    $acct = $_POST["acct" . $acct_no];
    $amt = $_POST["amount" . $acct_no];
    if ($acct != "0")
      {
	if ($amt == "")
	  {
	    $amt = - $balance;
	  }
	else
	  {
	    $amt = str_replace("$","",$amt);
	    $amt = str_replace(",","",$amt);
	    $amt = floatval($amt);
	    $balance += $amt;
	  }
	pg_query_params("insert into split (fk_txn,fk_chart,amount) values ($1,$2,$3)", array($id,$_POST["acct" . $acct_no],$amt));
      }
    $acct_no++;
  }

if (isset($_FILES["receipt"]["tmp_name"]))
  {
    $fname = $_FILES["receipt"]["tmp_name"];
    if ($fname != "") {
      $pdf = pg_escape_bytea(readfile($fname));
      pg_query_params("update txn set pdf=$1 where id=$2",array($pdf,$id));
    }
  }

?>
<html><title>Saved transaction</title></head>
<body>
Transaction saved.
<a href="/edit_ledger.php?id=new">New Transaction</a>
</body>
</html>

