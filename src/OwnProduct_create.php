<?php
$db_connection = new MongoDB\Driver\Manager('mongodb://mongo:27017', array('USERNAME'=>'root', 'PASSWORD'=>'1111'));

$deal = "";
$price = "";
$succes = 0;
if(isset($_POST['deal'])){
    $errrors = "";
    if($_POST['deal'] == ""){
        $errrors .= "[Deal: empty]";
    }else{
        $deal = $_POST['deal'];
    }
    if($_POST['price'] == ""){
        $errrors .= "[CurrentPrice: empty]";
    }else{
        $price = $_POST['price'];
    }
    if($errrors == ""){

        $writer = new MongoDB\Driver\BulkWrite;
        $writer -> insert([
           'Deal' => [
               '$ref' => "Deal",
               '$id' => new \MongoDB\BSON\ObjectId($deal)
           ],
           'CurrentPrice' => $price
        ]);
        $result = $db_connection -> executeBulkWrite("Pawnshop.OwnProduct", $writer);
        $confirm = $result ->isAcknowledged();

        if (!$confirm){
            $success = 2;
            $write_errors = $result -> getWriteErrors();
            foreach ($write_errors as $one_error) {
                $errors .= '[' . $one_error->getMessage() . ']';
            }
        }else{
            $deal = "";
            $price = "";
            $succes = 1;
        }

    }else{
        $succes = 2;
    }
}

?>
<html>
<head>
    <link href="Styles/styles.CSS" rel="stylesheet" type="text/css">
    <link href="Styles/style2.css" rel="stylesheet" type="text/css">
    <meta charset="utf-8">
    <title>Create</title>
</head>
<header>
    <div style = "text-align: left; margin: 20px">
        <form action="OwnProduct.php">
            <button class="button-30" role="button">
                <-Back
            </button>
        </form>
    </div>
    <div class="hit-the-floor" style = "margin-bottom: 70px; font-size: 45px">
        Adding new OwnProduct
    </div>
</header>
<body>
<form action="" method="POST">
    <table align = "center" style = "width:20%">
        <tr>
            <td>
                <select name="deal" style="margin: 20px;">
                    <option selected value="">ProductCategory</option>
                    <?php
                    $query = new \MongoDB\Driver\Query([],[]);
                    $cursor = $db_connection -> executeQuery("Pawnshop.Deal", $query);
                    foreach($cursor as $document){
                        $query2 = new \MongoDB\Driver\Query([], []);
                        $cursor2 = $db_connection -> executeQuery("Pawnshop.OwnProduct", $query2);
                        $flag = false;
                        foreach ($cursor2 as $document2){

                            $id = null;
                            $int = 0;
                            foreach ($document2 -> Deal as $item){
                                $int += 1;
                                if ($int == 2){
                                    $id = $item;
                                }
                            }
                            if ($id == $document -> _id){
                                $flag = true;
                                break;
                            }

                        }
                        if ($flag) {continue;}
                        ?>
                        <option value="<?php echo $document -> _id ?>"><?php echo $document -> ProductDescription ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" id="input" name="price" value='<?php echo $price ?>' class="Input-text" placeholder="CurrentPrice">
                    <label for="input" class="Input-label">CurrentPrice</label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div style = "text-align: center; margin: 20px">
                    <button class="button-30" role="button">
                        Add one
                    </button>
                </div>
            </td>
        </tr>
    </table>
</form>
<?php if($succes == 1){ ?>
    <div class="alert success-alert">
        <h3>SUCCESS</h3>
        <a class="close">&times;</a>
    </div>

<?php }else if($succes == 2){ ?>
    <div class="alert danger-alert">
        <h3>Errors: <?php echo $errrors ?></h3>
        <a class="close">&times;</a>
    </div>
<?php  } ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    $(".close").click(function() {
        $(this)
            .parent(".alert")
            .fadeOut();
    });
</script>
</body>
</html>
