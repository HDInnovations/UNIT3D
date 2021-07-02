@extends('layout.default')

@section('title')
    <title>{{ $user->username }} @lang('user.profile') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('users.show', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('user_edit_profile_form', ['username' => $user->username]) }}" itemprop="url"
            class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }} @lang('user.profile')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            @include('user.buttons.edit')
            <div class="header gradient blue">
                <div class="inner_content">
                    <h1>
                        {{ $user->username }} @lang('user.profile')
                    </h1>
                </div>
            </div>
            <div class="some-padding">
                <form role="form" method="POST" action="{{ route('user_edit_profile', ['username' => $user->username]) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="well">

                        <div class="form-group">
                            <label for="image">@lang('user.avatar')</label>
                            <small>@lang('user.formats-are-supported', ['formats' => '.jpg , .jpeg , .bmp , .png , .tiff ,
                                .gif'])</small>
                            <input type="file" name="image" id="image">
                        </div>

                        <div class="form-group">
                            <label for="title">@lang('user.custom-title')</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ $user->title }}">
                        </div>

                        <div class="form-group">
                            <label for="about">@lang('user.about-me') <span class="badge-extra">BBCode
                                    @lang('common.is-allowed')</span></label>
                            <textarea name="about" id="about" cols="30" rows="10" class="form-control">{{ $user->about }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="signature">@lang('user.forum-signature') <span class="badge-extra">BBCode
                                    @lang('common.is-allowed')</span></label>
                            <textarea name="signature" id="signature" cols="30" rows="10" class="form-control">{{ $user->signature }}</textarea>
                        </div>

                        @if ( !is_null($user->signature))
                            <div class="text-center">
                                <p>@lang('user.forum-signature') </p> {!! $user->getSignature() !!}
                            </div>
                        @endif
                    </div>
                    <div class="well text-center">
                        <button type="submit" class="btn btn-primary">@lang('common.submit')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    </div>
@endsection

@section('javascripts')
    <script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce('script') }}">
        $(document).ready(function() {
            $('#about, #signature').wysibb({});
        })
    </script>
@endsection