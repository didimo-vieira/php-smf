<pre>
<?php
function autoLoad($class_name) {
   require_once "midi.$class_name.php";
}
spl_autoload_register("autoLoad");
try {
	$smf = MidiReader::loadMidiFile("dresden_tone.mid");
	echo $smf;
} catch (Exception $e){
	echo $e->getMessage();
}
?>
</pre>