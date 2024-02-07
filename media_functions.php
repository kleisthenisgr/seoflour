<?php
function img_alt($word_arr, $post_ids){
    $posts_with_thumbnail_id = get_posts(array(
        'meta_key' => '_thumbnail_id',
        'fields'   => 'ids', // Retrieve only post IDs
    ));

    // If there are matching post IDs
    if (!empty($posts_with_thumbnail_id)) {
        foreach ($posts_with_thumbnail_id as $post_id) {
            // Display post title and link
            $post_title = get_the_title($post_id);
            echo 'Post ID: ' . $post_id . ' - <a href="' . get_permalink($post_id) . '">' . $post_title . '</a><br>';

            // Display image information
            $image_id = get_post_thumbnail_id($post_id);

            // Display image
            echo 'Image ID: ' . $image_id . '<br>';
            
            // Display image title
            echo 'Image Title: ' . get_the_title($image_id) . '<br>';
            
            echo wp_get_attachment_image($image_id, 'thumbnail').'<br>';
            // Display image alt attribute
            $temp_alt = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
            echo 'Image Alt: ' . $temp_alt . '<br>';
            
            $image_url = wp_get_attachment_image_src($image_id, 'thumbnail')[0];
            $image_url= ensure_https($image_url);
            
            if (isset($_POST['show_description']) && empty($temp_alt)) {
                // Call the img_analyze() function when the button is pressed
                $temp_fetch= img_analyze($image_url);
                echo 'Description: ' . $temp_fetch;
                if ($temp_fetch!='' && !empty($temp_fetch))
                {
                    update_post_meta($image_id, '_wp_attachment_image_alt', $temp_fetch);
                }
            }
            ?>
            
            <script>
            function toggleDescription() {
                // Create a hidden form and submit it
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = window.location.href;
            
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'show_description';
                input.value = '1';
            
                form.appendChild(input);
                document.body.appendChild(form);
            
                form.submit();
            }
            </script>
            <?php
            
            echo '<hr>'; // Add a horizontal line for better separation
        }
    } else {
        echo 'No posts found with _thumbnail_id meta key.';
    }
     ?>
    <button onclick="toggleDescription()">Set Images Alt </button>

    <?php
    
    return $word_arr_new;
}

function img_analyze($image)
{
    $asticaAPI_key = '55E6ACE4-8157-4AE9-A7C0-B3020C09F460182D8D51-9C21-4588-A907-C9F1563360F3';
    $asticaAPI_timeout = 35;

    $asticaAPI_endpoint = 'https://vision.astica.ai/describe';
    $asticaAPI_modelVersion = '2.1_full';

    $asticaAPI_input = $image;
    $asticaAPI_visionParams = 'description';

    $asticaAPI_payload = [
        'tkn' => $asticaAPI_key,
        'modelVersion' => $asticaAPI_modelVersion,
        'visionParams' => $asticaAPI_visionParams,
        'input' => $asticaAPI_input,
    ];
    
    $result = asticaAPI($asticaAPI_endpoint, $asticaAPI_payload, $asticaAPI_timeout);

    if (isset($result['caption']) && isset($result['caption']['text']) && $result['caption']['text'] != '') {
        return  $result['caption']['text'];
    }
}


function asticaAPI($endpoint, $payload, $timeout = 15) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);        
        $result = json_decode($response, true);
        if(!isset($result['status'])) {
            $result = json_decode(json_decode($response), true);            
        }
        return $result;
}
    
function ensure_https($url) {
    // Check if the URL starts with "http://"
    if (strpos($url, 'http://') === 0) {
        // Replace "http://" with "https://"
        $url = 'https://' . substr($url, 7);
    }
    // Check if the URL starts with neither "http://" nor "https://"
    elseif (strpos($url, 'https://') !== 0) {
        // If not, prepend "https://"
        $url = 'https://' . $url;
    }

    return $url;
}

function db_connect($word_arr, $post_ids) {
    $host = '89.116.147.52';  // Replace with the actual host of your database
    $username = 'u448459142_words';  // Replace with your database username
    $password = '7258647@Bc';  // Replace with your database password
    $database = 'u448459142_words';  // Replace with your database name

    // Create a connection to the remote database
    $mysqli = new mysqli($host, $username, $password, $database);

    // Check the connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // echo get_dictionary_data($word_arr[42]).", ";
    
    echo "<br>---Connected successfully---<br>";
    
    $q= array();
    $i=0;
    $count=0;
    $new_gr_arr= array();
    $count_en=array();
    $count_gr=array();
    $unique_words=array();
    $check_new=true;
    $english_arr=array();
    foreach ($word_arr as $word)
    {   
        if (in_array($word, $unique_words)){
            continue;
        }
        else {
            $unique_words[]=$word;
        }
        
        $english=containsEnglishCharacters($word);
        if ($english && $word!='-divider-') {
            $check_eng= get_dictionary_data($word);
            $english_arr[$post_ids[$i]].= isset($check_eng)?$check_eng." ":'';
            continue;
        }
        if ($word== '-divider-')
        {
            $i++;
            $unique_words = [];
            if (end($english_arr)!= "-divider-" && !empty(end($english_arr)))
            {
                $count_en[]=$post_ids[$count];
            }
            $check_new=true;
            $count++;
            continue;
        }
        else{
            if ($check_new==true)
            {
                $q[$i] .= "'".$word."'";
                $check_new=false;
                $count_gr[]=$post_ids[$count];
            }
            else{
                $q[$i] .= ","."'".$word."'";
            }
        }
       
    }

    echo '<br>';

    $j=0;
    $keys1 = array_keys($q);
    echo '<br>';
    foreach ($q as $qs)
    {
        $sql = "SELECT DISTINCT(lemma),form FROM `words` WHERE `form` IN ({$qs}) AND `pos` = 'NOUN'
        ORDER BY FIELD(`form`, {$qs})
        ";
        // Execute the query
        $result = $mysqli->query($sql);
        
        // Check if there are results
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                $single_result= ($row['lemma']);
                if (strpos($single_result, ' ') !== false) {
                    $single_result=$row['form'];
                }
                //echo $single_result.'<br>';
                $new_gr_arr[$keys1[$j]].= $single_result." ";
            }
            $j++;
        } else {
            echo "0 results<br>";
        }
        echo '<br>';
    }

    $mysqli->close();
    echo '<hr>';
    
    $new_gr_arr= array_combine($count_gr, $new_gr_arr);
    $count_gr= array_keys($new_gr_arr);
    $count_en= array_keys($english_arr);
    $finalMergedArray = array();
    foreach ($count_gr as $key => $value) {
        if (isset($new_gr_arr[$value])) {
            $finalMergedArray[$value] = $new_gr_arr[$value];
        }
    }
    
    foreach ($count_en as $key => $value) {
        if (isset($english_arr[$value])) {
            $finalMergedArray[$value] = $english_arr[$value];
        }
    }

    return $finalMergedArray;
}

function get_dictionary_data($word) {
    // Construct the API URL with the given word
    $api_url = "https://api.dictionaryapi.dev/api/v2/entries/en/$word";

    // Make the API request
    $response = wp_remote_get($api_url);

    // Check if the request was successful
    if (is_wp_error($response)) {
        return false; // Handle the error as needed
    }

    // Parse the JSON response
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Check if JSON decoding was successful
    if ($data === null) {
        return false; // Handle the JSON decoding error
    }
    $selected_data= array();
    $selected_data = (
    $data[0]['meanings'][0]['partOfSpeech'] == 'noun' ||
    $data[0]['meanings'][0]['partOfSpeech'] == 'proper noun'
) ? $data[0]['word'] : null;

    // Return the decoded data
    return $selected_data;
}

function containsEnglishCharacters($string) {
    // Use a regular expression to check if the string contains any English characters
    return preg_match('/[a-zA-Z]/', $string) === 1;
}
