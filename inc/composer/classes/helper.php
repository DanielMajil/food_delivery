<?php
class Helper {
    // Function to validate a credit card number (a simple example)
    public static function validateCreditCard($cardNumber) {
        // Implement your credit card validation logic here
        // For security reasons, you should use a library like Stripe for handling payments
        // This is just a placeholder function
        return strlen($cardNumber) === 16; // For demonstration purposes, we assume a valid card has 16 digits
    }

    // Function to sanitize user input to prevent SQL injection
    public static function sanitizeInput($input) {
        // Implement input sanitization logic here
        // Use prepared statements and parameterized queries for database interactions
        // This is just a placeholder function
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    // Function to generate a random order ID (a simple example)
    public static function generateOrderID() {
        // Implement your order ID generation logic here
        // This is just a placeholder function
        return 'ORD_' . mt_rand(1000, 9999); // For demonstration purposes, a random order ID is generated
    }
}
?>
