<pre>
<?php
function __autoload($class_name) {
   require_once "midi.$class_name.php";
}
try {
	$smf = MidiReader::loadMidiFile("dresden_tone.mid");
	echo $smf;
} catch (Exception $e){
	echo $e->getMessage();
}
?>
</pre>