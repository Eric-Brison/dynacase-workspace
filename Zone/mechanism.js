
var inprogress=false;
var thecible=false;
var imgcible=false;
var req;


// ----------------------------- expand tree --------------------
function folderTreeSend(n,cible,adddocid) {
  if (inprogress) return false; // one request only
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

        if (adddocid) req.send("addid="+adddocid);
	else req.send(null);
	//var o=document.getElementById('err'+n);
	//	if (o) o.innerHTML="<img src=\"Images/progressbar.gif\"><blink>Executing...</blink>";
	//o=document.getElementById('easycr');
	//if (o) o.innerHTML="<img src=\"Images/progressbar.gif\"><br><blink>Executing "+n+"...</blink>";
	
	inprogress=true;
	thecible=cible;
	return true;
    }    
}

function folderTreeAdd() {
    // only if req shows "loaded"
  inprogress=false; 
  var o=thecible;
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
	      if (c > 0)       o.style.display='';
	      o.innerHTML=elt;
	      endexpandtree(imgcible,c);
	      if (! isNetscape) correctPNG();
	      
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

function viewfoldertree(img,fldid,where,adddocid) {
  if ((!img) ||  (where.childNodes.length==0)) {
    if (folderTreeSend(fldid,where,adddocid)) {
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
function clipboardSend(n,cible,adddocid) {
  if (inprogress) return false; // one request only
    // branch for native XMLHttpRequest object
    if (window.XMLHttpRequest) {
        req = new XMLHttpRequest(); 
    } else if (window.ActiveXObject) {
      // branch for IE/Windows ActiveX version
      isIE = true;
      req = new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (req) {
        req.onreadystatechange = clipboardView;
        req.open("POST", 'index.php?sole=Y&app=WORKSPACE&action=WS_FOLDERICON&id='+n, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 
        if (adddocid) req.send("addid="+adddocid);
	else req.send(null);
	//var o=document.getElementById('err'+n);
	//	if (o) o.innerHTML="<img src=\"Images/progressbar.gif\"><blink>Executing...</blink>";
	//o=document.getElementById('easycr');
	//if (o) o.innerHTML="<img src=\"Images/progressbar.gif\"><br><blink>Executing "+n+"...</blink>";
	
	inprogress=true;
	thecible=cible;
	return true;
    }    
}

function clipboardView() {
    // only if req shows "loaded"
  inprogress=false; 
  var o=thecible;
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

	     
	      if (code != 'OK') {
		alert('code not OK\n'+req.responseText);
		return;
	      }
	      elts = req.responseXML.getElementsByTagName("branch");
	      elt=elts[0].firstChild.nodeValue;
	      // alert(elt);
	      if (c > 0)       o.style.display='';
	      o.innerHTML=elt;
	      endexpandtree(imgcible,c);
	      if (! isNetscape) correctPNG();
	      
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

function refreshClipBoard(bid,where) {
  clipboardSend(bid,where);
}

// ----------------------------- drag & drop --------------------
var dragging=false;
var dragdoc=false;
var justdrag=false;

//var micon=new Image(100,100);
var micon=document.createElement("span");
var pe=null; // previous elt
micon.className='micon';
micon.style.display='none';

function begindrag(event,o,docid) {
  if (! event) event=window.event;
  if (! dragging) {
    GetXY(event);
    //micon=o;micon.style.position='absolute';
    micon.style.top = Ypos+2; 
    micon.style.left = Xpos+2; 
    micon.style.zIndex = 100;
    micon.style.display='';
    dragging=true;
    dragdoc=docid;
    //micon.src=o.src;
    
    micon.innerHTML=o.innerHTML;
    document.onmousemove=movedrag ;
    document.onmouseup=enddrag ;

    if (isIE) {
      // sendEvent(o,"mouseover");
      o.className='';
    }
    stopPropagation(event);
    return false;
  }
}


function movedrag(event) {
  if (dragging) {
    if (! event) event=window.event;
    GetXY(event);
    micon.style.top = Ypos+2; 
    micon.style.left = Xpos+2; 
    //stopPropagation(event)
    var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);

    var drop=e.getAttribute("droppable");
    if (drop == 'yes') {
      if (pe!=e) {	
	if (pe)   sendEvent(pe,"mouseout");
	sendEvent(e,"mouseover");
	//e.style.backgroundColor='yellow';
	//e.style.border='dashed';
	pe=e;
      }
    } else {
      
      //	e.style.backgroundColor='purple';
    }
    return false;
  }
}
function endjustdrag() {
  //justdrag=false;
}
function enddrag(event) {
  
  if (! event) event=window.event;
  document.onmousemove= "";
  document.onmouseup="" ;
  var e = (event.target) ? event.target : ((event.srcElement) ? event.srcElement : null);

  micon.style.display='none';
  sendEvent(e,"mouseup");

  
  
  dragging=false;
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
    if (dragging) {
      dragging=false;
      if (dragdoc>0) clipboardSend(bid,o,dragdoc);
    } 
}
function insertinfolder(event,o,oimg,docid,ulid) {
    if (! event) event=window.event;
    if ((dragging)&& (dragdoc!=docid)){
      document.getElementById(ulid).innerHTML='';
      expandtree(document.getElementById(oimg),docid,ulid,dragdoc);
      dragging=false;
    } 
}
function cancelEvent() {
            window.event.returnValue = false;
        }
