@extends('layouts.app')

@section('title', $page->getTranslation('title', 'ar') . ' - تزود')
@section('body-class', 'bg-gray-50')

@section('content')
    <main class="pt-32 pb-16">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">
                    {{ $page->getTranslation('title', 'ar') }}
                </h1>
                <div class="prose prose-lg max-w-none">
                    {!! $page->getTranslation('content', 'ar') !!}
                </div>
            </div>
        </div>
    </main>
@endsection 