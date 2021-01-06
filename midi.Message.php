<?php
/**
 * Represents all types of midi or non-midi Messages.
 * @author Dídimo Vieira de Araújo Junior
 * @method string toHexaString() toHexaString() Converts the Message bytes to a hexadecimal string representation.
 * @method string toString() toString() Converts the Message to its string representation.
 * @method string getTitle() getTitle() Gives the title of the Message, a more specific type, in string format.
 * @method string getType() getType() Gives the type of Message in string format.
 * @method array toArray() toArray() Converts the Message to an array of bytes.
 * @method int getQtdDataBytes() getQtdDataBytes() Gives the number of data bytes for this type of Message.
 * @method getMessage() getMessage() Gives the Message in midi protocol's format.
 * @todo @method string getNoteName/getDetail() getNoteName/getDetail($ShortMessage $message) Gives the note name of a Note On or Note Off message.
 * @todo getDetail retorna nome das notas (canais 10 ou outros), nome do controle, texto/dados dos metas, etc...
 */
abstract class Message extends Entity {
	/**
	 * Gives the type of Message in string format.
	 */
	public abstract function getType();
	
	/**
	 * Gives the title of the Message, a more specific type, in string format.
	 */
	public abstract function getTitle();
	
	/**
	 * Gives the number of data bytes for this type of Message.
	 */
	public abstract function getQtdDataBytes();
	
	/**
	 * Gives the Message in midi protocol's format.
	 */
	public abstract function getMessage();
	
	/**
	 * Verify if a given number is a status byte.
	 * @param int $byte The byte for verifying.
	 * @return bool Returns true if the number is a status byte.
	 */
	protected static function isStatusByte($byte){
		return ( ($byte >= 128) && ($byte <= 255) );
	}
	
	/**
	 * Verify if a given number is a data byte.
	 * @param int $byte The byte for verifying.
	 * @return bool Returns true if the number is a data byte.
	 */
	protected static function isDataByte($byte){
		return ($byte <= 127);
	}
	
	/**
	 * Converts the Message bytes to a hexadecimal string representation.
	 */
	public function toHexaString(){
		$msgArr = $this->toArray();
		foreach($msgArr as $element){
			$result[] = str_pad(strtoupper(dechex($element)), 2, "0", STR_PAD_LEFT);
		}
		return implode (" ", $result);
	}

	/**
	 * Converts the Message to an array of bytes.
	 */
	public abstract function toArray();
	
	/**
	 * Converts the Message to its string representation.
	 */
	public function toString(){
		return $this->__toString();
	}
	public function __toString(){
		return ("[".$this->getType()."][".$this->getTitle()."][".$this->toHexaString()."]");
	}
}
?>