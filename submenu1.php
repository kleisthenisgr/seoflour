<?php
    function seo_submenu_page1() {
        echo '<div class="wrap">';
        echo '<h2>Images\' Alt Attributes</h2>';
        // ...Rest of code for the submenu page....
        img_alt($word_arr, $post_ids);
        echo '</div>';
    }
