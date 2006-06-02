var WEDITHTML=null; // window of HTML editor

function viewinline(event,url,toview,tocache) {
  var ov=document.getElementById('iinline');
  var dview=getElementsByNameTag(document,toview,'div');
  var dcache=getElementsByNameTag(document,tocache,'div');

  var i;

  for (i=0;i<dcache.length;i++) {
    dcache[i].style.display='none';
  }
  for (i=0;i<dview.length;i++) {
    dview[i].style.display='';
  }
  
  if (ov ) {
    if (toview=='dview') ov.style.display='';
    else  ov.style.display='none';
    
    if (url != '') {
      if  (  (ov.src == 'about:blank')   )  {
	//	alert('resource:\n'+ov.src+'\n'+url);
	ov.src=url;
      } 
    } 

    resizeIurl(ov.id);
  }
}

function wsreload(event,toreload) {
  var ov=window.winline;
  if (ov ) {
    ov.location.reload();
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

function wsischanged(event) {
  if (! event) event=window.event;
  //alert(event);
  if (INPUTCHANGED)  event.returnValue='Attribute in modification';
  else {
    if (WEDITHTML) {
      if (! WEDITHTML.closed) {
	if (WEDITHTML.wsIsModified()) {
	  event.returnValue='HTML in edition';
	}
      }
    }
  }
}
addEvent(window,"beforeunload",wsischanged)
