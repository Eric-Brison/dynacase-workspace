
var INPROGRESS=false;
var THECIBLE=false;
var imgcible=false;
var req;
var CURSPACE=false;

// ----------------------------- expand tree --------------------
function folderTreeSend(n,cible,adddocid,padddocid,addft) {
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
	      if (POUL && DRAGFT=='move') {		
		  var imgc=POUL.parentNode.childNodes[0];
		  if (!(imgc && imgc.onclick)) imgc=CURSPACE;
		  if (imgc && imgc.onclick) {
		    // reload branch parent branch
		    POUL.innerHTML='';
		    DRAGFT='';
		    imgc.onclick.apply(imgc,[]);
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

function viewfoldertree(img,fldid,where,adddocid,padddocid,addft) {
  if ((!img) ||  (where.childNodes.length==0)) {
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


//var micon=new Image(100,100);
var micon=document.createElement("span");
var PE=null; // previous elt
var PECTRL=null; // previous key
micon.className='micon';
micon.style.display='none';

function begindrag(event,oul,osp,docid,pdocid) {
  if (! event) event=window.event;
  if (! DRAGGING) {
    GetXY(event);
    
    micon.style.top = Ypos+2; 
    micon.style.left = Xpos+2; 
    micon.style.zIndex = 100;
    micon.style.display='';
    DRAGGING=true;
    DRAGDOC=docid;
    PDRAGDOC=pdocid;
    
    micon.innerHTML=osp.innerHTML;
    document.onmousemove=movedrag ;
    document.onmouseup=enddrag ;
    document.onkeydown=keydrag ;
    document.onkeyup=keydrag ;


    if (isIE) {
      // sendEvent(o,"mouseover");
      osp.className='';
    }
    POUL=oul;
    stopPropagation(event);
    return false;
  }
}


function movedrag(event) {
  if (DRAGGING) {
    if (! event) event=window.event;
    GetXY(event);
    micon.style.top = Ypos+2; 
    micon.style.left = Xpos+2; 
    //stopPropagation(event)
    var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);

    var drop=e.getAttribute("droppable");
    if (drop == 'yes') {
      if (PE!=e) {	
	if (PE)   sendEvent(PE,"mouseout");
	sendEvent(e,"mouseover");

	PE=e;
      }
    } else {
      
      //	e.style.backgroundColor='purple';
    }
    return false;
  }
}
function keydrag(event) {
  
    if (! event) event=window.event;
   
    var ctrl=event.ctrlKey;
    if (isNetscape) {
      // the ctrlKey   is not correct
      if (event.keyCode==17) {
	if (event.type == 'keyup') ctrl=false;
	else if (event.type == 'keydown') ctrl=true;
      }
    }
    window.status=event.keyCode + ':'+event.altKey+ ':'+event.ctrlKey;
    if (ctrl != PECTRL) {
      // redisplay emblem
      var li=micon.getElementsByTagName("img");
      var il=false;

      for (var i=0;i<li.length;i++) {
	if (li[i].className=="ilink") {
	  il=li[i];
	}
      }
      if (il) {
	if (ctrl) {
	  il.src='Images/plus.gif';
	} else {
	  il.src='Images/minus.gif';
	}
      }
      PECTRL=ctrl;
    }
}
function enddrag(event) {
  
  if (! event) event=window.event;
  document.onmousemove= "";
  document.onmouseup="" ;
  document.onkeyup="" ;
  document.onkeydown="" ;
  var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);

  micon.style.display='none';
  sendEvent(e,"mouseup");
  
  
  DRAGGING=false;
}

function copydrag(o,ulid,cdocid) {
  
}

function initDrag() {
  document.body.appendChild(micon);  
}

addEvent(window,"load",initDrag);

// ----------------------------- Insert ClipBoard --------------------
function insertinclipboard(event,o,bid) {

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
      if (DRAGDOC>0) folderSend(bid,o,DRAGDOC,PDRAGDOC,ft);
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
