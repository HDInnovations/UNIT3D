@extends('layout.default')

@section('title')
    <title>{{ $user->username }} @lang('user.tips') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('bonus') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('bon.bonus') @lang('bon.points')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('bonus_tips') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('bon.bonus') @lang('bon.tips')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            @include('bonus.buttons')
            <div class="header gradient purple">
                <div class="inner_content">
                    <h1>@lang('bon.bon') @lang('bon.tips')</h1>
                </div>
            </div>
            <div class="some-padding">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="well">
                            <h3>@lang('bon.tips')</h3>
                            <table class="table table-condensed table-striped">
                                <thead>
                                    <tr>
                                        <th>@lang('bon.sender')</th>
                                        <th>@lang('bon.receiver')</th>
                                        <th>@lang('bon.points')</th>
                                        <th>@lang('bon.date')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bontransactions as $b)
                                        <tr>
                                            <td>
                                                <a href="{{ route('users.show', ['username' => $b->senderObj->username]) }}">
                                                    <span class="badge-user text-bold">{{ $b->senderObj->username }}</span>
                                                </a>
                                            </td>
                                            <td>
                                                @if($b->whereNotNull('torrent_id'))
                                                    @php $torrent = \App\Models\Torrent::select(['anon'])->find($b->torrent_id); @endphp
                                                @endif
                                                @if(isset($torrent) && $torrent->anon === 1 && $b->receiver !== $user->id)
                                                        <span class="badge-user text-bold">@lang('common.anonymous')</span>
                                                @else
                                                    <a href="{{ route('users.show', ['username' => $b->receiverObj->username]) }}">
                                                        <span class="badge-user text-bold">{{ $b->receiverObj->username }}</span>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $b->cost }}
                                            </td>
                                            <td>
                                                {{ $b->date_actioned }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="some-padding text-center">
                                {{ $bontransactions->links() }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">

                        <div class="text-blue well well-sm text-center">
                            <h2><strong>@lang('bon.your-points'): {{ $userbon }}<br></strong></h2>
                        </div>

                        <div class="text-orange well well-sm text-center">
                            <div>
                                <h3>@lang('bon.you-have-received-tips'): <strong>{{ $tips_received }}</strong>
                                    @lang('bon.total-tips')</h3>
                            </div>
                            <div>
                                <h3>@lang('bon.you-have-sent-tips'): <strong>{{ $tips_sent }}</strong>
                                    @lang('bon.total-tips')</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
