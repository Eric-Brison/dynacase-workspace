
function viewinline(event,url,toview,tocache) {
  var ov=document.getElementById(toview);
  var oc=document.getElementById(tocache);
  var obrfull=document.getElementById('brfull');
  if (ov && oc) {

    if (obrfull.style.display=='none') obrfull.style.display='';
    else obrfull.style.display='none';
    if  (  (ov.src != url)   )  {
      ov.src=url;
    }
    oc.style.display='none';
    ov.style.display='';
    resizeIurl(ov.id);
  }
}
function resizeIurl(iurl) {
var eiurl=document.getElementById(iurl);
var xy=getAnchorPosition(iurl);
var hiurl=getFrameHeight();
var wiurl=getFrameWidth();
var nh=hiurl - xy.y - 22; // offset for scrollbar
// alert(xy.y+'--'+hiurl+'--'+nh);

eiurl.height=nh;
eiurl.width=wiurl-10;
}
