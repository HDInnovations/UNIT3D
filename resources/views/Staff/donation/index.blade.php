@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Donations</li>
@endsection

@section('page', 'page__staff-donation--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Donation Statistics</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <select id="chartTimeframe" class="form__select">
                        <option value="daily">Daily View</option>
                        <option value="monthly">Monthly View</option>
                        <option value="yearly">Yearly View</option>
                    </select>
                </div>
            </div>
        </header>
        <div class="panel__body">
            <div id="donationChart" style="width: 100%; height: 400px"></div>
            <div
                class="stats-summary"
                style="margin-top: 20px; display: flex; justify-content: space-around"
            >
                <div class="stat-card">
                    <h4>Total Donations</h4>
                    <p id="totalDonations">$0</p>
                </div>
                <div class="stat-card">
                    <h4>Average Donation</h4>
                    <p id="avgDonation">$0</p>
                </div>
                <div class="stat-card">
                    <h4>Most Active Day</h4>
                    <p id="mostActiveDay">-</p>
                </div>
            </div>
        </div>
    </section>

    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Donations</h2>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Transaction</th>
                        <th>Cost</th>
                        <th>Upload #</th>
                        <th>Invite #</th>
                        <th>Bonus #</th>
                        <th>Length</th>
                        <th>Status</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($donations as $donation)
                        <tr>
                            <td>{{ $donation->created_at }}</td>
                            <td>
                                <x-user-tag :user="$donation->user" :anon="false" />
                            </td>
                            <td style="max-width: 80ch; word-wrap: break-word; white-space: normal">
                                {{ $donation->transaction }}
                            </td>
                            <td
                                class="{{ $donation->package->trashed() ? 'text-danger' : '' }}"
                                title="{{ $donation->package->trashed() ? 'Package has been deleted' : '' }}"
                            >
                                $ {{ $donation->package->cost }}
                            </td>
                            <td
                                class="{{ $donation->package->trashed() ? 'text-danger' : '' }}"
                                title="{{ $donation->package->trashed() ? 'Package has been deleted' : '' }}"
                            >
                                {{ App\Helpers\StringHelper::formatBytes($donation->package->upload_value ?? 0) }}
                            </td>
                            <td
                                class="{{ $donation->package->trashed() ? 'text-danger' : '' }}"
                                title="{{ $donation->package->trashed() ? 'Package has been deleted' : '' }}"
                            >
                                {{ $donation->package->invite_value ?? 0 }}
                            </td>
                            <td
                                class="{{ $donation->package->trashed() ? 'text-danger' : '' }}"
                                title="{{ $donation->package->trashed() ? 'Package has been deleted' : '' }}"
                            >
                                {{ $donation->package->bonus_value ?? 0 }}
                            </td>
                            <td
                                class="{{ $donation->package->trashed() ? 'text-danger' : '' }}"
                                title="{{ $donation->package->trashed() ? 'Package has been deleted' : '' }}"
                            >
                                @if ($donation->package->donor_value === null)
                                    Lifetime
                                @else
                                    {{ $donation->package->donor_value }} Days
                                @endif
                            </td>
                            <td>
                                @if ($donation->status === App\Enums\ModerationStatus::PENDING)
                                    <span class="text-warning">Pending</span>
                                @elseif ($donation->status === App\Enums\ModerationStatus::APPROVED)
                                    <span class="text-success">Approved</span>
                                @else
                                    <span class="text-danger">Rejected</span>
                                @endif
                            </td>

                            <td>
                                @if ($donation->status === App\Enums\ModerationStatus::PENDING)
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <form
                                                action="{{ route('staff.donations.update', ['donation' => $donation]) }}"
                                                method="POST"
                                                x-data="confirmation"
                                            >
                                                @csrf
                                                <button
                                                    x-on:click.prevent="confirmAction"
                                                    data-b64-deletion-message="{{ base64_encode('Are you sure you want to approve this donation: ' . $donation->id . '?') }}"
                                                    class="form__button form__button--filled"
                                                >
                                                    Approve
                                                </button>
                                            </form>
                                        </li>

                                        <li class="data-table__action">
                                            <form
                                                action="{{ route('staff.donations.destroy', ['donation' => $donation]) }}"
                                                method="POST"
                                                x-data="confirmation"
                                            >
                                                @csrf
                                                <button
                                                    x-on:click.prevent="confirmAction"
                                                    data-b64-deletion-message="{{ base64_encode('Are you sure you want to reject this donation: ' . $donation->id . '?') }}"
                                                    class="form__button form__button--filled"
                                                >
                                                    Reject
                                                </button>
                                            </form>
                                        </li>
                                    </menu>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $donations->links('partials.pagination') }}
    </section>
@endsection

@section('styles')
    <style>
        .stat-card {
            background: var(--panel-bg);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .stat-card h4 {
            margin: 0 0 10px 0;
            color: var(--text-color);
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .stat-card p {
            margin: 0;
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--link-color);
        }
        #donationChart {
            background: var(--panel-bg);
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .chart-container {
            position: relative;
            margin-top: 1rem;
        }
        .stats-summary {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 0 1rem;
        }
        #chartTimeframe {
            background: var(--panel-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-color);
            padding: 0.5rem;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        #chartTimeframe option {
            background: var(--panel-bg);
            color: var(--text-color);
        }
    </style>
@endsection

@section('scripts')
    @vite('resources/js/vendor/apexcharts.js')
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('DOMContentLoaded', function () {
            const dailyDonations = {!! Js::encode($dailyDonations) !!};
            const monthlyDonations = {!! Js::encode($monthlyDonations) !!};

            const style = getComputedStyle(document.documentElement);
            const textColor = style.getPropertyValue('--text-color').trim();
            const linkColor = style.getPropertyValue('--link-color').trim();
            const panelBg = style.getPropertyValue('--panel-bg').trim();

            const processedData = {
                daily: dailyDonations.map((d) => ({
                    x: new Date(d.date),
                    y: parseFloat(d.total),
                })),
                monthly: monthlyDonations.map((d) => ({
                    x: new Date(d.year, d.month - 1),
                    y: parseFloat(d.total),
                })),
                yearly: monthlyDonations.reduce((acc, curr) => {
                    const year = parseInt(curr.year);
                    const existing = acc.find((item) => item.x.getFullYear() === year);
                    if (existing) {
                        existing.y += parseFloat(curr.total);
                    } else {
                        acc.push({
                            x: new Date(year, 0),
                            y: parseFloat(curr.total),
                        });
                    }
                    return acc;
                }, []),
            };

            const calculateStats = (data) => {
                const total = data.reduce((sum, item) => sum + item.y, 0);
                const avg = total / data.length;
                const mostActive = data.reduce(
                    (max, item) => (item.y > max.y ? item : max),
                    data[0],
                );

                document.getElementById('totalDonations').textContent = '$' + total.toFixed(2);
                document.getElementById('avgDonation').textContent = '$' + avg.toFixed(2);
                document.getElementById('mostActiveDay').textContent =
                    mostActive.x.toLocaleDateString();
            };

            const options = {
                series: [
                    {
                        name: 'Donations',
                        data: processedData.monthly,
                    },
                ],
                chart: {
                    type: 'area',
                    height: 400,
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                    },
                    background: 'transparent',
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                        },
                        autoSelected: 'zoom',
                    },
                    zoom: {
                        enabled: true,
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                    colors: ['#1e3d59'],
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 100],
                        colorStops: [
                            {
                                offset: 0,
                                color: '#1e3d59',
                                opacity: 0.45,
                            },
                            {
                                offset: 100,
                                color: '#1e3d59',
                                opacity: 0.05,
                            },
                        ],
                    },
                },
                grid: {
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    strokeDashArray: 3,
                    position: 'back',
                },
                xaxis: {
                    type: 'datetime',
                    labels: {
                        style: {
                            colors: textColor,
                            opacity: 0.9,
                        },
                        datetimeFormatter: {
                            year: 'yyyy',
                            month: 'MMM yyyy',
                            day: 'dd MMM',
                        },
                    },
                    axisBorder: {
                        color: 'rgba(255, 255, 255, 0.1)',
                    },
                    axisTicks: {
                        color: 'rgba(255, 255, 255, 0.1)',
                    },
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: textColor,
                            opacity: 0.9,
                        },
                        formatter: function (val) {
                            return '$' + val.toFixed(2);
                        },
                    },
                },
                tooltip: {
                    theme: 'dark',
                    x: {
                        format: 'dd MMM yyyy',
                    },
                    y: {
                        formatter: function (val) {
                            return '$' + val.toFixed(2);
                        },
                    },
                    style: {
                        fontSize: '12px',
                        fontFamily: undefined,
                    },
                    marker: {
                        show: true,
                    },
                },
                markers: {
                    size: 4,
                    colors: ['#1e3d59'],
                    strokeColors: '#0a1f33',
                    strokeWidth: 2,
                    hover: {
                        size: 6,
                        colors: ['#2a5580'],
                    },
                },
                theme: {
                    mode: 'dark',
                    palette: 'palette1',
                },
            };

            const chart = new ApexCharts(document.querySelector('#donationChart'), options);
            chart.render();
            calculateStats(processedData.daily);

            document.getElementById('chartTimeframe').addEventListener('change', function (e) {
                const timeframe = e.target.value;
                chart.updateSeries([
                    {
                        data: processedData[timeframe],
                    },
                ]);
                calculateStats(processedData[timeframe]);

                chart.updateOptions({
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            datetimeFormatter: {
                                year: 'yyyy',
                                month: timeframe === 'yearly' ? 'yyyy' : 'MMM yyyy',
                                day: timeframe === 'daily' ? 'dd MMM' : 'MMM yyyy',
                            },
                        },
                    },
                });
            });
        });
    </script>
@endsection
