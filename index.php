<?php
include 'include.php';
include 'header.php';
// For anything above <body>, look in header.php.
// You are now inside the <body> tag.



echo "<div id='drink'>";
echo 'Scan to Drink: ';
echo '<form name="label" method="post" action="index.php">';
echo '<input type="text" id="upc" name="upc" value="">';
echo '<input type="submit" name="submit1" value="Drink!">';
echo '</form>';
echo '</div>';
//echo '<br />';


?>
<!-- Quick little script to always have the keyboard focus on the text box when page loads. -->
<!-- This makes it easier to use the page with a barcode scanner. -->
<script>
document.getElementById('upc').focus();
</script>
<?php

if(isset($_POST['upc']))
{
	//getInventory($_POST['upc']);
	//include 'query.php';
	//lookupUpc($_POST['upc']);
	drinkBeer($_POST['upc']);
}
showInventory3();

// footer.php closes out the </body> tag
// Maybe change this depending on how you feel about it.
include 'footer.php';
?>

