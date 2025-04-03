<?php

function getImagePath($imageName)
{
    return env('ADMIN_PANEL_URL') . env('IMAGES_PATH') . $imageName;
}

function calculateDiscount($price, $discount)
{
    return round((($price - $discount) / $price) * 100);
}
