function c1it(tabName,btnId,tabNumber){
	for(i=0;i<tabNumber;i++){
		document.getElementById(tabName+"_div"+i).style.display = "none";
		document.getElementById(tabName+"_btn"+i).className = "off";
	}
	document.getElementById(tabName+"_div"+btnId).style.display = "block";
	document.getElementById(tabName+"_btn"+btnId).className = "on";	
}

function crazybu(tabName,btnId,tabNumber){
	for(i=0;i<tabNumber;i++){
		document.getElementById(tabName+"_div"+i).style.display = "none";
		document.getElementById(tabName+"_btn"+i).className = "cyaskbu2";
	}
	document.getElementById(tabName+"_div"+btnId).style.display = "block";
	document.getElementById(tabName+"_btn"+btnId).className = "cyaskbu1";	
}