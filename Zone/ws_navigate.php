<?php
/*
 * Display doucment explorer
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
include_once ("GENERIC/generic_util.php");
include_once ("FDL/Class.SearchDoc.php");
/**
 * View folders and document for exchange them
 * @param Action &$action current action
 */

class ws_Navigate
{
    private $action;
    private $application;
    /**
     * @var SearchDoc
     */
    private $spaces = null;
    private $viewMySpace = true;
    private $folderListHeight = 200;
    private $folderTreeHeight = 300;
    private $folderTreeWidth = 250;
    private $globalSearch = null;
    private $actionFolderList = 'WORKSPACE:WS_FOLDERLIST';
    private $actionFolderDocPopup = 'WORKSPACE:WS_POPUPDOCFOLDER';
    private $actionFolderPopup = 'WORKSPACE:WS_POPUPLISTFOLDER';
    private $actionColumnDefinition = "wsFolderListFormat::getColumnDescription()";
    private $includeColumnDefinition = '';
    private $initialFolder = 0;
    
    public function __construct(Action & $action)
    {
        $this->action = $action;
        $this->application = $action->parent;
        $this->lay = new Layout(getLayoutFile("WORKSPACE", "ws_navigate.xml") , $action);
    }
    public function setSpaces(SearchDoc $spaces)
    {
        $spaces->setObjectReturn();
        $this->spaces = $spaces;
    }
    /**
     * use predefined search object
     * @param array $families
     */
    public function setGlobalSearch(SearchDoc $search)
    {
        $this->globalSearch = $search;
    }
    
    public function viewMySpace($viewMySpace)
    {
        
        $this->viewMySpace = ($viewMySpace == true);
    }
    public function setFolderListHeight($h)
    {
        if ($h) $this->folderListHeight = $h;
    }
    public function setFolderTreeHeight($h)
    {
        if ($h) $this->folderTreeHeight = $h;
    }
    public function setFolderTreeWidth($h)
    {
        if ($h) $this->folderTreeWidth = $h;
    }
    private function trashempty($userid)
    {
        $q = new QueryDb($this->action->dbaccess, "Doc");
        $q->Query(0, 0, "TABLE", sprintf("select id from doc where doctype='Z' and owner=%d limit 1", $userid));
        
        return ($q->nb == 0);
    }
    
    function setFolderListAction($actionName)
    {
        if (preg_match('/([^:]*):(.*)/', $actionName, $reg)) {
            $this->actionFolderList = $actionName;
        }
    }
    
    function setFolderDocPopup($actionName)
    {
        if (preg_match('/([^:]*):(.*)/', $actionName, $reg)) {
            $this->actionFolderDocPopup = $actionName;
        }
    }
    
    function setFolderPopup($actionName)
    {
        if (preg_match('/([^:]*):(.*)/', $actionName, $reg)) {
            $this->actionFolderPopup = $actionName;
        }
    }
    function setFolderListInclude($phpFile)
    {
        
        $this->includeColumnDefinition = $phpFile;
    }
    function setFolderListColumn($column)
    {
        
        $this->actionColumnDefinition = $column;
    }
    function setInitialFolder($dirid)
    {
        $this->initialFolder = $dirid;
    }
    
    function addOffline(Action & $action)
    {
        $dbaccess = $this->action->dbaccess;
        $desktop = getTDoc($dbaccess, 'FLDOFFLINE_' . Doc::getSystemUserId());
        if (!$desktop) {
            $desktop = createDoc($dbaccess, "DIR");
            $desktop->title = _("Offline");
            $desktop->setTitle($desktop->title);
            $desktop->setValue("ba_desc", sprintf(_("Offline folder of %s") , $action->user->firstname . " " . $action->user->lastname));
            $desktop->icon = 'fldoffline.png';
            $desktop->name = 'FLDOFFLINE_' . $action->user->id;
            $desktop->Add();
            /**
             * @var Dir $desktop
             */
            $home = $desktop->getHome();
            $home->insertDocument($desktop->initid);
            $action->lay->set("FREEDOM_IDOFFLINE", $desktop->initid);
        } else $action->lay->set("FREEDOM_IDOFFLINE", $desktop["id"]);
    }
    public function setHeaders()
    {
        $this->application->AddJsRef($this->action->getParam("CORE_JSURL") . "/geometry.js");
        $this->application->AddJsRef($this->action->getParam("CORE_JSURL") . "/subwindow.js");
        $this->application->AddJsRef($this->action->getParam("CORE_JSURL") . "/AnchorPosition.js");
        if ($this->action->Read("navigator") == "EXPLORER") $this->application->AddJsRef($this->action->getParam("CORE_JSURL") . "/iehover.js");
        $this->application->AddJsRef($this->action->getParam("CORE_JSURL") . "/resizeimg.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL") . "/FDL/Layout/common.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL") . "/FDL/Layout/popupdoc.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL") . "/FDC/Layout/inserthtml.js");
        $this->application->AddJsRef($this->action->getParam("CORE_JSURL") . "/DHTMLapi.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL") . "/FDC/Layout/setparamu.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL") . "/WORKSPACE/Layout/displayws.js");
        $this->application->AddJsRef($this->action->getParam("CORE_PUBURL") . "/WORKSPACE/Layout/mechanism.js");
        // css pour popup
        $this->application->AddCssRef("FDL:POPUP.CSS", true);
        $this->application->AddCssRef("WORKSPACE:system.css", true);
        $this->application->AddCssRef("WORKSPACE:default.css", true);
    }
    public function render()
    {
        
        $dbaccess = $this->action->GetParam("FREEDOM_DB");
        
        $tlayspaces = array();
        
        if ($this->spaces) {
            $this->spaces->search();
            /**
             * @var Doc $doc
             */
            while ($doc = $this->spaces->getNextDoc()) {
                $tlayspaces[] = array(
                    "stitle" => $doc->title,
                    "sicon" => $doc->getIcon() ,
                    "sid" => $doc->id
                );
            }
        }
        $this->lay->Set("nospaces", ($this->spaces == null));
        $famid = getFamIdFromName($dbaccess, "SIMPLEFILE");
        $mode = getSearchMode($this->action, $famid);
        $this->lay->Set("FULLMODE", ($mode == "FULL"));
        
        $this->lay->setBlockData("SPACES", $tlayspaces);
        if ($this->trashempty($this->action->user->id)) $this->lay->set("imgtrash", $this->action->parent->getImageLink('trashempty.png'));
        else $this->lay->set("imgtrash", $this->action->parent->getImageLink('trash.png'));
        $persofldid = '';
        $homename = "WS_PERSOFLD_" . Doc::getSystemUserId();
        $perso = getTDoc($dbaccess, $homename);
        if (!$perso) {
            // create "my space" folder
            
            /**
             * @var _SIMPLEFOLDER $perso
             */
            $perso = createDoc($dbaccess, "SIMPLEFOLDER", false);
            $perso->name = $homename;
            
            $perso->setValue("ba_title", _("My space"));
            $perso->setValue("ba_desc", sprintf(_("personal space of %s") , $perso->getUserName(true)));
            $perso->icon = 'gnome-fs-home.png';
            $err = $perso->Add();
            if ($err != "") $this->action->addWarningMsg($err);
            if ($err == "") {
                $persofldid = $perso->id;
                $home = $perso->getHome();
                if ($home) $home->insertDocument($persofldid); //add in general home
                
            }
        } else {
            $persofldid = $perso["id"];
        }
        if ($this->action->getParam("WS_OFFLINE") == "yes") $this->addOffline($this->action);
        $this->lay->set("persofldid", $persofldid);
        $this->lay->set("myspace", $this->viewMySpace);
        $this->lay->set("folderListHeight", $this->folderListHeight);
        $this->lay->set("folderTreeHeight", $this->folderTreeHeight);
        $this->lay->set("folderTreeWidth", $this->folderTreeWidth);
        $this->lay->set("actionFolderList", $this->actionFolderList);
        $this->lay->set("actionFolderDocPopup", $this->actionFolderDocPopup);
        $this->lay->set("actionFolderPopup", $this->actionFolderPopup);
        $this->lay->set("initialFolder", $this->initialFolder);
    }
    private function setSearchDocument()
    {
        if (!$this->globalSearch) {
            $this->globalSearch = new SearchDoc($this->action->dbaccess);
        }
        /**
         * @var _DSEARCH $ws
         */
        $ws = createTmpDoc($this->action->dbaccess, "DSEARCH");
        $ws->setValue("ba_title", sprintf(_("search %s") , "workspace"));
        $ws->add();
        $ws->addStaticQuery($this->globalSearch->getOriginalQuery());
        $this->lay->set("searchId", $ws->id);
    }
    
    private function memoConfiguration()
    {
        global $action;
        $configNumber = time();
        $this->lay->set("configNumber", $configNumber);
        $action->register("wsColumn" . $configNumber, $this->actionColumnDefinition);
        $action->register("wsInclude" . $configNumber, $this->includeColumnDefinition);
    }
    
    public function output()
    {
        $this->setHeaders();
        $this->render();
        $this->setSearchDocument();
        $this->memoConfiguration();
        return $this->lay->gen();
    }
}
?>
