<!DOCTYPE html>
<!--[if IE 8]>
<html lang="ru" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="ru" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ru">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>
        <?php
        if ($this->title) {
            echo $this->title;
        }
        ?>
    </title>
    <meta name="keywords" content="<?php
    if ($this->keywords) {
        echo $this->keywords;
    }
    ?>" />
    <meta name="description" content="<?php
    if ($this->description) {
        echo $this->description;
    }
    ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="author" />

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    
    <!--глобальные стили-->
    <link href="/css/layout.css" rel="stylesheet" type="text/css" />
    <!--дополнительные стили-->
    <?php
    if ($this->additionalCSS) {
        foreach ($this->additionalCSS as $css) {
            echo '<link rel="stylesheet" href="' . $css . '">' . "\r\n    ";
        }
    }
    ?>

    <!--глобальные скрипты-->
    <script src="/js/jquery.min.js" type="text/javascript"></script>
    <!--дополнительные скрипты-->
    <?php
    if ($this->additionalJS) {
        foreach ($this->additionalJS as $js) {
            echo '<script src="' . $js . '"></script>' . "\r\n    ";
        }
    }
    ?>

    
</head>
<!-- END HEAD -->

<body>
   