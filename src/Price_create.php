<?php
$db_connection = new MongoDB\Driver\Manager('mongodb://mongodb_mongo_1:27017', array('USERNAME'=>'root', 'PASSWORD'=>'1111'));

$product = "";
$price = "";
$succes = 0;
if(isset($_POST['product'])){
    $errrors = "";
    if($_POST['product'] == ""){
        $errrors .= "[OwnProduct: empty]\n";
    }else{
        $product = $_POST['product'];
    }
    if($_POST['price'] == ""){
        $errrors .= "[CurrentPrice: empty]\n";
    }else if(ctype_digit($_POST['price'])){
        $price = (int)$_POST['price'];
    }else{
        $errrors .= "[Price: there's must be only numbers]\n";
    }
    if($errrors == ""){
        $writer = new MongoDB\Driver\BulkWrite;
        $writer ->insert([
                'OwnProduct' => [
                        '$ref' => 'OwnProduct',
                        '$id' => new \MongoDB\BSON\ObjectId($product)
                ],
                'Price' => $price
        ]);
        $result = $db_connection -> executeBulkWrite("Pawnshop.Price", $writer);
        $confirm = $result ->isAcknowledged();

        if (!$confirm){
            $success = 2;
            $write_errors = $result -> getWriteErrors();
            foreach ($write_errors as $one_error) {
                $errors .= '[' . $one_error->getMessage() . ']';
            }
        }else{
            $product = "";
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
        <form action="Price.php">
            <button class="button-30" role="button">
                <-Back
            </button>
        </form>
    </div>
    <div class="hit-the-floor" style = "margin-bottom: 70px; font-size: 45px">
        Adding new Price
    </div>
</header>
<body>

<form action="" method="POST">
    <table align = "center" style = "width:20%">
        <tr>
            <td>
                <select name="product" style="margin: 20px;">
                    <option selected value="">OwnProduct</option>
                    <?php
                    $query = new \MongoDB\Driver\Query([], []);
                    $cursor = $db_connection -> executeQuery("Pawnshop.OwnProduct", $query);

                    foreach($cursor as $document){
                        $idDeal = null;
                        $int = 0;
                        $deal = $document -> Deal;
                        foreach ($deal as $p){
                            $int += 1;
                            if ($int == 2){
                                $idDeal = $p;
                            }
                        }

                        $query1 = new \MongoDB\Driver\Query(['_id' => new \MongoDB\BSON\ObjectId($idDeal)]);
                        $cursor1 = $db_connection -> executeQuery("Pawnshop.Deal", $query1);
                        $cursor1 -> rewind();
                        $document1 = $cursor1 -> current();
                        ?>
                        <option value="<?php echo $document -> _id ?>"><?php echo $document1 -> ProductDescription ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" id="input" name="price" value='<?php echo $price ?>' class="Input-text" placeholder="Price">
                    <label for="input" class="Input-label">Price</label>
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
