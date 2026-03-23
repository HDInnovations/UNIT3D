<div class="panelV2" x-data="toggle">
    <h2 class="panel__heading" style="cursor: pointer" x-on:click="toggle">
        <i class="{{ config('other.font-awesome') }} fa-clipboard-list"></i>
        Reports
        <i
            class="{{ config('other.font-awesome') }} fa-plus-circle fa-pull-right"
            x-show="isToggledOff"
        ></i>
        <i
            class="{{ config('other.font-awesome') }} fa-minus-circle fa-pull-right"
            x-show="isToggledOn"
            x-cloak
        ></i>
    </h2>
    <div class="data-table-wrapper" x-show="isToggledOn" x-cloak>
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('common.title') }}</th>
                    <th>Reported</th>
                    <th>{{ __('common.reporter') }}</th>
                    <th>{{ __('common.created_at') }}</th>
                    <th>{{ __('common.staff') }}</th>
                    <th>{{ __('forum.solved') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($torrent->reports as $report)
                    <tr>
                        <td>
                            <a href="{{ route('staff.reports.show', ['report' => $report]) }}">
                                {{ $report->title }}
                            </a>
                        </td>
                        <td>
                            <x-user-tag :anon="false" :user="$report->reported" />
                        </td>
                        <td>
                            <x-user-tag :anon="false" :user="$report->reporter" />
                        </td>
                        <td>
                            <time
                                datetime="{{ $report->created_at }}"
                                title="{{ $report->created_at }}"
                            >
                                {{ $report->created_at->toDayDateTimeString() }}
                            </time>
                        </td>
                        <td>
                            @if ($report->staff_id !== null)
                                <x-user-tag :anon="false" :user="$report->staff" />
                            @else
                                Unassigned
                            @endif
                        </td>
                        <td>
                            @if ($report->solved)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                                {{ __('common.yes') }}
                            @else
                                <i class="{{ config('other.font-awesome') }} fa-times text-red"></i>
                                {{ __('common.no') }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">No reports</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
