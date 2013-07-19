<?php
include 'include.php';
include 'header.php';

$action = $_POST['action'];

echo "<table align='center' width='80%'>
		<tr>
			<td><form name=\"inventory\" action='inventory.php' method=\"post\">
				<input type=\"hidden\" name=\"action\" value=\"List\" />
				<input type=\"submit\" value=\"List All\" />
				</form>
			</td>
			<td><form name=\"inventory\" action='inventory.php' method=\"post\">
				<input type=\"hidden\" name=\"action\" value=\"new_stock\" />
				<input type=\"submit\" value=\"Add Stock\" />
				</form>
			</td>
			<td><form name=\"inventory\" action='inventory.php' method=\"post\">
				<input type=\"hidden\" name=\"action\" value=\"add_to\" />
				<input type=\"submit\" value=\"Restock\" />
				</form>
			</td>
			<td><form name=\"inventory\" action='inventory.php' method=\"post\">
				<input type=\"hidden\" name=\"action\" value=\"sub_from\" />
				<input type=\"submit\" value=\"De-Stock\" />
				</form>
			</td>
			<td><form name=\"inventory\" action='inventory.php' method=\"post\">
				<input type=\"hidden\" name=\"action\" value=\"set_to\" />
				<input type=\"submit\" value=\"Set Qty.\" />
				</form>
			</td>
			<td><form name=\"inventory\" action='inventory.php' method=\"post\">
				<input type=\"hidden\" name=\"action\" value=\"fix_values\" />
				<input type=\"submit\" value=\"Change data\" />
				</form>
			</td>
			<td><form name=\"inventory\" action='inventory.php' method=\"post\">
				<input type=\"hidden\" name=\"action\" value=\"refresh_all\" />
				<input type=\"submit\" value=\"Refresh All Data\" />
				</form>
			</td>
		</tr>
	</table>";
// Uncomment if you want inventory to always be displayed.
//showInventory3();


switch ($action)
{
	case "List":
		showInventory3();
		break;
	case "new_stock":
		
		echo '<br /><br />';
		echo '<form action="inventory.php" method="post">';
		echo 'UPC: <input type="text" name="upc"> ';
		echo 'ID: <input type="text" name="id"> ';
		echo 'QTY: <input type="text" name="qty">';
		echo '<input type="hidden" name="action" value="new_stock">';
		echo '<input type="submit">';
		echo '</form>';
		
		if(isset($_POST['upc']) || isset($_POST['id']))
		{
			
			if(setInventory($_POST['upc'], $_POST['id'], 1, $_POST['qty']))
			{
				fetchDetails($_POST['upc']);
				unset($_POST['action']);
			}
			
		}

		break;
	case "add_to":
		echo '<br /><br />';
		echo '<form action="inventory.php" method="post">';
		echo 'UPC: <input type="text" name="upc">';
		echo 'New Qty.: <input type="text" name="qty">';
		echo '<input type="hidden" name="action" value="add_to">';
		echo '<input type="submit" value="Add">';
		echo '</form>';
		
		if(isset($_POST['upc']))
		{
			if(addInventory($_POST['upc'], $_POST['qty']))
			{
				unset($_POST['action']);
			}
		}
		break;
	case "sub_from":
		echo '<br /><br />';
		echo '<form action="inventory.php" method="post">';
		echo 'UPC: <input type="text" name="upc">';
		echo 'New Qty.: <input type="text" name="qty">';
		echo '<input type="hidden" name="action" value="sub_from">';
		echo '<input type="submit" value="Subtract">';
		echo '</form>';
		
		if(isset($_POST['upc']))
		{
			if(minusInventory($_POST['upc'], $_POST['qty']))
			{
				unset($_POST['action']);
			}
		}
		break;
	case "set_to":
		echo '<br /><br />';
		echo '<form action="inventory.php" method="post">';
		echo 'UPC: <input type="text" name="upc">';
		echo 'New Qty.: <input type="text" name="qty">';
		echo '<input type="hidden" name="action" value="set_to">';
		echo '<input type="submit" value="Set">';
		echo '</form>';
		
		if(isset($_POST['upc']))
		{
			if(setQty($_POST['upc'], $_POST['qty']))
			{
				unset($_POST['action']);
			}
		}
		break;
	case "fix_values":
		echo '<br /><br />';
		echo '<form action="inventory.php" method="post">';
		echo 'UPC: <input type="text" name="upc">';
		echo 'ID: <input type="text" name="id">';
		echo 'QTY: <input type="text" name="qty">';
		echo '<input type="hidden" name="action" value="fix_values">';
		echo '<input type="submit">';
		echo '</form>';
		
		if(isset($_POST['upc']))
		{
			unset($_POST['action']);
			if(updateInventoryValues($_POST))
			{
				unset($_POST['action']);
			}
		}
		break;
	case "refresh_all":
		echo '<br /><br />';
		echo '<form action="inventory.php" method="post">';
		echo '<input type="hidden" name="action" value="refresh_all">';
		echo '<input type="hidden" name="confirm" value="yes">';
		echo '<input type="submit" Value="Are You Sure?">';
		echo '</form>';
		if(isset($_POST['confirm']))
		{
			unset($_POST['action']);
			if(reloadAllDetails())
			{
				unset($_POST['action']);
			}
		}
		break;
	default:
		break;
		
}


include 'footer.php';
?>
		