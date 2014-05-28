function showResult(str)
{
if (str.length==0)
  {
  document.getElementById("familysearch").innerHTML="";
  document.getElementById("familysearch").style.border="0px";
  return;
  }
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("familysearch").innerHTML=xmlhttp.responseText;
    document.getElementById("familysearch").style.border="1px solid #A5ACB2";
    document.getElementById("familysearch").style.background="white";
    document.getElementById("familysearch").style.padding="5px";
    }
  }
xmlhttp.open("GET","ajax/familysearch.php?q="+str,true);
xmlhttp.send();
}