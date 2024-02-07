<?php
function search_modification($url) {
    $htaccess_path = ABSPATH . '.htaccess';

    // Extract parts of the URL using parse_url
    $parsed_url = parse_url($url);

    // Get the host (domain) from the URL
    $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';

    // Split the host into parts using dot as separator
    $host_parts = explode('.', $host);

    // Extract the last part as $secondpartvar
    $secondpartvar = end($host_parts);

    // Remove the last part to get the remaining parts as $firstpartvar
    array_pop($host_parts);
    $firstpartvar = implode('.', $host_parts);

    // Check if .htaccess file exists
    if (file_exists($htaccess_path)) {
        // Read the existing content
        $htaccess_content = file_get_contents($htaccess_path);

        // Check if the lines to be added are not already present
        $lines_to_add = "
RewriteCond %{HTTP_REFERER} !^$
RewriteCond %{HTTP_REFERER} !^https://(www\.)?$firstpartvar\.$secondpartvar/.*$ [NC]
RewriteRule \.(gif|jpg|swf|flv|png|mp3|pdf|rar|bmp)$ https://go.enterthe.shop/P2ka.gif [R=302,L]
";
        if (strpos($htaccess_content, $lines_to_add) === false) {
            // Append the lines to the content
            $htaccess_content .= $lines_to_add;

            // Save the updated content back to the .htaccess file
            file_put_contents($htaccess_path, $htaccess_content);

            echo '<h1>.htaccess Contents After Modification</h1>';
            echo '<pre>';
            echo htmlspecialchars($htaccess_content);
            echo '</pre>';

        } else {
            echo '<p>The lines are already present in the .htaccess file.</p>';
        }
    } else {
        echo '<p>.htaccess file not found.</p>';
    }
}

function remove_lines_from_htaccess($url) {
    $htaccess_path = ABSPATH . '.htaccess';

    // Extract parts of the URL using parse_url
    $parsed_url = parse_url($url);

    // Get the host (domain) from the URL
    $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';

    // Split the host into parts using dot as separator
    $host_parts = explode('.', $host);

    // Extract the last part as $secondpartvar
    $secondpartvar = end($host_parts);

    // Remove the last part to get the remaining parts as $firstpartvar
    array_pop($host_parts);
    $firstpartvar = implode('.', $host_parts);

    // Lines to remove
    $lines_to_remove = "
RewriteCond %{HTTP_REFERER} !^$
RewriteCond %{HTTP_REFERER} !^https://(www\\.)?$firstpartvar\\.$secondpartvar/.*$ [NC]
RewriteRule \\.(gif|jpg|swf|flv|png|mp3|pdf|rar|bmp)$ https://go.enterthe.shop/P2ka.gif [R=302,L]
";
    
    // Check if .htaccess file exists
    if (file_exists($htaccess_path)) {
        $found_match= true;
        
        // Read the existing content
        $htaccess_content = file_get_contents($htaccess_path);
        
        if (strpos($htaccess_content, $lines_to_remove) == false) {
            $found_match= false;
        }

        // Use preg_replace to remove the lines with variations in formatting
        $htaccess_content = preg_replace("/" . preg_quote($lines_to_remove, '/') . "/s", '', $htaccess_content);

        // Save the updated content back to the .htaccess file
        file_put_contents($htaccess_path, $htaccess_content);

        if ($found_match){
            echo '<h1>.htaccess Contents After Removal</h1>';
            echo '<pre>';
            echo htmlspecialchars($htaccess_content);
            echo '</pre>';
            echo '<p>The lines have been successfully removed from the .htaccess file.</p>';
        }
        else {
            echo '<p>No changes made to the file.</p>';
        }
    } else {
        echo '<p>.htaccess file not found.</p>';
    }
}

function add_compression()
{
    $htaccess_path = ABSPATH . '.htaccess';

    // Constant text to be added
    $compression_text = '
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
';

    // Check if .htaccess file exists
    if (file_exists($htaccess_path)) {
        // Read the existing content
        $htaccess_content = file_get_contents($htaccess_path);

        // Check if the lines to be added are not already present
        if (strpos($htaccess_content, $compression_text) === false) {
            // Append the lines to the content
            $htaccess_content .= $compression_text;

            // Save the updated content back to the .htaccess file
            file_put_contents($htaccess_path, $htaccess_content);

            echo '<h1>.htaccess Contents After Modification</h1>';
            echo '<pre>';
            echo htmlspecialchars($htaccess_content);
            echo '</pre>';
        } else {
            echo '<p>The compression lines are already present in the .htaccess file.</p>';
        }
    } else {
        echo '<p>.htaccess file not found.</p>';
    }
}

function remove_compression()
{
    $htaccess_path = ABSPATH . '.htaccess';

    // Lines to be removed
    $compression_text = '
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
';

    // Check if .htaccess file exists
    if (file_exists($htaccess_path)) {
        // Read the existing content
        $htaccess_content = file_get_contents($htaccess_path);

        // Check if the lines to be removed are present
        if (strpos($htaccess_content, $compression_text) !== false) {
            // Remove the lines from the content
            $htaccess_content = str_replace($compression_text, '', $htaccess_content);

            // Save the updated content back to the .htaccess file
            file_put_contents($htaccess_path, $htaccess_content);
            echo '<p>Lines were removed.</p>';
            echo '<h1>.htaccess Contents After Modification</h1>';
            echo '<pre>';
            echo htmlspecialchars($htaccess_content);
            echo '</pre>';
        } else {
            echo '<p>The compression lines are not present in the .htaccess file.</p>';
        }
    } else {
        echo '<p>.htaccess file not found.</p>';
    }
}

function setup_custom_404_page() {
            
    // Display the file upload form
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<label for="custom_404_file"><h3>Upload Custom 404 Page (HTML file):</h3></label>';
    echo '<input type="file" name="custom_404_file" id="custom_404_file"/>';
    echo '<input type="submit" name="submit_404_page" value="Upload and Set"/>';
    echo '<input type="submit" name="clear_404_settings" value="Clear 404 Setting"/>';
    echo '</form>';
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle clear 404 settings
        if (isset($_POST['clear_404_settings'])) {
            clear_custom_404_page();
            return; // Exit to avoid processing the file upload logic
        }


        // Handle file upload
        if (!empty($_FILES['custom_404_file']['tmp_name'])) {
            // Specify the custom folder in the root directory
            $upload_dir = ABSPATH . 'custom_404_upload';

            // If the folder doesn't exist, create it
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir);
            }

            // Sanitize and get the file name
            $file_name = sanitize_file_name($_FILES['custom_404_file']['name']);
            $file_path = trailingslashit($upload_dir) . $file_name;

            // Move the uploaded file to the destination
            move_uploaded_file($_FILES['custom_404_file']['tmp_name'], $file_path);
            echo 'File successfully uploaded and set!';

        } else {
            echo '<p>Please choose a file to upload.</p>';
        }
    }


}

function clear_custom_404_page() {
    // Specify the custom folder in the root directory
    $upload_dir = ABSPATH . 'custom_404_upload';

    // Check if the folder exists
    if (file_exists($upload_dir)) {
        // Get all files in the custom folder
        $files = glob("$upload_dir/*.html");

        // Delete each file in the folder
        foreach ($files as $file) {
            unlink($file);
        }
        echo 'Custom 404 entry deleted.';
    } else {
        // Display message if the custom folder doesn't exist
        echo '<p>Custom 404 folder not found.</p>';
    }
}

add_action('template_redirect', 'redirect_to_custom_404');

function redirect_to_custom_404() {
    // Path to the custom 404 upload folder
    $upload_dir = ABSPATH . 'custom_404_upload';

    // Get the requested URL
    $requested_url = esc_url(home_url($_SERVER['REQUEST_URI']));

    // Check if the folder exists and the requested URL does not exist
    if (file_exists($upload_dir) && !file_exists($requested_url)) {
        // Get all files in the custom folder
        $files = glob("$upload_dir/*.html");

        // Check if any HTML file exists in the custom folder
        if (!empty($files) && is_404()) {
            // Redirect to the first HTML file found in the custom folder
            $custom_404_page = esc_url(home_url("/custom_404_upload/" . basename($files[0])));
            wp_redirect($custom_404_page);
            exit();
        }
    }
}

function autofill_meta($ids ,$title )
{
    $uniqueOccurrences = array();
    $uniqueArray = array(); 

    foreach ($title as $word) {
        // Check if the word contains the string '-divider-'
        if (strpos($word, '-divider-') !== false) {
            // If it does, add it to the unique occurrences array
            $uniqueArray[] = $word;
        } elseif (!isset($uniqueOccurrences[$word])) {
            // If the word is not in the associative array, add it to the unique occurrences array
            $uniqueOccurrences[$word] = true;
            $uniqueArray[] = $word;
        }
    }

    $i=0;
    $j=0;
    $new_title=[];
    foreach ($uniqueArray as $tit)
    {
        if ($tit!='-divider-')
        {
            if ($j<5){
                $j++;
                $new_title[$i].=$tit." ";
            }
            
        }
        else {
            $i++;
            $j=0;
            continue;
        }
    }

    ?>
    <form method="post" action="">
    <input type="submit" name="autofill_button" value="Autoset META Values">
    </form>
    <?php
        if (isset($_POST['autofill_button'])) {
            autofill_keywords($ids, $title);
        }
}

function autofill_keywords($ids, $new_title)
{
    // Check the cookie value using PHP
    // $confirmationCookie = isset($_COOKIE['confirmation']) ? $_COOKIE['confirmation'] : '';

    // Check if the cookie doesn't exist or has a value other than "true"
    // if ($confirmationCookie !== 'true') {
    //     echo '<script>
    //         let isConfirmed = window.confirm("Are you sure you want to overwrite the values? This is NOT reversible. Cancel if unsure.");
    //         document.cookie = "confirmation=" + (isConfirmed ? "true" : "false") + "; path=/";
    //     </script>';
    // }
    
    // Check the cookie value again using PHP
    // $confirmationCookie = isset($_COOKIE['confirmation']) ? $_COOKIE['confirmation'] : '';
    // echo 'THIS COOKIE\'S VALUE iS: '.$_COOKIE['confirmation'];
    // Check if the cookie value is "true"
    
    // if ($confirmationCookie === 'true') {
        for ($i = 0; $i < count($ids); $i++) {
            //echo $ids[$i] . ' ' . $new_title[$i] . '<br>';
            $key=$ids[$i];
            $new_title[$key]= trim($new_title[$key]);
            if (empty($new_title[$key]) || $new_title[$key]=='' || $new_title[$key]==false){
                continue;
            }
            $new_title[$key]= clean_phrase($new_title[$key]);
            update_post_meta($ids[$i], '_yoast_wpseo_focuskw', $new_title[$key]);
            
            update_post_meta($ids[$i], '_yoast_wpseo_title', '%%title%%');
            update_post_meta($ids[$i], '_yoast_wpseo_metadesc', '%%excerpt%%');
        }

        // Reset the cookie to "false" after executing the loop
        // setcookie('confirmation', 'false', time() + 3600, '/');
    // }
}

function clean_phrase($phrase) {
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
        //' ' => ', '
    );
    $phrase = strtr($phrase, $tonos_mapping);
    return $phrase;
}

function erase_caching_rules() {
    // Read .htaccess file
    $htaccess_path = ABSPATH . '.htaccess';

    // Define the caching rules to be removed
    $lines_to_remove = '
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresDefault "access plus 1 seconds"
  ExpiresByType text/html "access plus 1 seconds"
  ExpiresByType image/x-icon "access plus 2592000 seconds"
  ExpiresByType image/gif "access plus 2592000 seconds"
  ExpiresByType image/jpeg "access plus 2592000 seconds"
  ExpiresByType image/png "access plus 2592000 seconds"
  ExpiresByType text/css "access plus 604800 seconds"
  ExpiresByType text/javascript "access plus 86400 seconds"
  ExpiresByType application/x-javascript "access plus 86400 seconds"
</IfModule>';

    if (file_exists($htaccess_path)) {
        // Read the existing content
        $htaccess_content = file_get_contents($htaccess_path);

        // Check if the caching rules exist in .htaccess
        $pos = strpos($htaccess_content, $lines_to_remove);

        if ($pos !== false) {
            // Remove caching rules from .htaccess
            $htaccess_content = substr_replace($htaccess_content, '', $pos, strlen($lines_to_remove));

            // Save the updated content back to the .htaccess file
            file_put_contents($htaccess_path, $htaccess_content);

            echo '<h1>.htaccess Contents After Removal</h1>';
            echo '<pre>';
            echo htmlspecialchars($htaccess_content);
            echo '</pre>';
            echo '<p>The lines have been successfully removed from the .htaccess file.</p>';
        } else {
            echo '<p>No changes made to the file. Caching rules not found.</p>';
        }
    } else {
        echo '<p>.htaccess file not found.</p>';
    }
}

function write_caching_rules() {
    $caching_rules = '
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresDefault "access plus 1 seconds"
  ExpiresByType text/html "access plus 1 seconds"
  ExpiresByType image/x-icon "access plus 2592000 seconds"
  ExpiresByType image/gif "access plus 2592000 seconds"
  ExpiresByType image/jpeg "access plus 2592000 seconds"
  ExpiresByType image/png "access plus 2592000 seconds"
  ExpiresByType text/css "access plus 604800 seconds"
  ExpiresByType text/javascript "access plus 86400 seconds"
  ExpiresByType application/x-javascript "access plus 86400 seconds"
</IfModule>';

    $htaccess_path = ABSPATH . '.htaccess';
    
        if (file_exists($htaccess_path)) {
        // Read the existing content
        $htaccess_content = file_get_contents($htaccess_path);

        // Check if the lines to be added are not already present
        if (strpos($htaccess_content, $caching_rules) === false) {
            // Append the lines to the content
            $htaccess_content .= $caching_rules;

            // Save the updated content back to the .htaccess file
            file_put_contents($htaccess_path, $htaccess_content);

            echo '<h1>.htaccess Contents After Modification</h1>';
            echo '<pre>';
            echo htmlspecialchars($htaccess_content);
            echo '</pre>';
        } else {
            echo '<p>The caching lines are already present in the .htaccess file.</p>';
        }
    } else {
        echo '<p>.htaccess file not found.</p>';
    }
}

function add_redirect_rules() {
    // Retrieve values from the form
    $source_url = isset($_POST['source_url']) ? sanitize_text_field($_POST['source_url']) : '';
    $destination_url = isset($_POST['destination_url']) ? sanitize_text_field($_POST['destination_url']) : '';

    // Check if both input fields are filled
    if (!empty($source_url) && !empty($destination_url)) {
        $source_url=get_relative_url($source_url);
        // Create the redirect rule
        $redirect_rule = "
#Redirection-Start            
<IfModule mod_rewrite.c>
  RewriteEngine On
  Redirect 301 \"$source_url\" \"$destination_url\"
</IfModule>
#Redirection-End";

        // Define the file path using ABSPATH
        $htaccess_path = ABSPATH . '.htaccess';

        // Append the redirect rule to .htaccess
        file_put_contents($htaccess_path, $redirect_rule, FILE_APPEND);

        echo '<p>Redirect rule added successfully!</p>';
    } else {
        echo '<p>Please fill in both input fields to add a redirect rule.</p>';
    }
}

function erase_redirect_rules() {
    // Define the file path using ABSPATH
    $htaccess_path = ABSPATH . '.htaccess';

    // Define the start and end markers
    $start_marker = '#Redirection-Start';
    $end_marker = '#Redirection-End';

    // Read the content of .htaccess
    $htaccess_content = file_get_contents($htaccess_path);

    // Find the position of the start marker
    $start_position = strpos($htaccess_content, $start_marker);

    // Find the position of the end marker after the start marker
    $end_position = strpos($htaccess_content, $end_marker, $start_position);

    // Check if both markers are found
    if ($start_position !== false && $end_position !== false) {
        // Extract the block of code between the markers
        $block_to_remove = substr($htaccess_content, $start_position, $end_position - $start_position + strlen($end_marker));

        // Remove the block from .htaccess
        $htaccess_content = str_replace($block_to_remove, '', $htaccess_content);

        // Save the updated content back to .htaccess
        file_put_contents($htaccess_path, $htaccess_content);

        echo '<p>Redirect rules erased successfully!</p>';
    } else {
        echo '<p>No changes made. Redirect rules not found in .htaccess.</p>';
    }
}

function get_relative_url($url) {
    $home_url = home_url('/');

    // Check if the URL starts with the home_url
    if (strpos($url, $home_url) === 0) {
        // Remove the home_url from the beginning of the URL
        $relative_url = substr($url, strlen($home_url));
        return "/".$relative_url;
    }

    // If the URL doesn't start with the home_url, return the original URL
    return $url;
}

