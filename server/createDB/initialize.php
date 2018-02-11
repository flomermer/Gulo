<?php include('../dbDetails.php'); ?>
<?php
$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$conn->query("SET NAMES 'utf8'");

dropTables($conn);
createTables($conn);
initializeValues($conn);

$conn->close();


function createTables($conn){
    $tables = array();

    $tables[] = "CREATE TABLE 238_users (
                                user_id INT UNSIGNED AUTO_INCREMENT,
                                mail VARCHAR(50) NOT NULL,
                                firstname VARCHAR(50) NOT NULL,
                                lastname VARCHAR(50) NOT NULL,

                                PRIMARY KEY (user_id)
                            ) ENGINE=InnoDB";

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
                            product_name VARCHAR(100) NOT NULL,
                            category_id INT UNSIGNED NOT NULL,
                            brand_id INT UNSIGNED NOT NULL,
                            capacity INT UNSIGNED,
                            capacity_unit_id INT UNSIGNED,
                            memo VARCHAR(100),

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
    $tables[] = "CREATE TABLE 238_products_to_confirm (
                            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                            barcode BIGINT UNSIGNED NOT NULL,
                            product_name VARCHAR(100) NOT NULL,
                            user_id INT UNSIGNED NOT NULL,
                            category_id INT UNSIGNED,
                            category_name VARCHAR(50),
                            brand_id INT UNSIGNED,
                            brand_name VARCHAR(50),
                            capacity INT UNSIGNED,
                            capacity_unit_id INT UNSIGNED,
                            capacity_unit_name VARCHAR(50),

                            PRIMARY KEY (id),

                            FOREIGN KEY (user_id) REFERENCES 238_users (user_id)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE,

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
                            isWaiting SMALLINT(1) NOT NULL DEFAULT 0,

                            PRIMARY KEY(id)
                    ) ENGINE=InnoDB";

    $tables[] = "CREATE TABLE 238_notifications_types (
                            notification_type_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                            info VARCHAR(100),
                            topic VARCHAR(100),

                            PRIMARY KEY(notification_type_id)
                    ) ENGINE=InnoDB";

    $tables[] = "CREATE TABLE 238_notifications (
                            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                            user_id INT UNSIGNED NOT NULL,
                            notification_type_id INT UNSIGNED NOT NULL,
                            id_1 BIGINT UNSIGNED,
                            id_2 BIGINT UNSIGNED,
                            datetime DATETIME,
                            isNew SMALLINT(1) DEFAULT 1,
                            isChecked SMALLINT(1) DEFAULT 0,

                            PRIMARY KEY(id),

                            FOREIGN KEY (user_id) REFERENCES 238_users (user_id)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE,

                            FOREIGN KEY (notification_type_id) REFERENCES 238_notifications_types (notification_type_id)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE
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
            238_notifications,
            238_notifications_types,
            238_list_products,
            238_list_manual_products,
            238_brand_categories,
            238_products,
            238_products_to_confirm,
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
    $users =  "INSERT INTO 238_users (user_id,mail,firstname,lastname) VALUES
                            (1,'danielle.tal22@gmail.com','Danielle','Tal'),
                            (2,'adielperez20@gmail.com','Adiel','Perez'),
                            (3,'danielle.tal22@gmail.com','Danielle','Tal')
                            ";

    $products_categories =  "INSERT INTO 238_products_categories (category_id,category_name) VALUES
                            (1,'מטבח'),
                            (2, 'שירותים ואמבטיה'),
                            (3, 'מוצרי ניקיון')
                            ";

    $products_brands =  "INSERT INTO 238_products_brands (brand_id,brand_name) VALUES
                            (1,'צבר'),
                            (2, 'אחלה'),
                            (3, 'סנו'),
                            (4, 'נסטלה'),
                            (5, 'סוגת'),
                            (6, 'פרודונטקס'),
                            (7, 'נטורל פורמולה'),
                            (8, 'פיירי'),
                            (9, 'AJAX')
                            ";

    $brand_categories =  "INSERT INTO 238_brand_categories (brand_id,category_id) VALUES
                            (1,1),
                            (2,1),
                            (3,1),(3,2),
                            (4,1),
                            (5,3)
                            ";

    $capacity_units = "INSERT INTO 238_capacity_units (unit_id,unit_name,unit_symbol) VALUES
                            (1,'גרם','גרם'),
                            (2,'מילי-גרם','מג'),
                            (3,'קילוגרם','קג'),
                            (4,'מילי-ליטר','מל'),
                            (5,'ליטר','ליטר')
                            ";

    $products =  "INSERT INTO 238_products (barcode,category_id,brand_id,capacity,capacity_unit_id,product_name) VALUES
                            (7290000072753,1,4,200,1,'קפה נמס מגורען טסטרס צויס'),
                            (7290003643387,1,5,1,3,'סוכן לבן'),
                            (7290001990094,1,5,1,3,'סוכר חום'),
                            (7290008096317,2,6,50,4,'משחת שיניים'),
                            (7290006287106,2,7,120,1,'ווקס לעיצוב אלסטי'),
                            (4084500853553,3,8,650,4,'סבון כלים קלאסי'),
                            (8718951001596,3,9,600,4,'תרסיס לניקוי')
                            ";
    $lists =  "INSERT INTO 238_lists (list_id,user_id,list_name) VALUES
                            (1,1,'הבית שלי')                            
                            ";

    $list_products = "INSERT INTO 238_list_products (list_id,barcode,quantity) VALUES
                            (1,7290006287106,1),
                            (1,7290008096317,1),
                            (1,7290003643387,1),
                            (1,7290000072753,1)
                            ";

    $notifications_types = "INSERT INTO 238_notifications_types (notification_type_id,topic) VALUES
                            (1,'מוצר נסרק ולא זוהה'),
                            (2,'בקשה לשיתוף רשימה')
                            ";
    $notifications = "INSERT INTO 238_notifications (user_id,notification_type_id,datetime,isNew,id_1,id_2) VALUES
                            (1, 1, '2009-01-02 22:15:00', 1, 9999,1),
                            (1, 1, '2008-01-02 22:15:00', 1, 8888,1),
                            (1, 1, '2007-01-02 22:15:00', 1, 7777,2)
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
        $conn->query($notifications_types);
        //$conn->query($notifications);
        $conn->commit();

        echo "<BR> *** all initial values inserted successfully *** <BR><BR>";
    }
    catch (Exception $e) {
        $conn->rollback();
        echo "error: insert has failed";
    }
}

?>