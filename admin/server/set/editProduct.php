<?php include('../../../server/dbDetails.php'); ?>
<?php
header('Content-Type: application/json');

$rowID              =   $_GET["hdnRowID"];
$barcode            =   $_GET["barcode"];
$category_id        =   $_GET["category_id"];
$category_name      =   $_GET["category"];
$brand_id           =   $_GET["brand_id"];
$brand_name         =   $_GET["brand"];
$capacity           =   $_GET["capacity"];
$capacity_unit_id   =   $_GET["capacity_unit_id"];
$capacity_unit_name =   $_GET["capacity_units"];
$product_name       =   $_GET["name"];

if($capacity=='')
    $capacity='NULL';

$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->query("SET NAMES 'utf8'");


/*check for existance*/
$sql = "SELECT barcode from 238_products WHERE barcode=$barcode";
$result = $conn->query($sql);
if($result->num_rows>0 && $rowID==''){
	$json->error = "alreadyExists";
    echo json_encode($json);    
	exit();
}

if($category_id=='null'){ //insert new category
	$sql = "INSERT INTO 238_products_categories (category_name) VALUES ('$category_name')";
	$conn->query($sql);

	$category_id = $conn->insert_id;
}

if($brand_id=='null'){ //insert new brand
	$sql = "INSERT INTO 238_products_brands (brand_name) VALUES ('$brand_name')";
	$conn->query($sql);

	$brand_id = $conn->insert_id;
}
if($capacity_unit_id=='null' && $capacity>0){ //insert new capacity_unit
	$sql = "INSERT INTO 238_capacity_units (unit_symbol) VALUES ('$capacity_unit_name')";
	$conn->query($sql);

	$capacity_unit_id = $conn->insert_id;
}


/*add to brand_categories*/
$sql = "INSERT INTO 238_brand_categories (brand_id,category_id) VALUES ($brand_id,$category_id)";
$conn->query($sql);



if(is_null($rowID) || $rowID==''){ //if new product-> insert it
    $sql = "INSERT INTO 238_products (barcode,category_id,brand_id,capacity,capacity_unit_id,product_name)
		    VALUES ($barcode,$category_id,$brand_id,$capacity,$capacity_unit_id,'$product_name')";
    $json->isNew=1;
} else { //edited product
    $sql = "UPDATE 238_products SET
                barcode=$barcode,
                category_id=$category_id,
                brand_id=$brand_id,
                capacity=$capacity,
                capacity_unit_id=$capacity_unit_id,
                product_name='$product_name'
            WHERE barcode=$barcode";
    $json->isNew=0;
}
$conn->query($sql);


/*return new product json*/
$sql = "
        SELECT products.*,
        cats.category_name, brands.brand_name,
        CONCAT(products.capacity,' ',units.unit_symbol) As capacityStr, units.unit_symbol AS capacity_unit_symbol
        FROM 238_products products
        LEFT JOIN 238_products_categories cats ON products.category_id=cats.category_id
        LEFT JOIN 238_products_brands brands ON products.brand_id=brands.brand_id
        LEFT JOIN 238_capacity_units units ON products.capacity_unit_id=units.unit_id
		WHERE products.barcode=$barcode
        ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $rs = $result->fetch_assoc();
    
	$json->rowID=$rs['barcode'];
	$json->name=$rs['product_name'];
	$json->category_name=$rs['category_name'];
    $json->brand_name=$rs['brand_name'];
	$json->capacityStr=$rs['capacityStr'];
	$json->category_id=$rs['category_id'];
	$json->brand_id=$rs['brand_id'];
	$json->capacity=$rs['capacity'];
	$json->capacity_unit_id=$rs['capacity_unit_id'];
    $json->capacity_unit_symbol=$rs['capacity_unit_symbol'];
}

$json = json_encode($json);
echo $json;

$conn->close();
?>