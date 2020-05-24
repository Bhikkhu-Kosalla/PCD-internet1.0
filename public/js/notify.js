
	
	function ntf_init(){
		var divNotify=document.createElement("div");
		var typ=document.createAttribute("class");
		
		typ.nodeValue="pcd_notify";
		divNotify.attributes.setNamedItem(typ);

		var typId=document.createAttribute("id");
		typId.nodeValue="id_pcd_notify";
		divNotify.attributes.setNamedItem(typId);

		var body=document.getElementsByTagName("body")[0];
		body.appendChild(divNotify);
		divNotify.style.display="none";
		
	}
	function ntf_show(msg,timeout=5){
		var divNotify=document.getElementById("id_pcd_notify");
		if(divNotify){
			divNotify.innerHTML=msg;
			divNotify.style.display="block";
			setTimeout("ntf_hide()",timeout*1000);
		}
	}
	function ntf_hide(){
	document.getElementById('id_pcd_notify').style.display='none';
	}