@extends('layouts.app')

@section('title', $category->getTranslation('name', 'ar') . ' - تزود')
@section('body-class', 'bg-gray-50')

@section('content')
    <section class="py-12">
        <div class="container mx-auto px-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">
                {{ $category->getTranslation('name', 'ar') }}
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                        <div class="h-48 overflow-hidden">
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->getTranslation('name', 'ar') }}"
                                 class="w-full h-full object-cover" />
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                {{ $product->getTranslation('name', 'ar') }}
                            </h3>
                            <p class="text-sm text-gray-600 mb-4">
                                {{ $product->getTranslation('description', 'ar') }}
                            </p>
                            <div class="flex items-center justify-between">
                                @if($product->price_before_discount && $product->price_before_discount > $product->price)
                                    <span class="text-gray-400 line-through text-base mr-2">
                                        {{ number_format($product->price_before_discount, 2) }} ريال
                                    </span>
                                @endif
                                <span class="text-primary font-bold text-lg">
                                    {{ number_format($product->price, 2) }} ريال
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </section>
@endsection 