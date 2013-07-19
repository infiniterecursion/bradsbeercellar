<?php
/********************************************************************************
This page allows for the submitting of UPC codes to the BreweryDB database for a 
particular beer. Remember, you need to have a Premium developer account to access
this feature in the BDB API. Also, if you are testing the submission of data to 
the BDB database, make sure to set your API key to read-only until your functions
are fully tested. Read-only will allow your calls to function as they normally 
would, but will not write anything to the BDB database.

Note: I wrote this last, so it is probably the least tested part of the site.
Beware of rough edges :)
*******************************************************************************/
require_once 'include.php';
require_once 'header.php';

echo "<br /><br />";

switch($_POST['control'])
{
	case "check":
		//bug_print($_POST);
		$row = lookupId($_POST['id']);
		$breweries = getBreweries($_POST['id']);
		//bug_print($row);
		
		echo "<table id='list' align='center'><col><col id='middle'><col><col id='middle'><col><col><col><col><thead>";
		echo "<th></th><th class='center'>Beer</th><th>Abv.</th><th class='center'>Hops</th><th>SRM</th><th>Type</th>";
		echo "</thead>\n";
	
		echo "<tr>";
		echo "<td><a href='http://brewerydb.com/beer/" . $row['data']['id'] . "' target='_blank'>
						<img src='" . $row['data']['labels']['medium'] . "' height='150'></a></td>";
		echo "<td><table id='namegroup'><tr><td><h2>" . $row['data']['name'] . "</h2></td></tr>";
		echo "<tr><td><p id='style'><em>" . $row['data']['style']['name'] . "</em></p>";
		echo $breweries['data'][0]['name'] . "</td></tr></table></td>";
		echo "<td>" . $row['data']['abv'] . "</td>";
		echo "<td><img src='/kegerface/images/" . floor($row['data']['ibu']/20) . "-Hops.png' width='200'></td>";
		echo "<td><img src='/kegerface/images/SRM "	. $row['data']['srmId'] . ".png' height='75'></td>";
		echo "<td class='center'>" . $row['type'] . "</td>";
		echo "</tr>";
		echo "</table>";
		
		echo "<br />";
		echo "<p>Submit UPC: " . $_POST['upc3'] . " to BDB?</p>";
		echo '<form action="submit.php" method="post">';
		echo '<input type="hidden" name="control" value="submit">';
		echo '<input type="hidden" name="upc3" value="' . $_POST['upc3'] . '">';
		echo '<input type="hidden" name="id" value="' . $_POST['id'] . '">';
		echo '<input type="hidden" name="type" value="' . $_POST['type'] . '">';
		echo '<input type="submit" value="Are you Sure?">';
		echo '</form>';
		break;
		
	case "submit":
		//bug_print($_POST);
		$results = submitBeerUpc($_POST['upc3'], $_POST['id'], $_POST['type']);
		//bug_print($results);
		echo $results['status'] . "!";
		echo "<br /><br />";
		echo $results['message'];
		unset($results);
		break;
		
	default:
		echo "<p>Submit UPC to BDB:</p>";
		echo '<form action="submit.php" method="post">';
		echo 'UPC: <input type="text" name="upc3">  ';
		echo 'ID: <input type="text" name="id">  ';
		echo 'FluidSize: <input type="text" name="type">  ';
		echo '<input type="hidden" name="control" value="check">';
		echo '<input type="submit">';
		echo '</form>';
	
}

require_once 'footer.php';
?>