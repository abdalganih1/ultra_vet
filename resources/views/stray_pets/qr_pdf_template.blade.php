<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة رموز QR للحيوانات الشاردة</title>
    <style>
        body {
            font-family: 'dejavu sans', sans-serif; /* يدعم العربية في Dompdf */
            direction: rtl;
            text-align: right;
        }
        .page {
            width: 210mm; /* A4 width */
            height: 297mm; /* A4 height */
            margin: 0;
            padding: 10mm;
            box-sizing: border-box;
            page-break-after: always;
        }
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 أعمدة */
            gap: 10mm; /* مسافة بين الـ QR Codes */
            width: 100%;
            height: 100%;
        }
        .qr-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 1px solid #eee;
            padding: 5mm;
            box-sizing: border-box;
        }
        .qr-item img {
            max-width: 60mm; /* حجم مناسب للطباعة */
            height: auto;
            margin-bottom: 5mm;
        }
        .qr-item p {
            font-size: 10pt;
            margin: 0;
            line-height: 1.2;
        }
        .page:last-child {
            page-break-after: avoid;
        }
    </style>
</head>
<body>
    @foreach($chunks as $pageQRs)
    <div class="page">
        <div class="qr-grid">
            @foreach($pageQRs as $qr)
            <div class="qr-item">
                <img src="{{ $qr['qr_data_uri'] }}" alt="{{ $qr['name'] }}">
                <p>{{ $qr['name'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</body>
</html>