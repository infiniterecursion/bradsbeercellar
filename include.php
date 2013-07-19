<?php
include 'Pintlabs/Service/Brewerydb.php';

// Settings for your MySQL server. These should be pretty self-explanatory. Make sure $database is set to your correct database name.
$db_host = 'localhost';
$db_user = 'user';
$db_pwd = 'password';
$database = 'beerstock';

// $apikey: Set your BreweryDB.com API key here. Make sure the key is in quotes. To use UPC-related functions you will need to purchase
// a Premier developer account, for those API endpoints to work.
$apikey = "API-KeyGoesHere";

// Makes a straightline connection to database, should be replaced by createCon() in all functions. Keep until fully replaced in all functions!
// Should now be deprecated, use createCon() in all further functions.
$con=mysqli_connect($db_host, $db_user, $db_pwd, $database);
if (mysqli_connect_errno())
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Simple, but useful debugging method I wrote. 
// Prints a <pre> version of whatever array (including multi-dimesional)
// you feed it in a much more readable format.
// UGLY - Do not use in production - FOR DEBUG ONLY!
function bug_print( $results )
{
	echo "<pre>";
	print_r($results);
	echo "</pre>";
}



/********************************************************************************************************
//
//		This code-block is for functions that solely deal with BreweryDB API calls.
//
*******************************************************************************************************/


// When passed a upc code, this function queries the BDB website and returns any results in array? format.
function lookupUpc($upc)
{
	global $apikey;

	$bdb = new Pintlabs_Service_Brewerydb($apikey);
	$bdb->setFormat('json');
	$params = array('code' => $upc);
	try 
	{
		$results = $bdb->request('search/upc', $params, 'GET');
	}
	catch (Exception $e)
	{
	$results = array('error' => $e->getMessage());
	echo $bdb->getLastRawResponse();
	}
	echo $bdb->getLastRequestUri();
	return $results;
	
	bug_print($results);
}

// When passed an id string, this function queries the BDB website and returns any results.
function lookupId( $id )
{

	global $apikey;

	$bdb = new Pintlabs_Service_Brewerydb($apikey);
	$bdb->setFormat('json');
	
	try
	{
		$results = $bdb->request('beer/' . $id , null,'GET');
	}
	catch (Exception $e)
	{
	$results = array('error' => $e->getMessage());
	echo $bdb->getLastRawResponse();
	}
	
	//bug_print($results);
	return $results;
}

// When passed a beer ID string this function queries the BDB website and returns detailed info about the beer in
// an array???  Decided this probably wasn't needed, but left the stub just in case.
function getBeerDetails( $beer_id )
{
	
}

// When passed a beerID and UPC code, this function will submit the passed UPC code to the BreweryDB API to be attached to the beer 
// whose beerID is passed to the function.  -- May also need to pass fluidsize as well --
function submitBeerUpc( $upc, $id, $fluidsize )
{
	global $apikey;

	$bdb = new Pintlabs_Service_Brewerydb($apikey);
	$bdb->setFormat('json');
	
	$params = array("upcCode" => $upc, "fluidSizeId" => $fluidsize);
	try
	{
		$results = $bdb->request('beer/' . $id . '/upcs', $params,'POST');
	}
	catch (Exception $e)
	{
	$results = array('error' => $e->getMessage());
	echo $bdb->getLastRawResponse();
	}
	//echo $bdb->getLastRequestUri();
	return $results;
}

// This function will return all the breweries that brew the beer identified by $id.
function getBreweries( $id )
{
	global $apikey;
	$bdb = new Pintlabs_Service_Brewerydb($apikey);	
	$bdb->setFormat('json');
	$params = array('code' => $upc);
	try 
	{
		$results = $bdb->request('beer/' . $id . '/breweries', null,'GET');
	}
	catch (Exception $e)
	{
		$results = array('error' => $e->getMessage());
		echo $bdb->getLastRawResponse();
	}
	//echo $bdb->getLastRequestUri();
	return $results;
	
}
// End of BreweryDB function block.
/******************************************************************************************************/





/********************************************************************************************************
//
//		This code-block is for functions that solely deal with the MySQL database.
//
*******************************************************************************************************/



// Connects to local MySQL database, then returns the connection.
function createCon()
{
	global $db_host, $db_user, $db_pwd, $database;
	$set=mysqli_connect($db_host, $db_user, $db_pwd, $database);
	if (mysqli_connect_errno())
	{	
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	return $set;
}

// When passed a upc code, this function queries the SQL database and decrements the matching UPC by one.
function drinkBeer( $upc )
{
	$con=createCon();
	// WARNING! query will not decrement beer past 0. Make sure you keep your stock count accurate!
	if(!$result = mysqli_query($con, "UPDATE stock SET qty=qty-1 WHERE upc='" . $upc . "' and qty>0"))
	{
		die('Error[drinkBeer]: ' . mysqli_error($con));
	}
	
	echo "beer drunk!";
	
	mysqli_close($con);
}



// Adds the amount qty to the total quantity for the beer identified by upc.
function addInventory( $upc, $qty )
{
	$con=createCon();
	// WARNING! query will not decrement beer past 0. Make sure you keep your stock count accurate!
	if(!$result = mysqli_query($con, "UPDATE stock SET qty=qty+" . $qty . " WHERE upc='" . $upc . "'"))
	{
		die('Error[addInventory]: ' . mysqli_error($con));
	}
	
	echo "Inventory Added!";
	
	mysqli_close($con);
}

// Subtracts the amount qty from the total quantity for the beer identified by upc.
// Allows you to remove a specified amount of qty from an item's inventory.
function minusInventory( $upc, $qty )
{
	$con=createCon();
	// WARNING! query will not decrement beer past 0. Make sure you keep your stock count accurate!
	if(!$result = mysqli_query($con, "UPDATE stock SET qty=qty-" . $qty . " WHERE upc='" . $upc . "' and qty>0"))
	{
		die('Error[minusInventory]: ' . mysqli_error($con));
	}
	
	echo "Inventory Subtracted!";
	
	mysqli_close($con);
}

// Force an item with a specific upc to a specified qty in the database.
// This was intended to be used in situations were database quantities
// had gotten out of whack.
function setQty( $upc, $qty )
{
	$con = createCon();
	if(!$result = mysqli_query($con, "UPDATE stock SET qty=" . $qty . " WHERE upc='" . $upc . "'"))
	{
		die('Error[setQty]: ' . mysqli_error($con));
	}
	echo "Quantity Set!";
}

// Sets the current inventory for beer upc to $qty in the database.
// Note - Check into how to make certain arguments optional, allowing for 
// the modification of a current item as well.
function setInventory( $upc, $id, $type, $qty )
{
	$con=createCon();
	$sql="INSERT INTO stock (upc,id,type,qty) VALUES ('" . $upc . "','" . $id . "'," . $type . "," . $qty . ")";
	//echo $sql;
	if (!mysqli_query($con,$sql))
	{
		die('Error[setInventory]: ' . mysqli_error($con));
	}
	echo "1 record added";
	
	
	mysqli_close($con);
	return true;
}

// Broken - Designed this function to allow me to create a form
// that could be partially or fully filled out, and the function
// would update the corresponding fields in the database. Finishing
// this would probably allow greater control over inventory without
// manually editing the database, but I decided it would get less
// use than it was worth. I included it in the event someone wants
// to try to make it work.
function updateInventoryValues( $array )
{
	$sql="UPDATE stock SET ";
	var_dump($array);
	
	foreach ($array as $key => $value)
	{
		if($value != NULL)
		{
			$sql=$sql . $key . "=" . $value . ", ";
		}
	}
	echo $sql;
/*	while($field = mysqli_fetch_field($array))
	{
		echo $field->name . "=" . $field->value;
		echo "<br />";
		//$sql=$sql . {$field->name} . "=" . $field->value;
	} */
}


// Queries the current inventory for beer with upc.
// Displays the inventory information for the item specified by upc in an "echo" format. This should be changed to return the results instead.
function getInventory( $upc )
{
	$con=createCon();
	
	if(!$result = mysqli_query($con,"SELECT * FROM stock WHERE upc='" . $upc . "'"))
	{
		die('Error[getInventory]: ' . mysqli_error($con));
	}
	
	
	while($row = mysqli_fetch_array($result))
	{
		echo $row['upc'] . " " . $row['id'] . " " . $row['type'] . " " . $row['qty'];
		echo "<br />";
	}
	
	mysqli_close($con);
}


// Deprecated - replaced by showInventory2() which has a cleaner look.
// Queries the current inventory for beer with upc.
// Displays the information for all current items in the inventory in an HTML table format.
function showInventory()
{
	$con=createCon();
	if(!$result = mysqli_query($con, "SELECT * FROM stock WHERE qty > 0"))
	{
		die('Error[showInventory]: ' . mysqli_error($con));
	}
	
	/*echo "<pre>";
	print_r($result);
	echo "</pre>"; */
	
	$capacity = 24;
	$fields_num = mysqli_num_fields($result);
	echo "<table align='center'><col><col id='middle'><col><col id='middle'><col><col><col><col><thead>";
//	echo "<table align='center'><thead>";
	
	// printing table headers
	// this came from kegerface, but someone may find it useful if modding.
/*	for($i=0; $i<$fields_num; $i++)
	{    $field = mysqli_fetch_field($result);
		echo "<th>{$field->name}</th>";
	}
*/
	echo "<th></th><th>Beer</th><th>Brewery</th><th>Style</th><th>abv</th><th>Hops</th><th>srm</th><th>Type</th><th>Qty.</th>";
	echo "</thead>\n";
	
	while($row = mysqli_fetch_array($result))
	{
				
		echo "<tr>";
		// Uncomment these to show upc and id values. Make sure to create table headers for them if you do.
		//echo "<td>" . $row['upc'] . "</td>";
		//echo "<td>" . $row['id'] . "</td>";
		echo "<td><a href='http://brewerydb.com/beer/" . $row['id'] . "' target='_blank'><img src='" . $row['thumb'] . "' height='150'></a></td>";
		echo "<td><h2>" . $row['beer'] . "</h2></td>";
		echo "<td>" . $row['brewery'] . "</td>";
		echo "<td>" . $row['style'] . "</td>";
		echo "<td>" . $row['abv'] . "</td>";
		echo "<td><img src='/kegerface/images/" . floor($row['ibu']/20) . "-Hops.png' width='200'></td>";
		echo "<td><img src='/kegerface/images/SRM "	. $row['srm'] . ".png' height='75'></td>";
		echo "<td>" . $row['type'] . "</td>";
		//echo "<td><img src='/kegerface/images/kegs/"	. round(($row['qty']/$capacity), 1)*100	.	" .png' width='40'></td>";
		echo "<td>" . $row['qty'] . "</td>";
	}
	echo "</table>";
	
	mysqli_close($con);
	
}


// Newer version of showInventory(). This version makes some UI changes to the displayed data, and generally looks a lot better.
function showInventory2()
{
	$con=createCon();
	if(!$result = mysqli_query($con, "SELECT * FROM stock WHERE qty > 0"))
	{
		die('Error[showInventory]: ' . mysqli_error($con));
	}
	
	/*echo "<pre>";
	print_r($result);
	echo "</pre>"; */
	
	$capacity = 24;
	$fields_num = mysqli_num_fields($result);
	echo "<table id='list' align='center'><col><col id='middle'><col><col id='middle'><col><col><col><col><thead>";
//	echo "<table align='center'><thead>";
	
	// printing table headers
	// this came from kegerface, but someone may find it useful if modding.
/*	for($i=0; $i<$fields_num; $i++)
	{    $field = mysqli_fetch_field($result);
		echo "<th>{$field->name}</th>";
	}
*/
	echo "<th></th><th class='center'>Beer</th><th class='center'>Abv</th><th class='center'>Hops</th><th>SRM</th><th>Type</th><th>Qty.</th>";
	echo "</thead>\n";
	$i = 1;
	while($row = mysqli_fetch_array($result))
	{
		 
		// Allows you to set every-other row to have backgroud opacity using CSS.
		if(($i++)%2 == 0)
		{
			echo "<tr class='opaque'>";
		}
		else
		{
			echo "<tr>";
		}
		
		// Uncomment these to show upc and id values. Make sure to create table headers for them if you do.
		//echo "<td>" . $row['upc'] . "</td>";
		//echo "<td>" . $row['id'] . "</td>";
		echo "<td><a href='http://brewerydb.com/beer/" . $row['id'] . "' target='_blank'><img src='" . $row['thumb'] . "' height='150'></a></td>";
		echo "<td><table id='namegroup'><tr><td><h2>" . $row['beer'] . "</h2></td></tr>";
		echo "<tr><td><p id='style'><em>" . $row['style'] . "</em><br />";
		echo $row['brewery'] . "</p></td></tr></table></td>";
		echo "<td class='fontlarger'>" . $row['abv'] . "</td>";
		echo "<td><img src='/kegerface/images/" . floor($row['ibu']/20) . "-Hops.png' class='hops' ></td>";
		echo "<td><img src='/kegerface/images/SRM "	. $row['srm'] . ".png' height='75'></td>";
		echo "<td class='center fontlarger'>" . $row['type'] . "</td>";
		//echo "<td><img src='/kegerface/images/kegs/"	. round(($row['qty']/$capacity), 1)*100	.	" .png' width='40'></td>";
		echo "<td class='center fontlarger'>" . $row['qty'] . "</td>";
		echo "</tr>";
	}
	echo "</table>";
	
	mysqli_close($con);
	
}


// Third and final iteration of ShowInventory(), decided to play with the formatting a little and remove "Type" as the 
// Container type information from BDB website isn't reliable enough.
function showInventory3()
{
	$con=createCon();
	if(!$result = mysqli_query($con, "SELECT * FROM stock WHERE qty > 0"))
	{
		die('Error[showInventory]: ' . mysqli_error($con));
	}
	
	/*echo "<pre>";
	print_r($result);
	echo "</pre>"; */
	
	$capacity = 24;  // UNUSED - Allows setting of capacity limit for quantity images.
	$fields_num = mysqli_num_fields($result);
	echo "<table id='list' align='center'><col><col id='middle'><col><col id='middle'><col><col><col><col><thead>";
//	echo "<table align='center'><thead>";
	
	// printing table headers
	// this came from kegerface, but someone may find it useful if modding.
/*	for($i=0; $i<$fields_num; $i++)
	{    $field = mysqli_fetch_field($result);
		echo "<th>{$field->name}</th>";
	}
*/
	//echo "<th></th><th class='center'>Beer</th><th class='center'>Info</th><th>SRM</th><th>Type</th><th>Qty.</th>";
	echo "<th></th><th class='center'>Beer</th><th class='center'>Info</th><th>SRM</th><th>Qty.</th>";
	echo "</thead>\n";
	$i = 1;
	while($row = mysqli_fetch_array($result))
	{
		 
		// Allows you to set every-other row to have backgroud opacity using CSS.
		if(($i++)%2 == 0)
		{
			echo "<tr class='opaque'>";
		}
		else
		{
			echo "<tr>";
		}
		
		// Uncomment these to show upc and id values. Make sure to create table headers for them if you do.
		//echo "<td>" . $row['upc'] . "</td>";
		//echo "<td>" . $row['id'] . "</td>";
		echo "<td><a href='http://brewerydb.com/beer/" . $row['id'] . "' target='_blank'><img src='" . $row['thumb'] . "' height='150'></a></td>";
		echo "<td><table id='namegroup'><tr><td><h2>" . $row['beer'] . "</h2></td></tr>";
		
		echo "<tr><td><p id='style'><em>" . $row['style'] . "</em></p>";
		echo $row['brewery'] . "</td></tr></table></td>";
		//echo "<td class='fontlarger'>" . $row['abv'] . "</td>";
		echo "<td><table id='info'><tr><td><p id='ABV' class='center fontlarger'>". $row['abv'] . "%</div>&nbsp;&nbsp;abv</p></td></tr><tr><td><img src='/kegerface/images/" . floor($row['ibu']/20) . "-Hops.png' class='hops' ></td></tr></table></td>";
		echo "<td><img src='/kegerface/images/SRM "	. $row['srm'] . ".png' height='75'></td>";
		//echo "<td class='center fontlarger'>" . $row['type'] . "</td>";
		//echo "<td><img src='/kegerface/images/kegs/"	. round(($row['qty']/$capacity), 1)*100	.	" .png' width='40'></td>";
		echo "<td class='center fontlarger'>" . $row['qty'] . "</td>";
		echo "</tr>";
	}
	echo "</table>";
	
	mysqli_close($con);
	
}
// End of MySQL only block
/******************************************************************************************************/



/********************************************************************************************************
//
//		This code-block is for functions that access both MySQL and BreweryDB API.
//
*******************************************************************************************************/


// Queries BDB for detailed beer info and loads it into the database. If no information is present, it will fill all relevant fields. 
// If some info is already there, it will fill the rest of the fields missed by setInventory().
function fetchDetails( $upc )
{
	$con=createCon();
	
	global $apikey;

	$bdb = new Pintlabs_Service_Brewerydb($apikey);
	$bdb->setFormat('json');
	$params = array('code' => $upc);
	
	try
	{
		$results = $bdb->request('search/upc', $params, 'GET');
		// Brewery data is not returned from beer query, so a separate request must be made.
		$breweries = $bdb->request('beer/' . $results['data'][0]['id'] . '/breweries', null,'GET');
		
		$id = $results['data'][0]['id'];
		$thumb = $results['data'][0]['labels']['medium'];  		// Change 'medium' to 'icon' or 'large' for different image sizes.
		$name = mysql_real_escape_string($results['data'][0]['name']);  		// Some strings contain " or ' so 
		$brewery = mysql_real_escape_string($breweries['data'][0]['name']);  	// escaping is necessary.
		$style = $results['data'][0]['style']['name'];
		$abv = $results['data'][0]['abv'];  	// Careful: abv, ibu, and srm info 
		$ibu = $results['data'][0]['ibu'];  	// will not be set if the beer doesn't have info for them.
		$srm = $results['data'][0]['srmId'];
		$type = $results['data'][0]['fluidSize']['id'];
		
		
		$check = mysqli_query($con,"SELECT upc FROM stock WHERE upc = '" . $upc . "'");
		if(mysqli_num_rows($check) > 0)
		{
			$sql="UPDATE stock SET id='" . $id . "',
					  thumb='" . $thumb . "',
					  beer='" . $name . "',
					  brewery='" . $brewery . "',
					  style='" . $style . "',
					  abv='" . $abv . "',
					  ibu='" . $ibu . "',
					  srm='" . $srm . "',
					  type='" . $type . "' WHERE upc='" . $upc . "'";
			
			if (!mysqli_query($con,$sql))
			{
				die('Error[fetchDetails]: ' . mysqli_error($con));
			}
			echo "1 record updated";
		}
		else
		{
		
			$sql="INSERT INTO stock (upc,id,beer,brewery,style,abv,ibu,srm,type,qty) VALUES ('" . $upc . "',
					'" . $results['data'][0]['id'] . "',
					'" . $thumb . "',
					'" . $name . "',
					'" . $breweries['data'][0]['name'] . "',
					'" . $results['data'][0]['style']['name'] . "',
					'" . $abv . "',
					'" . $ibu . "',
					'" . $srm . "',
					'" . $type . "',
					'" . $qty . "')";
			//echo $sql;
			
			if (!mysqli_query($con,$sql))
			{
				die('Error[fetchDetails]: ' . mysqli_error($con));
			}
			echo "1 record added";
			}
		}
		catch (Exception $e)
		{
			$results = array('error' => $e->getMessage());
			echo $bdb->getLastRawResponse();
		}
	
		//bug_print($results);
	
	mysqli_close($con);
}

// This function overwrites the beer details in the MySQL database
// with fresh info queried from the BreweryDB website. This is useful
// for loading the database with the most up-to-date info possible
// for the beers in the database. If you change the size of the labels
// make sure you call this function to refresh the links in the db.
function reloadAllDetails()
{
	$con = createCon();
	
	if(!$result = mysqli_query($con, "SELECT * FROM stock"))
	{
		die('Error[showInventory]: ' . mysqli_error($con));
	}
	
	while($row = mysqli_fetch_array($result))
	{
		fetchDetails($row['upc']);
		echo $row['beer'] . " refreshed.";
	}
}
// End of mixed function block. 
?>