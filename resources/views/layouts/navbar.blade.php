<div class="topbar">

    {{-- Left --}}
    <div>

        <div class="page-title">

            Global Trade Risk Intelligence Platform

        </div>

        <small class="text-muted">

            Monitor global trade risks and recommendations in real time.

        </small>

    </div>

    {{-- Right --}}
    <div class="d-flex align-items-center gap-3">

        {{-- Search --}}
        <div class="input-group" style="width:280px;">

            <span class="input-group-text bg-white">

                <i class="bi bi-search"></i>

            </span>

            <input
                type="text"
                class="form-control"
                placeholder="Search country...">

        </div>

        {{-- Last Sync --}}
        <div class="text-end">

            <small class="text-muted d-block">

                Last Synchronization

            </small>

            <strong>

                {{ now()->format('d M Y H:i') }}

            </strong>

        </div>

        {{-- Notification --}}
        <button class="btn btn-light position-relative">

            <i class="bi bi-bell fs-5"></i>

            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                3

            </span>

        </button>

        {{-- Sync --}}
        <button class="btn btn-warning text-white">

            <i class="bi bi-arrow-repeat"></i>

            Sync Data

        </button>

    </div>

</div>