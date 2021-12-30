<?php

define('CLIPWIDTH',320); // whole width
define('CLIPHEIGHT',96); // as a standard for DAAD Ready and Maluva, but change if you please
define('NUM_PLANES',4);
define('BYTES_PER_LINE',160);

include_once('degas.php');

function error($errorMsg)
{
    echo "$errorMsg\n";
    exit(1);
}

function dumpDatabase($outputFile, $outputFilename)
{
    $fp  = fopen($outputFilename, 'w');
    if ($fp) 
    {
        for ($i=0;$i<sizeof($outputFile);$i++)
        fwrite($fp, chr($outputFile[$i]), 1);
    }
    fclose($fp);
}

// MAIN
$degas = new degasFileReader();
if (!$degas->loadFile('10.PI1')) error('Invalid Degas Elite file.');

$outputFile = array();

// The DAT file header

$outputFile[] = 0x03;
$outputFile[] = 0x00;  // Output signature 0x0300

$outputFile[] = 0x00;  
$outputFile[] = 0x00;  // Output screen mode (low res, 320x200)

$outputFile[] = 0x00;  
$outputFile[] = 0x01;  // Output number of pictures

$outputFile[] = 0x00;  
$outputFile[] = 0x00;  
$outputFile[] = 0x00;  
$outputFile[] = 0x00;   // Dummy for filesize

// First "image header"

$outputFile[] = 0x00;  
$outputFile[] = 0x00;  
$outputFile[] = 0x30;  
$outputFile[] = 0x0A;   // Offset to data

$outputFile[] = 0x00;  
$outputFile[] = 0x04;  // Flags (no buffer, fixed)

$outputFile[] = 0x00;  
$outputFile[] = 0x00;   // X coord

$outputFile[] = 0x00;  
$outputFile[] = 0x00;   // Y coord

$outputFile[] = 0x00;   // First palette color,filler as it's float
$outputFile[] = 0x0F;   // Last palette color,filler as it's float

// Now the palette
$degas->seekFile(2);  // point to palette

for($i=0;$i<32;$i++) $outputFile[] = $degas->readByte(); // read palette

$outputFile[] = 0x00;  
$outputFile[] = 0x00;   
$outputFile[] = 0x00;  
$outputFile[] = 0x00;   // CGA palette pointer, filler as there is no CGA palette

// Now fill the other 255 "image headers"

for ($i=0;$i<255;$i++)
 for ($j=0;$j<48;$j++)
   $outputFile[] = 0x00;

$screen = array();
for ($i=0;$i<32000;$i++) $screen[] = $degas->readByte(); // read 32.000 bytes of image data


// we will be getting only the window (0,0,320,96)

$xs = 0;
$ys = 0;
$width = CLIPWIDTH;
$height= CLIPHEIGHT; 

// From now on, this is a copy of Tim's code, which honestly I haven't tried to understand, 
// basically because I don't need to :-)

$co=0;
$xs=$xs>>3; // Convert to a column number 
$width=$width>>3;



$length=$width * $height * NUM_PLANES; // 4 = number of planes
$lo=$ys * BYTES_PER_LINE;
$cs = ($xs>>1) * (NUM_PLANES<<1) + ($xs & 1); 

for($l=0;$l<$height;$l++)
{
  $cp=$cs;
  for($c=0;$c<$width;$c++)
  {
    for($p=0;$p<NUM_PLANES;$p++)
      $clipdata[$co++] = $screen[$lo + $cp + ($p<<1)];
    $cp++;
    if(($cp & 1)==0) $cp+=(NUM_PLANES-1)*2; /* Skip plane data */
  }
  $lo+=BYTES_PER_LINE;
}

// Tim's code ends here

// The image data mini header
$outputFile[] = CLIPWIDTH  >> 8; //MSB
$outputFile[] = CLIPWIDTH & 0x00FF ; //LSB

$outputFile[] = CLIPHEIGHT  >> 8; //MSB
$outputFile[] = CLIPHEIGHT & 0x00FF ; //LSB

$datasize = sizeof($clipdata);

$outputFile[] = $datasize  >> 8; //MSB
$outputFile[] = $datasize & 0x00FF ; //LSB

for ($i=0;$i<$datasize;$i++) $outputFile[] = $clipdata[$i];

// Update file size in the header
$filesize = sizeof($outputFile);
$outputFile[6] = ($filesize & 0xFF000000) >> 24;
$outputFile[7] = ($filesize & 0x00FF0000) >> 16;
$outputFile[8] = ($filesize & 0x0000FF00) >> 8;
$outputFile[9] = ($filesize & 0x000000FF);

dumpDatabase($outputFile, "PART1.DAT");




