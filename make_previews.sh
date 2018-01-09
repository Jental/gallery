#!/bin/sh                                                                                                                                                                                       

IMAGES_DIR="../";
PREVIEWS_DIR="../previews";

for filepath in $IMAGES_DIR/*[.jpg,.png,.JPG,.PNG]
do
    filename=$(basename $filepath)
    if [ ! -f $PREVIEWS_DIR/$filename ]
       then
           echo $filename;
           convert $filepath -resize 320x180 $PREVIEWS_DIR/$filename
           chown jental:server $PREVIEWS_DIR/$filename
           chmod 770 $PREVIEWS_DIR/$filename
    fi
done
