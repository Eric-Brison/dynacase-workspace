<?php


  /**
   * return array of sub element of folder
   */
function getFldContentNot() {
include_once("FDL/Class.DocRel.php");
  $rdoc=new DocRel($this->dbaccess,$this->initid);
  $rdoc->sinitid=$this->initid;
  
  $trel=$rdoc->getRelations("folder","D");
  $tc=array();
  foreach ($trel as $k=>$v) {
    $tc[]=array("title"=>utf8_encode($v["ctitle"]),
		"id"=>$v["cinitid"],
		"icon"=>$this->getIcon($v["cicon"]));
  }
  return $tc;
}
function getFldContent() {
  $ls=$this->getContent(true,array("doctype='D'"));
  $tc=array();
  foreach ($ls as $k=>$v) {
    $tc[]=array("title"=>utf8_encode($v["title"]),
		"id"=>$v["id"],
		"icon"=>$this->getIcon($v["icon"]));
  }
  return $tc;
}
?>