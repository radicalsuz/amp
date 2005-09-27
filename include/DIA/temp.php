<?php



$api =& DIA_API_Factory::create();

$supporter =& new DIA_Supporter($id);
$supporter->interface($api);

$supporter->read();
$supporter->set(array("First_Name" => "Seth"));
$supporter->get("First_Name");
$supporter->get(array("First_Name", "Last_Name"));
$supporter->save();

?>
