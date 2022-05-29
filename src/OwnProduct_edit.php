<?php
$db_connection = new MongoDB\Driver\Manager('mongodb://mongo:27017', array('USERNAME'=>'root', 'PASSWORD'=>'1111'));

$document = null;
$id_for_rew = null;
if(isset($_POST['edite'])){
    $id_for_rew = $_POST['edite'];
    $id = new \MongoDB\BSON\ObjectId($id_for_rew);
    $query = new \MongoDB\Driver\Query(['_id' => $id], []);
    $cursor = $db_connection -> executeQuery("Pawnshop.OwnProduct", $query);
    $cursor -> rewind();
    $document = $cursor -> current();

}
$deal = "";
$price = "";
$succes = 0;
if(isset($_POST['deal'])){
    $errors = "";
    if($_POST['deal'] == ""){
        $d = $document -> Deal;
        $int = 0;
        $id = null;
        foreach ($d as $c){
            $int += 1;
            if ($int == 2){
                $id = $c;
            }
        }
        $deal = $id;
    }else{
        $deal = $_POST['deal'];
    }

    if($_POST['price'] == ""){
        $price = (int)$document -> CurrentPrice;
    }else if (!ctype_digit($_POST['price'])){
        $errors .= "[CurrentPrice: not only numbers]\n";
    }else{
        $price = (int)$_POST['price'];
    }
    if($errors == ""){
            $updater = new MongoDB\Driver\BulkWrite;
            $id = new \MongoDB\BSON\ObjectId($id_for_rew);
            $updater -> update(['_id' => $id],[
                    'Deal' => [
                            '$ref' => 'Deal',
                            '$id' => new \MongoDB\BSON\ObjectId($deal)
                    ],
                    'CurrentPrice' => $price
            ]);
            $result = $db_connection -> executeBulkWrite("Pawnshop.OwnProduct", $updater);

        if ($result->getModifiedCount() == 0){
            $update_errors = $result->getWriteErrors();
            foreach ($update_errors as $error) {
                $errors .= '[' . $error->getMessage() . "]\n";
            }
            $success = 2;
        }else{
                $deal = "";
                $price = "";
                $succes = 1;
                $id = new \MongoDB\BSON\ObjectId($id_for_rew);
                $query = new \MongoDB\Driver\Query(['_id' => $id], []);
                $cursor = $db_connection -> executeQuery("Pawnshop.OwnProduct", $query);
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
        <form action="OwnProduct.php">
            <button class="button-30" role="button">
                <-Back
            </button>
        </form>
    </div>
    <div class="hit-the-floor" style = "margin-bottom: 30px; font-size: 45px">
        Editing an OwnProduct
    </div>
</header>
<body>
<form action="" method="POST">
    <table style="width:70%; text-align:center; align-items:center; font-size:20px" align="center">
        <tr>
            <th>
                Product
            </th>
            <td>
                <?php
                $product = $document -> Deal;
                $int = 0;
                $id = null;
                foreach ($product as $p){
                    $int += 1;
                    if ($int == 2){
                        $id = $p;
                    }
                }
                $query = new \MongoDB\Driver\Query(['_id' => $id]);
                $cursor = $db_connection -> executeQuery("Pawnshop.Deal", $query);
                $cursor -> rewind();
                $documentt = $cursor -> current();
                echo $documentt -> ProductDescription;
                ?>
            </td>
            <td>
                --->
            </td>
            <td style="text-align:left">
                <select name="deal" style="margin: 20px;">
                    <option selected value="">ProductCategory</option>
                    <?php
                    $query = new \MongoDB\Driver\Query([],[]);
                    $cursor = $db_connection -> executeQuery("Pawnshop.Deal", $query);
                    foreach($cursor as $document1){
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
                            if ($id == $document1 -> _id){
                                $flag = true;
                                break;
                            }

                        }
                        if ($flag) {continue;}
                        ?>
                        <option value="<?php echo $document1 -> _id ?>"><?php echo $document1 -> ProductDescription ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>
                CurrentPrice
            </th>
            <td>
                <?php echo $document -> CurrentPrice ?>
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
