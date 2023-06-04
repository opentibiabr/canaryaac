<?php
/**
 * Upload Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use App\Model\Entity\ServerConfig;
use App\Model\Entity\Uploads as EntityUploads;

class Upload
{
    private $name;

    private $extension;

    private $type;

    private $tmpName;

    private $error;

    private $size;

    private $duplicates = 0;

    private $web_url;

    public function __construct($file)
    {
        $this->type = $file['type'];
        $this->tmpName = $file['tmp_name'];
        $this->error = $file['error'];
        $this->size = $file['size'];

        $info = pathinfo($file['full_path']);
        $this->name = $info['filename'];
        $this->extension = $info['extension'];
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function getUrl()
    {
        return $this->web_url;
    }

    public function generateNewName()
    {
        $this->name = strtotime(date('Y-m-d')) . '-' . time() . '-' . uniqid();
    }

    public function getBasename()
    {
        $extension = strlen($this->extension) ? '.'.$this->extension : '';
        $duplicates = $this->duplicates > 0 ? '-'.$this->duplicates : '';
        return $this->name.$duplicates.$extension;
    }

    private function getPossibleBasename($dir, $overwrite)
    {
        if ($overwrite) {
            return $this->getBasename();
        }
        $basename = $this->getBasename();
        if (!file_exists($dir.'/'.$basename)) {
            return $basename;
        }
        $this->duplicates++;
        return $this->getPossibleBasename($dir, $overwrite);
    }

    public function upload($dir, $overwrite = true)
    {
        if ($this->error != 0) {
            return false;
        }
        $dirob = __DIR__ . '/../../../resources/' . $dir;
        $path = __DIR__ . '/../../../resources/' . $dir . '/' . $this->getPossibleBasename($dirob, $overwrite);
        $url = URL . '/resources/' . $dir . '/' . $this->getPossibleBasename($dirob, $overwrite);

        $this->web_url = $url;
        self::insertUpload($this->getPossibleBasename($dirob, $overwrite), $url);
        return move_uploaded_file($this->tmpName, $path);
    }

    public static function createMultiUpload($files)
    {
        $uploads = [];
        foreach ($files['name'] as $key => $value) {
            $file = [
                'name' => $files['name'][$key],
                'full_path' => $files['full_path'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error' => $files['error'][$key],
                'size' => $files['size'][$key],
            ];
            $uploads[] = new Upload($file);
        }
        return $uploads;
    }

    public static function insertUpload($name, $path)
    {
        $select_webconfig = ServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($select_webconfig->timezone);

        EntityUploads::insertUploads([
            'name' => $name,
            'path' => $path,
            'date' => strtotime(date('Y-m-d H:i:s'))
        ]);
    }
}
