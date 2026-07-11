<div class="card border-0 shadow-sm rounded-4 h-100">

    <div class="card-header bg-white border-0">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="fw-bold text-orange mb-1">

                    🌦 Weather Monitoring

                </h5>

                <small class="text-muted">

                    Latest weather information from monitored countries

                </small>

            </div>

            <span class="badge bg-info">

                {{ count($weatherPanel) }} Records

            </span>

        </div>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>Country</th>
                        <th>Weather</th>
                        <th>Temperature</th>
                        <th>Humidity</th>
                        <th>Wind</th>
                        <th>Updated</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($weatherPanel as $weather)

                    <tr>

                        <td>

                            <div class="d-flex align-items-center">

                                <img src="{{ $weather->country->flag }}"
                                     width="34"
                                     class="rounded shadow-sm me-2">

                                <div>

                                    <strong>

                                        {{ $weather->country->country_name }}

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        {{ $weather->country->country_code }}

                                    </small>

                                </div>

                            </div>

                        </td>

                        <td>

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

                            <span class="badge bg-{{ $badge }}">

                                {{ ucwords(str_replace('_',' ',$weather->weather_status)) }}

                            </span>

                        </td>

                        <td>

                            {{ number_format($weather->temperature,1) }} °C

                        </td>

                        <td>

                            {{ $weather->humidity }} %

                        </td>

                        <td>

                            {{ number_format($weather->wind_speed,1) }} km/h

                        </td>

                        <td>

                            {{ optional($weather->recorded_at)->format('d M Y H:i') }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center py-5">

                            No weather data available.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>