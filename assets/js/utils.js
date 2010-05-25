


// Page Ready functions
$(document).ready( function(){
    
    // Locate all .corner elements and round using jquery.corners, only if jquery.corners.js is installed
    if(typeof $(".rounded").corners == 'function') {
        $('.rounded').corners();
    }
  
});


// Prepare a Google Map (API v3)
// Based on work from: http://gmaps-samples-v3.googlecode.com/svn/trunk/callback/
//
// $locFunc - name of the function in the page that Google Map uses to build map
function gmapV3(localFunc) {
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.onload = function() {
        google.maps.loadScripts();
    };
    script.onreadystatechange = function() {
        if (this.readyState == 'loaded' || this.readyState == 'complete') {
            google.maps.loadScripts();
        }
    };
    script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=" + localFunc;
    document.body.appendChild(script);
}
