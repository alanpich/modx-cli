<?php
/*

Aloha Editor plugin for MODX Revolution

http://www.aloha-editor.org/
http://www.simpledream.ru/

Andchir

OnWebPagePrerender

*/

$lang = $modx->getOption('lang',$scriptProperties,$modx->config['manager_language']);
$usergroups = $modx->getOption('usergroups',$scriptProperties,'');
$content_fields = $modx->getOption('content_fields',$scriptProperties,'{}');
$tv_fields = $modx->getOption('tv_fields',$scriptProperties,'{}');
if(!$content_fields) $content_fields = '{}';
if(!$tv_fields) $tv_fields = '{}';

$resource = $modx->resource;//$modx->getObject('modResource',$modx->resourceIdentifier);

$isEditor = false;

if($modx->user->get('id') && $modx->user->hasSessionContext('mgr')){
    
    //???????? ???? ???????
    if ($modx->hasPermission('save_document') && $resource->checkPolicy('save')) {
        
        //???? ????? ????????? ?????? ?? ???????????? ? ??????
        $isEditor = $usergroups ? $modx->user->isMember(explode(',',str_replace(', ',',',$usergroups))) : true;
        
    }
    
}

if($isEditor){
    
    $modAuth = $modx->user->getUserToken('mgr');
    
    $scriptHtml = '
    <script type="text/javascript">
    var alohaOptions = {
        "lang": "'.$lang.'",
        "base_url": "'.$modx->getOption('base_url').'",
        "auth": "'.$modAuth.'",
        "context_key": "'.$resource->get('context_key').'",
        "parent": '.$resource->get('parent').',
        "resource_id": '.$resource->get('id').',
        "content": '.$content_fields.',
        "tv": '.$tv_fields.',
        "aloha_elements": [[],[]]
    };
    </script>
    <link href="'.MODX_ASSETS_URL.'components/aloha/css/aloha.css" rel="stylesheet" type="text/css" />
    <script src="'.MODX_ASSETS_URL.'components/aloha/lib/require.js" type="text/javascript"></script>
    <script src="'.MODX_ASSETS_URL.'components/aloha/aloha-config.js" type="text/javascript"></script>
    <script src="'.MODX_ASSETS_URL.'components/aloha/lib/aloha-full.min.js" type="text/javascript" data-aloha-plugins="common/ui,common/format,common/table,common/list,common/link,common/highlighteditables,common/block,common/undo,common/image,common/contenthandler,common/paste,common/commands,common/abbr,extra/action,extra/store"></script>
    ';
    
    //?? ??????????    
    $modx->resource->_output= preg_replace("/(<\/body>)/i", $scriptHtml . "\n\\1", $modx->resource->_output);
    
    //$modx->regClientStartupHTMLBlock($scriptHtml);
    
}