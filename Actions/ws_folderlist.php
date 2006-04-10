<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_folderlist.php,v 1.6 2006/04/10 16:21:32 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * View list of documents from one folder
 * @param Action &$action current action
 * @global id Http var : basket id
 * @global addid Http var : document id to add/move to basket id
 * @global paddid Http var : current folder of document id to add/move to basket id
 * @global addft Http var : action to realize : [add|move]
 * @global order Http var : list order [title,date,size,type]
 * @global dorder Http var : decrease or increase [true,false]
 */
function ws_folderlist(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 

  $mb=microtime();
  $docid = GetHttpVars("id");
  $pdocid = GetHttpVars("paddid");
  $addid = GetHttpVars("addid");
  $addft = GetHttpVars("addft");
  $order = GetHttpVars("order");
  $dorder = (GetHttpVars("dorder","true")=="true");
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $action->lay->set("warning","");

  switch ($docid) {
  case "lock":
    // test locked
    $doc=createTmpDoc($dbaccess,5);
    $doc->title="locked";
    $doc->Add();
    $doc->addQuery("select * from doc where locked = ".$action->user->id);
    break;
  default:

    $doc=new_doc($dbaccess,$docid);
  }


  if (($addft == "move") || ($addft == "add")) {
    if ($doc->isAlive()) {
      if ($addid) {
	$adddoc=new_doc($dbaccess,$addid);
	if ($adddoc->isAlive()) {
	  $err=$doc->AddFile($adddoc->id);
	}
      }
    }
  }
  if ($err=="") {
    if (($addft == "move")) {
      $pdoc=new_doc($dbaccess,$pdocid);
      if ($pdoc->isAlive()) {
	$err=$pdoc->DelFile($adddoc->id);
      }
    }
  }
  if ($err=="") { 
    if (($addft == "del")) { 
      if ($addid) {
	$adddoc=new_doc($dbaccess,$addid);
	if ($adddoc->isAlive()) {
	  $err=$adddoc->delete(); 
	}
      }   
    }
  }



  //--------------------------------------------------
  // construct header
  $thead=array("title"=>array("htitle"=>_("Filename Menu"),
			      "horder"=>"title",
			      "issort"=>false),
	       "date"=>array("htitle"=>_("Access Date Menu"),
			     "horder"=>"date",
			     "issort"=>false),
	       "size"=>array("htitle"=>_("File Size Menu"),
			     "horder"=>"size",
			     "issort"=>false),
	       "mime"=>array("htitle"=>_("File Type Menu"),
			     "horder"=>"mime",
			     "issort"=>false));

  //--------------------------------------------------
  // construct body


  $action->lay->set("pid",$doc->initid);
  $action->lay->set("CODE","KO");
  if ($doc->isAlive()) {
    //    $ls=$doc->getContent();
    $ls = getChildDoc($dbaccess, $doc->initid ,0,"ALL", $filter, $action->user->id, "TABLE");
   
    $tc=array();
    switch ($order) {
    case "date":
      usort($ls,"revdatecmp");
      $thead["date"]["issort"]=true;
      break;
    case "size":
      usort($ls,"sizecmp");
      $thead["size"]["issort"]=true;
      break;  
    case "mime":
      usort($ls,"mimecmp");
      $thead["mime"]["issort"]=true;
      break;            
    default:
      usort($ls,"titlecmp");    
      $thead["title"]["issort"]=true;  
    }
    if ($dorder){
      $action->lay->set("orderimg",$action->getImageUrl('b_down.png'));
    } else {
      $ls=array_reverse($ls);
      $action->lay->set("orderimg",$action->getImageUrl('b_up.png'));
    }

    foreach ($ls as $k=>$v) {
      $size=getv($v,"sfi_filesize",-1);
      if ($size < 0) $dsize="";
      else if ($size < 1024) $dsize=sprintf(_("%d bytes"),$size);
      else if ($size < 1048576) $dsize=sprintf(_("%d kb"),$size/1024);
      else $dsize=sprintf(_("%.01f Mb"),$size/1048576);
   //    $icon=getv($v,"sfi_mimeicon");
//       if (! $icon) $icon=$doc->getIcon($v["icon"]);
//       else $icon=$doc->getIcon($icon);

	$icon=$doc->getIcon($v["icon"]);


      $tc[]=array("title"=>utf8_encode($v["title"]),
		  "id"=>$v["id"],
		  "size"=>$dsize,
		  "mime"=>getv($v,"sfi_mimetxt"),
		  "mdate"=>utf8_encode(strftime("%d %b %Y %H:%M",getv($v,"revdate"))),
		  "icon"=>$icon);
    }
    $action->lay->setBlockData("TREE",$tc);
    $action->lay->set("ulid",uniqid("ul"));
    $action->lay->set("CODE","OK");
  } else {
    $action->lay->set("CODE","NOTALIVE");
  }
  $action->lay->set("count",count($tc));
  $action->lay->set("delay",microtime_diff(microtime(),$mb));
  $action->lay->set("title",utf8_encode($doc->title));
  $action->lay->setBlockData("HEAD",$thead);
  if (count($tc) > 0) $action->lay->set("nbdoc",utf8_encode(sprintf(_("%d documents"),count($tc))));
  else $action->lay->set("nbdoc",utf8_encode(_("0 document")));
					


}
function titlecmp($a,$b) {
  $ta=strtr($a["title"], "àéèêçù", "aeeecu");
  $tb=strtr($b["title"], "àéèêçù", "aeeecu");
  return strcasecmp($ta,$tb);
}
function revdatecmp($a,$b) {
  $ta=intval($a["revdate"]);
  $tb=intval($b["revdate"]);
  //  if ($ta==$tb) return 0;
  return ($ta>$tb)?1:-1;
}
function sizecmp($a,$b) {
  $ta=intval(getv($a,"sfi_filesize",-1));
  $tb=intval(getv($b,"sfi_filesize",-1));
    if ($ta==$tb) return 0;
  return ($ta>$tb)?1:-1;
}
function mimecmp($a,$b) {
  $ta=getv($a,"sfi_mimetxt");
  $tb=getv($b,"sfi_mimetxt");
  return strcmp($ta,$tb);
}

?>