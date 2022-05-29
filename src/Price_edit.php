<?php
$db_connection = new MongoDB\Driver\Manager('mongodb://mongodb_mongo_1:27017', array('USERNAME'=>'root', 'PASSWORD'=>'1111'));


$document = null;
$id_for_rew = null;
if(isset($_POST['edite'])) {
    $id_for_rew = $_POST['edite'];
    $id = new \MongoDB\BSON\ObjectId($id_for_rew);
    $query = new \MongoDB\Driver\Query(['_id' => $id], []);
    $cursor = $db_connection->executeQuery("Pawnshop.Price", $query);
    $cursor->rewind();
    $document = $cursor->current();
}

$product = "";
$price = "";
$succes = 0;
if(isset($_POST['product'])){
    $errors = "";
    if($_POST['product'] == ""){
        $OwnProduct = $document -> OwnProduct;
        $id = null;
        $int = null;
        foreach ($OwnProduct as $p) {
            $int += 1;
            if ($int == 2){
                $id = $p;
            }
        }

        $product = $id;
    }else{
        $product = $_POST['product'];
    }

    if($_POST['price'] == ""){
        $price = (int)$document -> Price;
    }else if (!ctype_digit($_POST['price'])){
        $errors .= "[Price: not only numbers]";
    }else{
        $price = (int)$_POST['price'];
    }
    if($errors == ""){

            $updater = new MongoDB\Driver\BulkWrite;
            $id = new \MongoDB\BSON\ObjectId($id_for_rew);
            $updater -> update(['_id' => $id], [
               'OwnProduct' => [
                   '$ref' => 'OwnProduct',
                   '$id' => new \MongoDB\BSON\ObjectId($product)
               ],
                'Price' => $price
            ]);
        $result = $db_connection->executeBulkWrite("Pawnshop.Price", $updater);

        if ($result->getModifiedCount() == 0){
            $update_errors = $result->getWriteErrors();
            foreach ($update_errors as $error) {
                $errors .= '[' . $error->getMessage() . "]\n";
            }
            $success = 2;
        }else{
            $product = "";
            $price = "";
            $succes = 1;
            $id = new \MongoDB\BSON\ObjectId($id_for_rew);
            $query = new \MongoDB\Driver\Query(['_id' => $id], []);
            $cursor = $db_connection -> executeQuery("Pawnshop.Price", $query);
            $cursor -> rewind();
            $document = $cursor -> current();
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
    <div class="hit-the-floor" style = "margin-bottom: 30px; font-size: 45px">
        Editing an Price
    </div>
</header>
<body>
<form action="" method="POST">
    <table style="width:70%; text-align:center; align-items:center; font-size:20px" align="center">
        <tr>
            <th>
                OwnProduct
            </th>
            <td>
                <?php
                $idOwnProduct = null;
                $ownproduct = $document -> OwnProduct;
                $int = 0;
                foreach ($ownproduct as $p){
                    $int += 1;
                    if ($int == 2){
                        $idOwnProduct = $p;
                    }
                }
                $query1 = new \MongoDB\Driver\Query(['_id' => new \MongoDB\BSON\ObjectId($idOwnProduct)], []);
                $cursor1= $db_connection -> executeQuery("Pawnshop.OwnProduct", $query1);
                $cursor1 -> rewind();
                $document1 = $cursor1 -> current();

                $idDeal = null;
                $deal = $document1 -> Deal;
                $int = 0;
                foreach ($deal as $p){
                    $int += 1;
                    if ($int == 2){
                        $idDeal = $p;
                    }
                }

                $query2 = new \MongoDB\Driver\Query(['_id' => new \MongoDB\BSON\ObjectId($idDeal)], []);
                $cursor2 = $db_connection -> executeQuery("Pawnshop.Deal", $query2);
                $cursor2 -> rewind();
                $document2 = $cursor2 -> current();

                echo $document2 -> ProductDescription;

                ?>
            </td>
            <td>
                --->
            </td>
            <td style="text-align:left">
                <select name="product" style="margin: 20px;">
                    <option selected value="">OwnProduct</option>
                    <?php
                    $query = new \MongoDB\Driver\Query([], []);
                    $cursor = $db_connection -> executeQuery("Pawnshop.OwnProduct", $query);

                    foreach($cursor as $documentt){
                        $idDeal = null;
                        $int = 0;
                        $deal = $documentt -> Deal;
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
                        <option value="<?php echo $documentt -> _id ?>"><?php echo $document1 -> ProductDescription ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>
                Price
            </th>
            <td>
                <?php echo $document -> Price ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="price" id="input" value="<?php echo $price; ?>" class="Input-text" placeholder="CurrentPrice">
                    <label for="input" class="Input-label">CurrentPrice</label>
                </div>
            </td>
        </tr>
    </table>
    <div style = "text-align: center; margin: 20px">
        <button class="button-30" role="button" name="edite" value="<?php echo $document -> _id ?>">
            Submit
        </button>
    </div>
</form>
<?php if($succes == 1){ ?>
    <div class="alert success-alert">
        <h3>SUCCESS</h3>
        <a class="close">&times;</a>
    </div>

<?php }else if($succes == 2){ ?>
    <div class="alert danger-alert">
        <h3>Errors: <?php echo $errors ?></h3>
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
