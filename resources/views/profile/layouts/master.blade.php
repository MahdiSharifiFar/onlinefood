@extends('.layouts.master')
@section('title' , 'پروفایل')

@section('content')
    <section class="profile_section layout_padding">
        <div class="container">
            <div class="row">
                @include('.profile.layouts.sidebar')
                @yield('body')
            </div>
        </div>
    </section>
@endsection

