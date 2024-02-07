<?php
/*
Plugin Name: Flour SEO
Description: A custom plugin to retrieve and display posts from the database within the admin menu. <html></ br></html>*To make SEO bread, you need flour and yoast.
Version: 0.1
Author: Alexios - Theocharis Koilias
*/


include( plugin_dir_path( __FILE__ ) . 'htaccess_functions.php' );
include( plugin_dir_path( __FILE__ ) . 'media_functions.php');
include( plugin_dir_path( __FILE__ ) . 'submenu1.php');
include( plugin_dir_path( __FILE__ ) . 'submenu2.php');

function my_custom_plugin_menu() {
    add_menu_page(
        'Flour SEO',
        'Flour SEO',
        'manage_options',
        'main',
        'seo_plugin_page',
        plugins_url('flour.png', __FILE__)
    );

    add_submenu_page(
        'main',
        'Post Handling',
        'Post Handling',
        'manage_options',
        'main', // Set the same slug as the parent menu
        'seo_plugin_page'
    );

    add_submenu_page(
        'main',
        'Submenu Page 1',
        'Posts\' Images Alt',
        'manage_options',
        'alt',
        'seo_submenu_page1'
    );

    add_submenu_page(
        'main',
        'Submenu Page 2',
        'Htaccess Features',
        'manage_options',
        'htaccess',
        'seo_submenu_page2'
    );
}



function seo_plugin_page() {
    echo '<div class="wrap">';
    echo '<h2>Post Overview</h2>';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_meta'])) {
        // Retrieve values from the form
        $post_id = absint($_POST['post_id']); // Ensure it's a positive integer
        $meta_title = sanitize_text_field($_POST['meta_title']);
        $meta_description = sanitize_text_field($_POST['meta_description']);
        $meta_keywords = sanitize_text_field($_POST['meta_keywords']);
        $post_slug = sanitize_text_field($_POST['post_slug']); // Retrieve the post slug
    
        // Check if the post exists with an exact ID match
        $post = get_post($post_id);
        if ($post && $post->ID == $post_id) {
            // Update post data
            if (!empty($post_slug)) {
                $post->post_name = $post_slug;
            }
    
            // Update post meta values if fields are not empty
            if (!empty($meta_title)) {
                update_post_meta($post_id, '_yoast_wpseo_title', $meta_title);
            }
            if (!empty($meta_description)) {
                update_post_meta($post_id, '_yoast_wpseo_metadesc', $meta_description);
            }
            if (!empty($meta_keywords)) {
                update_post_meta($post_id, '_yoast_wpseo_focuskw', $meta_keywords);
            }
    
            // Update the post in the database
            wp_update_post($post);
            if (empty($post_slug) && empty($meta_title) && empty($meta_description) && empty($meta_keywords))
            {
                echo '<div class="info"><p>No updates. All fields were empty.</p></div>';
            }
            else{
                echo '<div class="updated"><p>Meta information updated successfully!</p></div>';
            }
        } else {
            echo '<div class="error"><p>This post does not exist!</p></div>';
        }
    }

    echo '<form method="post" action="">';
    echo '<div style="display: flex; flex-direction: column;">';
    
    echo '<label for="post_id">Post ID:</label>';
    echo '<input type="text" name="post_id" id="post_id" required style="width: 500px;">';
    
    echo '<label for="meta_title">Meta Title:</label>';
    echo '<input type="text" name="meta_title" id="meta_title" style="width: 500px;">';
    
    echo '<label for="meta_description">Meta Description:</label>';
    echo '<textarea name="meta_description" id="meta_description" style="width: 500px; height: 100px;"></textarea>';

    
    echo '<label for="meta_keywords">Meta Keywords:</label>';
    echo '<input type="text" name="meta_keywords" id="meta_keywords" style="width: 500px;">';
    
    echo '<label for="post_slug">Post Slug:</label>';
    echo '<input type="text" name="post_slug" id="post_slug" style="width: 500px;">';
    
    echo '</div>';
    
    echo '<input type="submit" name="update_meta" value="Update">';
    echo '</form>';


    global $wpdb;
    
    $pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;
    $limit = 5;
    $offset = ($pagenum - 1) * $limit;
    
    // Retrieve posts using $wpdb
    $posts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type='post' AND post_status='publish' LIMIT $offset, $limit");
    
    echo '<div class="wrap">';
    $word_arr = [];
    $count_arr = [];
    $totalw_arr = [];
    $post_ids = [];
    $og_title = [];
    
    if ($posts) {
        echo '<table border="1">';
        echo '<tr>';
        echo '<th>Post ID</th>';
        echo '<th>Post Title</th>';
        echo '<th>Post Content</th>';
        echo '<th>Total Words</th>';
        echo '<th>Word Occurrences in Post Content</th>';
        echo '<th>Permalink</th>';
        echo '<th>Meta Title</th>';
        echo '<th>Meta Description</th>';
        echo '<th>Meta Keywords</th>';
        echo '</tr>';
    
        foreach ($posts as $post) {
            echo '<tr>';
            echo '<td>' . $post->ID . '</td>'; // New column for Post ID
            
            echo '<td>' . get_the_title($post) . '</td>';
            array_push($post_ids, $post->ID);
            array_push($og_title, get_the_title($post));
            // Post Content
            $post_content = apply_filters('the_content', $post->post_content);
            $post_content_short=$post_content;
            if (strlen($post_content)>600)
            {
                $post_content_short= mb_substr($post_content, 0 , 600)." ...";
            }
            echo '<td>' . $post_content_short . '</td>';

            // Split the post title into words
            $title_words = preg_split('/\s+/', get_the_title($post));
            for ($increm = 0; $increm < count($title_words); $increm++) {
                $title_words[$increm] = clean_word($title_words[$increm]);
            }

            // Total Words
            $content_words = mb_split('\s+', mb_strtolower(strip_tags($post_content)));
            $content_words = array_map('clean_word', $content_words);
            
            $post_words = count($content_words);
            echo '<td>' . $post_words . '</td>';
            array_push($totalw_arr, $post_words);
            
            // Word Occurrences (Title)
            echo '<td>';
            $added_count=0;
            $tonos_mapping = array(
                'ά' => 'α',
                'έ' => 'ε',
                'ί' => 'ι',
                'ό' => 'ο',
                'ύ' => 'υ',
                'ή' => 'η',
                'ώ' => 'ω',
                'ϊ' => 'ι',
                'ΐ' => 'ι',
                'ϋ' => 'υ',
                'ΰ' => 'υ',
            );
            
            foreach ($title_words as $word) {
                $escaped_word = preg_quote($word, '/');
                $pattern = "/\b$escaped_word\b/iu"; // Case-insensitive whole word match
                $lowercase_post_content = mb_strtolower(strip_tags($post_content));
                $lowercase_post_content = strtr($lowercase_post_content, $tonos_mapping);
                $count = preg_match_all($pattern, $lowercase_post_content, $matches);
                $word = mb_strtolower($word);
                if (!in_array($word, $word_arr)) {
                    echo $word . ': ' . $count . '<br>';
                    array_push($word_arr, $word);
                    array_push($count_arr, $count);
                }
            }
            array_push ($word_arr, "-divider-");
            array_push ($count_arr, "-divider-");
            echo '</td>';
            // Permalink
            echo '<td><a href="' . get_permalink($post) . '" target="_blank">Link</a></td>';

            // Meta Information
            $meta_title = get_post_meta($post->ID, '_yoast_wpseo_title', true);
            $meta_description = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
            $meta_keywords = get_post_meta($post->ID, '_yoast_wpseo_focuskw', true);
            echo '<td>' . $meta_title . '</td>';
            echo '<td>' . $meta_description . '</td>';
            echo '<td>' . $meta_keywords . '</td>';

            echo '</tr>';
        }

        echo '</table>';
    $total = $wpdb->get_var("SELECT COUNT(`ID`) FROM {$wpdb->prefix}posts WHERE post_type='post' AND post_status='publish'");
    $num_of_pages = ceil($total / $limit);
    $page_links = paginate_links(array(
        'base' => add_query_arg('pagenum', '%#%'),
        'format' => '',
        'prev_text' => __('&laquo;', 'aag'),
        'next_text' => __('&raquo;', 'aag'),
        'total' => $num_of_pages,
        'current' => $pagenum,
    ));

    if ($page_links) {
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
    }

    echo '</div>';
    } else {
        echo 'No posts found.';
    }

    check_word_percentage($word_arr, $count_arr, $totalw_arr);
        if (isset($_POST['db_connect_button'])) {
        $word_arr_new=db_connect($word_arr, $post_ids);
        autofill_keywords($post_ids, $word_arr_new);
    }
	?>
    <form method="POST" action="">
    <input type="hidden" name="db_connect_button" value="1">
    <button type="submit">Auto-assign post meta values</button>
    </form>
<?php
}


function check_word_percentage($word_arr, $count_arr, $totalw_arr)
{
    $word_string=[];
    $count_sum=[];
    $count_sum=array_fill(0, count($totalw_arr), 0);
    $j=0;
    for ($i=0; $i<count($word_arr); $i++)
    {
        if ($word_arr[$i]!= "-divider-")
        {
            $word_string[$j].= $word_arr[$i]." ";
            $count_sum[$j]+= $count_arr[$i];
        }
        else {
            $j++;
            continue;
        }
    }
    echo '<p>';

    for ($i=0; $i<count($totalw_arr); $i++)
    {
        $status_check='OK';
        $total_words=$totalw_arr[$i]>0?$totalw_arr[$i]:1;
        $percent= $count_sum[$i]/$total_words*100;
        if ($percent<5)
        {
            $status_check= 'INCREASE USAGE';
        }
        elseif ($percent>10)
        {
            $status_check= 'DECREASE USAGE';
        }
        echo 'Content word density: ( '.$word_string[$i].") | Percentage: ".$percent." --- ".$status_check . '<br>';
    }
    echo '</p>';
}


function clean_word($word) {
    // Remove special characters from the word
    $tonos_mapping = array(
        'ά' => 'α',
        'έ' => 'ε',
        'ί' => 'ι',
        'ό' => 'ο',
        'ύ' => 'υ',
        'ή' => 'η',
        'ώ' => 'ω',
        'ϊ' => 'ι',
        'ΐ' => 'ι',
        'ϋ' => 'υ',
        'ΰ' => 'υ',
    );

    // Remove non-alphanumeric characters, keeping Greek letters and tonos marks
    $cleaned_word = preg_replace('/[^\p{L}\p{N}α-ωίϊΐόάέύϋΰήώ]/u', '', $word);

    // Transliterate Greek letters with tonos marks to their base form using strtr
    $cleaned_word = strtr($cleaned_word, $tonos_mapping);
    //$cleaned_word = preg_replace('/[.,!;()"€%*?:|\'-]/', '', $word);
    return $cleaned_word;
}



add_action('admin_menu', 'my_custom_plugin_menu');
