<?php
/**
 * Represents a Event in a object structure.
 * @author Dídimo Vieira de Araújo Junior
 * @property VariableLengthQuantitie deltaTime The relative timestamp of this Event.
 * @property Message message The Message of this Event.
 * @method array toArray() toArray($withStatus = true) Converts the Event to an array of bytes.
 * @method string toString() toString($withStatus = true) Converts the Event to its string representation.
 */
class Event extends Entity {
	/**
	 * Builds a new Track object structure.
	 */
	public function __construct(){
		$this->properties = array(
			"deltaTime" => null,
			"message" => null
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
			case "deltaTime": return is_a($newValue, "VariableLengthQuantitie");
			case "message":
				$typeIs = $newValue->getType();
				if (($typeIs == "System Common") || ($typeIs == "System Realtime")){
					return false;//throw new EventException("$typeIs Messages can not be stored in a smf file.");
				}
				return is_a($newValue, "Message");
		}
	}
	
	/**
	 * Converts the Event to an array of bytes.
	 * @param bool $withStatus Indicates if the returned array has the status byte. Useful to write smf midi file.
	 */
	public function toArray($withStatus = true){
		if (($this->message->getType() == "Voice") && (!$withStatus)) {
			return array_merge($this->deltaTime->toArray(), array_slice($this->message->toArray(), 1));
		}
		return array_merge($this->deltaTime->toArray(), $this->message->toArray());
	}
	
	/**
	 * Converts the Event to its string representation.
	 */
	public function toString(){
		return $this->__toString();
	}
	public function __toString(){
		return "[".$this->deltaTime->toString()."]".$this->message->toString()."";
	}
}
?>