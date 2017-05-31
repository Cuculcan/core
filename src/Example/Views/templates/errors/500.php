<!DOCTYPE html>
<!--[if IE 8]> <html lang="ru" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="ru" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ru">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>500 Internal Error</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="Internal Server Error" name="description" />
    <meta content="" name="author" />
    
<body>
    <div>
        <h3>Oops! Something went wrong ...</h3>
        <p> We already fix it! Try to look here later<br/></p>
        <p> <?php echo $this->model['error'] ?></p>
        <p>
            <a href="/" class="btn red btn-outline">  Return home </a> <br/>
        </p>
    </div>

</body>

</html>
