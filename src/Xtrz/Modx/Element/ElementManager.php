<?php

namespace Xtrz\Modx\Element;

use Xtrz\Util\Filesystem;

/**
 * Xtrz\Modx\Element\ElementManager
 */
class ElementManager
{
    protected $pkg;
    protected $elements = array();

    public function scanFilesystem($path)
    {
        $this->findSnippets(PKG_CORE . 'elements/snippets/');
        $this->findPlugins(PKG_CORE . 'elements/plugins/');
    }

    /**
     * Find all snippets in path
     *
     * @param        $path
     * @param string $hydrateClass
     */
    public function findSnippets($path, $hydrateClass = 'Xtrz\Modx\Element\Snippet')
    {
        // Recursively look for files
        $files = $this->glob($path, $hydrateClass::FILE_PATTERN);

        $snippets = array();
        foreach ($files as $file) {
            $snippet = new $hydrateClass($this->getPkg());
            $snippet->fromFile($file);
            $snippets[] = $snippet;
        }

        $this->elements['snippets'] = $snippets;
    }

    /**
     * Find all plugins in path
     *
     * @param        $path
     * @param string $hydrateClass
     */
    public function findPlugins($path, $hydrateClass = 'Xtrz\Modx\Element\Plugin')
    {
        // Recursively look for files
        $files = $this->glob($path, $hydrateClass::FILE_PATTERN);

        $plugins = array();
        foreach ($files as $file) {
            $plugin = new $hydrateClass($this->getPkg());
            $plugin->fromFile($file);
            $plugins[] = $plugin;
        }


        $this->elements['plugins'] = $plugins;
    }

    public function glob($path, $pattern)
    {
        $files = array();
        $path = rtrim(str_replace("\\", "/", $path), '/') . '/*';
        foreach (glob($path) as $fullname) {
            if (is_dir($fullname)) {
                $files = array_merge($files, $this->glob($fullname, $pattern));
            } else {
                if (preg_match($pattern, $fullname)) {
                    $files[] = $fullname;
                }
            }
        }
        return $files;
    }

    /**
     * @return \Xtrz\Modx\Pkg
     */
    public function getPkg()
    {
        return $this->pkg;
    }

    /**
     * @param \Xtrz\Modx\Pkg $pkg
     */
    public function setPkg($pkg)
    {
        $this->pkg = $pkg;
    }

    public function getElements()
    {
        return $this->elements;
    }

    public function getxPDOObjects()
    {
        $elements = array();

        foreach($this->elements as $type => $els){

            foreach($els as $element){
                $elements[] = $element->getxPDOObject( $this->getPkg()->getModx());
            }

        }

        return $elements;
    }


}
