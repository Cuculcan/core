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
    <meta content="Страница не найдена" name="description" />
    <meta content="" name="author" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="/public/css/opensans.css" rel="stylesheet" type="text/css" />
    <link href="/public/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="/public/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/public/css/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/public/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="/public/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="/public/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="/public/css/error.min.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="/favicon.ico" /> </head>
<!-- END HEAD -->

<body class=" page-500-full-page">
<div class="row">
    <div class="col-md-12 page-500">
        <div class=" number font-red"> 400 </div>
        <div class=" details">
            <h2>Houston, we have a problem.</h2><br/>
            <h3>Bad request</h3><br/>
            <p> Что в переводе с английского означает - "Неверный запрос".
                <br/> </p>
            <p> <?php echo $this->model['error'] ?></p>
            <p>
                <a href="/" class="btn red btn-outline"> Ничего не понял. На главную страницу </a>
                <br> </p>
        </div>
    </div>
</div>
<!--[if lt IE 9]>
<script src="/public/js/respond.min.js"></script>
<script src="/public/js/excanvas.min.js"></script>
<script src="/public/js/ie8.fix.min.js"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="/public/js/jquery.min.js" type="text/javascript"></script>
<script src="/public/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/public/js/js.cookie.min.js" type="text/javascript"></script>
<script src="/public/js/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="/public/js/jquery.blockui.min.js" type="text/javascript"></script>
<script src="/public/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="/public/js/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<!-- END THEME LAYOUT SCRIPTS -->
</body>

</html>
