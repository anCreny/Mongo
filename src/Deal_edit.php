<?php
$db_connection = new MongoDB\Driver\Manager('mongodb://mongo:27017', array('USERNAME'=>'root', 'PASSWORD'=>'1111'));

$document = null;
$id_for_rew = null;
if(isset($_POST['edite'])){
    $id_for_rew = $_POST['edite'];
    $id = new \MongoDB\BSON\ObjectId($id_for_rew);
    $query = new \MongoDB\Driver\Query(['_id' => $id],[]);
    $cursor = $db_connection -> executeQuery("Pawnshop.Deal", $query);
    $cursor -> rewind();
    $document = $cursor -> current();
}
$productCategory = "";
$client = "";
$productDescription = "";
$takingDate = "";
$returningDate = "";
$pledge = "";
$comission = "";
$succes = 0;
if(isset($_POST['prod'])){
    $errors = "";
    if($_POST['prod'] == ""){
        $category = $document -> Category;
        $int = 0;
        $id = null;
        foreach ($category as $c){
            $int += 1;
            if ($int == 2){
                $id = $c;
            }
        }
        $productCategory = $id;
    }else{
        $productCategory = $_POST['prod'];
    }

    if($_POST['client'] == ""){
        $client = $document -> Client;
        $int = 0;
        $id = null;
        foreach ($client as $c){
            $int += 1;
            if ($int == 2){
                $id = $c;
            }
        }
        $client = $id;
    }else{
        $client = $_POST['client'];
    }

    if($_POST['product'] == ""){
        $productDescription = $document -> ProductDescription;
    }else{
        $productDescription = $_POST['product'];
    }

    if($_POST['takdate'] == ""){
        $milliseconds = (string)$document -> TakingDate;
        $date = new \MongoDB\BSON\UTCDateTime((int)$milliseconds);
        $str = substr($date -> toDateTime() -> format(DATE_ATOM), 0, 10);
        $takingDate = $str;
    }else{
        $takingDate = $_POST['takdate'];
    }

    if($_POST['retdate'] == ""){
        $milliseconds = (string)$document -> ReturningDate;
        $date = new \MongoDB\BSON\UTCDateTime((int)$milliseconds);
        $str = substr($date -> toDateTime() -> format(DATE_ATOM), 0, 10);
        $returningDate = $str;
    }else{
        $returningDate = $_POST['retdate'];
    }
    if($_POST['pledge'] == ""){
        $pledge = (int)$document -> Pledge;
    }else{
        $pledge = (int)$_POST['pledge'];
    }
    if($_POST['comission'] == ""){
        $comission = (int)$document -> Comission;
    }else{
        $comission = (int)$_POST['comission'];
    }
    if($errors == ""){

            $updater = new MongoDB\Driver\BulkWrite;
            $id = new \MongoDB\BSON\ObjectId($document -> _id);
            $updater -> update(['_id' => $id],[
                'Client' => [
                    '$ref' => 'Client',
                    '$id' => new \MongoDB\BSON\ObjectId($client)
                ],
                'Category' => [
                    '$ref' => 'ProductCategory',
                    '$id' => new \MongoDB\BSON\ObjectId($productCategory)
                ],
                'ProductDescription' => $productDescription,
                'TakingDate' => new \MongoDB\BSON\UTCDateTime(new DateTime($takingDate)),
                'ReturningDate' => new \MongoDB\BSON\UTCDateTime(new DateTime($returningDate)),
                'Pledge' => $pledge,
                'Comission' => $comission
            ]);
            $result = $db_connection->executeBulkWrite("Pawnshop.Deal", $updater);

            if ($result->getModifiedCount() == 0){
                $update_errors = $result->getWriteErrors();
                foreach ($update_errors as $error) {
                    $errors .= '[' . $error->getMessage() . "]\n";
                }
                $success = 2;
            }else{
                $productCategory = "";
                $client = "";
                $productDescription = "";
                $takingDate = "";
                $returningDate = "";
                $pledge = "";
                $comission = "";
                $succes = 1;
                $id = new \MongoDB\BSON\ObjectId($id_for_rew);
                $filter = ['_id' => $id];
                $options = [];
                $query = new MongoDB\Driver\Query($filter, $options);
                $cursor = $db_connection ->executeQuery("Pawnshop.Deal", $query);
                $cursor ->rewind();
                $document = $cursor ->current();
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
        <form action="Deal.php">
            <button class="button-30" role="button">
                <-Back
            </button>
        </form>
    </div>
    <div class="hit-the-floor" style = "margin-bottom: 30px; font-size: 45px">
        Editing a Deal
    </div>
</header>
<body>
<form action="" method="POST">
    <table style="width:70%; text-align:center; align-items:center; font-size:20px" align="center">
        <tr>
            <th>
                Client
            </th>
            <td>
                <?php
                $int = 0;
                $id = null;
                foreach ($document -> Client as $c){
                    $int += 1;
                    if($int == 2){
                        $id = $c;
                    }
                }
                $Client_id = new \MongoDB\BSON\ObjectId($id);
                $query = new \MongoDB\Driver\Query(['_id' => $Client_id],[]);
                $cursor = $db_connection -> executeQuery("Pawnshop.Client", $query);
                $cursor -> rewind();
                $client = $cursor -> current();
                echo $client -> surname.' '.$client -> name; ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <select name="client" style="margin-top:20px; margin-bottom:20px">
                    <option selected value="">Client</option>
                    <?php
                    $query = new \MongoDB\Driver\Query([],[]);
                    $cursor = $db_connection -> executeQuery("Pawnshop.Client", $query);
                    foreach($cursor as $documentt){
                        ?>
                        <option value="<?php echo $documentt -> _id ?>"><?php echo $documentt -> surname." ".$documentt -> name ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>
                ProductCategory
            </th>
            <td>
                <?php
                $int = 0;
                $id = null;
                foreach ($document -> Category as $c){
                    $int += 1;
                    if($int == 2){
                        $id = $c;
                    }
                }
                $Category_id = new \MongoDB\BSON\ObjectId($id);
                $query = new \MongoDB\Driver\Query(['_id' => $Category_id],[]);
                $cursor = $db_connection -> executeQuery("Pawnshop.ProductCategory", $query);
                $cursor -> rewind();
                $category = $cursor -> current();
                echo $category -> name; ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <select name="prod" style="margin-top:20px; margin-bottom:20px">
                    <option selected value="">ProductCategory</option>
                    <?php
                    $query = new \MongoDB\Driver\Query([],[]);
                    $cursor = $db_connection -> executeQuery("Pawnshop.ProductCategory", $query);
                    foreach($cursor as $documentt){
                        ?>
                        <option value="<?php echo $documentt -> _id ?>"><?php echo $documentt -> name ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>
                ProductDescription
            </th>
            <td>
                <?php echo $document -> ProductDescription ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="product" id="input" value="<?php echo $productDescription; ?>" class="Input-text" placeholder="ProductDescription">
                    <label for="input" class="Input-label">ProductDescription</label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                TakingDate
            </th>
            <td>
                <?php $milliseconds = (string)$document -> TakingDate;
                $TakingDate = new \MongoDB\BSON\UTCDateTime((int)$milliseconds);
                $date = $TakingDate -> toDateTime() ->format(DATE_ATOM);
                echo substr($date, 0, 10) ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <input class = "date" type="date" name="takdate" value="<?php echo $takingDate; ?>" style="font-size: 1.2em;">
            </td>
        </tr>
        <tr>
            <th>
                ReturningDate
            </th>
            <td>
                <?php $milliseconds = (string)$document -> ReturningDate;
                $TakingDate = new \MongoDB\BSON\UTCDateTime((int)$milliseconds);
                $date = $TakingDate -> toDateTime() ->format(DATE_ATOM);
                echo substr($date, 0, 10) ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <input class = "date" type="date" name="retdate" value="<?php echo $returningDate; ?>" style="font-size: 1.2em;">
            </td>
        </tr>
        <tr>
            <th>
                Pledge
            </th>
            <td>
                <?php echo $document -> Pledge ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="pledge" id="input" value="<?php echo $pledge; ?>" class="Input-text" placeholder="Pledge">
                    <label for="input" class="Input-label">Pledge</label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                Comission
            </th>
            <td>
                <?php echo $document -> Comission ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="comission" id="input" value="<?php echo $comission; ?>" class="Input-text" placeholder="Comission">
                    <label for="input" class="Input-label">Comission</label>
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