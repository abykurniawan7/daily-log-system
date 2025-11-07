<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Aktivitas - WorkLog PGD</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #059669;
            padding-bottom: 8px;
            position: relative;
        }
        
        .logo {
            position: absolute;
            top: -10px;
            left: 10px;
            width: 50px;
            height: auto;
        }
        
        .header-content {
            padding-left: 0;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #059669;
        }
        
        .header p {
            margin: 3px 0;
            font-size: 9px;
            color: #666;
        }
        
        .info {
            margin-bottom: 10px;
            font-size: 8px;
        }
        
        .info-row {
            margin: 3px 0;
        }
        
        .filter-section {
            background-color: #f0fdf4;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 3px;
            border-left: 3px solid #059669;
        }
        
        .filter-title {
            font-weight: bold;
            color: #059669;
            font-size: 9px;
            margin-bottom: 5px;
        }
        
        .filter-item {
            display: inline-block;
            background-color: white;
            padding: 2px 6px;
            margin: 2px;
            border-radius: 2px;
            font-size: 8px;
            border: 1px solid #d1fae5;
        }
        
        .filter-label {
            font-weight: bold;
            color: #047857;
        }
        
        .summary {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 5px;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #059669;
        }
        
        .summary-label {
            font-size: 8px;
            color: #666;
            margin-top: 3px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        
        th {
            background-color: #059669;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
        }
        
        td {
            padding: 5px 4px;
            border-bottom: 1px solid #ddd;
            font-size: 8px;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .badge {
            padding: 2px 5px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
            white-space: nowrap;
        }
        
        .status-progress { background-color: #dbeafe; color: #1e40af; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-done { background-color: #d1fae5; color: #065f46; }
        
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('images/bank-bpd-bali-seeklogo.png')))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/bank-bpd-bali-seeklogo.png'))) }}" 
                alt="Bank BPD Bali" 
                class="logo">
        @endif
        
        <div class="header-content">
            <h1>DAFTAR AKTIVITAS KARYAWAN</h1>
            <p>WorkLog PGD - Bank BPD Bali | Divisi Pengembangan Digital</p>
        </div>
    </div>

    <div class="info">
        <div class="info-row"><strong>Dicetak:</strong> {{ now()->format('d F Y, H:i') }} WITA | <strong>Oleh:</strong> {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</div>
    </div>

    @if(!empty($filterInfo))
        <div class="filter-section">
            <div class="filter-title">Filter yang Diterapkan:</div>
            @foreach($filterInfo as $label => $value)
                <div class="filter-item">
                    <span class="filter-label">{{ $label }}:</span> {{ $value }}
                </div>
            @endforeach
        </div>
    @endif

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $activities->count() }}</div>
                <div class="summary-label">Total Aktivitas</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activities->where('status', 'Progress')->count() }}</div>
                <div class="summary-label">Progress</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activities->where('status', 'Pending')->count() }}</div>
                <div class="summary-label">Pending</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activities->where('status', 'Done')->count() }}</div>
                <div class="summary-label">Done</div>
            </div>
        </div>
    </div>

    @if($activities->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 18%;">Nama Aktivitas</th>
                    <th style="width: 18%;">Project</th>
                    <th style="width: 12%;">PIC</th>
                    <th style="width: 7%;">Bagian</th>
                    <th style="width: 9%;">Mulai</th>
                    <th style="width: 9%;">Selesai</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 16%;">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $index => $activity)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $activity->nama_aktivitas }}</strong><br>
                            <small style="color: #666;">{{ $activity->jenis_kegiatan }}</small>
                        </td>
                        <td>
                            {{ $activity->project->nama_project }}<br>
                            <small style="color: #666;">{{ $activity->project->pemilikProject->nama_divisi }}</small>
                        </td>
                        <td>{{ $activity->user->name }}</td>
                        <td>
                            <span class="badge" style="background-color: {{ $activity->user->bagian === 'PKJ' ? '#f3e8ff' : '#fef3c7' }}; color: {{ $activity->user->bagian === 'PKJ' ? '#6b21a8' : '#92400e' }};">
                                {{ $activity->user->bagian }}
                            </span>
                        </td>
                        <td>{{ $activity->tanggal_mulai->format('d/m/Y') }}</td>
                        <td>{{ $activity->tanggal_selesai ? \Carbon\Carbon::parse($activity->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                        <td>
                            <span class="badge status-{{ strtolower($activity->status) }}">
                                {{ $activity->status }}
                            </span>
                        </td>
                        <td>{{ \Str::limit($activity->deskripsi ?? '-', 80) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p style="font-size: 11px; margin: 0;">📭 Tidak ada data aktivitas ditemukan</p>
        </div>
    @endif

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh Sistem WorkLog PGD - Bank BPD Bali</p>
        <p style="margin-top: 3px;">Berisi {{ $activities->count() }} aktivitas | Dicetak: {{ now()->format('d F Y, H:i') }} WITA</p>
    </div>
</body>
</html>