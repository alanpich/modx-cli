<?php

/* Usage Example assuming $modx is already been defined.
$pkg = new PackageProvider($modx);
$packageName = 'Login';
echo $pkg->processInstall($packageName);
*/

namespace Xtrz\Modx;

class PackageProvider
{
    /** @var \modX */
    public $modx;

    /** @var  \modTransportProvider */
    protected $provider;

    /**
     * Constructor
     *
     * @param \modX $modx        MODx instance to install packages to
     * @param int   $providerId  ID of provider object in modx instance
     * @throws \Exception
     */
    public function __construct(\modX $modx, $providerId = 1)
    {
        $this->modx = $modx;
        $this->provider = $modx->getObject('transport.modTransportProvider', $providerId);
        if (empty($this->provider)) {
            throw new \Exception("Invalid PackageProvider ID");
        }
    }


    /**
     * Search the provider for packages matching $query
     *
     * @param $query
     * @throws \Exception
     * @return bool
     */
    public function search($query)
    {
        $packages = array();
        /** @var \modRestResponse $response */
        $response = $this->provider->request(
            'package',
            'GET',
            array(
                'query' => $query
            )
        );
        if ($response->isError()) {
            throw new \Exception($this->modx->lexicon('provider_err_connect', array('error' => $response->getError())));
            return false;
        }
        foreach ($response->xml->package as $pkg) {
            $packages[(string)$pkg->name] = (string)$pkg->signature;
        }
        return $packages;
    }


    /**
     * Return an array of information about a package
     *
     * @param string $signature Package signature
     * @return mixed
     */
    public function getPackageInfo($signature)
    {
        $response = $this->provider->request(
            'package',
            'GET',
            array(
                'signature' => $signature
            )
        );

        $data = json_decode(json_encode($response->xml));
        return $data;
    }



    /**
     * Download a package by signature
     *
     * @param string $signature
     * @param string $url
     * @throws \Exception
     */
    public function download($signature, $url = '')
    {

        // If no url provided, look one up
        if ($url == '') {
            $info = $this->getPackageInfo($signature);
            $url = $info->file->location;
        }

        // Download the package
        if (!file_exists($this->modx->getOption('core_path') . 'packages/' . $signature . '.zip')) {
            // Download the package
            $response = $this->modx->runProcessor('workspace/packages/rest/download',array(
                    'info' => $url . '::' . $signature
                )
            );
            if (!$response) {
                throw new \Exception("Failed to download package");
            }
        }

        // Run the scanLocal processor to update the db
        $this->modx->runProcessor('workspace/packages/scanlocal');

    }


    /**
     * Install a package by signature
     * @param string $signature
     * @return bool
     */
    public function install($signature)
    {
        // See if the package is already downloaded
        $file = $this->modx->getOption('core_path').'packages/'.$signature.'.zip';
        if(!is_readable($file)){
            $this->download($signature);
        }

        // Install the package
        $response = $this->modx->runProcessor('workspace/packages/install',array(
            'signature' => $signature
        ));

        return $response;
    }


    /**
     * Perform full download/install of package
     * @param string $packageName
     * @throws \Exception
     * @return string
     */
    public function processInstall($packageName) {
        $packages = $this->search($packageName);

        if(!array_key_exists($packageName,$packages)){
            return '';
        };

        $this->download($packages[$packageName]);
        $response = $this->install($packages[$packageName]);
        $output = $response->response['message'];
        return $output;
    }

}
