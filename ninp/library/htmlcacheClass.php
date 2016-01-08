<?php

class htmlcacheclass {

    var $Cachedir;

    public function __construct() {

        $this->Cachedir = __DIR__ . "/cache";
        ;
    }

    public function init() {


        $this->Cachedir();

        $value = "text connet xxxFFFFx";

        $this->setHtmlCache("test", $value);
        
        $content = $this->getHtmlCache("test");
        
        print_r($content);
    }

    public function Cachedir() {

        if (!is_dir($this->Cachedir)) {

            mkdir($this->Cachedir, 0777);
        }
    }

    public function setHtmlCache($name, $value) {

        $TemCacheFileName = $this->Cachedir . "/cache" . $name . ".html";

        $this->cleanCache($TemCacheFileName);

        $myfile = fopen($TemCacheFileName, "w") or die("Unable to open file!");
        fwrite($myfile, $value);
        chmod($TemCacheFileName, 0777);
        fclose($myfile);
    }

    public function getHtmlCache($name) {

        $TemCacheFileName = $this->Cachedir . "/cache" . $name . ".html";

        if (file_exists($TemCacheFileName)) {
            $content = fopen($TemCacheFileName, "r") or die("Unable to open file!");
            $file_content = fread($content, filesize($TemCacheFileName));
            fclose($myfile);
            return $file_content;
        }
    }

    public function cleanCache() {

        if (is_file($TemCacheFileName)) {

            if (!unlink($TemCacheFileName)) {

                echo "File Competence is wrong";

                return FALSE;
            }
        }
    }

}
