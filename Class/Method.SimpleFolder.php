<?php

var $defaultview= "WORKSPACE:VIEWSIMPLEFOLDER:T";
var $defaultedit= "WORKSPACE:EDITSIMPLEFOLDER";



function viewsimplefolder($target="_self",$ulink=true,$abstract=false) {
  global $action;
  $this->viewdefaultcard($target,$ulink,$abstract);
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDL/Layout/editattr.js");
}

function editsimplefolder() {
  $this->editattr();
  
}
?>