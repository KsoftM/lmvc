<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <title>
        <yield name="title" />
    </title>
    <yield name="css" />
    <link rel="stylesheet" href="./css/app.css">
</head>

<body>
    <yield name="content" />


    <yield name="js" />
</body>

</html>