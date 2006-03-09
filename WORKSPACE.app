<?php


$app_desc = array (
"name"		=>"WORKSPACE",		//Name
"short_name"	=>N_("WorkSpace"),		//Short name
"description"	=>N_("Documents Exchange"),//long description
"access_free"	=>"N",			//Access free ? (Y,N)
"icon"		=>"workspace.gif",	//Icon
"displayable"	=>"Y",			//Should be displayed on an app list (Y,N)
"with_frame"	=>"Y",			//Use multiframe ? (Y,N)
"childof"	=>""		// 	
);


$app_acl = array (
  array(
   "name"               =>"WORKSPACE_MASTER",
   "description"        =>N_("Access For Workspace edition")),
  array(
   "name"               =>"WORKSPACE_USER",
   "description"        =>N_("Access for exchange documents"))
);
   
$action_desc = array (
  array( 
   "name"		=>"WS_NAVIGATE",
   "short_name"		=>N_("interface to navigate in spaces"),
   "acl"		=>"WORKSPACE_USER",
   "root"		=>"Y"
  ),
  array( 
   "name"		=>"WS_ADDFLDBRANCH",
   "short_name"		=>N_("add branch in folder tree"),
   "acl"		=>"WORKSPACE_USER"
  ) ,
  array( 
   "name"		=>"WS_FOLDERICON",
   "short_name"		=>N_("to view clipboard"),
   "acl"		=>"WORKSPACE_USER"
  ) 
);

?>
