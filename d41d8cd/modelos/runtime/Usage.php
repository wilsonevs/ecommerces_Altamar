<?php
$r = EavModel::load(123);
$r->nombre


$itemId = EavModel::save($p);

$r = EavModel::items($filter);
$r->attrs->nombre->data[0];
$r->attrs->nombre->label[0];

$arr = EavModel::item($filter);
$arr[0]->attrs->nombre->data[0];


?>
