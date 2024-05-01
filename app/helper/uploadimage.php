<?php

use Illuminate\Support\Facades\Storage;


function uploadimage($image,$folderName,$disk){


    $fileName= $image->getClientOriginalName();
    $image->storeAs($folderName, $fileName, $disk);
   return $fileName;
    }
