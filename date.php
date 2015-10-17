<?php

function parse_date($date)
{
  $frag = explode("/",$date);
  $n = count($frag);
  $curr_month = intval(date("n"));
  $curr_year = intval(date("Y"));
  if ($n == 2)
    {
      $day = intval($frag[0]);
      $month = intval($frag[1]);
      if ($month > $curr_month)
	{
	  $year = $curr_year-1;
	}
      else
	{
	  $year = $curr_year;
	}
    }
  elseif($n == 3)
    {
      $day = intval($frag[0]);
      $month = intval($frag[1]);
      $year = intval($frag[2]);
      if ($year < 100)
	$year = $year+2000;
    }
  else
    {
      return $date;
    }
  return $year . "-" . $month . "-" . $day;
}


?>