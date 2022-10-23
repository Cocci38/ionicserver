<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    $scan = scandir('picture');
    foreach ($scan as $file) {
        if (!is_dir($file)) {
            echo '<h3>' . $file . '</h3>';
            echo '<img src="picture/' . $file . '" style="width: 400px;"/><br />';
        }
    }
    ?>
</body>

</html>