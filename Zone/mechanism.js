
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


var THECIBLE=false; // object where insert HTML code
var INPROGRESS=false;
var EXPANDIMG=false;
var EXPWHERE=false; // use for expand tree
var req;
var CURSPACE=false; // current selected space object
var CURSPACEID=false; // current selected space id
var CFLDID=false; // current folder doc id
var CLIPCID=false; // current folder for clipboard
var REFRESH=false; // to indicate the the state is for resfresh one part
var PREVVIEWDOCID=false; // to don't do twice teh same action
var CORE_STANDURL=window.location.pathname+'?sole=Y&';
// ----------------------------- expand tree --------------------
function folderTreeSend(n,cible,adddocid,padddocid,addft) {
  var url;
  url= CORE_STANDURL+'app=WORKSPACE&action=WS_ADDFLDBRANCH&id='+n;

  if (adddocid) url = url+ "&addid="+adddocid+"&addft="+addft+"&paddid="+padddocid;
  
  var ret=requestUrlSend(cible,url);
  changedragft(null,'');
  return ret;
}


function viewfoldertree(img,fldid,where,adddocid,padddocid,addft,reset) {
  if (! where) return 0;
  if (reset && reset==true) {
    where.innerHTML='';
  }

  if ((!img) || (where.childNodes.length==0)) {
    //      where.style.display='';
    if (folderTreeSend(fldid,where,adddocid,padddocid,addft)) {
      EXPANDIMG=img;
      EXPWHERE=where;
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
function folderSend(n,cible,adddocid,padddocid,addft,kview,key) {

  var url;
 
      
  if (addft=='del') url= CORE_STANDURL+'app=WORKSPACE&action=WS_DELETEDOC&id='+adddocid;
  else if (kview == 'list') url=CORE_STANDURL+'app=WORKSPACE&action=WS_FOLDERLIST&kview='+kview+'&order='+CORDER+'&dorder='+CDESCORDER+'&id='+n+'&key='+key;
  else url= CORE_STANDURL+'app=WORKSPACE&action=WS_FOLDERICON&kview='+kview+'&id='+n+'&key='+key;
  if (adddocid) url+="&addid="+adddocid+"&addft="+addft+"&paddid="+padddocid;
  requestUrlSend(cible,url);
  changedragft(null,'');
}


function emptytrash(event) {
  requestUrlSend(null,CORE_STANDURL+'app=WORKSPACE&action=WS_EMPTYTRASH');
}
function restoreDoc(event,docid) {
  requestUrlSend(null,CORE_STANDURL+'app=WORKSPACE&action=WS_RESTOREDOC&id='+docid)
}
function restoreFld(event,docid) {
  requestUrlSend(null,CORE_STANDURL+'app=WORKSPACE&action=WS_RESTOREDOC&containt=yes&id='+docid)
}
function deleteDoc(event,docid) {
  requestUrlSend(null,CORE_STANDURL+'app=WORKSPACE&action=WS_DELETEDOC&id='+docid+'&paddid='+CFLDID);
}
function copyDoc(event,docid) {
  requestUrlSend(null,CORE_STANDURL+'app=WORKSPACE&action=WS_COPYDOC&id='+docid+'&paddid='+CFLDID);
}

function addToBasket(event,docid) {

  folderSend(IDBASKET,false,docid,false,'shortcut');
}



// ----------------------------- view context memnu --------------------


function refreshClipBoard(bid,where) {
  CLIPCID=bid;
  folderSend(bid,where);
}

// ----------------------------- drag & drop --------------------
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
    DRAGGING=true;
    DRAGDOC=docid;
    PDRAGDOC=pdocid;
   
    if (typeof(osp)=='string')  MICON.innerHTML=osp;  
    else  MICON.innerHTML=osp.innerHTML;
    document.onmouseup=enddrag ;
    setTimeout('reallybegindrag()',200); // display dragging mode 200ms after

      
    //    POUL=oul;
    stopPropagation(event);

    return false;
  }
}


function reallybegindrag(event) {
  if (DRAGGING) {
    MICON.style.display='';
    DRAGFT=false;
    document.onmousemove=movedrag ;
    document.onkeydown=keydrag ;
    document.onkeyup=keydrag ;

    //    document.body.style.cursor='no-drop';
    globalcursor('no-drop');
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
       MICON.innerHTML=MICON.innerHTML+'<br><span id="miconft">COUOU</span>';
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
	  else  changedragft(event,'');
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
  if (! DRAGFT) unglobalcursor();
  MICON.style.display='none';
  sendEvent(e,"mouseup");
  
  
  
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
    if (DRAGFT) {
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
function viewSearch(event,key) {
  var  where=document.getElementById('fldlist');
 
  CFLDID=null;
  folderSend('search',where,null,null,null,'list',key);
}

function trackCR(event) {
  var intKeyCode;

  if (!event) event=window.event;
  intKeyCode=event.keyCode;
  if (intKeyCode == 13) return true;

  return false;
}

function changeOrder(event,norder) {
  if (CORDER == norder) CDESCORDER=(!CDESCORDER); // invert order
  else {
    CORDER=norder;
    CDESCORDER=true;
  }
  viewFolder(event,CFLDID);
}


function viewDoc(event,docid,o) {
  if (PREVVIEWDOCID==docid) return;
  PREVVIEWDOCID=docid;

  var  where=document.getElementById('iresume');
  if (o) {
    if (PREVTRSELECT) PREVTRSELECT.className='';    
    o.className='docselect';
    PREVTRSELECT=o;
  }

  where.src=CORE_STANDURL+'app=FDL&action=FDL_CARD&id='+docid;

  
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
  var menuurl=CORE_STANDURL+'app=WORKSPACE&action=WS_POPUPDOCFOLDER&id='+docid;
  viewmenu(event,menuurl,source);
}

function viewfoldermenu(event,docid,source) {
  var menuurl=CORE_STANDURL+'app=WORKSPACE&action=WS_POPUPLISTFOLDER&id='+docid;
  viewmenu(event,menuurl,source); 
}





function postActionRefresh(action,docid,c) {  
  switch (action) {
  case "ADDFOLDER":
  case "DELFOLDER":
    postAddFile(docid);
    postAddFolder(docid);
    break;
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
    endexpandtree(EXPANDIMG,EXPWHERE,1);
    EXPANDIMG=null;
    break;
  case "EMPTYBRANCH":
    endexpandtree(EXPANDIMG,EXPWHERE,0);
    EXPANDIMG=null;    
    break;
  case "LOCKFILE":
  case "UNLOCKFILE":    
   postLocking(docid);
        
    break;
  default:    
    // alert("UNKNOW:"+action+":"+docid);
  }
  //   alert("ACTION:"+action+":"+docid);
}
function endexpandtree(o,w,c) {
     if (o) {
       if (c==0) {
	 o.src='Images/none.png'; 
	 o.style.visibility='hidden';
	 // if (isIE) correctOnePNG(o);
       }
       else {
	 o.src='Images/b_down.png';
	 o.style.visibility='visible';
	 if (w) w.style.display='';
       }
     }
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
  
  
  SYNCHRO=false;
  
}
function  postAddFolder(docid) {
  var fldid;
  var img;
  SYNCHRO=true;
 
  if (CURSPACEID == docid) {
    
    if (CURSPACE && CURSPACE.getAttribute('onclick')) {
      CURSPACE.onclick.apply(CURSPACE,[]);	
    }
  } else {
    
    
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
  }
  SYNCHRO=false;
  
}

function postEmptyTrash(docid) {
  var fldid;
  var o;
  if ((CFLDID == 'trash')||(CFLDID == docid)) {
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
function cathtml(o1,o2) {
  if (o1 && o2) {
    return o1.innerHTML + o2.innerHTML;
  }

  return 'nothing in cat';
}

// onlyview : if true display always not undisplays
function clipviewornot(event,onlyview) {
  var dclipboard=document.getElementById('clipboard');
  var dfolders=document.getElementById('folders');
  var dsearches=document.getElementById('searches');
  var dtabclip=document.getElementById('tabclip');
  var imgbutton=document.getElementById('imgclipbutton');
  var ch=186;
  var fh;// height folder
  var sy,ty; // pos y for search

  if (isIE) ch +=7; // values from displayws.js
  if (dclipboard) {
    fh=parseInt(dfolders.style.height);
    sy=parseInt(dsearches.style.top);
    ty=parseInt(dtabclip.style.top);
    if (dclipboard.style.display=='none') {
      dclipboard.style.display='';

      dfolders.style.height=fh-ch;
      dtabclip.style.top=ty-ch;
      dsearches.style.top=sy-ch;
      imgbutton.src='Images/b_down.png';
      if (! CLIPCID) {
	refreshClipBoard(IDBASKET,dclipboard);
      }
    } else if (! onlyview) {
      dclipboard.style.display='none';

      dfolders.style.height=fh+ch;
      dtabclip.style.top=ty+ch;
      dsearches.style.top=sy+ch;
      imgbutton.src='Images/b_up.png';
    }
  }
}

addEvent(window,"load",clipviewornot);
