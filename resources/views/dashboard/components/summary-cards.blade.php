<div class="row g-4 mb-4">

    {{-- Total Countries --}}
    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="card border-0 shadow-sm rounded-4 h-100">

            <div class="card-body">

                <small class="text-muted d-block mb-3">
                    Total Countries
                </small>

                <div class="d-flex align-items-center">

                    <div class="summary-icon-sm bg-warning me-2">

                        <i class="bi bi-globe-americas"></i>

                    </div>

                    <h2 class="fw-bold text-dark mb-0">

                        {{ number_format($summary['totalCountries']) }}

                    </h2>

                </div>

            </div>

        </div>

    </div>


    {{-- Average Risk --}}
    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="card border-0 shadow-sm rounded-4 h-100">

            <div class="card-body">

                <small class="text-muted d-block mb-3">
                    Average Risk
                </small>

                <div class="d-flex align-items-center">

                    <div class="summary-icon-sm bg-orange me-2">

                        <i class="bi bi-speedometer2"></i>

                    </div>

                    <h2 class="fw-bold text-orange mb-0">

                        {{ number_format($summary['averageRisk'],2) }}

                    </h2>

                </div>

            </div>

        </div>

    </div>


    {{-- Critical --}}
    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="card border-0 shadow-sm rounded-4 h-100">

            <div class="card-body text-center">

                <small class="text-muted d-block mb-3">
                    Critical
                </small>

                <h2 class="fw-bold text-danger mb-0">

                    {{ $summary['critical'] }}

                </h2>

            </div>

        </div>

    </div>


    {{-- Dangerous --}}
    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="card border-0 shadow-sm rounded-4 h-100">

            <div class="card-body text-center">

                <small class="text-muted d-block mb-3">
                    Dangerous
                </small>

                <h2 class="fw-bold mb-0" style="color:#ff8c00">

                    {{ $summary['dangerous'] }}

                </h2>

            </div>

        </div>

    </div>


    {{-- Alert --}}
    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="card border-0 shadow-sm rounded-4 h-100">

            <div class="card-body text-center">

                <small class="text-muted d-block mb-3">
                    Alert
                </small>

                <h2 class="fw-bold text-warning mb-0">

                    {{ $summary['alert'] }}

                </h2>

            </div>

        </div>

    </div>


    {{-- Risk Countries --}}
    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="card border-0 shadow-sm rounded-4 h-100">

            <div class="card-body text-center">

                <small class="text-muted d-block mb-3">
                    Risk Countries
                </small>

                <h2 class="fw-bold text-danger mb-0">

                    {{ $summary['riskCountries'] }}

                </h2>

            </div>

        </div>

    </div>

</div>

<style>

.summary-icon-sm{

    width:28px;
    height:28px;

    display:flex;
    align-items:center;
    justify-content:center;

    border-radius:10px;

    flex-shrink:0;

    color:#fff;

}

.summary-icon-sm i{

    font-size:15px;

    line-height:1;

}

.bg-warning{

    background:#FDB515 !important;

}

.bg-orange{

    background:#ff8c00 !important;

}

.text-orange{

    color:#ff8c00;

}

</style>