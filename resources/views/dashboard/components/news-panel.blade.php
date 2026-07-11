<div class="card border-0 shadow-sm rounded-4 h-100">

    <div class="card-header bg-white border-0">

        <h5 class="fw-bold mb-1">
            📰 Trade News Intelligence
        </h5>

        <small class="text-muted">
            Latest global trade related news analysis
        </small>

    </div>


    <div class="card-body">


        @forelse($newsPanel as $news)


            <div class="border rounded-3 p-3 mb-3">


                <h6 class="fw-bold">

                    {{ $news->title }}

                </h6>


                <p class="small text-muted mb-2">

                    {{ Str::limit($news->description,120) }}

                </p>



                <div class="d-flex justify-content-between">


                    <span class="badge bg-secondary">

                        {{ ucfirst($news->category) }}

                    </span>



                    @if($news->sentiment == 'positive')

                        <span class="badge bg-success">
                            Positive
                        </span>

                    @elseif($news->sentiment == 'negative')

                        <span class="badge bg-danger">
                            Negative
                        </span>

                    @else

                        <span class="badge bg-warning text-dark">
                            Neutral
                        </span>

                    @endif


                </div>


            </div>


        @empty


            <div class="text-center text-muted py-3">

                No news available.

            </div>


        @endforelse


    </div>

</div>