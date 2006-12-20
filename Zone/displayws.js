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
  var bscroll1=document.getElementById('bscroll1');
  var bscroll2=document.getElementById('bscroll2');
  var bscroll3=document.getElementById('bscroll3');
  var ww=getFrameWidth();
  var wh=getFrameHeight();
  var dx=0;
  var ch=0;//current height
  var w2,w3; // width of col2 col3

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

  if (COL2H1 && (COL2H1>0)) ch=COL2H1;
  else ch=wh-250;
  if (ROW2W1 && (ROW2W1>0)) {
    if (ROW2W1<55) ROW2W1=55;
    wcol2=ROW2W1-parseInt(dcol1.style.width);
    wcol3=ww-ROW2W1-30;
  } else {
    wcol2=198;
    wcol3=ww-280;
  }
  if (wcol2 < 50) wcol2=50;
  dx+=7; if (isIE) dx-=7;
  dcol2.style.width=wcol2+2;
  dcol2.style.top='0px';
  dcol2.style.left=dx;
  dcol2.style.height=wh-4; // no border

  dfolders.style.width=wcol2;
  dfolders.style.top=0;
  dfolders.style.left=0;
  dfolders.style.height=ch;

  if (isIE) ch -=3;

  bscroll2.style.width=wcol2;
  bscroll2.style.height=4;
  bscroll2.style.top=ch+6;
  bscroll2.style.left=0;

  ch+=8;
  dsearches.style.width=wcol2;  
  dsearches.style.top=ch+4; 
  dsearches.style.left=(isIE)?1:2;
  dsearches.style.height='30px';

  ch+=30;if (isIE) ch -=3;
  dtabclip.style.width=wcol2;
  dtabclip.style.top=ch;
  dtabclip.style.left=0;
  dtabclip.style.height='19px';

  ch+=16;if (isIE) ch -=0;
  dsecondview.style.width=wcol2;
  dsecondview.style.top=ch;
  dsecondview.style.left=0;
  ch=wh-ch-10;if (isIE) ch +=0;
  dsecondview.style.height=ch;

  /*  dsecondul.style.top=dclipboard.style.top;
  dsecondul.style.left=dclipboard.style.left;
  dsecondul.style.width=dclipboard.style.width;
  dsecondul.style.height=dclipboard.style.height;

  dsecondview.style.top=dclipboard.style.top;
  dsecondview.style.left=dclipboard.style.left;
  dsecondview.style.width=dclipboard.style.width;
  dsecondview.style.height=dclipboard.style.height;*/

  dx+=202; 
  if (ROW2W1 && (ROW2W1>0)) dx=ROW2W1+16;

  if (dx < 50) dx=50;
  if (isIE) dx-=12;

  bscroll1.style.width=4;
  bscroll1.style.height=wh-10;
  bscroll1.style.top=0;
  bscroll1.style.left=dx;

  if (isIE) wcol3-=14;
  dcol3.style.width=wcol3
  dcol3.style.top='0px';
  dcol3.style.left=dx;
  dcol3.style.height=wh-4; // 2 x border of 3px

  

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


  dfldlist.style.width=wcol3+3;
  dfldlist.style.top='0px';
  dfldlist.style.left=0;
  if (COL3H1 && (COL3H1>0)) ch=COL3H1;
  else ch=parseInt((wh-10)/2)-15;
  dfldlist.style.height=ch; 

  ch+=8;
  if (isIE) ch-=4;
  bscroll3.style.width=wcol3+3;
  bscroll3.style.height=4;
  bscroll3.style.top=ch;
  bscroll3.style.left=0;

  if (isIE) dresume.style.borderStyle='none';
  dresume.style.width=wcol3+3;
  dresume.style.top=ch; 
  dresume.style.left=0;
  
  if (isIE) ch+=0;
  dresume.style.height=wh-ch-10;

  dtrash.style.position='absolute';
  dtrash.style.top=wh-100;
  
}
// h new h size of dfldlist
function col3resize(h) {
  var dfldlist=document.getElementById('fldlist');
  var dresume=document.getElementById('resume');
  var wh=getFrameHeight();
  var delta=6;
  h-=10;

  dfldlist.style.height=h;

  dresume.style.top=h+10+4+delta; 
  dresume.style.height=parseInt(wh-h-30-delta);
}
// w new w size of 
function row2resize(w) {
  var col2=document.getElementById('col2');
  var dresume=document.getElementById('resume');
  var ww=getFrameWidth();
  var delta=6;
  h-=10;

  
}
// h new h size of folders
function col2resize(h) {
  var dfolders=document.getElementById('folders');
  var dsearches=document.getElementById('searches');
  var dsecondview=document.getElementById('secondview');
  var dtabclip=document.getElementById('tabclip');
  var wh=getFrameHeight();
  var delta=10;

  if (isIE) delta=8;

  dfolders.style.height=h;

  h+=delta;
  dsearches.style.top=h;
  h+=30;
  dtabclip.style.top=h;
  h+=16;
  dsecondview.style.top=h;
  dsecondview.style.height=parseInt(wh-h-delta);
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
