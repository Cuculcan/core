<!DOCTYPE html>
<!--[if IE 8]> <html lang="ru" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="ru" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ru">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>400 Bad request</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="Bad request" name="description" />
    <meta content="" name="author" />
    
<body>
    <div>
        <h2>Houston, we have a problem.</h2><br/>
        <h3>Bad request</h3><br/>
        <p> <?php echo $this->model['error'] ?></p>
        <p>
            <a href="/" class="btn red btn-outline">Back to main </a>
            <br> </p>
    </div>
</body>

</html>
