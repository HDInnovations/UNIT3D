@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('bon.bon') }} {{ __('bon.earning') }}</li>
@endsection

@section('page', 'page__staff-bon-earning--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('bon.bon') }} {{ __('bon.exchange') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <a
                        href="{{ route('staff.bon_earnings.create') }}"
                        class="form__button form__button--text"
                    >
                        {{ __('common.add') }}
                        {{ trans_choice('common.a-an-art', true) }}
                        {{ __('bon.earning') }}
                    </a>
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.position') }}</th>
                        <th>Variable</th>
                        <th>Operation</th>
                        <th>Multiplier</th>
                        <th>Conditions</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bonEarnings as $bonEarning)
                        <tr>
                            <td>
                                <a
                                    href="{{ route('staff.bon_earnings.edit', ['bonEarning' => $bonEarning]) }}"
                                >
                                    {{ $bonEarning->name }}
                                </a>
                            </td>
                            <td>{{ $bonEarning->position }}</td>
                            <td>
                                @switch($bonEarning->variable)
                                    @case('1')
                                        1 (Constant)

                                        @break
                                    @case('age')
                                        {{ __('torrent.age') }}

                                        @break
                                    @case('size')
                                        {{ __('torrent.size') }}

                                        @break
                                    @case('seeders')
                                        {{ __('torrent.seeders') }}

                                        @break
                                    @case('leechers')
                                        {{ __('torrent.leechers') }}

                                        @break
                                    @case('times_completed')
                                        {{ __('torrent.completed-times') }}

                                        @break
                                    @case('internal')
                                        {{ __('common.internal') }}

                                        @break
                                    @case('personal_release')
                                        {{ __('torrent.personal-release') }}

                                        @break
                                    @case('seedtime')
                                        {{ __('torrent.seedtime') }}

                                        @break
                                    @case('connectable')
                                        Connectable

                                        @break
                                    @default
                                        {{ __('common.unknown') }}
                                @endswitch
                            </td>
                            <td>
                                @switch($bonEarning->operation)
                                    @case('append')
                                        Append

                                        @break
                                    @case('multiply')
                                        Multiply

                                        @break
                                    @default
                                        {{ __('common.unknown') }}
                                @endswitch
                            </td>
                            <td>
                                {{ preg_replace('/(\.\d+?)0+$/', '$1', $bonEarning->multiplier) }}
                            </td>
                            <td>
                                <ul>
                                    @forelse ($bonEarning->conditions as $condition)
                                        <li>
                                            {{ $condition->operand1 }} {{ $condition->operator }}
                                            {{
                                                match ($condition->operand1) {
                                                    'age' => \App\Helpers\StringHelper::timeElapsed($condition->operand2),
                                                    'size' => \App\Helpers\StringHelper::formatBytes($condition->operand2),
                                                    'seedtime' => \App\Helpers\StringHelper::timeElapsed($condition->operand2),
                                                    'type_id' => \App\Models\Type::query()->find($condition->operand2)?->name ?? __('common.unknown'),
                                                    default => preg_replace('/(\.\d+?)0+$/', '$1', $condition->operand2),
                                                }
                                            }}
                                        </li>
                                    @empty
                                        <li>No conditions</li>
                                    @endforelse
                                </ul>
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            class="form__button form__button--text"
                                            href="{{ route('staff.bon_earnings.edit', ['bonEarning' => $bonEarning]) }}"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.bon_earnings.destroy', ['bonEarning' => $bonEarning]) }}"
                                            method="POST"
                                            x-data
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="
                                                    Swal.fire({
                                                        title: 'Delete?',
                                                        text: 'Are you sure you want to delete this bon earning?',
                                                        icon: 'warning',
                                                        showConfirmButton: true,
                                                        showCancelButton: true,
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            $root.submit();
                                                        }
                                                    })
                                                "
                                                class="form__button form__button--text"
                                            >
                                                {{ __('common.delete') }}
                                            </button>
                                        </form>
                                    </li>
                                </menu>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
