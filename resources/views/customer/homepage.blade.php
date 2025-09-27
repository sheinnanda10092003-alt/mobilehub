<!-- resources/views/customer/home.blade.php -->

@extends('customer.layouts.customer')  {{-- Extend customer layout --}}

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Buy Phones')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold">Find Your Perfect Mobile Phone</h1>
                <p class="lead">Discover the latest smartphones from top brands at unbeatable prices.</p>
                
                <!-- Search Bar -->
                <div class="search-container mb-4">
                    <form method="GET" action="{{ route('home') }}" class="position-relative">
                        <div class="input-group input-group-lg">
                            <input type="text" 
                                   name="search" 
                                   id="searchInput"
                                   class="form-control" 
                                   placeholder="Search for phones, brands, colors, RAM, storage..." 
                                   value="{{ $search ?? '' }}"
                                   autocomplete="off">
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        
                        <!-- Search Suggestions Dropdown -->
                        <div id="searchSuggestions" class="search-suggestions position-absolute w-100" style="display: none; z-index: 1000;">
                            <div class="list-group shadow">
                                <!-- Suggestions will be populated here -->
                            </div>
                        </div>
                    </form>
                </div>
                
                @guest('web')
                    <a href="{{ route('customer.signup') }}" class="btn btn-light btn-lg customer-btn me-3">
                        <i class="fas fa-user-plus"></i> Join Now
                    </a>
                    <a href="{{ route('customer.login') }}" class="btn btn-outline-light btn-lg customer-btn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                @else
                    <div class="alert alert-light d-inline-block">
                        <i class="fas fa-user-check"></i> Welcome back, <strong>{{ auth('web')->user()->Name }}</strong>!
                    </div>
                @endguest
            </div>
            <div class="col-lg-4 text-center">
                <i class="fas fa-mobile-alt" style="font-size: 8rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section id="products" class="py-5">
    <div class="container">
        <!-- Search Results Header -->
        @if($search)
            <div class="text-center mb-4">
                <div class="search-results-header">
                    <h2 class="fw-bold mb-2">Search Results</h2>
                    <div class="search-query-display mb-3">
                        <span class="text-muted">Searching for:</span>
                        <span class="badge bg-primary fs-6 mx-2">{{ $search }}</span>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm ms-2">
                            <i class="fas fa-times"></i> Clear Search
                        </a>
                    </div>
                    @if($searchStats)
                        <div class="search-stats">
                            @if($searchStats['found'] > 0)
                                <span class="badge bg-success fs-6">
                                    Found {{ $searchStats['found'] }} of {{ $searchStats['total'] }} products
                                </span>
                            @else
                                <span class="badge bg-danger fs-6">
                                    No products found out of {{ $searchStats['total'] }} total products
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center mb-5">
                <h2 class="fw-bold">Latest Mobile Phones</h2>
                <p class="text-muted">Choose from our premium collection of smartphones</p>
                @if($phones->count() > 0)
                    <div class="mb-3">
                        <span class="badge bg-primary fs-6">
                            Showing {{ $phones->firstItem() }}-{{ $phones->lastItem() }} of {{ $phones->total() }} products
                        </span>
                    </div>
                @endif
            </div>
        @endif

        <div class="row">
            @forelse ($phones as $phone)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 product-card shadow-sm border-0">
                    <div class="product-image-container">
                        @if($phone->sanitized_image_url)
                            <img src="{{ $phone->sanitized_image_url }}" 
                                 class="product-image" 
                                 alt="{{ $phone->Name }}" 
                                 loading="lazy"
                                 decoding="async"
                                 onerror="this.style.display='none'; this.parentElement.classList.add('error');">
                            <div class="image-fallback">
                                <div class="fallback-content">
                                    <i class="fas fa-mobile-alt"></i>
                                    <span class="product-name">{{ $phone->Name }}</span>
                                    <small>Image not available</small>
                                </div>
                            </div>
                        @else
                            <div class="image-fallback active">
                                <div class="fallback-content">
                                    <i class="fas fa-mobile-alt"></i>
                                    <span class="product-name">{{ $phone->Name }}</span>
                                    <small>No image available</small>
                                </div>
                            </div>
                        @endif
                        <div class="loading-placeholder">
                            <div class="loading-spinner"></div>
                        </div>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary">{{ $phone->Name }}</h5>
                        <p class="card-text text-muted flex-grow-1">{{ \Illuminate\Support\Str::limit($phone->Description, 100) }}</p>
                        
                        @if($phone->hasVariants())
                            <!-- Variant Selection -->
                            <div class="variant-selection mb-3">
                                <div class="variant-options" data-product-id="{{ $phone->ProductID }}">
                                    <!-- Color Options -->
                                    @if($phone->variants->pluck('Color')->unique()->count() > 1)
                                        <div class="mb-2">
                                            <label class="form-label fw-bold">Color:</label>
                                            <div class="color-options d-flex flex-wrap gap-1">
                                                @foreach($phone->variants->pluck('Color')->unique() as $color)
                                                    <button type="button" class="btn btn-outline-secondary btn-sm color-option" 
                                                            data-color="{{ $color }}" data-product="{{ $phone->ProductID }}">
                                                        {{ $color }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- RAM Options -->
                                    @if($phone->variants->pluck('RAM')->unique()->count() > 1)
                                        <div class="mb-2">
                                            <label class="form-label fw-bold">RAM:</label>
                                            <div class="ram-options d-flex flex-wrap gap-1">
                                                @foreach($phone->variants->pluck('RAM')->unique()->sort() as $ram)
                                                    <button type="button" class="btn btn-outline-info btn-sm ram-option" 
                                                            data-ram="{{ $ram }}" data-product="{{ $phone->ProductID }}">
                                                        {{ $ram }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Storage Options -->
                                    @if($phone->variants->pluck('Storage')->unique()->count() > 1)
                                        <div class="mb-2">
                                            <label class="form-label fw-bold">Storage:</label>
                                            <div class="storage-options d-flex flex-wrap gap-1">
                                                @foreach($phone->variants->pluck('Storage')->unique()->sort() as $storage)
                                                    <button type="button" class="btn btn-outline-warning btn-sm storage-option" 
                                                            data-storage="{{ $storage }}" data-product="{{ $phone->ProductID }}">
                                                        {{ $storage }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            @if($phone->hasVariants())
                                <div class="price-display">
                                    <h4 class="text-success mb-0 selected-price" data-product="{{ $phone->ProductID }}">
                                        @if($phone->min_price == $phone->max_price)
                                            ${{ number_format($phone->min_price, 2) }}
                                        @else
                                            ${{ number_format($phone->min_price, 2) }} - ${{ number_format($phone->max_price, 2) }}
                                        @endif
                                    </h4>
                                    <small class="text-muted variant-info" data-product="{{ $phone->ProductID }}">
                                        Select options above
                                    </small>
                                </div>
                                <div class="stock-display" data-product="{{ $phone->ProductID }}">
                                    @if($phone->total_stock > 0)
                                        <small class="text-success"><i class="fas fa-check-circle"></i> In Stock</small>
                                    @else
                                        <small class="text-danger"><i class="fas fa-times-circle"></i> Out of Stock</small>
                                    @endif
                                </div>
                            @else
                                <h4 class="text-success mb-0">${{ number_format($phone->Price, 2) }}</h4>
                                @if($phone->Stock > 0)
                                    <small class="text-success"><i class="fas fa-check-circle"></i> In Stock</small>
                                @else
                                    <small class="text-danger"><i class="fas fa-times-circle"></i> Out of Stock</small>
                                @endif
                            @endif
                        </div>
                        
                        @if($phone->hasVariants() ? $phone->total_stock > 0 : $phone->Stock > 0)
                            @auth('web')
                                @if($phone->hasVariants())
                                    <!-- Variant-based cart form -->
                                    <form method="POST" action="{{ route('cart.add.variant') }}" class="mt-3 variant-cart-form" data-product="{{ $phone->ProductID }}">
                                        @csrf
                                        <input type="hidden" name="variant_id" class="selected-variant-id" value="">
                                        <div class="row g-2 mb-2">
                                            <div class="col-4">
                                                <input type="number" 
                                                       class="form-control form-control-sm quantity-input" 
                                                       name="quantity" 
                                                       value="1" 
                                                       min="1" 
                                                       max="1" 
                                                       data-product="{{ $phone->ProductID }}">
                                            </div>
                                            <div class="col-8">
                                                <button class="btn btn-primary w-100 customer-btn add-to-cart-btn" 
                                                        type="submit" 
                                                        data-product-id="{{ $phone->ProductID }}"
                                                        disabled>
                                                    <span class="btn-text">
                                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <!-- Regular product cart form -->
                                    <form method="POST" action="{{ route('cart.add', $phone->ProductID) }}" class="mt-3">
                                        @csrf
                                        <div class="row g-2 mb-2">
                                            <div class="col-4">
                                                <input type="number" 
                                                       class="form-control form-control-sm" 
                                                       name="quantity" 
                                                       value="1" 
                                                       min="1" 
                                                       max="{{ $phone->Stock }}">
                                            </div>
                                            <div class="col-8">
                                                <button class="btn btn-primary w-100 customer-btn add-to-cart-btn" 
                                                        type="submit" 
                                                        data-product-id="{{ $phone->ProductID }}">
                                                    <span class="btn-text">
                                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('customer.login') }}" class="btn btn-primary w-100 customer-btn mt-3">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login to Buy
                                </a>
                            @endauth
                        @else
                            <button class="btn btn-outline-secondary w-100 mt-3" disabled>
                                <i class="fas fa-ban"></i> Out of Stock
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <div class="py-5">
                    @if($search)
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <h3 class="text-muted">No Products Found</h3>
                        <p class="text-muted mb-4">Sorry, we couldn't find any products matching "<strong>{{ $search }}</strong>".</p>
                        <div class="search-suggestions-text">
                            <h5 class="text-muted mb-3">Try searching for:</h5>
                            <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
                                <a href="{{ route('home', ['search' => 'iPhone']) }}" class="badge bg-light text-dark text-decoration-none">iPhone</a>
                                <a href="{{ route('home', ['search' => 'Samsung']) }}" class="badge bg-light text-dark text-decoration-none">Samsung</a>
                                <a href="{{ route('home', ['search' => 'Android']) }}" class="badge bg-light text-dark text-decoration-none">Android</a>
                                <a href="{{ route('home', ['search' => '128GB']) }}" class="badge bg-light text-dark text-decoration-none">128GB</a>
                                <a href="{{ route('home', ['search' => '6GB RAM']) }}" class="badge bg-light text-dark text-decoration-none">6GB RAM</a>
                                <a href="{{ route('home', ['search' => 'Black']) }}" class="badge bg-light text-dark text-decoration-none">Black</a>
                            </div>
                        </div>
                        <a href="{{ route('home') }}" class="btn btn-primary customer-btn">
                            <i class="fas fa-arrow-left me-2"></i>View All Products
                        </a>
                    @else
                        <i class="fas fa-mobile-alt fa-4x text-muted mb-3"></i>
                        <h3 class="text-muted">No Products Available</h3>
                        <p class="text-muted">Please check back later for new arrivals.</p>
                    @endif
                </div>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($phones->hasPages())
            <div class="row">
                <div class="col-12">
                    <nav aria-label="Product pagination" class="mt-5">
                        <div class="d-flex justify-content-center">
                            {{ $phones->links('pagination::bootstrap-4') }}
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Page {{ $phones->currentPage() }} of {{ $phones->lastPage() }} 
                                ({{ $phones->total() }} total products)
                            </small>
                        </div>
                    </nav>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Search Suggestions CSS -->
<style>
.search-suggestions {
    top: 100%;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    max-height: 300px;
    overflow-y: auto;
}

.search-suggestions .list-group-item {
    border: none;
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: background-color 0.15s ease-in-out;
}

.search-suggestions .list-group-item:hover {
    background-color: #f8f9fa;
}

.search-suggestions .list-group-item.active {
    background-color: #007bff;
    color: white;
}

.suggestion-icon {
    width: 20px;
    text-align: center;
    margin-right: 10px;
}

.suggestion-image {
    width: 30px;
    height: 30px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 10px;
}

.search-results-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.search-query-display {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .search-query-display {
        flex-direction: column;
        gap: 0.75rem;
    }
}

/* Search suggestion badges hover effect */
.search-suggestions-text .badge:hover {
    background-color: #007bff !important;
    color: white !important;
    transform: translateY(-1px);
    transition: all 0.2s ease;
}
</style>

<!-- Include variants data for JavaScript -->
<script>
const productVariants = {
@foreach($phones as $phone)
    '{{ $phone->ProductID }}': [
    @foreach($phone->variants as $variant)
        {
            'VariantID': {{ $variant->VariantID }},
            'Color': '{{ $variant->Color }}',
            'RAM': '{{ $variant->RAM }}',
            'Storage': '{{ $variant->Storage }}',
            'Price': {{ $variant->Price }},
            'Stock': {{ $variant->Stock }},
            'SKU': '{{ $variant->SKU }}',
            'ImageURL': '{{ $variant->image_url ?: $phone->sanitized_image_url }}'
        }@if(!$loop->last),@endif
    @endforeach
    ]@if(!$loop->last),@endif
@endforeach
};

// Create color-to-image mapping for each product
const productColorImages = {
@foreach($phones as $phone)
    '{{ $phone->ProductID }}': {
    @foreach($phone->variants->groupBy('Color') as $color => $colorVariants)
        '{{ $color }}': '{{ $colorVariants->first()->image_url ?: $phone->sanitized_image_url }}'@if(!$loop->last),@endif
    @endforeach
    }@if(!$loop->last),@endif
@endforeach
};
</script>

<script>
// Search suggestions functionality
let searchTimeout;
let currentSuggestionIndex = -1;

function initSearchSuggestions() {
    const searchInput = document.getElementById('searchInput');
    const suggestionsContainer = document.getElementById('searchSuggestions');
    const suggestionsListGroup = suggestionsContainer?.querySelector('.list-group');
    
    if (!searchInput || !suggestionsContainer || !suggestionsListGroup) return;
    
    // Handle input events
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        if (query.length < 2) {
            hideSuggestions();
            return;
        }
        
        // Debounce the search
        searchTimeout = setTimeout(() => {
            fetchSuggestions(query);
        }, 300);
    });
    
    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const suggestions = suggestionsListGroup.querySelectorAll('.list-group-item');
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                currentSuggestionIndex = Math.min(currentSuggestionIndex + 1, suggestions.length - 1);
                updateSuggestionHighlight(suggestions);
                break;
            case 'ArrowUp':
                e.preventDefault();
                currentSuggestionIndex = Math.max(currentSuggestionIndex - 1, -1);
                updateSuggestionHighlight(suggestions);
                break;
            case 'Enter':
                if (currentSuggestionIndex >= 0 && suggestions[currentSuggestionIndex]) {
                    e.preventDefault();
                    suggestions[currentSuggestionIndex].click();
                }
                break;
            case 'Escape':
                hideSuggestions();
                break;
        }
    });
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            hideSuggestions();
        }
    });
    
    // Show suggestions when focusing on search input
    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            fetchSuggestions(this.value.trim());
        }
    });
}

function fetchSuggestions(query) {
    fetch(`/api/search-suggestions?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(suggestions => {
            displaySuggestions(suggestions);
        })
        .catch(error => {
            console.error('Error fetching suggestions:', error);
            hideSuggestions();
        });
}

function displaySuggestions(suggestions) {
    const suggestionsListGroup = document.querySelector('#searchSuggestions .list-group');
    
    if (!suggestionsListGroup || suggestions.length === 0) {
        hideSuggestions();
        return;
    }
    
    suggestionsListGroup.innerHTML = '';
    
    suggestions.forEach((suggestion, index) => {
        const listItem = document.createElement('div');
        listItem.className = 'list-group-item list-group-item-action d-flex align-items-center';
        
        let icon = '';
        let content = '';
        
        switch (suggestion.type) {
            case 'product':
                if (suggestion.image) {
                    content = `<img src="${suggestion.image}" class="suggestion-image" alt="${suggestion.text}" onerror="this.style.display='none'"> ${suggestion.text}`;
                } else {
                    icon = '<i class="fas fa-mobile-alt suggestion-icon"></i>';
                    content = suggestion.text;
                }
                break;
            case 'brand':
                icon = '<i class="fas fa-tag suggestion-icon"></i>';
                content = suggestion.text;
                break;
            case 'color':
                icon = '<i class="fas fa-palette suggestion-icon"></i>';
                content = suggestion.text;
                break;
            case 'spec':
                icon = '<i class="fas fa-microchip suggestion-icon"></i>';
                content = suggestion.text;
                break;
            default:
                icon = '<i class="fas fa-search suggestion-icon"></i>';
                content = suggestion.text;
        }
        
        listItem.innerHTML = `${icon}${content}`;
        
        listItem.addEventListener('click', function() {
            document.getElementById('searchInput').value = suggestion.text;
            hideSuggestions();
            // Submit the form
            document.querySelector('.search-container form').submit();
        });
        
        suggestionsListGroup.appendChild(listItem);
    });
    
    currentSuggestionIndex = -1;
    showSuggestions();
}

function updateSuggestionHighlight(suggestions) {
    suggestions.forEach((item, index) => {
        if (index === currentSuggestionIndex) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

function showSuggestions() {
    const suggestionsContainer = document.getElementById('searchSuggestions');
    if (suggestionsContainer) {
        suggestionsContainer.style.display = 'block';
    }
}

function hideSuggestions() {
    const suggestionsContainer = document.getElementById('searchSuggestions');
    if (suggestionsContainer) {
        suggestionsContainer.style.display = 'none';
    }
    currentSuggestionIndex = -1;
}

// Variant selection functionality
function initVariantSelection() {
    // Handle variant option selection
    document.querySelectorAll('.color-option, .ram-option, .storage-option').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.product;
            const optionType = this.classList.contains('color-option') ? 'color' : 
                             this.classList.contains('ram-option') ? 'ram' : 'storage';
            
            // Remove active class from siblings
            this.parentElement.querySelectorAll('button').forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('btn-secondary', 'btn-info', 'btn-warning');
                btn.classList.add(btn.classList.contains('color-option') ? 'btn-outline-secondary' :
                                 btn.classList.contains('ram-option') ? 'btn-outline-info' : 'btn-outline-warning');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            this.classList.remove('btn-outline-secondary', 'btn-outline-info', 'btn-outline-warning');
            this.classList.add(this.classList.contains('color-option') ? 'btn-secondary' :
                              this.classList.contains('ram-option') ? 'btn-info' : 'btn-warning');
            
            updateVariantSelection(productId);
        });
    });
}

function updateVariantSelection(productId) {
    const colorBtn = document.querySelector(`.color-option.active[data-product="${productId}"]`);
    const ramBtn = document.querySelector(`.ram-option.active[data-product="${productId}"]`);
    const storageBtn = document.querySelector(`.storage-option.active[data-product="${productId}"]`);
    
    const selectedColor = colorBtn ? colorBtn.dataset.color : null;
    const selectedRam = ramBtn ? ramBtn.dataset.ram : null;
    const selectedStorage = storageBtn ? storageBtn.dataset.storage : null;
    
    // Find matching variant
    const variants = productVariants[productId] || [];
    const matchingVariant = variants.find(variant => {
        return (!selectedColor || variant.Color === selectedColor) &&
               (!selectedRam || variant.RAM === selectedRam) &&
               (!selectedStorage || variant.Storage === selectedStorage);
    });
    
    // Update UI based on selection
    updateVariantUI(productId, matchingVariant, selectedColor, selectedRam, selectedStorage);
}

function updateVariantUI(productId, variant, selectedColor, selectedRam, selectedStorage) {
    const priceElement = document.querySelector(`.selected-price[data-product="${productId}"]`);
    const stockElement = document.querySelector(`.stock-display[data-product="${productId}"]`);
    const variantInfoElement = document.querySelector(`.variant-info[data-product="${productId}"]`);
    const quantityInput = document.querySelector(`.quantity-input[data-product="${productId}"]`);
    const addToCartBtn = document.querySelector(`.add-to-cart-btn[data-product-id="${productId}"]`);
    const variantIdInput = document.querySelector(`.variant-cart-form[data-product="${productId}"] .selected-variant-id`);
    
    // Update product image based on selected color
    updateProductImage(productId, selectedColor);
    
    if (variant) {
        // Update price
        if (priceElement) {
            priceElement.innerHTML = `$${parseFloat(variant.Price).toFixed(2)}`;
        }
        
        // Update stock
        if (stockElement) {
            if (variant.Stock > 0) {
                stockElement.innerHTML = `<small class="text-success"><i class="fas fa-check-circle"></i> ${variant.Stock} in stock</small>`;
            } else {
                stockElement.innerHTML = `<small class="text-danger"><i class="fas fa-times-circle"></i> Out of Stock</small>`;
            }
        }
        
        // Update variant info
        if (variantInfoElement) {
            const variantDetails = [];
            if (selectedColor) variantDetails.push(selectedColor);
            if (selectedRam) variantDetails.push(selectedRam);
            if (selectedStorage) variantDetails.push(selectedStorage);
            variantInfoElement.textContent = variantDetails.join(', ') + ` (${variant.SKU})`;
        }
        
        // Update quantity input max
        if (quantityInput) {
            quantityInput.max = variant.Stock;
            if (parseInt(quantityInput.value) > variant.Stock) {
                quantityInput.value = Math.max(1, variant.Stock);
            }
        }
        
        // Update add to cart button
        if (addToCartBtn && variantIdInput) {
            variantIdInput.value = variant.VariantID;
            addToCartBtn.disabled = variant.Stock === 0;
        }
    } else {
        // No matching variant found - show selection needed
        if (variantInfoElement) {
            variantInfoElement.textContent = 'Please select all options';
        }
        
        if (addToCartBtn) {
            addToCartBtn.disabled = true;
        }
    }
}

// Function to update product image based on selected color
function updateProductImage(productId, selectedColor) {
    if (!selectedColor) return;
    
    const productCard = document.querySelector(`[data-product-id="${productId}"]`)?.closest('.product-card');
    if (!productCard) return;
    
    const productImage = productCard.querySelector('.product-image');
    if (!productImage) return;
    
    // Get the color-specific image URL
    const colorImages = productColorImages[productId];
    if (colorImages && colorImages[selectedColor]) {
        const newImageUrl = colorImages[selectedColor];
        
        // Only update if the image URL is different
        if (productImage.src !== newImageUrl) {
            // Add a subtle loading effect
            productImage.style.opacity = '0.7';
            productImage.style.transition = 'opacity 0.3s ease';
            
            // Update the image source
            productImage.src = newImageUrl;
            
            // Restore opacity when image loads
            productImage.onload = function() {
                this.style.opacity = '1';
            };
            
            // Handle error case
            productImage.onerror = function() {
                this.style.opacity = '1';
                console.warn(`Failed to load image for color ${selectedColor}:`, newImageUrl);
            };
        }
    }
}

// Enhanced mobile-friendly image loading
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search suggestions
    initSearchSuggestions();
    
    // Initialize variant selection
    initVariantSelection();
    const productCards = document.querySelectorAll('.product-image-container');
    
    productCards.forEach(function(container) {
        const img = container.querySelector('img');
        const fallback = container.querySelector('.image-fallback');
        
        if (img) {
            // Set initial state
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease';
            
            // Add loading placeholder while image loads
            container.classList.add('loading');
            
            // When image loads successfully
            img.onload = function() {
                container.classList.remove('loading');
                container.classList.add('loaded');
                this.style.opacity = '1';
                if (fallback) fallback.style.display = 'none';
            };
            
            // If image fails to load
            img.onerror = function() {
                console.log('Image failed to load:', this.src);
                container.classList.remove('loading');
                container.classList.add('error');
                this.style.display = 'none';
                if (fallback) {
                    fallback.style.display = 'flex';
                }
            };
            
            // Force check if image is already cached
            if (img.complete) {
                if (img.naturalWidth > 0) {
                    img.onload();
                } else {
                    img.onerror();
                }
            }
        }
    });
    
    // Add mobile touch enhancements
    if ('ontouchstart' in window) {
        document.querySelectorAll('.product-card').forEach(function(card) {
            card.addEventListener('touchstart', function() {
                this.classList.add('touched');
            });
            card.addEventListener('touchend', function() {
                setTimeout(() => this.classList.remove('touched'), 150);
            });
        });
    }
});
</script>
@endsection
