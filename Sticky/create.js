// Global namespace declaration
var g = {};

function init() { 
    g.z=1000;
    g.request = new XMLHttpRequest();
	loadNotes(); // load notes for the user who just logged in
	
    // set reference to form input element
    g.ta=document.getElementById("textarea");

    // set reference to form submit button
    g.btn=document.getElementById("createbtn");
	g.div0=document.getElementById("div0");
    
	// add listener to button click	
    addEvent(g.btn, "click", createNote);
	addEvent(g.btn, "click", function(event){event.preventDefault()}); 
}

function createNote() {
    // validate if form input
    if(g.ta.value!=null && g.ta.value.trim()!=""){
        serverRequest(g.ta.value.trim(), true) // asyn = true
    }else{
        alert("Need text content to create a note!");
    }
}

function loadNotes(){
	g.request.open("POST", "loadNotes.php", true);
    g.request.onreadystatechange = processResponse;
    g.request.setRequestHeader("Content-type", 
	                           "application/x-www-form-urlencoded");
    g.request.send("load=load");
}

function serverRequest(msg, async) {
    g.request.open("POST", "createnotes.php", async);
    g.request.onreadystatechange = processResponse;
    g.request.setRequestHeader("Content-type", 
	                           "application/x-www-form-urlencoded");
    g.request.send("content="+msg);
}

function deleteNote(e){
	g.request.open("POST", "deleteNote.php", true);
    g.request.onreadystatechange = processResponse;
    g.request.setRequestHeader("Content-type", 
	                           "application/x-www-form-urlencoded");
    g.request.send("deletemsg="+e.target.parentElement.id);
	// remove the note from the screen
	$("#"+e.target.parentElement.id).remove();	
}

// retrieve all notes from server after creating or deleting or moving a note
function processResponse() {	
	// remove call sticky class elements from screen
	$(".sticky").remove(); 
    if (g.request.readyState == 4 && g.request.status == 200) {
        var jsonArray = JSON.parse(g.request.responseText);
		var divdelete;
		var divnewcontent;
		var divsticky;
		if(jsonArray['results']!="error"){
			for(var ctr = 0; ctr < jsonArray.length; ctr++){
				divdelete=document.createElement("div");
				divdelete.className="delete";
				divdelete.innerHTML="X";
				
				divnewcontent=document.createElement("div");
				divnewcontent.className="newcontent"; 
				divnewcontent.innerHTML=jsonArray[ctr].memo;
				divnewcontent.style.zIndex=jsonArray[ctr].z_Index;
		
				divsticky=document.createElement("div");
				divsticky.className="sticky";
				divsticky.id=jsonArray[ctr].id;
					
				divsticky.appendChild(divdelete);
				// add delete listener to 'delete' class elements 
				addEvent(divdelete, "click", deleteNote);
				divsticky.appendChild(divnewcontent);
				divsticky.style.left=jsonArray[ctr].lft + "px";
				divsticky.style.top=jsonArray[ctr].top + "px";
				divsticky.style.position="absolute";	
				g.div0.appendChild(divsticky);
				// add draggable function to 'sticky' class elements
				draggable();
			}
		}else{
			location.reload();
		}
	}
}

// make sticky class elements draggable and report the position 
// where they stop 
function draggable(){
	$( ".sticky" ).draggable({
		stop: function(event, ui) { 
            // the currently dragged element always has the highest z-index 		
			$(this).css("z-index", g.z++); 
			var top = $(this).position().top;
			var left = $(this).position().left;
			var id=$(this).attr('id');				
			var zi=$(this).zIndex();
			$.ajax({
				url: "updateposition.php",
				type: "POST",
				data: {left: left, top: top, id: id, z_index: zi},
				dataType: "json",
				success: function(data) {
				},						
				error: function(jqXHR, textStatus, errorThrown) {
				   //alert(errorThrown);
				}
			});
		}
	});
}

// Adds event listeners to objects using the appropriate listener 
// for the browser in use
function addEvent(obj, type, fn)
{
	if(obj.addEventListener)
    {
		obj.addEventListener(type, fn, false);
	}
	else if(obj.attachEvent)
    {
		obj.attachEvent("on" + type, fn);
	}
}

// Run once the page is loaded
window.onload = init;