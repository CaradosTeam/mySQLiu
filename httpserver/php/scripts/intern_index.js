window.addEventListener("load", evt=>{
    //let endPoint = new Date().getTime(), result = endPoint - startPoint;
    //console.log();

    document.getElementById("openMenunav").addEventListener("click", ev=>{
        let thisNode = document.getElementById("openMenunav");
        if(typeof thisNode.clicked=="undefined") thisNode.clicked = false;
        if(thisNode.clicked) {
            document.getElementById("menunav").animate("fadeOut", 5000);
            thisNode.className = "";
            thisNode.clicked = false;
        } else {
            document.getElementById("menunav").animate("fadeIn", 5000);
            thisNode.clicked = true;
            thisNode.className = "selected";
        }
    });

    //Console
    var consoleNode = new MysqliuConsole().injectTo(document.getElementById("hiddenConsole"));

    const borderSize = 4, minHeightF = 500, minHeightS = 40, maxHeightS = 300; let pos = 0;

    let base = document.getElementById("base"), hiddenConsole = document.getElementById("hiddenConsole");

    function resizeContainers(ev) {
        const dy = pos - ev.y;
        pos = ev.y;
        let containerFCurr = parseInt(getComputedStyle(base, '').height), containerSCurr = parseInt(getComputedStyle(hiddenConsole, '').height) || hiddenConsole.offsetHeight;
        console.log(containerSCurr+dy, containerFCurr-dy, containerSCurr, getComputedStyle(hiddenConsole, '').height);
        if((containerSCurr+dy>minHeightS && containerSCurr+dy<maxHeightS) && containerFCurr-dy>minHeightF) {
        //base.style.height = (containerFCurr - dy) + "px";
        base.style.height = "calc(100vh - "+(containerSCurr + dy) + "px)"; //containerFCurr - dy
        hiddenConsole.style.height = (containerSCurr + dy) + "px";
        }
    }

    hiddenConsole.addEventListener("mousedown", function(e){
        if(e.offsetY<0) {
            //document.getElementById("res").textContent = e.offsetY+"/"+e.y;
            console.log(e.offsetY+"/"+e.y);
        pos = e.y;
        document.addEventListener("mousemove", resizeContainers, false);
        }
    });
    
    
    document.addEventListener("mouseup", function(){
        document.removeEventListener("mousemove", resizeContainers, false);
    }, false);

    document.getElementById("openConsole").addEventListener("click", ev=>{
        let hiddenConsoleNode =  document.getElementById("hiddenConsole");
        if(typeof hiddenConsoleNode.clicked=="undefined") hiddenConsoleNode.clicked = false;
        if(hiddenConsoleNode.clicked) {
            //hiddenConsoleNode.animate("dropOut", 5000); //slideUp
            hiddenConsoleNode.style.display = "none";
            hiddenConsoleNode.clicked = false;
            document.getElementById("openConsole").className = "";
            base.removeAttribute("style");
        } else {
            //hiddenConsoleNode.animate("dropIn", 5000); //slideDown
            hiddenConsoleNode.style.display = "block";
            hiddenConsoleNode.clicked = true;
            document.getElementById("openConsole").className = "selected";
            base.setAttribute("style", "height:calc(100vh - 70px)");
        }

    });


});
