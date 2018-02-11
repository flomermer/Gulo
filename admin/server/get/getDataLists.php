<?php include('../../../server/dbDetails.php'); ?>
<?php
//header('Content-Type: application/json');

$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("SET NAMES 'utf8'");

/*SYSTEM DATA*/
$sql = "SELECT * FROM 238_products_categories";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()) {
        $json->system->categories[] = array(
            'category_id'        =>      $rs['category_id'],
            'category_name'      =>      $rs['category_name']
        );
    }
}

$sql = "
        SELECT brands.brand_id, brands.brand_name,
               GROUP_CONCAT(bc.category_id) AS brand_categories
        FROM 238_products_brands brands
        LEFT JOIN 238_brand_categories bc ON bc.brand_id=brands.brand_id
        GROUP BY brands.brand_id
        ";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()) {
        $json->system->brands[] = array(
            'brand_id'          =>      $rs['brand_id'],
            'brand_name'        =>      $rs['brand_name'],
            'brand_categories'  =>      $rs['brand_categories']
        );
    }
}

$sql = "SELECT * FROM 238_capacity_units";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()) {
        $json->system->capacity_units[] = array(
            'id'           =>      $rs['unit_id'],
            'symbol'       =>      $rs['unit_symbol'],
            'name'         =>      $rs['unit_name']
        );
    }
}

$json = json_encode($json);
echo $json;

$conn->close();
?>