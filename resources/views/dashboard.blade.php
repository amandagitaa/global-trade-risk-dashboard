<!DOCTYPE html>
<html>
<head>
    <title>Global Trade Risk Dashboard</title>
    <style>
        body{
            font-family: Arial;
            background:#f5f7fa;
            margin:20px;
        }
        .card{
            display:inline-block;
            width:220px;
            background:white;
            padding:20px;
            margin:10px;
            border-radius:12px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }
        table{
            width:100%;
            border-collapse:collapse;
            background:white;
            margin-top:20px;
        }
        th, td{
            border:1px solid #ddd;
            padding:12px;
        }
        th{
            background:#2c3e50;
            color:white;
        }
    </style>
</head>
<body>

<h1>Global Trade Risk Dashboard</h1>

<div class="card">
    <h3>Total Countries</h3>
    <h2>{{ $totalCountries }}</h2>
</div>

<div class="card">
    <h3>Safe Countries</h3>
    <h2>{{ $safeCount }}</h2>
</div>

<div class="card">
    <h3>Alert Countries</h3>
    <h2>{{ $alertCount }}</h2>
</div>

<h2>Risk Scores</h2>

<table>
    <tr>
        <th>Country</th>
        <th>Risk Score</th>
        <th>Level</th>
    </tr>

    @foreach($riskScores as $risk)
    <tr>
        <td>{{ $risk->country->country_name ?? '-' }}</td>
        <td>{{ $risk->final_score }}</td>
        <td>{{ $risk->risk_level }}</td>
    </tr>
    @endforeach
</table>

<h2>Latest News</h2>

<ul>
    @foreach($latestNews as $news)
        <li>{{ $news->title }}</li>
    @endforeach
</ul>

</body>
</html>