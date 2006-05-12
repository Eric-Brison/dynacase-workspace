


var INPROGRESS=false;
var THECIBLE=false;
var EXPANDIMG=false;
var req;
var CURSPACE=false;
var CFLDID=false; // current folder doc id
var CLIPCID=false; // current folder for clipboard
var SYNCHRO=false; // 
var  CGCURSOR='auto'; // current global cursor
var REFRESH=false; // to indicate the the state is for resfresh one part
// ----------------------------- expand tree --------------------
function folderTreeSend(n,cible,adddocid,padddocid,addft) {
  if (INPROGRESS) alert('abordted');
  if (INPROGRESS) return false; // one request only
    // branch for native XMLHttpRequest object
    if (window.XMLHttpRequest) {
        req = new XMLHttpRequest(); 
    } else if (window.ActiveXObject) {
      // branch for IE/Windows ActiveX version
      isIE = true;
      req = new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (req) {
      if (! SYNCHRO) req.onreadystatechange = XmlInsertHtml;
      req.open("POST", 'index.php?sole=Y&app=WORKSPACE&action=WS_ADDFLDBRANCH&id='+n,(!SYNCHRO));
      req.setRequestHeader("Content-type", "application/x-www-form-urlencoded2"); 
      //      req.setRequestHeader("Content-length", "1"); 
      globalcursor('progress');
      THECIBLE=cible;
      if (adddocid) req.send("addid="+adddocid+"&addft="+addft+"&paddid="+padddocid);
      else req.send('');

      if (SYNCHRO) {
	INPROGRESS=false;
	unglobalcursor();
	if(req.status == 200) {
	   
	  if (req.responseXML) insertXMlResponse(req.responseXML)
	  else {
	    alert('no xml\n'+req.responseText);
	    return;
	  } 
	}
      } else {
	globalcursor('progress');
	INPROGRESS=true;
	return true;
      }
    }    
}

function XmlInsertHtml() {
  INPROGRESS=false; 
  //document.body.style.cursor='auto';
  unglobalcursor();
  if (req.readyState == 4) {
    // only if "OK"
    //dump('readyState\n');
    if (req.status == 200) {
      // ...processing statements go here...
      //  alert(req.responseText);
      if (req.responseXML) insertXMlResponse(req.responseXML)
      else {
	alert('no xml\n'+req.responseText);
	return;
      } 	  
    } else {
      alert("There was a problem retrieving the XML data:\n" +
	    req.statusText+' code :'+req.status);
      return;
    }
  } 
}
function insertXMlResponse(xmlres) {  
    var o=THECIBLE;
    if (xmlres) {
      var elts = xmlres.getElementsByTagName("status");
      if (elts.length == 1) {
	  var elt=elts[0];
	  var code=elt.getAttribute("code");
	  var delay=elt.getAttribute("delay");
	  var c=elt.getAttribute("count");
	  var w=elt.getAttribute("warning");

	  if (w != '') alert(w);
	  if (code != 'OK') {
	    alert('code not OK\n'+req.responseText);
	    return;
	  }
	  elts = xmlres.getElementsByTagName("branch");
	  if (elts && (elts.length>0)) {
	    elt=elts[0].firstChild.nodeValue;
	    if (o) {
	      if (c > 0)       o.style.display='';
	      o.innerHTML=elt;
	    }
	  }
	  var actions=xmlres.getElementsByTagName("action");
	  
	  var actname;
	  var actdocid;
	  for (var i=0;i<actions.length;i++) {
	    actname=actions[i].getAttribute("name");
	    actdocid=actions[i].getAttribute("docid");
	    postActionRefresh(actname,actdocid,c);
	  }

	  if (! isNetscape) correctPNG();
	  changedragft(null,'nothing');
	} else {
	  alert('no status\n'+req.responseText);
	  return;
	}
      }
}
function viewfoldertree(img,fldid,where,adddocid,padddocid,addft,reset) {
  if (! where) return 0;
  if (reset && reset==true) {
    where.innerHTML='';
  }

  if ((!img) || (where.childNodes.length==0)) {
    if (folderTreeSend(fldid,where,adddocid,padddocid,addft)) {
      EXPANDIMG=img;
      return 2;
    }
    else return -1;
  } else {
    if (where.style.display=='none') {
      where.style.display='';
      return 1;
    } else {
      where.style.display='none';
      return 0;
    }
  } 
}

// ----------------------------- view clipboard --------------------
function folderSend(n,cible,adddocid,padddocid,addft,kview) {
  if (INPROGRESS) return false; // one request only

  // branch for native XMLHttpRequest object
  if (window.XMLHttpRequest) {
    req = new XMLHttpRequest(); 
  } else if (window.ActiveXObject) {
    // branch for IE/Windows ActiveX version
    isIE = true;
    req = new ActiveXObject("Microsoft.XMLHTTP");
  }

  if (req) {
      if (! SYNCHRO) req.onreadystatechange = XmlInsertHtml;
      
      if (addft=='del') req.open("POST", 'index.php?sole=Y&app=WORKSPACE&action=WS_DELETEDOC&id='+adddocid, (!SYNCHRO));
      else if (kview == 'list') req.open("POST", 'index.php?sole=Y&app=WORKSPACE&action=WS_FOLDERLIST&kview='+kview+'&order='+CORDER+'&dorder='+CDESCORDER+'&id='+n, (!SYNCHRO));
      else req.open("POST", 'index.php?sole=Y&app=WORKSPACE&action=WS_FOLDERICON&kview='+kview+'&id='+n, (!SYNCHRO));
      req.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 
      //      req.setRequestHeader("Content-Length", "0");
      globalcursor('progress');
      THECIBLE=cible;
     
      if (adddocid) req.send("addid="+adddocid+"&addft="+addft+"&paddid="+padddocid);
      else req.send('');

      if (SYNCHRO) {
	INPROGRESS=false;
	unglobalcursor();
	if (req.status == 200) {	   
	  if (req.responseXML) insertXMlResponse(req.responseXML)
	  else {
	    alert('no xml\n'+req.responseText);
	    return;
	  } 
	}
      } else {
	INPROGRESS=true;	
	globalcursor('progress');
	clipboardWait(cible);
	return true;
      }
    }
}

// send generic request
function requestUrlSend(cible,url) {
  if (INPROGRESS) return false; // one request only

  // branch for native XMLHttpRequest object
  if (window.XMLHttpRequest) {
    req = new XMLHttpRequest(); 
  } else if (window.ActiveXObject) {
    // branch for IE/Windows ActiveX version
    isIE = true;
    req = new ActiveXObject("Microsoft.XMLHTTP");
  }

  if (req) {
      if (! SYNCHRO) req.onreadystatechange = XmlInsertHtml;
      
      req.open("POST", url, (!SYNCHRO));     
      req.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 
      //      req.setRequestHeader("Content-Length", "0");
      globalcursor('progress');
      THECIBLE=cible;
     
 
      req.send('');

      if (SYNCHRO) {
	INPROGRESS=false;
	unglobalcursor();
	if (req.status == 200) {	   
	  if (req.responseXML) insertXMlResponse(req.responseXML)
	  else {
	    alert('no xml\n'+req.responseText);
	    return;
	  } 
	}
      } else {
	INPROGRESS=true;	
	globalcursor('progress');
	clipboardWait(cible);
	return true;
      }
    }
}

function emptytrash(event) {
  requestUrlSend(null,'index.php?sole=Y&app=WORKSPACE&action=WS_EMPTYTRASH');
}
function restoreDoc(event,docid) {
  requestUrlSend(null,'index.php?sole=Y&app=WORKSPACE&action=WS_RESTOREDOC&id='+docid);
}
function deleteDoc(event,docid) {
  requestUrlSend(null,'index.php?sole=Y&app=WORKSPACE&action=WS_DELETEDOC&id='+docid+'&paddid='+CFLDID);
}

// ----------------------------- view document detail --------------------
function documentSend(docid,cible) {
  if (INPROGRESS) return false; // one request only
    // branch for native XMLHttpRequest object
    if (window.XMLHttpRequest) {
        req = new XMLHttpRequest(); 
    } else if (window.ActiveXObject) {
      // branch for IE/Windows ActiveX version
      isIE = true;
      req = new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (req) {
        req.onreadystatechange = XmlInsertHtml ;
        req.open("POST", 'index.php?sole=Y&app=WORKSPACE&action=WS_VIEWDOC&id='+docid, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 
	//      req.setRequestHeader("Content-Length", "0");
	THECIBLE=cible;


	req.send('');
	
	
	INPROGRESS=true;
	//document.body.style.cursor='progress';	
	globalcursor('progress');
	clipboardWait(cible);
	return true;
    }    
}
// ----------------------------- view context memnu --------------------


function refreshClipBoard(bid,where) {
  CLIPCID=bid;
  folderSend(bid,where);
}

// ----------------------------- drag & drop --------------------
var DRAGGING=false;
var DRAGDOC=false;// current document id being dragged
var PDRAGDOC=false; // current folder id of DRAGDOC
var POUL=false; // current ul id object is being dragged
var CORDER='title'; // current order for folder list
var CDESCORDER=true; // decrease or increase order
var DRAGFT=false;
var PREVSPANSELECT=false; //previous span element (folder) selected
var PREVTRSELECT=false; //previous tr element (document) selected
//var MICON=new Image(100,100);
var MICON=document.createElement("span");
var PE=null; // previous elt
var PEDROP=null; // previous elt droppable
var PECTRL=0; // previous hot key pushed (ctrl or shift)
var CDROPZ=null; // current drop zone
var IEPARASITE=null; // to ignored unwanted event fire produced by IE
MICON.className='MICON';
MICON.style.display='none';


// osp : element to copy to see in draggin mode
function begindrag(event,oul,osp,docid,pdocid) {
  if (! event) event=window.event;
  if (! DRAGGING) {
    GetXY(event);
    if (isIE) IEPARASITE=false;
    MICON.style.top = Ypos+2; 
    MICON.style.left = Xpos+2; 
    MICON.style.zIndex = 100;
    MICON.style.display='';
    DRAGGING=true;
    DRAGFT=false;
    DRAGDOC=docid;
    PDRAGDOC=pdocid;
    
    MICON.innerHTML=osp.innerHTML;
    document.onmousemove=movedrag ;
    document.onmouseup=enddrag ;
    document.onkeydown=keydrag ;
    document.onkeyup=keydrag ;

    //    document.body.style.cursor='no-drop';
    globalcursor('no-drop');

    if (isIE) {
      // sendEvent(o,"mouseover");
      //      osp.className='';
    }
    POUL=oul;
    stopPropagation(event);

    return false;
  }
}

var DEBUG=0;
function overdragft(event,o) {  
  if (DRAGGING) {
    var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);

    if (IEPARASITE == o) return;
    var drop=o.getAttribute("droppable");
    if (drop == 'yes') { 
      //       o.style.border='red 1px solid';
      
      if (isIE && (! IEPARASITE)) {
	IEPARASITE=o;
	//IEPARASITE.style.border='orange 3px solid';
	return;
      }
      if (PECTRL== 0) DRAGFT=false;
      var dft=DRAGFT;
      CDROPZ=o;
      if (!dft) {
	var ft=o.getAttribute("dropft");
	if (ft) dft=ft;
      }
      changedragft(event,dft);  
      //      document.body.style.cursor='move';
      globalcursor('move');
    } else {      
      var oft=document.getElementById('miconft');
      if (oft) oft.innerHTML='';
      //o.style.border='orange 1px solid';
      
      //document.body.style.cursor='no-drop';
      globalcursor('no-drop');
    }
  } else {    
    //o.style.border='green 1px solid';
      if (o.className=='') o.className='folderhover';
  }
  //   window.status='overdragft'+DRAGFT +'idrag:'+DEBUG+'PE:'+PECTRL+'drop:'+drop;
}

function outdragft(event,o) {  
  if (DRAGGING) {
    if (IEPARASITE == o) return;
    var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);
    var oft=document.getElementById('miconft');
    if (oft) {
      oft.innerHTML='';
      //      document.body.style.cursor='no-drop';
      globalcursor('no-drop');
    }
    if (PECTRL== 0) DRAGFT=false;
    //        window.status=DRAGFT +'PE:'+PECTRL;
    CDROPZ=null;
    //o.style.border='blue 1px solid';
      if (e == PEDROP) PEDROP=false;
  } else {    
      if (o.className=='folderhover') o.className='';
  }
}

function overdoc(o) {
  if (o.className=='') o.className='trhover';
}
function outdoc(o) {
  if (o.className=='trhover') o.className='';
}
function changedragft(event,nft) {
  var oft=document.getElementById('miconft'); 
  if (!oft) {
       MICON.innerHTML=MICON.innerHTML+'<span id="miconft">COUOU</span>';
       oft=document.getElementById('miconft');
    }

  if (oft) {
    oft.innerHTML=nft;   
    DRAGFT=nft;
  }
}
function movedrag(event) {
  if (DRAGGING) {
    if (! event) event=window.event;
    GetXY(event);
    MICON.style.top = Ypos+2; 
    MICON.style.left = Xpos+2; 
    //stopPropagation(event)
    var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);   
    var drop=e.getAttribute("droppable");

    if (drop == 'yes') {
      if (PEDROP!=e) {	
	if (PEDROP)   sendEvent(PEDROP,"mouseout");
	sendEvent(e,"mouseover");
	PEDROP=e;

      }
    } else {
      if (isIE) {
	if (PEDROP!=e) {	if (PEDROP)   sendEvent(PEDROP,"mouseout");}
      if (PE!=e) {	
	if (PE)   sendEvent(PE,"mouseout");
	sendEvent(e,"mouseover");
	PE=e;

      }  

      }

    }
    return false;
  }
}
function keydrag(event) {
  
    if (! event) event=window.event;
   
    var ctrl=event.ctrlKey;
    var shift=event.shiftKey;
    var lpe=0;
    if (isNetscape) {
      // the ctrlKey   is not correct
      if (event.keyCode==17) {
	if (event.type == 'keyup') ctrl=false;
	else if (event.type == 'keydown') ctrl=true;
      } else if (event.keyCode==16) {
	if (event.type == 'keyup') shift=false;
	else if (event.type == 'keydown') shift=true;
      }
    }

    if (ctrl) lpe++;
    if (shift) lpe++;
    if (lpe != PECTRL) {
      
      if (ctrl && (!shift)) {   changedragft(event,'copy');	}
      else if (ctrl && shift) { changedragft(event,'link');	}
      else  if ((!ctrl) && shift) { changedragft(event,'move');	}
      else {
	  var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null); 

	  DRAGFT=false;
	  //dump('\tkeydrag\n');
	  if (CDROPZ) sendEvent(CDROPZ,"mouseover");
	  else  changedragft(event,'nothing');
      }
      
      PECTRL=lpe;
    }    
    //    window.status= ':ft:['+DRAGFT+ ']:dropz['+CDROPZ;
    //    window.status=event.keyCode + ':alt:['+event.altKey+ ']:ctrl['+event.ctrlKey+']:shift['+event.shiftKey+'PE:'+PECTRL;
}
function enddrag(event) {
  
  if (! event) event=window.event;
  document.onmousemove= "";
  document.onmouseup="" ;
  document.onkeyup="" ;
  document.onkeydown="" ;
  var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);

  MICON.style.display='none';
  sendEvent(e,"mouseup");
  
  //  document.body.style.cursor='auto';
  unglobalcursor();
  DRAGGING=false;
  PECTRL=false;
  //  changedragft(event,'nothing');
}

function copydrag(o,ulid,cdocid) {
  
}

function initDrag() {
  document.body.appendChild(MICON);  
}

addEvent(window,"load",initDrag);

// ----------------------------- Insert ClipBoard --------------------
function insertinclipboard(event,o,bid,kview) {

    if (! event) event=window.event;
    if (! kview) kview='icon';
    var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);

    //    alert("insertinclipboard"+bid+':'+kview+event.toString());
    if (DRAGGING) {
     
      if (! isIE) DRAGGING=false;

      if (PDRAGDOC != bid) {
	DRAGGING=false;
	if ((DRAGDOC>0)&&(DRAGFT)) folderSend(bid,o,DRAGDOC,PDRAGDOC,DRAGFT,kview);
      }
    } 
}

function insertinspace(event,o,sid) {
    if (! event) event=window.event;
    if (DRAGGING) {
      DRAGGING=false;
      if ((DRAGDOC>0)&&(DRAGFT)) folderSend(sid,false,DRAGDOC,PDRAGDOC,DRAGFT);
    } 
}
function deleteinspace(event,o) {
    if (! event) event=window.event;
    if (DRAGGING) {
      DRAGFT='del';
      DRAGGING=false;
      if ((DRAGDOC>0)&&(DRAGFT)) folderSend(null,false,DRAGDOC,PDRAGDOC,DRAGFT,'list');
    } 
}


function insertinfolder(event,o,oimg,docid,ulid) {
    if (! event) event=window.event;
   
    if ((DRAGGING)&& (PDRAGDOC!=docid)&& (DRAGDOC!=docid)&&(DRAGFT)){
      document.getElementById(ulid).innerHTML='';

      expandtree(document.getElementById(oimg),docid,ulid,DRAGDOC,PDRAGDOC,DRAGFT);
      DRAGGING=false;
    } 
}
function viewFolder(event,dirid,o) {
  var  where=document.getElementById('fldlist');
  if (o) {
    if (PREVSPANSELECT) PREVSPANSELECT.className='';
    o.className='folderselect';
    PREVSPANSELECT=o;
  }
  CFLDID=dirid;
  folderSend(dirid,where,null,null,null,'list');
  
}
function changeOrder(event,norder) {
  if (CORDER == norder) CDESCORDER=(!CDESCORDER); // invert order
  else {
    CORDER=norder;
    CDESCORDER=true;
  }
  viewFolder(event,CFLDID);
}
function viewDoc_(event,docid) {
  var  where=document.getElementById('resume');

  documentSend(docid,where);
  
}

function viewDoc(event,docid,o) {
  var  where=document.getElementById('iresume');
  if (o) {
    if (PREVTRSELECT) PREVTRSELECT.className='';    
    o.className='docselect';
    PREVTRSELECT=o;
  }

  where.src='index.php?sole=Y&app=FDL&action=FDL_CARD&id='+docid;

  
}
// to find the previous button to refresh branch in folder trre
function getPrevLiButton(o) {
  var e=o.parentNode.parentNode.parentNode;
  if (e && e.parentNode) {
    e=e.parentNode.childNodes[0];
    //    alert(e.nodeType);
    if (e && (e.nodeType==1) && e.getAttribute('ondblclick')) return e;
  }
  return CURSPACE;
}


function viewdetailmenu(event,docid,source) {
  var menuurl='index.php?sole=Y&app=WORKSPACE&action=WS_POPUPDOCFOLDER&id='+docid;
  viewmenu(event,menuurl,source);
}

function viewfoldermenu(event,docid,source) {
  var menuurl='index.php?sole=Y&app=WORKSPACE&action=WS_POPUPLISTFOLDER&id='+docid;
  viewmenu(event,menuurl,source); 
}


function globalcursor(c)
{
  if (c==CGCURSOR) return;
  if (!document.styleSheets) return;
  unglobalcursor();
  document.body.style.cursor=c;
  if (document.styleSheets[1].addRule) {
	  document.styleSheets[1].addRule("*","cursor:"+c+" ! important",0);
  } else if (document.styleSheets[1].insertRule) {
	  document.styleSheets[1].insertRule("*{cursor:"+c+" ! important;}", 0); 
  }
  CGCURSOR=c;
		
}
function unglobalcursor() {
  if (!document.styleSheets) return;
  var theRules;
  var theSheet;
  var r0;
  var s='';

  document.body.style.cursor='auto';

  theSheet=document.styleSheets[1];
  if (document.styleSheets[1].cssRules)
    theRules = document.styleSheets[1].cssRules;
  else if (document.styleSheets[1].rules)
    theRules = document.styleSheets[1].rules;
  else return;

  r0=theRules[0].selectorText; 
  /* for (var i=0; i<theSheet.rules.length; i++) {
     s=s+'\n'+theSheet.rules[i].selectorText;
     s=s+'-'+theSheet.rules[i].style;
     }*/
  //  alert(s);

  if ((r0 == '*')||(r0 == '')) {

    if (document.styleSheets[1].removeRule) {
   
      document.styleSheets[1].removeRule(0);
    } else if (document.styleSheets[1].deleteRule) {
      document.styleSheets[1].deleteRule(0); 
    }
  }
  CGCURSOR='auto';;
		
}


function postActionRefresh(action,docid,c) {  
  switch (action) {
  case "ADDFILE":
    //    alert("ADDFILE:"+docid);
    postAddFile(docid);
    break;
  case "DELFILE":
    //  alert("DELFILE:"+docid);
    postAddFile(docid);
    
    break;
  case "EMPTYTRASH":
    //  alert("DELFILE:"+docid);
    postEmptyTrash(docid);
    
    break;
  case "TRASHFILE":
    postTrashFile(docid);
    
    
    break;
  case "ADDBRANCH":
    //  alert("DELFILE:"+docid);
    endexpandtree(EXPANDIMG,c);
    EXPANDIMG=null;
    
    break;
  case "LOCKFILE":
  case "UNLOCKFILE":    
   postLocking(docid);
        
    break;
  default:    
    alert("UNKNOW:"+action+":"+docid);
  }
  // alert("ACTION:"+action+":"+docid);
}


function postAddFile(docid) {
  var fldid;
  var img;
  SYNCHRO=true;
  if (CFLDID == docid) {
    viewFolder(null,CFLDID)
  }
  if (CLIPCID == docid) {
    refreshClipBoard(CLIPCID,document.getElementById('clipboard'))
  }
  for (var i=0; i<document.images.length; i++)   {
    img=document.images[i];
    fldid=img.getAttribute("docid");
    if (docid==fldid) {
      img.style.border='solid 2px green';
      if (img.getAttribute('ondblclick')) {
	//	expandtree(this,'[id]','[ulid][id]',null,null,null,true)
	img.ondblclick.apply(img,[]);	 
      }      
    }
  }
  
  SYNCHRO=false;
  
}


function postEmptyTrash() {
  var fldid;
  var o;
  if (CFLDID == 'trash') {
    viewFolder(null,CFLDID)
  }
  o=document.getElementById('trashicon');
  if (o) o.src='Images/trashempty.png';    
}

function postLocking(docid) {
  var fldid;
  var o;
  if (CFLDID == 'lock') {
    viewFolder(null,CFLDID)
  }
  if (CLIPCID == 'lock') {
    refreshClipBoard(CLIPCID,document.getElementById('clipboard'))
  } 
}


function postTrashFile() {
  var fldid;
  var o;
  if (CFLDID == 'trash') {
    viewFolder(null,CFLDID)
  }
  o=document.getElementById('trashicon');
  if (o) o.src='Images/trash.png';
  
  
}
function receiptActionNotification(code,arg) {
  
  for (var i=0;i<code.length;i++) {
    //  alert(code[i]);
    postActionRefresh(code[i],arg[i]);
  }
  

}
