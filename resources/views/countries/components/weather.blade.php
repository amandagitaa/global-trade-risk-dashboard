<div class="card border-0 shadow-sm rounded-4 h-100">

    <div class="card-header bg-white border-0">

        <h5 class="fw-bold text-orange">

            🌦 Weather Information

        </h5>

    </div>

    <div class="card-body">

        @if($weather)

            @php

                $status = strtolower($weather->weather_status);

                $badge = match($status){

                    'clear' => 'success',

                    'cloudy' => 'secondary',

                    'rain' => 'primary',

                    'heavy_rain' => 'warning',

                    'storm' => 'danger',

                    default => 'dark'

                };

            @endphp

            <div class="text-center mb-4">

                <span class="badge bg-{{ $badge }} px-4 py-2">

                    {{ strtoupper(str_replace('_',' ',$weather->weather_status)) }}

                </span>

            </div>

            <table class="table table-borderless">

                <tr>

                    <th width="45%">

                        Temperature

                    </th>

                    <td>

                        {{ number_format($weather->temperature,1) }} °C

                    </td>

                </tr>

                <tr>

                    <th>

                        Humidity

                    </th>

                    <td>

                        {{ $weather->humidity }} %

                    </td>

                </tr>

                <tr>

                    <th>

                        Wind Speed

                    </th>

                    <td>

                        {{ number_format($weather->wind_speed,1) }} km/h

                    </td>

                </tr>

                <tr>

                    <th>

                        Updated

                    </th>

                    <td>

                        {{ optional($weather->recorded_at)->format('d M Y H:i') }}

                    </td>

                </tr>

            </table>

        @else

            <div class="text-center py-5">

                No weather information available.

            </div>

        @endif

    </div>

</div>