<div class="card border-0 shadow-sm rounded-4 h-100">

    <div class="card-header bg-white border-0">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="fw-bold text-orange mb-1">

                    📊 Global Risk Distribution

                </h5>

                <small class="text-muted">

                    Distribution of countries by trade risk level

                </small>

            </div>

        </div>

    </div>

    <div class="card-body">

        <canvas id="riskDistributionChart"
                height="240">
        </canvas>

    </div>

</div>

@push('scripts')

<script>

document.addEventListener("DOMContentLoaded",()=>{

    const labels=[];

    const values=[];

    const colors=[];

    const risk=@json($riskDistribution);

    risk.forEach(item=>{

        labels.push(

            item.risk_level.toUpperCase()

        );

        values.push(

            item.total

        );

        switch(item.risk_level){

            case 'safe':

                colors.push('#28a745');

                break;

            case 'stable':

                colors.push('#0d6efd');

                break;

            case 'alert':

                colors.push('#ffc107');

                break;

            case 'dangerous':

                colors.push('#ff8c00');

                break;

            case 'critical':

                colors.push('#dc3545');

                break;

        }

    });

    new Chart(

        document.getElementById('riskDistributionChart'),

        {

            type:'doughnut',

            data:{

                labels:labels,

                datasets:[{

                    data:values,

                    backgroundColor:colors,

                    borderWidth:2,

                    borderColor:'#ffffff'

                }]

            },

            options:{

                responsive:true,

                plugins:{

                    legend:{

                        position:'bottom'

                    }

                }

            }

        }

    );

});

</script>

@endpush