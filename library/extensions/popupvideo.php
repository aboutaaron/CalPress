<?php
// iframe video player

// take h.264 .mov reference file from quicktime export and build a flash / qt video page for inclusion into iFrames. 

$mov = $_REQUEST['url'];
$poster = str_replace(".mov", "-poster.jpg", $mov);
$iphone = str_replace(".mov", "-iPhone.m4v", $mov);

$autostart = strtolower($_REQUEST['autostart']);

if ($autostart === "true") {
    $autostartopt = "so.addVariable(\"autostart\",\"true\")";
}
else{
    $autostartopt = "so.addVariable(\"autostart\",\"false\")";
}


$r = rand();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="http://media.journalism.berkeley.edu/common/swfobject.js" type="text/javascript" charset="utf-8"></script>
	<style type="text/css" media="screen">
	   /* http://meyerweb.com/eric/tools/css/reset/ */
       /* v1.0 | 20080212 */

       html, body, div, span, applet, object, iframe,
       h1, h2, h3, h4, h5, h6, p, blockquote, pre,
       a, abbr, acronym, address, big, cite, code,
       del, dfn, em, font, img, ins, kbd, q, s, samp,
       small, strike, strong, sub, sup, tt, var,
       b, u, i, center,
       dl, dt, dd, ol, ul, li,
       fieldset, form, label, legend,
       table, caption, tbody, tfoot, thead, tr, th, td {
       	margin: 0;
       	padding: 0;
       	border: 0;
       	outline: 0;
       	font-size: 100%;
       	vertical-align: baseline;
       	background: transparent;
       }
       body {
       	line-height: 1;
       }
       ol, ul {
       	list-style: none;
       }
       blockquote, q {
       	quotes: none;
       }
       blockquote:before, blockquote:after,
       q:before, q:after {
       	content: '';
       	content: none;
       }

       /* remember to define focus styles! */
       :focus {
       	outline: 0;
       }

       /* remember to highlight inserts somehow! */
       ins {
       	text-decoration: none;
       }
       del {
       	text-decoration: line-through;
       }

       /* tables still need 'cellspacing="0"' in the markup */
       table {
       	border-collapse: collapse;
       	border-spacing: 0;
       }
       
       .lead-art{
           width: 620px;
           margin: auto;
       }
	</style>
	<title>Video Player</title>
    
</head>

<body>
    <div class="lead-art entry-leadvideo">    
        <div id="video_<?php echo($r);?>">
            <object width="620" height="365" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
            	<param name="src" value="<?php echo($poster);?>" />
            	<param name="href" value="<?php echo($mov);?>" />
            	<param name="target" value="myself" />
            	<param name="controller" value="true" />
            	<param name="autoplay" value="false" />
            	<param name="scale" value="tofit" />
            	<embed width="620" height="365" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"
            		src="<?php echo($poster);?>"
            		href="<?php echo($mov);?>"
            		target="myself"
            		controller="true"
            		autoplay="false"
            		scale="tofit">
            	</embed>
            </object>
        </div>


        <script type="text/javascript">
            var so = new SWFObject('http://media.journalism.berkeley.edu/common/player-licensed-viral.swf','player','620','409','9');
            so.addParam('allowfullscreen','true');
            so.addParam('allowscriptaccess','always');
            so.addVariable('file', '<?php echo($iphone);?>');
            so.addVariable('image','<?php echo($poster);?>');
            so.addVariable('skin', 'http://media.journalism.berkeley.edu/common/bekle.swf');    
            <?php echo $autostartopt; ?>        
            so.write('video_<?php echo($r);?>');
        </script>

</body>
</html>