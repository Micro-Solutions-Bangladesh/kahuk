<?php
/**
 * Return an array of file types allowed to upload by a user
 * 
 * @since 6.0.5
 * 
 * @return array
 */
function kahuk_file_types_upload_allowed() {
    return ["image/jpeg", "image/gif", "image/png", "image/x-png", "image/pjpeg"];
}

$fileTypesUploadAllowed = kahuk_file_types_upload_allowed();

