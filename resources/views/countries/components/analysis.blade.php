<div class="card border-0 shadow-sm rounded-4 h-100">

    <div class="card-header bg-white border-0">

        <h5 class="fw-bold text-orange">

            📊 Risk Analysis

        </h5>

    </div>

    <div class="card-body">

        <table class="table table-borderless align-middle">

            <tr>

                <th width="220">

                    Weather Risk

                </th>

                <td>

                    {{ $risk->weather_score ?? '-' }}

                </td>

            </tr>

            <tr>

                <th>

                    Currency Risk

                </th>

                <td>

                    {{ $risk->currency_score ?? '-' }}

                </td>

            </tr>

            <tr>

                <th>

                    Port Risk

                </th>

                <td>

                    {{ $risk->port_score ?? '-' }}

                </td>

            </tr>

            <tr>

                <th>

                    News Risk

                </th>

                <td>

                    {{ $risk->news_score ?? '-' }}

                </td>

            </tr>

            <tr>
                <th>Economy Risk</th>
                <td>
                    {{ number_format($risk->economic_score ?? 0, 2) }}
                </td>
            </tr>

            <tr class="table-warning">

                <th>

                    Final Risk Score

                </th>

                <td>

                    <strong>

                        {{ number_format($risk->final_score ?? 0,2) }}

                    </strong>

                </td>

            </tr>

        </table>

        <hr>

        <h6 class="fw-bold">

            Analysis Result

        </h6>

        <div class="alert alert-light border">

            {{ $risk->reason ?? '-' }}

        </div>

    </div>

</div>