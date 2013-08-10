<?php


class ___PKG_NAME_LWR___ implements ArrayAccess
{

    protected $modx;
    protected $config;

    public function __construct(modX $modx, $params = array())
    {
        $this->modx = $modx;

        // Setup config data
        $core = $this->modx->getOption('___PKG_NAMESPACE___.core_path',null,$this->modx->getOption('core_path').'components/___PKG_NAMESPACE___/');
        $assets = $this->modx->getOption('___PKG_NAMESPACE___.assets_url',null, $this->modx->getOption('assets_url').'components/___PKG_NAMESPACE___/');
        $this->config = array(
            'corePath' => $core,
            'processorPath' => $core.'processors/',
            'controllerPath' => $core.'controllers/',
            'templatePath' => $core.'templates/',
            'libPath' => $core.'lib/',
            'docsPath' => $core.'docs/',
            'assetsUrl' => $assets,
            'jsUrl' => $assets.'mgr/js/',
            'cssUrl' => $assets.'mgr/css/',
            'imgUrl' => $assets.'mgr/img/',
            'connectorUrl' => $assets.'mgr/connector.php',
            'managerUrl' => $this->modx->getOption('manager_url'),
        );
        $this->config = array_merge($this->config,$params);
    }


    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($offset)
    {
        // First check system settings
        $sys = $this->modx->getOption('___PKG_NAMESPACE___.'.$offset,null,NULL);
        if(!is_null($sys))
            return $sys;

        return $this->config[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->config[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }}
