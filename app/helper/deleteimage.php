<?php

use Illuminate\Support\Facades\Storage;


function deleteimage($imagePath, $disk = 'public')

{

    if (Storage::disk($disk)->exists($imagePath)) {
        // Delete the image
        Storage::disk($disk)->delete($imagePath);
        return true; // Image deleted successfully
    }
    return false;


}
