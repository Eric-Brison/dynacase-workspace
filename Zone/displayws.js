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
  var ww=getFrameWidth();
  var wh=getFrameHeight();
  var dx=0;

  if (isIE && (window==top)) ww-=20; // vertical scroll bar always
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

  dx+=5;
  dcol2.style.width='200px';
  dcol2.style.top='0px';
  dcol2.style.left=dx;
  dcol2.style.height=wh-4; // no border

  dfolders.style.width='198px';
  dfolders.style.top=0;
  dfolders.style.left=0;
  dfolders.style.height=wh-250;

  dsearches.style.width='198px';
  dsearches.style.top=wh-240;
  dsearches.style.left=0;
  dsearches.style.height='30px';

  dtabclip.style.width='198px';
  dtabclip.style.top=wh-210;
  dtabclip.style.left=0;
  dtabclip.style.height='30px';

  dclipboard.style.width='198px';
  dclipboard.style.top=wh-193;
  dclipboard.style.left=0;
  dclipboard.style.height='186px';

  dx+=205;
  dcol3.style.width=ww-280;
  dcol3.style.top='0px';
  dcol3.style.left=dx;
  dcol3.style.height=wh-4; // 2 x border of 3px

  dfldlist.style.width=ww-290;
  dfldlist.style.top='0px';
  dfldlist.style.left=0;
  dfldlist.style.height=parseInt((wh-10)/2)-15; 

  dresume.style.width=ww-290;
  dresume.style.top=parseInt((wh-10)/2)-5; 
  dresume.style.left=0;
  dresume.style.height=parseInt((wh-10)/2)+7; 


}

addEvent(window,"load",redisplaywsdiv);
addEvent(window,"resize",redisplaywsdiv);
