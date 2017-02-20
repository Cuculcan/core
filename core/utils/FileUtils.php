<?php



class FileUtils
{
    /**
     * Создает директорию для хранения файлов в формате
     *  /{имя  объекта храниния, тип или раздел и т.п...}/{год-месяц-день}
     * @param $objecType string раздел или объект для которого скачивается  или загружается файл
     * @return array(full_path=> , folder_date=> ) путь к директории директория с датой
     */
    public static function createStorageDir($objecType){
        //будут храниться в папках по дате
        $folderDate =(new DateTime())->format('Y-m-d');

        //формируем путь к папке в которую загрузится картинка
        $storage = FileUtils::getStorageBase();

        //создаем папку базового хранилища и устанавливаем ей полные права
        $path = $storage."/".$objecType;
        FileUtils::createDir($path);

        //создаем папку  и устанавливаем ей полные права
        $path = $storage."/".$objecType."/".$folderDate;
        FileUtils::createDir($path);

        return array(
            'full_path'=>$path,
            'storage' => $storage,
            'folder_date'=>$folderDate,
            'object_type' =>$objecType
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
            $storage =  dirname(@$_SERVER['SCRIPT_FILENAME']).'/public/images';
        }
        return $storage;
    }

    /**
     * Генерирует уникальное имя для файла
     * @return string
     */
    public static function generateFileName(){
        $name = FileUtils::getRandomHex(6).'_'.(new DateTime())->getTimestamp();
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
     * @param $link полный путь к файлу
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
        //определяем тип
        $image_type = '';
        if(exif_imagetype($link) == IMAGETYPE_GIF) {
            $type = 'image/gif';
            $image_type = '.gif';
        }
        else if(exif_imagetype($link) == IMAGETYPE_JPEG) {
            $type = 'image/jpeg';
            $image_type = '.jpg';
        }
        else if(exif_imagetype($link) == IMAGETYPE_PNG) {
            $type = 'image/png';
            $image_type = '.png';
        }
        return $image_type;

    }

    /**
     * добавляем рассширение к скачанной картинке
     * @param $link полный путь к файлу
     * @param $ext
     */
    public static function setImageExtension($link, $ext){
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