function redisplaywsdiv() {
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
  dsearches.style.left=0;
  dsearches.style.height='30px';

  ch+=30;if (isIE) ch -=3;
  dtabclip.style.width='198px';
  dtabclip.style.top=ch;
  dtabclip.style.left=0;
   dtabclip.style.height='30px';

   ch+=19;if (isIE) ch -=3;
  dclipboard.style.width='198px';
  dclipboard.style.top=ch;
  dclipboard.style.left=0;
  ch=186;if (isIE) ch +=7;
  dclipboard.style.height=ch;

  dx+=202; if (isIE) dx-=5;
  dcol3.style.width=ww-280;
  dcol3.style.top='0px';
  dcol3.style.left=dx;
  dcol3.style.height=wh-4; // 2 x border of 3px

  dfldlist.style.width=ww-283;
  dfldlist.style.top='0px';
  dfldlist.style.left=0;
  dfldlist.style.height=parseInt((wh-10)/2)-15; 


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

addEvent(window,"load",redisplaywsdiv);
addEvent(window,"resize",redisplaywsdiv);
