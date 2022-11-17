<?php
session_start();




 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: http://localhost/shop/login.php");
    exit;
}

include 'DBController.php';
require_once("dbcontroller.php");
$db_handle = new DBController();
if(!empty($_GET["action"])) {
switch($_GET["action"]) {
	case "add":
		if(!empty($_POST["quantity"])) {
			$productByCode = $db_handle->runQuery("SELECT * FROM tblproduct WHERE code='" . $_GET["code"] . "'");
			$itemArray = array($productByCode[0]["code"]=>array('name'=>$productByCode[0]["name"], 'code'=>$productByCode[0]["code"], 'quantity'=>$_POST["quantity"], 'price'=>$productByCode[0]["price"], 'image'=>$productByCode[0]["image"]));
			
			if(!empty($_SESSION["cart_item"])) {
				if(in_array($productByCode[0]["code"],array_keys($_SESSION["cart_item"]))) {
					foreach($_SESSION["cart_item"] as $k => $v) {
							if($productByCode[0]["code"] == $k) {
								if(empty($_SESSION["cart_item"][$k]["quantity"])) {
									$_SESSION["cart_item"][$k]["quantity"] = 0;
								}
								$_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
							}
					}
				} else {
					$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
				}
			} else {
				$_SESSION["cart_item"] = $itemArray;
			}
		}
	break;
	case "remove":
		if(!empty($_SESSION["cart_item"])) {
			foreach($_SESSION["cart_item"] as $k => $v) {
					if($_GET["code"] == $k)
						unset($_SESSION["cart_item"][$k]);				
					if(empty($_SESSION["cart_item"]))
						unset($_SESSION["cart_item"]);
			}
		}
	break;
	case "empty":
		unset($_SESSION["cart_item"]);
	break;	
}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
	<link rel="stylesheet" href="style2.css" />
	<link rel="stylesheet" href="style3.css" />
	
    <title>Sticky Navigation</title>
  </head>
  <body>
    <nav class="nav">
      <div class="container">
        <h1 class="logo"><a href="/index.html">My Website</a></h1>
        <ul>
          
          <li ><a href="index.html" accesskey="1" title="">Home</a></li>
          <li><a href="About us.html" accesskey="2" title="">About US</a></li>
          <li><a href="upload.html" accesskey="3" title="">Careers </a></li>
          <li><a href="orderonline.html" accesskey="4" title="">Order online </a></li>
          <li class="active"><a href="contactus.html" accesskey="5" title="">Contact Us</a></li>
          <li><a href="register.html" accesskey="6" title="">Register</a></li>
        
      </ul>
      </div>
    </nav>

    <div class="hero">
      <img src="logo.png " >
      <div class="container">
        <h1>Welcome To My Bread shop</h1>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Maiores, consequuntur?</p>
      </div>
    </div>
<div><a href="logout.php">here is logout</a></div>

<div id="shopping-cart">
<div class="txt-heading">Shopping Cart</div>

<a id="btnEmpty" href="orderonline.php?action=empty">Empty Cart</a>
<?php
if(isset($_SESSION["cart_item"])){
	$total_quantity = 0;
	$total_price = 0;
?>	
<table class="tbl-cart" cellpadding="10" cellspacing="1">
<tbody>
<tr>
<th style="text-align:left;">Name</th>
<th style="text-align:left;">Code</th>
<th style="text-align:right;" width="5%">Quantity</th>
<th style="text-align:right;" width="10%">Unit Price</th>
<th style="text-align:right;" width="10%">Price</th>
<th style="text-align:center;" width="5%">Remove</th>
</tr>	
<?php		
	foreach ($_SESSION["cart_item"] as $item){
		$item_price = $item["quantity"]*$item["price"];
	?>
		<tr>
		<td><img src="<?php echo $item["image"]; ?>" class="cart-item-image" /><?php echo $item["name"]; ?></td>
		<td><?php echo $item["code"]; ?></td>
		<td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
		<td  style="text-align:right;"><?php echo "$ ".$item["price"]; ?></td>
		<td  style="text-align:right;"><?php echo "$ ". number_format($item_price,2); ?></td>
		<td style="text-align:center;"><a href="orderonline.php?action=remove&code=<?php echo $item["code"]; ?>" class="btnRemoveAction"><img src="icon-delete.png" alt="Remove Item" /></a></td>
		</tr>
		<?php
		$total_quantity += $item["quantity"];
		$total_price += ($item["price"]*$item["quantity"]);
	}
	?>

<tr>
<td colspan="2" align="right">Total:</td>
<td align="right"><?php echo $total_quantity; ?></td>
<td align="right" colspan="2"><strong><?php echo "$ ".number_format($total_price, 2); ?></strong></td>
<td></td>
</tr>
</tbody>
</table>		
  <?php
} else {
?>
<div class="no-records">Your Cart is Empty</div>
<?php 
}
?>
</div>

<div id="product-grid">
  <div class="txt-heading">Products</div>
  <?php
  $product_array = $db_handle->runQuery("SELECT * FROM tblproduct ORDER BY id ASC");
  if (!empty($product_array)) { 
	foreach($product_array as $key=>$value){
  ?>
  
	<div class="image">
	  
	  <div class="image"><img src="<?php echo $product_array[$key]["image"]; ?>"/>
	  <button class="quick_look" data-id="<?php echo $product_array[$key]["id"] ; ?>">Quick Look</button>
	  </div>
	  <form method="post" action="orderonline.php?action=add&code=<?php echo $product_array[$key]["code"]; ?>">
	  <div class="product-tile-footer">
	  <div class="product-title"><?php echo $product_array[$key]["name"]; ?></div>
	  <div class="product-price"><?php echo "$".$product_array[$key]["price"]; ?></div>
	  <div class="cart-action"><input type="text" class="product-quantity" name="quantity" value="1" size="2" /><input type="submit" value="Add to Cart" class="btnAddAction" /></div>
	  </div>
	  
	  </form>
	  
	</div>
  <?php
	}
  }
  
  ?>
</div>
</div>	

	<div id="demo-modal"></div>

	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<script>
	$(".quick_look").on("click", function() {
		var product_id = $(this).data("id");
		  var options = {
			  modal: true,
			  height: 'auto',
			  width:'70%'
			};
		  $('#demo-modal').load('get-product-info.php?id='+product_id).dialog(options).dialog('open');
	});

	$(document).ready(function() {
		  $(".image").hover(function() {
				$(this).children(".quick_look").show();
			},function() {
				 $(this).children(".quick_look").hide();
			});
	});
	</script>
  
  </div>
  <div class="foot">
  <footer>
  <p>group:IT 208 </p>
  
</footer>
  </div>

</body>
</html>
