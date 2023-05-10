<?php
date_default_timezone_set("MST");
$today = Date("Y-m-d");
$begin = new DateTime( $today );
$end   = new DateTime( $today . ' +6 days' );
$end->setTime(0,0,1);
// $end   = $end->modify( '+1 day' );

$interval = new DateInterval('P1D');
$daterange = new DatePeriod($begin, $interval ,$end);
print_r($daterange);
foreach($daterange as $date){
    echo $date->format("Y-m-d") . "<br>";
}

echo "End of period is: " . $daterange->getEndDate()->format("Y-m-d");
// $test  = new DateTime(Date("Y-m-d"));
// echo $test->format("Y-m-d");
// $begin = new DateTime( '2012-08-01' );
// $end   = new DateTime( '2012-08-7' );
// $end   = $end->modify( '+1 day' );
//
// $interval = new DateInterval('P1D');
// $daterange = new DatePeriod($begin, $interval ,$end);
//
// foreach($daterange as $date){
//     echo $date->format("Y-m-d") . "<br>";
// }
//
// echo "End of period is: " . $daterange->getEndDate()->modify("-1 day")->format("Y-m-d");
?>