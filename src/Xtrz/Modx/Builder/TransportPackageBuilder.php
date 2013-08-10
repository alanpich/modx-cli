<?php

namespace Xtrz\Modx\Builder;

use Xtrz\Modx\ModxAwareInterface;
use \xPDOTransport;

/**
 * Xtrz\Modx\Builder\TransportPackageBuilder
 */
class TransportPackageBuilder implements ModxAwareInterface
{
    protected $pkg;
    protected $elementManager;
    /** @var \modX */
    protected $modx;
    /** @var  \modPackageBuilder */
    protected $builder;
    protected $mainCategory;

    public function init()
    {
        $this->modx->loadClass('transport.modPackageBuilder', '', false, true);
        $this->builder = new \modPackageBuilder($this->modx);
        $this->builder->createPackage(PKG_NAMESPACE, PKG_VERSION, PKG_RELEASE);

        // Register Namespace -----------------------------------------------------------------------------
        $this->builder->registerNamespace(PKG_NAMESPACE, false, true, '{core_path}components/' . PKG_NAMESPACE . '/');
    }

    /**
     * Add an array of elements to the main category
     *
     * @param $elements
     */
    public function addElements($elements)
    {
        $cat = $this->getMainCategory();
        $cat->addMany($elements);
        $this->getModx()->log(\modX::LOG_LEVEL_INFO, "Added " . count($elements) . " elements");
    }

    public function getMainCategory()
    {
        if (!$this->mainCategory) {
            $this->mainCategory = $this->modx->newObject('modCategory');
            $this->mainCategory->set('category', PKG_NAME);
        }
        return $this->mainCategory;
    }

    /**
     * @return mixed
     */
    public function getModx()
    {
        return $this->modx;
    }

    /**
     * @param \modX $modx
     * @return mixed|void
     */
    public function setModx(\modX $modx)
    {
        $this->modx = $modx;
    }

    /**
     * Build us a transport package!
     */
    public function buildTransportPackage()
    {

        $this->getPkg()->incrementVersionPatch();

        // Package main category
        $attributes= array(
            xPDOTransport::UNIQUE_KEY => 'category',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
                'Snippets' => array(
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => array('name'),
                ),
                'Chunks' => array(
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => array('name'),
                ),
                'Templates' => array(
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => array('templatename'),
                ),
                'Plugins' => array(
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => array('name'),
                    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
                        'Events' => array(
                            xPDOTransport::PRESERVE_KEYS => true,
                            xPDOTransport::UPDATE_OBJECT => true,
                            xPDOTransport::UNIQUE_KEY => array('event','pluginid'),
                        )
                    )
                ),
            ),
        );
        $catVehicle = $this->builder->createVehicle($this->getMainCategory(), $attributes);

        // Add File resolvers ------------------------------------------------------------------
        $catVehicle->resolve('file',array(
                'source' => PKG_ASSETS,
                'target' => "return MODX_ASSETS_PATH . 'components/';",
            ));
        $catVehicle->resolve('file',array(
                'source' => PKG_CORE,
                'target' => "return MODX_CORE_PATH . 'components/';",
            ));


        // Add vehicle to package
        $this->builder->putVehicle($catVehicle);


        // Set package attributes
        // Adding in docs ----------------------------------------------------------------------
        /* now pack in the license file, readme and setup options */
        $this->builder->setPackageAttributes(array(
                'license' => file_get_contents(PKG_ROOT . 'LICENSE'),
                'readme' => file_get_contents(PKG_ROOT . 'README.md'),
                'changelog' => file_get_contents(PKG_ROOT . 'CHANGELOG.md'),
            ));
        $this->modx->log(\xPDO::LOG_LEVEL_INFO,'Set Package Attributes.'); flush();


        // Pack
        $this->getModx()->log(\xPDO::LOG_LEVEL_INFO,'Zipping up package...'); flush();
        $this->builder->pack();
    }

    /**
     * @return mixed
     */
    public function getElementManager()
    {
        return $this->elementManager;
    }

    /**
     * @param mixed $elementManager
     */
    public function setElementManager($elementManager)
    {
        $this->elementManager = $elementManager;
    }

    /**
     * @return mixed
     */
    public function getPkg()
    {
        return $this->pkg;
    }

    /**
     * @param mixed $pkg
     */
    public function setPkg($pkg)
    {
        $this->pkg = $pkg;
    }


}
