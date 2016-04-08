
/**
 * @author Anakeen
 */


function verifydownload(docid) {

  var v=getdocvalue(docid,'sfi_version');
 
  if ((! v) || (v != document.getElementById('iversion').value)) {
    window.setTimeout('verifydownload('+docid+')',2000);
  } else {

    window.opener.location.reload();
    document.getElementById('before').style.display='none';
    document.getElementById('after').style.display=''; 
  }
}

var OURL;
function ws_autoclose(event) {
  if ((! window.opener) || (window.opener.closed)) self.close();

  var a;
  try{
    a=window.opener.location;
    a=window.opener.location.href;
  }
  catch(exception) {
    self.close();
  }
  
  if ( (! window.opener.location)  || (! window.opener.location.href) || (window.opener.location.href != OURL)) {
    self.close();
  }  
}
if (window.opener)   {
    OURL=window.opener.location.href;
    setInterval('ws_autoclose()',2000);
 }
