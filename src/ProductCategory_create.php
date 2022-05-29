<?php
$db_connection = new MongoDB\Driver\Manager('mongodb://mongo:27017', array('USERNAME'=>'root', 'PASSWORD'=>'1111'));

$name = "";
$notes = "";
$succes = 0;
if(isset($_POST['name'])){
    $errrors = "";
    if($_POST['name'] == ""){
        $errrors .= "[Name: empty]";
    }else{
        $name = $_POST['name'];
    }
    if($_POST['notes'] == ""){
        $errrors .= "[Notes: empty]";
    }else{
        $notes = $_POST['notes'];
    }
    if($errrors == ""){

            $writer = new MongoDB\Driver\BulkWrite;
            $writer -> insert([
                    'name' => $name,
                    'notes' => $notes
            ]);
            $result = $db_connection -> executeBulkWrite("Pawnshop.ProductCategory", $writer);
            $confirm = $result -> isAcknowledged();

            if (!$confirm){
                $success = 2;
                $write_errors = $result -> getWriteErrors();
                foreach ($write_errors as $one_error) {
                    $errors .= '[' . $one_error->getMessage() . ']';
                }
            }else{
                $name = "";
                $notes = "";
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
        <form action="ProductCategory.php">
            <button class="button-30" role="button">
                <-Back
            </button>
        </form>
    </div>
    <div class="hit-the-floor" style = "margin-bottom: 70px; font-size: 45px">
        Adding new ProductCategory
    </div>
</header>
<body>
<form action="" method="POST">
    <table align = "center" style = "width:20%">
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" id="input" name="name" value='<?php echo $name ?>' class="Input-text" placeholder="Name">
                    <label for="input" class="Input-label">Name</label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="Input" style = "margin: 20px; align-items:center">
                    <input type="text" id="input" name="notes" value='<?php echo $notes ?>' class="Input-text" placeholder="Notes">
                    <label for="input" class="Input-label">Notes</label>
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
