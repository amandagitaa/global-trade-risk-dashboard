<div class="card border-0 shadow-sm rounded-4 h-100">


    <div class="card-header bg-white border-0">


        <div class="d-flex justify-content-between align-items-center">


            <div>

                <h5 class="fw-bold text-orange mb-1">

                    🌦 Weather Risk Monitoring

                </h5>


                <small class="text-muted">

                    Latest weather risks impacting international trade operations

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


                    <th style="width:40%">
                        Country
                    </th>


                    <th style="width:25%">
                        Weather
                    </th>


                    <th style="width:15%">
                        Temperature
                    </th>


                    <th style="width:20%" class="text-center">
                        Trade Impact
                    </th>


                </tr>


                </thead>



                <tbody>


                @forelse($weatherPanel as $weather)


                    @php


                        $status = strtolower($weather->weather_status);



                        switch($status){


                            case 'clear':

                                $icon = '☀';

                                $badge = 'success';

                                $impact = 'Low Risk';

                                break;



                            case 'cloudy':

                                $icon = '☁';

                                $badge = 'success';

                                $impact = 'Low Risk';

                                break;



                            case 'rain':

                                $icon = '🌧';

                                $badge = 'warning';

                                $impact = 'Medium Risk';

                                break;



                            case 'heavy_rain':

                                $icon = '🌧';

                                $badge = 'warning';

                                $impact = 'High Risk';

                                break;



                            case 'storm':

                                $icon = '⛈';

                                $badge = 'danger';

                                $impact = 'Critical Risk';

                                break;



                            default:

                                $icon = '🌤';

                                $badge = 'secondary';

                                $impact = 'Unknown';

                        }


                    @endphp



                    <tr>



                        <td>


                            <div class="d-flex align-items-center">


                                <img
                                    src="{{ $weather->country->flag }}"
                                    width="36"
                                    class="rounded shadow-sm me-3">



                                <div>


                                    <div class="fw-semibold">


                                        {{ $weather->country->country_name }}


                                    </div>



                                    <small class="text-muted">


                                        {{ strtoupper($weather->country->country_code) }}


                                    </small>



                                </div>


                            </div>


                        </td>




                        <td>


                            <span class="badge bg-{{ $badge }}">


                                {{ $icon }}

                                {{ ucwords(str_replace('_',' ',$weather->weather_status)) }}


                            </span>


                        </td>




                        <td>


                            <strong>


                                {{ number_format($weather->temperature,1) }}°C


                            </strong>


                        </td>




                        <td class="text-center">


                            <span class="badge bg-{{ $badge }} px-3 py-2">


                                {{ $impact }}


                            </span>


                        </td>



                    </tr>



                @empty


                    <tr>


                        <td colspan="4"
                            class="text-center py-5 text-muted">


                            No weather risk information available.


                        </td>


                    </tr>


                @endforelse



                </tbody>



            </table>


        </div>


    </div>


</div>