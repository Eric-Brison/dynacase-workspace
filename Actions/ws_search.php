<?php
/**
 * Search document and return list of them
 *
 * @author Anakeen 2006
 * @version $Id: ws_search.php,v 1.1 2006/07/21 09:20:17 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package WORKSPACE
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");
include_once("WORKSPACE/Lib.WsFtCommon.php");


/**
 * View list of documents from one folder
 * @param Action &$action current action
 * @global famid Http var : family id where search document
 * @global key Http var : filter key on the title
 */
function ws_search(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 
  $action->lay->setEncoding("utf-8");

  $mb=microtime();
  $famid = GetHttpVars("famid");
  $key = GetHttpVars("key");
  $noids = explode('|',GetHttpVars("noids"));
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $action->lay->set("warning","");
  $action->lay->set("CODE","OK");
  $limit=20;
  $filter[]="title ~* '".pg_escape_string($key)."'";

  $lq=getChildDoc($dbaccess, 0,0,$limit, $filter,$action->user->id,"TABLE",$famid);


  foreach ($lq as $k=>$v) {
    if (! in_array($v["id"],$noids)) {
      $lq[$k]["title"]=utf8_encode($lq[$k]["title"]);
      $lq[$k]["stitle"]=str_replace("'","\\'",($lq[$k]["title"]));
    } else {
      unset($lq[$k]);
    }
  }


  $action->lay->setBlockData("DOCS",$lq);


  $action->lay->set("count",count($lq));
  $action->lay->set("delay",microtime_diff(microtime(),$mb));

					


}

?>