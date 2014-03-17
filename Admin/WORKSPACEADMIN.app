<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
*/

$app_desc = array(
    "name" => "WORKSPACEADMIN", //Name
    "short_name" => N_("WorkSpace Admin") , //Short name
    "description" => N_("Manage workspaces") , //long description
    "icon" => "workspace-admin.png", //Icon
    "displayable" => "Y", //Should be displayed on an app list (Y,N)
    "childof" => "", //
    "tag" => "ADMIN"
);

$app_acl = array(
    array(
        "name" => "WORKSPACE_ADMIN",
        "description" => N_("Access For Workspace edition")
    )
);

$action_desc = array(
    
    array(
        "name" => "ADMIN",
        "short_name" => N_("interface to navigate in spaces") ,
        "acl" => "WORKSPACE_ADMIN",
        "root" => "Y"
    )
);
