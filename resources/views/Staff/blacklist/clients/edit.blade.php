@extends('layout.default')

@section('breadcrumb')
<li>
    <a href="{{ route('staff.dashboard.index') }}" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">{{ __('staff.staff-dashboard') }}</span>
    </a>
</li>
<li class="active">
    <a href="#" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">Blacklists</span>
    </a>
</li>
<li class="active">
    <a href="{{ route('staff.blacklists.clients.index') }}" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">Clients</span>
    </a>
</li>
<li class="active">
    <a href="{{ route('staff.blacklists.clients.edit', ['name' => $client->name, 'id' => $client->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $client->name }}</span>
    </a>
</li>
@endsection

@section('content')
    <div class="container box">
        <h2>{{ $client->name }}</h2>
        <div class="table-responsive">
            <form role="form" method="POST"
                  action="{{ route('staff.blacklists.clients.update', ['name' => $client->name, 'id' => $client->id]) }}">
                @csrf
                <div class="table-responsive">
                    <table class="table table-condensed table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>{{ __('common.name') }}</th>
                            <th>Reason</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td>
                                <label>
                                    <input type="text" name="name" value="{{ $client->name }}" class="form-control" required/>
                                </label>
                            </td>
                            <td>
                                <label>
                                    <input type="text" name="reason" value="{{ $client->reason }}" class="form-control"/>
                                </label>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <button type="submit" class="btn btn-primary">{{ __('common.submit') }}</button>
            </form>
        </div>
    </div>
@endsection
