


var INPROGRESS=false;
var THECIBLE=false;
var imgcible=false;
var req;
var CURSPACE=false;
var IDCFLDID=false; // current folder doc id
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
        req.onreadystatechange = folderTreeAdd;
        req.open("POST", 'index.php?sole=Y&app=WORKSPACE&action=WS_ADDFLDBRANCH&id='+n, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 

       
        if (adddocid) req.send("addid="+adddocid+"&addft="+addft+"&paddid="+padddocid);
	else req.send(null);
	
	INPROGRESS=true;
	THECIBLE=cible;
	return true;
    }    
}

function folderTreeAdd() {
  INPROGRESS=false; 
  var o=THECIBLE;
 
  if (req.readyState == 4) {
    // only if "OK"
    //dump('readyState\n');
    if (req.status == 200) {
      // ...processing statements go here...
      //  alert(req.responseText);
      if (req.responseXML) {
	var elts = req.responseXML.getElementsByTagName("status");
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
	  elts = req.responseXML.getElementsByTagName("branch");
	  elt=elts[0].firstChild.nodeValue;
	  // alert(elt);
	  if (o) {
	    if (c > 0)       o.style.display='';
	    o.innerHTML=elt;
	  }
	  endexpandtree(imgcible,c);
	  if (! isNetscape) correctPNG();
	  //dump('\tDRAGFT:'+DRAGFT+'\n');
	  if (POUL && DRAGFT=='move') {		
	    //	    alert(POUL.tagName);
	      // reload branch parent branch
	    DRAGFT='';
	    //dump('\tPOUL detected\n');
	    if (POUL.getAttribute('ondblclick')) {
	      POUL.ondblclick.apply(POUL,[]);	    
	      //dump('\tondblclick apply\n');
	    }

	  } 
	} else {
	  alert('no status\n'+req.responseText);
	  return;
	}
      } else {
	alert('no xml\n'+req.responseText);
	return;
      } 	  
    } else {
      alert("There was a problem retrieving the XML data:\n" +
	    req.statusText);
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
      imgcible=img;
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
      if (! kview) kview='icon';
        req.onreadystatechange = folderTreeAdd ;
	if (kview == 'list') req.open("POST", 'index.php?sole=Y&app=WORKSPACE&action=WS_FOLDERLIST&kview='+kview+'&id='+n, true);
        else req.open("POST", 'index.php?sole=Y&app=WORKSPACE&action=WS_FOLDERICON&kview='+kview+'&id='+n, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 
	THECIBLE=cible;

        if (adddocid) req.send("addid="+adddocid+"&addft="+addft+"&paddid="+padddocid);
	else req.send(null);
	
	
	INPROGRESS=true;	
	clipboardWait(cible);
	return true;
    }    
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
        req.onreadystatechange = folderTreeAdd ;
        req.open("POST", 'index.php?sole=Y&app=WORKSPACE&action=WS_VIEWDOC&id='+docid, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 
	THECIBLE=cible;


	req.send(null);
	
	
	INPROGRESS=true;	
	clipboardWait(cible);
	return true;
    }    
}

function refreshClipBoard(bid,where) {
  folderSend(bid,where);
}

// ----------------------------- drag & drop --------------------
var DRAGGING=false;
var DRAGDOC=false;// current document id being dragged
var PDRAGDOC=false; // current folder id of DRAGDOC
var POUL=false; // current ul id object is being dragged
var DRAGFT=false;


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
    DRAGDOC=docid;
    PDRAGDOC=pdocid;
    
    MICON.innerHTML=osp.innerHTML;
    document.onmousemove=movedrag ;
    document.onmouseup=enddrag ;
    document.onkeydown=keydrag ;
    document.onkeyup=keydrag ;
    document.body.style.cursor='move';

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
      o.style.border='red 1px solid';
      
      if (isIE && (! IEPARASITE)) {
	IEPARASITE=o;
	IEPARASITE.style.border='orange 3px solid';
      }
      var dft=DRAGFT;
      CDROPZ=o;
      if (!dft) {
	var ft=o.getAttribute("dropft");
	if (ft) dft=ft;
      }
      changedragft(event,dft);  
    } else {      
      if (oft) oft.innerHTML='';
      o.style.border='orange 1px solid';

    }
  } else {    
      o.style.border='green 1px solid';
  }
  //   window.status='overdragft'+DRAGFT +'idrag:'+DEBUG+'PE:'+PECTRL+'drop:'+drop;
}

function outdragft(event,o) {  
  if (DRAGGING) {
    if (IEPARASITE == o) return;
    var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);
    var oft=document.getElementById('miconft');
    if (oft) oft.innerHTML='';
    if (PECTRL== 0) DRAGFT=false;
    //        window.status=DRAGFT +'PE:'+PECTRL;
    CDROPZ=null;
      o.style.border='blue 1px solid';
      if (e == PEDROP) PEDROP=false;
  }
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
  
  document.body.style.cursor='auto';
  
  DRAGGING=false;
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
	if (DRAGDOC>0) folderSend(bid,o,DRAGDOC,PDRAGDOC,DRAGFT,kview);
      }
    } 
}

function insertinspace(event,o,sid) {

    if (! event) event=window.event;
    if (DRAGGING) {
      var ft='move';
      var ctrlKey = event.ctrlKey;
      if (ctrlKey) ft='add';
      DRAGFT=ft;
      if (ft == 'move') {
	//
      }
      DRAGGING=false;
      if (DRAGDOC>0) folderSend(sid,false,DRAGDOC,PDRAGDOC,ft);
    } 
}
function insertinfolder(event,o,oimg,docid,ulid) {
  var ft='move';
    if (! event) event=window.event;
    var ctrlKey = event.ctrlKey;
    if (ctrlKey) ft='add';
    if ((DRAGGING)&& (PDRAGDOC!=docid)&& (DRAGDOC!=docid)){
      document.getElementById(ulid).innerHTML='';
      DRAGFT=ft;
      expandtree(document.getElementById(oimg),docid,ulid,DRAGDOC,PDRAGDOC,ft);
      DRAGGING=false;
    } 
}
function viewFolder(event,dirid) {
  var  where=document.getElementById('fldlist');
  IDCFLDID=dirid;
  folderSend(dirid,where,null,null,null,'list');
  
}

function viewDoc_(event,docid) {
  var  where=document.getElementById('resume');

  documentSend(docid,where);
  
}

function viewDoc(event,docid) {
  var  where=document.getElementById('iresume');

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
