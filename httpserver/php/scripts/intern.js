window.addEventListener("load", evt=>{

    var event = new MouseEvent('mousedown');
    element.dispatchEvent(event);

    /*Array.prototype.slice.call(document.getElementsByTagName("select")).forEach(v=>{
        v.addEventListener("click", ev=>{
            //ev.stopPropagation = false;
            //if(document.activeElement == v) {
            if(typeof v.clicked=="undefined") v.clicked = false;
            console.log(v.clicked);
            if(v.clicked) {
                v.blur();
                v.clicked = false;
            } else  {
                v.focus();
                v.clicked = true;
            }
            //}
        });
    });*/
});