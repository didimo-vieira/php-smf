<?php
/**
 * Represents a smf midi file in a object structure.
 * @author Dídimo Vieira de Araújo Junior
 * @property int division The number of ticks per quater note.
 * @property int format The format of the midi file (0, 1 or 2).
 * @property-read array track An array of Tracks.
 * @method addTrack() addTrack(Track $track) Adds a Track in the MidiFile.
 * @method removeTrack() removeTrack(int $index) Removes the Track specified by the $index parameter.
 * @method string toString() toString() Gives a string representation for the MidiFile object.
 */
class MidiFile extends Entity {
	/**
	 * Builds a new MidiFile object structure.
	 */
	public function __construct(){
		$this->properties = array(
			"division" => 0,
			"format"   => 0,
			"track"    => array()
		);
	}
	
	/**
	 * Validates a new value for a property. It's a implementation for the abstract method from Entity class.
	 * @param string $propertyName Property name to be validated.
	 * @param string $newValue The new value for vilidating.
	 * @return bool Returns true if the new value is ok for the property.
	 */
	protected function validate($propertyName, $newValue){
		switch ($propertyName) {
			case "division": return is_int($newValue);
			case "format": return in_array($newValue, array(0, 1, 2) );
			case "track": throw new ReadOnlyPropertyException("MidiFile->track is a read only property.");
		}
	}
	
	/**
	 * Adds a Track in the MidiFile.
	 * @param Track $track The Track object to be added.
	 */
	public function addTrack(Track $track){
		$this->properties["track"][] = $track;
	}
	
	/**
	 * Removes the Track specified by the $index parameter.
	 * @param int $index The index of the Track to be removed.
	 */
	public function removeTrack($index){
		array_splice($this->properties["track"], $index, 1);
	}
	
	/**
	 * Gives a string representation for the MidiFile object.
	 */
	public function toString(){
		return $this->__toString();
	}
	public function __toString(){
		$buf = "Format: ".$this->format;
		$buf .= "\nDivision: ".$this->division;
		for ($i=0; $i<count($this->track); $i++){
			$buf .= "\n\nTrack[$i]: ".$this->track[$i]->toString();
		}
		return $buf;
	}
}
?>