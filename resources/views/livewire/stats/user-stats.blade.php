<section class="panelV2 panel--grid-item">
    <h2 class="panel__heading">{{ __('common.users') }}</h2>
    <dl class="key-value">
        <div class="key-value__group">
            <dt>{{ __('stat.all') }}</dt>
            <dd>{{ $all_user }}</dd>
        </div>
        <div class="key-value__group">
            <dt>{{ __('stat.disabled') }}</dt>
            <dd>{{ $disabled_user }}</dd>
        </div>
        <div class="key-value__group">
            <dt>{{ __('stat.pruned') }}</dt>
            <dd>{{ $pruned_user }}</dd>
        </div>
        <div class="key-value__group">
            <dt>{{ __('stat.banned') }}</dt>
            <dd>{{ $banned_user }}</dd>
        </div>
        <div class="key-value__group--nested">
            <dt>{{ __('stat.active') }}</dt>
            <dd>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>Today</dt>
                        <dd>{{ $users_active_today }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>This week</dt>
                        <dd>{{ $users_active_this_week }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>This month</dt>
                        <dd>{{ $users_active_this_month }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('stat.all') }}</dt>
                        <dd>{{ $active_user }}</dd>
                    </div>
                </dl>
            </dd>
        </div>
    </dl>
</section>
