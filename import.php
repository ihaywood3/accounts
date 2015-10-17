<?php

include('connect.php');

$skip = 0;
if (isset($argv[3]))
  {
    $skip = intval($argv[3]);
  }
for ($i = 0; $i < $skip; $i++)
  {
    fgets(STDIN);
  }
if (! isset($argv[1]))
  {
    fwrite(STDERR, "Import CSV to bank statement. Arguments: chart [format] [skip]\nchart: ID of account to import into\nformat: format of CSV, otions: suncorp (default)\nskip: header lies of CSV to skip\n");
    exit(1);
  }
$acct = $argv[1];
if (isset($argv[2]))
  {
    $format = $argv[2];
  }
else
  {
    $format = 'suncorp';
  }

while ($line = fgetcsv(STDIN))
  {
    if ($format == 'suncorp')
      $params = array($line[0],$line[2],$line[1],$acct);

    $result = pg_query_params("select count(*) from statement where \"date\"=$1 and amount=$2 and \"comment\"=$3 and fk_chart=$4",$params);
    if (pg_fetch_result($result, 0) == 0)
      {
	pg_query_params("insert into statement (fk_chart, \"comment\", \"date\", amount) values ($4, $3, $1, $2)", $params);
      } 
  }

?>
