<?php
// this are config for table aesthetic
$tableAction = ($configData && array_key_exists('table_action', $configData))?$configData['table_action']:$modelObj::$tableAction;
$tableExclude = ($configData && array_key_exists('table_exclude', $configData))?$configData['table_exclude']:array();
$tableQueryString = ($configData && array_key_exists('query_string', $configData))?$configData['query_string']: array();
$tableTitle = ($configData && array_key_exists('table_title', $configData))?$configData['table_title']:ucfirst(removeUnderscore($model) .' '. "Page");
$icon = ($configData && array_key_exists('table_icon', $configData))?$configData['table_icon']:"";
$tableAttr = ($configData && array_key_exists('table_attr', $configData))?$configData['table_attr']:array('class'=>'table align-middle table-row-dashed fs-6 gy-5', 'id' => "kt_table_users");

// this are the config for data filtering
$query = ($configData && array_key_exists('query', $configData))?$configData['query']:"";
$search = ($configData && array_key_exists('search', $configData))?$configData['search']:"";
$searchPlaceholder = ($configData && array_key_exists('search_placeholder', $configData))?$configData['search_placeholder']:"";
$searchOrderBy = ($configData && array_key_exists('order_by', $configData))?$configData['order_by']:"";
$filter = ($configData && array_key_exists('filter', $configData))?$configData['filter']:"";

$checkBox = ($configData && array_key_exists('table_checkbox', $configData))?$configData['table_checkbox']:false;

$where = '';
if ($filter) {
  foreach ($filter as $item) {
    $display = (isset($item['filter_display']) && $item['filter_display']) ? $item['filter_display']:$item['filter_label'];

    if (isset($_GET[$display]) && $_GET[$display]) {
      $value = $db->conn_id->escape_string($_GET[$display]);
      $where.= $where?" and {$item['filter_label']}='$value' ":"where {$item['filter_label']}='$value' ";
    }
  }
}

if ($search) {
 $val = isset($_GET['q']) ? $_GET['q'] : '';
 $val = $db->escape($val);
  if (isset($_GET['q']) && $_GET['q']) {
    $whereQ = (strpos($query,'where') !== false) ? " and " : "where ";
    $temp = $where ? " and (" : " $whereQ ";
    $count = 0;
    foreach ($search as $criteria) {
      $temp .= $count == 0 ? " $criteria like '%$val%'" : " or $criteria like '%$val%' ";
      $count++;
    }
    $temp .= (strpos($temp, 'and (') !== false) ? ")" : "";
    $where .= $temp;
  }
}
