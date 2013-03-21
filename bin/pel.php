<?php // 1 > Extension pour modification des données EXIF ;
class PelJpegContent {
  private $data = null;
  function __construct(PelDataWindow $data) {
    $this->data = $data;
  }
  function getBytes() {
    return $this->data->getBytes();
  }
}

class PelJpegComment extends PelJpegContent {
  private $comment = '';
  function __construct($comment = '') {
    $this->comment = $comment;
  }
  function load(PelDataWindow $d) {
    $this->comment = $d->getBytes();
  }
  function setValue($comment) {
    $this->comment = $comment;
  }
  function getValue() {
    return $this->comment;
  }
  function getBytes() {
    $this->comment;
  }
  function __toString() {
    return $this->getValue();
  }
}

class PelException extends Exception {


  function __construct(/* fmt, args... */) {
    $args = func_get_args();
    $fmt = array_shift($args);
    parent::__construct(vsprintf($fmt, $args));
  }
}
class PelDataWindowOffsetException extends PelException {}


class PelDataWindowWindowException extends PelException {}


class PelDataWindow {
  private $data = '';
  private $order;
  private $start  = 0;
  private $size   = 0;
  function __construct($d = '', $e = PelConvert::LITTLE_ENDIAN) {
    $this->data  = $d;
    $this->order = $e;
    $this->size  = strlen($d);
  }

  function getSize() {
    return $this->size;
  }

  function setByteOrder($o) {
    $this->order = $o;
  }

  function getByteOrder() {
    return $this->order;
  }

  function setWindowStart($start) {
    if ($start < 0 || $start > $this->size)
      throw new PelDataWindowWindowException('Window [%d, %d] does ' .
                                             'not fit in window [0, %d]',
                                             $start, $this->size, $this->size);

    $this->start += $start;
    $this->size  -= $start;
  }

  function setWindowSize($size) {
    if ($size < 0)
      $size += $this->size;

    if ($size < 0 || $size > $this->size)
      throw new PelDataWindowWindowException('Window [0, %d] ' .
                                             'does not fit in window [0, %d]',
                                             $size, $this->size);
    $this->size = $size;
  }
  
  function getClone($start = false, $size = false) {
    $c = clone $this;
    
    if (is_int($start))
      $c->setWindowStart($start);

    if (is_int($size))
      $c->setWindowSize($size);

    return $c;
  }

  private function validateOffset($o) {
    if ($o < 0 || $o >= $this->size)
      throw new PelDataWindowOffsetException('Offset %d not within [%d, %d]',
                                             $o, 0, $this->size-1);
  }

  function getBytes($start = false, $size = false) {
    if (is_int($start)) {
      if ($start < 0)
        $start += $this->size;
      
      $this->validateOffset($start);
    } else {
      $start = 0;
    }
    
    if (is_int($size)) {
      if ($size <= 0)
        $size += $this->size - $start;
      
      $this->validateOffset($start+$size);
    } else {
      $size = $this->size - $start;
    }
    return substr($this->data, $this->start + $start, $size);
  }


  function getByte($o = 0) {
    $this->validateOffset($o);
	 $o += $this->start;    
    return PelConvert::bytesToByte($this->data, $o);
  }


  function getSByte($o = 0) {
    $this->validateOffset($o);
    $o += $this->start;    
    return PelConvert::bytesToSByte($this->data, $o);
  }


  function getShort($o = 0) {
    $this->validateOffset($o);
    $this->validateOffset($o+1);
    $o += $this->start;
    return PelConvert::bytesToShort($this->data, $o, $this->order);
  }


  function getSShort($o = 0) {
    $this->validateOffset($o);
    $this->validateOffset($o+1);
    $o += $this->start;
    return PelConvert::bytesToSShort($this->data, $o, $this->order);
  }



  function getLong($o = 0) {
    $this->validateOffset($o);
    $this->validateOffset($o+3);   
    $o += $this->start;
   return PelConvert::bytesToLong($this->data, $o, $this->order);
  }



  function getSLong($o = 0) {
     $this->validateOffset($o);
    $this->validateOffset($o+3);
    $o += $this->start;
    return PelConvert::bytesToSLong($this->data, $o, $this->order);
  }


  function getRational($o = 0) {
    return array($this->getLong($o), $this->getLong($o+4));
  }


  function getSRational($o = 0) {
    return array($this->getSLong($o), $this->getSLong($o+4));
  }



  function strcmp($o, $str) {
    $s = strlen($str);
    $this->validateOffset($o);
    $this->validateOffset($o + $s - 1);

    /* Translate the offset into an offset into the data. */
    $o += $this->start;
  
    /* Check each character, return as soon as the answer is known. */
    for ($i = 0; $i < $s; $i++) {
      if ($this->data{$o + $i} != $str{$i})
        return false;
    }

    /* All characters matches each other, return true. */
    return true;
  }


  function __toString() {
    return Pel::fmt('DataWindow: %d bytes in [%d, %d] of %d bytes',
                    $this->size,
                    $this->start, $this->start + $this->size,
                    strlen($this->data));
  }

}
class PelConvert {

  const LITTLE_ENDIAN = true;

  const BIG_ENDIAN = false;

  static function shortToBytes($value, $endian) {
    if ($endian == self::LITTLE_ENDIAN)
      return chr($value) . chr($value >> 8);
    else
      return chr($value >> 8) . chr($value);
  }


  static function sShortToBytes($value, $endian) {
    return self::shortToBytes($value, $endian);
  }
  

  static function longToBytes($value, $endian) {
    $hex = str_pad(base_convert($value, 10, 16), 8, '0', STR_PAD_LEFT);
    if ($endian == self::LITTLE_ENDIAN)
      return (chr(hexdec($hex{6} . $hex{7})) .
              chr(hexdec($hex{4} . $hex{5})) .
              chr(hexdec($hex{2} . $hex{3})) .
              chr(hexdec($hex{0} . $hex{1})));
    else
      return (chr(hexdec($hex{0} . $hex{1})) .
              chr(hexdec($hex{2} . $hex{3})) .
              chr(hexdec($hex{4} . $hex{5})) .
              chr(hexdec($hex{6} . $hex{7})));
  }


  static function sLongToBytes($value, $endian) {
    if ($endian == self::LITTLE_ENDIAN)
      return (chr($value) .
              chr($value >>  8) .
              chr($value >> 16) .
              chr($value >> 24));
    else
      return (chr($value >> 24) .
              chr($value >> 16) .
              chr($value >>  8) .
              chr($value));
  }


  static function bytesToByte($bytes, $offset) {
    return ord($bytes{$offset});
  }


  static function bytesToSByte($bytes, $offset) {
    $n = self::bytesToByte($bytes, $offset);
    if ($n > 127)
      return $n - 256;
    else
      return $n;
  }


  static function bytesToShort($bytes, $offset, $endian) {
    if ($endian == self::LITTLE_ENDIAN)
      return (ord($bytes{$offset+1}) * 256 +
              ord($bytes{$offset}));
    else
      return (ord($bytes{$offset})   * 256 +
              ord($bytes{$offset+1}));
  }

  static function bytesToSShort($bytes, $offset, $endian) {
    $n = self::bytesToShort($bytes, $offset, $endian);
    if ($n > 32767)
      return $n - 65536;
    else
      return $n;
  }

  static function bytesToLong($bytes, $offset, $endian) {
    if ($endian == self::LITTLE_ENDIAN)
      return (ord($bytes{$offset+3}) * 16777216 +
              ord($bytes{$offset+2}) * 65536    +
              ord($bytes{$offset+1}) * 256      +
              ord($bytes{$offset}));
    else
      return (ord($bytes{$offset})   * 16777216 +
              ord($bytes{$offset+1}) * 65536    +
              ord($bytes{$offset+2}) * 256      +
              ord($bytes{$offset+3}));
  }


  static function bytesToSLong($bytes, $offset, $endian) {
    $n = self::bytesToLong($bytes, $offset, $endian);
    if ($n > 2147483647)
      return $n - 4294967296;
    else
      return $n;
  }

  static function bytesToRational($bytes, $offset, $endian) {
    return array(self::bytesToLong($bytes, $offset, $endian),
                 self::bytesToLong($bytes, $offset+4, $endian));
  }


  static function bytesToSRational($bytes, $offset, $endian) {
    return array(self::bytesToSLong($bytes, $offset, $endian),
                 self::bytesToSLong($bytes, $offset+4, $endian));
  }

  static function bytesToDump($bytes, $max = 0) {
    $s = strlen($bytes);

    if ($max > 0)
      $s = min($max, $s);

    $line = 24;

    for ($i = 0; $i < $s; $i++) {
      printf('%02X ', ord($bytes{$i}));
      
      if (($i+1) % $line == 0)
        print("\n");
    }
    print("\n");
  }

}
class PelJpegMarker {

  /** Encoding (baseline) */
  const SOF0  = 0xC0;
  /** Encoding (extended sequential) */
  const SOF1  = 0xC1;
  /** Encoding (progressive) */
  const SOF2  = 0xC2;
  /** Encoding (lossless) */
  const SOF3  = 0xC3;
  /** Define Huffman table */
  const DHT   = 0xC4;
  /** Encoding (differential sequential) */
  const SOF5  = 0xC5;
  /** Encoding (differential progressive) */
  const SOF6  = 0xC6;
  /** Encoding (differential lossless) */
  const SOF7  = 0xC7;
  /** Extension */
  const JPG   = 0xC8;
  /** Encoding (extended sequential, arithmetic) */
  const SOF9  = 0xC9;
  /** Encoding (progressive, arithmetic) */
  const SOF10 = 0xCA;
  /** Encoding (lossless, arithmetic) */
  const SOF11 = 0xCB;
  /** Define arithmetic coding conditioning */
  const DAC   = 0xCC;
  /** Encoding (differential sequential, arithmetic) */
  const SOF13 = 0xCD;
  /** Encoding (differential progressive, arithmetic) */
  const SOF14 = 0xCE;
  /** Encoding (differential lossless, arithmetic) */
  const SOF15 = 0xCF;
  /** Restart 0 */
  const RST0  = 0xD0;
  /** Restart 1 */
  const RST1  = 0xD1;
  /** Restart 2 */
  const RST2  = 0xD2;
  /** Restart 3 */
  const RST3  = 0xD3;
  /** Restart 4 */
  const RST4  = 0xD4;
  /** Restart 5 */
  const RST5  = 0xD5;
  /** Restart 6 */
  const RST6  = 0xD6;
  /** Restart 7 */
  const RST7  = 0xD7;
  /** Start of image */
  const SOI   = 0xD8;
  /** End of image */
  const EOI   = 0xD9;
  /** Start of scan */
  const SOS   = 0xDA;
  /** Define quantization table */
  const DQT   = 0xDB;
  /** Define number of lines */
  const DNL   = 0xDC;
  /** Define restart interval */
  const DRI   = 0xDD;
  /** Define hierarchical progression */
  const DHP   = 0xDE;
  /** Expand reference component */
  const EXP   = 0xDF;
  /** Application segment 0 */
  const APP0  = 0xE0;
  /**
   * Application segment 1
   *
   * When a JPEG image contains Exif data, the data will normally be
   * stored in this section and a call to {@link PelJpeg::getExif()}
   * will return a {@link PelExif} object representing it.
   */
  const APP1  = 0xE1;
  /** Application segment 2 */
  const APP2  = 0xE2;
  /** Application segment 3 */
  const APP3  = 0xE3;
  /** Application segment 4 */
  const APP4  = 0xE4;
  /** Application segment 5 */
  const APP5  = 0xE5;
  /** Application segment 6 */
  const APP6  = 0xE6;
  /** Application segment 7 */
  const APP7  = 0xE7;
  /** Application segment 8 */
  const APP8  = 0xE8;
  /** Application segment 9 */
  const APP9  = 0xE9;
  /** Application segment 10 */
  const APP10 = 0xEA;
  /** Application segment 11 */
  const APP11 = 0xEB;
  /** Application segment 12 */
  const APP12 = 0xEC;
  /** Application segment 13 */
  const APP13 = 0xED;
  /** Application segment 14 */
  const APP14 = 0xEE;
  /** Application segment 15 */
  const APP15 = 0xEF;
  /** Extension 0 */
  const JPG0  = 0xF0;
  /** Extension 1 */
  const JPG1  = 0xF1;
  /** Extension 2 */
  const JPG2  = 0xF2;
  /** Extension 3 */
  const JPG3  = 0xF3;
  /** Extension 4 */
  const JPG4  = 0xF4;
  /** Extension 5 */
  const JPG5  = 0xF5;
  /** Extension 6 */
  const JPG6  = 0xF6;
  /** Extension 7 */
  const JPG7  = 0xF7;
  /** Extension 8 */
  const JPG8  = 0xF8;
  /** Extension 9 */
  const JPG9  = 0xF9;
  /** Extension 10 */
  const JPG10 = 0xFA;
  /** Extension 11 */
  const JPG11 = 0xFB;
  /** Extension 12 */
  const JPG12 = 0xFC;
  /** Extension 13 */
  const JPG13 = 0xFD;
  /** Comment */
  const COM   = 0xFE;

  /**
   * Check if a byte is a valid JPEG marker.
   *
   * @param PelJpegMarker the byte that will be checked.
   *
   * @return boolean if the byte is recognized true is returned,
   * otherwise false will be returned.
   */
  static function isValid($m) {
    return ($m >= self::SOF0 && $m <= self::COM);
  }
  
  /**
   * Turn a JPEG marker into bytes.
   *
   * @param PelJpegMarker the marker.
   *
   * @return string the marker as a string.  This will be a string
   * with just a single byte since all JPEG markers are simply single
   * bytes.
   */
  static function getBytes($m) {
    return chr($m);
  }

  /**
   * Return the short name for a marker.
   *
   * @param PelJpegMarker the marker.
   *
   * @return string the name of the marker, e.g., 'SOI' for the Start
   * of Image marker.
   */
  static function getName($m) {
    switch ($m) {
    case self::SOF0:  return 'SOF0';
    case self::SOF1:  return 'SOF1';
    case self::SOF2:  return 'SOF2';
    case self::SOF3:  return 'SOF3';
    case self::SOF5:  return 'SOF5';
    case self::SOF6:  return 'SOF6';
    case self::SOF7:  return 'SOF7';
    case self::SOF9:  return 'SOF9';
    case self::SOF10: return 'SOF10';
    case self::SOF11: return 'SOF11';
    case self::SOF13: return 'SOF13';
    case self::SOF14: return 'SOF14';
    case self::SOF15: return 'SOF15';
    case self::SOI:   return 'SOI';
    case self::EOI:   return 'EOI';
    case self::SOS:   return 'SOS';
    case self::COM:   return 'COM';
    case self::DHT:   return 'DHT';
    case self::JPG:   return 'JPG';
    case self::DAC:   return 'DAC';
    case self::RST0:  return 'RST0';
    case self::RST1:  return 'RST1';
    case self::RST2:  return 'RST2';
    case self::RST3:  return 'RST3';
    case self::RST4:  return 'RST4';
    case self::RST5:  return 'RST5';
    case self::RST6:  return 'RST6';
    case self::RST7:  return 'RST7';
    case self::DQT:   return 'DQT';
    case self::DNL:   return 'DNL';
    case self::DRI:   return 'DRI';
    case self::DHP:   return 'DHP';
    case self::EXP:   return 'EXP';
    case self::APP0:  return 'APP0';
    case self::APP1:  return 'APP1';
    case self::APP2:  return 'APP2';
    case self::APP3:  return 'APP3';
    case self::APP4:  return 'APP4';
    case self::APP5:  return 'APP5';
    case self::APP6:  return 'APP6';
    case self::APP7:  return 'APP7';
    case self::APP8:  return 'APP8';
    case self::APP9:  return 'APP9';
    case self::APP10: return 'APP10';
    case self::APP11: return 'APP11';
    case self::APP12: return 'APP12';
    case self::APP13: return 'APP13';
    case self::APP14: return 'APP14';
    case self::APP15: return 'APP15';
    case self::JPG0:  return 'JPG0';
    case self::JPG1:  return 'JPG1';
    case self::JPG2:  return 'JPG2';
    case self::JPG3:  return 'JPG3';
    case self::JPG4:  return 'JPG4';
    case self::JPG5:  return 'JPG5';
    case self::JPG6:  return 'JPG6';
    case self::JPG7:  return 'JPG7';
    case self::JPG8:  return 'JPG8';
    case self::JPG9:  return 'JPG9';
    case self::JPG10: return 'JPG10';
    case self::JPG11: return 'JPG11';
    case self::JPG12: return 'JPG12';
    case self::JPG13: return 'JPG13';
    case self::COM:   return 'COM';
    default:          return Pel::fmt('Unknown marker: 0x%02X', $m);
    }
  }

  /**
   * Returns a description of a JPEG marker.
   *
   * @param PelJpegMarker the marker.
   *
   * @return string the description of the marker.
   */
  static function getDescription($m) {
    switch ($m) {
    case self::SOF0:
      return Pel::tra('Encoding (baseline)');
    case self::SOF1:
      return Pel::tra('Encoding (extended sequential)');
    case self::SOF2:
      return Pel::tra('Encoding (progressive)');
    case self::SOF3:
      return Pel::tra('Encoding (lossless)');
    case self::SOF5:
      return Pel::tra('Encoding (differential sequential)');
    case self::SOF6:
      return Pel::tra('Encoding (differential progressive)');
    case self::SOF7:
      return Pel::tra('Encoding (differential lossless)');
    case self::SOF9:
      return Pel::tra('Encoding (extended sequential, arithmetic)');
    case self::SOF10:
      return Pel::tra('Encoding (progressive, arithmetic)');
    case self::SOF11:
      return Pel::tra('Encoding (lossless, arithmetic)');
    case self::SOF13:
      return Pel::tra('Encoding (differential sequential, arithmetic)');
    case self::SOF14:
      return Pel::tra('Encoding (differential progressive, arithmetic)');
    case self::SOF15:
      return Pel::tra('Encoding (differential lossless, arithmetic)');
    case self::SOI:
      return Pel::tra('Start of image');
    case self::EOI:
      return Pel::tra('End of image');
    case self::SOS:
      return Pel::tra('Start of scan');
    case self::COM:
      return Pel::tra('Comment');
    case self::DHT:
      return Pel::tra('Define Huffman table');
    case self::JPG:
      return Pel::tra('Extension');
    case self::DAC:
      return Pel::tra('Define arithmetic coding conditioning');
    case self::RST0:
      return Pel::fmt('Restart %d', 0);
    case self::RST1:
      return Pel::fmt('Restart %d', 1);
    case self::RST2:
      return Pel::fmt('Restart %d', 2);
    case self::RST3:
      return Pel::fmt('Restart %d', 3);
    case self::RST4:
      return Pel::fmt('Restart %d', 4);
    case self::RST5:
      return Pel::fmt('Restart %d', 5);
    case self::RST6:
      return Pel::fmt('Restart %d', 6);
    case self::RST7:
      return Pel::fmt('Restart %d', 7);
    case self::DQT:
      return Pel::tra('Define quantization table');
    case self::DNL:
      return Pel::tra('Define number of lines');
    case self::DRI:
      return Pel::tra('Define restart interval');
    case self::DHP:
      return Pel::tra('Define hierarchical progression');
    case self::EXP:
      return Pel::tra('Expand reference component');
    case self::APP0:
      return Pel::fmt('Application segment %d', 0);
    case self::APP1:
      return Pel::fmt('Application segment %d', 1);
    case self::APP2:
      return Pel::fmt('Application segment %d', 2);
    case self::APP3:
      return Pel::fmt('Application segment %d', 3);
    case self::APP4:
      return Pel::fmt('Application segment %d', 4);
    case self::APP5:
      return Pel::fmt('Application segment %d', 5);
    case self::APP6:
      return Pel::fmt('Application segment %d', 6);
    case self::APP7:
      return Pel::fmt('Application segment %d', 7);
    case self::APP8:
      return Pel::fmt('Application segment %d', 8);
    case self::APP9:
      return Pel::fmt('Application segment %d', 9);
    case self::APP10:
      return Pel::fmt('Application segment %d', 10);
    case self::APP11:
      return Pel::fmt('Application segment %d', 11);
    case self::APP12:
      return Pel::fmt('Application segment %d', 12);
    case self::APP13:
      return Pel::fmt('Application segment %d', 13);
    case self::APP14:
      return Pel::fmt('Application segment %d', 14);
    case self::APP15:
      return Pel::fmt('Application segment %d', 15);
    case self::JPG0:
      return Pel::fmt('Extension %d', 0);
    case self::JPG1:
      return Pel::fmt('Extension %d', 1);
    case self::JPG2:
      return Pel::fmt('Extension %d', 2);
    case self::JPG3:
      return Pel::fmt('Extension %d', 3);
    case self::JPG4:
      return Pel::fmt('Extension %d', 4);
    case self::JPG5:
      return Pel::fmt('Extension %d', 5);
    case self::JPG6:
      return Pel::fmt('Extension %d', 6);
    case self::JPG7:
      return Pel::fmt('Extension %d', 7);
    case self::JPG8:
      return Pel::fmt('Extension %d', 8);
    case self::JPG9:
      return Pel::fmt('Extension %d', 9);
    case self::JPG10:
      return Pel::fmt('Extension %d', 10);
    case self::JPG11:
      return Pel::fmt('Extension %d', 11);
    case self::JPG12:
      return Pel::fmt('Extension %d', 12);
    case self::JPG13:
      return Pel::fmt('Extension %d', 13);
    case self::COM:
      return Pel::tra('Comment');
    default:
      return Pel::fmt('Unknown marker: 0x%02X', $m);
    }
  }
}




class PelInvalidDataException extends PelException {}

class PelInvalidArgumentException extends PelException {}
class PelFormat {

  const BYTE       =  1;

  const ASCII      =  2;

  const SHORT      =  3;

  const LONG       =  4;

  const RATIONAL   =  5;

  const SBYTE      =  6;

  const UNDEFINED  =  7;

  const SSHORT     =  8;

  const SLONG      =  9;

  const SRATIONAL  = 10;

  const FLOAT      = 11;

  const DOUBLE     = 12;

  static function getName($type) {
    switch ($type) {
    case self::ASCII:     return 'Ascii';
    case self::BYTE:      return 'Byte';
    case self::SHORT:     return 'Short';
    case self::LONG:      return 'Long';
    case self::RATIONAL:  return 'Rational';
    case self::SBYTE:     return 'SByte';
    case self::SSHORT:    return 'SShort';
    case self::SLONG:     return 'SLong';
    case self::SRATIONAL: return 'SRational';
    case self::FLOAT:     return 'Float';
    case self::DOUBLE:    return 'Double';
    case self::UNDEFINED: return 'Undefined';
    default:
      return Pel::fmt('Unknown format: 0x%X', $type);
    }
  }

  static function getSize($type) {
    switch ($type) {
    case self::ASCII:     return 1;
    case self::BYTE:      return 1;
    case self::SHORT:     return 2;
    case self::LONG:      return 4;
    case self::RATIONAL:  return 8;
    case self::SBYTE:     return 1;
    case self::SSHORT:    return 2;
    case self::SLONG:     return 4;
    case self::SRATIONAL: return 8;
    case self::FLOAT:     return 4;
    case self::DOUBLE:    return 8;
    case self::UNDEFINED: return 1;
    default:
      return Pel::fmt('Unknown format: 0x%X', $type);
    }
  }

}
class PelEntryException extends PelException {


  protected $type;

  protected $tag;

  function getIfdType() {
    return $this->type;
  }


  function getTag() {
    return $this->tag;
  }

}

class PelUnexpectedFormatException extends PelEntryException {

  function __construct($type, $tag, $found, $expected) {
    parent::__construct('Unexpected format found for %s tag: PelFormat::%s. ' .
                        'Expected PelFormat::%s instead.',
                        PelTag::getName($type, $tag),
                        strtoupper(PelFormat::getName($found)),
                        strtoupper(PelFormat::getName($expected)));
    $this->tag  = $tag;
    $this->type = $type;
  }
}

class PelWrongComponentCountException extends PelEntryException {

  function __construct($type, $tag, $found, $expected) {
    parent::__construct('Wrong number of components found for %s tag: %d. ' .
                        'Expected %d.',
                        PelTag::getName($type, $tag), $found, $expected);
    $this->tag  = $tag;
    $this->type = $type;
  }
}


abstract class PelEntry {

  protected $ifd_type;

  protected $bytes = '';

  protected $tag;

  protected $format;

  protected $components;

  function getTag() {
    return $this->tag;
  }


  function getIfdType() {
    return $this->ifd_type;
  }

  function setIfdType($type) {
    $this->ifd_type = $type;
  }

  function getFormat() {
    return $this->format;
  }

  function getComponents() {
    return $this->components;
  }


  function getBytes($o) {
    return $this->bytes;
  }


  abstract function getText($brief = false);


  abstract function getValue();

  function setValue($value) {

    throw new PelException('setValue() is abstract.');
  }


  function __toString() {
    $str = Pel::fmt("  Tag: 0x%04X (%s)\n",
                    $this->tag, PelTag::getName($this->ifd_type, $this->tag));
    $str .= Pel::fmt("    Format    : %d (%s)\n",
                     $this->format, PelFormat::getName($this->format));
    $str .= Pel::fmt("    Components: %d\n", $this->components);
    if ($this->getTag() != PelTag::MAKER_NOTE &&
        $this->getTag() != PelTag::PRINT_IM)
      $str .= Pel::fmt("    Value     : %s\n", print_r($this->getValue(), true));
    $str .= Pel::fmt("    Text      : %s\n", $this->getText());
    return $str;
  }
}


class PelTiff {

  const TIFF_HEADER = 0x002A;

  private $ifd = null;

  function __construct($data = false) {
    if ($data === false)
      return;

    if (is_string($data)) {
      Pel::debug('Initializing PelTiff object from %s', $data);
      $this->loadFile($data);
    } elseif ($data instanceof PelDataWindow) {
      Pel::debug('Initializing PelTiff object from PelDataWindow.');
      $this->load($data);
    } else {
      throw new PelInvalidArgumentException('Bad type for $data: %s', 
                                            gettype($data));
    }
  }

  function load(PelDataWindow $d) {
    Pel::debug('Parsing %d bytes of TIFF data...', $d->getSize());

    if ($d->getSize() < 8)
      throw new PelInvalidDataException('Expected at least 8 bytes of TIFF ' .
                                        'data, found just %d bytes.',
                                        $d->getSize());

    /* Byte order */
    if ($d->strcmp(0, 'II')) {
      Pel::debug('Found Intel byte order');
      $d->setByteOrder(PelConvert::LITTLE_ENDIAN);
    } elseif ($d->strcmp(0, 'MM')) {
      Pel::debug('Found Motorola byte order');
      $d->setByteOrder(PelConvert::BIG_ENDIAN);
    } else {
      throw new PelInvalidDataException('Unknown byte order found in TIFF ' .
                                        'data: 0x%2X%2X',
                                        $d->getByte(0), $d->getByte(1));
    }
    
    /* Verify the TIFF header */
    if ($d->getShort(2) != self::TIFF_HEADER)
      throw new PelInvalidDataException('Missing TIFF magic value.');

    /* IFD 0 offset */
    $offset = $d->getLong(4);
    Pel::debug('First IFD at offset %d.', $offset);

    if ($offset > 0) {
      /* Parse the first IFD, this will automatically parse the
       * following IFDs and any sub IFDs. */
      $this->ifd = new PelIfd(PelIfd::IFD0);
      $this->ifd->load($d, $offset);
    }
  }


  function loadFile($filename) {
    $this->load(new PelDataWindow(file_get_contents($filename)));
  }

  function setIfd(PelIfd $ifd) {
    if ($ifd->getType() != PelIfd::IFD0)
      throw new PelInvalidDataException('Invalid type of IFD: %d, expected %d.',
                                        $ifd->getType(), PelIfd::IFD0);

    $this->ifd = $ifd;
  }


  function getIfd() {
    return $this->ifd;
  }


  function getBytes($order = PelConvert::LITTLE_ENDIAN) {
    if ($order == PelConvert::LITTLE_ENDIAN)
      $bytes = 'II';
    else
      $bytes = 'MM';
    
    /* TIFF magic number --- fixed value. */
    $bytes .= PelConvert::shortToBytes(self::TIFF_HEADER, $order);

    if ($this->ifd != null) {

      $bytes .= PelConvert::longToBytes(8, $order);

      $bytes .= $this->ifd->getBytes(8, $order);
    } else {
      $bytes .= PelConvert::longToBytes(0, $order);
    }

    return $bytes;
  }


  function __toString() {
    $str = Pel::fmt("Dumping TIFF data...\n");
    if ($this->ifd != null)
      $str .= $this->ifd->__toString();

    return $str;
  }

  static function isValid(PelDataWindow $d) {
    /* First check that we have enough data. */
    if ($d->getSize() < 8)
      return false;

    /* Byte order */
    if ($d->strcmp(0, 'II')) {
      $d->setByteOrder(PelConvert::LITTLE_ENDIAN);
    } elseif ($d->strcmp(0, 'MM')) {
      Pel::debug('Found Motorola byte order');
      $d->setByteOrder(PelConvert::BIG_ENDIAN);
    } else {
      return false;
    }
    
    /* Verify the TIFF header */
    return $d->getShort(2) == self::TIFF_HEADER;
  }

}

class PelTag {

  /**
   * Interoperability index.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 4.
   */
  const INTEROPERABILITY_INDEX                            = 0x0001;

  /**
   * Interoperability version.
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: 4.
   */
  const INTEROPERABILITY_VERSION                          = 0x0002;

  /**
   * Image width.
   *
   * Format: {@link PelFormat::SHORT} or {@link PelFormat::LONG}.
   *
   * Components: 1.
   */
  const IMAGE_WIDTH                                       = 0x0100;

  /**
   * Image length.
   *
   * Format: {@link PelFormat::SHORT} or {@link PelFormat::LONG}.
   *
   * Components: 1.
   */
  const IMAGE_LENGTH                                      = 0x0101;

  /**
   * Number of bits per component.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 3.
   */
  const BITS_PER_SAMPLE                                   = 0x0102;

  /**
   * Compression scheme.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const COMPRESSION                                       = 0x0103;

  /**
   * Pixel composition.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const PHOTOMETRIC_INTERPRETATION                        = 0x0106;

  /**
   * Fill Orde
   *
   * Format: Unknown.
   *
   * Components: Unknown.
   */
  const FILL_ORDER                                        = 0x010A;

  /**
   * Document Name
   *
   * Format: Unknown.
   *
   * Components: Unknown.
   */
  const DOCUMENT_NAME                                     = 0x010D;

  /**
   * Image Description
   *
   * Format: {@link PelEntryAscii}.
   *
   * Components: any number.
   */
  const IMAGE_DESCRIPTION                                 = 0x010E;

  /**
   * Manufacturer
   *
   * Format: {@link PelEntryAscii}.
   *
   * Components: any number.
   */
  const MAKE                                              = 0x010F;

  /**
   * Model
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: any number.
   */
  const MODEL                                             = 0x0110;

  /**
   * Strip Offsets
   *
   * Format: {@link PelFormat::SHORT} or {@link PelFormat::LONG}.
   *
   * Components: any number.
   */
  const STRIP_OFFSETS                                     = 0x0111;

  /**
   * Orientation of image.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const ORIENTATION                                       = 0x0112;

  /**
   * Number of components.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const SAMPLES_PER_PIXEL                                 = 0x0115;

  /**
   * Rows per Strip
   *
   * Format: {@link PelFormat::SHORT} or {@link PelFormat::LONG}.
   *
   * Components: 1.
   */
  const ROWS_PER_STRIP                                    = 0x0116;

  /**
   * Strip Byte Count
   *
   * Format: {@link PelFormat::SHORT} or {@link PelFormat::LONG}.
   *
   * Components: any number.
   */
  const STRIP_BYTE_COUNTS                                 = 0x0117;

  /**
   * Image resolution in width direction.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const X_RESOLUTION                                      = 0x011A;

  /**
   * Image resolution in height direction.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const Y_RESOLUTION                                      = 0x011B;

  /**
   * Image data arrangement.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const PLANAR_CONFIGURATION                              = 0x011C;

  /**
   * Unit of X and Y resolution.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const RESOLUTION_UNIT                                   = 0x0128;

  /**
   * Transfer function.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 3.
   */
  const TRANSFER_FUNCTION                                 = 0x012D;

  /**
   * Software used.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: any number.
   */
  const SOFTWARE                                          = 0x0131;

  /**
   * File change date and time.
   *
   * Format: {@link PelFormat::ASCII}, modelled by the {@link
   * PelEntryTime} class.
   *
   * Components: 20.
   */
  const DATE_TIME                                         = 0x0132;

  /**
   * Person who created the image.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: any number.
   */
  const ARTIST                                            = 0x013B;

  /**
   * White point chromaticity.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 2.
   */
  const WHITE_POINT                                       = 0x013E;

  /**
   * Chromaticities of primaries.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 6.
   */
  const PRIMARY_CHROMATICITIES                            = 0x013F;

  /**
   * Transfer Range
   *
   * Format: Unknown.
   *
   * Components: Unknown.
   */
  const TRANSFER_RANGE                                    = 0x0156;

  /**
   * JPEGProc
   *
   * Format: Unknown.
   *
   * Components: Unknown.
   */
  const JPEG_PROC                                         = 0x0200;

  /**
   * Offset to JPEG SOI.
   *
   * Format: {@link PelFormat::LONG}.
   *
   * Components: 1.
   */
  const JPEG_INTERCHANGE_FORMAT                           = 0x0201;

  /**
   * Bytes of JPEG data.
   *
   * Format: {@link PelFormat::LONG}.
   *
   * Components: 1.
   */
  const JPEG_INTERCHANGE_FORMAT_LENGTH                    = 0x0202;

  /**
   * Color space transformation matrix coefficients.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 3.
   */
  const YCBCR_COEFFICIENTS                                = 0x0211;

  /**
   * Subsampling ratio of Y to C.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 2.
   */
  const YCBCR_SUB_SAMPLING                                = 0x0212;

  /**
   * Y and C positioning.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const YCBCR_POSITIONING                                 = 0x0213;

  /**
   * Pair of black and white reference values.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 6.
   */
  const REFERENCE_BLACK_WHITE                             = 0x0214;

  /**
   * Related Image File Format
   *
   * Format: Unknown.
   *
   * Components: Unknown.
   */
  const RELATED_IMAGE_FILE_FORMAT                         = 0x1000;

  /**
   * Related Image Width
   *
   * Format: Unknown, probably {@link PelFormat::SHORT}?
   *
   * Components: Unknown, probably 1.
   */
  const RELATED_IMAGE_WIDTH                               = 0x1001;

  /** Related Image Length
   *
   * Format: Unknown, probably {@link PelFormat::SHORT}?
   *
   * Components: Unknown, probably 1.
   */
  const RELATED_IMAGE_LENGTH                              = 0x1002;

  /**
   * CFA Repeat Pattern Dim.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 2.
   */
  const CFA_REPEAT_PATTERN_DIM                            = 0x828D;

  /**
   * Battery level.
   *
   * Format: Unknown.
   *
   * Components: Unknown.
   */
  const BATTERY_LEVEL                                     = 0x828F;

  /**
   * Copyright holder.
   *
   * Format: {@link PelFormat::ASCII}, modelled by the {@link
   * PelEntryCopyright} class.
   *
   * Components: any number.
   */
  const COPYRIGHT                                         = 0x8298;

  /**
   * Exposure Time
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const EXPOSURE_TIME                                     = 0x829A;

  /**
   * FNumber
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const FNUMBER                                           = 0x829D;

  /**
   * IPTC/NAA
   *
   * Format: {@link PelFormat::LONG}.
   *
   * Components: any number.
   */
  const IPTC_NAA                                          = 0x83BB;

  /**
   * Exif IFD Pointer
   *
   * Format: {@link PelFormat::LONG}.
   *
   * Components: 1.
   */
  const EXIF_IFD_POINTER                                  = 0x8769;

  /**
   * Inter Color Profile
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: any number.
   */
  const INTER_COLOR_PROFILE                               = 0x8773;

  /**
   * Exposure Program
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const EXPOSURE_PROGRAM                                  = 0x8822;

  /**
   * Spectral Sensitivity
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: any number.
   */
  const SPECTRAL_SENSITIVITY                              = 0x8824;

  /**
   * GPS Info IFD Pointer
   *
   * Format: {@link PelFormat::LONG}.
   *
   * Components: 1.
   */
  const GPS_INFO_IFD_POINTER                              = 0x8825;

  /**
   * ISO Speed Ratings
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 2.
   */
  const ISO_SPEED_RATINGS                                 = 0x8827;

  /**
   * OECF
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: any number.
   */
  const OECF                                              = 0x8828;

  /**
   * Exif version.
   *
   * Format: {@link PelFormat::UNDEFINED}, modelled by the {@link
   * PelEntryVersion} class.
   *
   * Components: 4.
   */
  const EXIF_VERSION                                      = 0x9000;

  /**
   * Date and time of original data generation.
   *
   * Format: {@link PelFormat::ASCII}, modelled by the {@link
   * PelEntryTime} class.
   *
   * Components: 20.
   */
  const DATE_TIME_ORIGINAL                                = 0x9003;

  /**
   * Date and time of digital data generation.
   *
   * Format: {@link PelFormat::ASCII}, modelled by the {@link
   * PelEntryTime} class.
   *
   * Components: 20.
   */
  const DATE_TIME_DIGITIZED                               = 0x9004;

  /**
   * Meaning of each component.
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: 4.
   */
  const COMPONENTS_CONFIGURATION                          = 0x9101;

  /**
   * Image compression mode.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const COMPRESSED_BITS_PER_PIXEL                         = 0x9102;

  /**
   * Shutter speed
   *
   * Format: {@link PelFormat::SRATIONAL}.
   *
   * Components: 1.
   */
  const SHUTTER_SPEED_VALUE                               = 0x9201;

  /**
   * Aperture
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const APERTURE_VALUE                                    = 0x9202;

  /**
   * Brightness
   *
   * Format: {@link PelFormat::SRATIONAL}.
   *
   * Components: 1.
   */
  const BRIGHTNESS_VALUE                                  = 0x9203;

  /**
   * Exposure Bias
   *
   * Format: {@link PelFormat::SRATIONAL}.
   *
   * Components: 1.
   */
  const EXPOSURE_BIAS_VALUE                               = 0x9204;

  /**
   * Max Aperture Value
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const MAX_APERTURE_VALUE                                = 0x9205;

  /**
   * Subject Distance
   *
   * Format: {@link PelFormat::SRATIONAL}.
   *
   * Components: 1.
   */
  const SUBJECT_DISTANCE                                  = 0x9206;

  /**
   * Metering Mode
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const METERING_MODE                                     = 0x9207;

  /**
   * Light Source
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const LIGHT_SOURCE                                      = 0x9208;

  /**
   * Flash
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const FLASH                                             = 0x9209;

  /**
   * Focal Length
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const FOCAL_LENGTH                                      = 0x920A;

  /**
   * Subject Area
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 4.
   */
  const SUBJECT_AREA                                      = 0x9214;

  /**
   * Maker Note
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: any number.
   */
  const MAKER_NOTE                                        = 0x927C;

  /**
   * User Comment
   *
   * Format: {@link PelFormat::UNDEFINED}, modelled by the {@link
   * PelEntryUserComment} class.
   *
   * Components: any number.
   */
  const USER_COMMENT                                      = 0x9286;

  /**
   * SubSec Time
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: any number.
   */
  const SUB_SEC_TIME                                      = 0x9290;

  /**
   * SubSec Time Original
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: any number.
   */
  const SUB_SEC_TIME_ORIGINAL                             = 0x9291;

  /**
   * SubSec Time Digitized
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: any number.
   */
  const SUB_SEC_TIME_DIGITIZED                            = 0x9292;

  /**
   * Windows XP Title
   *
   * Format: {@link PelFormat::BYTE}.
   *
   * Components: any number.
   */
  const XP_TITLE                                          = 0x9C9B;


  /**
   * Windows XP Comment
   *
   * Format: {@link PelFormat::BYTE}.
   *
   * Components: any number.
   */
  const XP_COMMENT                                        = 0x9C9C;


  /**
   * Windows XP Author
   *
   * Format: {@link PelFormat::BYTE}.
   *
   * Components: any number.
   */
  const XP_AUTHOR                                         = 0x9C9D;


  /**
   * Windows XP Keywords
   *
   * Format: {@link PelFormat::BYTE}.
   *
   * Components: any number.
   */
  const XP_KEYWORDS                                       = 0x9C9E;


  /**
   * Windows XP Subject
   *
   * Format: {@link PelFormat::BYTE}.
   *
   * Components: any number.
   */
  const XP_SUBJECT                                        = 0x9C9F;


  /**
   * Supported Flashpix version
   *
   * Format: {@link PelFormat::UNDEFINED}, modelled by the {@link
   * PelEntryVersion} class.
   *
   * Components: 4.
   */
  const FLASH_PIX_VERSION                                 = 0xA000;

  /**
   * Color space information.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const COLOR_SPACE                                       = 0xA001;

  /**
   * Valid image width.
   *
   * Format: {@link PelFormat::SHORT} or {@link PelFormat::LONG}.
   *
   * Components: 1.
   */
  const PIXEL_X_DIMENSION                                 = 0xA002;

  /**
   * Valid image height.
   *
   * Format: {@link PelFormat::SHORT} or {@link PelFormat::LONG}.
   *
   * Components: 1.
   */
  const PIXEL_Y_DIMENSION                                 = 0xA003;

  /**
   * Related audio file.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: any number.
   */
  const RELATED_SOUND_FILE                                = 0xA004;

  /**
   * Interoperability IFD Pointer
   *
   * Format: {@link PelFormat::LONG}.
   *
   * Components: 1.
   */
  const INTEROPERABILITY_IFD_POINTER                      = 0xA005;

  /**
   * Flash energy.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const FLASH_ENERGY                                      = 0xA20B;

  /**
   * Spatial frequency response.
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: any number.
   */
  const SPATIAL_FREQUENCY_RESPONSE                        = 0xA20C;

  /**
   * Focal plane X resolution.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const FOCAL_PLANE_X_RESOLUTION                          = 0xA20E;

  /**
   * Focal plane Y resolution.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const FOCAL_PLANE_Y_RESOLUTION                          = 0xA20F;

  /**
   * Focal plane resolution unit.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const FOCAL_PLANE_RESOLUTION_UNIT                       = 0xA210;

  /**
   * Subject location.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const SUBJECT_LOCATION                                  = 0xA214;

  /**
   * Exposure index.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const EXPOSURE_INDEX                                    = 0xA215;

  /**
   * Sensing method.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const SENSING_METHOD                                    = 0xA217;

  /**
   * File source.
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: 1.
   */
  const FILE_SOURCE                                       = 0xA300;

  /**
   * Scene type.
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: 1.
   */
  const SCENE_TYPE                                        = 0xA301;

  /**
   * CFA pattern.
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: any number.
   */
  const CFA_PATTERN                                       = 0xA302;

  /**
   * Custom image processing.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const CUSTOM_RENDERED                                   = 0xA401;

  /**
   * Exposure mode.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const EXPOSURE_MODE                                     = 0xA402;

  /**
   * White balance.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const WHITE_BALANCE                                     = 0xA403;

  /**
   * Digital zoom ratio.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const DIGITAL_ZOOM_RATIO                                = 0xA404;

  /**
   * Focal length in 35mm film.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const FOCAL_LENGTH_IN_35MM_FILM      = 0xA405;

  /**
   * Scene capture type.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const SCENE_CAPTURE_TYPE                                = 0xA406;

  /**
   * Gain control.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const GAIN_CONTROL                                      = 0xA407;

  /**
   * Contrast.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const CONTRAST                                          = 0xA408;

  /**
   * Saturation.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const SATURATION                                        = 0xA409;

  /**
   * Sharpness.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const SHARPNESS                                         = 0xA40A;

  /**
   * Device settings description.
   *
   * This tag indicates information on the picture-taking conditions
   * of a particular camera model.  The tag is used only to indicate
   * the picture-taking conditions in the reader.
   */
  const DEVICE_SETTING_DESCRIPTION                        = 0xA40B;

  /**
   * Subject distance range.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const SUBJECT_DISTANCE_RANGE                            = 0xA40C;

  /**
   * Image unique ID.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 32.
   */
  const IMAGE_UNIQUE_ID                                   = 0xA420;

  /**
   * Gamma.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const GAMMA                                             = 0xA500;

  /**
   * PrintIM
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: unknown.
   */
  const PRINT_IM                                          = 0xC4A5;

  /**
   * GPS tag version.
   *
   * Format: {@link PelFormat::BYTE}.
   *
   * Components: 4.
   */
  const GPS_VERSION_ID                                    = 0x0000;

  /**
   * North or South Latitude.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 2.
   */
  const GPS_LATITUDE_REF                                  = 0x0001;

  /**
   * Latitude.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 3.
   */
  const GPS_LATITUDE                                      = 0x0002;

  /**
   * East or West Longitude.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 2.
   */
  const GPS_LONGITUDE_REF                                 = 0x0003;

  /**
   * Longitude.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 3.
   */
  const GPS_LONGITUDE                                     = 0x0004;

  /**
   * Altitude reference.
   *
   * Format: {@link PelFormat::BYTE}.
   *
   * Components: 1.
   */
  const GPS_ALTITUDE_REF                                  = 0x0005;

  /**
   * Altitude.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const GPS_ALTITUDE                                      = 0x0006;

  /**
   * GPS time (atomic clock).
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 3.
   */
  const GPS_TIME_STAMP                                    = 0x0007;

  /**
   * GPS satellites used for measurement.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: Any.
   */
  const GPS_SATELLITES                                    = 0x0008;

  /**
   * GPS receiver status.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 2.
   */
  const GPS_STATUS                                        = 0x0009;

  /**
   * GPS measurement mode.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 2.
   */
  const GPS_MEASURE_MODE                                  = 0x000A;

  /**
   * Measurement precision.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const GPS_DOP                                           = 0x000B;

  /**
   * Speed unit.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 2.
   */
  const GPS_SPEED_REF                                     = 0x000C;

  /**
   * Speed of GPS receiver.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const GPS_SPEED                                         = 0x000D;

  /**
   * Reference for direction of movement.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 2.
   */
  const GPS_TRACK_REF                                     = 0x000E;

  /**
   * Direction of movement.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const GPS_TRACK                                         = 0x000F;

  /**
   * Reference for direction of image.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 2.
   */
  const GPS_IMG_DIRECTION_REF                             = 0x0010;

  /**
   * Direction of image.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const GPS_IMG_DIRECTION                                 = 0x0011;

  /**
   * Geodetic survey data used.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: Any.
   */
  const GPS_MAP_DATUM                                     = 0x0012;

  /**
   * Reference for latitude of destination.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 2.
   */
  const GPS_DEST_LATITUDE_REF                             = 0x0013;

  /**
   * Latitude of destination.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 3.
   */
  const GPS_DEST_LATITUDE                                 = 0x0014;

  /**
   * Reference for longitude of destination.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 2.
   */
  const GPS_DEST_LONGITUDE_REF                            = 0x0015;

  /**
   * Longitude of destination.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 3.
   */
  const GPS_DEST_LONGITUDE                                = 0x0016;

  /**
   * Reference for bearing of destination.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 2.
   */
  const GPS_DEST_BEARING_REF                              = 0x0017;

  /**
   * Bearing of destination.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const GPS_DEST_BEARING                                  = 0x0018;

  /**
   * Reference for distance to destination.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 2.
   */
  const GPS_DEST_DISTANCE_REF                             = 0x0019;

  /**
   * Distance to destination.
   *
   * Format: {@link PelFormat::RATIONAL}.
   *
   * Components: 1.
   */
  const GPS_DEST_DISTANCE                                 = 0x001A;

  /**
   * Name of GPS processing method.
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: Any.
   */
  const GPS_PROCESSING_METHOD                             = 0x001B;

  /**
   * Name of GPS area.
   *
   * Format: {@link PelFormat::UNDEFINED}.
   *
   * Components: Any.
   */
  const GPS_AREA_INFORMATION                              = 0x001C;

  /**
   * GPS date.
   *
   * Format: {@link PelFormat::ASCII}.
   *
   * Components: 11.
   */
  const GPS_DATE_STAMP                                    = 0x001D;

  /**
   * GPS differential correction.
   *
   * Format: {@link PelFormat::SHORT}.
   *
   * Components: 1.
   */
  const GPS_DIFFERENTIAL                                  = 0x001E;


  /**
   * Returns a short name for an Exif tag.
   *
   * @param int the IFD type of the tag, one of {@link PelIfd::IFD0},
   * {@link PelIfd::IFD1}, {@link PelIfd::EXIF}, {@link PelIfd::GPS},
   * or {@link PelIfd::INTEROPERABILITY}.
   *
   * @param PelTag the tag.
   *
   * @return string the short name of the tag, e.g., 'ImageWidth' for
   * the {@link IMAGE_WIDTH} tag.  If the tag is not known, the string
   * 'Unknown:0xTTTT' will be returned where 'TTTT' is the hexadecimal
   * representation of the tag.
   */
  static function getName($type, $tag) {

    switch ($type) {
    case PelIfd::IFD0:
    case PelIfd::IFD1:
    case PelIfd::EXIF:
    case PelIfd::INTEROPERABILITY:

      switch ($tag) {
      case self::INTEROPERABILITY_INDEX:
        return 'InteroperabilityIndex';
      case self::INTEROPERABILITY_VERSION:
        return 'InteroperabilityVersion';
      case self::IMAGE_WIDTH:
        return 'ImageWidth';
      case self::IMAGE_LENGTH:
        return 'ImageLength';
      case self::BITS_PER_SAMPLE:
        return 'BitsPerSample';
      case self::COMPRESSION:
        return 'Compression';
      case self::PHOTOMETRIC_INTERPRETATION:
        return 'PhotometricInterpretation';
      case self::FILL_ORDER:
        return 'FillOrder';
      case self::DOCUMENT_NAME:
        return 'DocumentName';
      case self::IMAGE_DESCRIPTION:
        return 'ImageDescription';
      case self::MAKE:
        return 'Make';
      case self::MODEL:
        return 'Model';
      case self::STRIP_OFFSETS:
        return 'StripOffsets';
      case self::ORIENTATION:
        return 'Orientation';
      case self::SAMPLES_PER_PIXEL:
        return 'SamplesPerPixel';
      case self::ROWS_PER_STRIP:
        return 'RowsPerStrip';
      case self::STRIP_BYTE_COUNTS:
        return 'StripByteCounts';
      case self::X_RESOLUTION:
        return 'XResolution';
      case self::Y_RESOLUTION:
        return 'YResolution';
      case self::PLANAR_CONFIGURATION:
        return 'PlanarConfiguration';
      case self::RESOLUTION_UNIT:
        return 'ResolutionUnit';
      case self::TRANSFER_FUNCTION:
        return 'TransferFunction';
      case self::SOFTWARE:
        return 'Software';
      case self::DATE_TIME:
        return 'DateTime';
      case self::ARTIST:
        return 'Artist';
      case self::WHITE_POINT:
        return 'WhitePoint';
      case self::PRIMARY_CHROMATICITIES:
        return 'PrimaryChromaticities';
      case self::TRANSFER_RANGE:
        return 'TransferRange';
      case self::JPEG_PROC:
        return 'JPEGProc';
      case self::JPEG_INTERCHANGE_FORMAT:
        return 'JPEGInterchangeFormat';
      case self::JPEG_INTERCHANGE_FORMAT_LENGTH:
        return 'JPEGInterchangeFormatLength';
      case self::YCBCR_COEFFICIENTS:
        return 'YCbCrCoefficients';
      case self::YCBCR_SUB_SAMPLING:
        return 'YCbCrSubSampling';
      case self::YCBCR_POSITIONING:
        return 'YCbCrPositioning';
      case self::REFERENCE_BLACK_WHITE:
        return 'ReferenceBlackWhite';
      case self::RELATED_IMAGE_FILE_FORMAT:
        return 'RelatedImageFileFormat';
      case self::RELATED_IMAGE_WIDTH:
        return 'RelatedImageWidth';
      case self::RELATED_IMAGE_LENGTH:
        return 'RelatedImageLength';
      case self::CFA_REPEAT_PATTERN_DIM:
        return 'CFARepeatPatternDim';
      case self::CFA_PATTERN:
        return 'CFAPattern';
      case self::BATTERY_LEVEL:
        return 'BatteryLevel';
      case self::COPYRIGHT:
        return 'Copyright';
      case self::EXPOSURE_TIME:
        return 'ExposureTime';
      case self::FNUMBER:
        return 'FNumber';
      case self::IPTC_NAA:
        return 'IPTC/NAA';
      case self::EXIF_IFD_POINTER:
        return 'ExifIFDPointer';
      case self::INTER_COLOR_PROFILE:
        return 'InterColorProfile';
      case self::EXPOSURE_PROGRAM:
        return 'ExposureProgram';
      case self::SPECTRAL_SENSITIVITY:
        return 'SpectralSensitivity';
      case self::GPS_INFO_IFD_POINTER:
        return 'GPSInfoIFDPointer';
      case self::ISO_SPEED_RATINGS:
        return 'ISOSpeedRatings';
      case self::OECF:
        return 'OECF';
      case self::EXIF_VERSION:
        return 'ExifVersion';
      case self::DATE_TIME_ORIGINAL:
        return 'DateTimeOriginal';
      case self::DATE_TIME_DIGITIZED:
        return 'DateTimeDigitized';
      case self::COMPONENTS_CONFIGURATION:
        return 'ComponentsConfiguration';
      case self::COMPRESSED_BITS_PER_PIXEL:
        return 'CompressedBitsPerPixel';
      case self::SHUTTER_SPEED_VALUE:
        return 'ShutterSpeedValue';
      case self::APERTURE_VALUE:
        return 'ApertureValue';
      case self::BRIGHTNESS_VALUE:
        return 'BrightnessValue';
      case self::EXPOSURE_BIAS_VALUE:
        return 'ExposureBiasValue';
      case self::MAX_APERTURE_VALUE:
        return 'MaxApertureValue';
      case self::SUBJECT_DISTANCE:
        return 'SubjectDistance';
      case self::METERING_MODE:
        return 'MeteringMode';
      case self::LIGHT_SOURCE:
        return 'LightSource';
      case self::FLASH:
        return 'Flash';
      case self::FOCAL_LENGTH:
        return 'FocalLength';
      case self::MAKER_NOTE:
        return 'MakerNote';
      case self::USER_COMMENT:
        return 'UserComment';
      case self::SUB_SEC_TIME:
        return 'SubSecTime';
      case self::SUB_SEC_TIME_ORIGINAL:
        return 'SubSecTimeOriginal';
      case self::SUB_SEC_TIME_DIGITIZED:
        return 'SubSecTimeDigitized';
      case self::XP_TITLE:
        return 'WindowsXPTitle';
      case self::XP_COMMENT:
        return 'WindowsXPComment';
      case self::XP_AUTHOR:
        return 'WindowsXPAuthor';
      case self::XP_KEYWORDS:
        return 'WindowsXPKeywords';
      case self::XP_SUBJECT:
        return 'WindowsXPSubject';
      case self::FLASH_PIX_VERSION:
        return 'FlashPixVersion';
      case self::COLOR_SPACE:
        return 'ColorSpace';
      case self::PIXEL_X_DIMENSION:
        return 'PixelXDimension';
      case self::PIXEL_Y_DIMENSION:
        return 'PixelYDimension';
      case self::RELATED_SOUND_FILE:
        return 'RelatedSoundFile';
      case self::INTEROPERABILITY_IFD_POINTER:
        return 'InteroperabilityIFDPointer';
      case self::FLASH_ENERGY:
        return 'FlashEnergy';
      case self::SPATIAL_FREQUENCY_RESPONSE:
        return 'SpatialFrequencyResponse';
      case self::FOCAL_PLANE_X_RESOLUTION:
        return 'FocalPlaneXResolution';
      case self::FOCAL_PLANE_Y_RESOLUTION:
        return 'FocalPlaneYResolution';
      case self::FOCAL_PLANE_RESOLUTION_UNIT:
        return 'FocalPlaneResolutionUnit';
      case self::SUBJECT_LOCATION:
        return 'SubjectLocation';
      case self::EXPOSURE_INDEX:
        return 'ExposureIndex';
      case self::SENSING_METHOD:
        return 'SensingMethod';
      case self::FILE_SOURCE:
        return 'FileSource';
      case self::SCENE_TYPE:
        return 'SceneType';
      case self::SUBJECT_AREA:
        return 'SubjectArea';
      case self::CUSTOM_RENDERED:
        return 'CustomRendered';
      case self::EXPOSURE_MODE:
        return 'ExposureMode';
      case self::WHITE_BALANCE:
        return 'WhiteBalance';
      case self::DIGITAL_ZOOM_RATIO:
        return 'DigitalZoomRatio';
      case self::FOCAL_LENGTH_IN_35MM_FILM:
        return 'FocalLengthIn35mmFilm';
      case self::SCENE_CAPTURE_TYPE:
        return 'SceneCaptureType';
      case self::GAIN_CONTROL:
        return 'GainControl';
      case self::CONTRAST:
        return 'Contrast';
      case self::SATURATION:
        return 'Saturation';
      case self::SHARPNESS:
        return 'Sharpness';
      case self::DEVICE_SETTING_DESCRIPTION:
        return 'DeviceSettingDescription';
      case self::SUBJECT_DISTANCE_RANGE:
        return 'SubjectDistanceRange';
      case self::IMAGE_UNIQUE_ID:
        return 'ImageUniqueID';
      case self::GAMMA:
        return 'Gamma';
      case self::PRINT_IM:
        return 'PrintIM';
      }

    case PelIfd::GPS:
      switch ($tag) {
      case self::GPS_VERSION_ID:
        return 'GPSVersionID';
      case self::GPS_LATITUDE_REF:
        return 'GPSLatitudeRef';
      case self::GPS_LATITUDE:
        return 'GPSLatitude';
      case self::GPS_LONGITUDE_REF:
        return 'GPSLongitudeRef';
      case self::GPS_LONGITUDE:
        return 'GPSLongitude';
      case self::GPS_ALTITUDE_REF:
        return 'GPSAltitudeRef';
      case self::GPS_ALTITUDE:
        return 'GPSAltitude';
      case self::GPS_TIME_STAMP:
        return 'GPSTimeStamp';
      case self::GPS_SATELLITES:
        return 'GPSSatellites';
      case self::GPS_STATUS:
        return 'GPSStatus';
      case self::GPS_MEASURE_MODE:
        return 'GPSMeasureMode';
      case self::GPS_DOP:
        return 'GPSDOP';
      case self::GPS_SPEED_REF:
        return 'GPSSpeedRef';
      case self::GPS_SPEED:
        return 'GPSSpeed';
      case self::GPS_TRACK_REF:
        return 'GPSTrackRef';
      case self::GPS_TRACK:
        return 'GPSTrack';
      case self::GPS_IMG_DIRECTION_REF:
        return 'GPSImgDirectionRef';
      case self::GPS_IMG_DIRECTION:
        return 'GPSImgDirection';
      case self::GPS_MAP_DATUM:
        return 'GPSMapDatum';
      case self::GPS_DEST_LATITUDE_REF:
        return 'GPSDestLatitudeRef';
      case self::GPS_DEST_LATITUDE:
        return 'GPSDestLatitude';
      case self::GPS_DEST_LONGITUDE_REF:
        return 'GPSDestLongitudeRef';
      case self::GPS_DEST_LONGITUDE:
        return 'GPSDestLongitude';
      case self::GPS_DEST_BEARING_REF:
        return 'GPSDestBearingRef';
      case self::GPS_DEST_BEARING:
        return 'GPSDestBearing';
      case self::GPS_DEST_DISTANCE_REF:
        return 'GPSDestDistanceRef';
      case self::GPS_DEST_DISTANCE:
        return 'GPSDestDistance';
      case self::GPS_PROCESSING_METHOD:
        return 'GPSProcessingMethod';
      case self::GPS_AREA_INFORMATION:
        return 'GPSAreaInformation';
      case self::GPS_DATE_STAMP:
        return 'GPSDateStamp';
      case self::GPS_DIFFERENTIAL:
        return 'GPSDifferential';
      }

    default:
      return Pel::fmt('Unknown: 0x%04X', $tag);
    }
  }


  /**
   * Returns a title for an Exif tag.
   *
   * @param int the IFD type of the tag, one of {@link PelIfd::IFD0},
   * {@link PelIfd::IFD1}, {@link PelIfd::EXIF}, {@link PelIfd::GPS},
   * or {@link PelIfd::INTEROPERABILITY}.
   *
   * @param PelTag the tag.
   *
   * @return string the title of the tag, e.g., 'Image Width' for the
   * {@link IMAGE_WIDTH} tag.  If the tag isn't known, the string
   * 'Unknown Tag: 0xTT' will be returned where 'TT' is the
   * hexadecimal representation of the tag.
   */
  function getTitle($type, $tag) {

    switch ($type) {
    case PelIfd::IFD0:
    case PelIfd::IFD1:
    case PelIfd::EXIF:
    case PelIfd::INTEROPERABILITY:

      switch ($tag) {
      case self::INTEROPERABILITY_INDEX:
        return Pel::tra('Interoperability Index');
      case self::INTEROPERABILITY_VERSION:
        return Pel::tra('Interoperability Version');
      case self::IMAGE_WIDTH:
        return Pel::tra('Image Width');
      case self::IMAGE_LENGTH:
        return Pel::tra('Image Length');
      case self::BITS_PER_SAMPLE:
        return Pel::tra('Bits per Sample');
      case self::COMPRESSION:
        return Pel::tra('Compression');
      case self::PHOTOMETRIC_INTERPRETATION:
        return Pel::tra('Photometric Interpretation');
      case self::FILL_ORDER:
        return Pel::tra('Fill Order');
      case self::DOCUMENT_NAME:
        return Pel::tra('Document Name');
      case self::IMAGE_DESCRIPTION:
        return Pel::tra('Image Description');
      case self::MAKE:
        return Pel::tra('Manufacturer');
      case self::MODEL:
        return Pel::tra('Model');
      case self::STRIP_OFFSETS:
        return Pel::tra('Strip Offsets');
      case self::ORIENTATION:
        return Pel::tra('Orientation');
      case self::SAMPLES_PER_PIXEL:
        return Pel::tra('Samples per Pixel');
      case self::ROWS_PER_STRIP:
        return Pel::tra('Rows per Strip');
      case self::STRIP_BYTE_COUNTS:
        return Pel::tra('Strip Byte Count');
      case self::X_RESOLUTION:
        return Pel::tra('x-Resolution');
      case self::Y_RESOLUTION:
        return Pel::tra('y-Resolution');
      case self::PLANAR_CONFIGURATION:
        return Pel::tra('Planar Configuration');
      case self::RESOLUTION_UNIT:
        return Pel::tra('Resolution Unit');
      case self::TRANSFER_FUNCTION:
        return Pel::tra('Transfer Function');
      case self::SOFTWARE:
        return Pel::tra('Software');
      case self::DATE_TIME:
        return Pel::tra('Date and Time');
      case self::ARTIST:
        return Pel::tra('Artist');
      case self::WHITE_POINT:
        return Pel::tra('White Point');
      case self::PRIMARY_CHROMATICITIES:
        return Pel::tra('Primary Chromaticities');
      case self::TRANSFER_RANGE:
        return Pel::tra('Transfer Range');
      case self::JPEG_PROC:
        return Pel::tra('JPEG Process');
      case self::JPEG_INTERCHANGE_FORMAT:
        return Pel::tra('JPEG Interchange Format');
      case self::JPEG_INTERCHANGE_FORMAT_LENGTH:
        return Pel::tra('JPEG Interchange Format Length');
      case self::YCBCR_COEFFICIENTS:
        return Pel::tra('YCbCr Coefficients');
      case self::YCBCR_SUB_SAMPLING:
        return Pel::tra('YCbCr Sub-Sampling');
      case self::YCBCR_POSITIONING:
        return Pel::tra('YCbCr Positioning');
      case self::REFERENCE_BLACK_WHITE:
        return Pel::tra('Reference Black/White');
      case self::RELATED_IMAGE_FILE_FORMAT:
        return Pel::tra('Related Image File Format');
      case self::RELATED_IMAGE_WIDTH:
        return Pel::tra('Related Image Width');
      case self::RELATED_IMAGE_LENGTH:
        return Pel::tra('Related Image Length');
      case self::CFA_REPEAT_PATTERN_DIM:
        return Pel::tra('CFA Repeat Pattern Dim');
      case self::CFA_PATTERN:
        return Pel::tra('CFA Pattern');
      case self::BATTERY_LEVEL:
        return Pel::tra('Battery Level');
      case self::COPYRIGHT:
        return Pel::tra('Copyright');
      case self::EXPOSURE_TIME:
        return Pel::tra('Exposure Time');
      case self::FNUMBER:
        return Pel::tra('FNumber');
      case self::IPTC_NAA:
        return Pel::tra('IPTC/NAA');
      case self::EXIF_IFD_POINTER:
        return Pel::tra('Exif IFD Pointer');
      case self::INTER_COLOR_PROFILE:
        return Pel::tra('Inter Color Profile');
      case self::EXPOSURE_PROGRAM:
        return Pel::tra('Exposure Program');
      case self::SPECTRAL_SENSITIVITY:
        return Pel::tra('Spectral Sensitivity');
      case self::GPS_INFO_IFD_POINTER:
        return Pel::tra('GPS Info IFD Pointer');
      case self::ISO_SPEED_RATINGS:
        return Pel::tra('ISO Speed Ratings');
      case self::OECF:
        return Pel::tra('OECF');
      case self::EXIF_VERSION:
        return Pel::tra('Exif Version');
      case self::DATE_TIME_ORIGINAL:
        return Pel::tra('Date and Time (original)');
      case self::DATE_TIME_DIGITIZED:
        return Pel::tra('Date and Time (digitized)');
      case self::COMPONENTS_CONFIGURATION:
        return Pel::tra('Components Configuration');
      case self::COMPRESSED_BITS_PER_PIXEL:
        return Pel::tra('Compressed Bits per Pixel');
      case self::SHUTTER_SPEED_VALUE:
        return Pel::tra('Shutter speed');
      case self::APERTURE_VALUE:
        return Pel::tra('Aperture');
      case self::BRIGHTNESS_VALUE:
        return Pel::tra('Brightness');
      case self::EXPOSURE_BIAS_VALUE:
        return Pel::tra('Exposure Bias');
      case self::MAX_APERTURE_VALUE:
        return Pel::tra('Max Aperture Value');
      case self::SUBJECT_DISTANCE:
        return Pel::tra('Subject Distance');
      case self::METERING_MODE:
        return Pel::tra('Metering Mode');
      case self::LIGHT_SOURCE:
        return Pel::tra('Light Source');
      case self::FLASH:
        return Pel::tra('Flash');
      case self::FOCAL_LENGTH:
        return Pel::tra('Focal Length');
      case self::MAKER_NOTE:
        return Pel::tra('Maker Note');
      case self::USER_COMMENT:
        return Pel::tra('User Comment');
      case self::SUB_SEC_TIME:
        return Pel::tra('SubSec Time');
      case self::SUB_SEC_TIME_ORIGINAL:
        return Pel::tra('SubSec Time Original');
      case self::SUB_SEC_TIME_DIGITIZED:
        return Pel::tra('SubSec Time Digitized');
      case self::XP_TITLE:
        return 'Windows XP Title';
      case self::XP_COMMENT:
        return 'Windows XP Comment';
      case self::XP_AUTHOR:
        return 'Windows XP Author';
      case self::XP_KEYWORDS:
        return 'Windows XP Keywords';
      case self::XP_SUBJECT:
        return 'Windows XP Subject';
      case self::FLASH_PIX_VERSION:
        return Pel::tra('FlashPix Version');
      case self::COLOR_SPACE:
        return Pel::tra('Color Space');
      case self::PIXEL_X_DIMENSION:
        return Pel::tra('Pixel x-Dimension');
      case self::PIXEL_Y_DIMENSION:
        return Pel::tra('Pixel y-Dimension');
      case self::RELATED_SOUND_FILE:
        return Pel::tra('Related Sound File');
      case self::INTEROPERABILITY_IFD_POINTER:
        return Pel::tra('Interoperability IFD Pointer');
      case self::FLASH_ENERGY:
        return Pel::tra('Flash Energy');
      case self::SPATIAL_FREQUENCY_RESPONSE:
        return Pel::tra('Spatial Frequency Response');
      case self::FOCAL_PLANE_X_RESOLUTION:
        return Pel::tra('Focal Plane x-Resolution');
      case self::FOCAL_PLANE_Y_RESOLUTION:
        return Pel::tra('Focal Plane y-Resolution');
      case self::FOCAL_PLANE_RESOLUTION_UNIT:
        return Pel::tra('Focal Plane Resolution Unit');
      case self::SUBJECT_LOCATION:
        return Pel::tra('Subject Location');
      case self::EXPOSURE_INDEX:
        return Pel::tra('Exposure index');
      case self::SENSING_METHOD:
        return Pel::tra('Sensing Method');
      case self::FILE_SOURCE:
        return Pel::tra('File Source');
      case self::SCENE_TYPE:
        return Pel::tra('Scene Type');
      case self::SUBJECT_AREA:
        return Pel::tra('Subject Area');
      case self::CUSTOM_RENDERED:
        return Pel::tra('Custom Rendered');
      case self::EXPOSURE_MODE:
        return Pel::tra('Exposure Mode');
      case self::WHITE_BALANCE:
        return Pel::tra('White Balance');
      case self::DIGITAL_ZOOM_RATIO:
        return Pel::tra('Digital Zoom Ratio');
      case self::FOCAL_LENGTH_IN_35MM_FILM:
        return Pel::tra('Focal Length In 35mm Film');
      case self::SCENE_CAPTURE_TYPE:
        return Pel::tra('Scene Capture Type');
      case self::GAIN_CONTROL:
        return Pel::tra('Gain Control');
      case self::CONTRAST:
        return Pel::tra('Contrast');
      case self::SATURATION:
        return Pel::tra('Saturation');
      case self::SHARPNESS:
        return Pel::tra('Sharpness');
      case self::DEVICE_SETTING_DESCRIPTION:
        return Pel::tra('Device Setting Description');
      case self::SUBJECT_DISTANCE_RANGE:
        return Pel::tra('Subject Distance Range');
      case self::IMAGE_UNIQUE_ID:
        return Pel::tra('Image Unique ID');
      case self::GAMMA:
        return Pel::tra('Gamma');
      case self::PRINT_IM:
        return Pel::tra('Print IM');
      }

    case PelIfd::GPS:
      switch ($tag) {
      case self::GPS_VERSION_ID:
        return 'GPSVersionID';
      case self::GPS_LATITUDE_REF:
        return 'GPSLatitudeRef';
      case self::GPS_LATITUDE:
        return 'GPSLatitude';
      case self::GPS_LONGITUDE_REF:
        return 'GPSLongitudeRef';
      case self::GPS_LONGITUDE:
        return 'GPSLongitude';
      case self::GPS_ALTITUDE_REF:
        return 'GPSAltitudeRef';
      case self::GPS_ALTITUDE:
        return 'GPSAltitude';
      case self::GPS_TIME_STAMP:
        return 'GPSTimeStamp';
      case self::GPS_SATELLITES:
        return 'GPSSatellites';
      case self::GPS_STATUS:
        return 'GPSStatus';
      case self::GPS_MEASURE_MODE:
        return 'GPSMeasureMode';
      case self::GPS_DOP:
        return 'GPSDOP';
      case self::GPS_SPEED_REF:
        return 'GPSSpeedRef';
      case self::GPS_SPEED:
        return 'GPSSpeed';
      case self::GPS_TRACK_REF:
        return 'GPSTrackRef';
      case self::GPS_TRACK:
        return 'GPSTrack';
      case self::GPS_IMG_DIRECTION_REF:
        return 'GPSImgDirectionRef';
      case self::GPS_IMG_DIRECTION:
        return 'GPSImgDirection';
      case self::GPS_MAP_DATUM:
        return 'GPSMapDatum';
      case self::GPS_DEST_LATITUDE_REF:
        return 'GPSDestLatitudeRef';
      case self::GPS_DEST_LATITUDE:
        return 'GPSDestLatitude';
      case self::GPS_DEST_LONGITUDE_REF:
        return 'GPSDestLongitudeRef';
      case self::GPS_DEST_LONGITUDE:
        return 'GPSDestLongitude';
      case self::GPS_DEST_BEARING_REF:
        return 'GPSDestBearingRef';
      case self::GPS_DEST_BEARING:
        return 'GPSDestBearing';
      case self::GPS_DEST_DISTANCE_REF:
        return 'GPSDestDistanceRef';
      case self::GPS_DEST_DISTANCE:
        return 'GPSDestDistance';
      case self::GPS_PROCESSING_METHOD:
        return 'GPSProcessingMethod';
      case self::GPS_AREA_INFORMATION:
        return 'GPSAreaInformation';
      case self::GPS_DATE_STAMP:
        return 'GPSDateStamp';
      case self::GPS_DIFFERENTIAL:
        return 'GPSDifferential';
      }

    default:
      return Pel::fmt('Unknown Tag: 0x%04X', $tag);
    }
  }

}
class PelEntryUndefined extends PelEntry {

  /**
   * Make a new PelEntry that can hold undefined data.
   *
   * @param PelTag the tag which this entry represents.  This
   * should be one of the constants defined in {@link PelTag},
   * e.g., {@link PelTag::SCENE_TYPE}, {@link
   * PelTag::MAKER_NOTE} or any other tag with format {@link
   * PelFormat::UNDEFINED}.
   *
   * @param string the data that this entry will be holding.  Since
   * the format is undefined, no checking will be done on the data.
   */
  function __construct($tag, $data = '') {
    $this->tag        = $tag;
    $this->format     = PelFormat::UNDEFINED;
    $this->setValue($data);
  }


  /**
   * Set the data of this undefined entry.
   *
   * @param string the data that this entry will be holding.  Since
   * the format is undefined, no checking will be done on the data.
   */
  function setValue($data) {
    $this->components = strlen($data);
    $this->bytes      = $data;
  }


  /**
   * Get the data of this undefined entry.
   *
   * @return string the data that this entry is holding.
   */
  function getValue() {
    return $this->bytes;
  }


  /**
   * Get the value of this entry as text.
   *
   * The value will be returned in a format suitable for presentation.
   *
   * @param boolean some values can be returned in a long or more
   * brief form, and this parameter controls that.
   *
   * @return string the value as text.
   */
  function getText($brief = false) {
    switch ($this->tag) {
    case PelTag::FILE_SOURCE:
      //CC (e->components, 1, v);
      switch (ord($this->bytes{0})) {
      case 0x03:
        return 'DSC';
      default:
        return sprintf('0x%02X', ord($this->bytes{0}));
      }
   
    case PelTag::SCENE_TYPE:
      //CC (e->components, 1, v);
      switch (ord($this->bytes{0})) {
      case 0x01:
        return 'Directly photographed';
      default:
        return sprintf('0x%02X', ord($this->bytes{0}));
      }
   
    case PelTag::COMPONENTS_CONFIGURATION:
      //CC (e->components, 4, v);
      $v = '';
      for ($i = 0; $i < 4; $i++) {
        switch (ord($this->bytes{$i})) {
        case 0:
          $v .= '-';
          break;
        case 1:
          $v .= 'Y';
          break;
        case 2:
          $v .= 'Cb';
          break;
        case 3:
          $v .= 'Cr';
          break;
        case 4:
          $v .= 'R';
          break;
        case 5:
          $v .= 'G';
          break;
        case 6:
          $v .= 'B';
          break;
        default:
          $v .= 'reserved';
          break;
        }
        if ($i < 3) $v .= ' ';
      }
      return $v;

    case PelTag::MAKER_NOTE:
      // TODO: handle maker notes.
      return $this->components . ' bytes unknown MakerNote data';

    default:
      return '(undefined)';
    }
  }

}


class PelEntryUserComment extends PelEntryUndefined {

  /**
   * The user comment.
   *
   * @var string
   */
  private $comment;

  /**
   * The encoding.
   *
   * This should be one of 'ASCII', 'JIS', 'Unicode', or ''.
   *
   * @var string
   */
  private $encoding;

  /**
   * Make a new entry for holding a user comment.
   *
   * @param string the new user comment.
   *
   * @param string the encoding of the comment.  This should be either
   * 'ASCII', 'JIS', 'Unicode', or the empty string specifying an
   * undefined encoding.
   */
  function __construct($comment = '', $encoding = 'ASCII') {
    parent::__construct(PelTag::USER_COMMENT);
    $this->setValue($comment, $encoding);
  }

  
  /**
   * Set the user comment.
   *
   * @param string the new user comment.
   *
   * @param string the encoding of the comment.  This should be either
   * 'ASCII', 'JIS', 'Unicode', or the empty string specifying an
   * unknown encoding.
   */
  function setValue($comment = '', $encoding = 'ASCII') {
    $this->comment  = $comment;
    $this->encoding = $encoding;
    parent::setValue(str_pad($encoding, 8, chr(0)) . $comment);
  }


  /**
   * Returns the user comment.
   *
   * The comment is returned with the same character encoding as when
   * it was set using {@link setValue} or {@link __construct the
   * constructor}.
   *
   * @return string the user comment.
   */
  function getValue() {
    return $this->comment;
  }


  /**
   * Returns the encoding.
   *
   * @return string the encoding of the user comment.
   */
  function getEncoding() {
    return $this->encoding;
  }


  /**
   * Returns the user comment.
   *
   * @return string the user comment.
   */
  function getText($brief = false) {
    return $this->comment;
  }

}
abstract class PelEntryNumber extends PelEntry {

  /**
   * The value held by this entry.
   *
   * @var array
   */
  protected $value = array();

  /**
   * The minimum allowed value.
   *
   * Any attempt to change the value below this variable will result
   * in a {@link PelOverflowException} being thrown.
   *
   * @var int
   */
  protected $min;

  /**
   * The maximum allowed value.
   *
   * Any attempt to change the value over this variable will result in
   * a {@link PelOverflowException} being thrown.
   *
   * @var int
   */
  protected $max;
  
  /**
   * The dimension of the number held.
   *
   * Normal numbers have a dimension of one, pairs have a dimension of
   * two, etc.
   *
   * @var int
   */
  protected $dimension = 1;


  /**
   * Change the value.
   *
   * This method can change both the number of components and the
   * value of the components.  Range checks will be made on the new
   * value, and a {@link PelOverflowException} will be thrown if the
   * value is found to be outside the legal range.
   *
   * The method accept several number arguments.  The {@link getValue}
   * method will always return an array except for when a single
   * number is given here.
   *
   * @param int|array $value... the new value(s).  This can be zero or
   * more numbers, that is, either integers or arrays.  The input will
   * be checked to ensure that the numbers are within the valid range.
   * If not, then a {@link PelOverflowException} will be thrown.
   *
   * @see getValue
   */
  function setValue(/* $value... */) {
    $value = func_get_args();
    $this->setValueArray($value);
  }


  /**
   * Change the value.
   *
   * This method can change both the number of components and the
   * value of the components.  Range checks will be made on the new
   * value, and a {@link PelOverflowException} will be thrown if the
   * value is found to be outside the legal range.
   *
   * @param array the new values.  The array must contain the new
   * numbers.
   *
   * @see getValue
   */
  function setValueArray($value) {
    foreach ($value as $v)
      $this->validateNumber($v);
    
    $this->components = count($value);
    $this->value      = $value;
  }


  /**
   * Return the numeric value held.
   *
   * @return int|array this will either be a single number if there is
   * only one component, or an array of numbers otherwise.
   */
  function getValue() {
    if ($this->components == 1)
      return $this->value[0];
    else
      return $this->value;
  }


  /**
   * Validate a number.
   *
   * This method will check that the number given is within the range
   * given my {@link getMin()} and {@link getMax()}, inclusive.  If
   * not, then a {@link PelOverflowException} is thrown.
   *
   * @param int|array the number in question.
   *
   * @return void nothing, but will throw a {@link
   * PelOverflowException} if the number is found to be outside the
   * legal range and {@link Pel::$strict} is true.
   */
  function validateNumber($n) {
    if ($this->dimension == 1) {
      if ($n < $this->min || $n > $this->max)
        Pel::maybeThrow(new PelOverflowException($n,
                                                 $this->min,
                                                 $this->max));
    } else {
      for ($i = 0; $i < $this->dimension; $i++)
        if ($n[$i] < $this->min || $n[$i] > $this->max)
          Pel::maybeThrow(new PelOverflowException($n[$i],
                                                   $this->min,
                                                   $this->max));
    }
  }


  /**
   * Add a number.
   *
   * This appends a number to the numbers already held by this entry,
   * thereby increasing the number of components by one.
   *
   * @param int|array the number to be added.
   */
  function addNumber($n) {
    $this->validateNumber($n);
    $this->value[] = $n;
    $this->components++;
  }


  /**
   * Convert a number into bytes.
   *
   * The concrete subclasses will have to implement this method so
   * that the numbers represented can be turned into bytes.
   *
   * The method will be called once for each number held by the entry.
   *
   * @param int the number that should be converted.
   *
   * @param PelByteOrder one of {@link PelConvert::LITTLE_ENDIAN} and
   * {@link PelConvert::BIG_ENDIAN}, specifying the target byte order.
   *
   * @return string bytes representing the number given.
   */
  abstract function numberToBytes($number, $order);

  
  /**
   * Turn this entry into bytes.
   *
   * @param PelByteOrder the desired byte order, which must be either
   * {@link PelConvert::LITTLE_ENDIAN} or {@link
   * PelConvert::BIG_ENDIAN}.
   *
   * @return string bytes representing this entry.
   */
  function getBytes($o) {
    $bytes = '';
    for ($i = 0; $i < $this->components; $i++) {
      if ($this->dimension == 1) {
        $bytes .= $this->numberToBytes($this->value[$i], $o);
      } else {
        for ($j = 0; $j < $this->dimension; $j++) {
          $bytes .= $this->numberToBytes($this->value[$i][$j], $o);
        }
      }
    }
    return $bytes;
  }


  /**
   * Format a number.
   *
   * This method is called by {@link getText} to format numbers.
   * Subclasses should override this method if they need more
   * sophisticated behavior than the default, which is to just return
   * the number as is.
   *
   * @param int the number which will be formatted.
   *
   * @param boolean it could be that there is both a verbose and a
   * brief formatting available, and this argument controls that.
   *
   * @return string the number formatted as a string suitable for
   * display.
   */
  function formatNumber($number, $brief = false) {
    return $number;
  }


  /**
   * Get the numeric value of this entry as text.
   *
   * @param boolean use brief output?  The numbers will be separated
   * by a single space if brief output is requested, otherwise a space
   * and a comma will be used.
   *
   * @return string the numbers(s) held by this entry.
   */
  function getText($brief = false) {
    if ($this->components == 0)
      return '';

    $str = $this->formatNumber($this->value[0]);
    for ($i = 1; $i < $this->components; $i++) {
      $str .= ($brief ? ' ' : ', ');
      $str .= $this->formatNumber($this->value[$i]);
    }

    return $str;
  }

}
class PelEntryLong extends PelEntryNumber {

  /**
   * Make a new entry that can hold an unsigned long.
   *
   * The method accept its arguments in two forms: several integer
   * arguments or a single array argument.  The {@link getValue}
   * method will always return an array except for when a single
   * integer argument is given here, or when an array with just a
   * single integer is given.
   *
   * This means that one can conveniently use objects like this:
   * <code>
   * $a = new PelEntryLong(PelTag::EXIF_IMAGE_WIDTH, 123456);
   * $b = $a->getValue() - 654321;
   * </code>
   * where the call to {@link getValue} will return an integer instead
   * of an array with one integer element, which would then have to be
   * extracted.
   *
   * @param PelTag the tag which this entry represents.  This
   * should be one of the constants defined in {@link PelTag},
   * e.g., {@link PelTag::IMAGE_WIDTH}, or any other tag which can
   * have format {@link PelFormat::LONG}.
   *
   * @param int $value... the long(s) that this entry will
   * represent or an array of longs.  The argument passed must obey
   * the same rules as the argument to {@link setValue}, namely that
   * it should be within range of an unsigned long (32 bit), that is
   * between 0 and 4294967295 (inclusive).  If not, then a {@link
   * PelExifOverflowException} will be thrown.
   */
  function __construct($tag /* $value... */) {
    $this->tag    = $tag;
    $this->min    = 0;
    $this->max    = 4294967295;
    $this->format = PelFormat::LONG;

    $value = func_get_args();
    array_shift($value);
    $this->setValueArray($value);
  }



  function numberToBytes($number, $order) {
    return PelConvert::longToBytes($number, $order);
  }
}

class PelEntryVersion extends PelEntryUndefined {

  /**
   * The version held by this entry.
   *
   * @var float
   */
  private $version;


  /**
   * Make a new entry for holding a version.
   *
   * @param PelTag the tag.  This should be one of {@link
   * PelTag::EXIF_VERSION}, {@link PelTag::FLASH_PIX_VERSION},
   * or {@link PelTag::INTEROPERABILITY_VERSION}.
   *
   * @param float the version.  The size of the entries leave room for
   * exactly four digits: two digits on either side of the decimal
   * point.
   */
  function __construct($tag, $version = 0.0) {
    parent::__construct($tag);
    $this->setValue($version);
  }


  /**
   * Set the version held by this entry.
   *
   * @param float the version.  The size of the entries leave room for
   * exactly four digits: two digits on either side of the decimal
   * point.
   */
  function setValue($version = 0.0) {
    $this->version = $version;
    $major = floor($version);
    $minor = ($version - $major)*100;
    parent::setValue(sprintf('%02.0f%02.0f', $major, $minor));
  }


  /**
   * Return the version held by this entry.
   *
   * @return float the version.  This will be the same as the value
   * given to {@link setValue} or {@link __construct the
   * constructor}.
   */
  function getValue() {
    return $this->version;
  }

 
  /**
   * Return a text string with the version.
   *
   * @param boolean controls if the output should be brief.  Brief
   * output omits the word 'Version' so the result is just 'Exif x.y'
   * instead of 'Exif Version x.y' if the entry holds information
   * about the Exif version --- the output for FlashPix is similar.
   *
   * @return string the version number with the type of the tag,
   * either 'Exif' or 'FlashPix'.
   */
  function getText($brief = false) {
    $v = $this->version;

    /* Versions numbers like 2.0 would be output as just 2 if we don't
     * add the '.0' ourselves. */
    if (floor($this->version) == $this->version)
      $v .= '.0';

    switch ($this->tag) {
    case PelTag::EXIF_VERSION:
      if ($brief)
        return Pel::fmt('Exif %s', $v);
      else
        return Pel::fmt('Exif Version %s', $v);
      
    case PelTag::FLASH_PIX_VERSION:
      if ($brief)
        return Pel::fmt('FlashPix %s', $v);
      else
        return Pel::fmt('FlashPix Version %s', $v);
      
    case PelTag::INTEROPERABILITY_VERSION:
      if ($brief)
        return Pel::fmt('Interoperability %s', $v);
      else
        return Pel::fmt('Interoperability Version %s', $v);
    }

    if ($brief)
      return $v;
    else
      return Pel::fmt('Version %s', $v);
    
  }

}

class PelEntryRational extends PelEntryLong {

  /**
   * Make a new entry that can hold an unsigned rational.
   *
   * @param PelTag the tag which this entry represents.  This should
   * be one of the constants defined in {@link PelTag}, e.g., {@link
   * PelTag::X_RESOLUTION}, or any other tag which can have format
   * {@link PelFormat::RATIONAL}.
   *
   * @param array $value... the rational(s) that this entry will
   * represent.  The arguments passed must obey the same rules as the
   * argument to {@link setValue}, namely that each argument should be
   * an array with two entries, both of which must be within range of
   * an unsigned long (32 bit), that is between 0 and 4294967295
   * (inclusive).  If not, then a {@link PelOverflowException} will be
   * thrown.
   */
  function __construct($tag /* $value... */) {
    $this->tag       = $tag;
    $this->format    = PelFormat::RATIONAL;
    $this->dimension = 2;
    $this->min       = 0;
    $this->max       = 4294967295;

    $value = func_get_args();
    array_shift($value);
    $this->setValueArray($value);
  }


  /**
   * Format a rational number.
   *
   * The rational will be returned as a string with a slash '/'
   * between the numerator and denominator.
   *
   * @param array the rational which will be formatted.
   *
   * @param boolean not used.
   *
   * @return string the rational formatted as a string suitable for
   * display.
   */
  function formatNumber($number, $brief = false) {
    return $number[0] . '/' . $number[1];
  }


  /**
   * Get the value of an entry as text.
   *
   * The value will be returned in a format suitable for presentation,
   * e.g., rationals will be returned as 'x/y', ASCII strings will be
   * returned as themselves etc.
   *
   * @param boolean some values can be returned in a long or more
   * brief form, and this parameter controls that.
   *
   * @return string the value as text.
   */
  function getText($brief = false) {
    if (isset($this->value[0]))
      $v = $this->value[0];

    switch ($this->tag) {
    case PelTag::FNUMBER:
      //CC (e->components, 1, v);
      return Pel::fmt('f/%.01f', $v[0]/$v[1]);

    case PelTag::APERTURE_VALUE:
      //CC (e->components, 1, v);
      //if (!v_rat.denominator) return (NULL);
      return Pel::fmt('f/%.01f', pow(2, $v[0]/$v[1]/2));

    case PelTag::FOCAL_LENGTH:
      //CC (e->components, 1, v);
      //if (!v_rat.denominator) return (NULL);
      return Pel::fmt('%.1f mm', $v[0]/$v[1]);

    case PelTag::SUBJECT_DISTANCE:
      //CC (e->components, 1, v);
      //if (!v_rat.denominator) return (NULL);
      return Pel::fmt('%.1f m', $v[0]/$v[1]);

    case PelTag::EXPOSURE_TIME:
      //CC (e->components, 1, v);
      //if (!v_rat.denominator) return (NULL);
      if ($v[0]/$v[1] < 1)
        return Pel::fmt('1/%d sec.', $v[1]/$v[0]);
      else
        return Pel::fmt('%d sec.', $v[0]/$v[1]);

    case PelTag::GPS_LATITUDE:
    case PelTag::GPS_LONGITUDE:
      $degrees = $this->value[0][0]/$this->value[0][1];
      $minutes = $this->value[1][0]/$this->value[1][1];
      $seconds = $this->value[2][0]/$this->value[2][1];

      return sprintf('%s° %s\' %s" (%.2f°)',
                     $degrees, $minutes, $seconds,
                     $degrees + $minutes/60 + $seconds/3600);

    default:
      return parent::getText($brief);
    }
  }
}



class PelEntrySRational extends PelEntrySLong {

  /**
   * Make a new entry that can hold a signed rational.
   *
   * @param PelTag the tag which this entry represents.  This should
   * be one of the constants defined in {@link PelTag}, e.g., {@link
   * PelTag::SHUTTER_SPEED_VALUE}, or any other tag which can have
   * format {@link PelFormat::SRATIONAL}.
   *
   * @param array $value... the rational(s) that this entry will
   * represent.  The arguments passed must obey the same rules as the
   * argument to {@link setValue}, namely that each argument should be
   * an array with two entries, both of which must be within range of
   * a signed long (32 bit), that is between -2147483648 and
   * 2147483647 (inclusive).  If not, then a {@link
   * PelOverflowException} will be thrown.
   */
  function __construct($tag /* $value... */) {
    $this->tag       = $tag;
    $this->format    = PelFormat::SRATIONAL;
    $this->dimension = 2;
    $this->min       = -2147483648;
    $this->max       = 2147483647;

    $value = func_get_args();
    array_shift($value);
    $this->setValueArray($value);
  }


  /**
   * Format a rational number.
   *
   * The rational will be returned as a string with a slash '/'
   * between the numerator and denominator.  Care is taken to display
   * '-1/2' instead of the ugly but mathematically equivalent '1/-2'.
   *
   * @param array the rational which will be formatted.
   *
   * @param boolean not used.
   *
   * @return string the rational formatted as a string suitable for
   * display.
   */
  function formatNumber($number, $brief = false) {
    if ($number[1] < 0)
      /* Turn output like 1/-2 into -1/2. */
      return (-$number[0]) . '/' . (-$number[1]);
    else
      return $number[0] . '/' . $number[1];
  }


  /**
   * Get the value of an entry as text.
   *
   * The value will be returned in a format suitable for presentation,
   * e.g., rationals will be returned as 'x/y', ASCII strings will be
   * returned as themselves etc.
   *
   * @param boolean some values can be returned in a long or more
   * brief form, and this parameter controls that.
   *
   * @return string the value as text.
   */
  function getText($brief = false) {
    if (isset($this->value[0]))
      $v = $this->value[0];

    switch ($this->tag) {
    case PelTag::SHUTTER_SPEED_VALUE:
      //CC (e->components, 1, v);
      //if (!v_srat.denominator) return (NULL);
      return Pel::fmt('%.0f/%.0f sec. (APEX: %d)',
                      $v[0], $v[1], pow(sqrt(2), $v[0]/$v[1]));

    case PelTag::BRIGHTNESS_VALUE:
      //CC (e->components, 1, v);
      //
      // TODO: figure out the APEX thing, or remove this so that it is
      // handled by the default clause at the bottom.
      return sprintf('%d/%d', $v[0], $v[1]);
      //FIXME: How do I calculate the APEX value?

    case PelTag::EXPOSURE_BIAS_VALUE:
      //CC (e->components, 1, v);
      //if (!v_srat.denominator) return (NULL);
      return sprintf('%s%.01f', $v[0]*$v[1] > 0 ? '+' : '', $v[0]/$v[1]);

    default:
      return parent::getText($brief);
    }
  }

}

class PelEntryAscii extends PelEntry {

  /**
   * The string hold by this entry.
   *
   * This is the string that was given to the {@link __construct
   * constructor} or later to {@link setValue}, without any final NULL
   * character.
   *
   * @var string
   */
  private $str;


  /**
   * Make a new PelEntry that can hold an ASCII string.
   *
   * @param int the tag which this entry represents.  This should be
   * one of the constants defined in {@link PelTag}, e.g., {@link
   * PelTag::IMAGE_DESCRIPTION}, {@link PelTag::MODEL}, or any other
   * tag with format {@link PelFormat::ASCII}.
   *
   * @param string the string that this entry will represent.  The
   * string must obey the same rules as the string argument to {@link
   * setValue}, namely that it should be given without any trailing
   * NULL character and that it must be plain 7-bit ASCII.
   */
  function __construct($tag, $str = '') {
    $this->tag    = $tag;
    $this->format = PelFormat::ASCII;
    $this->setValue($str);
  }


  /**
   * Give the entry a new ASCII value.
   *
   * This will overwrite the previous value.  The value can be
   * retrieved later with the {@link getValue} method.
   *
   * @param string the new value of the entry.  This should be given
   * without any trailing NULL character.  The string must be plain
   * 7-bit ASCII, the string should contain no high bytes.
   *
   * @todo Implement check for high bytes?
   */
  function setValue($str) {
    $this->components = strlen($str)+1;
    $this->str        = $str;
    $this->bytes      = $str . chr(0x00);
  }


  /**
   * Return the ASCII string of the entry.
   *
   * @return string the string held, without any final NULL character.
   * The string will be the same as the one given to {@link setValue}
   * or to the {@link __construct constructor}.
   */
  function getValue() {
    return $this->str;
  }


  /**
   * Return the ASCII string of the entry.
   *
   * This methods returns the same as {@link getValue}.
   *
   * @param boolean not used with ASCII entries.
   *
   * @return string the string held, without any final NULL character.
   * The string will be the same as the one given to {@link setValue}
   * or to the {@link __construct constructor}.
   */
  function getText($brief = false) {
    return $this->str;      
  }

}



class PelEntryTime extends PelEntryAscii {

  /**
   * Constant denoting a UNIX timestamp.
   */
  const UNIX_TIMESTAMP   = 1;
  /**
   * Constant denoting a Exif string.
   */
  const EXIF_STRING      = 2;
  /**
   * Constant denoting a Julian Day Count.
   */
  const JULIAN_DAY_COUNT = 3;

  /**
   * The Julian Day Count of the timestamp held by this entry.
   *
   * This is an integer counting the number of whole days since
   * January 1st, 4713 B.C. The fractional part of the timestamp held
   * by this entry is stored in {@link $seconds}.
   *
   * @var int
   */
  private $day_count;

  /**
   * The number of seconds into the day of the timestamp held by this
   * entry.
   *
   * The number of whole days is stored in {@link $day_count} and the
   * number of seconds left-over is stored here.
   *
   * @var int
   */
  private $seconds;


  /**
   * Make a new entry for holding a timestamp.
   *
   * @param int the Exif tag which this entry represents.  There are
   * only three standard tags which hold timestamp, so this should be
   * one of the constants {@link PelTag::DATE_TIME}, {@link
   * PelTag::DATE_TIME_ORIGINAL}, or {@link
   * PelTag::DATE_TIME_DIGITIZED}.
   *
   * @param int the timestamp held by this entry in the correct form
   * as indicated by the third argument. For {@link UNIX_TIMESTAMP}
   * this is an integer counting the number of seconds since January
   * 1st 1970, for {@link EXIF_STRING} this is a string of the form
   * 'YYYY:MM:DD hh:mm:ss', and for {@link JULIAN_DAY_COUNT} this is a
   * floating point number where the integer part denotes the day
   * count and the fractional part denotes the time of day (0.25 means
   * 6:00, 0.75 means 18:00).
   *
   * @param int the type of the timestamp. This must be one of
   * {@link UNIX_TIMESTAMP}, {@link EXIF_STRING}, or
   * {@link JULIAN_DAY_COUNT}.
   */
  function __construct($tag, $timestamp, $type = self::UNIX_TIMESTAMP) {
    parent::__construct($tag);
    $this->setValue($timestamp, $type);
  }

  
  /**
   * Return the timestamp of the entry.
   *
   * The timestamp held by this entry is returned in one of three
   * formats: as a standard UNIX timestamp (default), as a fractional
   * Julian Day Count, or as a string.
   *
   * @param int the type of the timestamp. This must be one of
   * {@link UNIX_TIMESTAMP}, {@link EXIF_STRING}, or
   * {@link JULIAN_DAY_COUNT}.
   *
   * @return int the timestamp held by this entry in the correct form
   * as indicated by the type argument. For {@link UNIX_TIMESTAMP}
   * this is an integer counting the number of seconds since January
   * 1st 1970, for {@link EXIF_STRING} this is a string of the form
   * 'YYYY:MM:DD hh:mm:ss', and for {@link JULIAN_DAY_COUNT} this is a
   * floating point number where the integer part denotes the day
   * count and the fractional part denotes the time of day (0.25 means
   * 6:00, 0.75 means 18:00).
   */
  function getValue($type = self::UNIX_TIMESTAMP) {
    switch ($type) {
    case self::UNIX_TIMESTAMP:
      $seconds = jdtounix($this->day_count);
      if ($seconds === false)
        /* jdtounix() return false if the Julian Day Count is outside
         * the range of a UNIX timestamp. */ 
        return false;
      else
        return $seconds + $this->seconds;

    case self::EXIF_STRING:
      list($month, $day, $year) = explode('/', jdtogregorian($this->day_count));
      $hours   = (int)($this->seconds / 3600);
      $minutes = (int)($this->seconds % 3600 / 60);
      $seconds = $this->seconds % 60;
      return sprintf('%04d:%02d:%02d %02d:%02d:%02d',
                     $year, $month, $day, $hours, $minutes, $seconds);
    case self::JULIAN_DAY_COUNT:
      return $this->day_count + $this->seconds / 86400;
    default:
      throw new PelInvalidArgumentException('Expected UNIX_TIMESTAMP (%d), ' .
                                            'EXIF_STRING (%d), or ' .
                                            'JULIAN_DAY_COUNT (%d) for $type, '.
                                            'got %d.',
                                            self::UNIX_TIMESTAMP,
                                            self::EXIF_STRING,
                                            self::JULIAN_DAY_COUNT,
                                            $type);
        }
  }


  /**
   * Update the timestamp held by this entry.
   *
   * @param int the timestamp held by this entry in the correct form
   * as indicated by the third argument. For {@link UNIX_TIMESTAMP}
   * this is an integer counting the number of seconds since January
   * 1st 1970, for {@link EXIF_STRING} this is a string of the form
   * 'YYYY:MM:DD hh:mm:ss', and for {@link JULIAN_DAY_COUNT} this is a
   * floating point number where the integer part denotes the day
   * count and the fractional part denotes the time of day (0.25 means
   * 6:00, 0.75 means 18:00).
   *
   * @param int the type of the timestamp. This must be one of
   * {@link UNIX_TIMESTAMP}, {@link EXIF_STRING}, or
   * {@link JULIAN_DAY_COUNT}.
   *
   * @todo How to deal with timezones? Use the TimeZoneOffset tag
   * 0x882A?
   */
  function setValue($timestamp, $type = self::UNIX_TIMESTAMP) {
    switch ($type) {
    case self::UNIX_TIMESTAMP:
      $this->day_count = unixtojd($timestamp);
      $this->seconds   = $timestamp % 86400;
      break;

    case self::EXIF_STRING:
      /* Clean the timestamp: some timestamps are broken other
       * separators than ':' and ' '. */
      $d = split('[^0-9]+', $timestamp);
      $this->day_count = gregoriantojd($d[1], $d[2], $d[0]);
      $this->seconds   = $d[3]*3600 + $d[4]*60 + $d[5];
      break;

    case self::JULIAN_DAY_COUNT:
      $this->day_count = (int)floor($timestamp);
      $this->seconds = (int)(86400 * ($timestamp - floor($timestamp)));
      break;

    default:
      throw new PelInvalidArgumentException('Expected UNIX_TIMESTAMP (%d), ' .
                                            'EXIF_STRING (%d), or ' .
                                            'JULIAN_DAY_COUNT (%d) for $type, '.
                                            'got %d.',
                                            self::UNIX_TIMESTAMP,
                                            self::EXIF_STRING,
                                            self::JULIAN_DAY_COUNT,
                                            $type);
    }

    /* Now finally update the string which will be used when this is
     * turned into bytes. */
    parent::setValue($this->getValue(self::EXIF_STRING));
  }
}



class PelEntryCopyright extends PelEntryAscii {

  /**
   * The photographer copyright.
   *
   * @var string
   */
  private $photographer;

  /**
   * The editor copyright.
   *
   * @var string
   */
  private $editor;


  /**
   * Make a new entry for holding copyright information.
   *
   * @param string the photographer copyright.  Use the empty string
   * if there is no photographer copyright.
   *
   * @param string the editor copyright.  Use the empty string if
   * there is no editor copyright.
   */
  function __construct($photographer = '', $editor = '') {
    parent::__construct(PelTag::COPYRIGHT);
    $this->setValue($photographer, $editor);
  }
  

  /**
   * Update the copyright information.
   *
   * @param string the photographer copyright.  Use the empty string
   * if there is no photographer copyright.
   *
   * @param string the editor copyright.  Use the empty string if
   * there is no editor copyright.
   */
  function setValue($photographer = '', $editor = '') {
    $this->photographer = $photographer;
    $this->editor       = $editor;

    if ($photographer == '' && $editor != '')
      $photographer = ' ';

    if ($editor == '')
      parent::setValue($photographer);
    else
      parent::setValue($photographer . chr(0x00) . $editor);
  }


  /**
   * Retrive the copyright information.
   *
   * The strings returned will be the same as the one used previously
   * with either {@link __construct the constructor} or with {@link
   * setValue}.
   *
   * @return array an array with two strings, the photographer and
   * editor copyrights.  The two fields will be returned in that
   * order, so that the first array index will be the photographer
   * copyright, and the second will be the editor copyright.
   */
  function getValue() {
    return array($this->photographer, $this->editor);
  }


  /**
   * Return a text string with the copyright information.
   *
   * The photographer and editor copyright fields will be returned
   * with a '-' in between if both copyright fields are present,
   * otherwise only one of them will be returned.
   *
   * @param boolean if false, then the strings '(Photographer)' and
   * '(Editor)' will be appended to the photographer and editor
   * copyright fields (if present), otherwise the fields will be
   * returned as is.
   *
   * @return string the copyright information in a string.
   */
  function getText($brief = false) {
    if ($brief) {
      $p = '';
      $e = '';
    } else {
      $p = ' ' . Pel::tra('(Photographer)');
      $e = ' ' . Pel::tra('(Editor)');
    }

    if ($this->photographer != '' && $this->editor != '')
      return $this->photographer . $p . ' - ' . $this->editor . $e;
    
    if ($this->photographer != '')
      return $this->photographer . $p;

    if ($this->editor != '')
      return $this->editor . $e;

    return '';
  }
}
class PelEntryShort extends PelEntryNumber {

  /**
   * Make a new entry that can hold an unsigned short.
   *
   * The method accept several integer arguments.  The {@link
   * getValue} method will always return an array except for when a
   * single integer argument is given here.
   *
   * This means that one can conveniently use objects like this:
   * <code>
   * $a = new PelEntryShort(PelTag::EXIF_IMAGE_HEIGHT, 42);
   * $b = $a->getValue() + 314;
   * </code>
   * where the call to {@link getValue} will return an integer
   * instead of an array with one integer element, which would then
   * have to be extracted.
   *
   * @param PelTag the tag which this entry represents.  This should be
   * one of the constants defined in {@link PelTag}, e.g., {@link
   * PelTag::IMAGE_WIDTH}, {@link PelTag::ISO_SPEED_RATINGS},
   * or any other tag with format {@link PelFormat::SHORT}.
   *
   * @param int $value... the short(s) that this entry will
   * represent.  The argument passed must obey the same rules as the
   * argument to {@link setValue}, namely that it should be within
   * range of an unsigned short, that is between 0 and 65535
   * (inclusive).  If not, then a {@link PelOverFlowException} will be
   * thrown.
   */
  function __construct($tag /* $value... */) {
    $this->tag    = $tag;
    $this->min    = 0;
    $this->max    = 65535;
    $this->format = PelFormat::SHORT;

    $value = func_get_args();
    array_shift($value);
    $this->setValueArray($value);
  }


  /**
   * Convert a number into bytes.
   *
   * @param int the number that should be converted.
   *
   * @param PelByteOrder one of {@link PelConvert::LITTLE_ENDIAN} and
   * {@link PelConvert::BIG_ENDIAN}, specifying the target byte order.
   *
   * @return string bytes representing the number given.
   */
  function numberToBytes($number, $order) {
    return PelConvert::shortToBytes($number, $order);
  }


  /**
   * Get the value of an entry as text.
   *
   * The value will be returned in a format suitable for presentation,
   * e.g., instead of returning '2' for a {@link
   * PelTag::METERING_MODE} tag, 'Center-Weighted Average' is
   * returned.
   *
   * @param boolean some values can be returned in a long or more
   * brief form, and this parameter controls that.
   *
   * @return string the value as text.
   */
  function getText($brief = false) {
    switch ($this->tag) {
    case PelTag::METERING_MODE:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0:
        return Pel::tra('Unknown');
      case 1:
        return Pel::tra('Average');
      case 2:
        return Pel::tra('Center-Weighted Average');
      case 3:
        return Pel::tra('Spot');
      case 4:
        return Pel::tra('Multi Spot');
      case 5:
        return Pel::tra('Pattern');
      case 6:
        return Pel::tra('Partial');
      case 255:
        return Pel::tra('Other');
      default:
        return $this->value[0];
      }

    case PelTag::COMPRESSION:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 1:
        return Pel::tra('Uncompressed');
      case 6:
        return Pel::tra('JPEG compression');
      default:
        return $this->value[0];
      
      }

    case PelTag::PLANAR_CONFIGURATION:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 1:
        return Pel::tra('chunky format');
      case 2:
        return Pel::tra('planar format');
      default:
        return $this->value[0];
      }
      
    case PelTag::SENSING_METHOD:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 1:
        return Pel::tra('Not defined');
      case 2:
        return Pel::tra('One-chip color area sensor');
      case 3:
        return Pel::tra('Two-chip color area sensor');
      case 4:
        return Pel::tra('Three-chip color area sensor');
      case 5:
        return Pel::tra('Color sequential area sensor');
      case 7:
        return Pel::tra('Trilinear sensor');
      case 8:
        return Pel::tra('Color sequential linear sensor');
      default:
        return $this->value[0];
      }

    case PelTag::LIGHT_SOURCE:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0:
        return Pel::tra('Unknown');
      case 1:
        return Pel::tra('Daylight');
      case 2:
        return Pel::tra('Fluorescent');
      case 3:
        return Pel::tra('Tungsten (incandescent light)');
      case 4:
        return Pel::tra('Flash');
      case 9:
        return Pel::tra('Fine weather');
      case 10:
        return Pel::tra('Cloudy weather');
      case 11:
        return Pel::tra('Shade');
      case 12:
        return Pel::tra('Daylight fluorescent');
      case 13:
        return Pel::tra('Day white fluorescent');
      case 14:
        return Pel::tra('Cool white fluorescent');
      case 15:
        return Pel::tra('White fluorescent');
      case 17:
        return Pel::tra('Standard light A');
      case 18:
        return Pel::tra('Standard light B');
      case 19:
        return Pel::tra('Standard light C');
      case 20:
        return Pel::tra('D55');
      case 21:
        return Pel::tra('D65');
      case 22:
        return Pel::tra('D75');
      case 24:
        return Pel::tra('ISO studio tungsten');
      case 255:
        return Pel::tra('Other');
      default:
        return $this->value[0];
      }

    case PelTag::FOCAL_PLANE_RESOLUTION_UNIT:
    case PelTag::RESOLUTION_UNIT:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 2:
        return Pel::tra('Inch');
      case 3:
        return Pel::tra('Centimeter');
      default:
        return $this->value[0];
      }

    case PelTag::EXPOSURE_PROGRAM:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0:
        return Pel::tra('Not defined');
      case 1:
        return Pel::tra('Manual');
      case 2:
        return Pel::tra('Normal program');
      case 3:
        return Pel::tra('Aperture priority');
      case 4:
        return Pel::tra('Shutter priority');
      case 5:
        return Pel::tra('Creative program (biased toward depth of field)');
      case 6:
        return Pel::tra('Action program (biased toward fast shutter speed)');
      case 7:
        return Pel::tra('Portrait mode (for closeup photos with the background out of focus');
      case 8:
        return Pel::tra('Landscape mode (for landscape photos with the background in focus');
      default:
        return $this->value[0];
      }
   
    case PelTag::ORIENTATION:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 1:
        return Pel::tra('top - left');
      case 2:
        return Pel::tra('top - right');
      case 3:
        return Pel::tra('bottom - right');
      case 4:
        return Pel::tra('bottom - left');
      case 5:
        return Pel::tra('left - top');
      case 6:
        return Pel::tra('right - top');
      case 7:
        return Pel::tra('right - bottom');
      case 8:
        return Pel::tra('left - bottom');
      default:
        return $this->value[0];
      }

    case PelTag::YCBCR_POSITIONING:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 1:
        return Pel::tra('centered');
      case 2:
        return Pel::tra('co-sited');
      default:
        return $this->value[0];
      }

    case PelTag::YCBCR_SUB_SAMPLING:
      //CC (e->components, 2, v);
      if ($this->value[0] == 2 && $this->value[1] == 1)
        return 'YCbCr4:2:2';
      if ($this->value[0] == 2 && $this->value[1] == 2)
        return 'YCbCr4:2:0';
      
      return $this->value[0] . ', ' . $this->value[1];
   
    case PelTag::PHOTOMETRIC_INTERPRETATION:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 2:
        return 'RGB';
      case 6:
        return 'YCbCr';
      default:
        return $this->value[0];
      }
   
    case PelTag::COLOR_SPACE:
      //CC (e->components, 1, v); 
      switch ($this->value[0]) { 
      case 1:
        return 'sRGB';
      case 2:
        return 'Adobe RGB';
      case 0xffff:
        return Pel::tra('Uncalibrated');
      default:
        return $this->value[0];
      }

    case PelTag::FLASH:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0x0000:
        return Pel::tra('Flash did not fire.');
      case 0x0001:
        return Pel::tra('Flash fired.');
      case 0x0005:
        return Pel::tra('Strobe return light not detected.');
      case 0x0007:
        return Pel::tra('Strobe return light detected.');
      case 0x0009:
        return Pel::tra('Flash fired, compulsory flash mode.');
      case 0x000d:
        return Pel::tra('Flash fired, compulsory flash mode, return light not detected.');
      case 0x000f:
        return Pel::tra('Flash fired, compulsory flash mode, return light detected.');
      case 0x0010:
        return Pel::tra('Flash did not fire, compulsory flash mode.');
      case 0x0018:
        return Pel::tra('Flash did not fire, auto mode.');
      case 0x0019:
        return Pel::tra('Flash fired, auto mode.');
      case 0x001d:
        return Pel::tra('Flash fired, auto mode, return light not detected.');
      case 0x001f:
        return Pel::tra('Flash fired, auto mode, return light detected.');
      case 0x0020:
        return Pel::tra('No flash function.');
      case 0x0041:
        return Pel::tra('Flash fired, red-eye reduction mode.');
      case 0x0045:
        return Pel::tra('Flash fired, red-eye reduction mode, return light not detected.');
      case 0x0047:
        return Pel::tra('Flash fired, red-eye reduction mode, return light detected.');
      case 0x0049:
        return Pel::tra('Flash fired, compulsory flash mode, red-eye reduction mode.');
      case 0x004d:
        return Pel::tra('Flash fired, compulsory flash mode, red-eye reduction mode, return light not detected.');
      case 0x004f:
        return Pel::tra('Flash fired, compulsory flash mode, red-eye reduction mode, return light detected.');
      case 0x0058:
        return Pel::tra('Flash did not fire, auto mode, red-eye reduction mode.');
      case 0x0059:
        return Pel::tra('Flash fired, auto mode, red-eye reduction mode.');
      case 0x005d:
        return Pel::tra('Flash fired, auto mode, return light not detected, red-eye reduction mode.');
      case 0x005f:
        return Pel::tra('Flash fired, auto mode, return light detected, red-eye reduction mode.');
      default:
        return $this->value[0];
      }

    case PelTag::CUSTOM_RENDERED:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0:
        return Pel::tra('Normal process');
      case 1:
        return Pel::tra('Custom process');
      default:
        return $this->value[0];
      }

    case PelTag::EXPOSURE_MODE:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0:
        return Pel::tra('Auto exposure');
      case 1:
        return Pel::tra('Manual exposure');
      case 2:
        return Pel::tra('Auto bracket');
      default:
        return $this->value[0];
      }
   
    case PelTag::WHITE_BALANCE:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0:
        return Pel::tra('Auto white balance');
      case 1:
        return Pel::tra('Manual white balance');
      default:
        return $this->value[0];
      }

    case PelTag::SCENE_CAPTURE_TYPE:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0:
        return Pel::tra('Standard');
      case 1:
        return Pel::tra('Landscape');
      case 2:
        return Pel::tra('Portrait');
      case 3:
        return Pel::tra('Night scene');
      default:
        return $this->value[0];
      }

    case PelTag::GAIN_CONTROL:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0:
        return Pel::tra('Normal');
      case 1:
        return Pel::tra('Low gain up');
      case 2:
        return Pel::tra('High gain up');
      case 3:
        return Pel::tra('Low gain down');
      case 4:
        return Pel::tra('High gain down');
      default:
        return $this->value[0];
      }

    case PelTag::SATURATION:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0:
        return Pel::tra('Normal');
      case 1:
        return Pel::tra('Low saturation');
      case 2:
        return Pel::tra('High saturation');
      default:
        return $this->value[0];
      }

    case PelTag::CONTRAST:
    case PelTag::SHARPNESS:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0:
        return Pel::tra('Normal');
      case 1:
        return Pel::tra('Soft');
      case 2:
        return Pel::tra('Hard');
      default:
        return $this->value[0];
      }

    case PelTag::SUBJECT_DISTANCE_RANGE:
      //CC (e->components, 1, v);
      switch ($this->value[0]) {
      case 0:
        return Pel::tra('Unknown');
      case 1:
        return Pel::tra('Macro');
      case 2:
        return Pel::tra('Close view');
      case 3:
        return Pel::tra('Distant view');
      default:
        return $this->value[0];
      }

    case PelTag::SUBJECT_AREA:
      switch ($this->components) {
      case 2:
        return Pel::fmt('(x,y) = (%d,%d)', $this->value[0], $this->value[1]);
      case 3:
        return Pel::fmt('Within distance %d of (x,y) = (%d,%d)',
                        $this->value[0], $this->value[1], $this->value[2]);
      case 4:
        return Pel::fmt('Within rectangle (width %d, height %d) around (x,y) = (%d,%d)',
                        $this->value[0], $this->value[1],
                        $this->value[2], $this->value[3]);
        
      default:
        return Pel::fmt('Unexpected number of components (%d, expected 2, 3, or 4).', $this->components);
      }

    default:
      return parent::getText($brief);
    }
  }
}



class PelEntrySShort extends PelEntryNumber {

  /**
   * Make a new entry that can hold a signed short.
   *
   * The method accept several integer arguments.  The {@link
   * getValue} method will always return an array except for when a
   * single integer argument is given here.
   *
   * @param PelTag the tag which this entry represents.  This
   * should be one of the constants defined in {@link PelTag}
   * which has format {@link PelFormat::SSHORT}.
   *
   * @param int $value... the signed short(s) that this entry will
   * represent.  The argument passed must obey the same rules as the
   * argument to {@link setValue}, namely that it should be within
   * range of a signed short, that is between -32768 to 32767
   * (inclusive).  If not, then a {@link PelOverFlowException} will be
   * thrown.
   */
  function __construct($tag /* $value... */) {
    $this->tag    = $tag;
    $this->min    = -32768;
    $this->max    = 32767;
    $this->format = PelFormat::SSHORT;

    $value = func_get_args();
    array_shift($value);
    $this->setValueArray($value);
  }



  function numberToBytes($number, $order) {
    return PelConvert::sShortToBytes($number, $order);
  }
}

class PelEntryByte extends PelEntryNumber {

  /**
   * Make a new entry that can hold an unsigned byte.
   *
   * The method accept several integer arguments.  The {@link
   * getValue} method will always return an array except for when a
   * single integer argument is given here.
   *
   * @param PelTag the tag which this entry represents.  This
   * should be one of the constants defined in {@link PelTag}
   * which has format {@link PelFormat::BYTE}.
   *
   * @param int $value... the byte(s) that this entry will represent.
   * The argument passed must obey the same rules as the argument to
   * {@link setValue}, namely that it should be within range of an
   * unsigned byte, that is between 0 and 255 (inclusive).  If not,
   * then a {@link PelOverflowException} will be thrown.
   */
  function __construct($tag /* $value... */) {
    $this->tag    = $tag;
    $this->min    = 0;
    $this->max    = 255;
    $this->format = PelFormat::BYTE;

    $value = func_get_args();
    array_shift($value);
    $this->setValueArray($value);
  }



  function numberToBytes($number, $order) {
    return chr($number);
  }

}



class PelEntrySByte extends PelEntryNumber {

  /**
   * Make a new entry that can hold a signed byte.
   *
   * The method accept several integer arguments.  The {@link getValue}
   * method will always return an array except for when a single
   * integer argument is given here.
   *
   * @param PelTag the tag which this entry represents.  This
   * should be one of the constants defined in {@link PelTag}
   * which has format {@link PelFormat::BYTE}.
   *
   * @param int $value... the byte(s) that this entry will represent.
   * The argument passed must obey the same rules as the argument to
   * {@link setValue}, namely that it should be within range of a
   * signed byte, that is between -128 and 127 (inclusive).  If not,
   * then a {@link PelOverflowException} will be thrown.
   */
  function __construct($tag /* $value... */) {
    $this->tag    = $tag;
    $this->min    = -128;
    $this->max    = 127;
    $this->format = PelFormat::SBYTE;

    $value = func_get_args();
    array_shift($value);
    $this->setValueArray($value);
  }



  function numberToBytes($number, $order) {
    return chr($number);
  }

}



class PelEntryWindowsString extends PelEntry {

  /**
   * The string hold by this entry.
   *
   * This is the string that was given to the {@link __construct
   * constructor} or later to {@link setValue}, without any extra NULL
   * characters or any such nonsense.
   *
   * @var string
   */
  private $str;


  /**
   * Make a new PelEntry that can hold a Windows XP specific string.
   *
   * @param int the tag which this entry represents.  This should be
   * one of {@link PelTag::XP_TITLE}, {@link PelTag::XP_COMMENT},
   * {@link PelTag::XP_AUTHOR}, {@link PelTag::XP_KEYWORD}, and {@link
   * PelTag::XP_SUBJECT} tags.  If another tag is used, then this
   * entry will be incorrectly reloaded as a {@link PelEntryByte}.
   *
   * @param string the string that this entry will represent.  It will
   * be passed to {@link setValue} and thus has to obey its
   * requirements.
   */
  function __construct($tag, $str = '') {
    $this->tag    = $tag;
    $this->format = PelFormat::BYTE;
    $this->setValue($str);
  }


  /**
   * Give the entry a new value.
   *
   * This will overwrite the previous value.  The value can be
   * retrieved later with the {@link getValue} method.
   *
   * @param string the new value of the entry.  This should be use the
   * Latin-1 encoding and be given without any extra NULL characters.
   */
  function setValue($str) {
    $l = strlen($str);

    $this->components = 2 * ($l + 1);
    $this->str        = $str;
    $this->bytes      = '';
    for ($i = 0; $i < $l; $i++)
      $this->bytes .= $str{$i} . chr(0x00);

    $this->bytes .= chr(0x00) . chr(0x00);
  }



  function getValue() {
    return $this->str;
  }



  function getText($brief = false) {
    return $this->str;      
  }

}


class PelOverflowException extends PelException {
  
  /**
   * Construct a new overflow exception.
   *
   * @param int the value that is out of range.
   *
   * @param int the minimum allowed value.
   *
   * @param int the maximum allowed value.
   */
  function __construct($v, $min, $max) {
    parent::__construct('Value %.0f out of range [%.0f, %.0f]',
                        $v, $min, $max);
  }
}






class PelEntrySLong extends PelEntryNumber {


  function __construct($tag /* $value... */) {
    $this->tag    = $tag;
    $this->min    = -2147483648;
    $this->max    = 2147483647;
    $this->format = PelFormat::SLONG;

    $value = func_get_args();
    array_shift($value);
    $this->setValueArray($value);
  }



  function numberToBytes($number, $order) {
    return PelConvert::sLongToBytes($number, $order);
  }
}


class PelIfdException extends PelException {}


class PelIfd implements IteratorAggregate, ArrayAccess {

  /**
   * Main image IFD.
   *
   * Pass this to the constructor when creating an IFD which will be
   * the IFD of the main image.
   */
  const IFD0 = 0;

  /**
   * Thumbnail image IFD.
   *
   * Pass this to the constructor when creating an IFD which will be
   * the IFD of the thumbnail image.
   */
  const IFD1 = 1;

  /**
   * Exif IFD.
   *
   * Pass this to the constructor when creating an IFD which will be
   * the Exif sub-IFD.
   */
  const EXIF = 2;

  /**
   * GPS IFD.
   *
   * Pass this to the constructor when creating an IFD which will be
   * the GPS sub-IFD.
   */
  const GPS  = 3;

  /**
   * Interoperability IFD.
   *
   * Pass this to the constructor when creating an IFD which will be
   * the interoperability sub-IFD.
   */
  const INTEROPERABILITY = 4;

  /**
   * The entries held by this directory.
   *
   * Each tag in the directory is represented by a {@link PelEntry}
   * object in this array.
   *
   * @var array
   */
  private $entries = array();

  /**
   * The type of this directory.
   *
   * Initialized in the constructor.  Must be one of {@link IFD0},
   * {@link IFD1}, {@link EXIF}, {@link GPS}, or {@link
   * INTEROPERABILITY}.
   *
   * @var int
   */
  private $type;

  /**
   * The next directory.
   *
   * This will be initialized in the constructor, or be left as null
   * if this is the last directory.
   *
   * @var PelIfd
   */
  private $next = null;

  /**
   * Sub-directories pointed to by this directory.
   *
   * This will be an array of ({@link PelTag}, {@link PelIfd}) pairs.
   *
   * @var array
   */
  private $sub = array();

  /**
   * The thumbnail data.
   *
   * This will be initialized in the constructor, or be left as null
   * if there are no thumbnail as part of this directory.
   *
   * @var PelDataWindow
   */
  private $thumb_data = null;
  // TODO: use this format to choose between the
  // JPEG_INTERCHANGE_FORMAT and STRIP_OFFSETS tags.
  // private $thumb_format;

  
  /**
   * Construct a new Image File Directory (IFD).
   *
   * The IFD will be empty, use the {@link addEntry()} method to add
   * an {@link PelEntry}.  Use the {@link setNext()} method to link
   * this IFD to another.
   *
   * @param int type the type of this IFD.  Must be one of {@link
   * IFD0}, {@link IFD1}, {@link EXIF}, {@link GPS}, or {@link
   * INTEROPERABILITY}.  An {@link PelIfdException} will be thrown
   * otherwise.
   */
  function __construct($type) {
    if ($type != PelIfd::IFD0 && $type != PelIfd::IFD1 &&
        $type != PelIfd::EXIF && $type != PelIfd::GPS &&
        $type != PelIfd::INTEROPERABILITY)
      throw new PelIfdException('Unknown IFD type: %d', $type);

    $this->type = $type;
  }


  /**
   * Load data into a Image File Directory (IFD).
   *
   * @param PelDataWindow the data window that will provide the data.
   *
   * @param int the offset within the window where the directory will
   * be found.
   */
  function load(PelDataWindow $d, $offset) {
    $thumb_offset = 0;
    $thumb_length = 0;

    Pel::debug('Constructing IFD at offset %d from %d bytes...',
               $offset, $d->getSize());

    /* Read the number of entries */
    $n = $d->getShort($offset);
    Pel::debug('Loading %d entries...', $n);
    
    $offset += 2;

    /* Check if we have enough data. */
    if ($offset + 12 * $n > $d->getSize()) {
      $n = floor(($offset - $d->getSize()) / 12);
      Pel::maybeThrow(new PelIfdException('Adjusted to: %d.', $n));
    }

    for ($i = 0; $i < $n; $i++) {
      // TODO: increment window start instead of using offsets.
      $tag = $d->getShort($offset + 12 * $i);
      Pel::debug('Loading entry with tag 0x%04X: %s (%d of %d)...',
                 $tag, PelTag::getName($this->type, $tag), $i + 1, $n);
      
      switch ($tag) {
      case PelTag::EXIF_IFD_POINTER:
      case PelTag::GPS_INFO_IFD_POINTER:
      case PelTag::INTEROPERABILITY_IFD_POINTER:
        $o = $d->getLong($offset + 12 * $i + 8);
        Pel::debug('Found sub IFD at offset %d', $o);

        /* Map tag to IFD type. */
        if ($tag == PelTag::EXIF_IFD_POINTER)
          $type = PelIfd::EXIF;
        elseif ($tag == PelTag::GPS_INFO_IFD_POINTER)
          $type = PelIfd::GPS;
        elseif ($tag == PelTag::INTEROPERABILITY_IFD_POINTER)
          $type = PelIfd::INTEROPERABILITY;

        $this->sub[$type] = new PelIfd($type);
        $this->sub[$type]->load($d, $o);
        break;
      case PelTag::JPEG_INTERCHANGE_FORMAT:
        $thumb_offset = $d->getLong($offset + 12 * $i + 8);
        $this->safeSetThumbnail($d, $thumb_offset, $thumb_length);
        break;
      case PelTag::JPEG_INTERCHANGE_FORMAT_LENGTH:
        $thumb_length = $d->getLong($offset + 12 * $i + 8);
        $this->safeSetThumbnail($d, $thumb_offset, $thumb_length);
        break;
      default:
        $format     = $d->getShort($offset + 12 * $i + 2);
        $components = $d->getLong($offset + 12 * $i + 4);
        
        /* The data size.  If bigger than 4 bytes, the actual data is
         * not in the entry but somewhere else, with the offset stored
         * in the entry.
         */
        $s = PelFormat::getSize($format) * $components;
        if ($s > 0) {    
          $doff = $offset + 12 * $i + 8;
          if ($s > 4)
            $doff = $d->getLong($doff);

          $data = $d->getClone($doff, $s);
        } else {
          $data = new PelDataWindow();
        }

        try {
            $entry = $this->newEntryFromData($tag, $format, $components, $data);

            if ($this->isValidTag($tag)) {
              $entry->setIfdType($this->type);
              $this->entries[$tag] = $entry;
            } else {
              Pel::maybeThrow(new PelInvalidDataException("IFD %s cannot hold\n%s",
                                                          $this->getName(),
                                                          $entry->__toString()));
            }
          } catch (PelException $e) {
            /* Throw the exception when running in strict mode, store
             * otherwise. */
            Pel::maybeThrow($e);
          }

        /* The format of the thumbnail is stored in this tag. */
//         TODO: handle TIFF thumbnail.
//         if ($tag == PelTag::COMPRESSION) {
//           $this->thumb_format = $data->getShort();
//         }
        break;
      }
    }

    /* Offset to next IFD */
    $o = $d->getLong($offset + 12 * $n);
    Pel::debug('Current offset is %d, link at %d points to %d.',
               $offset,  $offset + 12 * $n, $o);

    if ($o > 0) {
      /* Sanity check: we need 6 bytes  */
      if ($o > $d->getSize() - 6) {
        Pel::maybeThrow(new PelIfdException('Bogus offset to next IFD: ' .
                                            '%d > %d!',
                                            $o, $d->getSize() - 6));
      } else {
        if ($this->type == PelIfd::IFD1) // IFD1 shouldn't link further...
          Pel::maybeThrow(new PelIfdException('IFD1 links to another IFD!'));

        $this->next = new PelIfd(PelIfd::IFD1);
        $this->next->load($d, $o);
      }
    } else {
      Pel::debug('Last IFD.');
    }
  }


  /**
   * Make a new entry from a bunch of bytes.
   *
   * This method will create the proper subclass of {@link PelEntry}
   * corresponding to the {@link PelTag} and {@link PelFormat} given.
   * The entry will be initialized with the data given.
   *
   * Please note that the data you pass to this method should come
   * from an image, that is, it should be raw bytes.  If instead you
   * want to create an entry for holding, say, an short integer, then
   * create a {@link PelEntryShort} object directly and load the data
   * into it.
   *
   * A {@link PelUnexpectedFormatException} is thrown if a mismatch is
   * discovered between the tag and format, and likewise a {@link
   * PelWrongComponentCountException} is thrown if the number of
   * components does not match the requirements of the tag.  The
   * requirements for a given tag (if any) can be found in the
   * documentation for {@link PelTag}.
   *
   * @param PelTag the tag of the entry.
   *
   * @param PelFormat the format of the entry.
   *
   * @param int the components in the entry.
   *
   * @param PelDataWindow the data which will be used to construct the
   * entry.
   *
   * @return PelEntry a newly created entry, holding the data given.
   */
  function newEntryFromData($tag, $format, $components, PelDataWindow $data) {

    /* First handle tags for which we have a specific PelEntryXXX
     * class. */

    switch ($this->type) {

    case self::IFD0:
    case self::IFD1:
    case self::EXIF:
    case self::INTEROPERABILITY:

      switch ($tag) {
      case PelTag::DATE_TIME:
      case PelTag::DATE_TIME_ORIGINAL:
      case PelTag::DATE_TIME_DIGITIZED:
        if ($format != PelFormat::ASCII)
          throw new PelUnexpectedFormatException($this->type, $tag, $format,
                                               PelFormat::ASCII);

        if ($components != 20)
          throw new PelWrongComponentCountException($this->type, $tag, $components, 20);

        // TODO: handle timezones.
        return new PelEntryTime($tag, $data->getBytes(0, -1), PelEntryTime::EXIF_STRING);

      case PelTag::COPYRIGHT:
        if ($format != PelFormat::ASCII)
          throw new PelUnexpectedFormatException($this->type, $tag, $format,
                                                 PelFormat::ASCII);

        $v = explode("\0", trim($data->getBytes(), ' '));
        return new PelEntryCopyright($v[0], $v[1]);

      case PelTag::EXIF_VERSION:
      case PelTag::FLASH_PIX_VERSION:
      case PelTag::INTEROPERABILITY_VERSION:
        if ($format != PelFormat::UNDEFINED)
          throw new PelUnexpectedFormatException($this->type, $tag, $format,
                                               PelFormat::UNDEFINED);

        return new PelEntryVersion($tag, $data->getBytes() / 100);

      case PelTag::USER_COMMENT:
        if ($format != PelFormat::UNDEFINED)
          throw new PelUnexpectedFormatException($this->type, $tag, $format,
                                                 PelFormat::UNDEFINED);
        if ($data->getSize() < 8) {
          return new PelEntryUserComment();
        } else {
          return new PelEntryUserComment($data->getBytes(8),
                                       rtrim($data->getBytes(0, 8)));
        }

      case PelTag::XP_TITLE:
      case PelTag::XP_COMMENT:
      case PelTag::XP_AUTHOR:
      case PelTag::XP_KEYWORDS:
      case PelTag::XP_SUBJECT:
        if ($format != PelFormat::BYTE)
          throw new PelUnexpectedFormatException($this->type, $tag, $format,
                                               PelFormat::BYTE);

        $v = '';
        for ($i = 0; $i < $components; $i++) {
          $b = $data->getByte($i);
          /* Convert the byte to a character if it is non-null ---
           * information about the character encoding of these entries
           * would be very nice to have!  So far my tests have shown
           * that characters in the Latin-1 character set are stored in
           * a single byte followed by a NULL byte. */
          if ($b != 0)
            $v .= chr($b);
        }

        return new PelEntryWindowsString($tag, $v);
      }

    case self::GPS:
      
    default:
      /* Then handle the basic formats. */
      switch ($format) {
      case PelFormat::BYTE:
        $v =  new PelEntryByte($tag);
        for ($i = 0; $i < $components; $i++)
          $v->addNumber($data->getByte($i));
        return $v;

      case PelFormat::SBYTE:
        $v =  new PelEntrySByte($tag);
        for ($i = 0; $i < $components; $i++)
          $v->addNumber($data->getSByte($i));
        return $v;

      case PelFormat::ASCII:
        return new PelEntryAscii($tag, $data->getBytes(0, -1));

      case PelFormat::SHORT:
        $v =  new PelEntryShort($tag);
        for ($i = 0; $i < $components; $i++)
          $v->addNumber($data->getShort($i*2));
        return $v;

      case PelFormat::SSHORT:
        $v =  new PelEntrySShort($tag);
        for ($i = 0; $i < $components; $i++)
          $v->addNumber($data->getSShort($i*2));
        return $v;

      case PelFormat::LONG:
        $v =  new PelEntryLong($tag);
        for ($i = 0; $i < $components; $i++)
          $v->addNumber($data->getLong($i*4));
        return $v;

      case PelFormat::SLONG:
        $v =  new PelEntrySLong($tag);
        for ($i = 0; $i < $components; $i++)
          $v->addNumber($data->getSLong($i*4));
        return $v;

      case PelFormat::RATIONAL:
        $v =  new PelEntryRational($tag);
        for ($i = 0; $i < $components; $i++)
          $v->addNumber($data->getRational($i*8));
        return $v;

      case PelFormat::SRATIONAL:
        $v =  new PelEntrySRational($tag);
        for ($i = 0; $i < $components; $i++)
          $v->addNumber($data->getSRational($i*8));
        return $v;

      case PelFormat::UNDEFINED:
        return new PelEntryUndefined($tag, $data->getBytes());

      default:
        throw new PelException('Unsupported format: %s',
                               PelFormat::getName($format));
      }
    }
  }




  /**
   * Extract thumbnail data safely.
   *
   * It is safe to call this method repeatedly with either the offset
   * or the length set to zero, since it requires both of these
   * arguments to be positive before the thumbnail is extracted.
   *
   * When both parameters are set it will check the length against the
   * available data and adjust as necessary. Only then is the
   * thumbnail data loaded.
   *
   * @param PelDataWindow the data from which the thumbnail will be
   * extracted.
   *
   * @param int the offset into the data.
   *
   * @param int the length of the thumbnail.
   */
  private function safeSetThumbnail(PelDataWindow $d, $offset, $length) {
    /* Load the thumbnail if both the offset and the length is
     * available. */
    if ($offset > 0 && $length > 0) {
      /* Some images have a broken length, so we try to carefully
       * check the length before we store the thumbnail. */
      if ($offset + $length > $d->getSize()) {
        Pel::maybeThrow(new PelIfdException('Thumbnail length %d bytes ' .
                                            'adjusted to %d bytes.',
                                            $length,
                                            $d->getSize() - $offset));
        $length = $d->getSize() - $offset;
      }

      /* Now set the thumbnail normally. */
      $this->setThumbnail($d->getClone($offset, $length));
    }
  }

  
  /**
   * Set thumbnail data.
   *
   * Use this to embed an arbitrary JPEG image within this IFD. The
   * data will be checked to ensure that it has a proper {@link
   * PelJpegMarker::EOI} at the end.  If not, then the length is
   * adjusted until one if found.  An {@link PelIfdException} might be
   * thrown (depending on {@link Pel::$strict}) this case.
   *
   * @param PelDataWindow the thumbnail data.
   */
  function setThumbnail(PelDataWindow $d) {
    $size = $d->getSize();
    /* Now move backwards until we find the EOI JPEG marker. */
    while ($d->getByte($size - 2) != 0xFF ||
           $d->getByte($size - 1) != PelJpegMarker::EOI) {
      $size--;
    }

    if ($size != $d->getSize())
      Pel::maybeThrow(new PelIfdException('Decrementing thumbnail size ' .
                                          'to %d bytes', $size));
    
    $this->thumb_data = $d->getClone(0, $size);
  }


  /**
   * Get the type of this directory.
   *
   * @return int of {@link PelIfd::IFD0}, {@link PelIfd::IFD1}, {@link
   * PelIfd::EXIF}, {@link PelIfd::GPS}, or {@link
   * PelIfd::INTEROPERABILITY}.
   */
  function getType() {
    return $this->type;
  }


  /**
   * Is a given tag valid for this IFD?
   *
   * Different types of IFDs can contain different kinds of tags ---
   * the {@link IFD0} type, for example, cannot contain a {@link
   * PelTag::GPS_LONGITUDE} tag.
   *
   * A special exception is tags with values above 0xF000.  They are
   * treated as private tags and will be allowed everywhere (use this
   * for testing or for implementing your own types of tags).
   *
   * @param PelTag the tag.
   *
   * @return boolean true if the tag is considered valid in this IFD,
   * false otherwise.
   *
   * @see getValidTags()
   */
  function isValidTag($tag) {
    return $tag > 0xF000 || in_array($tag, $this->getValidTags());
  }


  /**
   * Returns a list of valid tags for this IFD.
   *
   * @return array an array of {@link PelTag}s which are valid for
   * this IFD.
   */
  function getValidTags() {
    switch ($this->type) {
    case PelIfd::IFD0:
    case PelIfd::IFD1:
      return array(PelTag::IMAGE_WIDTH,
                   PelTag::IMAGE_LENGTH,
                   PelTag::BITS_PER_SAMPLE,
                   PelTag::COMPRESSION,
                   PelTag::PHOTOMETRIC_INTERPRETATION,
                   PelTag::IMAGE_DESCRIPTION,
                   PelTag::MAKE,
                   PelTag::MODEL,
                   PelTag::STRIP_OFFSETS,
                   PelTag::ORIENTATION,
                   PelTag::SAMPLES_PER_PIXEL,
                   PelTag::ROWS_PER_STRIP,
                   PelTag::STRIP_BYTE_COUNTS,
                   PelTag::X_RESOLUTION,
                   PelTag::Y_RESOLUTION,
                   PelTag::PLANAR_CONFIGURATION,
                   PelTag::RESOLUTION_UNIT,
                   PelTag::TRANSFER_FUNCTION,
                   PelTag::SOFTWARE,
                   PelTag::DATE_TIME,
                   PelTag::ARTIST,
                   PelTag::WHITE_POINT,
                   PelTag::PRIMARY_CHROMATICITIES,
                   PelTag::JPEG_INTERCHANGE_FORMAT,
                   PelTag::JPEG_INTERCHANGE_FORMAT_LENGTH,
                   PelTag::YCBCR_COEFFICIENTS,
                   PelTag::YCBCR_SUB_SAMPLING,
                   PelTag::YCBCR_POSITIONING,
                   PelTag::REFERENCE_BLACK_WHITE,
                   PelTag::COPYRIGHT,
                   PelTag::EXIF_IFD_POINTER,
                   PelTag::GPS_INFO_IFD_POINTER,
                   PelTag::PRINT_IM);

    case PelIfd::EXIF:
      return array(PelTag::EXPOSURE_TIME,
                   PelTag::FNUMBER,
                   PelTag::EXPOSURE_PROGRAM,
                   PelTag::SPECTRAL_SENSITIVITY,
                   PelTag::ISO_SPEED_RATINGS,
                   PelTag::OECF,
                   PelTag::EXIF_VERSION,
                   PelTag::DATE_TIME_ORIGINAL,
                   PelTag::DATE_TIME_DIGITIZED,
                   PelTag::COMPONENTS_CONFIGURATION,
                   PelTag::COMPRESSED_BITS_PER_PIXEL,
                   PelTag::SHUTTER_SPEED_VALUE,
                   PelTag::APERTURE_VALUE,
                   PelTag::BRIGHTNESS_VALUE,
                   PelTag::EXPOSURE_BIAS_VALUE,
                   PelTag::MAX_APERTURE_VALUE,
                   PelTag::SUBJECT_DISTANCE,
                   PelTag::METERING_MODE,
                   PelTag::LIGHT_SOURCE,
                   PelTag::FLASH,
                   PelTag::FOCAL_LENGTH,
                   PelTag::MAKER_NOTE,
                   PelTag::USER_COMMENT,
                   PelTag::SUB_SEC_TIME,
                   PelTag::SUB_SEC_TIME_ORIGINAL,
                   PelTag::SUB_SEC_TIME_DIGITIZED,
                   PelTag::XP_TITLE,
                   PelTag::XP_COMMENT,
                   PelTag::XP_AUTHOR,
                   PelTag::XP_KEYWORDS,
                   PelTag::XP_SUBJECT,
                   PelTag::FLASH_PIX_VERSION,
                   PelTag::COLOR_SPACE,
                   PelTag::PIXEL_X_DIMENSION,
                   PelTag::PIXEL_Y_DIMENSION,
                   PelTag::RELATED_SOUND_FILE,
                   PelTag::FLASH_ENERGY,
                   PelTag::SPATIAL_FREQUENCY_RESPONSE,
                   PelTag::FOCAL_PLANE_X_RESOLUTION,
                   PelTag::FOCAL_PLANE_Y_RESOLUTION,
                   PelTag::FOCAL_PLANE_RESOLUTION_UNIT,
                   PelTag::SUBJECT_LOCATION,
                   PelTag::EXPOSURE_INDEX,
                   PelTag::SENSING_METHOD,
                   PelTag::FILE_SOURCE,
                   PelTag::SCENE_TYPE,
                   PelTag::CFA_PATTERN,
                   PelTag::CUSTOM_RENDERED,
                   PelTag::EXPOSURE_MODE,
                   PelTag::WHITE_BALANCE,
                   PelTag::DIGITAL_ZOOM_RATIO,
                   PelTag::FOCAL_LENGTH_IN_35MM_FILM,
                   PelTag::SCENE_CAPTURE_TYPE,
                   PelTag::GAIN_CONTROL,
                   PelTag::CONTRAST,
                   PelTag::SATURATION,
                   PelTag::SHARPNESS,
                   PelTag::DEVICE_SETTING_DESCRIPTION,
                   PelTag::SUBJECT_DISTANCE_RANGE,
                   PelTag::IMAGE_UNIQUE_ID,
                   PelTag::INTEROPERABILITY_IFD_POINTER,
                   PelTag::GAMMA);

    case PelIfd::GPS:
      return array(PelTag::GPS_VERSION_ID, 
                   PelTag::GPS_LATITUDE_REF, 
                   PelTag::GPS_LATITUDE, 
                   PelTag::GPS_LONGITUDE_REF, 
                   PelTag::GPS_LONGITUDE, 
                   PelTag::GPS_ALTITUDE_REF,
                   PelTag::GPS_ALTITUDE,
                   PelTag::GPS_TIME_STAMP,
                   PelTag::GPS_SATELLITES,
                   PelTag::GPS_STATUS,
                   PelTag::GPS_MEASURE_MODE,
                   PelTag::GPS_DOP,
                   PelTag::GPS_SPEED_REF,
                   PelTag::GPS_SPEED,
                   PelTag::GPS_TRACK_REF,
                   PelTag::GPS_TRACK,
                   PelTag::GPS_IMG_DIRECTION_REF,
                   PelTag::GPS_IMG_DIRECTION,
                   PelTag::GPS_MAP_DATUM,
                   PelTag::GPS_DEST_LATITUDE_REF,
                   PelTag::GPS_DEST_LATITUDE,
                   PelTag::GPS_DEST_LONGITUDE_REF,
                   PelTag::GPS_DEST_LONGITUDE,
                   PelTag::GPS_DEST_BEARING_REF,
                   PelTag::GPS_DEST_BEARING,
                   PelTag::GPS_DEST_DISTANCE_REF,
                   PelTag::GPS_DEST_DISTANCE,
                   PelTag::GPS_PROCESSING_METHOD,
                   PelTag::GPS_AREA_INFORMATION,
                   PelTag::GPS_DATE_STAMP,
                   PelTag::GPS_DIFFERENTIAL);

    case PelIfd::INTEROPERABILITY:
      return array(PelTag::INTEROPERABILITY_INDEX, 
                   PelTag::INTEROPERABILITY_VERSION,
                   PelTag::RELATED_IMAGE_FILE_FORMAT, 
                   PelTag::RELATED_IMAGE_WIDTH, 
                   PelTag::RELATED_IMAGE_LENGTH);

      /* TODO: Where do these tags belong?
PelTag::FILL_ORDER,
PelTag::DOCUMENT_NAME, 
PelTag::TRANSFER_RANGE, 
PelTag::JPEG_PROC, 
PelTag::BATTERY_LEVEL, 
PelTag::IPTC_NAA, 
PelTag::INTER_COLOR_PROFILE, 
PelTag::CFA_REPEAT_PATTERN_DIM, 
      */
    }
  }


  /**
   * Get the name of an IFD type.
   *
   * @param int one of {@link PelIfd::IFD0}, {@link PelIfd::IFD1},
   * {@link PelIfd::EXIF}, {@link PelIfd::GPS}, or {@link
   * PelIfd::INTEROPERABILITY}.
   *
   * @return string the name of type.
   */
  static function getTypeName($type) {
    switch ($type) {
    case self::IFD0:
      return '0';
    case self::IFD1:
      return '1';
    case self::EXIF:
      return 'Exif';
    case self::GPS:
      return 'GPS';
    case self::INTEROPERABILITY:
      return 'Interoperability';
    default:
      throw new PelIfdException('Unknown IFD type: %d', $type);
    }
  }


  /**
   * Get the name of this directory.
   *
   * @return string the name of this directory.
   */
  function getName() {
    return $this->getTypeName($this->type);
  }


  /**
   * Adds an entry to the directory.
   *
   * @param PelEntry the entry that will be added.
   *
   * @todo The entry will be identified with its tag, so each
   * directory can only contain one entry with each tag.  Is this a
   * bug?
   */
  function addEntry(PelEntry $e) {
    $this->entries[$e->getTag()] = $e;
  }


  /**
   * Does a given tag exist in this IFD?
   *
   * This methods is part of the ArrayAccess SPL interface for
   * overriding array access of objects, it allows you to check for
   * existance of an entry in the IFD:
   *
   * <code>
   * if (isset($ifd[PelTag::FNUMBER]))
   *   // ... do something with the F-number.
   * </code>
   *
   * @param PelTag the offset to check.
   *
   * @return boolean whether the tag exists.
   */
  function offsetExists($tag) {
    return isset($this->entries[$tag]);
  }


  /**
   * Retrieve a given tag from this IFD.
   *
   * This methods is part of the ArrayAccess SPL interface for
   * overriding array access of objects, it allows you to read entries
   * from the IFD the same was as for an array:
   *
   * <code>
   * $entry = $ifd[PelTag::FNUMBER];
   * </code>
   *
   * @param PelTag the tag to return.  It is an error to ask for a tag
   * which is not in the IFD, just like asking for a non-existant
   * array entry.
   *
   * @return PelEntry the entry.
   */
  function offsetGet($tag) {
    return $this->entries[$tag];
  }


  /**
   * Set or update a given tag in this IFD.
   *
   * This methods is part of the ArrayAccess SPL interface for
   * overriding array access of objects, it allows you to add new
   * entries or replace esisting entries by doing:
   *
   * <code>
   * $ifd[PelTag::EXPOSURE_BIAS_VALUE] = $entry;
   * </code>
   *
   * Note that the actual array index passed is ignored!  Instead the
   * {@link PelTag} from the entry is used.
   *
   * @param PelTag the offset to update.
   *
   * @param PelEntry the new value.
   */
  function offsetSet($tag, $e) {
    if ($e instanceof PelEntry) {
      $tag = $e->getTag();
      $this->entries[$tag] = $e;
    } else {
      throw new PelInvalidArgumentException('Argument "%s" must be a PelEntry.', $e);
    }
  }


  /**
   * Unset a given tag in this IFD.
   *
   * This methods is part of the ArrayAccess SPL interface for
   * overriding array access of objects, it allows you to delete
   * entries in the IFD by doing:
   *
   * <code>
   * unset($ifd[PelTag::EXPOSURE_BIAS_VALUE])
   * </code>
   *
   * @param PelTag the offset to delete.
   */
  function offsetUnset($tag) {
    unset($this->entries[$tag]);
  }


  /**
   * Retrieve an entry.
   *
   * @param PelTag the tag identifying the entry.
   *
   * @return PelEntry the entry associated with the tag, or null if no
   * such entry exists.
   */
  function getEntry($tag) {
    if (isset($this->entries[$tag]))
      return $this->entries[$tag];
    else
      return null;
  }


  /**
   * Returns all entries contained in this IFD.
   *
   * @return array an array of {@link PelEntry} objects, or rather
   * descendant classes.  The array has {@link PelTag}s as keys
   * and the entries as values.
   *
   * @see getEntry
   * @see getIterator
   */
  function getEntries() {
    return $this->entries;
  }

  
  /**
   * Return an iterator for all entries contained in this IFD.
   *
   * Used with foreach as in
   *
   * <code>
   * foreach ($ifd as $tag => $entry) {
   *   // $tag is now a PelTag and $entry is a PelEntry object.
   * }
   * </code>
   *
   * @return Iterator an iterator using the {@link PelTag tags} as
   * keys and the entries as values.
   */
  function getIterator() {
    return new ArrayIterator($this->entries);
  }
  

  /**
   * Returns available thumbnail data.
   *
   * @return string the bytes in the thumbnail, if any.  If the IFD
   * does not contain any thumbnail data, the empty string is
   * returned.
   *
   * @todo Throw an exception instead when no data is available?
   *
   * @todo Return the $this->thumb_data object instead of the bytes?
   */
  function getThumbnailData() {
    if ($this->thumb_data != null)
      return $this->thumb_data->getBytes();
    else
      return '';
  }
  

  /**
   * Make this directory point to a new directory.
   *
   * @param PelIfd the IFD that this directory will point to.
   */
  function setNextIfd(PelIfd $i) {
    $this->next = $i;
  }


  /**
   * Return the IFD pointed to by this directory.
   *
   * @return PelIfd the next IFD, following this IFD. If this is the
   * last IFD, null is returned.
   */
  function getNextIfd() {
    return $this->next;
  }


  /**
   * Check if this is the last IFD.
   *
   * @return boolean true if there are no following IFD, false
   * otherwise.
   */
  function isLastIfd() {
    return $this->next == null;
  }


  /**
   * Add a sub-IFD.
   *
   * Any previous sub-IFD of the same type will be overwritten.
   *
   * @param PelIfd the sub IFD.  The type of must be one of {@link
   * PelIfd::EXIF}, {@link PelIfd::GPS}, or {@link
   * PelIfd::INTEROPERABILITY}.
   */
  function addSubIfd(PelIfd $sub) {
    $this->sub[$sub->type] = $sub;
  }


  /**
   * Return a sub IFD.
   *
   * @param int the type of the sub IFD.  This must be one of {@link
   * PelIfd::EXIF}, {@link PelIfd::GPS}, or {@link
   * PelIfd::INTEROPERABILITY}.
   *
   * @return PelIfd the IFD associated with the type, or null if that
   * sub IFD does not exist.
   */
  function getSubIfd($type) {
    if (isset($this->sub[$type]))
      return $this->sub[$type];
    else
      return null;
  }


  /**
   * Get all sub IFDs.
   *
   * @return array an associative array with (IFD-type, {@link
   * PelIfd}) pairs.
   */
  function getSubIfds() {
    return $this->sub;
  }


  /**
   * Turn this directory into bytes.
   *
   * This directory will be turned into a byte string, with the
   * specified byte order.  The offsets will be calculated from the
   * offset given.
   *
   * @param int the offset of the first byte of this directory.
   *
   * @param PelByteOrder the byte order that should be used when
   * turning integers into bytes.  This should be one of {@link
   * PelConvert::LITTLE_ENDIAN} and {@link PelConvert::BIG_ENDIAN}.
   */
  function getBytes($offset, $order) {
    $bytes = '';
    $extra_bytes = '';

    Pel::debug('Bytes from IDF will start at offset %d within Exif data',
               $offset);
    
    $n = count($this->entries) + count($this->sub);
    if ($this->thumb_data != null) {
      /* We need two extra entries for the thumbnail offset and
       * length. */
      $n += 2;
    }

    $bytes .= PelConvert::shortToBytes($n, $order);

    /* Initialize offset of extra data.  This included the bytes
     * preceding this IFD, the bytes needed for the count of entries,
     * the entries themselves (and sub entries), the extra data in the
     * entries, and the IFD link.
     */
    $end = $offset + 2 + 12 * $n + 4;

    foreach ($this->entries as $tag => $entry) {
      /* Each entry is 12 bytes long. */
      $bytes .= PelConvert::shortToBytes($entry->getTag(), $order);
      $bytes .= PelConvert::shortToBytes($entry->getFormat(), $order);
      $bytes .= PelConvert::longToBytes($entry->getComponents(), $order);
      
      /*
       * Size? If bigger than 4 bytes, the actual data is not in
       * the entry but somewhere else.
       */
      $data = $entry->getBytes($order);
      $s = strlen($data);
      if ($s > 4) {
        Pel::debug('Data size %d too big, storing at offset %d instead.',
                   $s, $end);
        $bytes .= PelConvert::longToBytes($end, $order);
        $extra_bytes .= $data;
        $end += $s;
      } else {
        Pel::debug('Data size %d fits.', $s);
        /* Copy data directly, pad with NULL bytes as necessary to
         * fill out the four bytes available.*/
        $bytes .= $data . str_repeat(chr(0), 4 - $s);
      }
    }

    if ($this->thumb_data != null) {
      Pel::debug('Appending %d bytes of thumbnail data at %d',
                 $this->thumb_data->getSize(), $end);
      // TODO: make PelEntry a class that can be constructed with
      // arguments corresponding to the newt four lines.
      $bytes .= PelConvert::shortToBytes(PelTag::JPEG_INTERCHANGE_FORMAT_LENGTH,
                                         $order);
      $bytes .= PelConvert::shortToBytes(PelFormat::LONG, $order);
      $bytes .= PelConvert::longToBytes(1, $order);
      $bytes .= PelConvert::longToBytes($this->thumb_data->getSize(),
                                        $order);
      
      $bytes .= PelConvert::shortToBytes(PelTag::JPEG_INTERCHANGE_FORMAT,
                                         $order);
      $bytes .= PelConvert::shortToBytes(PelFormat::LONG, $order);
      $bytes .= PelConvert::longToBytes(1, $order);
      $bytes .= PelConvert::longToBytes($end, $order);
      
      $extra_bytes .= $this->thumb_data->getBytes();
      $end += $this->thumb_data->getSize();
    }

    
    /* Find bytes from sub IFDs. */
    $sub_bytes = '';
    foreach ($this->sub as $type => $sub) {
      if ($type == PelIfd::EXIF)
        $tag = PelTag::EXIF_IFD_POINTER;
      elseif ($type == PelIfd::GPS)
        $tag = PelTag::GPS_INFO_IFD_POINTER;
      elseif ($type == PelIfd::INTEROPERABILITY)
        $tag = PelTag::INTEROPERABILITY_IFD_POINTER;

      /* Make an aditional entry with the pointer. */
      $bytes .= PelConvert::shortToBytes($tag, $order);
      /* Next the format, which is always unsigned long. */
      $bytes .= PelConvert::shortToBytes(PelFormat::LONG, $order);
      /* There is only one component. */
      $bytes .= PelConvert::longToBytes(1, $order);

      $data = $sub->getBytes($end, $order);
      $s = strlen($data);
      $sub_bytes .= $data;

      $bytes .= PelConvert::longToBytes($end, $order);
      $end += $s;
    }

    /* Make link to next IFD, if any*/
    if ($this->isLastIFD()) {
      $link = 0;
    } else {
      $link = $end;
    }

    Pel::debug('Link to next IFD: %d', $link);
    
    $bytes .= PelConvert::longtoBytes($link, $order);

    $bytes .= $extra_bytes . $sub_bytes;

    if (!$this->isLastIfd())
      $bytes .= $this->next->getBytes($end, $order);

    return $bytes;
  }

  
  /**
   * Turn this directory into text.
   *
   * @return string information about the directory, mainly for
   * debugging.
   */
  function __toString() {
    $str = Pel::fmt("Dumping IFD %s with %d entries...\n",
                    $this->getName(), count($this->entries));
    
    foreach ($this->entries as $entry)
      $str .= $entry->__toString();

    $str .= Pel::fmt("Dumping %d sub IFDs...\n", count($this->sub));

    foreach ($this->sub as $type => $ifd)
      $str .= $ifd->__toString();

    if ($this->next != null)
      $str .= $this->next->__toString();

    return $str;
  }


}

if (function_exists('dgettext')) {
  bindtextdomain('pel', dirname(__FILE__) . '/locale');
} else {


  function dgettext($domain, $str) {
    return $str;
  }
}



class Pel {

  /**
   * Flag for controlling debug information.
   *
   * The methods producing debug information ({@link debug()} and
   * {@link warning()}) will only output something if this variable is
   * set to true.
   *
   * @var boolean
   */
  private static $debug = false;

  /**
   * Flag for strictness of parsing.
   *
   * If this variable is set to true, then most errors while loading
   * images will result in exceptions being thrown.  Otherwise a
   * warning will be emitted (using {@link Pel::warning}) and the
   * exceptions will be appended to {@link Pel::$exceptions}.
   *
   * Some errors will still be fatal and result in thrown exceptions,
   * but an effort will be made to skip over as much garbage as
   * possible.
   *
   * @var boolean
   */
  private static $strict = false;
  
  /**
   * Stored exceptions.
   *
   * When {@link Pel::$strict} is set to false exceptions will be
   * accumulated here instead of being thrown.
   */
  private static $exceptions = array();


  /**
   * Return list of stored exceptions.
   *
   * When PEL is parsing in non-strict mode, it will store most
   * exceptions instead of throwing them.  Use this method to get hold
   * of them when a call returns.
   *
   * Code for using this could look like this:
   *
   * <code>
   * Pel::setStrictParsing(true);
   * Pel::clearExceptions();
   *
   * $jpeg = new PelJpeg($file);
   *
   * // Check for exceptions.
   * foreach (Pel::getExceptions() as $e) {
   *     printf("Exception: %s\n", $e->getMessage());
   *     if ($e instanceof PelEntryException) {
   *       // Warn about entries that couldn't be loaded.
   *       printf("Warning: Problem with %s.\n",
   *              PelTag::getName($e->getType(), $e->getTag()));
   *     }
   * }
   * </code>
   *
   * This gives applications total control over the amount of error
   * messages shown and (hopefully) provides the necessary information
   * for proper error recovery.
   *
   * @return array the exceptions.
   */
  static function getExceptions() {
    return self::$exceptions;
  }


  /**
   * Clear list of stored exceptions.
   *
   * Use this function before a call to some method if you intend to
   * check for exceptions afterwards.
   */
  static function clearExceptions() {
    self::$exceptions = array();
  }


  /**
   * Conditionally throw an exception.
   *
   * This method will throw the passed exception when strict parsing
   * in effect (see {@link setStrictParsing()}).  Otherwise the
   * exception is stored (it can be accessed with {@link
   * getExceptions()}) and a warning is issued (with {@link
   * Pel::warning}).
   *
   * @param PelException $e the exceptions.
   */
  static function maybeThrow(PelException $e) {
    if (self::$strict) {
      throw $e;
    } else {
      self::$exceptions[] = $e;
      self::warning('%s (%s:%s)', $e->getMessage(),
                   basename($e->getFile()), $e->getLine());
    }
  }


  /**
   * Enable/disable strict parsing.
   *
   * If strict parsing is enabled, then most errors while loading
   * images will result in exceptions being thrown.  Otherwise a
   * warning will be emitted (using {@link Pel::warning}) and the
   * exceptions will be stored for later use via {@link
   * getExceptions()}.
   *
   * Some errors will still be fatal and result in thrown exceptions,
   * but an effort will be made to skip over as much garbage as
   * possible.
   *
   * @param boolean $flag use true to enable strict parsing, false to
   * diable.
   */
  function setStrictParsing($flag) {
    self::$strict = $flag;
  }


  /**
   * Get current setting for strict parsing.
   *
   * @return boolean true if strict parsing is in effect, false
   * otherwise.
   */
  function getStrictParsing() {
    return self::$strict;
  }


  /**
   * Enable/disable debugging output.
   *
   * @param boolean $flag use true to enable debug output, false to
   * diable.
   */
  function setDebug($flag) {
    self::$debug = $flag;
  }


  /**
   * Get current setting for debug output.
   *
   * @return boolean true if debug is enabled, false otherwise.
   */
  function getDebug() {
    return self::$debug;
  }


  /**
   * Conditionally output debug information.
   *
   * This method works just like printf() except that it always
   * terminates the output with a newline, and that it only outputs
   * something if the {@link Pel::$debug} is true.
   *
   * @param string $format the format string.
   *
   * @param mixed $args,... any number of arguments can be given.  The
   * arguments will be available for the format string as usual with
   * sprintf().
   */
  static function debug() {
    if (self::$debug) {
      $args = func_get_args();
      $str = array_shift($args);
      vprintf($str . "\n", $args);
    }
  }

  
  /**
   * Conditionally output a warning.
   *
   * This method works just like printf() except that it prepends the
   * output with the string 'Warning: ', terminates the output with a
   * newline, and that it only outputs something if the PEL_DEBUG
   * defined to some true value.
   *
   * @param string $format the format string.
   *
   * @param mixed $args,... any number of arguments can be given.  The
   * arguments will be available for the format string as usual with
   * sprintf().
   */
  static function warning() {
    if (self::$debug) {
      $args = func_get_args();
      $str = array_shift($args);
      vprintf('Warning: ' . $str . "\n", $args);
    }
  }


  /**
   * Translate a string.
   *
   * This static function will use Gettext to translate a string.  By
   * always using this function for static string one is assured that
   * the translation will be taken from the correct text domain.
   * Dynamic strings should be passed to {@link fmt} instead.
   *
   * @param string the string that should be translated.
   *
   * @return string the translated string, or the original string if
   * no translation could be found.
   */
  static function tra($str) {
    return dgettext('pel', $str);
  }
  

  /**
   * Translate and format a string.
   *
   * This static function will first use Gettext to translate a format
   * string, which will then have access to any extra arguments.  By
   * always using this function for dynamic string one is assured that
   * the translation will be taken from the correct text domain.  If
   * the string is static, use {@link tra} instead as it will be
   * faster.
   *
   * @param string $format the format string.  This will be translated
   * before being used as a format string.
   *
   * @param mixed $args,... any number of arguments can be given.  The
   * arguments will be available for the format string as usual with
   * sprintf().
   *
   * @return string the translated string, or the original string if
   * no translation could be found.
   */
  static function fmt() {
    $args = func_get_args();
    $str = array_shift($args);
    return vsprintf(dgettext('pel', $str), $args);
  }

}

class PelExif extends PelJpegContent {
  const EXIF_HEADER = "Exif\0\0";

  private $tiff = null;

  function __construct() {

  }

  function load(PelDataWindow $d) {
    Pel::debug('Parsing %d bytes of Exif data...', $d->getSize());

    if ($d->getSize() < 6)
      throw new PelInvalidDataException('Expected at least 6 bytes of Exif ' .
                                        'data, found just %d bytes.',
                                        $d->getSize());
    
    if ($d->strcmp(0, self::EXIF_HEADER)) {
      $d->setWindowStart(strlen(self::EXIF_HEADER));
    } else {
      throw new PelInvalidDataException('Exif header not found.');
    }

    $this->tiff = new PelTiff();
    $this->tiff->load($d);
  }

  function setTiff(PelTiff $tiff) {
    $this->tiff = $tiff;
  }


  function getTiff() {
    return $this->tiff;
  }

  function getBytes() {
    return self::EXIF_HEADER . $this->tiff->getBytes();
  }


  function __toString() {
    return Pel::tra("Dumping Exif data...\n") .
      $this->tiff->__toString();
  }

}

class PelJpegInvalidMarkerException extends PelException {


  function __construct($marker, $offset) {
    parent::__construct('Invalid marker found at offset %d: 0x%2X',
                        $offset, $marker);
  }
}


class PelJpeg {

  private $sections = array();


  private $jpeg_data = null;


  function __construct($data = false) {
    if ($data === false)
      return;

    if (is_string($data)) {
      Pel::debug('Initializing PelJpeg object from %s', $data);
      $this->loadFile($data);
    } elseif ($data instanceof PelDataWindow) {
      Pel::debug('Initializing PelJpeg object from PelDataWindow.');
      $this->load($data);
    } elseif (is_resource($data) && get_resource_type($data) == 'gd') {
      Pel::debug('Initializing PelJpeg object from image resource.');
      ob_start();
      ImageJpeg($data);
      $bytes = ob_get_clean();
      $this->load(new PelDataWindow($bytes));
    } else {
      throw new PelInvalidArgumentException('Bad type for $data: %s', 
                                            gettype($data));
    }
  }

  function load(PelDataWindow $d) {
    Pel::debug('Parsing %d bytes...', $d->getSize());

    /* JPEG data is stored in big-endian format. */
    $d->setByteOrder(PelConvert::BIG_ENDIAN);
    

    while ($d->getSize() > 0) {

      for ($i = 0; $i < 7; $i++)
        if ($d->getByte($i) != 0xFF)
          break;

      $marker = $d->getByte($i);

      if (!PelJpegMarker::isValid($marker))
        throw new PelJpegInvalidMarkerException($marker, $i);

      /* Move window so first byte becomes first byte in this
       * section. */
      $d->setWindowStart($i+1);

      if ($marker == PelJpegMarker::SOI || $marker == PelJpegMarker::EOI) {
        $content = new PelJpegContent(new PelDataWindow());
        $this->appendSection($marker, $content);
      } else {
        /* Read the length of the section.  The length includes the
         * two bytes used to store the length. */
        $len = $d->getShort(0) - 2;
        
        Pel::debug('Found %s section of length %d',
                   PelJpegMarker::getName($marker), $len);

        /* Skip past the length. */
        $d->setWindowStart(2);

        if ($marker == PelJpegMarker::APP1) {

          try {
            $content = new PelExif();
            $content->load($d->getClone(0, $len));
          } catch (PelInvalidDataException $e) {
            /* We store the data as normal JPEG content if it could
             * not be parsed as Exif data. */
            $content = new PelJpegContent($d->getClone(0, $len));
          }

          $this->appendSection($marker, $content);
          /* Skip past the data. */
          $d->setWindowStart($len);

        } elseif ($marker == PelJpegMarker::COM) {

          $content = new PelJpegComment();
          $content->load($d->getClone(0, $len));
          $this->appendSection($marker, $content);
          $d->setWindowStart($len);

        } else {

          $content = new PelJpegContent($d->getClone(0, $len));
          $this->appendSection($marker, $content);
          /* Skip past the data. */
          $d->setWindowStart($len);
          
          /* In case of SOS, image data will follow. */
          if ($marker == PelJpegMarker::SOS) {
         

            $length = $d->getSize();
            while ($d->getByte($length-2) != 0xFF ||
                   $d->getByte($length-1) != PelJpegMarker::EOI) {
              $length--;
            }

            $this->jpeg_data = $d->getClone(0, $length-2);
            Pel::debug('JPEG data: ' . $this->jpeg_data->__toString());

            /* Append the EOI. */
            $this->appendSection(PelJpegMarker::EOI,
                                 new PelJpegContent(new PelDataWindow()));

            /* Now check to see if there are any trailing data. */
            if ($length != $d->getSize()) {
              Pel::maybeThrow(new PelException('Found trailing content ' .
                                               'after EOI: %d bytes',
                                               $d->getSize() - $length));
              $content = new PelJpegContent($d->getClone($length));
              /* We don't have a proper JPEG marker for trailing
               * garbage, so we just use 0x00... */
              $this->appendSection(0x00, $content);
            }

            break;
          }
        }
      }
    } 
  }


  function loadFile($filename) {
    $this->load(new PelDataWindow(file_get_contents($filename)));
  }
  


  function setExif(PelExif $exif) {
    $app0_offset = 1;
    $app1_offset = -1;

    for ($i = 0; $i < count($this->sections); $i++) {
      if ($this->sections[$i][0] == PelJpegMarker::APP0) {
        $app0_offset = $i;
      } elseif ($this->sections[$i][0] == PelJpegMarker::APP1) {
        $app1_offset = $i;
        break;
      }
    }

    if ($app1_offset > 0)
      $this->sections[$app1_offset][1] = $exif;
    else
      $this->insertSection(PelJpegMarker::APP1, $exif, $app0_offset+1);
  }



  function getExif() {
    $exif = $this->getSection(PelJpegMarker::APP1);
    if ($exif instanceof PelExif)
      return $exif;
    else
      return null;
  }


  function clearExif() {
    for ($i = 0; $i < count($this->sections); $i++) {
      if ($this->sections[$i][0] == PelJpegMarker::APP1) {
        unset($this->sections[$i]);
        return;
      }
    }
  }


  function appendSection($marker, PelJpegContent $content) {
    $this->sections[] = array($marker, $content);
  }


  function insertSection($marker, PelJpegContent $content, $offset) {
    array_splice($this->sections, $offset, 0, array(array($marker, $content)));
  }


  function getSection($marker, $skip = 0) {
    foreach ($this->sections as $s) {
      if ($s[0] == $marker)
        if ($skip > 0)
          $skip--;
        else
          return $s[1];
    }

    return null;        
  }


  function getSections() {
    return $this->sections;
  }


  function getBytes() {
    $bytes = '';

    foreach ($this->sections as $section) {
      $m = $section[0];
      $c = $section[1];

      $bytes .= "\xFF" . PelJpegMarker::getBytes($m);

      if ($m == PelJpegMarker::SOI || $m == PelJpegMarker::EOI)
        continue;

      $data = $c->getBytes();
      $size = strlen($data) + 2;
      
      $bytes .= PelConvert::shortToBytes($size, PelConvert::BIG_ENDIAN);
      $bytes .= $data;
      
     if ($m == PelJpegMarker::SOS)
        $bytes .= $this->jpeg_data->getBytes();
    }

    return $bytes;

  }


  function __toString() {
    $str = Pel::tra("Dumping JPEG data...\n");
    for ($i = 0; $i < count($this->sections); $i++) {
      $m = $this->sections[$i][0];
      $c = $this->sections[$i][1];
      $str .= Pel::fmt("Section %d (marker 0x%02X - %s):\n",
                       $i, $m, PelJpegMarker::getName($m));
      $str .= Pel::fmt("  Description: %s\n",
                       PelJpegMarker::getDescription($m));
      
      if ($m == PelJpegMarker::SOI ||
          $m == PelJpegMarker::EOI)
        continue;
      
      if ($c instanceof PelExif) {
        $str .= Pel::tra("  Content    : Exif data\n");
        $str .= $c->__toString() . "\n";
      } elseif ($c instanceof PelJpegComment) {
        $str .= Pel::fmt("  Content    : %s\n", $c->getValue());
      } else {
        $str .= Pel::tra("  Content    : Unknown\n");
      }
    }

    return $str;
  }



  static function isValid(PelDataWindow $d) {
   $d->setByteOrder(PelConvert::BIG_ENDIAN);
    
    for ($i = 0; $i < 7; $i++)
      if ($d->getByte($i) != 0xFF)
        break;
    
    return $d->getByte($i) == PelJpegMarker::SOI;
  }
}
?>