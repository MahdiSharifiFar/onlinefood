@extends('.profile.layouts.master')
@section('pageTitle', 'لیست علاقه مندی')

@section('body')

    <div class="col-sm-12 col-lg-9">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>محصول</th>
                    <th>نام</th>
                    <th>قیمت</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($favorites as $item)
                    <tr>
                        <th>
                            <img class="rounded" src="{{ getImagePath($item->product->primary_image) }}" width="100"
                                 alt="" />
                        </th>
                        <td class="fw-bold">
                            <a href="{{ route('products.show', ['product' => $item->product->slug]) }}">
                                {{ $item->product->name }}
                            </a>
                        </td>
                        <td>
                            @if ($item->product->is_discounted)
                                <div>
                                    <del>{{ number_format($item->product->price) }}</del>
                                    {{ number_format($item->product->discount_price) }}
                                    تومان
                                </div>
                                <div class="text-danger">
                                    {{ calculateDiscount($item->product->price, $item->product->discount_price) }}%
                                    تخفیف
                                </div>
                            @else
                                <div>
                                    {{ number_format($item->product->price) }}
                                    تومان
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('profile.favorites.remove', ['favorite' => $item->id]) }}"
                               class="btn btn-primary">
                                حذف
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

@endsection
