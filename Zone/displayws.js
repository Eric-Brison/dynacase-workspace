function redisplaywsdiv(event) {
  var dcol1=document.getElementById('col1');
  var drule2=document.getElementById('rule2');
  var dcol2=document.getElementById('col2');
  var dcol3=document.getElementById('col3');
  var dfldlist=document.getElementById('fldlist');
  var dtabclip=document.getElementById('tabclip');
  var dresume=document.getElementById('resume');
  var dclipboard=document.getElementById('clipboard');
  var dfolders=document.getElementById('folders');
  var dsearches=document.getElementById('searches');
  var dtrash=document.getElementById('itrash');
  var dsecondul=document.getElementById('secondul');
  var dsecondview=document.getElementById('secondview');
  var ww=getFrameWidth();
  var wh=getFrameHeight();
  var dx=0;
  var ch=0;//current height

  if (isIE && (window==top)) ww-=18; // vertical scroll bar always
  if (isIE) ww+=12;
  //  if (isIE) wh-=20;
  //  alert(ww+' x '+wh);
  dcol1.style.width='60px';
  dcol1.style.top='0px';
  dcol1.style.left=dx;
  dcol1.style.height=wh-10; // 2 x border of 3px

  dx+=65;
  drule2.style.width='3px';
  drule2.style.top='0px';
  drule2.style.left=dx;
  drule2.style.height=wh-10; // 2 x border of 3px

  dx+=5; if (isIE) dx-=5;
  dcol2.style.width='200px';
  dcol2.style.top='0px';
  dcol2.style.left=dx;
  dcol2.style.height=wh-4; // no border

  dfolders.style.width='198px';
  dfolders.style.top=0;
  dfolders.style.left=0;
  dfolders.style.height=wh-250;

  ch=wh-240;if (isIE) ch -=3;
  dsearches.style.width='198px';
  dsearches.style.top=ch;
  dsearches.style.left=(isIE)?1:2;
  dsearches.style.height='30px';

  ch+=30;if (isIE) ch -=3;
  dtabclip.style.width='198px';
  dtabclip.style.top=ch;
  dtabclip.style.left=0;
  dtabclip.style.height='19px';

  ch+=16;if (isIE) ch -=3;
  dsecondview.style.width='198px';
  dsecondview.style.top=ch;
  dsecondview.style.left=0;
  ch=184;if (isIE) ch +=7;
  dsecondview.style.height=ch;

  /*  dsecondul.style.top=dclipboard.style.top;
  dsecondul.style.left=dclipboard.style.left;
  dsecondul.style.width=dclipboard.style.width;
  dsecondul.style.height=dclipboard.style.height;

  dsecondview.style.top=dclipboard.style.top;
  dsecondview.style.left=dclipboard.style.left;
  dsecondview.style.width=dclipboard.style.width;
  dsecondview.style.height=dclipboard.style.height;*/


  dx+=202; if (isIE) dx-=5;
  dcol3.style.width=ww-280;
  dcol3.style.top='0px';
  dcol3.style.left=dx;
  dcol3.style.height=wh-4; // 2 x border of 3px

  dfldlist.style.width=ww-283;
  dfldlist.style.top='0px';
  dfldlist.style.left=0;
  dfldlist.style.height=parseInt((wh-10)/2)-15; 

  if (dsecondview.style.display=='none') {
    // adapt size of clipboard in case of resize
    var sy,ty,fh;
    fh=parseInt(dfolders.style.height);
    sy=parseInt(dsearches.style.top);
    ty=parseInt(dtabclip.style.top);
      dfolders.style.height=fh+ch;
      dtabclip.style.top=ty+ch;
      dsearches.style.top=sy+ch;
  }


  ch=0; if (isIE) ch=6;
  if (isIE) dresume.style.borderStyle='none';
  dresume.style.width=ww-283;
  dresume.style.top=parseInt((wh-10)/2)-7-ch; 
  dresume.style.left=0;
  ch=0; if (isIE) ch=6;
  dresume.style.height=parseInt((wh-10)/2)+8+ch;

  dtrash.style.position='absolute';
  dtrash.style.top=wh-100;
  
}
// onlyview : if true display always not undisplays
function clipviewornot(event,onlyview) {
  var dsecondul=document.getElementById('secondview');
  var dclipboard=document.getElementById('clipboard');
  var dfolders=document.getElementById('folders');
  var dsearches=document.getElementById('searches');
  var dtabclip=document.getElementById('tabclip');
  var imgbutton=document.getElementById('imgclipbutton');
  var ch=186;
  var fh;// height folder
  var sy,ty; // pos y for search

  if (isIE) ch +=7; // values from displayws.js
  if (dsecondul) {
    fh=parseInt(dfolders.style.height);
    sy=parseInt(dsearches.style.top);
    ty=parseInt(dtabclip.style.top);
    if (dsecondul.style.display=='none') {
      dsecondul.style.display='';

      dfolders.style.height=fh-ch;
      dtabclip.style.top=ty-ch;
      dsearches.style.top=sy-ch;
      imgbutton.src='Images/b_down.png';
      if (! CLIPCID) {
	refreshClipBoard(IDBASKET,dclipboard);
      }
    } else if (! onlyview) {
      dsecondul.style.display='none';

      dfolders.style.height=fh+ch;
      dtabclip.style.top=ty+ch;
      dsearches.style.top=sy+ch;
      imgbutton.src='Images/b_up.png';
    }
  }
}
function displayprivate(event) {
  var p=document.getElementById('tab_privatespace');
  if (p) p.onclick.apply(p,[event]);
}
addEvent(window,"load",redisplaywsdiv);
addEvent(window,"load",displayprivate);
//addEvent(window,"load",clipviewornot);
addEvent(window,"resize",redisplaywsdiv);
