<!-- food section -->
@php
    $categories = App\Models\Category::where('status' , 1)->where('deleted_at',null)->get();
@endphp
<section class="food_section layout_padding-bottom">
    <div class="container" x-data="{ tab: 1 }">
        <div class="heading_container heading_center">
            <h2>
                منو محصولات
            </h2>
        </div>

        <ul class="filters_menu">
            @foreach($categories as $category)
                <li :class="tab === 1 ? 'active' : 'active'"
                    @click="tab = {{ $category->id }}">{{ $category->name }}</li>
            @endforeach
        </ul>

        <div class="filters-content">

            @foreach($categories as $category)
                @php
                    $products = App\Models\Product::where('category_id' , $category->id)->where('quantity' , '>' , 0)->where('active' , 1)->where('deleted_at',null)->take(3)->get();
                @endphp
                <div x-show="tab === {{ $category->id }}">
                    <div class="row grid">
                        @foreach($products as $product)
                            <div class="col-sm-6 col-lg-4">
                                <div class="box">
                                    <div>
                                        <div class="img-box">
                                            <img class="img-fluid" src="{{ getImagePath($product->primary_image) }}"
                                                 alt="">
                                        </div>
                                        <div class="detail-box">
                                            <h5>
                                                <a href="{{ route('products.show' , ['product' => $product->slug]) }}">
                                                    {{ $product->name }}
                                                </a>
                                            </h5>
                                            <p>
                                                {{ $product->description }}
                                            </p>
                                            <div class="options">
                                                @if($product->is_discounted)

                                                    <h6>
                                                        <del>{{ number_format($product->price) }}</del>
                                                        <span>
                                                    <span class="text-danger">({{ calculateDiscount($product->price , $product->discount_price) }}%)</span>
                                                    {{ number_format($product->discount_price) }}
                                                    <span>تومان</span>
                                                    </span>
                                                    </h6>

                                                @else
                                                    <h6>
                                                        <span>{{ number_format($product->price) }}<span> تومان</span></span>
                                                    </h6>
                                                @endif

                                                <div class="d-flex">
                                                    <a class="me-2"
                                                       href="{{ route('cart.increment' , ['product_id' => $product->id]) }}">
                                                        <i class="bi bi-cart-fill text-white fs-6"></i>
                                                    </a>
                                                    <a href="{{ route('addToFavorites' , ['product' => $product]) }}">
                                                        <i class="bi bi-heart-fill  text-white fs-6"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            @endforeach

        </div>

        <div class="btn-box">
            <a href="{{ route('products.menu') }}">
                مشاهده بیشتر
            </a>
        </div>
    </div>
</section>
<!-- end food section -->
