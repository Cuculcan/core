<?php

namespace Cuculcan\Core\Utils;

class ImageUtils
{
    private $log;

    public function __construct() {
        $this->log = \Logger::getLogger(__CLASS__);
    }

    public function downloadImageFromUrl($url, $saveFileName){

        if(!$url || !is_string($url) || strlen($url)<=4 ){
            $this->log->error("Wrong url param! url=".$url);
            return false;
        }
        $curl = new RCCurl($url);
        $curl->setBinaryTransfer(true);
        $curl->createCurl();

        if($curl->getHttpStatus() != 200){
            $this->log->error("Unable to download image from url=".$url."\n\tResponse status = ".$curl->getHttpStatus());
            return false;
        }

        if(file_exists($saveFileName)){
            unlink($saveFileName);
        }
        $fp = fopen($saveFileName,'x');
        fwrite($fp, $curl->getResponseBody());
        fclose($fp);

        return true;
    }

    // Создание превью изображения (при помощи урезания лишнего)
    public static function cropImageResize($width_to, $height_to, $width_from, $height_from, $path_from, $path_to, $quality = 100) {

        // 1. Определяем размер граней меньшей версии фото
        $w = $width_to;
        $min_w = $width_to;
        $min_h = $height_to;

        $h = $min_w*$height_from/$width_from;
        if($h<$min_h) {
            $w = $min_h*$width_from/$height_from;
            $h = $w*$height_from/$width_from;
        }

        $ratio = $w/$h;
        $src_ratio = $width_from/$height_from;
        if($ratio<$src_ratio) {
            $h = $w/$src_ratio;
        }
        else {
            $w = $h*$src_ratio;
        }

        $mwshift = $w/2 - $min_w/2;
        $mhshift = $h/2 - $min_h/2;

        // 2. Меняем размер изображения (пропорционально уменьшаем или увеличиваем)
        $big_ph = $path_from;
        $min_ph = $path_to;
        ImageUtils::proportionImageResize($big_ph, $min_ph, $w, $h, 100);

        // 3. Создаем уменьшенную версию изображения
        $ph_to = imagecreatetruecolor($min_w, $min_h);

        if(exif_imagetype($min_ph) == IMAGETYPE_GIF) {
            $ph_from = imagecreatefromgif($min_ph);
            imagecopy($ph_to, $ph_from, 0, 0, $mwshift, $mhshift, $min_w, $min_h);
            imagegif($ph_to, $min_ph);
        }
        else if(exif_imagetype($min_ph) == IMAGETYPE_JPEG) {
            $ph_from = imagecreatefromjpeg($min_ph);
            imagecopy($ph_to, $ph_from, 0, 0, $mwshift, $mhshift, $min_w, $min_h);
            imagejpeg($ph_to, $min_ph, $quality);
        }
        else if(exif_imagetype($min_ph) == IMAGETYPE_PNG) {
            $ph_from = imagecreatefrompng($min_ph);
            imagecopy($ph_to, $ph_from, 0, 0, $mwshift, $mhshift, $min_w, $min_h);
            imagepng($ph_to, $min_ph, $quality);
        }

        imagedestroy($ph_to);
        imagedestroy($ph_from);

        return array(
            'w'=>$w,
            'h'=>$h,
            'mw'=>$min_w,
            'mh'=>$min_h,
            'mwshift'=>$mwshift,
            'mhshift'=>$mhshift
        );
    }


    public static function proportionImageResize($filename, $smallimage, $w, $h, $quality = 100, $watermark = false) {

        $src_img = false;
        if(exif_imagetype($filename) == IMAGETYPE_GIF) {
            $src_img = imagecreatefromgif($filename);
        }
        else if(exif_imagetype($filename) == IMAGETYPE_JPEG) {
            $src_img = imagecreatefromjpeg($filename);
        }
        else if(exif_imagetype($filename) == IMAGETYPE_PNG) {
            $src_img = @imagecreatefrompng($filename);
        }
        else {
            $src_img = @imagecreatefromstring(file_get_contents($filename, FILE_USE_INCLUDE_PATH));
        }

        if(!$src_img){
            return false;
        }

        $src_img = ImageUtils::coorectOrientationWithExif($src_img, $filename);

        $src_w = imagesx($src_img);
        $src_h = imagesy($src_img);

        $size = ImageUtils::detectSizesPropotional( $src_w, $src_h, $w, $h);
        $w = $size->w;
        $h = $size->h;

        // Создадим пустое изображение по заданным размерам
        $dest_img = imagecreatetruecolor($w, $h);

        if($watermark) {
            imageAlphaBlending($dest_img, false);
            imageSaveAlpha($dest_img, true);
        }


        // Масштабируем изображение
        // $dest_img - уменьшенная копия
        // $src_img - исходной изображение
        // $w - ширина уменьшенной копии
        // $h - высота уменьшенной копии
        // $size_img[0] - ширина исходного изображения
        // $size_img[1] - высота исходного изображения
        imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $src_w, $src_h);

        // Сохраняем уменьшенную копию в файл
        if(exif_imagetype($filename) == IMAGETYPE_GIF) {
            imagegif($dest_img, $smallimage);
        }
        else if(exif_imagetype($filename) == IMAGETYPE_JPEG) {
            imagejpeg($dest_img, $smallimage, $quality);
        }
        else if(exif_imagetype($filename) == IMAGETYPE_PNG) {
            if($watermark) {
                imagepng($dest_img, $smallimage);
            }
            else {
                imagejpeg($dest_img, $smallimage, $quality);
            }
        }

        // Чистим память от созданных изображений
        imagedestroy($dest_img);
        imagedestroy($src_img);

        return true;
    }

    public static function detectSizesPropotional($src_w, $src_h, $des_w, $des_h){
        $src_ratio = $src_w/$src_h;
        $ratio = $des_w/$des_h;

        // Если ширина и высота равны 0
        if($des_w == 0 && $des_h == 0) {
            return new Size($src_w, $src_h);
        }

        // Если ширина равна 0
        if($des_w == 0 && $des_w) {
            return new Size($des_h * $src_ratio, $src_h);
        }

        // Если высота равна 0
        if($des_h == 0 && $des_w) {
            return new Size($des_w, $des_w/$src_ratio);
        }

        // Здесь вычисляем размеры уменьшенной копии, чтобы при масштабировании сохранились пропорции исходного изображения
        if ($ratio < $src_ratio) {
            $h = $des_w / $src_ratio;
            return new Size($des_w, $h);
        } else {
            $w = $des_h * $src_ratio;
            return new Size($w, $des_h);
        }
    }

    public static function coorectOrientationWithExif($image, $imagePath){
         try {
             //TODO разобраться почему на тестовом криво читает эксиф
            $exif = exif_read_data($imagePath);
         }
         catch (\Exception $exp) {
             return $image;
        }
        if(!empty($exif['Orientation'])) {
            switch($exif['Orientation']) {
                case 8:
                    $image = imagerotate($image,90,0);
                    break;
                case 3:
                    $image = imagerotate($image,180,0);
                    break;
                case 6:
                    $image = imagerotate($image,-90,0);
                    break;
            }
        }

        return $image;
    }
}

class Size{
    public $w;
    public $h;
    public function __construct($w, $h) {
        $this->w = $w;
        $this->h = $h;
    }
}