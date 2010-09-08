<?php  
function calpress_documentation_admin(){
    global $themename, $shortname, $calpress_nonadmin_options;
?>
    <div id="calpress-options" class="wrap metabox-holder">
    <?php if ( function_exists('screen_icon') ) screen_icon(); ?>
    <h2>Documentation</h2>

    <p>For the most current version of CalPress documentation, visit: <a href="http://calpresstheme.org">http://calpresstheme.org</a></p>

    <hr />

    <div class="calpress_admin_sidebar">
        <h4>Quick Reference</h4>
        
        <h4>CalPress Code</h4>
        <ul>
            <li><a href="http://code.google.com/p/calpresstheme/source/checkout">Source code</a></li>
            <li><a href="http://code.google.com/p/calpresstheme/source/list">Latest code updates</a></li>
        </ul>
        
        <h4>Current Configuration</h4>
        <dl>
            <dt>Theme</dt>
            <?php
                if (THEMENAME != TEMPLATENAME) {
                    echo("<dd>".TEMPLATENAME .  " (". TEMPLATEVERSION . ") " . " running on " . THEMENAME . " (" . CALPRESSVERSION . ")</dd>");
                } else {
                    echo("<dd>".THEMENAME . " (v" . CALPRESSVERSION . ")</dd>");
                }
            ?>
            
            <dt>Install Location</dt>
            <?php
                echo("<dd>".TEMPLATEPATH."</dd>");
            ?>
            
            <dt>Cache Directory</dt>
                <?php echo("<dd>".CALPRESSCACHE."</dd>"); ?>
            
            <dt>Video URL</dt>
                <dd>
                <?php 
                    if (VIDEOLOCATION != "") {
                        echo ("<a href=\"".VIDEOLOCATION."\">".VIDEOLOCATION."</a>");
                    } else {
                        echo ("<a href=\"#\">Set</a>");
                    }
                ?>
                </dd>
            <dt>Soundslides URL</dt>
                <dd>
                <?php 
                    if (SOUNDSLIDESLOCATION != "") {
                        echo ("<a href=\"".SOUNDSLIDESLOCATION."\">".SOUNDSLIDESLOCATION."</a>");
                    } else {
                        echo ("<a href=\"#\">Set</a>");
                    }
                ?>
                </dd>
            
            <!-- 
            <dt>Mobile</dt>
                <dd>Enabled</dd>
            
                
            <dt>Google Analytics ID</dt>
                <dd>Change</dd>
                
            <dt>Google Maps Key</dt>
                <dd>Change</dd>
             -->   
            
        </dl>
    </div>
    
    <h3>Basic Tasks</h3>
    
    <ul>
    		<li><a href="http://calpresstheme.org/blog/2010/08/10/posting-a-story/">Basic posts</a></li>

    		<li>Multiple bylines</li>
    		<li>Lead art
    			<ul>
    				<li><a href="http://calpresstheme.org/blog/2010/08/10/lead-photo/">Photos</a></li>
    				<li><a href="http://calpresstheme.org/blog/2010/08/10/lead-video/">Quicktime</a></li>			
    				<li><a href="http://calpresstheme.org/blog/2010/08/10/lead-youtube-vimeo/">YouTube/Vimeo</a></li>
    				<li><a href="http://calpresstheme.org/blog/2010/08/10/embed-html-in-lead-art-spot/">Embedding content into lead art spot</a></li>
    			</ul>
    		</li>
    		<li><a href="http://calpresstheme.org/blog/2010/08/10/digital-sites-soundslides-workflow/">Soundslides</a></li>
    		<li>Geotagging posts</li>

    		<li>Inline Content
    			<ul>
    				<li>Overview</li>
    				<li><a href="http://calpresstheme.org/blog/2010/08/10/inline-video/">Inline Quicktime video</a></li>

    				<li><a href="http://calpresstheme.org/blog/2010/08/10/inline-youtube-vimeo/">Inline YouTube/Vimeo</a></li>
    				<li><a href="http://calpresstheme.org/blog/2010/08/10/related-links/">Related links</a></li>
    				<li>Related Twitter search</li>
    				<li>Audio</li>
    			</ul>
    		</li>
    		<li>Author bio Page</li>

    	</ul>
    
    
    
    
    <?php require_once( dirname(__FILE__).'/admin-footer.php'); ?>
    </div><!-- wrap -->
<?php
}