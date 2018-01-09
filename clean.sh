#!/bin/sh                                                                                                                                                                                       

IMAGES_DIR="../";
PREVIEWS_DIR="../previews";
TRASH_DIR="../trashbin"

for filepath in $TRASH_DIR/*
do
    filename=$(basename $filepath)
    if [ -f $IMAGES_DIR/$filename ]
    then
        echo $filename;
    fi
done
