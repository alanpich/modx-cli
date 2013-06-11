<?php
$path = $modx->getOption('core_path').'components/honeybearz/';

// Load the lib
require $path.'lib/kaltura_api/KalturaClient.php';

$data = array();

// Grab partner ID & Secret
define('KALTURA_PARTNER_ID',(int)$modx->getOption('kaltura.partner_id'));
define('KALTURA_PARTNER_SERVICE_SECRET',$modx->getOption('kaltura.partner_secret'));

//define session variables
$partnerUserID = $modx->user->get('email');

try {

    //construct Kaltura objects for session initiation
    $config           = new KalturaConfiguration(KALTURA_PARTNER_ID);
    $client           = new KalturaClient($config);
    $ks               = $client->session->start(KALTURA_PARTNER_SERVICE_SECRET, $partnerUserID, KalturaSessionType::ADMIN);


    //Prepare variables to be passed to embedded flash object.
    $flashVars = array();
    $flashVars["uid"]               = $partnerUserID;
    $flashVars["partnerId"]         = KALTURA_PARTNER_ID;
    $flashVars["subPId"]            = KALTURA_PARTNER_ID * 100;
    $flashVars["ks"]                = $ks;
    $flashVars["conversionProfile"] = 5;
    $flashVars["maxFileSize"]       = 200;
    $flashVars["uiConfId"]          = 7578522;
    $flashVars["jsDelegate"]        = "HoneyBearz.Kaltura.KSU.events";
    $flashVars["entryId"]           = -1;
    $data['flashvars'] = json_encode($flashVars);


    return $modx->getChunk('kaltura.ksu',$data);

} catch(Exception $E){

    return  "<pre>Kaltura Uploader Error: ". $E->getMessage().'</pre>';

}