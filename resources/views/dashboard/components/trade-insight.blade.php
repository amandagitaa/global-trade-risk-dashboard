<div class="card border-0 shadow-sm rounded-4 h-100">

    <div class="card-header bg-white border-0">

        <h5 class="fw-bold text-orange mb-1">

            💡 Trade Insight

        </h5>

        <small class="text-muted">

            AI generated trade recommendation based on risk analysis

        </small>

    </div>


    <div class="card-body">


        @forelse($recommendations as $item)


            <div class="border rounded-3 p-3 mb-3">


                <div class="d-flex justify-content-between align-items-center">


                    <div>

                        <h6 class="fw-bold mb-1">

                            {{ $item->country->country_name }}

                        </h6>


                        <small class="text-muted">

                            {{ $item->trade_action }}

                        </small>

                    </div>



                    @php

                        $color = match($item->priority){

                            'critical'=>'danger',

                            'high'=>'warning',

                            'medium'=>'primary',

                            default=>'success'

                        };

                    @endphp


                    <span class="badge bg-{{ $color }}">

                        {{ strtoupper($item->priority) }}

                    </span>


                </div>



                <hr>



                <p class="mb-2">

                    {{ $item->recommendation }}

                </p>



                <small class="text-muted">

                    Reason:

                    {{ $item->business_reason }}

                </small>


            </div>


        @empty


            <div class="text-center py-4">

                No trade recommendation available.

            </div>


        @endforelse


    </div>

</div>