<?php
/**
 * Represents long midi System Exclusive Messages.
 * @author Dídimo Vieira de Araújo Junior
 * @method int getQtdDataBytes() getQtdDataBytes() Gives the number of data bytes for this type of Message.
 * @method getMessage() getMessage() Gives the Message in midi protocol's format.
 * @method string getTitle() getTitle() Gives the title of the Message, a more specific type, in string format.
 * @method string getType() getType() Gives the type of Message in string format.
 * @method array toArray() toArray() Converts the Message to an array of bytes.
 */
class ShortMessage extends Message {
	/**
	 * Builds a new ShortMessage object structure.
	 */
	public function __construct(){
		$this->properties = array(
			"status" => 0,
			"data1"  => 0,
			"data2"  => 0
		);
	}
	
	/**
	 * Validates a new value for a property. It's a implementation for the abstract method from Entity class.
	 * @param string $propertyName Property name to be validated.
	 * @param string $newValue The new value for vilidating.
	 * @return bool Returns true if the new value is ok for the property.
	 */
	protected function validate($propertyName, $newValue){
		$MASK_IS_STATUS = 128;
		if (!is_int($newValue)){return false;}
		switch ($propertyName) {
			case "status":
				return $this->isStatusByte($newValue);
			case "data1":
			case "data2":
				return $this->isDataByte($newValue);
		}
		return false;
	}
	
	/**
	 * Gives the Message in midi protocol's format.
	 */
	public function getMessage(){
		$msg = $this->status +
			($this->data1 << 8) +
			($this->data2 << 16);
		return $msg;
	}

	/**
	 * Gives the number of data bytes for this type of Message.
	 */
	public function getQtdDataBytes(){
		$statusByte = $this->status;
		$hex = strtoupper( dechex($statusByte) );
		switch ($hex){
			//Common
			case "F1": return 1;
			case "F2": return 2;
			case "F3": return 1;
			case "F6": return 0;
			//Realtime
			case "F8":
			case "F9":
			case "FA":
			case "FB":
			case "FC":
			case "FE":
			case "FF":
				return 0;
			//Short message
			default:
				$msbNibble = substr($hex, 0, 1);
				$arrShort = array(
					"8" => 2,
					"9" => 2,
					"A" => 2,
					"B" => 2,
					"C" => 1,
					"D" => 1,
					"E" => 2
				);
				return $arrShort[$msbNibble];
		}
	}
	
	/**
	 * Gives the type of Message in string format.
	 */
	public function getType(){
		$statusByte = $this->status;
		$hex = strtoupper( dechex($statusByte) );
		$arrChannel  = array("8","9","A","B","C","D","E");
		$arrCommon   = array("F1","F2","F3","F6");
		$arrRealtime = array("F8","F9","FA","FB","FC","FE");
		if (in_array($hex, $arrCommon)){
			return "System Common";
		}
		if (in_array($hex, $arrRealtime)){
			return "System Realtime";
		}
		$msbNibble = substr($hex, 0, 1);
		if (in_array($msbNibble, $arrChannel)){
			return "Voice";
		}
		return "Unknown";
	}
	
	/**
	 * Gives the title of the Message, a more specific type, in string format.
	 */
	public function getTitle(){
		$statusByte = $this->status;
		$hex = strtoupper( dechex($statusByte) );
		$type = $this->getType();
		switch ($type){
			case "Voice":
				$msbNibble = substr($hex, 0, 1);
				return $this->getTitleVoice($msbNibble);
			case "Common": return $this->getTitleVoice($hex);
			case "Realtime": return $this->getTitleVoice($hex);
		}
		return "Unknown";
	}
	/**
	 * Gives the title of a Voice Message.
	 * @param string $statusNibble The first nibble of a status byte in hexadecimal representation.
	 */
	private function getTitleVoice($statusNibble){
		$arrVoice = array(
			"8" => "Note Off",
			"9" => "Note On",
			"A" => "Aftertouch",
			"B" => "Controller",
			"C" => "Program Change",
			"D" => "Channel Pressure",
			"E" => "Pitch Wheel"
		);
		$result = $arrVoice[$statusNibble];
		if ( ($result == "Note On") && ($this->data2 == 0) ){
			$result = "Note Off";
		}
		return $result;
	}
	/**
	 * Gives the title of a Common Message.
	 * @param string $statusByte The status byte in hexadecimal representation.
	 */
	private function getTitleCommon($statusByte){
		$arrCommon = array(
			"F1" => "MTC Quarter Frame",
			"F2" => "Song Position Pointer",
			"F3" => "Song Select",
			"F6" => "Tune Request"
		);
		return $arrCommon[$statusByte];
	}
	/**
	 * Gives the title of a Realtime Message.
	 * @param string $statusByte The status byte in hexadecimal representation.
	 */
	private function getTitleRealtime($statusByte){
		$arrRealtime = array(
			"F8" => "Clock",
			"F9" => "Tick",
			"FA" => "Start",
			"FB" => "Stop",
			"FC" => "Continue",
			"FE" => "Active Sense",
			"FF" => "Reset"
		);
		return $arrRealtime[$statusByte];
	}
	
	/**
	 * Converts the Message to an array of bytes.
	 */
	public function toArray(){
		$qtd = $this->getQtdDataBytes();
		switch ($qtd){
			case 0: return array($this->status);
			case 1: return array($this->status, $this->data1);
			case 2: return array($this->status, $this->data1, $this->data2);
		}
	}
}
?>