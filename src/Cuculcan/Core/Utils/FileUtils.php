<?php

namespace Cuculcan\Core\Utils;

class FileUtils
{
    /**
     * Создает директорию для хранения файлов
     * @param $filePath string путь к папке хранения файла относительно корня сайта
     * @return array(...) путь к файлу: полный, относительно корня сайта, путь к корню
     */
    public static function createStorageDir($filePath){

        //формируем путь к папке в которую загрузится картинка
        $storageBase = static::getStorageBase();

        $filePath = trim($filePath, "/");
        $pathParts = explode('/', $filePath);

        $storage = $storageBase;
        foreach($pathParts as $part){
            $storage = $storage."/".$part;
            FileUtils::createDir($storage);
        }

        return array(
            'full_path'=>$storage,
            'storage' => $storageBase,
            'file_path' =>$filePath
        );

    }

    /**
     * Получает путь к хранилищу файлов
     * @return string
     */
    public static function getStorageBase(){
        global $config;
        $storage = "";
        if(isset( $config['common']) && isset( $config['common']['file_storage'])){
            $storage = $config['common']['file_storage'];
        }
        else{
            $storage =  dirname(@$_SERVER['SCRIPT_FILENAME']).'/public';
        }
        return $storage;
    }

    /**
     * Генерирует уникальное имя для файла
     * @return string
     */
    public static function generateFileName(){
        $name = FileUtils::getRandomHex(6).'_'.(new \DateTime())->getTimestamp();
        return $name;
    }

    /**
     * генерирует строку случайное 16ричное число
     * количество символов в строке = количество байт * 2
     * @param int $num_bytes количество байт в числе
     * @return string
     */
    public static function getRandomHex($num_bytes=4) {
        return bin2hex(openssl_random_pseudo_bytes($num_bytes));
    }

    /**
     * создаем папку  и устанавливаем ей полные права
     * @param $path
     */
    public static function createDir($path){
        $oldmask = umask(0);
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        umask($oldmask);
    }

    /**
     * устанавливаем полный доступ к файлу
     * @param string $link полный путь к файлу
     * @param int $mode флаг доступа (по умолчанию 0777)
     */
    public static function setAccessRights($link, $mode=0777){
        $oldmask = umask(0);
        chmod($link, $mode);
        umask($oldmask);
    }

    /**
     * определяет расширение(тип файла) картинки
     * @param $link полный путь к файлу
     * @return string
     */
    public static function getImageExtension($link){

        if(exif_imagetype($link) == IMAGETYPE_GIF) {
            //$type = 'image/gif';
            return '.gif';
        }

        if(exif_imagetype($link) == IMAGETYPE_JPEG) {
            //$type = 'image/jpeg';
            return '.jpg';
        }

        if(exif_imagetype($link) == IMAGETYPE_PNG) {
            //$type = 'image/png';
            return '.png';
        }
        return null;

    }

    public static function getFileExtension($link){
        return pathinfo($link, PATHINFO_EXTENSION);
    }
    /**
     * добавляем рассширение к скачанной картинке
     * @param string $link полный путь к файлу
     * @param $ext
     */
    public static function setFileExtension($link, $ext){
        //добавляем рассширение к скачанной картинке
        @rename($link, $link.$ext);
    }

    public static function deleteFileInStorage($file){
        $storage = FileUtils::getStorageBase();
        $link = $storage."/".$file;
        FileUtils::deleteFile($link);
    }

    public static function deleteFile($link){
        @unlink($link);
    }

    public static function moveFile($from, $to){

        if(!file_exists($from)){
            return;
        }

        $pos = strrpos( $to, "/");
        if ($pos === false) {
            return;
        }

        $destDir = substr($to,0,$pos+1);
        if(!file_exists($destDir)){
            return;
        }

        @rename ($from, $to);
        FileUtils::setAccessRights($to);
    }
}