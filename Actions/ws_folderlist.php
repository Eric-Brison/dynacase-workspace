<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_folderlist.php,v 1.29 2007/07/30 16:03:37 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
 * @subpackage
 */
/**
 */

include_once ("FDL/Class.SearchDoc.php");
include_once ("WORKSPACE/Lib.WsFtCommon.php");
include_once ("WORKSPACE/ws_folderListFormat.php");

/**
 * View list of documents from one folder
 * @param Action &$action current action
 * @global id Http var : folder id where move/add document
 * @global addid Http var : document id to add/move to basket id
 * @global paddid Http var : current folder of document id to add/move to basket id
 * @global addft Http var : action to realize : [add|move]
 * @global order Http var : list order [title,date,size,type]
 * @global dorder Http var : decrease or increase [true,false]
 */
function ws_folderlist(Action &$action)
{
    header('Content-type: text/xml; charset=utf-8');
    $action->lay->setEncoding("utf-8");
    
    $mb = microtime();
    $docid = GetHttpVars("id");
    $pdocid = GetHttpVars("paddid");
    $addid = GetHttpVars("addid");
    $addft = GetHttpVars("addft");
    $order = GetHttpVars("order");
    $key = GetHttpVars("key");
    $smode = GetHttpVars("searchmode");
    $dorder = (GetHttpVars("dorder", "true") == "true");
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $action->lay->set("warning", "");
    
    switch ($docid) {
    case "lock" :
        // test locked
        $dir = createTmpDoc($dbaccess, 5);
        $dir->title = "locked";
        $dir->Add();
        $dir->addQuery("select * from doc where abs(locked) = " . $action->user->id);
        break;
    case "trash" :
        // test locked
        $dir = createTmpDoc($dbaccess, 5);
        $dir->title = _("trash");
        $dir->Add();
        $dir->addQuery("select * from doc where doctype='Z' and owner = " . $action->user->id);
        break;
    case "search" :
        include_once ("GENERIC/generic_util.php");
        // search
        if (!seems_utf8($key)) $keyword = utf8_encode($key);
        else $keyword = $key;
        
        $dir = createTmpDoc($dbaccess, 5);
        $dir->title = sprintf(_("search %s"), $keyword);
        $dir->Add();
        $famid = getFamIdFromName($dbaccess, "SIMPLEFILE");
        
        setSearchMode($action, $famid, $smode);
        $full = ($smode == "FULL");
        $sqlfilter = $dir->getSqlGeneralFilters($keyword, "yes", false, $full);
        
        $sdirid = 0;
        $query = getSqlSearchDoc($dbaccess, $sdirid, $famid, $sqlfilter);
        
        $dir->AddQuery($query);
        $docid = $dir->id;
        break;
    default :
        
        $dir = new_doc($dbaccess, $docid);
    }
    
    $err = movementDocument($action, $dbaccess, $dir->id, $addid, $pdocid, $addft);
    if ($err) $action->lay->set("warning", $err);
    
    $configColumn = $action->read('wsColumn' . $action->getArgument("configNumber"));
    $configInclude = $action->read('wsInclude' . $action->getArgument("configNumber"));
    if ($configInclude) {
        include_once ($configInclude);
    }
    if (! $configColumn) $configColumn="wsFolderListFormat::getColumnDescription()";
    //--------------------------------------------------
    // construct header
 
    $thead = $dir->applyMethod($configColumn);
    //--------------------------------------------------
    // construct body
    $action->lay->set("pid", $dir->initid);
    $action->lay->set("docid", $dir->id);
    $action->lay->set("CODE", "KO");
    if ($dir->isAlive()) {
        //    $ls=$dir->getContent();
        $slice = $action->GetParam("FDL_FOLDERMAXITEM", 1000);
        
        $sqlOrder = "title";
        if ($dorder) $sqlOrder .= " desc";
        switch ($order) {
        case "date" :
            //usort($ls,"revdatecmp");
            $sqlOrder = "revdate";
            if ($dorder) $sqlOrder .= " desc";
            $thead["date"]["issort"] = true;
            break;
        
        }
        
        $s = new SearchDoc($dbaccess);
        $s->useCollection($dir->initid);
        $s->setSlice($slice);
        $s->setOrder($sqlOrder);
        $s->excludeConfidential();
        if ($key) {
            if ($smode == "FULL") {
                $orderby = '';
                $keys = '';
                $sqlfilters = array();
                DocSearch::getFullSqlFilters($key, $sqlfilters, $orderby, $keys);
                foreach ( $sqlfilters as $sqlfilter )
                    $s->addFilter($sqlfilter);
                if (!$order) $s->setOrder($orderby);
            } else {
                $s->addFilter("svalues ~* '%s'", $key);
            }
        }
        $s->setObjectReturn();
        $ls = $s->search();
        switch ($order) {
        
        case "title" :
            
            $thead["title"]["issort"] = true;
            break;
        case "size" :
            usort($ls, "sizecmp");
            $thead["size"]["issort"] = true;
            break;
        case "mime" :
            usort($ls, "mimecmp");
            $thead["mime"]["issort"] = true;
            break;
        
        }
        
        $tc = array();
        if ($dorder) {
            $action->lay->set("orderimg", $action->getImageUrl('b_up.png'));
        } else {
            
            $action->lay->set("orderimg", $action->getImageUrl('b_down.png'));
        }
        /*
        $folder=array_filter($ls,"isfolder");
        $notfolder=array_filter($ls,"isnotfolder");
        $ls=array_merge($folder,$notfolder);
        */
        $dynfolder = ($dir->doctype != 'D');
        /*
        foreach ($ls as $k=>$v) {
            $size=getv($v,"sfi_filesize",-1);
            if ($size < 0) $dsize="";
            else if ($size < 1024) $dsize=_("<1 kb");
            else if ($size < 1048576) $dsize=sprintf(_("%d kb"),$size/1024);
            else $dsize=sprintf(_("%.01f Mb"),$size/1048576);
            //    $icon=getv($v,"sfi_mimeicon");
            //       if (! $icon) $icon=$dir->getIcon($v["icon"]);
            //       else $icon=$dir->getIcon($icon);

            $icon=$dir->getIcon($v["icon"]);


            $tc[]=array("title"=>$v["title"],
		  "id"=>$v["id"],
		  "linkfld"=>($dynfolder ||($v["prelid"]==$dir->initid))?false:true,
		  "isfld"=>($v["doctype"]=='D')||($v["doctype"]=='S')?1:0,
		  "size"=>$dsize,
		  "mime"=>getv($v,"sfi_mimetxtshort"),
		  "mdate"=>strftime("%d %b %Y %H:%M",getv($v,"revdate",0)),
		  "icon"=>$icon);
        }
        */
        $count = $s->count();
        $c = 0;
        $tc = array();
        while ( $doc = $s->nextDoc() ) {
            $tc[$c] = array(
                "id" => $doc->id,
                "isfld" => (($doc->doctype == 'D') || ($doc->doctype == 'S')) ? 1 : 0
            );
            $line = array();
            foreach ( $thead as $idx => $col ) {
                $err = '';
                $mValue = $doc->applyMethod($col["method"], '', -1, array(), array(
                    "DIR" => $dir
                ), $err);
                
                if ($mValue) $mValue = sprintf('<span class="%s">%s</span>', $idx, $mValue);
                $line[] = ($err) ? $err : $mValue;
            }
            $tc[$c]["line"] = implode("</td><td>", $line);
            $c++;
        }
        $action->lay->setBlockData("TREE", $tc);
        $action->lay->set("ulid", uniqid("ul"));
        $action->lay->set("CODE", "OK");
    } else {
        $action->lay->set("CODE", "NOTALIVE");
        $action->lay->set("warning", $docid);
    }
    $action->lay->set("count", $count);
    $action->lay->set("delay", microtime_diff(microtime(), $mb));
    if ($key) {
        $action->lay->set("title", sprintf(_("search %s"), $key));
    } else {
        $action->lay->set("title", $dir->getHtmltitle());
    }
    $action->lay->set("colspan", count($thead));
    $action->lay->setBlockData("HEAD", array_slice($thead, 1));
    $action->lay->set("key", "$key&searchmode=$smode");
    if (($dir->doctype == 'S') && ($dir->name != "")) {
        // rename folder only if it is a named search
        $taction = $action->lay->getBlockData("ACTIONS");
        $taction[] = array(
            "actname" => "RENAMEBRANCH",
            "actdocid" => '[' . $dir->id . ',' . "'" . sprintf("%s (%d)", $dir->title, $count) . "']"
        );
        
        $action->lay->setBlockData("ACTIONS", $taction);
    }
    if ($count > 0) {
        $taction = $action->lay->getBlockData("ACTIONS");
        $taction[] = array(
            "actname" => "IMGRESIZE",
            "actdocid" => $dir->id
        );
        $action->lay->setBlockData("ACTIONS", $taction);
    }
    if ($count > 1) $action->lay->set("nbdoc", sprintf(_("%d items"), $count));
    elseif ($count == 1) $action->lay->set("nbdoc", _("1 item"));
    else $action->lay->set("nbdoc", _("0 item"));
    $action->lay->set("isdynamic", $dynfolder);
    $action->lay->set("isreadonly", ($dir->control("modify") != "") ? "true" : "false");

}
function titlecmp($a, $b)
{
    $ta = strtr($a["title"], "àéèêçù", "aeeecu");
    $tb = strtr($b["title"], "àéèêçù", "aeeecu");
    return strcasecmp($ta, $tb);
}
function revdatecmp($a, $b)
{
    $ta = intval($a["revdate"]);
    $tb = intval($b["revdate"]);
    //  if ($ta==$tb) return 0;
    return ($ta > $tb) ? 1 : -1;
}
function sizecmp($a, $b)
{
    $ta = intval(getv($a, "sfi_filesize", -1));
    $tb = intval(getv($b, "sfi_filesize", -1));
    if ($ta == $tb) return 0;
    return ($ta > $tb) ? 1 : -1;
}
function mimecmp($a, $b)
{
    $ta = getv($a, "sfi_mimetxt");
    $tb = getv($b, "sfi_mimetxt");
    return strcmp($ta, $tb);
}
function isfolder($a)
{
    return $a['doctype'] != 'F';
}
function isnotfolder($a)
{
    return $a['doctype'] == 'F';
}

?>