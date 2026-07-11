<div class="card border-0 shadow-sm rounded-4 h-100">

    <div class="card-header bg-white border-0">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="fw-bold text-orange mb-1">

                    ⚠ Highest Risk Countries

                </h5>

                <small class="text-muted">

                    Countries requiring immediate attention

                </small>

            </div>

            <span class="badge bg-danger">

                Top 10

            </span>

        </div>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead class="table-light">

                <tr>

                    <th width="60">#</th>

                    <th>Country</th>

                    <th>Risk</th>

                    <th>Level</th>

                    <th>Priority</th>

                    <th>Action</th>

                </tr>

                </thead>

                <tbody>

                @forelse($highestRiskCountries as $index=>$country)

                    @php

                        $risk = $country->latestRisk;

                        $recommendation = $country->recommendation;

                    @endphp

                    <tr>

                        <td>

                            <strong>

                                {{ $index+1 }}

                            </strong>

                        </td>

                        <td>

                            <div class="d-flex align-items-center">

                                <img

                                    src="{{ $country->flag }}"

                                    width="34"

                                    class="me-2 rounded shadow-sm"

                                >

                                <div>

                                    <strong>

                                        {{ $country->country_name }}

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        {{ $country->country_code }}

                                    </small>

                                </div>

                            </div>

                        </td>

                        <td>

                            <strong>

                                {{ number_format($risk->final_score,2) }}

                            </strong>

                        </td>

                        <td>

                            @switch($risk->risk_level)

                                @case('critical')

                                    <span class="badge bg-danger">

                                        Critical

                                    </span>

                                    @break

                                @case('dangerous')

                                    <span class="badge"

                                          style="background:#ff8c00">

                                        Dangerous

                                    </span>

                                    @break

                                @case('alert')

                                    <span class="badge bg-warning text-dark">

                                        Alert

                                    </span>

                                    @break

                                @case('stable')

                                    <span class="badge bg-primary">

                                        Stable

                                    </span>

                                    @break

                                @default

                                    <span class="badge bg-success">

                                        Safe

                                    </span>

                            @endswitch

                        </td>

                        <td>

                            @if($recommendation)

                                <span class="badge bg-dark">

                                    {{ $recommendation->priority }}

                                </span>

                            @else

                                -

                            @endif

                        </td>

                        <td>

                            @if($recommendation)

                                {{ $recommendation->trade_action }}

                            @else

                                -

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6"

                            class="text-center py-5">

                            No data available

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>