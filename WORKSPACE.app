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
   "name"		=>"ADMIN",
   "short_name"		=>N_("interface to navigate in spaces"),
   "acl"		=>"WORKSPACE_MASTER",
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
  )  ,
  array( 
   "name"		=>"WS_FOLDERLIST",
   "short_name"		=>N_("to view folder"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_VIEWDOC",
   "short_name"		=>N_("to view a document"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_DELETEDOC",
   "short_name"		=>N_("to view a document"),
   "acl"		=>"WORKSPACE_USER"
  ) ,
  array( 
   "name"		=>"WS_POPUPLISTFOLDER",
   "short_name"		=>N_("popup menu global in folder list"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_POPUPDOCFOLDER",
   "short_name"		=>N_("popup menu in folder list"),
   "acl"		=>"WORKSPACE_USER"
  )  , 
  array( 
   "name"		=>"WS_POPUPSIMPLEFILE",
   "short_name"		=>N_("popup menu for simplefile family"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_POPUPSIMPLEFOLDER",
   "short_name"		=>N_("popup menu for simplefolder family"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_EMPTYTRASH",
   "short_name"		=>N_("empty the user trash"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_RESTOREDOC",
   "short_name"		=>N_("restore a document from the trash"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_DOWNLOADFILE",
   "short_name"		=>N_("download file from simplefile"),
   "acl"		=>"WORKSPACE_USER"
  ) ,
  array( 
   "name"		=>"WS_EDITHTMLFILE",
   "short_name"		=>N_("edit HTML file"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_MODHTMLFILE",
   "short_name"		=>N_("edit HTML file"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_MODEDITFILE",
   "short_name"		=>N_("edit download file"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_EDITMODFILE",
   "short_name"		=>N_("edit download file"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_COUNTFOLDER",
   "short_name"		=>N_("refresh count item of folder"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_CANCELMODFILE",
   "short_name"		=>N_("cancel download file"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_EDITADDVERSION",
   "short_name"		=>N_("edit for new version"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_DOWNLOADEDITFILE",
   "short_name"		=>N_("download file for edition"),
   "acl"		=>"WORKSPACE_USER"
  ) ,
  array( 
   "name"		=>"WS_UPLOADFILE",
   "short_name"		=>N_("upload file for modification"),
   "acl"		=>"WORKSPACE_USER"
  ) ,
  array( 
   "name"		=>"WS_ADDVERSION",
   "short_name"		=>N_("create a new version"),
   "acl"		=>"WORKSPACE_USER"
  )  ,
  array( 
   "name"		=>"WS_COPYDOC",
   "short_name"		=>N_("duplicate a document"),
   "acl"		=>"WORKSPACE_USER"
  ) 
);

?>
