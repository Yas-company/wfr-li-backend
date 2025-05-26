<?php

return [
    // General Messages
    'success' => 'Operation completed successfully',
    'error' => 'An error occurred',
    'created' => 'Created successfully',
    'updated' => 'Updated successfully',
    'deleted' => 'Deleted successfully',
    'not_found' => 'Data not found',
    'unauthorized' => 'Unauthorized',
    'forbidden' => 'Forbidden',
    'validation_failed' => 'Validation failed',
    'server_error' => 'Server error',

    // Cart Messages
    'cart' => [
        'added' => 'Product added to cart successfully',
        'removed' => 'Product removed from cart successfully',
        'updated' => 'Cart quantity updated successfully',
        'empty' => 'Cart is empty',
        'not_found' => 'Cart not found',
        'item_not_found' => 'Cart item not found',
        'invalid_quantity' => 'Invalid quantity',
        'product_not_found' => 'Product not found',
        'insufficient_stock' => 'Insufficient stock',
    ],

    // Auth Messages
    'registration_successful' => 'Registration successful',
    'registration_failed' => 'Registration failed',
    'registration_verified' => 'Registration verified successfully',
    'login_successful' => 'Login successful',
    'login_failed' => 'Login failed',
    'logout_successful' => 'Logout successful',
    'logout_failed' => 'Logout failed',
    'profile_failed' => 'Failed to get profile',
    'account_not_verified' => 'Your account is not verified. Please verify your phone number first',
    'invalid_credentials' => 'Invalid credentials',

    // Password Change Messages
    'password_changed_successful' => 'Password changed successfully',
    'password_change_failed' => 'Password change failed',
    'invalid_current_password' => 'Current password is incorrect',

    // OTP Messages
    'otp_sent' => 'Verification code sent successfully',
    'otp_send_failed' => 'Failed to send verification code',
    'otp_verified' => 'Verification code verified successfully',
    'otp_verification_failed' => 'Failed to verify code',
    'invalid_otp' => 'Invalid or expired verification code',
    'otp_expired' => 'Verification code expired',
    'registration_verified' => 'Registration verified successfully',

    // Password Reset Messages
    'password_reset_successful' => 'Password reset successful',
    'password_reset_failed' => 'Password reset failed',

    // Validation Messages
    'validation' => [
        'required' => [
            'name' => 'Name is required',
            'phone' => 'Phone number is required',
            'country_code' => 'Country code is required',
            'address' => 'Please enter address',
            'location' => 'Please enter location',
            'business_name' => 'Please enter business name',
            'lic_id' => 'Please enter license ID',
            'email' => 'Email is required',
            'password' => 'Password is required',
            'otp' => 'Please enter verification code',
            'current_password' => 'Please enter current password',
            'password_confirmation' => 'Please confirm password',
            'product_id' => 'Product ID is required',
            'quantity' => 'Quantity is required',
        ],
        'unique' => [
            'phone' => 'Phone number already registered',
            'email' => 'Email already registered',
            'lic_id' => 'License ID already registered',
        ],
        'email' => 'Please enter a valid email',
        'password' => [
            'min' => 'Password must be at least 8 characters',
            'confirmed' => 'Password confirmation does not match',
            'mixed_case' => 'Password must contain both uppercase and lowercase letters',
            'numbers' => 'Password must contain numbers',
            'symbols' => 'Password must contain symbols',
        ],
        'size' => [
            'otp' => 'Verification code must be 6 digits'
        ],
        'exists' => [
            'phone' => 'Phone number not registered',
            'product' => 'Product not found',
        ],
        'integer' => [
            'quantity' => 'Quantity must be a number',
        ],
        'min' => [
            'quantity' => 'Quantity must be at least 1',
        ],
    ],

    // Favorites Messages
    'added_to_favorites' => 'Added to favorites',
    'removed_from_favorites' => 'Removed from favorites',

    // Account Deletion Messages
    'account_deleted_successfully' => 'Account deleted successfully',
    'account_delete_failed' => 'Failed to delete account',
]; 