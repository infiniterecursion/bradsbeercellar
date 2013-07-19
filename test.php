<?php
require_once 'header.php';
require_once 'include.php';

echo '<p>Lookup UPC from web:</p>';
echo '<form name="label" method="post" action="test.php">';
echo '<input type="text" name="upc1" value="">';
echo '<input type="submit" name="submit1" value="Enter">';
echo '</form>';


if(isset($_POST['upc1']))
{
	$results = lookupUpc($_POST['upc1']);
	bug_print($results);
}

echo "<br /><br />";

echo "<p>Add info to Database:</p>";
echo '<form action="db.php" method="post">';
echo 'UPC: <input type="text" name="upc2">';
echo 'ID: <input type="text" name="id">';
echo 'QTY: <input type="text" name="qty">';
echo '<input type="submit">';
echo '</form>';

if(isset($_POST['upc2']))
{
	
	$sql="INSERT INTO stock (upc,id,type,qty) VALUES ('$_POST[upc2]','$_POST[id]',1,'$_POST[qty]')";

	if (!mysqli_query($con,$sql))
	{
		die('Error: ' . mysqli_error($con));
	}
	echo "1 record added";
	
	
	if(setInventory($_POST['upc2'], $_POST['id'], 1, $_POST['qty']))
	{
		fetchDetails($_POST['upc2']);
	}
	
}

echo "<br /><br />";

echo "<p>Submit UPC to BDB:</p>";
echo '<form action="test.php" method="post">';
echo 'UPC: <input type="text" name="upc3">';
echo 'ID: <input type="text" name="id">';
echo 'FluidSize: <input type="text" name="type">';
echo '<input type="submit">';
echo '</form>';

if(isset($_POST['upc3']))
{
	$results = submitBeerUpc($_POST['upc3'], $_POST['id'], $_POST['type']);
	bug_print($results);
	//echo $results['status'];
}

echo "<br /><br />";

require_once 'footer.php';
?>