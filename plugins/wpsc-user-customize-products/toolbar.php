<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../wp-config.php");
}

global $wpsc_wordpress_upload_dir;

if (!empty($_FILES)) {

    /* Handles the error output. This error message will be sent to the uploadSuccess event handler.  The event handler
    will have to check for any error messages and react as needed. */
    function HandleError($message) {
            echo '<script type="text/javascript">alert("'.$message.'");</script>'.$message.'';
    }

    // Code for Session Cookie workaround
            if (isset($_POST["PHPSESSID"])) {
                    session_id($_POST["PHPSESSID"]);
            } else if (isset($_GET["PHPSESSID"])) {
                    session_id($_GET["PHPSESSID"]);
            }

            session_start();

    // Check post_max_size (http://us3.php.net/manual/en/features.file-upload.php#73762)
            $POST_MAX_SIZE = @ini_get('post_max_size');
            if(@$POST_MAX_SIZE == NULL || $POST_MAX_SIZE < 1) {$POST_MAX_SIZE=9999999999999;};
            $unit = strtoupper(substr($POST_MAX_SIZE, -1));
            $multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

            if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
                    header("HTTP/1.1 500 Internal Server Error"); // This will trigger an uploadError event in SWFUpload
                    _e("POST exceeded maximum allowed size.", 'wpstorecart');
                    exit(0);
            }

    // Settings
            $save_path = $wpsc_wordpress_upload_dir['basedir']. '/wpstorecart/';
            $upload_name = "Filedata";
            $max_file_size_in_bytes = 2147483647;				// 2GB in bytes
            $valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';				// Characters allowed in the file name (in a Regular Expression format)

    // Other variables	
            $MAX_FILENAME_LENGTH = 260;
            $file_name = "";
            $file_extension = "";
            $uploadErrors = array(
                    0=>__("There is no error, the file uploaded with success", 'wpstorecart'),
                    1=>__("The uploaded file exceeds the upload_max_filesize directive in php.ini", 'wpstorecart'),
                    2=>__("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", 'wpstorecart'),
                    3=>__("The uploaded file was only partially uploaded", 'wpstorecart'),
                    4=>__("No file was uploaded", 'wpstorecart'),
                    6=>__("Missing a temporary folder", 'wpstorecart')
            );


    // Validate the upload
            if (!isset($_FILES[$upload_name])) {
                    HandleError(__("No upload found in", 'wpstorecart')." \$_FILES " . $upload_name);
                    exit(0);
            } else if (isset($_FILES[$upload_name]["error"]) && $_FILES[$upload_name]["error"] != 0) {
                    HandleError($uploadErrors[$_FILES[$upload_name]["error"]]);
                    exit(0);
            } else if (!isset($_FILES[$upload_name]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
                    HandleError(__("Upload failed is_uploaded_file test.", 'wpstorecart'));
                    exit(0);
            } else if (!isset($_FILES[$upload_name]['name'])) {
                    HandleError(__("File has no name.", 'wpstorecart'));
                    exit(0);
            }

    // Validate the file size (Warning: the largest files supported by this code is 2GB)
            $file_size = @filesize($_FILES[$upload_name]["tmp_name"]);
            if (!$file_size || $file_size > $max_file_size_in_bytes) {
                    HandleError(__("File exceeds the maximum allowed size", 'wpstorecart'));
                    exit(0);
            }

            if ($file_size <= 0) {
                    HandleError(__("File size outside allowed lower bound", 'wpstorecart'));
                    exit(0);
            }


    // Validate file name (for our purposes we'll just remove invalid characters)
            $file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES[$upload_name]['name']));
            if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
                    HandleError(__("Invalid file name", 'wpstorecart'));
                    exit(0);
            }


    /*
    // Validate that we won't over-write an existing file
            if (file_exists($save_path . $file_name)) {
                    HandleError("File with this name already exists");
                    exit(0);
            }
    */

    // Validate file extension
            $extension_whitelist = array();
            $extension_whitelist[0] = 'jpg';
            $extension_whitelist[1] = 'jpeg';
            $extension_whitelist[2] = 'gif';
            $extension_whitelist[3] = 'png';
            $extension_whitelist[4] = 'bmp';

            $path_info = pathinfo($_FILES[$upload_name]['name']);
            $file_extension = $path_info["extension"];

            $is_valid_extension = false;
            foreach ($extension_whitelist as $extension) {
                    if (strcasecmp($file_extension, $extension) == 0) {
                            $is_valid_extension = true;
                            break;
                    }
            }
            if (!$is_valid_extension) {
                    HandleError("Invalid file extension");
                    exit(0);
            }

    // Validate file contents (extension and mime-type can't be trusted)
            /*
                    Validating the file contents is OS and web server configuration dependant.  Also, it may not be reliable.
                    See the comments on this page: http://us2.php.net/fileinfo

                    Also see http://72.14.253.104/search?q=cache:3YGZfcnKDrYJ:www.scanit.be/uploads/php-file-upload.pdf+php+file+command&hl=en&ct=clnk&cd=8&gl=us&client=firefox-a
                        which describes how a PHP script can be embedded within a GIF image file.

                    Therefore, no sample code will be provided here.  Research the issue, decide how much security is
                        needed, and implement a solution that meets the needs.
            */


    // Process the file
            /*
                    At this point we are ready to process the valid file. This sample code shows how to save the file. Other tasks
                        could be done such as creating an entry in a database or generating a thumbnail.

                    Depending on your server OS and needs you may need to set the Security Permissions on the file after it has
                    been saved.
            */
            if (!@move_uploaded_file($_FILES[$upload_name]["tmp_name"], $save_path.$file_name)) {
                    HandleError(__("File could not be saved.", 'wpstorecart'));


                    exit(0);
            }

            echo $file_name;

        
}

exit(0);




	


?>