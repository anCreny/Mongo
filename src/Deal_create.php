<?php
$db_connection = new MongoDB\Driver\Manager('mongodb://mongo:27017', array('USERNAME'=>'root', 'PASSWORD'=>'1111'));
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
        $errors .= "[ProductCategory: empty]";
    }else{
        $productCategory = $_POST['prod'];
    }
    if($_POST['client'] == ""){
        $errors .= "[Client: empty]";
    }else{
        $client = $_POST['client'];
    }
    if($_POST['product'] == ""){
        $errors .= "[ProductDescription: empty]";
    }else{
        $productDescription = $_POST['product'];
    }
    if($_POST['returningDate'] == ""){
        $errors .= "[ReturningDate: empty]";
    }else{
        $returningDate = $_POST['returningDate'];
    }
    if($_POST['takingDate'] == ""){
        $errors .= "[TakingDate: empty]";
    }else{
        $takingDate = $_POST['takingDate'];
    }
    if($_POST['pledge'] == ""){
        $errors .= "[Pledge: empty]";
    }else{
        $pledge = $_POST['pledge'];
    }
    if($_POST['comission'] == ""){
        $errors .= "[Comission: empty]";
    }else{
        $comission = $_POST['comission'];
    }
    if($errors == ""){

            $writes = new MongoDB\Driver\BulkWrite;
            $writes ->insert([
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
        $result = $db_connection->executeBulkWrite('Pawnshop.Deal', $writes);
        $confirm = $result -> isAcknowledged();

            if (!$confirm){
                $success = 2;
                $write_errors = $result -> getWriteErrors();
                foreach ($write_errors as $one_error) {
                    $errors .= '[' . $one_error->getMessage() . ']';
                }
            }else{
                $productCategory = "";
                $client = "";
                $productDescription = "";
                $takingDate = "";
                $returningDate = "";
                $pledge = "";
                $comission = "";
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
        <form action="Deal.php">
            <button class="button-30" role="button">
                <-Back
            </button>
        </form>
    </div>
    <div class="hit-the-floor" style = "margin-bottom: 70px; font-size: 45px">
        Adding new Deal
    </div>
</header>
<body>
<form action="" method="POST">
    <table align = "center" style = "width:20%">
        <tr>
            <td>
                <select name="prod" style="margin: 20px;">
                    <option selected value="">ProductCategory</option>
                    <?php
                    $query = new \MongoDB\Driver\Query([],[]);
                    $cursor = $db_connection -> executeQuery("Pawnshop.ProductCategory", $query);
                    foreach($cursor as $document){
                        ?>
                        <option value="<?php echo $document -> _id ?>"><?php echo $document -> name ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <select name="client" style="margin: 20px;">
                    <option selected value="">Client</option>
                    <?php
                    $query = new \MongoDB\Driver\Query([],[]);
                    $cursor = $db_connection -> executeQuery("Pawnshop.Client", $query);
                    foreach($cursor as $document){
                        ?>
                        <option value="<?php echo $document -> _id ?>"><?php echo $document -> surname." ".$document -> name ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" id="input" name="product" value='<?php echo $productDescription ?>' class="Input-text" placeholder="ProductDescription">
                    <label for="input" class="Input-label">ProductDescription</label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <label for="dateofbirth">TakingDate</label>
                <input class = "date" type="date" name="takingDate" value="<?php echo $takingDate ?>" id="dateofbirth">
            </td>
        </tr>
        <tr>
            <td>
                <label for="dateofbirth">ReturningDate</label>
                <input class = "date" type="date" name="returningDate" value="<?php echo $returningDate ?>" id="dateofbirth">
            </td>
        </tr>
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" id="input" name="pledge" value='<?php echo $pledge ?>' class="Input-text" placeholder="Pledge">
                    <label for="input" class="Input-label">Pledge</label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" id="input" name="comission" value='<?php echo $comission ?>' class="Input-text" placeholder="comission">
                    <label for="input" class="Input-label">Comission</label>
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