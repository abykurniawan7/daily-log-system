<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Projects</title>
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
            border-bottom: 2px solid #1a56db;
            padding-bottom: 8px;
            position: relative;
        }
        
        .logo {
            position: absolute;
            top: 5px;
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
            color: #1a56db;
            font-weight: bold;
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        
        table th {
            background: #1a56db;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
        }
        
        table td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            font-size: 8px;
            vertical-align: top;
        }
        
        table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .badge {
            padding: 2px 5px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
        }
        
        .status-progress { background-color: #dbeafe; color: #1e40af; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-done { background-color: #d1fae5; color: #065f46; }
        
        .urgency-low { background-color: #d1fae5; color: #065f46; }
        .urgency-medium { background-color: #fef3c7; color: #92400e; }
        .urgency-high { background-color: #fed7aa; color: #9a3412; }
        .urgency-very-high { background-color: #fecaca; color: #991b1b; }
        
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
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
            <h1>LAPORAN DATA PROJECTS</h1>
            <p>WorkLog PGD - Bank BPD Bali | Divisi Pengembangan Digital</p>
        </div>
    </div>

    <div class="info">
        <div class="info-row"><strong>Dicetak:</strong> {{ now()->format('d F Y, H:i') }} WITA | <strong>Total Projects:</strong> {{ $projects->count() }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 22%;">Nama Project</th>
                <th style="width: 13%;">Pemilik</th>
                <th style="width: 12%;">PIC</th>
                <th style="width: 9%;">Status</th>
                <th style="width: 9%;">Urgensi</th>
                <th style="width: 10%;">Inisiasi</th>
                <th style="width: 10%;">Target</th>
                <th style="width: 12%;">Sifat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $index => $project)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $project->nama_project }}</strong></td>
                    <td>{{ $project->pemilikProject->nama_divisi }}</td>
                    <td>{{ $project->picProyek->name }}</td>
                    <td>
                        <span class="badge status-{{ strtolower($project->status) }}">
                            {{ $project->status }}
                        </span>
                    </td>
                    <td>
                        <span class="badge urgency-{{ strtolower(str_replace(' ', '-', $project->urgensi)) }}">
                            {{ $project->urgensi }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($project->tanggal_inisiasi)->format('d/m/Y') }}</td>
                    <td>{{ $project->target_implementasi }}</td>
                    <td>{{ $project->sifat_project }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh Sistem WorkLog PGD - Bank BPD Bali</p>
        <p style="margin-top: 3px;">Berisi {{ $projects->count() }} projects | Dicetak: {{ now()->format('d F Y, H:i') }} WITA</p>
    </div>
</body>
</html>