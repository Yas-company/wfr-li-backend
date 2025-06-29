<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpService
{
    private const OTP_EXPIRY_MINUTES = 5;
    private const OTP_LENGTH = 6;
    private const TEST_OTP = '123456'; // Fixed OTP for testing

    public function generateOtp(string $phone): string
    {
        // For testing, always return the fixed OTP
        $otp = self::TEST_OTP;

        $key = $this->getOtpKey($phone);

        // Store in cache with file driver
        Cache::store('file')->put($key, $otp, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        // Verify the OTP was stored
        $storedOtp = Cache::store('file')->get($key);

        Log::info('OTP generation attempt', [
            'phone' => $phone,
            'otp' => $otp,
            'key' => $key,
            'stored_otp' => $storedOtp,
            'cache_driver' => config('cache.default')
        ]);

        if ($storedOtp !== $otp) {
            Log::error('Failed to store OTP in cache', [
                'phone' => $phone,
                'otp' => $otp,
                'stored_otp' => $storedOtp
            ]);
        }

        return $otp;
    }

    public function verifyOtp(string $phone, string $otp): bool
    {
        $key = $this->getOtpKey($phone);
        $cachedOtp = Cache::store('file')->get($key);

        Log::info('OTP verification attempt', [
            'phone' => $phone,
            'provided_otp' => $otp,
            'cached_otp' => $cachedOtp,
            'key' => $key,
            'cache_driver' => config('cache.default')
        ]);

        if (!$cachedOtp) {
            Log::warning('No OTP found in cache', [
                'phone' => $phone,
                'key' => $key,
                'cache_driver' => config('cache.default')
            ]);
            return false;
        }

        if ($cachedOtp === $otp) {
            // Instead of removing the OTP, store a verification status
            $verificationKey = $this->getVerificationKey($phone);
            Cache::store('file')->put($verificationKey, true, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

            Log::info('OTP verified successfully', [
                'phone' => $phone,
                'key' => $key,
                'verification_key' => $verificationKey
            ]);
            return true;
        }

        Log::warning('OTP mismatch', [
            'phone' => $phone,
            'provided_otp' => $otp,
            'cached_otp' => $cachedOtp,
            'key' => $key
        ]);
        return false;
    }

    public function isVerified(string $phone): bool
    {
        $verificationKey = $this->getVerificationKey($phone);
        $isVerified = Cache::store('file')->get($verificationKey, false);

        Log::info('Checking verification status', [
            'phone' => $phone,
            'verification_key' => $verificationKey,
            'is_verified' => $isVerified,
            'cache_driver' => config('cache.default'),
            'cache_path' => storage_path('framework/cache/data'),
            'all_cache_keys' => Cache::store('file')->get('*')
        ]);

        if (!$isVerified) {
            Log::warning('Phone number not verified', [
                'phone' => $phone,
                'verification_key' => $verificationKey,
                'cache_driver' => config('cache.default')
            ]);
        }

        return $isVerified;
    }

    public function clearVerification(string $phone): void
    {
        $key = $this->getOtpKey($phone);
        $verificationKey = $this->getVerificationKey($phone);

        Cache::store('file')->forget($key);
        Cache::store('file')->forget($verificationKey);

        Log::info('Cleared OTP and verification', [
            'phone' => $phone,
            'key' => $key,
            'verification_key' => $verificationKey
        ]);
    }

    private function getOtpKey(string $phone): string
    {
        return 'otp_' . $phone;
    }

    private function getVerificationKey(string $phone): string
    {
        return 'verified_' . $phone;
    }
}
