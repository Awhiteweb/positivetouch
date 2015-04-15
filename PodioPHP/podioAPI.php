<?php
echo "<pre>";

date_default_timezone_set('Europe/London');

require_once 'podio-api/PodioAPI.php';

$client_id = "";
$client_token = "";
$app_id = ;
$appToken = "";

Podio::setup( $client_id, $client_token );
Podio::authenticate_with_app( $app_id, $appToken );

$count = PodioItem::get_count( $app_id );

// echo $count;

$titleCol = ;
$searchItem = ;
$calcField = ;
$dateField = ;

$today = Date("Y-m-d");

$options = array(
		"filters" => array( 
				"date" => array( 
						"from" => $today 
						)
				)
		);

$result = PodioItem::filter( $app_id, $options );

foreach ( $result as $item )
{
	$title = $item -> fields[ 'title' ] -> values;
	$searchItems = $item -> fields[ 'calculation-for-search-items' ] -> values;
 	$date = $item -> fields[ 'date' ] -> values;
	
	echo $title."\n";
	echo $date['start']->format("Y-m-d")."\n\n";

	
}

