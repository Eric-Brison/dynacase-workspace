<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/
namespace Dcp\Workspace;

use \Dcp\Family\Dir;

Class SimpleFolder extends Dir
{
    var $defaultview = "WORKSPACE:VIEWSIMPLEFOLDER:T";
    var $defaultedit = "WORKSPACE:EDITSIMPLEFOLDER";
    /**
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     * @templateController
     */
    function viewsimplefolder($target = "_self", $ulink = true, $abstract = false)
    {
        global $action;
        $this->viewdefaultcard($target, $ulink, $abstract);
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDL/Layout/editattr.js");
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDL/Layout/popupdoc.js");
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDC/Layout/inserthtml.js");
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/WORKSPACE/Layout/viewsimplefolder.js");
        $this->lay->set("icon", $this->getIcon());
    }
    /**
     * @templateController
     */
    function editsimplefolder()
    {
        $this->editattr();
    }
}
