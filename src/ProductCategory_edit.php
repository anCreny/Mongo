<?php
$db_connection = new MongoDB\Driver\Manager('mongodb://mongodb_mongo_1:27017', array('USERNAME'=>'root', 'PASSWORD'=>'1111'));

$document = null;
$id_for_rew = null;
if(isset($_POST['edite'])) {
    $id_for_rew = $_POST['edite'];
    $id = new \MongoDB\BSON\ObjectId($id_for_rew);
    $query = new \MongoDB\Driver\Query(['_id' => $id],[]);
    $cursor = $db_connection -> executeQuery("Pawnshop.ProductCategory", $query);
    $cursor -> rewind();
    $document = $cursor -> current();
}
$name = "";
$notes = "";
$succes = 0;
if(isset($_POST['name'])){
    $errors = "";
    if($_POST['name'] == ""){
        $name = $document -> name;
    }else{
        $name = $_POST['name'];
    }

    if($_POST['notes'] == ""){
        $notes = $document -> notes;
    }else{
        $notes = $_POST['notes'];
    }
    if($errors == ""){

        $updater = new MongoDB\Driver\BulkWrite;
        $id = new \MongoDB\BSON\ObjectId($document -> _id);
        $updater -> update(['_id' => $id],[
                'name' => $name,
                'notes' => $notes
        ]);
        $result = $db_connection ->executeBulkWrite("Pawnshop.ProductCategory", $updater);

        if ($result->getModifiedCount() == 0){
            $update_errors = $result->getWriteErrors();
            foreach ($update_errors as $error) {
                $errors .= '[' . $error->getMessage() . "]\n";
            }
            $success = 2;
        }else{
            $name = "";
            $notes = "";
            $succes = 1;
            $id = new \MongoDB\BSON\ObjectId($id_for_rew);
            $filter = ['_id' => $id];
            $options = [];
            $query = new MongoDB\Driver\Query($filter, $options);
            $cursor = $db_connection ->executeQuery("Pawnshop.ProductCategory", $query);
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
        <form action="ProductCategory.php">
            <button class="button-30" role="button">
                <-Back
            </button>
        </form>
    </div>
    <div class="hit-the-floor" style = "margin-bottom: 30px; font-size: 45px">
        Editing a ProductCategory
    </div>
</header>
<body>
<form action="" method="POST">
    <table style="width:70%; text-align:center; align-items:center; font-size:20px" align="center">
        <tr>
            <th>
                Name
            </th>
            <td>
                <?php echo $document -> name ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="name" id="input" value="<?php echo $name ?>" class="Input-text" placeholder="Name">
                    <label for="input" class="Input-label">Name</label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                Notes
            </th>
            <td>
                <?php echo $document -> notes ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="notes" id="input" value="<?php echo $notes ?>" class="Input-text" placeholder="Notes">
                    <label for="input" class="Input-label">Notes</label>
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
