<?php
/**
 * Represents a Number that can be stored in 1, 2, 3 or 4 bytes.
 * @author Dídimo Vieira de Araújo Junior
 * @property int value The value stored.
 * @method array toArray() toArray($withStatus = true) Converts the VariableLengthQuantitie object to an array of bytes in smf file format.
 * @method string toString() toString() Converts the VariableLengthQuantitie to its string representation.
 */
class VariableLengthQuantitie extends Entity {
	/**
	 * Builds a new VariableLengthQuantitie object structure.
	 */
	public function __construct(){
		$this->properties = array(
			"value" => 0
		);
	}
	
	/**
	 * Validates a new value for a property. It's a implementation for the abstract method from Entity class.
	 * @param string $propertyName Property name to be validated.
	 * @param string $newValue The new value for vilidating.
	 * @return bool Returns true if the new value is ok for the property.
	 */
	protected function validate($propertyName, $newValue){
		return (is_int($newValue) && ($newValue >= 0) && ($newValue <= 268435455) );
	}
	
	/**
	 * Converts the VariableLengthQuantitie to an array of bytes. Useful to write smf midi file.
	 */
	public function toArray(){
		$b4 = (($this->value & 127));
		$b3 = (($this->value & 16256) >> 7);
		$b2 = (($this->value & 2080768) >> 14);
		$b1 = (($this->value & 266338304) >> 21);
		$arr = array();
		$inserir = false;
		if ($b1 != 0) {
			$inserir = true;
			$arr[] = $b1 + 128;
		}
		if ($inserir) {
			$arr[] = $b2 + 128;
		} elseif ($b2 != 0) {
			$inserir = true;
			$arr[] = $b2 + 128;	
		}
		if ($inserir) {
			$arr[] = $b3 + 128;
		} elseif ($b3 != 0) {
			$inserir = true;
			$arr[] = $b3 + 128;	
		}
		$arr[] = $b4;
		return $arr;
	}
	
	/**
	 * Converts the VariableLengthQuantitie to its string representation.
	 */
	public function toString(){
		return $this->__toString();
	}
	public function __toString(){
		return $this->value;
	}
}
?>