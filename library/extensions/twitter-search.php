<?php
// This file is part of the CalPress Theme for WordPress
// http://calpresstheme.org
//
// CalPress is a project of the University of California 
// Berkeley Graduate School of Journalism
// http://journalism.berkeley.edu
//
// Copyright (c) 2010 The Regents of the University of California
//
// Released under the GPL Version 2 license
// http://www.opensource.org/licenses/gpl-2.0.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

// =================================
// = CalPress Twitter Search =
// = invoked with Post extra_field
// = twitter_search : term
// =================================
function calpress_twittersearch(){
    if ( get_post_custom_values('twitter_search') ) {
       
        $twittersearch = get_post_custom_values('twitter_search');
        $searchterms = $twittersearch[0];
        
         //load twitter's JS file
        echo("<script src=\"http://widgets.twimg.com/j/2/widget.js\"></script>");
        
        echo("<div class=\"twitter-search\">");
            //echo("<div class=\"search-terms\"><h3><span class=\"twitter-name\">Twitter:</span> <span class=\"search-terms\">$searchterms</span></h3></div>");
            echo("<div class=\"twitter-feed\">");
                echo"
            <script>
            new TWTR.Widget({
              version: 2,
              type: 'search',
              search: '$searchterms',
              interval: 6000,
              title: 'Latest on Twitter',
              subject: '$searchterms',
              width: 'auto',
              height: 300,
              theme: {
                shell: {
                     background: '#999999',
                      color: '#ffffff'
                },
                tweets: {
                    background: '#eeeeee',
                    color: '#000000',
                    links: '#666666'
                }
              },
              features: {
                scrollbar: true,
                loop: false,
                live: true,
                hashtags: true,
                timestamp: true,
                avatars: true,
                behavior: 'all'
              }
            }).render().start();
            </script>
            ";
            echo("</div><!--//end .twitter-feed -->");
        echo("</div><!--//end .twitter-search -->");
    }
}

// =================================
// = Twitterprofile =
// =================================
function calpress_twitterprofile($twitterhandle){
        
     //load twitter's JS file
    echo("<script src=\"http://widgets.twimg.com/j/2/widget.js\"></script>");
    
    echo("<div class=\"twitter-search\">");
        //echo("<div class=\"search-terms\"><h3><span class=\"twitter-name\">Twitter:</span> <span class=\"search-terms\">$searchterms</span></h3></div>");
        echo("<div class=\"twitter-feed\">");
            echo"
            <script>
            new TWTR.Widget({
              version: 2,
              type: 'profile',
              rpp: 4,
              interval: 6000,
              width: 'auto',
              height: 300,
              theme: {
                shell: {
                  background: '#999999',
                  color: '#ffffff'
                },
                tweets: {
                  background: '#eeeeee',
                  color: '#000000',
                  links: '#666666'
                }
              },
              features: {
                scrollbar: true,
                loop: false,
                live: false,
                hashtags: true,
                timestamp: true,
                avatars: false,
                behavior: 'all'
              }
            }).render().setUser('$twitterhandle').start();
            </script>
        ";
        echo("</div><!--//end .twitter-feed -->");
    echo("</div><!--//end .twitter-search -->");
}

?>