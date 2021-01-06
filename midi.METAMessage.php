<?php
/**
 * Represents long non-midi META Messages.
 * @author Dídimo Vieira de Araújo Junior
 * @method getMessage() getMessage() Gives the Message in midi protocol's format.
 * @method array toArray() toArray() Converts the Message to an array of bytes.
 */
class METAMessage extends LongMessage {
	/**
	 * Builds a new METAMessage object structure.
	 */
	public function __construct(){
		$this->properties = array(
			"status" => 0,
			"type"   => 0,
			"length" => null,
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
		switch ($propertyName) {
			case "status": return is_int($newValue);
			case "type": return is_int($newValue);
			case "length": return is_a($newValue, "VariableLengthQuantitie");
			case "data": return is_array($newValue);
		}
	}
	
	/**
	 * Gives the Message in midi protocol's format.
	 */
	public function getMessage(){
		return $this->toArray();
	}
	
	/**
	 * Converts the Message to an array of bytes.
	 */
	public function toArray(){
		$result = array_merge(
			array($this->status),
			array($this->type),
			$this->length->toArray()
		);
		if ($this->length->value > 0){
			$result = array_merge($result, $this->data);
		}		
		return $result;
	}
}
?>