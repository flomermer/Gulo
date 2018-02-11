<?php include('../dbDetails.php'); ?>
<?php
header('Content-Type: application/json');

$list_id = $_GET["list_id"];

$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("SET NAMES 'utf8'");
$sql = "
        SELECT list_products.*, products.category_id, categories.category_name,
               GROUP_CONCAT(
                    DISTINCT CONCAT(list_products.id,'@@@',products.product_name,' - ',brands.brand_name,
                                    IFNULL(CONCAT(' - ',products.capacity),''), ' ', IFNULL(units.unit_symbol,''),
                                    '@@@',list_products.quantity,'@@@',list_products.isChecked)
                    SEPARATOR '***'
                ) AS productsStr
        FROM 238_list_products list_products
        LEFT JOIN 238_products products ON list_products.barcode = products.barcode
        LEFT JOIN 238_products_categories categories ON products.category_id=categories.category_id
        LEFT JOIN 238_products_brands brands ON products.brand_id=brands.brand_id
        LEFT JOIN 238_capacity_units units ON products.capacity_unit_id=units.unit_id
        WHERE list_id=$list_id
        GROUP BY categories.category_id
        ORDER BY categories.category_id ASC
        ";
$result = $conn->query($sql);

$json->subLists = array();
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()) {
        $items = array();

        $products = explode('***',$rs['productsStr']);
        foreach($products as $product){
            $data = explode('@@@',$product);
            $items[] = array(
                'id' => $data[0],
                'name'  => $data[1],
                'quantity' => $data[2],
                'isChecked' => $data[3]
            );
        }

        $json->subLists[] = array(
           'subListID'      =>      $rs['category_id'],
           'subListName'    =>      $rs['category_name'],
           'items'          =>      $items
       );
    }
}

$sql = "SELECT * FROM 238_list_manual_products WHERE list_id=$list_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $items = array();

    while($rs = $result->fetch_assoc()) {
        $items[] = array(
            'id'        => $rs['id'],
            'name'      => $rs['product_name'],
            'quantity'  => $rs['quantity'],
            'isChecked' => $rs['isChecked'],
            'isWaiting' => $rs['isWaiting']
        );
    }
    $json->subLists[] = array(
        'subListID'      =>      '-1',
        'subListName'    =>      'הוספה ידנית',
        'items'          =>      $items
    );
}

$json = json_encode($json);
echo $json;

$conn->close();


?>