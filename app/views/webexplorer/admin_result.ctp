<?php
$separator = ',';
echo "matricule,note,comment\n";
foreach($tps as $tp){
	echo $tp['WebpageTp']['user_id'] . $separator;
	echo $tp['WebpageTp']['point'] . $separator;
	echo  str_replace(array("\n", "\r", $separator), array(' ', ' ', ' '), $tp['WebpageTp']['comment']) .  ". correcteur: " . $tp['Evaluator']['full_name'] ."\n";
}
?>