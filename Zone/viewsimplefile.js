var WEDITHTML=null; // window of HTML editor

function viewinline(event,url,ifr) {
  var ov=document.getElementById('i'+ifr);
  var ds=document.getElementById('summary');
  var ogid=document.getElementById('ogident');
  var ogreload=document.getElementById('ogreload');
  var ogin=document.getElementById('og'+ifr);
  var ov1,og1,og2;
  var i;

  ds.style.display='none';
  if (ifr=='histo') {
     ov1=document.getElementById('iinline');  
     og1=document.getElementById('oginline');  
     og2=document.getElementById('ogreload');    
  } else {
     ov1=document.getElementById('ihisto');  
     og1=document.getElementById('oghisto');    
     if (ogreload) ogreload.style.display='';  
  }
  if (ov1) ov1.style.display='none';
  if (og1) og1.className='';
  if (og2) og2.style.display='none';
  
  if (ov ) {
     ov.style.display='';
   
    
    if (url != '') {
      if  (  (ov.src == 'about:blank')   )  {
	//	alert('resource:\n'+ov.src+'\n'+url);
	ov.src=url;
      } 
    } 

    resizeIurl(ov.id);
  }
  ogid.className='';
  ogin.className='active';
}

function unviewinline(event) {
  var ov1=document.getElementById('iinline');
  var ov2=document.getElementById('ihisto');
  var ds=document.getElementById('summary');
  var ogid=document.getElementById('ogident');
  var ogin=document.getElementById('oginline');
  var oghi=document.getElementById('oghisto');
  var ogreload=document.getElementById('ogreload');
  if (ov1) ov1.style.display='none';
  if (ov2) ov2.style.display='none';
  if (ds) ds.style.display='';
  if (ogid) ogid.className='active';
  if (ogin) ogin.className='';
  if (oghi) oghi.className='';
  if (ogreload) ogreload.style.display='none';
  
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
var nh=hiurl - xy.y - 30; // offset for scrollbar
// alert(xy.y+'--'+hiurl+'--'+nh);

eiurl.height=nh;
eiurl.width=wiurl-50;
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
      displayemptyvalue();
      break;
    
    case "MODATTR":
      if (arg[i]=='sfi_version') {
	var ilck=document.getElementById('imgver');
	if (ilck) ilck.style.display='';
      }
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
      var wc; // to prevent not wanted IE event onbeforeunload in href
      try{
	wc= WEDITHTML.closed;
	if (! wc) {
	
	  //      alert('wsischanged'+event.type);displayPropertyNames(event);
	  if (WEDITHTML.wsIsModified()) {
	    // event.returnValue='HTML editor will be closed';
	    event.returnValue='L\'editeur HTML va etre ferme';
	  }
	}
      }
      catch(exception) {
	return;
      }        
    }
  }
}


function LTrim(STRING){
  while ((STRING.charAt(0)==" ")||(STRING.charCodeAt(0)==13)||(STRING.charCodeAt(0)==10)) {
    STRING = STRING.replace(STRING.charAt(0),"");
  }
  return STRING;
}

function displayemptyvalue() {
  var dattr=getElementsByNameTag(document,'attrvalue','div');
  var i;
  var emptytext;
  var s;

  for (i=0;i<dattr.length;i++) {
    emptytext=dattr[i].getAttribute('emptytext');
    if (!emptytext) emptytext='no text';

    s=LTrim(dattr[i].innerHTML);   
    if ((s=='')||(dattr[i].innerHTML==emptytext)) {     
      dattr[i].innerHTML=emptytext;      
      dattr[i].className='attrempty';
    } else {      
      dattr[i].className='attrvalue';
    }
  }  
}

addEvent(window,"beforeunload",wsischanged)
addEvent(window,"load",displayemptyvalue)

function viewsimplefilemenu(event,docid,source) {
  var corestandurl=window.location.pathname+'?sole=Y&';
  var menuurl=corestandurl+'app=WORKSPACE&action=WS_POPUPSIMPLEFILE&id='+docid;
  viewmenu(event,menuurl,source);
}

function shortcutToFld(event,docid,idbasket) {
  var corestandurl=window.location.pathname+'?sole=Y&';
  requestUrlSend(null,corestandurl+'app=WORKSPACE&action=WS_FOLDERICON&id='+idbasket+'&addid='+docid+'&addft=shortcut');

}

function restoreDoc(event,docid) {
  var corestandurl=window.location.pathname+'?sole=Y&';
  requestUrlSend(null,corestandurl+'app=WORKSPACE&action=WS_RESTOREDOC&id='+docid)
}

function renameFile(event,docid,attrid,newname) {
  var corestandurl=window.location.pathname+'?sole=Y&';
  enableSynchro();
  requestUrlSend(null,corestandurl+'app=WORKSPACE&action=WS_RENAMEFILE&id='+docid+'&newname='+newname);
  disableSynchro();
  cancelattr(event,docid,attrid);
}
function viewthumbnail() {
  var dt=document.getElementById('dthumbnail');
  var da=document.getElementById('dabstract');
  var ot=document.getElementById('ogthumbnail');
  var oa=document.getElementById('ogabstract');
  if (da && dt) {
    dt.style.display='';
    da.style.display='none';
    oa.className='';
    ot.className='active';
  }
}
function viewabstract() {
  var dt=document.getElementById('dthumbnail');
  var da=document.getElementById('dabstract');
  var ot=document.getElementById('ogthumbnail');
  var oa=document.getElementById('ogabstract');
  if (da && dt) {
    dt.style.display='none';
    da.style.display='';
    oa.className='active';
    ot.className='';
    
  }
}
function popdiv(event,url,divtitle,x,y,w,h) {
  var ddov=newPopdiv(event,divtitle,x,y,w,h);    
  requestUrlSend(ddov,url);
}


function getDavUrl(th,docid,vid,davaddr) {
  var aurl;
  var sid=getsessionid(docid,vid);
  th.href='asdav://'+davaddr+'/freedav/vid-'+sid+'/'+document.getElementById('sfi_title').innerHTML  
}
