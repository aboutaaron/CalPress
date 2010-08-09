<?php
/*
Plugin Name: Get Author's Comments
Plugin URI: http://pioupioum.fr/wordpress/plugins/get-authors-comments.html
Version: 1.1.0
Description: Display or retrieve comments posted by a user.
Author: Mehdi Kabab
Author URI: http://pioupioum.fr/
*/
/*
Copyright (C) 2008 Mehdi Kabab <http://pioupioum.fr/>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Check if WP version is at least 2.7-beta*
if (version_compare($wp_version, '2.7-beta', '<'))
{
    return;
}

if (!defined('HTML'))
{
    define('HTML', 'HTML', false);
}

/**
 * Retrieves comments posted by a user.
 * 
 * Usage :
 * <ul>
 *   <li>Within loop, retrieves mehdi's comments. Without loop, retrieves 
 *      all comments that mehdi wrote.
 *     <ul>
 *       <li><code>ppm_get_author_comments('mehdi', 'foo@example.com')</code></li>
 *       <li><code>ppm_get_author_comments('mehdi', array('foo@example.com', 'bar@example.com'))</code></li>
 *       <li><code>ppm_get_author_comments('mehdi', 'foo@example.com', null, 'output=ARRAY_N')</code></li>
 *     </ul>
 *   </li>
 *   <li>Retrieves comments wrote by a user in the post of ID number 9:
 *     <ul>
 *       <li><code>ppm_get_author_comments('mehdi', 'foo@example.com', 9)</code></li>
 *       <li><code>ppm_get_author_comments('mehdi', 'foo@example.com', 9, 'orderby=content&number=1')</code></li>
 *     </ul>
 *   </li>
 * </ul>
 * 
 * @uses wp_list_comments
 * @global wpdb $wpdb
 * @global wpdb $post
 * 
 * @param string $author_name The author's name
 * @param string|array $author_email The author's e-mail(s)
 * @param int    $postID An optional post ID
 * @param array  $args Search and formatting options ({@link wp_list_comments()})
 * 
 * @return string|array If output parameter is HTML, returns a (x)HTML formated list.
 */
function ppm_get_author_comments($author_name, $author_email, $postID = null, $args = array())
{

    // {{{ Checks parameters

    $author_name = trim($author_name);
    if (empty($author_name))
    {
        return;
    }

    if (is_array($author_email))
    {
        $emailsCount = count($author_email);
        if (0 === $emailsCount)
        {
            return;
        }
    }

    // }}}

    global $wpdb, $post;

    $defaults = array(
        'all'       => false,
        'status'    => '',
        'orderby'   => 'date',
        'order'     => 'DESC',
        'number'    => '',
        'offset'    => '',
        'output'    => OBJECT
    );

    $args = wp_parse_args($args, $defaults);
    $args['all'] = (boolean) $args['all'];
    $buf  = '';

    // {{{ Prepares SQL query

    $sql  = 'SELECT * FROM ' . $wpdb->comments . ' WHERE comment_author = %s';

    if (false === $args['all'])
    {
        if (null === $postID)
        {
            if (in_the_loop())
            {
                $sql .= ' AND comment_post_ID = ' . $post->ID;
            }
        }
        else
        {
            $sql .= ' AND comment_post_ID = ' . absint($postID);
        }
    }

    switch (strtolower($args['status']))
    {
        case 'hold':
            $sql .= ' AND comment_approved = \'0\'';
            break;

        case 'approve':
            $sql .= ' AND comment_approved = \'1\'';
            break;

        case 'spam':
            $sql .= ' AND comment_approved = \'spam\'';
            break;

        default:
            $sql .= ' AND (comment_approved = \'0\' OR comment_approved = \'1\')';
            break;
    }

    $sql .= ' AND comment_author_email';
    if (isset($emailsCount))
    {
        if (1 < $emailsCount)
        {
            array_walk($author_email, array($wpdb, 'escape_by_ref'));
            $list = str_repeat("'%s',", $emailsCount);
            $sql .= ' IN ('
                 . substr_replace(vsprintf($list, $author_email), '', -1, 1)
                 . ')';
            unset($list);
        }
        else
        {
            $sql .= ' = ' . $wpdb->prepare('%s', $author_email[0]);
        }
    }
    else
    {
        $sql .= ' = ' . $wpdb->prepare('%s', $author_email);
    }

    $args['orderby'] = addslashes_gpc(urldecode($args['orderby']));
    $orderby_array   = explode(' ', $args['orderby']);

    if (empty($orderby_array))
    {
        $orderby_array[] = $args['orderby'];
    }

    $orderby_array   = array_map('strtolower', $orderby_array);
    $args['orderby'] = '';

    while (list(, $orderby) = each($orderby_array))
    {
        switch ($orderby)
        {
            case 'id': // compatibility with 1.0.0
            case 'comment_id':
                $orderby = 'comment_ID';
                break;

            case 'post': // compatibility with 1.0.0
            case 'post_id':
                $orderby = 'comment_post_ID';
                break;

            case 'status':
                $orderby = 'comment_approved';
                break;

            case 'rand':
                $orderby = 'RAND()';
                break;

            case 'type':
                $orderby = 'comment_type';
                break;

            case 'content':
                $orderby = 'comment_content';
                break;

            case 'date':
            default:
                $orderby = 'comment_date_gmt';
                break;
        }

        $args['orderby'] .= $orderby . ',';
    }
    unset($allowed_keys, $orderby, $orderby_array);

    $args['orderby'] = substr_replace($args['orderby'], '', -1, 1);

    $sql .= ' ORDER BY '
         . $args['orderby']
         . ' '
         . (('ASC' == strtoupper($args['order'])) ? 'ASC' : 'DESC');

    if ('' != $args['number'])
    {
        if ('' != $args['offset'])
        {
            $sql .= ' LIMIT ' . absint($args['offset']) . ',' . absint($args['number']);
        }
        else
        {
            $sql .= ' LIMIT ' . absint($args['number']);
        }
    }

    // }}}
    // {{{ Go!

    $comments = $wpdb->get_results($wpdb->prepare($sql, $author_name));

    // }}}
    // {{{ Determines output format

    switch (strtoupper($args['output']))
    {
        case ARRAY_A:
            $_comments = array();

            foreach ($comments as $k => $comment)
            {
                $_comments[$k] = get_object_vars($comment);
            }

            $buf = $_comments;
            break;

        case ARRAY_N:
            $_comments = array();

            foreach ($comments as $k => $comment)
            {
                $_comments[$k] = array_values(get_object_vars($comment));
            }

            $buf = $_comments;
            break;

        case 'HTML':
            unset($args['status'], $args['orderby'], $args['order'],
                  $args['number'], $args['offset'], $args['output']
            );
            ob_start();
            wp_list_comments($args, $comments);
            $buf = ob_get_clean();
            break;

        default:
        case OBJECT:
            $buf = $comments;
            break;
    }

    // }}}

    return apply_filters('ppm_get_author_comments', $buf);
}

/**
 * Displays comments posted by a user.
 *
 * @uses apply_filters() Calls 'ppm_get_author_comments' on author's comments before displaying
 * 
 * @param string $author_name The author's name
 * @param string|array $author_email The author's e-mail
 * @param int    $postID An optional post ID
 * @param array  $args Formatting options ({@link ppm_get_author_comments()} and {@link wp_list_comments()})
 * 
 * @return void
 */
function ppm_author_comments($author_name, $author_email, $postID = null, $args = array())
{
    $args     = wp_parse_args($args, array('output' => 'HTML'));
    $comments = apply_filters(
        'ppm_author_comments',
        ppm_get_author_comments($author_name, $author_email, $postID, $args)
    );

    echo $comments;
}