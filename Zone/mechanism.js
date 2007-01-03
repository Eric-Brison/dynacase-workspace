
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
var DRAGNOMOVE=false; // to not move document : in case of dynamic folder -> only link or copy


var THECIBLE=false; // object where insert HTML code
var INPROGRESS=false;
var EXPANDIMG=false;
var EXPWHERE=false; // use for expand tree
var req;
var CURSPACE=false; // current selected space object
var CURSPACEID=false; // current selected space id
var CFLDID=false; // current folder doc id
var RCFLDID=false; // real id  (get by the server) of current folder doc id
var CLIPCID=false; // current folder for clipboard
var REFRESH=false; // to indicate the the state is for resfresh one part
var PREVVIEWDOCID=false; // to don't do twice teh same action
var CORE_STANDURL=window.location.pathname+'?sole=Y&';
var ALWAYSEXPAND=false; // set to true if you do not want the tree can be collapsed

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
    EXPWHERE=where;
    EXPANDIMG=img;
    if (folderTreeSend(fldid,where,adddocid,padddocid,addft)) {
      if (SYNCHRO) return 1;
      else return 2;
    }
    else return -1;
  } else {
    if (where.style.display=='none') {
      where.style.display='';
      return 1;
    } else {
      if (ALWAYSEXPAND) {
	return 1;
      }
      where.style.display='none';
      return 0;
    }
  } 
}

function expandtree(oimg,id,ulid,adddocid,padddocid,addft,reset) {
  var r=viewfoldertree(oimg,id,document.getElementById(ulid),adddocid,padddocid,addft,reset);
  if (r==1) {
    oimg.src='Images/b_down.png'; 
  } else if (r==0) {
    oimg.src='Images/b_right.png';
  } else if (r==2) {
    oimg.src='Images/b_wait.png';
  }
  if (isIE6) correctOnePNG(oimg);
}
function expandToptree(o,id,ulid) {
  enableSynchro();
  viewFolder(null,id);
  disableSynchro();
  if (o) { 
    var ldiv=o.parentNode.getElementsByTagName("div");
    for (var i=0;i<ldiv.length;i++) {
      if (ldiv[i].className=='spaceselect') ldiv[i].className='space';
    }
    o.className="spaceselect";
    CURSPACE=o;
    CURSPACEID=id;
  }
  var r=viewfoldertree(null,id,ulid);
  if (r==2) {
    ulid.innerHTML='<table style="width:100%;height:80%"><tr><td align="center"><img style="width:30px" src="Images/loading.gif"></tr></td></table>';    
  }
}

function expandPersoTree(id,where,reset) {
  var dclipboard=document.getElementById('clipboard');
  
  if (dclipboard) dclipboard.style.display='none';
  where.style.display='';
  if ((where.firstChild.tagName=='TABLE') || reset) {    
    var url;
    url= CORE_STANDURL+'app=WORKSPACE&action=WS_ADDFLDBRANCH&itself=Y&id='+id; 
    var ret=requestUrlSend(where,url);
    changedragft(null,'');
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
function sendRenameFolderTree(docid) {
  requestUrlSend(null,CORE_STANDURL+'app=WORKSPACE&action=WS_COUNTFOLDER&id='+docid);
}



// ----------------------------- view context memnu --------------------


function refreshClipBoard(bid,where) {
  CLIPCID=bid;
  folderSend(bid,where);
}
function displayClipboard(bid,where) {
  var dsecondul=document.getElementById('secondul');

  if (dsecondul) dsecondul.style.display='none';
  where.style.display='';
  if (bid != CLIPCID)  refreshClipBoard(bid,where);
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
   
    document.onmousemove=waitdrag ;
    if (typeof(osp)=='string')  MICON.innerHTML=osp;  
    else  MICON.innerHTML=osp.innerHTML;
    document.onmouseup=enddrag ;
    setTimeout('reallybegindrag()',100); // display dragging mode 200ms after

      
    //    POUL=oul;
    stopPropagation(event);

    return false;
  }
}


function reallybegindrag(event) {
  if (DRAGGING) {
    MICON.style.display='';
    DRAGFT=false;
    document.onkeydown=keydrag ;
    document.onkeyup=keydrag ;
    document.onmousemove=movedrag ;

    //    document.body.style.cursor='no-drop';
    globalcursor('no-drop');
  }
}

var DEBUG=0;
function overdragft(event,o) {  
  if (DRAGGING && DRAGDOC) {
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
	if (ft) {
	  if ((ft=='move') && DRAGNOMOVE) ft='link';
	  dft=ft;
	}
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
    switch (nft) {
    case 'copy':
      oft.innerHTML='copier';   
      break;
    case 'link':
      oft.innerHTML='lier';   
      break;
    case 'shortcut':
      oft.innerHTML='raccourci';   
      break;
    case 'move':
      oft.innerHTML='d&eacute;placer';   
      break;
    case 'del':
      oft.innerHTML='supprimer';   
      break;
    default:
      oft.innerHTML=nft;  
    }
    DRAGFT=nft;
  }
}
function waitdrag(event) {
    return false;
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
      if (DRAGFT) {
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
  
  DRAGDOC=false;
  DRAGNOMOVE=false;
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

/**
 * open and expand branch
 */
function openFolder(event,dirid) {
  var  fld=document.getElementById('folders'); // list of folders tree
  var lis=fld.getElementsByTagName('img');
  var i,spano;
  var docid;
  var pcfldid=CFLDID;
  enableSynchro();  
  globalcursor('wait');
  ALWAYSEXPAND=true;
  for (i=0;i<lis.length;i++) {
    docid=lis[i].getAttribute('docid');
    if ((docid == dirid)||(docid == pcfldid)) {
      lis[i].onclick.apply(lis[i],[]);
      //alert('openFolder '+docid);
    }
    if ((docid == dirid)) {
      lis[i].onclick.apply(lis[i],[]);
      spano=lis[i].nextSibling;
      while (spano && (spano.nodeType != 1)) spano = spano.nextSibling; //case TEXT node
      //      alert('openFolder '+spano);
    }
  }
  viewFolder(event,dirid,spano);
  disableSynchro();
  unglobalcursor();
  ALWAYSEXPAND=false;
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
  viewFolder(event,RCFLDID);
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


function viewdetailmenu(event,docid,dirid,source) {
  var menuurl=CORE_STANDURL+'app=WORKSPACE&action=WS_POPUPDOCFOLDER&id='+docid+'&dirid='+dirid;
  viewmenu(event,menuurl,source);
}

function viewfoldermenu(event,docid,source) {
  var menuurl=CORE_STANDURL+'app=WORKSPACE&action=WS_POPUPLISTFOLDER&id='+docid;
  viewmenu(event,menuurl,source); 
}





function postActionRefresh(action,arg) {
  var docid=arg;

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
  case "UNTRASHFILE":
    postTrashFile(docid,true);        
    break;
  case "ADDBRANCH":
    endexpandtree(EXPANDIMG,EXPWHERE,1);
    EXPANDIMG=null;
    break;
  case "RENAMEBRANCH":
    var targ=string2Array(arg);
    docid=targ[0];
    var title=targ[1];
    renamebranch(docid,title);
    //alert('arg:'+arg+'\nid:'+docid+' ,new title:'+title);
    break;
  case "EMPTYBRANCH":
    endexpandtree(EXPANDIMG,EXPWHERE,0);
    EXPANDIMG=null;    
    break;
  case "LOCKFILE":
  case "UNLOCKFILE":    
   postLocking(docid);
        
    break;
  case "GETRDOCID":
   RCFLDID=docid;
        
    break;
  case "IMGRESIZE":
    resizeImages();
        
    break;
  default:    
    // alert("UNKNOW:"+action+":"+docid);
  }
  // alert("ACTION:"+action+":"+docid);
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
  enableSynchro();
  if (CFLDID == docid) {
    viewFolder(null,CFLDID)
  }
  if (CLIPCID == docid) {
    refreshClipBoard(CLIPCID,document.getElementById('clipboard'))
  }
  
  if (IDBASKET == docid)   sendRenameFolderTree(docid); 
  disableSynchro();
  
}
function  postAddFolder(docid) {
  var fldid;
  var img;
  enableSynchro();
 
  if (CURSPACEID == docid) {
    
    if (CURSPACE && CURSPACE.getAttribute('onclick')) {
      CURSPACE.onclick.apply(CURSPACE,[]);	
    }
  } else {
    
    
  for (var i=0; i<document.images.length; i++)   {
    img=document.images[i];
    fldid=img.getAttribute("docid");
    if (docid==fldid) {
      //img.style.border='solid 2px green';
      if (img.getAttribute('ondblclick')) {
	//	expandtree(this,'[id]','[ulid][id]',null,null,null,true)
	img.ondblclick.apply(img,[]);
	if (EXPWHERE) {
	  EXPWHERE.style.display='';
	  EXPWHERE=null;
	}
      }      
    }
  }
  }
  disableSynchro();
  
}

function  renamebranch(docid,ntitle) {
  var fldid;
  var img;
  var ttext;
  var otext;
 
    
  for (var i=0; i<document.images.length; i++)   {
    img=document.images[i];
    fldid=img.getAttribute("docid");
    if (docid==fldid) {
      ttext=img.parentNode.getElementsByTagName('span');
       otext=ttext[0];
       var lc=otext.childNodes;
       for (var j=0;j<lc.length;j++) {
	 if (lc[j].nodeType == 3) lc[j].data=ntitle;
       }
       
       //      otext.style.backgroundColor='yellow';
    }
  }
  
  
}
function postEmptyTrash(docid) {
  var fldid;
  var o;
  if ((CFLDID == 'trash')||(CFLDID == docid)) {
    viewFolder(null,CFLDID)
  }
  o=document.getElementById('trashicon');
  if (o) o.src='Images/trashempty.png';   
  sendRenameFolderTree('WS_MYTRASH'); 
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
  sendRenameFolderTree('WS_MYLOCKEDFILE');
}


function postTrashFile(docid,untrash) {
  var fldid;
  var o;
  enableSynchro();
  if ((CFLDID == 'trash')||(CFLDID == docid)) {
    viewFolder(null,CFLDID)
  }
  if (! untrash) {
    o=document.getElementById('trashicon');
    if (o) o.src='Images/trash.png';    
  }
  sendRenameFolderTree('WS_MYTRASH'); 
  disableSynchro();;
}

function receiptActionNotification(code,arg) {
  enableSynchro();
  for (var i=0;i<code.length;i++) {
    //  alert(code[i]);
    postActionRefresh(code[i],arg[i]);
  }
  disableSynchro();  
}

function altern_basket_private(event,o) {
  var bo=document.getElementById('clipboard');

  var tp=document.getElementById('tab_privatespace');
  var tb=document.getElementById('tab_basket');
  var ti=o.getElementsByTagName('img');
  var i=ti[0];

  if (bo.style.display=='none') {
    // display clip
    tb.onclick.apply(tb,[]);
    i.src='Images/iconview.gif';
  } else {
    tp.onclick.apply(tp,[]);   
    i.src='Images/treeview.gif';
  }    
}

function cathtml(o1,o2) {
  if (o1 && o2) {
    return o1.innerHTML + o2.innerHTML;
  }

  return 'nothing in cat';
}

function string2Array(string) {
  eval('var result = ' + string);
  return result;
}
// ------------- RESIZE DIV ------------
//----- COL3 ----------
var COL3H1=-1;
function col3dragbegin(event) {
  if (! event) event=window.event;
  if (! DRAGGING) {
   
    var cacheres=document.getElementById('cacheresume');
    var res=document.getElementById('resume');
    document.onmousemove=col3dragmove ;
  
    document.onmouseup=col3dragend ;      
    stopPropagation(event);
    DRAGGING=true;
    col3dragmove(event);
    cacheres.style.top=res.style.top;
    cacheres.style.width=res.style.width;
    cacheres.style.left=res.style.left;
    cacheres.style.height=res.style.height;
    cacheres.style.display='';
  }
  return false;  
}

function col3dragmove(event) {
  if (DRAGGING) {
    var bscroll3=document.getElementById('bscroll3');
    bscroll3.className='viewvscroll';
    var delta=2;
    if (! event) event=window.event;
    GetXY(event);
    delta=2;
    bscroll3.style.top=Ypos-delta;
    //col3resize(Ypos-delta);   
    stopPropagation(event);
    return false;
  }
}

function col3dragend(event) {  
  if (! event) event=window.event;
  var bscroll3=document.getElementById('bscroll3');
  bscroll3.className='bvresize';
  document.onmousemove= "";
  document.onmouseup="" ;
  GetXY(event);
  COL3H1=Ypos-2;
  redisplaywsdiv(event);
  DRAGGING=false;
  unglobalcursor();

  var cacheres=document.getElementById('cacheresume');
  cacheres.style.display='none';
  setparamu('WORKSPACE','WS_COL3H1',COL3H1);
}

//----- COL2 ----------
var COL2H1=-1;
function col2dragbegin(event) {
  if (! event) event=window.event;
  if (! DRAGGING) {
   
    var bscroll2=document.getElementById('bscroll2');
    bscroll2.className='viewvscroll';
    document.onmousemove=col2dragmove ;  
    document.onmouseup=col2dragend ;      
    stopPropagation(event);
    DRAGGING=true;
    col2dragmove(event);
    
  }
  return false;  
}

function col2dragmove(event) {
  if (DRAGGING) {
    var bscroll2=document.getElementById('bscroll2');
    var delta=2;
    if (! event) event=window.event;
    GetXY(event);
    delta=2;
    bscroll2.style.top=Ypos-delta;
    //col2resize(Ypos-delta);   
    stopPropagation(event);
    return false;
  }
}

function col2dragend(event) {  
  if (! event) event=window.event;
  var bscroll2=document.getElementById('bscroll2');
  bscroll2.className='bvresize';
  document.onmousemove= "";
  document.onmouseup="" ;
  GetXY(event);
  COL2H1=Ypos-2;
  redisplaywsdiv(event);
  DRAGGING=false;
  unglobalcursor();
  setparamu('WORKSPACE','WS_COL2H1',COL2H1);
}
//----- ROW2 ----------
var ROW2H1=-1;
function row2dragbegin(event) {
  if (! event) event=window.event;
  if (! DRAGGING) {
   
  var bscroll1=document.getElementById('bscroll1');
  bscroll1.className='viewhscroll';
    var cacheres=document.getElementById('cacheresume');
    var res=document.getElementById('resume');
    document.onmousemove=row2dragmove ;
  
    document.onmouseup=row2dragend ;      
    stopPropagation(event);
    DRAGGING=true;
    row2dragmove(event);
    cacheres.style.top=res.style.top;
    cacheres.style.width=res.style.width;
    cacheres.style.left=res.style.left;
    cacheres.style.height=res.style.height;
    cacheres.style.display='';
  }
  return false;  
}

function row2dragmove(event) {
  if (DRAGGING) {
    var bscroll1=document.getElementById('bscroll1');
    var delta=2;
    if (! event) event=window.event;
    GetXY(event);
    delta=2;
    bscroll1.style.left=Xpos-delta;
    //row2resize(Xpos-delta);   
    stopPropagation(event);
    return false;
  }
}

function row2dragend(event) {  
  if (! event) event=window.event;
  var bscroll1=document.getElementById('bscroll1');
  bscroll1.className='bhresize';
  document.onmousemove= "";
  document.onmouseup="" ;
  GetXY(event);
  ROW2W1=Xpos-2;
  redisplaywsdiv(event);
  DRAGGING=false;
  unglobalcursor();
  var cacheres=document.getElementById('cacheresume');
    cacheres.style.display='none';
  setparamu('WORKSPACE','WS_ROW2W1',ROW2W1);
}



