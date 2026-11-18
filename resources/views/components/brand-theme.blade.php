@php
    $brandC1 = $siteTitleColor1 ?? '#003366';
    $brandC2 = $siteTitleColor2 ?? '#ff6600';
    $cleanHex1 = ltrim($brandC1, '#');
    if (strlen($cleanHex1) === 3) {
        $r1 = hexdec(substr($cleanHex1, 0, 1) . substr($cleanHex1, 0, 1));
        $g1 = hexdec(substr($cleanHex1, 1, 1) . substr($cleanHex1, 1, 1));
        $b1 = hexdec(substr($cleanHex1, 2, 1) . substr($cleanHex1, 2, 1));
    } elseif (strlen($cleanHex1) === 6) {
        $r1 = hexdec(substr($cleanHex1, 0, 2));
        $g1 = hexdec(substr($cleanHex1, 2, 2));
        $b1 = hexdec(substr($cleanHex1, 4, 2));
    } else {
        $r1 = 0; $g1 = 51; $b1 = 102;
    }
    $brandC1Rgb = "$r1, $g1, $b1";
@endphp
<style id="gk-dynamic-brand-colors">
    :root, [data-bs-theme="light"], [data-bs-theme="dark"], html {
        --primary: {{ $brandC1 }} !important;
        --primary-light: {{ $brandC1 }} !important;
        --secondary: {{ $brandC2 }} !important;
        --accent: {{ $brandC2 }} !important;
        --bs-primary: {{ $brandC1 }} !important;
        --bs-primary-rgb: {{ $brandC1Rgb }} !important;
    }
    .btn-primary-custom,
    .btn-primary:not(.btn-outline-primary) {
        background-color: {{ $brandC1 }} !important;
        border-color: {{ $brandC1 }} !important;
    }
    .btn-primary-custom:hover,
    .btn-primary:not(.btn-outline-primary):hover {
        opacity: 0.92;
        background-color: {{ $brandC1 }} !important;
        border-color: {{ $brandC1 }} !important;
    }
    .btn-outline-custom {
        color: {{ $brandC1 }} !important;
        border-color: {{ $brandC1 }} !important;
    }
    .btn-outline-custom:hover {
        background-color: {{ $brandC1 }} !important;
        color: #ffffff !important;
    }
    .sidebar-link:hover, .sidebar-link.active {
        color: {{ $brandC1 }} !important;
        border-right-color: {{ $brandC1 }} !important;
    }
    .page-label, .section-label {
        color: {{ $brandC1 }} !important;
    }
    .nav-link-custom.active, .nav-link-custom:hover {
        color: {{ $brandC1 }} !important;
    }
    .gk-bottom-nav-item.active {
        color: {{ $brandC1 }} !important;
    }
</style>
