<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_navigate.php,v 1.12 2007/07/30 16:03:37 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEDOM
 * @subpackage
 */
/**
 */



include_once("FDL/Lib.Dir.php");
include_once("GENERIC/generic_util.php");
include_once("FDL/Class.SearchDoc.php");


/**
 * View folders and document for exchange them
 * @param Action &$action current action
 */




class ws_Navigate {
    private $action;
    private $application;
    private $spaces=null;
    private $viewMySpace=true;
    private $folderListHeight=200;
    private $folderTreeHeight=300;
    private $folderTreeWidth=250;
    private $globalSearch=null;
    private $actionFolderList='WORKSPACE:WS_FOLDERLIST';
    private $actionColumnDefinition=array();
    private $includeColumnDefinition='';
    
    public function __construct(Action &$action) {
        $this->action=$action;
        $this->application=$action->parent;
        $this->lay=new Layout(getLayoutFile("WORKSPACE","ws_navigate.xml"),$action);

    }
    public function setSpaces(SearchDoc $spaces) {
        $spaces->setObjectReturn();
        $this->spaces= $spaces;
    }
    /**
     * use predefined search object
     * @param array $families
     */
    public function setGlobalSearch(SearchDoc $search) {
        $this->globalSearch=$search;
    }
    
    public function viewMySpace($viewMySpace) {
       
        $this->viewMySpace= ($viewMySpace==true);
    }
    public function setFolderListHeight($h) {
        if ($h) $this->folderListHeight= $h;
    }
    public function setFolderTreeHeight($h) {
        if ($h) $this->folderTreeHeight= $h;
    }
    public function setFolderTreeWidth($h) {
        if ($h) $this->folderTreeWidth= $h;
    }
    private function trashempty($userid) {
        $q=new QueryDb($this->action->dbaccess,"Doc");
        $q->Query(0,0,"TABLE",
        sprintf("select id from doc where doctype='Z' and owner=%d limit 1",$userid));

        return ($q->nb == 0);

    }
    
    function setFolderListAction($actionName) {
        if (preg_match('/([^:]*):(.*)/', $actionName, $reg)) {
            $this->actionFolderList=$actionName;
        }
    }

    function setFolderListInclude($phpFile) {
       
            $this->includeColumnDefinition=$phpFile;
        
    }
    function setFolderListColumn(array $column) {
       
            $this->actionColumnDefinition=$column;
        
    }
    
    function addOffline(&$action) {
        $dbaccess = $this->action->dbaccess;
        $desktop=getTDoc($dbaccess,'FLDOFFLINE_'.Doc::getWhatUserId());
        if (! $desktop) {
            $desktop = createDoc($dbaccess,"DIR");
            $desktop->title = _("Offline");
            $desktop->setTitle($desktop ->title);
            $desktop->setValue("ba_desc", sprintf(_("Offline folder of %s"),$action->user->firstname." ".$action->user->lastname));
            $desktop->icon = 'fldoffline.png';
            $desktop->name = 'FLDOFFLINE_'.$action->user->id;
            $desktop->Add();

            $home=$desktop->getHome();
            $home->addFile($desktop->initid);
            $action->lay->set("FREEDOM_IDOFFLINE",$desktop->initid);
        } else   $action->lay->set("FREEDOM_IDOFFLINE",$desktop["id"]);
    }
    public function setHeaders() {
        $this->application->AddJsRef($this->action->getParam("CORE_JSURL")."/geometry.js");
        $this->application->AddJsRef($this->action->getParam("CORE_JSURL")."/subwindow.js");
        $this->application->AddJsRef($this->action->getParam("CORE_JSURL")."/AnchorPosition.js");
        if ($this->action->Read("navigator")=="EXPLORER") $this->application->AddJsRef($this->action->getParam("CORE_JSURL")."/iehover.js");
        $this->application->AddJsRef($this->action->getParam("CORE_JSURL")."/resizeimg.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL")."/FDL/Layout/common.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL")."/FDL/Layout/popupdoc.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL")."/FDC/Layout/inserthtml.js");
        $this->application->AddJsRef($this->action->getParam("CORE_JSURL")."/DHTMLapi.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL")."/FDC/Layout/setparamu.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL")."/WORKSPACE/Layout/displayws.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL")."/WORKSPACE/Layout/mechanism.js");


        // css pour popup
        $this->application->AddCssRef("FDL:POPUP.CSS",true);
        $this->application->AddCssRef("WORKSPACE:system.css",true);
        $this->application->AddCssRef("WORKSPACE:default.css",true);
    }
    public function render() {

        $dbaccess = $this->action->GetParam("FREEDOM_DB");

        $tlayspaces=array();

        if ($this->spaces) {
            $this->spaces->search();
            while ($doc=$this->spaces->nextDoc()) {
                $tlayspaces[]=array("stitle"=>$doc->title,
			"sicon"=>$doc->getIcon(),
			"sid"=>$doc->id);
            }
        }
        $famid = getFamIdFromName($dbaccess,"SIMPLEFILE");
        $mode=getSearchMode($this->action,$famid);
        $this->lay->Set("FULLMODE",($mode=="FULL"));

        $this->lay->setBlockData("SPACES",$tlayspaces);
        if ($this->trashempty($this->action->user->id)) $this->lay->set("imgtrash",$this->action->getImageUrl('trashempty.png'));
        else $this->lay->set("imgtrash",$this->action->getImageUrl('trash.png'));

        $homename="WS_PERSOFLD_".Doc::getWhatUserId();
        $perso=getTDoc($dbaccess,$homename);
        if (! $perso) {
            // create "my space" folder

            $perso = createDoc($dbaccess,"SIMPLEFOLDER",false);
            $perso->name=$homename;

            $perso->setValue("ba_title",_("My space"));
            $perso->setValue("ba_desc",sprintf(_("personal space of %s"),$perso->getUserName(true)));
            $perso->icon='gnome-fs-home.png';
            $err=$perso->Add();
            if ($err != "") $this->action->addWarningMsg($err);
            if ($err =="") {
                $persofldid=$perso->id;
                $home=$perso->getHome();
                if ($home) $home->AddFile($persofldid);//add in general home
            }
        } else {
            $persofldid=$perso["id"];
        }
        if ($this->action->getParam("WS_OFFLINE")=="yes") addOffline($this->action);
        $this->lay->set("persofldid",$persofldid);
         $this->lay->set("myspace",$this->viewMySpace);
         $this->lay->set("folderListHeight",$this->folderListHeight);
         $this->lay->set("folderTreeHeight",$this->folderTreeHeight);
         $this->lay->set("folderTreeWidth",$this->folderTreeWidth);
         $this->lay->set("actionFolderList",$this->actionFolderList);
         
    }
    private function  setSearchDocument() {
        if (! $this->globalSearch) {
            $this->globalSearch=new SearchDoc($this->action->dbaccess);

        }
        $ws=createTmpDoc($this->action->dbaccess,"DSEARCH");
        $ws->setValue("ba_title",sprintf(_("search %s"),"workspace"));
        $ws->add();
        $ws->addStaticQuery($this->globalSearch->getOriginalQuery());
         $this->lay->set("searchId", $ws->id);
    }
    
    private function memoConfiguration() {
        global $action;
        $this->configNumber=time();
        $this->lay->set("configNumber", $this->configNumber);
        $action->register("wsColumn".$this->configNumber, $this->actionColumnDefinition);
        $action->register("wsInclude".$this->configNumber, $this->includeColumnDefinition);
    }

    public function output() {
        $this->setHeaders();
        $this->render();
        $this->setSearchDocument();
        $this->memoConfiguration();
        return $this->lay->gen();
    }
}
?>