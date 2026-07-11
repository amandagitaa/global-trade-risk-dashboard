<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>
        @yield('title','Global Trade Risk Intelligence Platform')
    </title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Leaflet --}}
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet/dist/leaflet.css"/>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>

        body{

            background:#F8F9FA;

            font-family:
            "Segoe UI",
            sans-serif;

        }

        /*
        -----------------------------------
        Sidebar
        -----------------------------------
        */

        .sidebar{

            width:260px;

            position:fixed;

            top:0;

            left:0;

            height:100vh;

            background:#ffffff;

            border-right:1px solid #ececec;

            padding:25px;

            overflow-y:auto;

            z-index:1000;

        }

        .logo{

            font-size:23px;

            font-weight:bold;

            color:#F97316;

            margin-bottom:35px;

        }

        .menu-title{

            font-size:11px;

            color:#9ca3af;

            margin-top:18px;

            margin-bottom:10px;

            text-transform:uppercase;

            letter-spacing:1px;

        }

        .sidebar a{

            display:flex;

            align-items:center;

            gap:10px;

            text-decoration:none;

            color:#374151;

            padding:12px 15px;

            border-radius:12px;

            margin-bottom:6px;

            transition:.25s;

        }

        .sidebar a:hover{

            background:#FFF7ED;

            color:#F97316;

        }

        .sidebar .active{

            background:#F97316;

            color:white;

        }

        /*
        -----------------------------------
        Main Content
        -----------------------------------
        */

        .main{

            margin-left:260px;

            min-height:100vh;

        }

        /*
        -----------------------------------
        Navbar
        -----------------------------------
        */

        .topbar{

            background:white;

            padding:18px 30px;

            border-bottom:1px solid #ececec;

            display:flex;

            justify-content:space-between;

            align-items:center;

        }

        .page-title{

            font-size:22px;

            font-weight:700;

            color:#1f2937;

        }

        /*
        -----------------------------------
        Cards
        -----------------------------------
        */

        .dashboard-card{

            border:none;

            border-radius:18px;

            box-shadow:0 6px 18px rgba(0,0,0,.05);

        }

        .card-icon{

            width:60px;

            height:60px;

            border-radius:50%;

            background:#FFF7ED;

            color:#F97316;

            display:flex;

            justify-content:center;

            align-items:center;

            font-size:28px;

        }

        .card-value{

            font-size:30px;

            font-weight:bold;

            color:#111827;

        }

        .card-title{

            color:#6b7280;

        }

        /*
        -----------------------------------
        Panel
        -----------------------------------
        */

        .panel{

            background:white;

            border:none;

            border-radius:18px;

            box-shadow:0 5px 15px rgba(0,0,0,.05);

            padding:22px;

            margin-bottom:25px;

        }

        .panel-title{

            font-size:18px;

            font-weight:bold;

            color:#1f2937;

            margin-bottom:18px;

        }

        /*
        -----------------------------------
        Badge
        -----------------------------------
        */

        .badge-orange{

            background:#F97316;

            color:white;

        }

    </style>

</head>

<body>

@include('layouts.sidebar')

<div class="main">

@include('layouts.navbar')

<div class="container-fluid p-4">

@yield('content')

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@stack('scripts')

</body>

</html>