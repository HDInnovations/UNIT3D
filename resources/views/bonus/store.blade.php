@extends('layout.default')

@section('breadcrumb')
    <li>
        <a href="{{ route('bonus') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('bon.bonus') @lang('bon.points')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('bonus_store') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('bon.bonus') @lang('bon.store')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            @include('bonus.buttons')
            <div class="header gradient purple">
                <div class="inner_content">
                    <h1>@lang('bon.bon') @lang('bon.store')</h1>
                </div>
            </div>
            <div class="some-padding">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="well">
                            <h3>@lang('bon.exchange')</h3>
                            <table class="table table-condensed table-striped">
                                <thead>
                                    <tr>
                                        <th>@lang('bon.item')</th>
                                        <th>@lang('bon.points')</th>
                                        <th>@lang('bon.exchange')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($uploadOptions as $u => $uu)
                                        <tr>
                                            <td>{{ $uu['description'] }}</td>
                                            <td>{{ $uu['cost'] }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('bonus_exchange', ['id' => $uu['id']]) }}">
                                                    @csrf
                                                    <button type="sumbit"
                                                        class="btn btn-sm btn-info btn-exchange">@lang('bon.exchange')</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @foreach ($personalFreeleech as $p => $pf)
                                        <tr>
                                            <td>{{ $pf['description'] }}</td>
                                            <td>{{ $pf['cost'] }}</td>
                                            <td>
                                                @if ($activefl)
                                                    <button type="submit" class="btn btn-sm btn-success btn-exchange disabled">
                                                        @lang('bon.activated')!
                                                    </button>
                                                @else
                                                    <form method="POST" action="{{ route('bonus_exchange', ['id' => $pf['id']]) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info btn-exchange">
                                                            @lang('bon.exchange')
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    @foreach ($invite as $i => $in)
                                        <tr>
                                            <td>{{ $in['description'] }}</td>
                                            <td>{{ $in['cost'] }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('bonus_exchange', ['id' => $in['id']]) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-info btn-exchange"
                                                        type="submit">@lang('bon.exchange')</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
