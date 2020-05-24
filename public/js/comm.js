function com_show_sub_tree(obj){
	eParent = obj.parentNode;
	var x=eParent.getElementsByTagName("ul");
	if(x[0].style.display=="none"){
		x[0].style.display="block";
		obj.getElementsByTagName("span")[0].innerHTML="-";
	}
	else{
		x[0].style.display="none";
		obj.getElementsByTagName("span")[0].innerHTML="+";
	}
	
}

//check if the next sibling node is an element node
function com_get_nextsibling(n)
{
  var x=n.nextSibling;
  if(x){
  while (x.nodeType!=1)
   {
   x=x.nextSibling;
   }
}
  return x;
}

function com_guid(trim = true,hyphen= false) {//guid生成器
	if(trim){
		if(hyphen){
			var tmp='xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';
		}
		else{
			var tmp='xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
		}
	}
	else{
		if(hyphen){
			var tmp='{xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx}';
		}
		else{
			var tmp='{xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx}';
		}
	}
	
	var guid=tmp.replace(/[xy]/g, function(c) {
        var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
    });
    return guid.toUpperCase();
}

function com_xmlToString(elem){
	var serialized;
	try{
		serializer = new XMLSerializer();
		serialized = serializer.serializeToString(elem);
	}
	catch(e){
		serialized = elem.xml;
	}
	return(serialized);
}

function com_getPaliReal(inStr){
	var paliletter="abcdefghijklmnoprstuvyāīūṅñṭḍṇḷṃ";
	var output="";
	inStr = inStr.toLowerCase();
	inStr = inStr.replace(/ṁ/g,"ṃ");
	inStr = inStr.replace(/ŋ/g,"ṃ");
	for(x in inStr){
		if(paliletter.indexOf(inStr[x])!=-1){
			output+=inStr[x];
		}
	}
	return(output);
}
