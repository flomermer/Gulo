<!DOCTYPE html>
<html>
<head>    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gulo</title>    

    <!-- Jquery CDN-->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>    
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
    <script defer src="https://use.fontawesome.com/releases/v5.0.2/js/all.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="includes/style.css" />
    <script src="includes/script.js"></script>
       
</head>
<body>
<?php 
    //$id = date("dmYHis");    
?>
    <div>
         <div class="page" data-page="mainLists" data-ajax="server/get/getLists.php">
            <header class="container-fluid">
                <div class="row">
                    <div class="col-2"></div>
                    <div class="col-8 text-center">רשימות קניות</div>
                    <div class="col-2 text-left">                        
                        <span class="toggleMenu">
                            <i class="fas fa-bars"></i>
                            <span class="badge">3</span>
                        </span>
                    </div>
                </div>            
            </header>
        
            <main class="container">
                <header class="row container-fluid">
                    <div class="col-12 text-center">
                        <button class="btn btn-lg switchPage" data-target="addMainList" id="newList">
                            רשימה חדשה <i class="fas fa-plus-circle"></i>
                        </button>
                    </div>
                </header>                                
                <div class="container" id="mainLists">   
                    <form id="formShowListItems" method="get" action="mainListItems.php">
                        <input type="hidden" name="listID" />
                    </form>
                    
                    <!-- 
                    **** Loaded Dynamically from js ****

                    <div class='row main-list'>              
                        <div class='col-6 text-right'>תומר</div>
                        <div class='col-4'><span class='quantity'>4</span> מוצרים</div>
                        <div class='col-2 text-left'><span class='fas fa-angle-left'></span></div>
                    </div>
                    -->                
                </div>                        
            </main>
        </div>
        
        <div class="page" data-page="addMainList">
            <header class="container-fluid">
                <div class="row">
                    <div class="col-2 text-right">
                        <span class="switchPage" data-target="mainLists">
                            <i class="far fa-arrow-alt-circle-right"></i>
                        </span>
                    </div>
                    <div class="col-8 text-center">רשימה חדשה</div>
                    <div class="col-2 text-left">
                        <span class="toggleMenu"><i class="fas fa-bars"></i></span>
                    </div>
                </div>
            </header>
            <main>
                <div class="container">
                    <form action="addMainList.php" id="formAddMainList" method="post">
                        <div class="form-group text-right">
                            <label for="txtAddMainListName">שם הרשימה:</label>
                            <input type="text" class="form-control" name="txtAddMainListName" placeholder="לדוגמא: הבית של פיסטוק" required />
                        </div>
                        <div class="form-group row text-right rowShareMemeber">
                            <label class="col-8" for="txtMainListShare[]">שיתוף:</label>
                            <div class="col-4 text-left">
                                <span class="input-group-l" id="btnNewListSharePlus">
                                    <i class="fas fa-user-plus"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-lg">
                                    <i class="fas fa-cart-plus"></i> צור רשימה  חדשה
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>

        <div class="page" data-page="mainListItems">
            <header class="container-fluid">
                <div class="row">
                    <div class="col-2 text-right">
                        <span class="switchPage" data-target="mainLists">
                            <i class="far fa-arrow-alt-circle-right"></i>
                        </span>
                    </div>
                    <div class="col-8 text-center" id="mainListName">שם הרשימה</div>
                    <div class="col-2 text-left">                        
                        <span class="toggleMenu"><i class="fas fa-bars"></i></span>
                    </div>
                </div>
            </header>
            <main>
                <header class="container">
                    <div class="row">
                        <div class="col-6 text-right">
                            <button class="btn btn-md switchPage" data-target="addManualItem">
                                פריט חדש <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                        <div class="col-6 text-left">
                            <button class="btn btn-md switchPage" data-target="addToCartConfirmation">
                               שלח online <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>                    
                    </div>                    
                </header>                
                <div class="container">
                    <ul class="ul-main-list">
                        <!--will be loadded from DB eventually. for now just from a js json -->                        
                    </ul>
                </div>                
            </main>
            <!--
            <footer class="secondary">                
                <span class="listOptions" data-option="addToCart">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <span class="listOptions" data-option="shareList">
                    <i class="fas fa-share-alt"></i>
                </span>
                <span class="listOptions" data-option="toggleListView" data-mode="1">
                    <i class="fas fa-list-ul"></i>
                </span>                                    
            </footer>
            -->
        </div>

        <div class="page" data-page="addManualItem">
            <header class="container-fluid">
                <div class="row">
                    <div class="col-2 text-right">
                        <span class="switchPage" data-target="mainListItems">
                            <i class="far fa-arrow-alt-circle-right"></i>
                        </span>
                    </div>
                    <div class="col-8 text-center">הוספת פריט ידנית</div>
                    <div class="col-2 text-left">
                        <span class="toggleMenu"><i class="fas fa-bars"></i></span>
                    </div>
                </div>
            </header>
            <main>
                <div class="container">
                    <form id="formAddManualItem" method="post">
                        <div class="form-group text-right">
                            <label for="txtAddListItemName">שם הפריט:</label>
                            <input type="text" class="form-control" name="txtAddListItemName" id="txtAddListItemName" placeholder="לדוגמא: קפה נמס" required />
                        </div>
                        <div class="form-group row text-right">
                            <label for="txtAddListItemQuantity" class="col-12">כמות:</label>
                            <div class="col-3">
                                <input type="number" class="form-control" name="txtAddListItemQuantity" id="txtAddListItemQuantity" value="1" min="1" max="99" step="1" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-lg">
                                    <i class="fas fa-cart-plus"></i> הוספת פריט חדש
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>
        <div class="page" data-page="addToCartConfirmation">
            <header class="container-fluid">
                <div class="row">
                    <div class="col-2">
                        <span class="switchPage" data-target="mainListItems">
                            <i class="far fa-arrow-alt-circle-right"></i>
                        </span>
                    </div>
                    <div class="col-8 text-center">הזמנת משלוח</div>
                    <div class="col-2 text-left">
                        <span class="toggleMenu"><i class="fas fa-bars"></i></span>
                    </div>
                </div>
            </header>
            <main>
                <div class="container">
                    <div class="row col-12 text-center">
                        המשלוח נשלח בהצלחה לרשת הקניות
                        <br /><br />
                        ניתן להתעדכן בסטטוס המשלוח בדף הפרופיל
                    </div>
                </div>
            </main>
        </div>

        <div class="page" data-page="profile" data-effect="blind">
            <header class="container-fluid">
                <div class="row">
                    <div class="col-2 text-right">
                        <span class="goBack">
                            <i class="far fa-arrow-alt-circle-right"></i>
                        </span>
                    </div>
                    <div class="col-8 text-center">הפרופיל שלי</div>
                    <div class="col-2 text-left"><i class="fas fa-user-circle"></i></div>                    
                </div>
            </header>
            <main>
                <div class="container-fluid">   
                    <div class="imgProfile">
                        <img src="images/misc/profilePic.png"/>
                        <div class="col-12 text-center">
                            מספר מזהה: 202212
                        </div>
                    </div>                        
                    
                    <div class="row profileRow">                        
                        <div class="col-12 text-center">
                            <i class="fas fa-shopping-cart"></i>
                            ההזמנות שלי
                        </div>
                    </div>                    
                    <div class="row profileRow">                        
                        <div class="col-12 text-center">
                            <i class="fas fa-comment"></i>
                            התראות <span class="notification-counter"></span>
                        </div>
                    </div>                    
                    <div class="row profileRow">                                                
                        <div class="col-12 text-center">                            
                            <i class="fas fa-envelope"></i>
                            כתבו לנו
                        </div>
                    </div>                          
                    <div class="row profileRow">
                        <div class="col-12 text-center">
                            <i class="fas fa-power-off"></i>    
                            התנתקות
                        </div>
                    </div>                          
                </div>
            </main>
        </div>
        <footer class="footer fixed-bottom main">                        
            <div class="switchPage" data-target="profile">
                <div class="row container-fluid content" id="profileRow">                   
                    <div class="col-2 text-left"><i class="fas fa-user-circle fa-lg"></i></div>
                    <div class="col-8 text-center">Adiel Perez</div>                        
                    <div class="col-2 text-right"><i class="fas fa-circle fa-sm"></i></div>
                </div>            
                <div class="row container-fluid" id="logoRow">
                    <div class="container text-center">
                        <a href="#" id="logoMenu"></a>
                    </div>                    
                </div>
            </div>
        </footer>
    </div>    
</body>
</html>