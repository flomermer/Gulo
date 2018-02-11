<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gulo - Demo Scan</title>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>    
    <script defer src="https://use.fontawesome.com/releases/v5.0.2/js/all.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
    <link rel="stylesheet" href="includes/style.css" />
    <script>
        function formSend(event) {
            event.preventDefault();
            var list_id, barcode;
            list_id = $("input[name=list_id]").val();
            barcode = $("input[name=barcode]").val();
            
            $(".alert").removeClass("alert-success").removeClass("alert-danger").html('<i class="fas fa-spinner fa-spin"></i>').css("visibility", "visible");            
            $.ajax({
                url: "server/set/scanBarcode.php?list_id=" + list_id + "&barcode=" + barcode,
                success: function (data) {
                    if (data == 'success') { //חיישן זיהה את הברקוד במערכת גולו
                        $(".alert").text("מוצר נוסף בהצלחה לרשימה").addClass("alert-success").css("visibility", "visible");
                    } else if (data == 'mailSent') {
                        $(".alert").html("ברקוד לא קיים במערכת<BR>נשלח מייל למשתמש").addClass("alert-danger").css("visibility", "visible");
                    } else if (data == 'noList') {
                        $(".alert").html("רשימה אינה קיימת במערכת").addClass("alert-danger").css("visibility", "visible");
                    } else {
                        $(".alert").css("visibility", "hidden");
                    }
                }
            })
        }
        $("document").ready(function () {
            $("#formScanDemo").on("submit", formSend);            
        })
    </script>
</head>
<body id="scanDemo">
    <main>
        <form id="formScanDemo">
            <div class="alert text-center">
                מוצר נוסף בהצלחה לרשימה
            </div>
            <div class="form-group">
                <input type="text" class="form-control" list="barcode" name="barcode" placeholder="מספר ברקוד..." required />
            </div>            
            <div class="form-group">
                <input type="number" class="form-control" name="list_id" placeholder="מספר רשימה..." min="1" step="1" value="1" required />
            </div>            
            <div class="form-group row">
                <div class="col-12 text-center">
                    <button type="submit" class="btn">
                        <i class="fas fa-barcode"></i> סריקה
                    </button>
                </div>
            </div>
        </form>
        <datalist id="barcode">
            <option value="4084500853553">סבון כלים - פיירי - קלאסי</option>
            <option value="7290008096317">משחת שיניים - פרודונטקס</option>
        </datalist>
    </main>    
</body>
</html>