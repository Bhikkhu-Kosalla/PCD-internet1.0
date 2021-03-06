//旧版 xml的根节点
var xmlDoc = null;
var xmlDict = null;
var gXmlOldVerData;//old ver xml file xml doc

var g_filename="";//工程文件名
var arrDocFileInfo=null;

//全部的逐词译单词xml 节点数组
var gXmlAllWordInWBW = new Array();

var gXmlBookData;
var gXmlBookDataHead;
var gXmlBookDataHeadToc;
var gXmlBookDataBody=null;
var gXmlBookDataInlineDict;  //内联字典数据
var gXmlBookDataMsg;  //消息数据
var gDocMsgList=new Array();//消息数组

var gXmlParIndex;//段落列表

var gArrayDocParagraph=new Array();//文档中的段落列表

//资源列表
var lstResTranslate = new Array();
var lstResNote = new Array();
var lstResWbw = new Array();
var lstResHeading = new Array();

var doc_info = new Object();

function createXmlDoc(){
	var strXml="";
	strXml+="<set>\n"
	strXml+="    <head>\n"
	strXml+="        <type>pcdsset</type>\n"
	strXml+="        <mode>package</mode>\n"
	strXml+="        <ver>1</ver>\n"
	strXml+="        <toc></toc>\n"
	strXml+="        <style></style>\n"
	strXml+="    </head>\n"
	strXml+="    <dict></dict>\n"
	strXml+="    <message></message>\n"
	strXml+="    <body>\n"
	strXml+="    </body>\n"
	strXml+="</set>\n"
	
	if (window.DOMParser){
		parser=new DOMParser();
		gXmlBookData=parser.parseFromString(strXml,"text/xml");
	}
	else // Internet Explorer
	{
		gXmlBookData=new ActiveXObject("Microsoft.XMLDOM");
		gXmlBookData.async="false";
		gXmlBookData.loadXML(strXml);
	}
			  
	if (gXmlBookData == null){
		alert("error:can not load book index.");
			return;
	}
	gXmlBookDataBody = gXmlBookData.getElementsByTagName("body")[0];
	gXmlBookDataHead= gXmlBookData.getElementsByTagName("head")[0];
	gXmlBookDataInlineDict= gXmlBookData.getElementsByTagName("dict")[0];
	gXmlBookDataMsg= gXmlBookData.getElementsByTagName("message")[0];
	gXmlBookDataHeadToc= gXmlBookDataHead.getElementsByTagName("toc")[0];

}

function com_XmlAllWordRefresh(){
	gXmlAllWordInWBW = gXmlBookDataBody.getElementsByTagName("word");
}

function insertBlockToXmlBookData(element){
	xmlParInfo = element.getElementsByTagName("info")[0];
	xmlParData = element.getElementsByTagName("data")[0];
	bookId=getNodeText(xmlParInfo,"book")
	paragraph=getNodeText(xmlParInfo,"paragraph")
	type=getNodeText(xmlParInfo,"type")
	switch(type){
		case "wbw":
			xWords=element.getElementsByTagName("word");
			for(iWord=0;iWord<xWords.length;iWord++){
				len=gXmlAllWordInWBW.length;
				gXmlAllWordInWBW[len]=xWords[iWord];
			}
		break;
	}
	
	gXmlBookDataBody.appendChild(element.cloneNode(true));
	updataDocParagraphList();
}

function getParIndex(bookId,parNo){
	for(var iPar=0;iPar<gArrayDocParagraph.length;iPar++){
			currBookId=gArrayDocParagraph[iPar].book
			currParNo=gArrayDocParagraph[iPar].paragraph

		if(currBookId==bookId && currParNo==parNo){
			return(iPar);
		}
	}
	return(-1);

}
//扫描文档 更新段落列表
function updataDocParagraphList(){
	gArrayDocParagraph=new Array();
	var temp=new Array();
	allBlock=gXmlBookDataBody.getElementsByTagName("block")
	for(var iBlock=0;iBlock<allBlock.length;iBlock++){
		xmlParInfo = allBlock[iBlock].getElementsByTagName("info")[0];
		xmlParData = allBlock[iBlock].getElementsByTagName("data")[0];
		
		var type=getNodeText(xmlParInfo,"type")
		var newPar=new Object();
		newPar.book=getNodeText(xmlParInfo,"book")
		newPar.paragraph=getNodeText(xmlParInfo,"paragraph")
		newPar.level=getNodeText(xmlParInfo,"level");
		newPar.style=getNodeText(xmlParInfo,"style");
		if(newPar.level==""){
			newPar.level=100;
		}
		
		//如果有相同段落层级更高的记录，替换。
		var bookpara=newPar.book.toString()+"-"+newPar.paragraph.toString();
		if(temp[bookpara]){
			if(newPar.level>0 && newPar.level<9){
				if(temp[bookpara].level>0 && temp[bookpara].level<9){
					if(temp[bookpara].level>newPar.level){
						temp[bookpara].level=newPar.level;
					}
				}
				else{
					temp[bookpara].level=newPar.level;
				}
			}
		}
		else{
			temp[bookpara]=newPar;
		}

	}
	for(var iTemp in temp){
		gArrayDocParagraph.push(temp[iTemp]);
	}
}

function parIsSet(inBook,inPar){
	for(var iPar=0;iPar<gArrayDocParagraph.length;iPar++){
		if(gArrayDocParagraph[iPar].book==inBook && gArrayDocParagraph[iPar].paragraph==inPar){
			return(iPar)
		}
	}
	return(-1)
}
function doc_setWordDataById(wordId,key,value){
	var xAllWord = gXmlBookDataBody.getElementsByTagName("word");	
	var wordIndex=getWordIndex(wordId);
	setNodeText(xAllWord[wordIndex],key,value);
}
function doc_file_info_get(){
  $.post("file_index.php",
  {
    op:"getall",
    filename:g_filename
  },
function(data,status){
	  arrDocFileInfo = JSON.parse(data);
	  if(arrDocFileInfo.parent_id==null){
		doc_info.parent_id="";
	  }
	  else{
		doc_info.parent_id=arrDocFileInfo.parent_id;
	  }
	  doc_info.doc_id=arrDocFileInfo.doc_id;
	  doc_info.share=arrDocFileInfo.share;
	  if(arrDocFileInfo.parent_id!=null){
		strMsgDocList=arrDocFileInfo.parent_id;
		msg_start();//该文档是他人分享的文档，需要发送消息
		doc_info.sendmsg=true;
	  }
	  else{
		  if(arrDocFileInfo.share==1){
			  strMsgDocList=arrDocFileInfo.doc_id;
			  msg_start();
			  doc_info.sendmsg=true;//共享给其他人，需要发送消息
		  }
		  else{
			  doc_info.sendmsg=false;//无需发送消息
		  }
	  }
  });
}

function doc_info_change(field,value){
	$.post("file_index.php",
	{
	op:"set",
	filename:g_filename,
	field:"accese_time",
	value:value
	},
	function(data,status){
	});

}

function doc_info_title_change(obj){
	setNodeText(gXmlBookDataHead,"doc_title",obj.value);
	document.getElementById("editor_doc_title").innerHTML=obj.value;
	document.getElementById("file_title").innerHTML=obj.value;
	$.post("file_index.php",
	{
	op:"set",
	filename:g_filename,
	field:"title",
	value:obj.value
	},
	function(data,status){
	alert("Data: " + data + "\nStatus: " + status);
	});
	
}
function getTranslateText(id){
		var xBlock=gXmlBookDataBody.getElementsByTagName("block");
		for(var iBlock=0;iBlock<xBlock.length;iBlock++){
			xmlParInfo = xBlock[iBlock].getElementsByTagName("info")[0];
			xmlParData = xBlock[iBlock].getElementsByTagName("data")[0];
			mId=getNodeText(xmlParInfo,"id")
			type=getNodeText(xmlParInfo,"type")
			if(type=="translate"){
				if(mId==id){
					xmlParDataSen = xmlParData.getElementsByTagName("sen");
					var currText="";
					for(iSen=0;iSen<xmlParDataSen.length;iSen++){
						currText += getNodeText(xmlParDataSen[iSen],"text");
					}
					var obj = new Object();
					obj.text = currText;
					obj.language =  getNodeText(xmlParInfo,"language");
					obj.author =  getNodeText(xmlParInfo,"author");
					return(obj);
				}
			}
		}
		return(null);
}

/*
setTranText
功能：存储翻译文本
参数：
id:数据块guid
senA:句子锚点
strValue:句子文本数据
返回值：
无

*/
function setTranText(id,senA,strValue){
	xBlock=gXmlBookDataBody.getElementsByTagName("block");
	for(var iBlock=0;iBlock<xBlock.length;iBlock++){
		xmlParInfo = xBlock[iBlock].getElementsByTagName("info")[0];
		xmlParData = xBlock[iBlock].getElementsByTagName("data")[0];
		let blockId=getNodeText(xmlParInfo,"id");
		let book=getNodeText(xmlParInfo,"book");
		let para=getNodeText(xmlParInfo,"paragraph");
		if(blockId==id){
			xSen=xmlParData.getElementsByTagName("sen");
			for(var iSen=0;iSen<xSen.length;iSen++){
				var aBegin=getNodeText(xSen[iSen],"begin");
				var aEnd=getNodeText(xSen[iSen],"end");
				if(aEnd==senA){
					//var newText=strValue.replace(/\n/g,"<br />");
					setNodeText(xSen[iSen],"text",strValue);
					ntf_show("修改："+strValue);
					//准备消息数据 並發送
					if(doc_info.sendmsg){
						let d=new Date();
						let msg_doc_id;
						if(doc_info.parent_id!=""){
							msg_doc_id=doc_info.parent_id;
						}
						else{
							msg_doc_id=doc_info.doc_id;
						}
						let strMsg="{\"id\":\""+id+"\",\"begin\":\""+aBegin+"\",\"end\":\""+aEnd+"\",\"text\":\""+strValue+"\"}";
						msg_push(2,strMsg,msg_doc_id,d.getTime(),book,para);
						console.log("send mseeage:"+strMsg);
					}
				}
			}
		}
	}
}

function setNoteText(id,strValue){
	xBlock=gXmlBookDataBody.getElementsByTagName("block");
	for(let iBlock=0;iBlock<xBlock.length;iBlock++){
		xmlParInfo = xBlock[iBlock].getElementsByTagName("info")[0];
		xmlParData = xBlock[iBlock].getElementsByTagName("data")[0];
		blockId=getNodeText(xmlParInfo,"id");
		if(blockId==id){
			xSen=xmlParData.getElementsByTagName("sen");
			if(xSen.length>0){
				newText=strValue.replace(/\n/g,"<br />");
				setNodeText(xSen[0],"text",strValue);
				//var_dump(strValue);
			}
		}
	}
}

function doc_head(key,value=null){
	
	if(value){//set
		setNodeText(gXmlBookDataHead,key,value);
	}
	else{//get
		return(getNodeText(gXmlBookDataHead,key));
	}
	
}

function doc_msg_push(msgobj){
	gDocMsgList.push(msgobj);
	if(gXmlBookDataMsg){
		var newNode=gXmlBookData.createElement("msg");
		setNodeText(newNode,"id",msgobj.id);
		setNodeText(newNode,"sender",msgobj.sender);
		setNodeText(newNode,"type",msgobj.type);
		setNodeText(newNode,"docid",msgobj.docid);
		setNodeText(newNode,"time",msgobj.time);
		setNodeText(newNode,"read",msgobj.read);
		setNodeText(newNode,"data",msgobj.data);
		gXmlBookDataMsg.appendChild(newNode);
	}
}

function doc_block(strSelector=""){
	if(strSelector==""){
		var xBlock = gXmlBookDataBody.getElementsByTagName("block");	
		return(xBlock);
	}
	else if(strSelector.substr(0,1)=="#"){
		var sBlockId=strSelector.substr(1);
		var xBlock = gXmlBookDataBody.getElementsByTagName("block");	
		for(let i in xBlock){
			let xmlParInfo = xBlock[i].getElementsByTagName("info")[0];
			let xmlParData = xBlock[iBlock].getElementsByTagName("data")[0];
			let blockId=getNodeText(xmlParInfo,"id");
			if(blockId==sBlockId){
				var blockObj=new Object();
				blockObj.info=xmlParInfo;
				blockObj.data=xmlParData;
				blockObj.element=xAllWord[wid];
				return(blockObj);				
			}
		}
		return(null);
	}
	else{
		return(null);
	}
}

/*
word("#p34-3-3").val("mean","jiji@en")
add
remove
.draw
*/
function doc_word(strSelector=""){
	if(strSelector==""){
		var xAllWord = gXmlBookDataBody.getElementsByTagName("word");	
		return(xAllWord);
	}
	else if(strSelector.substr(0,1)=="#"){
		var sWordId=strSelector.substr(1);
		var xAllWord = gXmlBookDataBody.getElementsByTagName("word");	
		var wid=getWordIndex(sWordId);
		if(xAllWord[wid]){
			var wordobj=new Object();
			wordobj.wordid=strSelector;
			wordobj.element=xAllWord[wid];
			wordobj.val=_doc_word_value;
			return(wordobj);
		}
		else{
			var wordobj=new Object();
			wordobj.wordid=strSelector;
			wordobj.element=null;
			wordobj.val=_doc_word_value;
			return(wordobj);
		}
	}
	else{
	}
}
function _doc_word_value(key,value=null){
	if(this.element){
		if(value){
			setNodeText(this.element,key,value);
		}
		else{
			var output = getNodeText(this.element,key);
			return(output);
		}
	}
	else{
		if(!value){
			return("");
		}
	}
}


/*
doc_data_tran["guid"][2].begin=2 
doc_tran("guid").info("book");
doc_tran("guid").info("author","new author");
doc_tran("guid").text();
doc_tran("guid").sen(1).begin()  .end() .text()
doc_tran("guid").split(array);

*/

function doc_tran(strSelector=""){
	if(strSelector==""){
		let xBlock = gXmlBookDataBody.getElementsByTagName("block");	
		return(xBlock);
	}
	else if(strSelector.substr(0,1)=="#"){
		var sBlockId=strSelector.substr(1);
		var xBlock = gXmlBookDataBody.getElementsByTagName("block");	
		let i=0;
		for(i=0; i<xBlock.length;i++){
			let xmlParInfo = xBlock[i].getElementsByTagName("info")[0];
			let xmlParData = xBlock[i].getElementsByTagName("data")[0];
			let blockId=getNodeText(xmlParInfo,"id");
			if(blockId==sBlockId){
				var blockObj=new Object();
				blockObj._info=xmlParInfo;
				blockObj._data=xmlParData;
				blockObj.text=_doc_tran_sent;
				blockObj.info=_doc_tran_info;
				return(blockObj);				
			}
		}
	}
	else{
	}
	var blockObj=new Object();
	blockObj.info=null;
	blockObj.data=null;
	blockObj.text=_doc_tran_sent;
	blockObj.info=_doc_tran_info;
	return(blockObj);	
	
}
function _doc_tran_sent(begin,end,key,value=null){
	if(this._data){
		xSen=this._data.getElementsByTagName("sen");
		for(let iSen=0;iSen<xSen.length;iSen++){
			let aBegin=getNodeText(xSen[iSen],"begin");
			let aEnd=getNodeText(xSen[iSen],"end");
			if(aBegin==begin && aEnd==end){
				if(value){
					setNodeText(xSen[iSen],key,value);
					console.log("translation changed. key="+key+" value="+value);
					if(key=="text"){
						ntf_show("修改："+key+"="+value);
						let blockId = this.info("id");
						let book = this.info("book");
						let para = this.info("paragraph");
						update_tran_block(blockId);
						//准备消息数据 並發送
						if(doc_info.sendmsg){
							let d=new Date();
							let msg_doc_id;
							if(doc_info.parent_id!=""){
								msg_doc_id=doc_info.parent_id;
							}
							else{
								msg_doc_id=doc_info.doc_id;
							}
							msg_push(2,"{\"id\":\""+blockId+"\",\"begin\":\""+aBegin+"\",\"end\":\""+aEnd+"\",\"text\":\""+value+"\"}",msg_doc_id,d.getTime(),book,para);
						}
					}
				}
				else{
					var output = getNodeText(xSen[iSen],key);
					return(output);
				}
			}
		}		
	}
	else{
		if(!value){
			return("");
		}
	}
}
function _doc_tran_info(key,value=null){
	if(this._info){
		if(value){
			setNodeText(this._info,key,value);
		}
		else{
			var output = getNodeText(this._info,key);
			return(output);
		}
	}
	else{
		if(!value){
			return("");
		}
	}
}



//工程文件数据解析
function projectDataParse(xmlBookData){
	gXmlBookDataBody = xmlBookData.getElementsByTagName("body")[0];
	gXmlBookDataHead= xmlBookData.getElementsByTagName("head")[0];
	gXmlBookDataInlineDict= xmlBookData.getElementsByTagName("dict")[0];
	gXmlBookDataHeadToc= xmlBookData.getElementsByTagName("toc")[0];
	if(xmlBookData.getElementsByTagName("message")){
		gXmlBookDataMsg= gXmlBookData.getElementsByTagName("message")[0];
	}
	else{
		gXmlBookDataMsg=null;
	}
	//解析消息队列
	if(gXmlBookDataMsg){
		var msgElements=gXmlBookDataMsg.getElementsByTagName("msg");
		for(var iMsg=0;iMsg<msgElements.length;iMsg++){
			var objMsg = new Object();
				objMsg.id=getNodeText(msgElements[iMsg],"id");
				objMsg.sender=getNodeText(msgElements[iMsg],"sender");
				objMsg.type=getNodeText(msgElements[iMsg],"type");
				objMsg.docid=getNodeText(msgElements[iMsg],"docid");
				objMsg.time=getNodeText(msgElements[iMsg],"time");
				objMsg.read=getNodeText(msgElements[iMsg],"read");
				strData=getNodeText(msgElements[iMsg],"data");
			try{
				objMsg.data=JSON.parse(strData);
				gDocMsgList.push(objMsg);//添加到消息列表数组
			}
			catch(e){
			}
		}
	}
	
	
	com_XmlAllWordRefresh();
	//更新工程资源列表
	editor_project_updataProjectInfo();
	//解析内联字典数据
	ildDataParse(gXmlBookDataInlineDict);
	//将内联字典数据导入已经下载的词的列表
	dict_inid_ild_word_list();
}

/*
doc_block("album_id",id).each(function(e){
	e.info("book")
});
doc_block("album_id",id).info("album_id",newid);
//translate
doc_block("id","dd").data(1).text("new sentenc");
doc_block("id","dd").data(1).begin(23);
doc_block("id","dd").data().break(breakpoint);
doc_block("id","dd").data().each(function(e){});

doc_block().push(element);
doc_block().length();

var block=new Object();
block.info["id"]="";
block.album_id=219;
block.album_guid="af023c";
block.type="wbw";
block.book=23;
block.para=33;
block.author="kosalla#kosalla@_tch;visuddhinanda;";
block.editor="kosalla#kosalla@_tch;"
block._data=xmlElement

_blocks = new Array();
_blocks.push(newblock)

}
*/
