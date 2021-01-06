<?php
abstract class Entity {
	protected $properties;
	public function __get($propertyName){
		if (array_key_exists($propertyName, $this->properties)){
			return $this->properties[$propertyName];
		} else {
			throw new PropertyNotExistsException(get_class($this)."::".$propertyName);
		}
	}
	public function __set($propertyName, $newValue){
		if (array_key_exists($propertyName, $this->properties)){
			if ($this->validate($propertyName, $newValue)){
				$this->properties[$propertyName] = $newValue;
			} else {
				throw new InvalidPropertyValueException($newValue->toString()." is not a valid value for ".get_class($this)."::"."$propertyName property.");
			}
		} else {
			throw new PropertyNotExistsException(get_class($this)."::".$propertyName);
		}
	}
	protected abstract function validate($propertyName, $newValue);
	public function __destruct(){
		foreach ($this->properties as $propertyName => $propertyValue){
			unset($this->properties[$propertyName]);
		}
		unset($this->properties);
	}
	public function __clone(){
		$this->properties = clone $this->properties;
	}
}
class PropertyNotExistsException extends Exception {
	public function __construct($propertyName){
		parent::__construct("Property '$propertyName' not exists.");
	}
}
class InvalidPropertyValueException extends Exception {
	public function __construct($description){
		parent::__construct($description);
	}
}
class ReadOnlyPropertyException extends Exception {
	public function __construct($description){
		parent::__construct($description);
	}
}
class MidiReaderException extends Exception {
	public function __construct($description){
		parent::__construct($description);
	}
}
/*class EventException extends Exception {
	public function __construct($description){
		parent::__construct($description);
	}
}*/

?>