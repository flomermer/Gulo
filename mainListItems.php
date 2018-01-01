<!DOCTYPE html>
<html>
<head>    
    <?php
        $listID = $_GET["listID"];
        if (is_null($listID)){
            header("Location: mainLists.html");
            exit();
        }

        //php db connect to get details about list = listID
        $listName = "עדיאל בית";
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gulo</title>    

    <!-- Jquery CDN-->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
    <script defer src="https://use.fontawesome.com/releases/v5.0.2/js/all.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="includes/style.css" />
    <script src="includes/script.js"></script>        
</head>
<body id="pageMainListItems">
    <div class="container">
        <header>
            <div class="row">                    
                <div class="col-2">
                    <a href="mainLists.html"><i class="far fa-arrow-alt-circle-right"></i></a>
                </div>                
                <div class="col-8 text-center" id="mainListName"><?php echo $listName;?></div>                
                <div class="col-2 text-left">
                    <a href="addManualItem.html">
                        <i id="addManuallItem" class="fas fa-plus-circle"></i>
                    </a>
                </div>
            </div>
        </header>          
        <main>
            <div class="container">                        
                <ul class="ul-main-list">
                    <!--will be loadded from DB eventually-->
                    <li>
                        <span data-toggle="collapse" data-target="ul[data-cat='kitchen']">
                            <i class="fas fa-plus fa-1x"></i>מטבח (3)
                        </span>                        
                        <ul class="ul-main-list-category collapse" data-cat="kitchen">
                            <li>חלב</li>
                            <li>ביצים</li>
                            <li>שמן קנולה</li>
                        </ul>
                    </li>      
                    <li>
                        <span data-toggle="collapse" data-target="ul[data-cat='bathroom']">
                            <i class="fas fa-plus fa-1x"></i>שירותים ואמבטיה (4)
                        </span>                        
                        <ul class="ul-main-list-category collapse" data-cat="bathroom">
                            <li>שמפו - פינוק - לשיער רגיל</li>
                            <li>מרכך שיער - פינוק</li>
                            <li>סבון גוף - dove</li>
                            <li>נייר טואלט - האגיס</li>
                        </ul>
                    </li>
                    <li>
                        <span data-toggle="collapse" data-target="ul[data-cat='office']">
                            <i class="fas fa-plus fa-1x"></i>ציוד משרדי (5)
                        </span>
                        <ul class="ul-main-list-category collapse" data-cat="office">
                            <li>5 עט ירוק</li>
                            <li>2 עט כחול</li>
                            <li>מהדק</li>
                            <li>מספריים</li>
                            <li>מחדד</li>                            
                        </ul>
                    </li>
                </ul>               
            </div>
            <footer class="footer fixed-bottom">                      
                <div class="row">
                    <div class="col-12 text-left">
                        <span class="listOptions" data-option="addToCart">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                        <span class="listOptions" data-option="shareList">
                            <i class="fas fa-share-alt"></i>
                        </span>
                        <span class="listOptions" data-option="toggleListView" data-mode="1">
                            <i class="fas fa-list-ul"></i>
                        </span>
                    </div>                                            
                </div>
            </footer>
        </main>
                
        <footer class="footer fixed-bottom">
            <div class="container">      
                <a href="profile.html">
                    <div class="row">    
                        <div class="col-2"></div>
                        <div class="col-8 text-center">Adiel Perez</div>                    
                        <div class="col-2"><i class="fas fa-user-circle"></i></div>
                    </div>                
                </a>
            </div>                
        </footer>
        <footer class="footer fixed-bottom overlay"></footer>         
    </div>    
</body>
</html>