<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 0; color: #1f2937; }
        .page { padding: 42px 56px; border: 10px solid #ca1268; min-height: 92vh; }
        .title { text-align: center; font-size: 34px; font-weight: bold; margin-top: 24px; color: #ca1268; }
        .subtitle { text-align: center; font-size: 18px; margin-top: 10px; }
        .name { text-align: center; font-size: 30px; margin: 28px 0 18px; font-weight: bold; }
        .body-text { text-align: center; font-size: 16px; line-height: 1.7; }
        .meta { margin-top: 40px; font-size: 14px; }
        .signatory { margin-top: 60px; width: 280px; }
        .signature-image { max-width: 220px; max-height: 80px; }
        .signature-line { border-top: 1px solid #111827; margin-top: 10px; padding-top: 8px; }
        .logo { text-align: center; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="page">
        <div class="logo">Association for Computer Adaptive Testing in Africa</div>
        <div class="title">Membership Certificate</div>
        <div class="subtitle">This is to certify that</div>
        <div class="name">{{ $member->full_name }}</div>
        <div class="body-text">
            is an active member of ACATA.<br>
            Member ID: <strong>{{ $certificateId }}</strong><br>
            Member since {{ $memberSince }} and issued on {{ $issueDate }}.
        </div>

        <div class="meta">
            Certificate Reference: {{ $certificateId }}
        </div>

        <div class="signatory">
            @if($setting?->signature_data_uri)
                <img class="signature-image" src="{{ $setting->signature_data_uri }}" alt="Signature">
            @endif
            <div class="signature-line">
                <strong>{{ $setting?->signatory_name ?? 'ACATA Secretariat' }}</strong><br>
                <span>{{ $setting?->signatory_title ?? 'Authorized Signatory' }}</span>
            </div>
        </div>
    </div>
</body>
</html>
