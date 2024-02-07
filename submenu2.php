<?php
    function seo_submenu_page2() {
        echo '<div class="wrap">';
        echo '<h2>Htaccess File Features</h2>';
        
        echo '<form method="post" action="">';
        echo '<h3>Hotlinking</h3>';
        echo '<label for="restrict_hotlinking">Toggle Hotlinking Restriction to common files</label>';
        echo '<input type="checkbox" name="restrict_hotlinking" id="restrict_hotlinking">';
    
        // Hidden input field to store the modified URL
        echo '<input type="hidden" name="modified_url" id="modified_url" value="">';
    
        // JavaScript/jQuery for live update
        echo '<script>
        jQuery(document).ready(function($) {
            var admin_url = "' . esc_url(admin_url()) . '";
            var url_without_path = admin_url.replace("/wp-admin", "");
            $("#modified_url").val(url_without_path);
        });
        </script>';
    
    
        echo '<input type="submit" name="submit_htaccess" value="Apply">';
        echo '</form>';
    
        
        // Process form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Retrieve the modified URL from the hidden input field
            $modified_url = isset($_POST['modified_url']) ? sanitize_text_field($_POST['modified_url']) : '';
        
            // Check if the checkbox is checked
            $restrict_hotlinking = isset($_POST['restrict_hotlinking']);
            // Perform action based on checkbox state
            if ($restrict_hotlinking) {
                // Call search_modification() with the modified URL as a parameter
                if (!empty($modified_url)) {
                    search_modification($modified_url);
                }
            } 
            else if (!$restrict_hotlinking){
                if (!empty($modified_url)) {
                    remove_lines_from_htaccess($modified_url);
                }
            }
        }
        
        echo '<form method="post" action="">';
        echo '<h3>Compression Settings</h3>';
        echo '<label for="enable_compression">Toggle Compression</label>';
        echo '<input type="checkbox" name="enable_compression" id="enable_compression">';
        
        // Hidden input field to store the modified URL
        echo '<input type="hidden" name="compression_action" id="compression_action" value="">';
        
        // JavaScript/jQuery for live update
        echo '<script>
            jQuery(document).ready(function($) {
                // Update the hidden input with the initial checkbox state
                var compression_action = $("#enable_compression").prop("checked") ? "enable" : "disable";
                $("#compression_action").val(compression_action);
        
                // Bind an event handler for subsequent changes
                $("#enable_compression").on("change", function() {
                    // Update the hidden input with the current checkbox state
                    var compression_action = $("#enable_compression").prop("checked") ? "enable" : "disable";
                    $("#compression_action").val(compression_action);
                });
            });
        </script>';
    
        
        echo '<input type="submit" name="submit_compression" value="Apply">';
        echo '</form>';
        echo '<form method="post" action="">';
        echo '<h3>Caching Settings</h3>';
        echo '<label for="enable_caching">Toggle Caching</label>';
        echo '<input type="checkbox" name="enable_caching" id="enable_caching">';
        
        // Hidden input field to store the caching action
        echo '<input type="hidden" name="caching_action" id="caching_action" value="">';
        
        // JavaScript/jQuery for live update
        echo '<script>
            jQuery(document).ready(function($) {
                // Update the hidden input with the initial checkbox state
                var caching_action = $("#enable_caching").prop("checked") ? "enable" : "disable";
                $("#caching_action").val(caching_action);
        
                // Bind an event handler for subsequent changes
                $("#enable_caching").on("change", function() {
                    // Update the hidden input with the current checkbox state
                    var caching_action = $("#enable_caching").prop("checked") ? "enable" : "disable";
                    $("#caching_action").val(caching_action);
                });
            });
        </script>';
        
        echo '<input type="submit" name="submit_caching" value="Apply">';
        echo '</form>';
    
    ?>
    
    <!-- HTML form for adding or clearing redirect rules -->
    <form method="post" action="">
        <h3>Add or Clear Redirect Rule</h3>
        <label for="source_url">Source URL:</label>
        <input type="text" name="source_url" id="source_url" >
    
        <label for="destination_url">Destination URL:</label>
        <input type="text" name="destination_url" id="destination_url" >
    
        <input type="submit" name="submit_redirect_rule" value="Add Rule">
        <input type="submit" name="erase_redirect_rule" value="Clear Rule">
    </form>
        <?php
        setup_custom_404_page();
        // Process form submission for caching settings
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_caching'])) {
            // Retrieve the caching action from the hidden input field
            $caching_action = isset($_POST['caching_action']) ? sanitize_text_field($_POST['caching_action']) : '';
        
            // Perform action based on checkbox state
            if ($caching_action == 'enable') {
                // Write caching lines to .htaccess
                write_caching_rules();
            } else {
                // Erase caching lines from .htaccess
                erase_caching_rules();
            }
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['submit_redirect_rule'])) {
                // Add Redirect Rule
                add_redirect_rules();
            } elseif (isset($_POST['erase_redirect_rule'])) {
                // Erase Redirect Rule
                erase_redirect_rules();
            }
        }
        
        // Process form submission for compression settings
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_compression'])) {
            // Retrieve the compression action from the hidden input field
            $compression_action = isset($_POST['compression_action']) ? sanitize_text_field($_POST['compression_action']) : '';
        
            // Perform action based on checkbox state
            if ($compression_action === "enable") {
                // Call add_compression() when checkbox is checked
                add_compression();
            } else if ($compression_action === "disable") {
                // Call remove_compression() when checkbox is unchecked
                remove_compression();
            }
        }
            echo '</div>';
    }
