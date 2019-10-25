<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if(in_array($type, array('area', 'volume', 'price', 'length')) and !$done_this)
{
	$column = $field->table_column;
	$query .= $table_name.".`".$column."`, ".$table_name.".`".$column."_unit`, ".$table_name.".`".$column."_si`, ";

	$done_this = true;
}
elseif(in_array($type, array('mmarea', 'mmvolume', 'mmprice', 'mmlength')) and !$done_this)
{
	$column = $field->table_column;
	$query .= $table_name.".`".$column."`, ".$table_name.".`".$column."_max`, ".$table_name.".`".$column."_unit`, ".$table_name.".`".$column."_si`, ".$table_name.".`".$column."_max_si`, ";

	$done_this = true;
}
elseif($type == 'locations' and !$done_this)
{
    /**
        Howard: for better performance and speed of query I didn't include location fields here.
        Because Locations are rendered before and we don't need them right now.
    **/
	$done_this = true;
}
elseif($type == 'mmnumber')
{
    $column = $field->table_column;
    $query .= $table_name.".`".$column."`, ".$table_name.".`".$column."_max`, ";

    $done_this = true;
}
elseif($type == 'feature')
{
    $column = $field->table_column;
    $query .= $table_name.".`".$column."`, ".$table_name.".`".$column."_options`, ";

    $done_this = true;
}
