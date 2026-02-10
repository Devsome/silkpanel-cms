@extends('layouts.app')

@section('title', 'Upload Template - SilkPanel CMS')

@section('content')
<div class="px-4 sm:px-0">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('templates.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Upload Template</h1>
        </div>
        <p class="text-sm text-gray-600">
            Upload a ZIP file containing your custom template views.
        </p>
    </div>

    <!-- Upload Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <form action="{{ route('templates.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Template Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Template Name
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    required
                    value="{{ old('name') }}"
                    pattern="^[a-zA-Z0-9_-]+$"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm @error('name') border-red-300 @enderror"
                    placeholder="my-custom-template"
                >
                <p class="mt-1 text-sm text-gray-500">
                    Only letters, numbers, hyphens, and underscores are allowed.
                </p>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Template File -->
            <div>
                <label for="template_file" class="block text-sm font-medium text-gray-700 mb-2">
                    Template ZIP File
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="template_file" class="relative cursor-pointer bg-white rounded-md font-medium text-gray-900 hover:text-gray-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-gray-900">
                                <span>Upload a file</span>
                                <input 
                                    id="template_file" 
                                    name="template_file" 
                                    type="file" 
                                    accept=".zip"
                                    required
                                    class="sr-only"
                                    onchange="document.getElementById('file-name').textContent = this.files[0].name"
                                >
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">ZIP file up to 50MB</p>
                        <p id="file-name" class="text-sm text-gray-700 font-medium mt-2"></p>
                    </div>
                </div>
                @error('template_file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Template Structure Info -->
            <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Template Structure</h3>
                <p class="text-sm text-gray-600 mb-3">
                    Your ZIP file should contain a <code class="bg-white px-1.5 py-0.5 rounded text-xs">views/</code> directory with your Blade templates:
                </p>
                <pre class="text-xs text-gray-700 bg-white border border-gray-200 rounded p-3 overflow-x-auto"><code>my-template.zip
└── views/
    ├── welcome.blade.php
    ├── layouts/
    │   └── app.blade.php
    └── components/
        └── header.blade.php</code></pre>
                <p class="text-sm text-gray-600 mt-3">
                    Files in your template will override the default views when the template is active.
                </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('templates.index') }}" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                    Upload Template
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
