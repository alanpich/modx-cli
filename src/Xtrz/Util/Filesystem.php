<?php
namespace Xtrz\Util;

class Filesystem
{


    /**
     * Write data to disk
     * @param string $path File path
     * @param string $content
     * @param bool $append True to append rather than overwrite
     * @throws FilesystemException
     */
    public static function write($path,$content,$append=false){
        try {
            if($append && is_readable($path)){
                $content = file_get_contents($path).$content;
            };
            file_put_contents($path,$content);
        } catch( \Exception $E ){
            throw new FilesystemException("Failed to write data to disk ad $path");
        }
    }


    public static function read($path){
        return file_get_contents($path);
    }



    public static function create($path,$content){
        $bits = explode('/',$path);
        $name = array_pop($bits);
        $dir = implode('/',$bits);

        // Ensure directory exists
        self::mkdir($dir);
        // Now create file
        self::write($path,$content);
    }



    public static function parseJSONFile($path){
        if(!is_readable($path)){ throw new FilesystemException("Unable to load pkg config from $path"); }
        $json = file_get_contents($path);
        return self::parseJSON($json);
    }


    /**
     * Parse a json string
     *
     * @param string $json
     * @throws \Exception
     * @return mixed
     */
    public static function parseJSON($json){
        if(is_null($data = json_decode($json))){
            $err = json_last_error();
            switch(json_last_error()){
                case JSON_ERROR_NONE:           $msg = "No error has occurred";   break;
                case JSON_ERROR_DEPTH:          $msg = "The maximum stack depth has been exceeded";   break;
                case JSON_ERROR_STATE_MISMATCH: $msg = "Invalid or malformed JSON";   break;
                case JSON_ERROR_CTRL_CHAR:      $msg = "Control character error, possibly incorrectly encoded";   break;
                case JSON_ERROR_SYNTAX:         $msg = "Syntax error";   break;
                case JSON_ERROR_UTF8:           $msg = "Malformed UTF-8 characters, possibly incorrectly encoded";   break;
            }
            throw new \Exception($msg);
        }
        return $data;
    }


    /**
     * Check if a directory is empty
     *
     * @param string $path
     * @throws FilesystemException
     * @return bool
     */
    public static function isDirEmpty($path){
        if(!is_dir($path)){ throw new FilesystemException("$path is not a directory");}
        return (count(scandir($path)) == 2);
    }


    public static function countChildNodes($path){
        if(!is_dir($path)){ throw new FilesystemException("$path is not a directory");}
        return (count(scandir($path))-2);
    }

    /**
     * Create a directory
     * @param $dir
     */
    public static function mkdir($dir){
        if(!is_dir($dir)){
            mkdir($dir,0755,true);
        }
    }

    /**
     * Recursively copy a directory to target
     *  Optionally replaces key=>val placeholders in content and filenames
     * @param string $src  Source path
     * @param string $tgt  Target path
     * @param array  $data [Optional] Placeholders
     */
    public static function recursiveCopy($src,$tgt,$data = array()){

        // Ensure root node exists
        self::mkdir($tgt);

        // Find all nodes in this directory
        $dh = opendir($src);
        while($node = readdir($dh)) {
            if($node=='.'||$node=='..'){ continue; };
            $srcNode = rtrim($src,'/').'/'.$node;
            $tgtNode = rtrim($tgt,'/').'/'. self::parseNodeName($node, $data);

            if(is_dir($srcNode)){
                // Copy to target
                self::mkdir($tgtNode);
                static::recursiveCopy($srcNode,$tgtNode, $data);
            } else {
                // File copy
                $fContent = self::parseFileContents($srcNode, $data);
                self::create($tgtNode, $fContent);
            }
        }
        closedir($dh);
    }



    public static function parseNodeName($node, $replacers){
        foreach($replacers as $key => $val){
            $key = '___'.strtoupper($key).'___';
            $node = str_replace($key, $val, $node);
        }
        return $node;
    }

    public static function parseFileContents($file, $replacers){
        $content = file_get_contents($file);
        foreach($replacers as $key => $val){
            $key = '___'.strtoupper($key).'___';
            $content = str_replace($key, $val, $content);
        }
        return $content;
    }


}
