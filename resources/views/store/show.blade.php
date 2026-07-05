<x-store-layout :title="$product->name" :meta-description="$product->shortDescriptionText()" :og-image="$product->images->first()?->url()">
    <div class="mx-auto max-w-6xl px-4 py-10">
        {{-- Breadcrumb --}}
        <nav class="mb-6 flex items-center gap-2 text-[13px] text-muted">
            <a href="{{ route('home') }}" class="hover:text-accent">Home</a>
            <span class="icon text-[10px]" data-icon="chevron-right"></span>
            <a href="{{ route('store.products') }}" class="hover:text-accent">Products</a>
            @if($product->category)
                <span class="icon text-[10px]" data-icon="chevron-right"></span>
                <a href="{{ route('store.products', ['category' => $product->category->slug]) }}" class="hover:text-accent">{{ $product->category->name }}</a>
            @endif
            <span class="icon text-[10px]" data-icon="chevron-right"></span>
            <span class="font-semibold text-text">{{ $product->name }}</span>
        </nav>

        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            {{-- Left: gallery + tabs --}}
            <div>
                {{-- Gallery --}}
                <div x-data="{ active: 0 }" class="mb-8">
                    <div class="relative mb-3 aspect-[16/9] overflow-hidden rounded-lg border" style="border-color:var(--border);background:var(--bg-input);">
                        @forelse($product->images as $index => $image)
                            <div x-show="active === {{ $index }}" class="absolute inset-0" @if($index > 0) style="display:none" @endif>
                                <img src="{{ $image->url() }}" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full scale-110 object-cover opacity-40 blur-2xl">
                                <img src="{{ $image->url() }}" alt="{{ $product->name }} screenshot {{ $index + 1 }}" @if($index > 0) loading="lazy" @endif class="relative h-full w-full object-contain">
                            </div>
                        @empty
                            <div class="flex h-full w-full items-center justify-center text-5xl text-muted"><span class="icon" data-icon="boxes"></span></div>
                        @endforelse
                    </div>
                    @if($product->images->count() > 1)
                        <div class="flex gap-2 overflow-x-auto pb-1">
                            @foreach($product->images as $index => $image)
                                <button type="button" @click="active = {{ $index }}"
                                        :class="active === {{ $index }} ? 'ring-2 ring-[var(--accent)]' : 'opacity-70'"
                                        class="h-16 w-24 shrink-0 overflow-hidden rounded-sm border transition-all duration-300" style="border-color:var(--border);">
                                    <img src="{{ $image->url() }}" alt="Thumbnail {{ $index + 1 }}" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Title (mobile shows above card) --}}
                <h1 class="mb-2 font-heading text-3xl font-extrabold text-text">{{ $product->name }}</h1>
                @if($reviews->isNotEmpty())
                    <div class="mb-2 flex items-center gap-2 text-[13px] text-muted">
                        @include('partials.store.stars', ['rating' => $averageRating])
                        <span class="font-semibold text-text">{{ $averageRating }}</span>
                        <span>({{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }})</span>
                    </div>
                @endif
                <p class="mb-6 text-[15px] leading-7 text-muted">{{ $product->shortDescriptionText() }}</p>

                {{-- Tabs --}}
                <div x-data="{ tab: 'description' }">
                    <div class="mb-5 flex gap-1 border-b" style="border-color:var(--border);">
                        <button type="button" @click="tab = 'description'" :class="tab === 'description' ? 'border-[var(--accent)] text-accent' : 'border-transparent text-muted'" class="border-b-2 px-4 py-2.5 text-sm font-semibold transition-colors duration-300">Description</button>
                        <button type="button" @click="tab = 'features'" :class="tab === 'features' ? 'border-[var(--accent)] text-accent' : 'border-transparent text-muted'" class="border-b-2 px-4 py-2.5 text-sm font-semibold transition-colors duration-300">Features</button>
                        <button type="button" @click="tab = 'changelog'" :class="tab === 'changelog' ? 'border-[var(--accent)] text-accent' : 'border-transparent text-muted'" class="border-b-2 px-4 py-2.5 text-sm font-semibold transition-colors duration-300">Changelog</button>
                        <button type="button" @click="tab = 'reviews'" :class="tab === 'reviews' ? 'border-[var(--accent)] text-accent' : 'border-transparent text-muted'" class="border-b-2 px-4 py-2.5 text-sm font-semibold transition-colors duration-300">Reviews ({{ $reviews->count() }})</button>
                    </div>

                    <div x-show="tab === 'description'" class="rich-text">
                        @if($product->description)
                            {!! $product->descriptionHtml() !!}
                        @else
                            No description yet.
                        @endif
                    </div>

                    <div x-show="tab === 'features'">
                        @if($product->featureList())
                            <ul class="space-y-2">
                                @foreach($product->featureList() as $feature)
                                    <li class="flex items-start gap-2 text-[14px] text-text"><span class="icon mt-1 text-accent" data-icon="check"></span>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-[14px] text-muted">Feature list coming soon.</p>
                        @endif
                    </div>

                    <div x-show="tab === 'changelog'">
                        @forelse($product->releases as $release)
                            <div class="mb-4 rounded-lg border p-4" style="border-color:var(--border);background:var(--bg-card);">
                                <div class="mb-1 flex items-center gap-3">
                                    <span class="rounded-full bg-accent/10 px-2.5 py-0.5 text-[12px] font-bold text-accent">v{{ $release->version }}</span>
                                    <span class="text-[12px] text-muted">{{ $release->released_at?->format('d M Y') }}</span>
                                </div>
                                @if($release->notes)
                                    <div class="rich-text text-[13px] leading-6">{!! $release->notesHtml() !!}</div>
                                @else
                                    <p class="text-[13px] leading-6 text-text">Maintenance release.</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-[14px] text-muted">No releases published yet.</p>
                        @endforelse
                    </div>

                    <div x-show="tab === 'reviews'">
                        {{-- Write a review --}}
                        @if($ownReview)
                            <div class="mb-5 rounded-lg border p-4 text-[13px]" style="border-color:var(--border);background:var(--bg-card);">
                                @if($ownReview->isPending())
                                    <span class="font-semibold text-text">Your review is awaiting approval.</span>
                                @else
                                    <span class="font-semibold text-text">You reviewed this product.</span>
                                    @include('partials.store.stars', ['rating' => $ownReview->rating])
                                @endif
                            </div>
                        @elseif($canReview)
                            <form method="POST" action="{{ route('store.reviews.store', $product->slug) }}" class="mb-6 rounded-lg border p-5" style="border-color:var(--border);background:var(--bg-card);">
                                @csrf
                                <h5 class="mb-3 text-sm font-bold text-text">Write a Review</h5>
                                <div x-data="{ rating: {{ (int) old('rating', 5) }} }" class="mb-3">
                                    <input type="hidden" name="rating" :value="rating">
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" @click="rating = {{ $i }}" class="text-2xl leading-none transition-colors duration-150"
                                                    :style="rating >= {{ $i }} ? 'color:#f59e0b;' : 'color:var(--border);'"
                                                    aria-label="Rate {{ $i }} {{ Str::plural('star', $i) }}">&#9733;</button>
                                        @endfor
                                    </div>
                                    <x-input-error :messages="$errors->get('rating')" class="mt-2" />
                                </div>
                                <textarea class="panel-textarea" name="body" rows="4" placeholder="How is {{ $product->name }} working for you?" required>{{ old('body') }}</textarea>
                                <x-input-error :messages="$errors->get('body')" class="mt-2" />
                                <button type="submit" class="mt-3 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Submit Review</button>
                                <p class="mt-2 text-[12px] text-muted">Reviews are published after moderation.</p>
                            </form>
                        @elseif(! auth()->check())
                            <p class="mb-5 text-[13px] text-muted">Purchased this product? <a href="{{ route('login') }}" class="font-semibold text-accent">Log in</a> to write a review.</p>
                        @else
                            <p class="mb-5 text-[13px] text-muted">Only verified buyers can review this product.</p>
                        @endif

                        {{-- Approved reviews --}}
                        @forelse($reviews as $review)
                            <div class="mb-4 rounded-lg border p-4" style="border-color:var(--border);background:var(--bg-card);">
                                <div class="mb-2 flex flex-wrap items-center gap-2">
                                    @include('partials.store.stars', ['rating' => $review->rating])
                                    <span class="text-[13px] font-bold text-text">{{ $review->user->name }}</span>
                                    <span class="rounded-full bg-accent/10 px-2 py-0.5 text-[11px] font-bold text-accent">Verified buyer</span>
                                    <span class="text-[12px] text-muted">{{ $review->created_at->format('d M Y') }}</span>
                                </div>
                                <p class="whitespace-pre-line text-[13px] leading-6 text-text">{{ $review->body }}</p>
                            </div>
                        @empty
                            <p class="text-[14px] text-muted">No reviews yet — be the first!</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right: purchase card --}}
            <aside>
                <div class="sticky top-24 space-y-4">
                    <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                        <div class="mb-4 flex items-end gap-2">
                            <span class="font-heading text-3xl font-extrabold text-text">{{ format_price($product->effectivePrice()) }}</span>
                            @if($product->isOnSale())
                                <span class="mb-1 text-[15px] font-semibold text-muted line-through">{{ format_price($product->price) }}</span>
                                <span class="mb-1 rounded-full bg-danger px-2 py-0.5 text-[11px] font-bold text-white">SALE</span>
                            @endif
                        </div>
                        <p class="mb-4 text-[12px] leading-5 text-muted">One-time payment · license key by email · lifetime re-downloads of every version.</p>

                        <a href="{{ route('store.checkout', $product->slug) }}" class="mb-3 flex w-full items-center justify-center gap-2 rounded-lg bg-accent px-4 py-3 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
                            <span class="icon" data-icon="shopping-cart"></span> Buy Now
                        </a>

                        @if($product->demo_url)
                            <a href="{{ $product->demo_url }}" target="_blank" rel="noopener" class="block w-full rounded-lg border px-4 py-3 text-center text-sm font-semibold text-text transition-colors duration-300 hover:border-accent hover:text-accent" style="border-color:var(--border);">
                                Live Demo
                            </a>
                        @endif
                    </div>

                    <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                        <h5 class="mb-3 text-sm font-bold text-text">Product Details</h5>
                        <dl class="space-y-2.5 text-[13px]">
                            @if($product->category)
                                <div class="flex justify-between"><dt class="text-muted">Category</dt><dd class="font-semibold text-text">{{ $product->category->name }}</dd></div>
                            @endif
                            @if($product->latestRelease())
                                <div class="flex justify-between"><dt class="text-muted">Latest version</dt><dd class="font-semibold text-text">v{{ $product->latestRelease()->version }}</dd></div>
                                <div class="flex justify-between"><dt class="text-muted">Last update</dt><dd class="font-semibold text-text">{{ $product->latestRelease()->released_at?->format('d M Y') }}</dd></div>
                            @endif
                            <div class="flex justify-between"><dt class="text-muted">Published</dt><dd class="font-semibold text-text">{{ $product->created_at->format('d M Y') }}</dd></div>
                        </dl>

                        @if($product->requirementList())
                            <h5 class="mb-2 mt-5 text-sm font-bold text-text">Requirements</h5>
                            <ul class="space-y-1.5">
                                @foreach($product->requirementList() as $requirement)
                                    <li class="flex items-start gap-2 text-[13px] text-muted"><span class="icon mt-0.5 text-accent" data-icon="check"></span>{{ $requirement }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </aside>
        </div>

        {{-- Related products --}}
        @if($related->isNotEmpty())
            <div class="mt-14">
                <h2 class="mb-6 font-heading text-2xl font-extrabold text-text">You May Also Like</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($related as $product)
                        @include('partials.store.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-store-layout>
