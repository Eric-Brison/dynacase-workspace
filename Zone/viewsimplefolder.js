
function viewsimplefoldermenu(event,docid,source) {
  var corestandurl=window.location.pathname+'?sole=Y&';
  var menuurl=corestandurl+'app=WORKSPACE&action=WS_POPUPSIMPLEFOLDER&id='+docid;
  viewmenu(event,menuurl,source);
}

function shortcutToFld(event,docid,idbasket) {
  var corestandurl=window.location.pathname+'?sole=Y&';
  requestUrlSend(null,corestandurl+'app=WORKSPACE&action=WS_FOLDERICON&id='+idbasket+'&addid='+docid+'&addft=shortcut');
}
