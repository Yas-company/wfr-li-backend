{{-- resources/views/payments/fail.blade.php --}}
@extends('layouts.app')

@section('title', 'Payment Failed')

@section('content')
<div class="container mx-auto mt-10">
    <div class="bg-white shadow-md rounded-lg p-8 text-center">
        <div class="text-red-500 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Payment Failed!</h1>
        <p class="text-gray-600 mb-6">
            Unfortunately, your payment could not be processed. Please try again or contact support.
        </p>
        <a href="{{ url('/') }}"
            class="inline-block px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg">
            Back to Dashboard
        </a>
    </div>
</div>
@endsection
