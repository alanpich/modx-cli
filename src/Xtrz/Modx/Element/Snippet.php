<?php

namespace Xtrz\Modx\Element;

use phpDocumentor\Reflection\DocBlock;
use Xtrz\Modx\Skeleton\SkeletonBuilder;

class Snippet extends AbstractElement
{
    const FILE_PATTERN = "/.*.php/";

    public $type = 'modSnippet';
    public $name;
    public $description;
    public $properties = array();
    public $content = '';


    /**
     * Hydrate self from a file
     * Takes file path as param, and populates properties
     * from file contents
     *
     * @param string $path
     * @throws \Xtrz\Exception
     */
    public function fromFile($path)
    {
        if(!is_readable($path)){
            throw new \Xtrz\Exception("Unable to load path $path");
        }

        $file = file_get_contents($path);
        $tokens = token_get_all($file);
        $docBlockString = false;

        foreach($tokens as $token){
            list($id,$text) = $token;
            if($id == T_DOC_COMMENT){
                $docBlockString = $text;
                break;
            }
        }


        if($docBlockString){
            $docBlock = new DocBlock($docBlockString);
            $this->class = $docBlock->getTagsByName('type')[0]->getContent();
            $this->name = $docBlock->getTagsByName('name')[0]->getContent();
            $this->description = $docBlock->getText();
        }

    }

    /**
     * Output element to file
     *
     * @return string
     */
    public function toFile()
    {
        // TODO: Implement toFile() method.
        $skel = new SkeletonBuilder($this->pkg);
        $skel->buildFromTemplate(MODX_CLI_TOOL.'/templates/snippet/',getcwd(),array(
                'ELEMENT_NAME' => $this->name,
                'ELEMENT_PATH' => $this->getPath(),
                'ELEMENT_CONTENT' => $this->content,
                'ELEMENT_TYPE' => 'snippet'
            ));
    }


    public function getPath(){
        return str_replace(array(' ','.','\\'),DIRECTORY_SEPARATOR,$this->name);
    }

    /**
     * Returns an xPDOObject instance of this element
     *
     * @param \modX $modx
     * @return \xPDOObject
     */
    public function getXpdoObject(\modX $modx)
    {
        $class = $this->type;
        $obj = $modx->newObject($class);
        $obj->fromArray(array(
                'name' => $this->name,
                'description' => $this->description,
                'content' => $this->content
            ));
        return $obj;
    }

    /**
     * Hydrate self from an xPDOObject element
     *
     * @param \xPDOObject $xpdoObject
     */
    public function fromxPDOObject(\xPDOObject $xpdoObject)
    {
        // TODO: Implement fromxPDOObject() method.
    }
}
