<?php

class YLSY_PermalinkRedirect {
    function admin_menu() {
        add_options_page('Permalink Redirect Manager', 'Permalink Redirect', 5,
            'permalink-redirect', array($this, 'admin_page'));
    }

    function admin_page() {
        global $wp_version;

        // If we are updating, we will flush all the rewrite rules to force the 
        // old structure to be added.
        if (isset($_GET['updated']) ||
            isset($_GET['settings-updated'])) {
            $this->regenerate_rules();
        }

        $options = array('feedburner', 'feedburnerbrand', 'hostname', 
            'oldstruct', 'skip', 'newpath');
        $optionvars = array();
        foreach ($options as $option) {
            $$option = get_option("permalink_redirect_$option");
            if (!$$option) {
                $$option = ($option == 'feedburnerbrand') ? 
                    'feeds.feedburner.com' : '';
            }
            if ($wp_version < '2' && !$$option) {
                add_option("permalink_redirect_$option", $$option);
            }
            $optionvars[] = "permalink_redirect_$option";
        }
        $home = parse_url(get_option('home'));
?>
<div class="wrap">
    <h2>Permalink Redirect Manager</h2>
    <form action="options.php" method="post">
        <fieldset class="options">
            <legend>Paths to be skipped</legend>
            <p>Separate each entry with a new line. Matched with regular expression.</p>
            <textarea name="permalink_redirect_skip" style="width:98%;" rows="5"><?php echo htmlspecialchars($skip); ?></textarea>

            <legend style="padding-top:20px">Path pairs to redirect from and to</legend>
            <p>Separate each entry with a new line. Each line is [from]&lt;spaces&gt;[to].</p>
            <textarea name="permalink_redirect_newpath" style="width:98%;" rows="5"><?php echo htmlspecialchars($newpath); ?></textarea>
            <table class="optiontable" style="padding-top:20px">
                <tr valign="top">
                    <th scope="row">Old Permalink Structures:</th> 
                    <td><textarea name="permalink_redirect_oldstruct" id="permalink_redirect_oldstruct" style="width:98%" rows="3"><?php echo htmlspecialchars($oldstruct); ?></textarea><br/><small><a href="http://codex.wordpress.org/Using_Permalinks">Available tags</a>. One Permalink Structure per line. Current permalink structure: <a href="#" onclick="document.getElementById('permalink_redirect_oldstruct').value = '<?php echo htmlspecialchars(get_option('permalink_structure')); ?>';return false;"><code><?php echo htmlspecialchars(get_option('permalink_structure')); ?></code></a></small></td>
                </tr>
                <tr>
                    <th scope="row">FeedBurner Redirect:</th> 
                    <td>http://<input name="permalink_redirect_feedburnerbrand" type="text" id="permalink_redirect_feedburnerbrand" value="<?php print htmlspecialchars($feedburnerbrand); ?>" size="20"/>/<input name="permalink_redirect_feedburner" type="text" id="permalink_redirect_feedburner" value="<?php echo htmlspecialchars($feedburner) ?>" size="20" /></td> 
                </tr> 
                <tr>
                    <th scope="row">Hostname Redirect:</th> 
                    <td><input name="permalink_redirect_hostname" type="checkbox" id="permalink_redirect_hostname" value="1"<?php if ($hostname) { ?> checked="checked"<?php } ?>/> Redirect if hostname is not <code><?php echo htmlspecialchars($home['host']); ?></code>.</td> 
                </tr>
            </table>
        </fieldset>
        <p class="submit">
            <input type="submit" name="Submit" value="<?php echo $GLOBALS['tr_Update_Options'] ?> &raquo;" />
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="<?php echo join(',', $optionvars); ?>"/>
            <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('update-options'); } ?>
        </p>
    </form>
</div>
<?php
    }

    function check_hostname() {
        if (! get_option('permalink_redirect_hostname')) {
            return false;
        }
        $requested = $_SERVER['HTTP_HOST'];
        $home = parse_url(get_option('home'));
        return $requested != $home['host'];
    }

    function execute() {
        global $wp_query;
		echo "It's here";
        $this->execute2($wp_query);
    }

    function execute2($query, $testold=true) {
        $req_uri = $_SERVER['REQUEST_URI'];
		
		echo "\$req_uri: ".$req_uri;

        if ($query->is_trackback || 
            $query->is_search || 
            $query->is_comments_popup ||
            $query->is_robots ||
            $this->is_skip($req_uri))
        {
            return;
        }

		$this->regenerate_rules();
        $this->redirect_newpath($req_uri);
        $this->redirect_feedburner($query);

        if ($query->is_404) {
            if ($testold) {
                $this->redirect_old_permalink($req_uri);
            }
            return;
        }

        if (($req_uri = @parse_url($_SERVER['REQUEST_URI'])) === false) {
            return;
        }

        $req_path = $req_uri['path'];
        $new_uri = $this->guess_permalink($query);
        if (!$new_uri) {
            return;
        }
        $permalink = @parse_url($new_uri);

        // WP2.1: If a static page has been set as the front-page, we'll get 
        // empty string here.
        if (!$permalink['path']) {
            $permalink['path'] = '/';
        }
        if (($req_path != $permalink['path']) || $this->check_hostname()) {
            wp_redirect($new_uri, 301);
        }
    }

    function guess_permalink($query) {
        $haspost = count($query->posts) > 0;
        $has_ut = function_exists('user_trailingslashit');

        if (get_query_var('m')) {
            // Handling special case with '?m=yyyymmddHHMMSS'
            // Since there is no code for producing the archive links for
            // is_time, we will give up and not trying any redirection.
            $m = preg_replace('/[^0-9]/', '', get_query_var('m'));
            switch (strlen($m)) {
                case 4: // Yearly
                    $link = get_year_link($m);
                    break;
                case 6: // Monthly
                    $link = get_month_link(substr($m, 0, 4), substr($m, 4, 2));
                    break;
                case 8: // Daily
                    $link = get_day_link(substr($m, 0, 4), substr($m, 4, 2),
                                         substr($m, 6, 2));
                    break;
                default:
                    return false;
            }
        } elseif (($query->is_single || $query->is_page) && $haspost) {
            $post = $query->posts[0];
            $link = get_permalink($post->ID);
            $page = get_query_var('page');
            if ($page && $page > 1) {
                $link = trailingslashit($link) . "$page";
                if ($has_ut) {
                    $link = user_trailingslashit($link, 'paged');
                } else {
                    $link .= '/';
                }
            }
            // WP2.2: In Wordpress 2.2+ is_home() returns false and is_page() 
            // returns true if front page is a static page.
            if ($query->is_page && ('page' == get_option('show_on_front')) && 
                $post->ID == get_option('page_on_front'))
            {
                $link = trailingslashit($link);
            }
        } elseif ($query->is_author && $haspost) {
            global $wp_version;
            if ($wp_version >= '2') {
                $author = get_userdata(get_query_var('author'));
                if ($author === false)
                    return false;
                if (function_exists('get_author_posts_url')) {
                    $link = get_author_posts_url($author->ID,
                        $author->user_nicename);
                } else {
                    $link = get_author_link(false, $author->ID,
                        $author->user_nicename);
                }
                // XXX: get_author_link seems to always return one with 
                // trailing slash. We have to call user_trailingslashit to 
                // make it right.
                if ($has_ut) {
                    $link = user_trailingslashit($link);
                }
            } else {
                // XXX: get_author_link() bug in WP 1.5.1.2
                //      s/author_nicename/user_nicename/
                global $cache_userdata;
                $userid = get_query_var('author');
                $link = get_author_link(false, $userid,
                    $cache_userdata[$userid]->user_nicename);
            }
        } elseif ($query->is_category && $haspost) {
            $link = get_category_link(get_query_var('cat'));
        } elseif ($query->is_tag && $haspost) {
            $link = get_tag_link(get_query_var('tag_id'));
        } elseif ($query->is_day && $haspost) {
            $link = get_day_link(get_query_var('year'),
                                 get_query_var('monthnum'),
                                 get_query_var('day'));
        } elseif ($query->is_month && $haspost) {
            $link = get_month_link(get_query_var('year'),
                                   get_query_var('monthnum'));
        } elseif ($query->is_year && $haspost) {
            $link = get_year_link(get_query_var('year'));
        } elseif ($query->is_home) {
            // WP2.1: Handling "Posts page" option. In WordPress 2.1 is_home() 
            // returns true and is_page() returns false if home page has been 
            // set to a page, and we are getting the permalink of that page 
            // here.
            if ((get_option('show_on_front') == 'page') &&
                ($pageid = get_option('page_for_posts'))) 
            {
                $link = trailingslashit(get_permalink($pageid));
            } else {
                $link = trailingslashit(get_option('home'));
            }
        } else {
            return false;
        }

        if ($query->is_paged) {
            $paged = get_query_var('paged');
            if ($paged) {
                $link = trailingslashit($link) . "page/$paged";
                if ($has_ut) {
                    $link = user_trailingslashit($link, 'paged');
                } else {
                    $link .= '/';
                }
            }
        }

        if ($query->is_feed) {
            $link = trailingslashit($link) . 'feed';
            if ($has_ut) {
                $link = user_trailingslashit($link, 'feed');
            } else {
                $link .= '/';
            }

        }

        return $link;
    }

    function is_feedburner() {
        return strncmp('FeedBurner/', $_SERVER['HTTP_USER_AGENT'], 11) == 0;
    }

    function is_skip($path) {
        $permalink_redirect_skip = get_option('permalink_redirect_skip');
        $permalink_redirect_skip = explode("\n", $permalink_redirect_skip);

        // Apply 'permalink_redirect_skip' filter so other plugins can
        // customise the skip behaviour. (Denis de Bernardy @ 2006-04-23)
        $permalink_redirect_skip = apply_filters('permalink_redirect_skip', 
            $permalink_redirect_skip);

        foreach ($permalink_redirect_skip as $skip) {
            $skip = trim($skip);
            if ($skip && ereg($skip, $path))
                return true;
        }

        return false;
    }

    function redirect_feedburner($query) {
        // Check whether we need to do redirect for FeedBurner.
        // NOTE this might not always get executed. For feeds,
        // WP::send_headers() might send back a 304 before template_redirect
        // action can be called.
        global $withcomments;

        if ($query->is_feed && !$query->is_archive && !$withcomments) {
            if (($feedburner = get_option('permalink_redirect_feedburner')) &&
                (strncmp('FeedBurner/', $_SERVER['HTTP_USER_AGENT'], 11) != 0))
            {
                $brand = get_option('permalink_redirect_feedburnerbrand');
                $brand = $brand ? $brand : 'feeds.feedburner.com';
                wp_redirect("http://$brand/$feedburner", 302);
            }
        }
    }

    // Static page redirect contributed by Sergey Menshikov.
    function redirect_newpath($path) {
        if (1/*$newpathlist = get_option('permalink_redirect_newpath')*/) {
            //$newpathlist = explode("\n", $newpathlist);
            //foreach ($newpathlist as $newpath) {
				$newpath = $path;
                $pair = preg_split('/\s+/', trim($newpath));
                if ($pair[0] == $path) {
                    wp_redirect($pair[1], 301);
                }
            //}
        }
    }

    /**
     * Called when the main execute function gets a 404 to check against old 
     * permalink structures and perform redirect if an old post can be 
     * matched.
     */
   	function redirect_old_permalink($req_uri) {
        global $wp_query, $wp_rewrite;
        global $wp_version;

        $rules = get_option('permalink_redirect_rules');
        if (!$rules) {
            return;
        }

        // Backing up the rewrite object for you, imperative programmers!
        $wp_rewrite_old = $wp_rewrite;

        // Unsetting the globals. Argh! Evil global variables!
        foreach ($wp_query->query_vars as $key => $val) {
            unset($GLOBALS[$key]);
        }

        // Going through the rules.
        foreach ($rules as $rules2) {
            $wp2 = new WP();
            $wp_rewrite = new YLSY_Rewrite();
            $wp_rewrite->index = $wp_rewrite_old->index;
            $wp_rewrite->rules = $rules2;

            $wp2->parse_request();
            if (isset($wp2->query_vars['error']) && 
                ($wp2->query_vars['error'] == 404)) 
            {
                continue;
            }
            $query = new WP_Query();
            if ($wp_version >= '2.1') {
                $posts = $query->query($wp2->query_vars);
            } else {
                $wp2->build_query_string();
                $posts = $query->query($wp2->query_string);
            }
            if (count($posts) > 0) {
                $wp_rewrite = $wp_rewrite_old;
                $this->execute2($query, false);
                return;
            }
        }

        // Restoring global variables. We don't bother to reset the other 
        // variables as we are going to do a 404 anyway.
        $wp_rewrite = $wp_rewrite_old;
    }

    /**
     * This function is called after someone saved the old permalink 
     * structure. It will create cached version of rewrite rules from the 
     * old structure.
     */
    function regenerate_rules() {
        global $wp_rewrite;
        $oldstruct = get_option('permalink_structure'); //get_option('permalink_redirect_oldstruct');
		echo "\$oldstruct: ".$oldstruct;
        if ($oldstruct) {
            $rules = array();
            $oldstruct = explode("\n", $oldstruct);
            foreach ($oldstruct as $item) {
                $rules2 = $wp_rewrite->generate_rewrite_rule(trim($item), 
                    false, false, false, true);
                $rules3 = array();
                foreach ($rules2 as $match => $query) {
                    $query = preg_replace('/\$(\d+)/', '\$matches[\1]', $query);
                    $rules3[$match] = $query;
                }
                $rules[] = $rules3;
            }
            update_option('permalink_redirect_rules', $rules);
        } else {
            delete_option('permalink_redirect_rules');
        }
    }
}

/**
 * I am a dummy class to simulate the WP_Rewite class, but only has one 
 * method implemented.
 */
class YLSY_Rewrite {
    function wp_rewrite_rules() {
        return $this->rules;
    }
}

if (!function_exists('wp_redirect')) {
    function wp_redirect($location, $status=302) {
        global $is_IIS;

        $location = apply_filters('wp_redirect', $location, $status);
        $status = apply_filters('wp_redirect_status', $status, $location);

        if (!$location)
            return false;

        if (function_exists('wp_sanitize_redirect')) {
            $location = wp_sanitize_redirect($location);
        }

        if ($is_IIS) {
            header("Refresh: 0;url=$location");
        } else {
            status_header($status);
            header("Location: $location");
        }
    }
}

$_permalink_redirect = new YLSY_PermalinkRedirect();
add_action('admin_menu', array($_permalink_redirect, 'admin_menu'));
add_action('init', array($_permalink_redirect, 'execute'));		//template_redirect
?>