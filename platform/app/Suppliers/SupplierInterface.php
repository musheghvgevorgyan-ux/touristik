<?php

namespace App\Suppliers;

/**
 * Universal interface for all travel suppliers.
 *
 * Every supplier adapter (Hotelbeds, Amadeus, local tours, etc.)
 * must implement this interface to ensure a unified booking pipeline.
 */
interface SupplierInterface
{
    /**
     * Get the supplier identifier
     */
    public function getName(): string;

    /**
     * Search for available products
     *
     * @param array $params Search criteria (dates, destination, guests, etc.)
     * @return array Unified list of search results
     */
    public function search(array $params): array;

    /**
     * Verify current rate/availability for a specific product
     *
     * @param string $rateKey Supplier-specific rate identifier
     * @return array ['available' => bool, 'price' => float, 'currency' => string, ...]
     */
    public function checkRate(string $rateKey): array;

    /**
     * Create a booking
     *
     * @param array $details Booking details (guest info, rate key, etc.)
     * @return array ['success' => bool, 'reference' => string, 'supplier_ref' => string, ...]
     */
    public function book(array $details): array;

    /**
     * Cancel an existing booking
     *
     * @param string $reference Supplier booking reference
     * @return array ['success' => bool, 'cancellation_ref' => string, ...]
     */
    public function cancel(string $reference): array;

    /**
     * Get booking details/status
     *
     * @param string $reference Supplier booking reference
     * @return array Booking details in unified format
     */
    public function getBooking(string $reference): array;
}
