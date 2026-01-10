<?php
/**
 * Currency Helper Functions
 * Centralized currency formatting for the entire application
 */

/**
 * Format a value as Philippine Peso (₱)
 * 
 * @param float $value The numeric value to format
 * @return string Formatted currency string (e.g., "₱1,234.56")
 */
function moneyFormat(float $value): string
{
    return '₱' . number_format($value, 2, '.', ',');
}

/**
 * Format a value as currency without symbol (for calculations or custom display)
 * 
 * @param float $value The numeric value to format
 * @return string Formatted number string (e.g., "1,234.56")
 */
function formatPrice(float $value): string
{
    return number_format($value, 2, '.', ',');
}
