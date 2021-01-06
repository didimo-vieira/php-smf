<?php
/**
 * Represents a MTrk Track Header Chunk structure of a midi file in a object structure.
 * @author Dídimo Vieira de Araújo Junior
 * @property string id The "MTrk" chunk's identifier.
 * @property int length The length of the Track in bytes exactly as the smf was stored (without some current status bytes).
 * @property array data An array of the Track's bytes.
 */
class MTrk extends Entity {
	/**
	 * Builds a new MTrk object structure.
	 * Fields length: 4, 4, undefined
	 * Fields values: "MTrk", int, array
	 */
	public function __construct(){
		$this->properties = array(
			"id"     => "",
			"length" => 0,
			"data"   => array()
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
			case "id": return ($newValue == "MTrk");
			case "length": return is_int($newValue);
			case "data": return is_array($newValue);
		}
	}
}
?>