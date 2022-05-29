<?php
$db_connection = new MongoDB\Driver\Manager('mongodb://mongo:27017', array('USERNAME'=>'root', 'PASSWORD'=>'1111'));
$name = "";
$surname = "";
$patronymic = "";
$paperNumber = "";
$paperSeries = "";
$success = 0;
if(isset($_POST['surname'])){
    $errors = "";
    if($_POST['name'] == ""){
        $errors .= "[Name: empty]";
    }else{
        $name = $_POST['name'];
    }
    if($_POST['surname'] == ""){
        $errors .= "[Surname: empty]";
    }else{
        $surname = $_POST['surname'];
    }
    if($_POST['patronymic'] == ""){
        $errors .= "[Patronymic: empty]";
    }else{
        $patronymic = $_POST['patronymic'];
    }

    if($_POST['paperseries'] == "") {

        $errors .= "[Paperseries: empty]";
    }else if (sizeof($_POST['paperseries']) != 4) {
        $errors .= "[Paperseries: must be only 4n.]";
    }else if(ctype_digit($_POST['paperseries'])){

        $paperSeries = $_POST['paperseries'];

    }else{

        $errors .= "[PaperSeries: not only numbers]";

    }
    if($_POST['papernumber'] == ""){
        $errors .= "[Papernumber: empty]";
    }else if (sizeof($_POST['papernumber']) != 6) {
        $errors .= "[Papernumber: must be only 6n.]";
    }else if(ctype_digit($_POST['papernumber'])){
        $paperNumber = $_POST['papernumber'];
    }else{
        $errors .= "[PaperNumber: not only numbers]";
    }
    if($errors == ""){
        $writes = new MongoDB\Driver\BulkWrite;
        $writes -> insert([
                'name' => $name,
                'surname' => $surname,
                'patronymic' => $patronymic,
                'paper' => [
                        'number' => $paperNumber,
                        'series' => $paperSeries
                ]
        ]);
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $result = $db_connection->executeBulkWrite('Pawnshop.Client', $writes, $writeConcern);
        $confirm = $result -> isAcknowledged();
        if (!$confirm){
            $success = 2;
            $write_errors = $result -> getWriteErrors();
            foreach ($write_errors as $one_error){
                $errors .= '['.$one_error -> getMessage().']';
            }
        }else{
            $name = "";
            $surname = "";
            $patronymic = "";
            $paperNumber = "";
            $paperSeries = "";
            $success = 1;
        }

    }else{
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
    <div class="hit-the-floor" style = "margin-bottom: 70px; font-size: 45px">
        Adding new Client
    </div>
</header>
<body>
<form action="" method="POST">
    <table align = "center" style = "width:20%">
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="surname" id="input" value='<?php echo $surname ?>' class="Input-text" placeholder="Surname">
                    <label for="input" class="Input-label">Surname</label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" name="name" id="input" value='<?php echo $name ?>' class="Input-text" placeholder="Name">
                    <label for="input" class="Input-label">Name</label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" id="input" name="patronymic" value='<?php echo $patronymic ?>' class="Input-text" placeholder="Patronymic">
                    <label for="input" class="Input-label">Patronymic</label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input maxlength="6" type="text" name="papernumber" id="input" value="<?php echo $paperNumber ?>" class="Input-text" placeholder="PaperNumber(6n.)">
                    <label for="input" class="Input-label">PaperNumber</label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input maxlength="4" type="text" name="paperseries" id="input" value="<?php echo $paperSeries ?>" class="Input-text" placeholder="PaperSeries(4n.)">
                    <label for="input" class="Input-label">PaperSeries</label>
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