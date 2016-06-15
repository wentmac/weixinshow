
// Demo variables
// iMouseDown represents the current mouse button state: up or down
/*
lMouseState represents the previous mouse button state so that we can
check for button clicks and button releases:

if(iMouseDown && !lMouseState) // button just clicked!
if(!iMouseDown && lMouseState) // button just released!
*/
var mouseOffset = null;
var iMouseDown  = false;
var lMouseState = false;
var dragObject  = null;

// Demo 0 variables
var DragDrops   = [];
var curTarget   = null;
var lastTarget  = null;
var dragHelper  = null;
var tempDiv     = null;
var rootParent  = null;
var rootSibling = null;

// Demo1 variables
var D1Target    = null;

Number.prototype.NaN0=function(){return isNaN(this)?0:this;}

function CreateDragContainer(){
	/*
	Create a new "Container Instance" so that items from one "Set" can not
	be dragged into items from another "Set"
	*/
	var cDrag        = DragDrops.length;
	DragDrops[cDrag] = [];

	/*
	Each item passed to this function should be a "container".  Store each
	of these items in our current container
	*/
	for(var i=0; i<arguments.length; i++){
		var cObj = arguments[i];
		DragDrops[cDrag].push(cObj);
		cObj.setAttribute('DropObj', cDrag);

		/*
		Every top level item in these containers should be draggable.  Do this
		by setting the DragObj attribute on each item and then later checking
		this attribute in the mouseMove function
		*/
		for(var j=0; j<cObj.childNodes.length; j++){

			// Firefox puts in lots of #text nodes...skip these
			if(cObj.childNodes[j].nodeName=='#text') continue;

			cObj.childNodes[j].setAttribute('DragObj', cDrag);
		}
	}
}

function getPosition(e){
	var left = 0;
	var top  = 0;
	while (e.offsetParent){
		left += e.offsetLeft + (e.currentStyle?(parseInt(e.currentStyle.borderLeftWidth)).NaN0():0);
		top  += e.offsetTop  + (e.currentStyle?(parseInt(e.currentStyle.borderTopWidth)).NaN0():0);
		e     = e.offsetParent;
	}


	left += e.offsetLeft + (e.currentStyle?(parseInt(e.currentStyle.borderLeftWidth)).NaN0():0);
	top  += e.offsetTop  + (e.currentStyle?(parseInt(e.currentStyle.borderTopWidth)).NaN0():0);

	return {x:left, y:top};

}

function mouseCoords(ev){
	if(ev.pageX || ev.pageY){
		return {x:ev.pageX, y:ev.pageY};
	}
	return {
		x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
		y:ev.clientY + document.body.scrollTop  - document.body.clientTop
	};
}

function writeHistory(object, message){
	if(!object || !object.parentNode || !object.parentNode.getAttribute) return;
	var historyDiv = object.parentNode.getAttribute('history');
	if(historyDiv){
		historyDiv = document.getElementById(historyDiv);
		historyDiv.appendChild(document.createTextNode(object.id+': '+message));
		historyDiv.appendChild(document.createElement('BR'));

		historyDiv.scrollTop += 50;
	}
}

function getMouseOffset(target, ev){
	ev = ev || window.event;

	var docPos    = getPosition(target);
	var mousePos  = mouseCoords(ev);
	return {x:mousePos.x - docPos.x, y:mousePos.y - docPos.y};
}

function mouseMove(ev){
	ev         = ev || window.event;
	/*
	We are setting target to whatever item the mouse is currently on

	Firefox uses event.target here, MSIE uses event.srcElement
	*/
	var target   = ev.target || ev.srcElement;
	var mousePos = mouseCoords(ev);

		// mouseOut event - fires if the item the mouse is on has changed
		if(lastTarget && (target!==lastTarget)){
			writeHistory(lastTarget, 'Mouse Out Fired');

			// reset the classname for the target element
			var origClass = lastTarget.getAttribute('origClass');
			if(origClass) lastTarget.className = origClass;
		}

		/*
		dragObj is the grouping our item is in (set from the createDragContainer function).
		if the item is not in a grouping we ignore it since it can't be dragged with this
		script.
		*/
		var dragObj = target.getAttribute('DragObj');

		 // if the mouse was moved over an element that is draggable
		if(dragObj!=null){

			// mouseOver event - Change the item's class if necessary
			if(target!=lastTarget){
				writeHistory(target, 'Mouse Over Fired');

				var oClass = target.getAttribute('overClass');
				if(oClass){
					target.setAttribute('origClass', target.className);
					target.className = oClass;
				}
			}

			// if the user is just starting to drag the element
			if(iMouseDown && !lMouseState){
				writeHistory(target, 'Start Dragging');

				// mouseDown target
				curTarget     = target;

				// Record the mouse x and y offset for the element
				rootParent    = curTarget.parentNode;
				rootSibling   = curTarget.nextSibling;

				mouseOffset   = getMouseOffset(target, ev);

				// We remove anything that is in our dragHelper DIV so we can put a new item in it.
				for(var i=0; i<dragHelper.childNodes.length; i++) dragHelper.removeChild(dragHelper.childNodes[i]);

				// Make a copy of the current item and put it in our drag helper.
				dragHelper.appendChild(curTarget.cloneNode(true));
				dragHelper.style.display = 'block';

				// set the class on our helper DIV if necessary
				var dragClass = curTarget.getAttribute('dragClass');
				if(dragClass){
					dragHelper.firstChild.className = dragClass;
				}

				// disable dragging from our helper DIV (it's already being dragged)
				dragHelper.firstChild.removeAttribute('DragObj');

				/*
				Record the current position of all drag/drop targets related
				to the element.  We do this here so that we do not have to do
				it on the general mouse move event which fires when the mouse
				moves even 1 pixel.  If we don't do this here the script
				would run much slower.
				*/
				var dragConts = DragDrops[dragObj];

				/*
				first record the width/height of our drag item.  Then hide it since
				it is going to (potentially) be moved out of its parent.
				*/
				curTarget.setAttribute('startWidth',  parseInt(curTarget.offsetWidth));
				curTarget.setAttribute('startHeight', parseInt(curTarget.offsetHeight));
				curTarget.style.display  = 'none';

				// loop through each possible drop container
				for(var i=0; i<dragConts.length; i++){
					with(dragConts[i]){
						var pos = getPosition(dragConts[i]);

						/*
						save the width, height and position of each container.

						Even though we are saving the width and height of each
						container back to the container this is much faster because
						we are saving the number and do not have to run through
						any calculations again.  Also, offsetHeight and offsetWidth
						are both fairly slow.  You would never normally notice any
						performance hit from these two functions but our code is
						going to be running hundreds of times each second so every
						little bit helps!

						Note that the biggest performance gain here, by far, comes
						from not having to run through the getPosition function
						hundreds of times.
						*/
						setAttribute('startWidth',  parseInt(offsetWidth));
						setAttribute('startHeight', parseInt(offsetHeight));
						setAttribute('startLeft',   pos.x);
						setAttribute('startTop',    pos.y);
					}

					// loop through each child element of each container
					for(var j=0; j<dragConts[i].childNodes.length; j++){
						with(dragConts[i].childNodes[j]){
							if((nodeName=='#text') || (dragConts[i].childNodes[j]==curTarget)) continue;

							var pos = getPosition(dragConts[i].childNodes[j]);

							// save the width, height and position of each element
							setAttribute('startWidth',  parseInt(offsetWidth));
							setAttribute('startHeight', parseInt(offsetHeight));
							setAttribute('startLeft',   pos.x);
							setAttribute('startTop',    pos.y);
						}
					}
				}
			}
		}

		// If we get in here we are dragging something
		if(curTarget){
			// move our helper div to wherever the mouse is (adjusted by mouseOffset)
			dragHelper.style.top  = mousePos.y - mouseOffset.y;
			dragHelper.style.left = mousePos.x - mouseOffset.x;

			var dragConts  = DragDrops[curTarget.getAttribute('DragObj')];
			var activeCont = null;

			var xPos = mousePos.x - mouseOffset.x + (parseInt(curTarget.getAttribute('startWidth')) /2);
			var yPos = mousePos.y - mouseOffset.y + (parseInt(curTarget.getAttribute('startHeight'))/2);

			// check each drop container to see if our target object is "inside" the container
			for(var i=0; i<dragConts.length; i++){
				with(dragConts[i]){
					if((parseInt(getAttribute('startLeft'))                                           < xPos) &&
						(parseInt(getAttribute('startTop'))                                            < yPos) &&
						((parseInt(getAttribute('startLeft')) + parseInt(getAttribute('startWidth')))  > xPos) &&
						((parseInt(getAttribute('startTop'))  + parseInt(getAttribute('startHeight'))) > yPos)){

							/*
							our target is inside of our container so save the container into
							the activeCont variable and then exit the loop since we no longer
							need to check the rest of the containers
							*/
							activeCont = dragConts[i];

							// exit the for loop
							break;
					}
				}
			}

			// Our target object is in one of our containers.  Check to see where our div belongs
			if(activeCont){
				if(activeCont!=curTarget.parentNode){
					writeHistory(curTarget, 'Moved into '+activeCont.id);
				}

				// beforeNode will hold the first node AFTER where our div belongs
				var beforeNode = null;

				// loop through each child node (skipping text nodes).
				for(var i=activeCont.childNodes.length-1; i>=0; i--){
					with(activeCont.childNodes[i]){
						if(nodeName=='#text') continue;

						// if the current item is "After" the item being dragged
						if(curTarget != activeCont.childNodes[i]                                                  &&
							((parseInt(getAttribute('startLeft')) + parseInt(getAttribute('startWidth')))  > xPos) &&
							((parseInt(getAttribute('startTop'))  + parseInt(getAttribute('startHeight'))) > yPos)){
								beforeNode = activeCont.childNodes[i];
						}
					}
				}

				// the item being dragged belongs before another item
				if(beforeNode){
					if(beforeNode!=curTarget.nextSibling){
						writeHistory(curTarget, 'Inserted Before '+beforeNode.id);

						activeCont.insertBefore(curTarget, beforeNode);
					}

				// the item being dragged belongs at the end of the current container
				} else {
					if((curTarget.nextSibling) || (curTarget.parentNode!=activeCont)){
						writeHistory(curTarget, 'Inserted at end of '+activeCont.id);

						activeCont.appendChild(curTarget);
					}
				}

				// the timeout is here because the container doesn't "immediately" resize
				setTimeout(function(){
				var contPos = getPosition(activeCont);
				activeCont.setAttribute('startWidth',  parseInt(activeCont.offsetWidth));
				activeCont.setAttribute('startHeight', parseInt(activeCont.offsetHeight));
				activeCont.setAttribute('startLeft',   contPos.x);
				activeCont.setAttribute('startTop',    contPos.y);}, 5);

				// make our drag item visible
				if(curTarget.style.display!=''){
					writeHistory(curTarget, 'Made Visible');
					curTarget.style.display    = '';
					curTarget.style.visibility = 'hidden';
				}
			} else {

				// our drag item is not in a container, so hide it.
				if(curTarget.style.display!='none'){
					writeHistory(curTarget, 'Hidden');
					curTarget.style.display  = 'none';
				}
			}
		}

		// track the current mouse state so we can compare against it next time
		lMouseState = iMouseDown;

		// mouseMove target
		lastTarget  = target;

	if(dragObject){
		dragObject.style.position = 'absolute';
		dragObject.style.top      = mousePos.y - mouseOffset.y;
		dragObject.style.left     = mousePos.x - mouseOffset.x;
	}

	// track the current mouse state so we can compare against it next time
	lMouseState = iMouseDown;

	// this prevents items on the page from being highlighted while dragging
	if(curTarget || dragObject) return false;
}

function mouseUp(ev){
		if(curTarget){
			writeHistory(curTarget, 'Mouse Up Fired');
			dragHelper.style.display = 'none';
			if(curTarget.style.display == 'none'){
				if(rootSibling){
					rootParent.insertBefore(curTarget, rootSibling);
				} else {
					rootParent.appendChild(curTarget);
				}
			}
			curTarget.style.display    = '';
			curTarget.style.visibility = 'visible';
		}
		curTarget  = null;

	dragObject = null;

	iMouseDown = false;
}

function mouseDown(ev){
	ev         = ev || window.event;
	var target = ev.target || ev.srcElement;

	iMouseDown = true;
	if(lastTarget){
			writeHistory(lastTarget, 'Mouse Down Fired');
	}
	if(target.onmousedown || target.getAttribute('DragObj')){
		return false;
	}
}

document.onmousemove = mouseMove;
document.onmousedown = mouseDown;
document.onmouseup   = mouseUp;