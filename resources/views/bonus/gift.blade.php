@extends('layout.default')

@section('breadcrumb')
    <li>
        <a href="{{ route('bonus') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('bon.bonus') @lang('bon.points')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('bonus_gift') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('bon.bonus') @lang('bon.send-gift')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            @include('bonus.buttons')
            <div class="header gradient purple">
                <div class="inner_content">
                    <h1>@lang('bon.bon') @lang('bon.send-gift')</h1>
                </div>
            </div>
            <div class="some-padding">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="well">
                            <div class="container-fluid">
                                <form role="form" method="POST" action="{{ route('bonus_send_gift') }}" id="send_bonus">
                                    @csrf

                                    <div class="form-group">
                                        <label for="to_username">@lang('bon.gift-to')</label>
                                    </div>
                                    <div class="form-group">
                                        <label for="users">@lang('pm.select')</label>
                                        <label>
                                            <input name="to_username" class="form-control"
                                                placeholder="@lang('common.username')" required>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="bonus_points">@lang('bon.amount')</label>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control"
                                            placeholder="@lang('common.enter') {{ strtolower(trans('common.amount')) }}"
                                            name="bonus_points" type="number" id="bonus_points" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="bonus_message">@lang('pm.message')</label>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" name="bonus_message" cols="50" rows="10" id="bonus_message"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <input class="btn btn-primary" type="submit" value="@lang('common.submit')">
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">

                        <div class="text-blue well well-sm text-center">
                            <h2><strong>@lang('bon.your-points'): <br></strong>{{ $userbon }}</h2>
                        </div>

                        <div class="well well-sm mt-20">
                            <p class="lead text-orange text-center">@lang('bon.exchange-warning')
                                <br><strong>@lang('bon.no-refund')</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
