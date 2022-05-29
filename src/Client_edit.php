<?php
$db_connection = new MongoDB\Driver\Manager('mongodb://mongodb_mongo_1:27017', array('USERNAME'=>'root', 'PASSWORD'=>'1111'));

$document = null;
$id_for_rew = null;
if(isset($_POST['edite'])){
    $id_for_rew = $_POST['edite'];
    $id = new \MongoDB\BSON\ObjectId($id_for_rew);
    $filter = ['_id' => $id];
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $db_connection ->executeQuery("Pawnshop.Client", $query);
    $cursor ->rewind();
    $document = $cursor ->current();
}
$name = "";
$surname = "";
$patronymic = "";
$paperNumber = "";
$paperSeries = "";
$success = 0;
if(isset($_POST['name'])){
    $errors = "";
    if($_POST['name'] == ""){
        $name = $document -> name;
    }else{
        $name = $_POST['name'];
    }

    if($_POST['surname'] == ""){
        $surname = $document -> surname;
    }else{
        $surname = $_POST['surname'];
    }

    if($_POST['patronymic'] == ""){
        $patronymic = $document -> patronymic;
    }else{
        $patronymic = $_POST['patronymic'];
    }

    if($_POST['papernumber'] == ""){
        $paperNumber = $document -> paper -> number;
    }else if (sizeof($_POST['papernumber']) != 6) {
        $errors .= "[Papernumber: must be only 6n.]";
    }else if(ctype_digit($_POST['papernumber'])){
        $paperNumber = $_POST['papernumber'];
    }else{
        $errors .= "[PaperNumber: not only numbers]\n";
    }

    if($_POST['paperseries'] == ""){
        $paperSeries = $document -> paper -> series;
    }else if (sizeof($_POST['paperseries']) != 4) {
        $errors .= "[Paperseries: must be only 4n.]";
    }else if(ctype_digit($_POST['paperseries'])){
        $paperSeries = $_POST['paperseries'];
    }else {
        $errors .= "[PaperSeries: not only numbers]\n";
    }
        if ($errors == "") {

            $updater = new MongoDB\Driver\BulkWrite;
            $id = new \MongoDB\BSON\ObjectId($document -> _id);
            $updater->update(['_id' => $id], [
                'name' => $name,
                'surname' => $surname,
                'patronymic' => $patronymic,
                'paper' => [
                    'number' => $paperNumber,
                    'series' => $paperSeries
                ]
            ]);
            $result = $db_connection->executeBulkWrite("Pawnshop.Client", $updater);

            if ($result->getModifiedCount() == 0) {
                $update_errors = $result->getWriteErrors();
                foreach ($update_errors as $error) {
                    $errors .= '[' . $error->getMessage() . "]\n";
                }
                $success = 2;
            } else {
                $name = "";
                $surname = "";
                $patronymic = "";
                $paperNumber = "";
                $paperSeries = "";
                $success = 1;
                $id = new \MongoDB\BSON\ObjectId($id_for_rew);
                $filter = ['_id' => $id];
                $options = [];
                $query = new MongoDB\Driver\Query($filter, $options);
                $cursor = $db_connection ->executeQuery("Pawnshop.Client", $query);
                $cursor ->rewind();
                $document = $cursor ->current();
            }

        } else {
            $success = 2;
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
        <form action="Client.php">
            <button class="button-30" role="button">
                <-Back
            </button>
        </form>
    </div>
    <div class="hit-the-floor" style = "margin-bottom: 30px; font-size: 45px">
        Editing a Client
    </div>
</header>
<body>
<form action="" method="POST">
    <table style="width:50%; text-align:center; align-items:center; font-size:20px" align="center">
        <tr>
            <th>
                Surname
            </th>
            <td>
                <?php echo $document -> surname ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="surname" value="<?php echo $surname; ?>" id="input" class="Input-text" placeholder="Surname">
                    <label for="input" class="Input-label">Surname</label>
                </div>
            </td>
        </tr>
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
                    <input type="text" name="name" id="input" value="<?php echo $name; ?>" class="Input-text" placeholder="Name">
                    <label for="input" class="Input-label">Name</label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                Patronymic
            </th>
            <td>
                <?php echo $document -> patronymic ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="patronymic" id="input" value="<?php echo $patronymic; ?>" class="Input-text" placeholder="Patronymic">
                    <label for="input" class="Input-label">Patronymic</label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                PaperNumber
            </th>
            <td>
                <?php echo $document -> paper -> number ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="papernumber" id="input" value="<?php echo $paperNumber; ?>" class="Input-text" placeholder="PaperNumber">
                    <label for="input" class="Input-label">PaperNumber</label>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                PaperSeries
            </th>
            <td>
                <?php echo $document -> paper -> series ?>
            </td>
            <td>
                --->
            </td>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="paperseries" id="input" value="<?php echo $paperSeries; ?>" class="Input-text" placeholder="PaperSeries">
                    <label for="input" class="Input-label">PaperSeries</label>
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
<?php if($success == 1){ ?>
    <div class="alert success-alert">
        <h3>SUCCESS</h3>
        <a class="close">&times;</a>
    </div>

<?php }else if($success == 2){ ?>
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