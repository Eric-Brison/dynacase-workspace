
function viewinline(event,url,toview,tocache) {
  var ov=document.getElementById(toview);
  var oc=document.getElementById(tocache);
  var obrfull=document.getElementById('brfull');
  if (ov && oc) {

    if (obrfull.style.display=='none') obrfull.style.display='';
    else obrfull.style.display='none';
    if  (  (ov.src != url)   )  {
      ov.src=url;
    }
    oc.style.display='none';
    ov.style.display='';
    resizeIurl(ov.id);
  }
}
function resizeIurl(iurl) {
var eiurl=document.getElementById(iurl);
var xy=getAnchorPosition(iurl);
var hiurl=getFrameHeight();
var wiurl=getFrameWidth();
var nh=hiurl - xy.y - 22; // offset for scrollbar
// alert(xy.y+'--'+hiurl+'--'+nh);

eiurl.height=nh;
eiurl.width=wiurl-10;
}

function receiptActionNotification(code,arg) {
  
  for (var i=0;i<code.length;i++) {
    switch (code[i]) {
      
    case "LOCKFILE":
      var ilck=document.getElementById('imglck');
      if (ilck) ilck.src='Images/clef1.gif';
      break;
    case "UNLOCKFILE":
      var ilck=document.getElementById('imglck');
      if (ilck) ilck.src='Images/1x1.gif';
      break;
    }
  }
}
