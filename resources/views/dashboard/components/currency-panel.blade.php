<div class="card border-0 shadow-sm rounded-4 h-100">


    <div class="card-header bg-white border-0">


        <div class="d-flex justify-content-between align-items-center">


            <div>

                <h5 class="fw-bold text-orange mb-1">

                    💱 Currency Risk Monitoring

                </h5>


                <small class="text-muted">

                    Latest currency movements impacting international trade

                </small>


            </div>


            <span class="badge bg-success">

                {{ count($currencyPanel) }} Records

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


                    <th style="width:18%">
                        Currency
                    </th>


                    <th style="width:20%">
                        Change
                    </th>


                    <th style="width:22%" class="text-center">

                        Trade Risk

                    </th>


                </tr>


                </thead>




                <tbody>



                @forelse($currencyPanel as $currency)



                    @php


                        $change = $currency->change_percentage;



                        if(abs($change) > 8){


                            $status = 'Critical Risk';

                            $badge = 'danger';



                        }elseif(abs($change) > 5){


                            $status = 'High Risk';

                            $badge = 'warning';



                        }elseif(abs($change) > 2){


                            $status = 'Medium Risk';

                            $badge = 'primary';



                        }else{


                            $status = 'Low Risk';

                            $badge = 'success';


                        }



                    @endphp





                    <tr>




                        <td>


                            <div class="d-flex align-items-center">



                                <img

                                    src="{{ $currency->country->flag }}"

                                    width="36"

                                    class="rounded shadow-sm me-3">



                                <div>


                                    <div class="fw-semibold">


                                        {{ $currency->country->country_name }}


                                    </div>



                                    <small class="text-muted">


                                        {{ strtoupper($currency->country->country_code) }}


                                    </small>



                                </div>



                            </div>


                        </td>





                        <td>


                            <strong>


                                {{ strtoupper($currency->currency_code) }}


                            </strong>


                        </td>





                        <td>



                            @if($change >= 0)


                                <span class="text-danger fw-semibold">


                                    ▲ {{ number_format($change,2) }}%


                                </span>



                            @else


                                <span class="text-success fw-semibold">


                                    ▼ {{ number_format(abs($change),2) }}%


                                </span>



                            @endif



                        </td>






                        <td class="text-center">


                            <span class="badge bg-{{ $badge }} px-3 py-2">


                                {{ $status }}


                            </span>


                        </td>




                    </tr>





                @empty



                    <tr>


                        <td colspan="4"

                            class="text-center py-5 text-muted">


                            No currency risk information available.


                        </td>


                    </tr>



                @endforelse




                </tbody>


            </table>


        </div>


    </div>


</div>