<?php

// TODO: Integrate new flight API for live destination prices
// For now, returns prices from local database only

function getDestinationFlightPrice($destinationName) {
    return null;
}

function getDestinationsWithLivePrices($pdo) {
    $destinations = getDestinations($pdo);
    foreach ($destinations as &$dest) {
        $livePrice = getDestinationFlightPrice($dest['name']);
        if ($livePrice !== null) {
            $dest['price'] = $livePrice;
        }
    }
    return $destinations;
}
