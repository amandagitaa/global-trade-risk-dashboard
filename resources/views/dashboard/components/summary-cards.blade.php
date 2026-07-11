<div class="row g-4 mb-4">

    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm rounded-4 h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">

                            Total Countries

                        </small>

                        <h2 class="fw-bold text-dark mt-2">

                            {{ number_format($summary['totalCountries']) }}

                        </h2>

                    </div>

                    <div class="summary-icon bg-warning">

                        🌍

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm rounded-4 h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">

                            Average Risk

                        </small>

                        <h2 class="fw-bold text-orange mt-2">

                            {{ number_format($summary['averageRisk'],2) }}

                        </h2>

                    </div>

                    <div class="summary-icon bg-orange">

                        📈

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-xl-2 col-md-4">

        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body text-center">

                <small class="text-muted">

                    Critical

                </small>

                <h3 class="text-danger fw-bold">

                    {{ $summary['critical'] }}

                </h3>

            </div>

        </div>

    </div>

    <div class="col-xl-2 col-md-4">

        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body text-center">

                <small class="text-muted">

                    Dangerous

                </small>

                <h3 style="color:#ff8c00" class="fw-bold">

                    {{ $summary['dangerous'] }}

                </h3>

            </div>

        </div>

    </div>

    <div class="col-xl-2 col-md-4">

        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body text-center">

                <small class="text-muted">

                    Alert

                </small>

                <h3 class="text-warning fw-bold">

                    {{ $summary['alert'] }}

                </h3>

            </div>

        </div>

    </div>

</div>

<style>

.summary-icon{

    width:55px;

    height:55px;

    border-radius:15px;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:28px;

    color:white;

}

.bg-orange{

    background:#ff8c00;

}

.text-orange{

    color:#ff8c00;

}

</style>