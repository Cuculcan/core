<?php
/**
 * Загрузчик картинок на сайт
 *
 * Надо добавить больще функционала на основе core/utils/UploadHandler.php
 */
include_once 'core/utils/FileUtils.php';
include_once 'core/utils/ImageUtils.php';

class FileUploader
{
    private $log;
    // PHP File Upload error message codes:
    // http://php.net/manual/en/features.file-upload.errors.php
    protected $error_messages = array(
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload',
        'post_max_size' => 'The uploaded file exceeds the post_max_size directive in php.ini',
        'max_file_size' => 'File is too big',
        'min_file_size' => 'File is too small',
        'accept_file_types' => 'Filetype not allowed',
        'max_number_of_files' => 'Maximum number of files exceeded',
        'max_width' => 'Image exceeds maximum width',
        'min_width' => 'Image requires a minimum width',
        'max_height' => 'Image exceeds maximum height',
        'min_height' => 'Image requires a minimum height',
        'abort' => 'File upload aborted',
        'image_resize' => 'Failed to resize image'
    );

    public function __construct()
    {
        $this->log = Logger::getLogger(__CLASS__);
        $this->options = array(

            'readfile_chunk_size' => 10 * 1024 * 1024, // 10 MiB
            // Defines which files can be displayed inline when downloaded:
            'inline_file_types' => '/\.(gif|jpe?g|png)$/i',
            // Defines which files (based on their names) are accepted for upload:
            'accept_file_types' => '/.+$/i',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => 5 * 1024 * 1024, //5M
            'min_file_size' => 10,
            // The maximum number of files for the upload directory:
            'max_number_of_files' => null,
            // Defines which files are handled as image files:
            'image_file_types' => '/\.(gif|jpe?g|png)$/i',
            // Use exif_imagetype on all files to correct file extensions:
            'correct_image_extensions' => false,
            // Image resolution restrictions:
            'max_width' => null,
            'max_height' => null,
            'min_width' => 1,
            'min_height' => 1,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads' => true,
            // Set to 0 to use the GD library to scale and orient images,
            // set to 1 to use imagick (if installed, falls back to GD),
            // set to 2 to use the ImageMagick convert binary directly:
            'image_library' => 1,
            // Uncomment the following to define an array of resource limits
            // for imagick:
            /*
            'imagick_resource_limits' => array(
                imagick::RESOURCETYPE_MAP => 32,
                imagick::RESOURCETYPE_MEMORY => 32
            ),
            */
            // Command or path for to the ImageMagick convert binary:
            'convert_bin' => 'convert',
            // Uncomment the following to add parameters in front of each
            // ImageMagick convert call (the limit constraints seem only
            // to have an effect if put in front):
            /*
            'convert_params' => '-limit memory 32MiB -limit map 32MiB',
            */
            // Command or path for to the ImageMagick identify binary:
            'identify_bin' => 'identify',
            'image_versions' => array(
                // The empty image version key defines options for the original image:
                '' => array(
                    // Automatically rotate images based on EXIF meta data:
                    'auto_orient' => true
                ),
                // Uncomment the following to create medium sized images:
                /*
                'medium' => array(
                    'max_width' => 800,
                    'max_height' => 600
                ),
                */
                'thumbnail' => array(
                    // Uncomment the following to use a defined directory for the thumbnails
                    // instead of a subdirectory based on the version identifier.
                    // Make sure that this directory doesn't allow execution of files if you
                    // don't pose any restrictions on the type of uploaded files, e.g. by
                    // copying the .htaccess file from the files directory for Apache:
                    //'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/thumb/',
                    //'upload_url' => $this->get_full_url().'/thumb/',
                    // Uncomment the following to force the max
                    // dimensions and e.g. create square thumbnails:
                    //'crop' => true,
                    'max_width' => 80,
                    'max_height' => 80
                )
            ),
            'print_response' => true
        );
    }

    protected function get_upload_data($id)
    {
        return @$_FILES[$id];
    }

    protected function get_server_var($id)
    {
        return @$_SERVER[$id];
    }

    protected function get_file_size($file_path, $clear_stat_cache = false)
    {
        if ($clear_stat_cache) {
            if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
                clearstatcache(true, $file_path);
            } else {
                clearstatcache();
            }
        }
        return $this->fix_integer_overflow(filesize($file_path));
    }

    protected function get_error_message($error)
    {
        return isset($this->error_messages[$error]) ?
            $this->error_messages[$error] : $error;
    }

    function get_config_bytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $this->fix_integer_overflow($val);
    }

    protected function validate($uploaded_file, $file, $error, $index)
    {
        if ($error) {
            $file->error = $this->get_error_message($error);
            return false;
        }
        $content_length = $this->fix_integer_overflow(
            (int)$this->get_server_var('CONTENT_LENGTH')
        );

        $post_max_size = $this->get_config_bytes(ini_get('post_max_size'));
        if ($post_max_size && ($content_length > $post_max_size)) {
            $file->error = $this->get_error_message('post_max_size');
            return false;
        }

        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            $file_size = $this->get_file_size($uploaded_file);
        } else {
            $file_size = $content_length;
        }
        if ($this->options['max_file_size'] && (
                $file_size > $this->options['max_file_size'] ||
                $file->size > $this->options['max_file_size'])
        ) {
            $file->error = $this->get_error_message('max_file_size');
            return false;
        }

        if ($this->options['min_file_size'] &&
            $file_size < $this->options['min_file_size']
        ) {
            $file->error = $this->get_error_message('min_file_size');
            return false;
        }
        return true;
    }


    protected function handle_file_upload($uploaded_file, $fileStorage, $size, $type, $error, $index = null, $content_range = null)
    {
        $file = new stdClass();
        $link = $fileStorage['full_path']."/"."source_".$fileStorage['file_name'];
        $file->size = $this->fix_integer_overflow((int)$size);
        $file->type = $type;

        if (!$this->validate($uploaded_file, $file, $error, $index)) {
            return $file;
        }
        $append_file = $content_range && is_file($link) && $file->size > $this->get_file_size($link);
        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            // multipart/formdata uploads (POST method uploads)
            if ($append_file) {
                //TODO переделать загрузку чтобы поддерживалась возможность дозаписи
                file_put_contents(
                    $link,
                    fopen($uploaded_file, 'r'),
                    FILE_APPEND
                );
            } else {
                move_uploaded_file($uploaded_file, $link);
            }
        } else {
            // Non-multipart uploads (PUT method support)
            file_put_contents(
                $link,
                fopen($this->options['input_stream'], 'r'),
                $append_file ? FILE_APPEND : 0
            );
        }
        $file_size = $this->get_file_size($link, $append_file);
        if ($file_size !== $file->size) {
            $file->size = $file_size;
            if (!$content_range && $this->options['discard_aborted_uploads']) {
                unlink($link);
                $file->error = $this->get_error_message('abort');
                return null;
            }
        }

        $fyleType = FileUtils::getImageExtension($link);
        FileUtils::setImageExtension($link, $fyleType);

        $size = getimagesize($link.$fyleType);
        $original_width= $size[0];
        $original_height=$size[1];
        $preview_name = $fileStorage['full_path']."/"."min_".$fileStorage['file_name'].$fyleType;
        ImageUtils::cropImageResize(173, 113, $original_width,$original_height, $link.$fyleType, $preview_name   );

        $file->name = "source_".$fileStorage['file_name'].$fyleType;
        $file->min_name = "min_".$fileStorage['file_name'].$fyleType;
        $file->date = $fileStorage['folder_date'];
        $file->object_type = $fileStorage['object_type'];
        $file->url =  '/public/images/'.$fileStorage['object_type'].'/'.$fileStorage['folder_date'].'/'."source_".$fileStorage['file_name'].$fyleType;
        $file->thumbnailUrl= '/public/images/'.$fileStorage['object_type'].'/'.$fileStorage['folder_date'].'/'."min_".$fileStorage['file_name'].$fyleType;
        $file->deleteType = "DELETE";
        $file->deleteUrl="http://mainroad.trade/notice/photo?file=".$fileStorage['object_type']."/".$fileStorage['folder_date'].'/'."source_".$fileStorage['file_name'].$fyleType;
        return $file;
    }


    public function uploadTo($objectType)
    {

        $upload = $this->get_upload_data('files');

        // Parse the Content-Range header, which has the following form:
        // Content-Range: bytes 0-524287/2000000
        //Пробуем получить размер из заголовка
        $content_range_header = $this->get_server_var('HTTP_CONTENT_RANGE');

        $content_range = $content_range_header ?
            preg_split('/[^0-9]+/', $content_range_header) : null;
        $size = $content_range ? $content_range[3] : null;

        $fileStorage = FileUtils::createStorageDir($objectType);
        $fileStorage['file_name'] = FileUtils::generateFileName();


        $files = array();
        if ($upload) {
            if (is_array($upload['tmp_name'])) {
                // param_name is an array identifier like "files[]",
                // $upload is a multi-dimensional array:
                foreach ($upload['tmp_name'] as $index => $value) {

                    $files[] = $this->handle_file_upload(
                        $upload['tmp_name'][$index],
                        $fileStorage,
                        $size ? $size : $upload['size'][$index],
                        $upload['type'][$index],
                        $upload['error'][$index],
                        $index,
                        $content_range
                    );
                }
            } else {
                // param_name is a single object identifier like "file",
                // $upload is a one-dimensional array:
                $files[] = $this->handle_file_upload(
                    isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                    $fileStorage,
                    $size ? $size : (isset($upload['size']) ?
                        $upload['size'] : $this->get_server_var('CONTENT_LENGTH')),
                    isset($upload['type']) ?
                        $upload['type'] : $this->get_server_var('CONTENT_TYPE'),
                    isset($upload['error']) ? $upload['error'] : null,
                    null,
                    $content_range
                );
            }
        }

        return $files;

    }


    // Fix for overflowing signed 32 bit integers,
    // works for sizes up to 2^32-1 bytes (4 GiB - 1):
    protected function fix_integer_overflow($size)
    {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }
        return $size;
    }




}

/**
 *
 deleteType:"DELETE"
deleteUrl:"http://mainroad.trade/index.php?file=oruzhie%20%2813%29.jpg"
name:"oruzhie (13).jpg"
size:103926
thumbnailUrl:"http://mainroad.trade/files/thumbnail/oruzhie%20%2813%29.jpg"
type:"image/jpeg"
url:"http://mainroad.trade/files/oruzhie%20%2813%29.jpg"
 */