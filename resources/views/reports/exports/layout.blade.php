<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Trade Report')</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { border-bottom: 2px solid #F59E0B; padding-bottom: 10px; margin-bottom: 20px; }
        .logo-container { float: left; width: 50%; }
        .logo { font-size: 20px; font-weight: bold; color: #F59E0B; margin-bottom: 5px; }
        .report-title { font-size: 16px; font-weight: bold; color: #333; }
        .meta-container { float: right; width: 50%; text-align: right; }
        .report-date { font-size: 11px; color: #666; }
        .clear { clear: both; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: auto; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #f8f9fa; font-weight: bold; color: #333; text-transform: uppercase; font-size: 11px; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-orange { color: #F59E0B; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; color: #fff; display: inline-block; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-success { background-color: #198754; }
        .bg-primary { background-color: #0d6efd; }
        .bg-secondary { background-color: #6c757d; }
        
        .footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 30px; border-top: 1px solid #ddd; padding-top: 10px; text-align: right; font-size: 10px; color: #666; }
        .page-break { page-break-after: always; }
        
        /* Web Preview specific overrides */
        @media screen {
            body { background: #f3f4f6; padding: 40px; font-family: system-ui, -apple-system, sans-serif; }
            .document-container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 8px; }
            .preview-actions { max-width: 1000px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; }
            .btn { display: inline-flex; align-items: center; padding: 8px 16px; color: #fff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; }
            .btn-secondary { background: #6c757d; }
            .btn-secondary:hover { background: #5c636a; }
            .btn-danger { background: #dc3545; }
            .btn-danger:hover { background: #bb2d3b; }
            .btn-success { background: #198754; }
            .btn-success:hover { background: #157347; }
            .gap-2 { gap: 0.5rem; display: flex; }
            .footer { position: static; margin-top: 40px; }
            table { width: 100%; }
        }
    </style>
</head>
<body>
    @if(!isset($isPdf) || !$isPdf)
    <div class="preview-actions">
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                &larr; Back to Reports
            </a>
        </div>
        <div class="gap-2">
            @yield('export_buttons')
        </div>
    </div>
    <div class="document-container">
    @endif

    <div class="header">
        <div class="logo-container">
            <div class="logo">Global Trade Intelligence Platform</div>
            <div class="report-title">@yield('title', 'Trade Report')</div>
        </div>
        <div class="meta-container">
            <div class="report-date">Generated: {{ now()->format('d M Y, H:i') }}</div>
            <div class="report-date">Report ID: {{ strtoupper(uniqid('REP-')) }}</div>
        </div>
        <div class="clear"></div>
    </div>

    @yield('content')

    <div class="footer">
        Global Trade Intelligence Platform - Page 
        @if(isset($isPdf) && $isPdf)
            <script type="text/php">
                if (isset($pdf)) {
                    $pdf->page_text($pdf->get_width() - 80, $pdf->get_height() - 25, "{PAGE_NUM} of {PAGE_COUNT}", null, 8, array(0.4,0.4,0.4));
                }
            </script>
        @else
            1 of 1
        @endif
    </div>

    @if(!isset($isPdf) || !$isPdf)
    </div>
    @endif
</body>
</html>
