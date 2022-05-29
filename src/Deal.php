<?php
$db_connection = new MongoDB\Driver\Manager('mongodb://mongo:27017', array('USERNAME'=>'root', 'PASSWORD'=>'1111'));

$success = 0;

if(isset($_POST['delete'])){
    $id = $_POST['delete'];
    $obj_id = new \MongoDB\BSON\ObjectId($id);
    $deleter = new MongoDB\Driver\BulkWrite;

    $deleter -> delete(['_id' => $obj_id]);

    $result = $db_connection->executeBulkWrite('Pawnshop.Deal', $deleter);


    if ($result -> getDeletedCount() == 0){
        $success = 2;
    }else{
        $success = 1;
    }
}
?>
<html>
<head>
    <link href="Styles/styles.CSS" rel="stylesheet" type="text/css">
    <link href="Styles/style2.css" rel="stylesheet" type="text/css">
    <meta charset="utf-8">
    <title>Deal</title>
</head>
<header>
    <?php if($success == 1){ ?>
        <div class="alert success-alert">
            <h3>SUCCESS</h3>
            <a class="close">&times;</a>
        </div>

    <?php }else if($success == 2){ ?>
        <div class="alert danger-alert">
            <h3>Somethings went wrong</h3>
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
    <div style = "text-align: left; margin: 20px">
        <form action="index.html">
            <button class="button-30" role="button">
                <-Back
            </button>
        </form>
    </div>
    <div class="hit-the-floor">
        Deal
    </div>
    <div class="hit-the-floor" style = "font-size: 26px; margin-bottom: 20px">
        There are <?php

        $filter = [];
        $options = [
            ['count' => true],
        ];
        $query = new MongoDB\Driver\Query($filter, $options);
        $cursor = $db_connection -> executeQuery("Pawnshop.Deal", $query);
        $count = 0;
        foreach($cursor as $_){
            $count += 1;
        }
        echo $count;

        ?>  rows
    </div>
    <div style = "text-align: center; margin-bottom: 20px">
        <form action="Deal_create.php">
            <button class="button-30" role="button">
                Add one
            </button>
        </form>
    </div>
</header>
<body>
<table align="center" border="1px" cellpadding= "10px" style = "font-size: 20px">
    <tr>
        <th>
            Client
        </th>
        <th>
            ProductCategory
        </th>
        <th>
            ProductDescription
        </th>
        <th>
            TakingDate
        </th>
        <th>
            ReturningDate
        </th>
        <th>
            Pledge
        </th>
        <th>
            Comission
        </th>
        <th>
            Actions
        </th>
    </tr>
    <?php
    $filter = [];
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options);

    $cursor = $db_connection -> executeQuery("Pawnshop.Deal", $query);
    foreach ($cursor as $document){
        ?>
        <tr>
            <td> <?php

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
                echo $client -> surname.' '.$client -> name;
                ?> </td>
            <td> <?php
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
                echo $category -> name;
                ?> </td>
            <td> <?php echo $document -> ProductDescription ?> </td>
            <td> <?php
                 $milliseconds = (string)$document -> TakingDate;
                 $TakingDate = new \MongoDB\BSON\UTCDateTime((int)$milliseconds);
                 $date = $TakingDate -> toDateTime() ->format(DATE_ATOM);
                   echo substr($date, 0, 10) ?> </td>
            <td> <?php $milliseconds = (string)$document -> ReturningDate;
                $TakingDate = new \MongoDB\BSON\UTCDateTime((int)$milliseconds);
                $date = $TakingDate -> toDateTime() ->format(DATE_ATOM);
                echo substr($date, 0, 10) ?> </td>
            <td> <?php echo $document -> Pledge ?> </td>
            <td> <?php echo $document -> Comission ?>% </td>

            <td align="center">
                <div style = "display: flex; justify-content: center; align-items: center;">
                    <form action="Deal_edit.php" method="POST" style = "margin-bottom:0px; margin:5px">
                        <button class="button-6" role="button" name="edite" value="<?php echo $document -> _id ?>">
                            Edite
                        </button>
                    </form>
                    <form action = "" method = "POST" style = "margin-bottom:0px; margin:5px">
                        <button class="button-6" role="button" name="delete" value="<?php echo $document -> _id ?>">
                            Delete
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
</body>
</html>