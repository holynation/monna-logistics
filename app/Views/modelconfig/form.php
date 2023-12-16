<?php

$show_add = ($configData && array_key_exists('show_add', $configData))?$configData['show_add']:false;
$exclude = ($configData && array_key_exists('exclude', $configData))?$configData['exclude']:array();
$has_upload = ($configData && array_key_exists('has_upload', $configData))?$configData['has_upload']:false;
$hidden = ($configData && array_key_exists('hidden', $configData))?$configData['hidden']:array();
$showStatus = ($configData && array_key_exists('show_status', $configData))?$configData['show_status']:false;
$showAddForm = ($configData && array_key_exists('show_add', $configData))?$configData['show_add']:true;
$submitLabel = ($configData && array_key_exists('submit_label', $configData))?$configData['submit_label']:"Save";
$extraLink = ($configData && array_key_exists('extra_link', $configData))?$configData['extra_link']:false;
$showAddCaption = ($configData && array_key_exists('show_add_caption', $configData))?$configData['show_add_caption']:false;
$extraValue = ($configData && array_key_exists('extra_value', $configData))?$configData['extra_value']:"Add";
$editMessageInfo = ($configData && array_key_exists('edit_message_info', $configData))?$configData['edit_message_info']:"";