
<?php
// Create connection
//$conn = new mysqli("182.50.133.55","auxstudDB7c","auxstud7cDB1!","auxstudDB7c");
$conn = new mysqli("localhost","mysql_montv","dinoflom","projectDB");

$conn->query("SET NAMES 'utf8'");

dropTables($conn);
createTables($conn);
initializeValues($conn);

$conn->close();




function createTables($conn){
    $conn->query("USE projectDB");

    $tables = array();

    $tables[] = "CREATE TABLE 238_products_categories (
                                category_id INT UNSIGNED AUTO_INCREMENT,
                                category_name VARCHAR(50) NOT NULL,

                                PRIMARY KEY (category_id)
                            ) ENGINE=InnoDB";

    $tables[] = "CREATE TABLE 238_products_brands (
                            brand_id INT UNSIGNED AUTO_INCREMENT,
                            brand_name VARCHAR(50) NOT NULL,

                            PRIMARY KEY (brand_id)
                    ) ENGINE=InnoDB";

    $tables[] =  "CREATE TABLE 238_brand_categories (
                                    brand_id INT UNSIGNED NOT NULL,
                                    category_id  INT UNSIGNED NOT NULL,

                                    PRIMARY KEY(brand_id,category_id),

                                    FOREIGN KEY (brand_id) REFERENCES 238_products_brands (brand_id)
                                    ON DELETE CASCADE
                                    ON UPDATE CASCADE,

                                    FOREIGN KEY (category_id) REFERENCES 238_products_categories (category_id)
                                    ON DELETE CASCADE
                                    ON UPDATE CASCADE
                                ) ENGINE=InnoDB";

    $tables[] =  "CREATE TABLE 238_capacity_units (
                                    unit_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                                    unit_name VARCHAR(50),
                                    unit_symbol VARCHAR(30),

                                    PRIMARY KEY(unit_id)
                                ) ENGINE=InnoDB";

    $tables[] = "CREATE TABLE 238_products (
                            barcode BIGINT UNSIGNED NOT NULL,
                            category_id INT UNSIGNED NOT NULL,
                            brand_id INT UNSIGNED NOT NULL,
                            capacity INT UNSIGNED,
                            capacity_unit_id INT UNSIGNED,
                            product_name VARCHAR(100) NOT NULL,
                            memo VARCHAR(100),
                            isValid SMALLINT(1) NOT NULL DEFAULT 1,

                            PRIMARY KEY (barcode),

                            FOREIGN KEY (category_id) REFERENCES 238_products_categories (category_id)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE,

                            FOREIGN KEY (brand_id) REFERENCES 238_products_brands (brand_id)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE,

                            FOREIGN KEY (capacity_unit_id) REFERENCES 238_capacity_units (unit_id)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE
                    ) ENGINE=InnoDB";

    $tables[] = "CREATE TABLE 238_users (
                                user_id INT UNSIGNED AUTO_INCREMENT,
                                mail VARCHAR(50) NOT NULL,

                                PRIMARY KEY (user_id)
                            ) ENGINE=InnoDB";

    $tables[] = "CREATE TABLE 238_lists (
                            list_id INT UNSIGNED AUTO_INCREMENT,
                            user_id INT UNSIGNED NOT NULL,
                            list_name VARCHAR(50) NOT NULL,

                            PRIMARY KEY (list_id),

                            FOREIGN KEY (user_id) REFERENCES 238_users (user_id)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE
                    ) ENGINE=InnoDB";

    $tables[] = "CREATE TABLE 238_lists_shares (
                            list_id INT UNSIGNED NOT NULL,
                            user_id INT UNSIGNED NOT NULL,

                            PRIMARY KEY (list_id,user_id),

                            FOREIGN KEY (list_id) REFERENCES 238_lists (list_id)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE,

                            FOREIGN KEY (user_id) REFERENCES 238_users (user_id)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE
                    ) ENGINE=InnoDB";


    $tables[] = "CREATE TABLE 238_list_products (
                            id INT UNSIGNED AUTO_INCREMENT,
                            list_id INT UNSIGNED NOT NULL,
                            barcode BIGINT UNSIGNED NOT NULL,
                            quantity INT UNSIGNED NOT NULL DEFAULT 1,
                            isChecked SMALLINT(1) NOT NULL DEFAULT 0,

                            PRIMARY KEY (id),

                            FOREIGN KEY (list_id) REFERENCES 238_lists (list_id)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE,

                            FOREIGN KEY (barcode) REFERENCES 238_products (barcode)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE
                    ) ENGINE=InnoDB";

    $tables[] = "CREATE TABLE 238_list_manual_products (
                            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                            list_id INT UNSIGNED NOT NULL,
                            product_name VARCHAR(100),
                            quantity INT NOT NULL DEFAULT 0,
                            isChecked SMALLINT(1) NOT NULL DEFAULT 0,

                            PRIMARY KEY(id)
                    ) ENGINE=InnoDB";

    foreach($tables as $table){
        createTable($table,$conn);
    }
}

function createTable($sql,$conn){
    $arr = explode(' ',trim($sql));
    $tableName = $arr[2];
    if ($conn->query($sql)==TRUE)
        echo "$tableName TABLE created successfully" . "<BR>";
    else
        echo $conn->error . "<BR><BR>";
}

function dropTables($conn){
    $sql = "DROP TABLE IF EXISTS
            238_list_products,
            238_list_manual_products,
            238_brand_categories,
            238_products,
            238_capacity_units,
            238_products_brands,
            238_products_categories,
            238_lists_shares,
            238_lists,
            238_users
            ";
    if($conn->query($sql)==FALSE)
        echo "drop failed: " . $conn->error;

    echo "all tables deleted successfully<BR><BR>";
}

function initializeValues($conn){
    $users =  "INSERT INTO 238_users (user_id,mail) VALUES
                            (1,'flom.tomer@gmail.com'),
                            (2,'adiel.perez@walla.co.il')
                            ";

    $products_categories =  "INSERT INTO 238_products_categories (category_id,category_name) VALUES
                            (1,'מטבח'),
                            (2, 'שירותים ואמבטיה'),
                            (3, 'ציוד משרדי')
                            ";

    $products_brands =  "INSERT INTO 238_products_brands (brand_id,brand_name) VALUES
                            (1,'צבר'),
                            (2, 'אחלה'),
                            (3, 'סנו'),
                            (4, 'עלית'),
                            (5, 'פיילוט')
                            ";

    $brand_categories =  "INSERT INTO 238_brand_categories (brand_id,category_id) VALUES
                            (1,1),
                            (2,1),
                            (3,1),(3,2),
                            (4,1)
                            ";

    $capacity_units = "INSERT INTO 238_capacity_units (unit_id,unit_name,unit_symbol) VALUES
                            (1,'גרם','גרם'),
                            (2,'מילי-ליטר','מל')
                            ";

    $products =  "INSERT INTO 238_products (barcode,category_id,brand_id,capacity,capacity_unit_id,product_name) VALUES
                            (1111,1,1,750,1,'חומוס'),
                            (2222,1,2,750,1,'חומוס'),
                            (3333,1,3,750,2,'סבון כלים'),
                            (4444,2,3,750,2,'סבון גוף'),
                            (5555,3,5,NULL,NULL,'עט')
                            ";
    $lists =  "INSERT INTO 238_lists (list_id,user_id,list_name) VALUES
                            (1,1,'תומר בית'),
                            (2,1,'תומר משרד'),
                            (3,2,'עדיאל בית')
                            ";

    $list_products = "INSERT INTO 238_list_products (list_id,barcode,quantity) VALUES
                            (1,1111,1),
                            (1,2222,1),
                            (1,3333,1),
                            (1,5555,1)
                            ";

    try{
        $conn->autocommit(FALSE);

        echo "<BR><BR>";

        $conn->query($users);
        $conn->query($products_categories);
        $conn->query($products_brands);
        $conn->query($brand_categories);
        $conn->query($capacity_units);
        $conn->query($products);
        $conn->query($lists);
        $conn->query($list_products);

        $conn->commit();

        echo "<BR> *** all initial values inserted successfully *** <BR><BR>";
    }
    catch (Exception $e) {
        $conn->rollback();
        echo "error: insert has failed";
    }
}

?>