<div class="card border-0 shadow-sm rounded-4">

    <div class="card-header bg-white border-0">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="fw-bold text-orange mb-1">

                    🌍 Global Trade Risk Map

                </h5>

                <small class="text-muted">

                    Real-time global trade monitoring

                </small>

            </div>

            <span class="badge bg-warning text-dark">

                {{ count($mapCountries) }} Countries

            </span>

        </div>

    </div>

    <div class="card-body p-0">

        <div
            id="worldMap"
            style="height:650px;border-radius:0 0 20px 20px;">
        </div>

    </div>

</div>

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded',()=>{

    const map=L.map('worldMap',{

        zoomControl:true

    }).setView([20,0],2);

    L.tileLayer(

        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',

        {

            attribution:'© OpenStreetMap'

        }

    ).addTo(map);

    const countries=@json($mapCountries);

    countries.forEach(country=>{

        if(

            !country.latitude ||

            !country.longitude

        ){

            return;

        }

        let color="#0d6efd";

        let risk="Stable";

        let score="-";

        if(country.latest_risk){

            risk=country.latest_risk.risk_level;

            score=country.latest_risk.final_score;

            switch(risk){

                case 'safe':

                    color="#28a745";

                    break;

                case 'stable':

                    color="#0d6efd";

                    break;

                case 'alert':

                    color="#ffc107";

                    break;

                case 'dangerous':

                    color="#ff8c00";

                    break;

                case 'critical':

                    color="#dc3545";

                    break;

            }

        }

        let recommendation="-";

        if(country.recommendation){

            recommendation=

                country.recommendation.trade_action;

        }

        let weather="-";

        if(country.latest_weather){

            weather=

                country.latest_weather.weather_status;

        }

        L.circleMarker(

            [

                country.latitude,

                country.longitude

            ],

            {

                radius:7,

                color:color,

                fillColor:color,

                fillOpacity:1,

                weight:2

            }

        )

        .bindPopup(`

            <div style="width:240px">

                <div class="text-center">

                    <img

                        src="${country.flag}"

                        width="50"

                        class="mb-2"

                    >

                    <h6>

                        <strong>

                        ${country.country_name}

                        </strong>

                    </h6>

                </div>

                <table class="table table-sm table-borderless">

                    <tr>

                        <td><strong>Region</strong></td>

                        <td>${country.region}</td>

                    </tr>

                    <tr>

                        <td><strong>Risk</strong></td>

                        <td>${risk}</td>

                    </tr>

                    <tr>

                        <td><strong>Score</strong></td>

                        <td>${score}</td>

                    </tr>

                    <tr>

                        <td><strong>Weather</strong></td>

                        <td>${weather}</td>

                    </tr>

                    <tr>

                        <td><strong>Currency</strong></td>

                        <td>${country.currency_code}</td>

                    </tr>

                    <tr>

                        <td><strong>Action</strong></td>

                        <td>

                            ${recommendation}

                        </td>

                    </tr>

                </table>

            </div>

        `)

        .addTo(map);

    });

});

</script>

@endpush