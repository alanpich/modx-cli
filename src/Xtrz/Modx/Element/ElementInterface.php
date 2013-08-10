<?php

namespace Xtrz\Modx\Element;

/**
 * Xtrz\Modx\Element\AbstractElement
 */
interface ElementInterface
{

    /**
     * Hydrate self from a file
     * Takes file path as param, and populates properties
     * from file contents
     *
     * @param string $path
     */
    public function fromFile( $path );


    /**
     * Output element to file
     *
     * @return mixed
     */
    public function toFile();


    /**
     * Returns an xPDOObject instance of this element
     *
     * @return \xPDOObject
     */
    public function getXpdoObject( \modX $modx);


    /**
     * Hydrate self from an xPDOObject element
     *
     * @param \xPDOObject $xpdoObject
     */
    public function fromxPDOObject( \xPDOObject $xpdoObject );

}
