<?php
/**
 * Represents long midi or non-midi Messages.
 * @author Dídimo Vieira de Araújo Junior
 * @method int getQtdDataBytes() getQtdDataBytes() Gives the number of data bytes for this type of Message.
 * @method string getTitle() getTitle() Gives the title of the Message, a more specific type, in string format.
 * @method string getType() getType() Gives the type of Message in string format.
 * @method array toArray() toArray() Converts the Message to an array of bytes.
 */
abstract class LongMessage extends Message {
	/**
	 * Gives the number of data bytes for this type of Message.
	 */
	public function getQtdDataBytes(){
		return $this->length->value;
	}
	
	/**
	 * Gives the title of the Message, a more specific type, in string format.
	 */
	public function getTitle(){
		$type = $this->getType();
		switch ($type){
			case "META"://META
				$arrMETA = array(
					"0" => "Sequence Number",
					"1" => "Text",
					"2" => "Copyright",
					"3" => "Sequence/Track Name",
					"4" => "Instrument",
					"5" => "Lyric",
					"6" => "Marker",
					"7" => "Cue Point",
					"8" => "Program Name",
					"9" => "Device (Port) Name",
					"2F" => "End Of Track",
					"51" => "Tempo",
					"54" => "SMPTE Offset",
					"58" => "Time Signature",
					"59" => "Key Signature",
					"7F" => "Proprietary Event",
					"20" => "MIDI Channel",
					"21" => "MIDI Port"
				);
				$hex = strtoupper( dechex($this->type) );
				return $arrMETA[$hex];
			case "System Exclusive"://Sysex
				return "General Message";
		}
	}

	/**
	 * Gives the type of Message in string format.
	 */
	public function getType(){
		$statusByte = $this->status;
		$hex = strtoupper( dechex($statusByte) );
		switch ($hex){
			//META
			case "FF":
				return "META";
			//Sysex
			case "F0":
			case "F7":
				return "System Exclusive";
		}
	}
}
?>