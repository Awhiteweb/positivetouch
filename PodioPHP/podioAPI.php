<?php

class Venue
{
	public $id;
	public $name;
	public $address;
}

class Sponsor
{
	public $id;
	public $name;
	public $descrip;
}

class Coffee
{
	public $day;
	public $startT;
	public $endT;
	public $venue = array();
	public $sponsor = array();
}

function venueList( $venue_id, $venue_token )
{
	
	// get items from venues app
	Podio::authenticate_with_app( $venue_id, $venue_token );
	
	$venueResult = PodioItem::filter( $venue_id );
	
	$venues = array();
	
	foreach ($venueResult as $v)
	{
		$vMod = new Venue();
		$vMod -> id = $v -> item_id;
		$vMod -> name = $v -> fields['title'] -> values;
		$vMod -> address = $v -> fields['address'] -> text;
	
		$venues[ $vMod -> id ] = $vMod;
	}
	
	return $venues;
}


function sponsorList( $sponsor_id, $sponsor_token)
{
	// get items from venues app
	Podio::authenticate_with_app( $sponsor_id, $sponsor_token );
	
	$sponsorResult = PodioItem::filter( $sponsor_id );
	
	$sponsors = array();
	
	foreach ($sponsorResult as $s)
	{
		$sMod = new Sponsor();
		$sMod -> id = $s -> item_id;
		$sMod -> name = $s -> fields['title'] -> values;
		$sMod -> descrip = $s -> fields['company'] -> values;
	
		$sponsors[ $sMod -> id ] = $sMod;
	}
	
	return $sponsors;
	
}

function coffeeList( $coffee_id, $coffee_Token, $venues, $sponsors )
{
	
	// get items from coffee mornings app.
	Podio::authenticate_with_app( $coffee_id, $coffee_Token );
	
	$today = Date("Y-m-d");
	
	$options = array(
			"filters" => array(
					"date" => array(
							"from" => $today
					)
			)
	);
	
	$result = PodioItem::filter( $coffee_id, $options );
	
	//print_r($venues);
	//exit();
	
	$coffeeArray = array();
	
	foreach ( $result as $item )
	{
	
		$cMod = new Coffee();
	
		$date = $item -> fields[ 'date' ] -> values;
	
		$cMod -> day = $date['start'] -> format("d, F Y");
		$cMod -> startT = $date['start'] -> format("H:i");
		$cMod -> endT = $date['end'] -> format("H:i");
	
		if( isset($item -> fields['venue-2']) )
		{
	
			$venuesRef = $item -> fields['venue-2']->values;
	
			foreach( $venuesRef as $venue )
			{
				$venueRefItemId = $venue->item_id;
					
				if( isset( $venues[ $venueRefItemId ] ) )
				{
					$cMod->venue[] = $venues[ $venueRefItemId ];
				}
					
			}
	
		}
	
		if( isset($item -> fields['external-guest']) )
		{
	
			$sponsorRef = $item -> fields['external-guest']->values;
	
			foreach( $sponsorRef as $venue )
			{
				$sponsorRefItemId = $venue->item_id;
	
				if( isset( $sponsors[ $sponsorRefItemId ] ) )
				{
					$cMod->sponsor[] = $sponsors[ $sponsorRefItemId ];
				}
	
			}
	
		}
	
		$coffeeArray[] = $cMod;
	
		//If has a reference field
	
		//if its a venue app
	
	}
	
	return $coffeeArray;
	
}


echo "<pre>";

date_default_timezone_set('Europe/London');

require_once '../podio-api/PodioAPI.php';

$client_id = "";
$client_token = "";
$coffee_id = 11599738;
$coffee_token = "9efeb69a6a084e6a85b2c56d4ae8b839";
$venue_id = 11987555;
$venue_token = "f9fd47d207d24911ae8d9b54e3315607";
$sponsor_id = 11599531;
$sponsor_token = "f6a2699283b8477abd26263656767b51";

Podio::setup( $client_id, $client_token );

$venues = venueList( $venue_id, $venue_token );

$sponsors = sponsorList( $sponsor_id, $sponsor_token );

$coffee = coffeeList( $coffee_id, $coffee_token, $venues, $sponsors );

foreach( $coffee as $espresso )
{
	
	$d = $espresso->day;
	$st = $espresso->startT;
	$et = $espresso->endT;
	echo "Day: " . $d . "\n";
	echo "Start: " . $st . "\n";
	echo "End: " . $et . "\n";
	
	if(isset($espresso->venue[0]))
	{
		$v = $espresso->venue[0]->name;
		$a = $espresso->venue[0]->address;
		echo "Venue: " . $v ."\n";
		echo "Address: " . $a . "\n";
	}

	if(isset($espresso->sponsor[0]))
	{
		$g = $espresso->sponsor[0]->name;
		echo "Guest Event: " . $g . "\n";
	}
	
	echo "\n";

}


