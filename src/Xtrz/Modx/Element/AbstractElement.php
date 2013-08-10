<?php

namespace Xtrz\Modx\Element;

use Xtrz\Modx\Pkg;

/**
 * Xtrz\Modx\Element\AbstractElement
 */
abstract class AbstractElement implements ElementInterface
{
    protected $pkg;

    public function __construct(Pkg $pkg)
    {
        $this->pkg = $pkg;
    }

}
