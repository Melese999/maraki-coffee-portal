<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Maraki Coffee House · Menu</title>
    <style>
        @font-face {
            font-family: 'NotoSans';
            font-style: normal;
            font-weight: normal;
            src: url('{{ resource_path('fonts/NotoSans-Regular.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'NotoSans';
            font-style: normal;
            font-weight: bold;
            src: url('{{ resource_path('fonts/NotoSans-Bold.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'NotoEthiopic';
            font-style: normal;
            font-weight: normal;
            src: url('{{ resource_path('fonts/NotoSansEthiopic-Regular.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'NotoEthiopic';
            font-style: normal;
            font-weight: bold;
            src: url('{{ resource_path('fonts/NotoSansEthiopic-Bold.ttf') }}') format('truetype');
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        @page { margin: 14px; }

        body {
            font-family: 'NotoEthiopic', 'NotoSans', DejaVu Sans, sans-serif;
            color: #1C120C;
            background: #FDFBF7;
            font-size: 12px;
        }

        .page {
            position: relative;
            background: #FDFBF7;
            border: 2.5px solid #1C120C;
            min-height: 1040px;
            padding: 28px 26px 24px;
        }

        .gold-frame {
            position: absolute;
            top: 7px;
            right: 7px;
            bottom: 7px;
            left: 7px;
            border: 1.5px solid #C9A24A;
        }

        .watermark {
            position: absolute;
            top: 42%;
            left: 0;
            width: 100%;
            text-align: center;
            font-family: 'NotoEthiopic', DejaVu Sans, sans-serif;
            font-size: 84px;
            font-weight: bold;
            color: #C45C3E;
            opacity: 0.06;
            z-index: 0;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .brand {
            text-align: center;
            padding: 4px 0 2px;
        }

        .logo-wrap {
            width: 92px;
            height: 92px;
            margin: 0 auto 14px;
            text-align: center;
        }

        .logo-wrap svg {
            width: 92px;
            height: 92px;
        }

        .brand-title {
            font-family: 'NotoSans', DejaVu Sans, sans-serif;
            font-size: 30px;
            font-weight: bold;
            color: #1C120C;
            letter-spacing: -0.5px;
            line-height: 1.15;
        }

        .brand-sub {
            margin-top: 7px;
            font-family: 'NotoSans', DejaVu Sans, sans-serif;
            font-size: 11px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #6B3E1F;
        }

        .tibeb {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0 22px;
        }

        .tibeb td {
            height: 5px;
            padding: 0;
            border: 0;
        }

        .menu-table {
            width: 100%;
            border-collapse: collapse;
        }

        .menu-table tr td {
            padding: 13px 0 11px;
            border-bottom: 1px dotted #C9A24A;
            vertical-align: bottom;
        }

        .menu-table tr:last-child td {
            border-bottom: 0;
        }

        .item-name {
            font-family: 'NotoEthiopic', 'NotoSans', DejaVu Sans, sans-serif;
            font-size: 16px;
            font-weight: bold;
            color: #1C120C;
        }

        .item-unit {
            margin-top: 3px;
            font-family: 'NotoEthiopic', 'NotoSans', DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #8A5A2A;
        }

        .dots {
            border-bottom: 1px dotted rgba(28, 18, 12, 0.28);
            height: 14px;
        }

        .item-price {
            font-family: 'NotoSans', DejaVu Sans, sans-serif;
            font-size: 16px;
            font-weight: bold;
            color: #C45C3E;
            text-align: right;
            white-space: nowrap;
            width: 78px;
            padding-left: 10px !important;
        }

        .empty {
            text-align: center;
            color: #6B3E1F;
            padding: 48px 0;
            font-family: 'NotoEthiopic', 'NotoSans', DejaVu Sans, sans-serif;
        }

        .footer-wrap {
            margin-top: 34px;
            text-align: center;
        }

        .footer-line {
            width: 120px;
            height: 1px;
            background: #C9A24A;
            margin: 0 auto 10px;
        }

        .footer {
            font-family: 'NotoEthiopic', 'NotoSans', DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #6B3E1F;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="gold-frame"></div>
        <div class="watermark">ማራኪ</div>

        <div class="content">
            <header class="brand">
                <div class="logo-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="92" height="92" viewBox="0 0 64 64" fill="none">
                        <circle cx="32" cy="32" r="30" fill="#1C120C"/>
                        <circle cx="32" cy="32" r="27.5" stroke="#D4A017" stroke-width="2.2" fill="none"/>
                        <g transform="translate(12 8)">
                            <path d="M20 6c-4 4-7 8-7 13a7 7 0 0 0 14 0c0-5-3-9-7-13Z" stroke="#D4A017" stroke-width="2.2" stroke-linejoin="round" fill="none"/>
                            <path d="M13 28h14c1.5 3 1.5 5 0 6H13c-1.5-1-1.5-3 0-6Z" fill="#D4A017"/>
                            <circle cx="12" cy="34" r="1.8" fill="#D4A017"/>
                            <circle cx="20" cy="34" r="1.8" fill="#D4A017"/>
                            <circle cx="28" cy="34" r="1.8" fill="#D4A017"/>
                        </g>
                    </svg>
                </div>
                <div class="brand-title">Maraki Coffee House</div>
                <div class="brand-sub">Menu</div>
            </header>

            <table class="tibeb" cellpadding="0" cellspacing="0">
                <tr>
                    @for ($i = 0; $i < 24; $i++)
                        <td style="background: {{ $i % 2 === 0 ? '#D4A017' : '#C45C3E' }};"></td>
                    @endfor
                </tr>
            </table>

            @if ($items->isEmpty())
                <p class="empty">{{ __('No menu items selected.') }}</p>
            @else
                <table class="menu-table">
                    @foreach ($items as $item)
                        <tr>
                            <td style="width: 48%;">
                                <div class="item-name">{{ $item->name }}</div>
                                <div class="item-unit">{{ $item->unit }}</div>
                            </td>
                            <td class="dots">&nbsp;</td>
                            <td class="item-price">{{ \App\Support\Money::format($item->price) }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            <div class="footer-wrap">
                <div class="footer-line"></div>
                <div class="footer">
                    {{ $printedAt->format('M j, Y') }} · {{ $items->count() }} {{ __('Items') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
