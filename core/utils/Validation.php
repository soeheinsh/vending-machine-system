<?php
class Validation {
    
    public static function validateProduct($data) {
        $errors = [];
        
        // Validate name
        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Product name is required.';
        }
        
        // Validate price
        $price = floatval($data['price'] ?? 0);
        if ($price <= 0) {
            $errors['price'] = 'Price must be a positive value.';
        }
        
        // Validate quantity
        $quantity = intval($data['quantity_available'] ?? 0);
        if ($quantity < 0) {
            $errors['quantity_available'] = 'Quantity available must be non-negative.';
        }
        
        return $errors;
    }
    
    public static function validatePurchase($data) {
        $errors = [];
        
        $productId = intval($data['product_id'] ?? 0);
        $quantity = intval($data['quantity'] ?? 1);
        
        if ($productId <= 0) {
            $errors['product_id'] = 'Valid product ID is required.';
        }
        
        if ($quantity <= 0) {
            $errors['quantity'] = 'Quantity must be a positive value.';
        }
        
        return $errors;
    }
    
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)));
    }
}