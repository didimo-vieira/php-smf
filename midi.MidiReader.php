<?php
/**
 * Reads midi files and converts to a object structure.
 * @author Dídimo Vieira de Araújo Junior
 * @property
 * @property-read
 * @property-write
 * @method MidiFile loadMidiFile() loadMidiFile(string $fileName) Loads a midi file and returns a MidiFile object structure.
 * @todo Verify current status update
 */
class MidiReader {
	private static $filePosition = null;
	private static $fileBytesArray = null;
	private static $currentStatus = null;

	/**
	 * Open a smf file and returns its object representation.
	 * @param string $fileName Complete file path.
	 * @return MidiFile A MidiFile structure containing all tracks and messages.
	 * @static
	 */
	public static function loadMidiFile($fileName){
		self::$filePosition = 0;
		self::$currentStatus = 0;
		$handle = fopen($fileName, "rb");
		self::$fileBytesArray = str_split( fread($handle, filesize($fileName)) );
		fclose($handle);
		$midiFile = self::readMidiFile();
		self::$fileBytesArray = null;
		self::$filePosition = null;
		self::$currentStatus = null;
		return $midiFile;
	}
	
	/**
	 * Builds the entire structure based on readen chunk's information.
	 * @return MidiFile A MidiFile structure containing all tracks and messages.
	 */
	private static function readMidiFile(){
		$midiFile = new MidiFile();
		$chunk = self::readMThd();
		$midiFile->division = $chunk->division;
		$midiFile->format = $chunk->format;
		for ($t=0; $t<$chunk->numberOfTracks; $t++){
			$midiFile->addTrack( self::readTrack() );
		}
		return $midiFile;
	}
	
	/**
	 * Reads the MThd chunk.
	 * @return MThd A midi MThd structure.
	 */
	private static function readMThd(){
		$chunk = new MThd();
		$chunk->id = self::readString(4);
		$chunk->length = self::readNumber(4);
		$chunk->format = self::readNumber(2);
		$chunk->numberOfTracks = self::readNumber(2);
		$chunk->division = self::readNumber(2);
		return $chunk;
	}
	
	/**
	 * Reads a number of bytes and convert to a integer number.
	 * @param $bytes The number of bytes to read. This parameter is optional and its default value is 1.
	 * @return int The integer value of the readen bytes.
	 */
	private static function readNumber($bytes = 1){
		if ((self::$filePosition + $bytes) > count(self::$fileBytesArray)){
			throw new MidiReaderException("Error reading number: Can not read $bytes byte(s) from file.");
		}
		$result = 0;
		for ($i=0; $i<$bytes; $i++){
			$result = $result << 8;
			$result = $result + ord( self::$fileBytesArray[self::$filePosition] );
			self::$filePosition++;
		}
		return $result;
	}
	
	/**
	 * Reads a number of bytes and convert to a ascii string.
	 * @param $bytes The number of bytes to read. This parameter is optional and its default value is 1.
	 * @return string The concatenated string representation of the readen bytes.
	 */
	private static function readString($bytes = 1){
		if ((self::$filePosition + $bytes) > count(self::$fileBytesArray)){
			throw new MidiReaderException("Error reading string: Can not read $bytes byte(s) from file.");
		}
		$result = implode("", array_slice(self::$fileBytesArray, self::$filePosition, $bytes));
		self::$filePosition += $bytes;
		return $result;
	}
	
	/**
	 * Builds a entire midi Track structure based on chunk's information.
	 * @return Track A Track structure filled with its events.
	 * @todo change stop condition: each type of message must have its own __toString() method
	 * @todo build add(), remove(), get() methods to tracks and events ???
	 */
	private static function readTrack(){
		$track = new Track();
		$chunk = self::readMTrk();
		do {
			$event = self::readEvent();
			$track->addEvent($event);
		} while ($event->message->getTitle() != "End Of Track");
		return $track;
	}
	
	/**
	 * Reads the MTrk chunk.
	 * @return MThd A midi MTrk structure.
	 */
	private static function readMTrk(){
		$chunk = new MTrk();
		$chunk->id = self::readString(4);
		$chunk->length = self::readNumber(4);
		$chunk->data = self::readArray($chunk->length);
		self::$filePosition -= $chunk->length;
		return $chunk;
	}
	
	/**
	 * Reads a number of bytes and convert to an array of integer.
	 * @param $bytes The number of bytes to read. This parameter is optional and its default value is 1.
	 * @return int[] An array of integer.
	 */
	private static function readArray($bytes = 1){
		if ((self::$filePosition + $bytes) > count(self::$fileBytesArray)){
			throw new MidiReaderException("Error reading array: Can not read $bytes byte(s) from file.");
		}
		$result = array_slice(self::$fileBytesArray, self::$filePosition, $bytes);
		for ($i=0; $i<count($result); $i++){
			$result[$i] = ord($result[$i]);
		}
		self::$filePosition += $bytes;
		return $result;
	}
	
	/**
	 * Reads an entire Event structure composed by a delta timestamp (VariableLengthQuantitie) and a midi Message.
	 * @return Event A Event structure.
	 */
	private static function readEvent(){
		$event = new Event();
		$event->deltaTime = self::readVariableLengthQuantitie();
		$event->message = self::readMessage();
		return $event;
	}
	
	/**
	 * Reads a VariableLengthQuantitie number.
	 * @return VariableLengthQuantitie A number that can be 1 to 4 bytes length.
	 */
	private static function readVariableLengthQuantitie(){
		$MASK_NEXT = 128; //10000000
		$MASK_FILL = 127; //01111111
		$vlq = new VariableLengthQuantitie();
		$byte = 0;
		$value = 0;
		$byteCount = 0;
		do {
			$byteCount++;
			if ($byteCount >= 5) { //more than 4 bytes
				throw new MidiFileException("Error reading Variable Length Quantitie: Can not read $bytes byte(s) from file.");
			}
			$byte = self::readNumber();
			$value = ($value << 7) + ($byte & $MASK_FILL);
		} while (($byte & $MASK_NEXT) == $MASK_NEXT); //has next
		$vlq->value = $value;
		return $vlq;
	}
	
	/**
	 * Reads a instance of a Message object.
	 * @return Message A midi or non-midi message of a smf file. Can be a ShortMessage, SystemExclusiveMessage or METAMessage object.
	 */
	private static function readMessage(){
		$MASK_STATUS_NIBBLE = 240; //11110000
		$MASK_IS_STATUS = 128;     //10000000
		$MASK_VALUE  = 127;        //01111111
		$message = null;
		$byte = self::readNumber();
		switch ($byte) {
			case 255: //FF: META Events***********************************
				$message = new METAMessage();
				$message->status = $byte;
				$message->type = self::readNumber();
				$message->length = self::readVariableLengthQuantitie();
				if ($message->length->value > 0){
					$message->data = self::readArray($message->length->value);
				} else {
					$message->data = array();
				}
				break;
			case 240: //F0: SysEx******************************************
				$message = new SystemExclusiveMessage();
				$message->status = $byte;
				$message->length = self::readVariableLengthQuantitie();
				$message->data = self::readArray($message->length->value);
				break;
			default: //Channel Events**************************************
				$message = new ShortMessage();
				if (self::isStatusByte($byte)) {
					self::$currentStatus = $byte;//if ( ($byte >= 128) && ($byte <= 239) ){//80-EF//not applicable for common or realtime messages
					$message->status = $byte;
				} else { //is data: position goes back one position (to read again below)
					$message->status = self::$currentStatus;
					self::$filePosition--;
				}
				//$qtdDataBytes = $message->getQtdDataBytes();
				$qtdDataBytes = self::getQtdDataBytes($message);
				switch ($qtdDataBytes) {
					case 0: //0 data byte
						
						break;
					case 1: //1 data byte
						if (self::isStatusByte(self::$fileBytesArray[self::$filePosition])){
							throw new MidiReaderException("Error reading Short Message data byte: Invalid data byte.");
						}
						$message->data1 = self::readNumber();
						break;
					case 2: //2 data bytes
						if (self::isStatusByte(self::$fileBytesArray[self::$filePosition])){
							throw new MidiReaderException("Error reading Short Message data byte 1: Invalid data byte.");
						}
						$message->data1 = self::readNumber();
						if (self::isStatusByte(self::$fileBytesArray[self::$filePosition])){
							throw new MidiReaderException("Error reading Short Message data byte 2: Invalid data byte.");
						}
						$message->data2 = self::readNumber();
						break;
				}
				break;
		}
		return $message;
	}
	
	/**
	 * Gets the number of data bytes for a Message object.
	 * @param Message A midi Message structure, with status (and the others) property filled.
	 * @return int The number of data bytes for the type of message passed in parameter.
	 */
	private static function getQtdDataBytes(Message $message){
		$statusByte = $message->status;
		$hex = strtoupper( dechex($statusByte) );
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
		switch ($hex){
			case "FF": //META message
			case "F0": //Sysex message
				return $message->length->value;
				break;
			default:   //Short message
				return $arrShort[$msbNibble];
				break;
		}
	}
	
	/**
	 * Verify if a given number is a status byte.
	 * @param int $byte The byte for verifying.
	 * @return bool Returns true if the number is a status byte.
	 */
	private static function isStatusByte($byte){
		return ( ($byte >= 128) && ($byte <= 255) );
	}
	
	/**
	 * Verify if a given number is a data byte.
	 * @param int $byte The byte for verifying.
	 * @return bool Returns true if the number is a data byte.
	 */
	private static function isDataByte($byte){
		return ($byte <= 127);
	}
}

?>