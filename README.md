# img2daad

A tool to include images into DAAD graphics database (DAT files)

This tool is able to create a DAAD Graphics Database for Atari ST and Amiga, 
from either a group of Degas files (PI1) or PNG files.

In either case they should be 320x200, 16 colour images. Please notice you 
don't need to create a 16 colour PNG file, just make sure you don't use more
than 16 colours. Also, for PNG files, take in mind DAAD only supports 3-bit 
depth colours, so you would have to stick to AtariST palette or your colours
would be affected when packing. Please search for "Atari ST palette" or
"9-bit palette" to find a proper one.

To use img2daad you just need php (with GD support if you are going to use
PNG files), and then run

php img2daad <folder>

Where folder is the path to a folder where your PNG or PI1 files are stored.
Files must be numbered nnn.PNG or nnn.PI1, where n is the number of the location
for each file. i.e. 012.PNG is the graphic for location 12. 

Please notice PNG takes precedence over PI1 if both are available.

Cutting
-------
At the moment, img2daad will only cut the 320x200 image to create a fixed graphic 
placed at 0,0, and with 320x96 size.

Cloning
-------
Sometimes the same graphic is used for more than one location. Of course, you 
can just copy the file and rename, but that will get double space. The proper
way to do it is cloning.

For instance, if you want location 15 to have same graphic as location 9, you can
put a text file, 15.JSON, at the pictures folder, with the following content:

 {
     "clone":1,
     "location":9
 }

 That will tell img2daad location 15 is a clone of location 9.

 Please notice you cannot clone a location whose number is higher than current one,
 so in the previous example, you cannot do this:

{
     "clone":1,
     "location":18
 }

So the idea is you always clone a previous location.

Image settings
--------------
You can change some settings adding a JSON file as well. If there is picture and
also a JSON file, img2daad will read the parameters inside, that can be (comma
separated):


    "float":1     --> Tells DAAD this is a floating image
    "buffer":1    --> Tells DAAD this image is to be buffered
    "firstPAL":n  --> Tells DAAD to load palette only from palette index N
    "lastPAL":n   --> Tells DAAD to load palette only up to palette index N
    "fixedX":n    --> Sets X coord for fixed images
    "fixedY":n    --> Sets Y coord for fixed images

For instance, if there is a 3.JSON together with 3.PNG, like the one below,
that image will be placed at 80,80 and be buffered.

{
    "buffer":1  
    "fixedX":80 
    "fixedY":80 
}

Clone and settings
------------------
You can combine the cloning and settings parameters in a JSON file, so you can,
for instance, have a clone placed at other X,Y coords.


Compression
-----------
Despite the -c parameter, currently img2daad does not support compression.


Acknowledges
------------
To Morgul, whose help and paralell work inspired this tool.
To Tim Gilberts, without his ancient code rescued from old floppy disk this would had been much harder.
To Stefan Vogt. Despite not being directly involved, he was kind enough to have a public repository with 
all the Curse of Rabenstein images, which has been helpful for testing.