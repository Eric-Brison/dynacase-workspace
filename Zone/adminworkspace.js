

function ws_searchUsers(event,uname,where) {
  var corestandurl=window.location.pathname+'?sole=Y&';
  requestUrlSend(where,corestandurl+'app=WORKSPACE&action=WS_SEARCH&famid=IUSER&key='+uname+'&noids='+ws_implodeInputKeyValues('uchange'))
}

function ws_addUser(event,o,uid,uname) {
  var t=document.getElementById('trtemplate');
  var ntable=document.getElementById('members');
  var ntr;
  if (t) {
    t.style.display='';
    ntr=t.cloneNode(true);
    t.style.display='none';
    ntr.id='';
    ntr.style.display='';
    nodereplacestr(ntr,'jsuname',uname);
    nodereplacestr(ntr,'jsuid',uid);
    ntable.appendChild(ntr);
    if (o) o.style.display='none';
    //    alert(ntr.innerHTML);
  }
}

function ws_implodeInputKeyValues(n) {
  var ti= document.getElementsByTagName("input");    
  var tv = new Array();
  var ni,na;
  var pos;
	
  for (var i=0; i< ti.length; i++) { 
    na=ti[i].name;
    pos=na.indexOf('[');
    if (pos==-1) ni=na;
    else ni=na.substr(0,pos);

    if ((ni == n) && (na.substr(na.length-4,4) != '[-1]')) {
      if (ti[i].value != 'deleted')  tv.push(na.substr(pos+1,na.length-pos-2));
    }
  }
  return(tv.join('|'));
}


function ws_changeUProf(event,o,iduser) {
  var lo=o.options;
  var ischanged=true;
  for (var i=0;i< lo.length;i++) {
    if (lo[i].defaultSelected == lo[i].selected) ischanged=false;
  }


  var su='uchange['+iduser+']';
  if (ischanged) {
    o.form[su].value='change';  
    o.parentNode.parentNode.className='modified';
  } else {
    o.form[su].value='nochange';  
    o.parentNode.parentNode.className='';
    
  }
}
