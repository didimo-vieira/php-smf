<?php
/**
 * Represents a MThd File Header Chunk structure of a midi file in a object structure.
 * @author Dídimo Vieira de Araújo Junior
 * @property string id The "MTrk" chunk's identifier.
 * @property int length The length of the Header in bytes. It's always 6.
 * @property int format The format of the midi file (0, 1 or 2).
 * @property int numberOfTracks The number of Tracks in the smf file.
 * @property int division The number of ticks per quater note.
 * @method addTrack() addTrack(Track $track) Adds a Track in the Midi File.
 * @method removeTrack() removeTrack(int $index) Removes the Track specified by the $index parameter.
 * @method string toString() toString() Gives a string representation for the MidiFile object.
 */
class MThd extends Entity {
	/**
	 * Builds a new MThd object structure.
	 * Fields length: 4, 4, 2, 2, 2
	 * Fields values: "MThd", 6, (0,1,2), n, n
	 */
	public function __construct(){
		$this->properties = array(
			"id"             => "",
			"length"         => 0,
			"format"         => 0,
			"numberOfTracks" => 0,
			"division"       => 0
		);
	}
	
	/**
	 * Validates a new value for a property. It's a implementation for the abstract method from Entity class.
	 * @param string $propertyName Property name to be validated.
	 * @param string $newValue The new value for vilidating.
	 * @return bool Returns true if the new value is ok for the property.
	 */
	protected function validate($propertyName, $newValue){
		switch ($propertyName){
			case "id": return $newValue == "MThd";
			case "length": return ($newValue == 6);
			case "format": return in_array($newValue, array(0, 1, 2) );
			case "numberOfTracks": return $this->isShortInteger($newValue);
			case "division": return $this->isShortInteger($newValue);
		}
	}
	
	/**
	 * Validates values for fields. Internal usage.
	 * @param string $value The value for vilidating.
	 * @return bool Returns true if the value is a two byte integer.
	 */
	private function isShortInteger($value){
		$MASK = 65535;
		return (($MASK & $value) == $value);
	}	
}
?>