<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('projects.project_report') }} - {{ $project->nama_project }}</title>
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
            border-bottom: 2px solid #0F5132;
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
            color: #0F5132;
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
        
        .project-info {
            background-color: #f0fdf4;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 3px;
            border-left: 3px solid #0F5132;
        }
        
        .project-info-grid {
            display: table;
            width: 100%;
        }
        
        .project-info-item {
            display: table-row;
        }
        
        .project-info-label {
            display: table-cell;
            font-weight: bold;
            padding: 3px 8px 3px 0;
            width: 140px;
            color: #0F5132;
            font-size: 8px;
        }
        
        .project-info-value {
            display: table-cell;
            padding: 3px 0;
            font-size: 8px;
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
            color: #0F5132;
        }
        
        .summary-label {
            font-size: 7px;
            color: #666;
            margin-top: 3px;
        }
        
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0F5132;
            border-bottom: 2px solid #0F5132;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        
        th {
            background-color: #0F5132;
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
        
        .no-data {
            text-align: center;
            padding: 15px;
            color: #999;
            font-style: italic;
            font-size: 9px;
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
            <h1>{{ __('projects.project_report') }}</h1>
            <p>WorkLog PGD - Bank BPD Bali | Divisi Pengembangan Digital</p>
        </div>
    </div>

    <div class="info">
        <div class="info-row"><strong>Dicetak:</strong> {{ now()->format('d F Y, H:i') }} WITA | <strong>Oleh:</strong> {{ auth()->user()->name }}</div>
    </div>

    <div class="project-info">
        <div class="project-info-grid">
            <div class="project-info-item">
                <div class="project-info-label">{{ __('projects.project_name_label') }}:</div>
                <div class="project-info-value"><strong>{{ $project->nama_project }}</strong></div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">{{ __('projects.project_owner') }}:</div>
                <div class="project-info-value">{{ $project->pemilikProject->nama_divisi }}</div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">{{ __('projects.pic_project') }}:</div>
                <div class="project-info-value">{{ $project->picProyek->name }}</div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">{{ __('projects.status') }}:</div>
                <div class="project-info-value">
                    @php
                        $statusLabels = [
                            'Progress' => __('projects.in_progress'),
                            'Pending' => __('projects.pending_status'),
                            'Done' => __('projects.completed')
                        ];
                    @endphp
                    <span class="badge status-{{ strtolower($project->status) }}">
                        {{ $statusLabels[$project->status] }}
                    </span>
                </div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">{{ __('projects.urgency') }}:</div>
                <div class="project-info-value">
                    @php
                        $urgencyLabels = [
                            'Low' => __('projects.low'),
                            'Medium' => __('projects.medium'),
                            'High' => __('projects.high'),
                            'Very High' => __('projects.very_high')
                        ];
                    @endphp
                    <span class="badge urgency-{{ strtolower(str_replace(' ', '-', $project->urgensi)) }}">
                        {{ $urgencyLabels[$project->urgensi] }}
                    </span>
                </div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">{{ __('projects.initiation_date') }}:</div>
                <div class="project-info-value">{{ $project->tanggal_inisiasi->format('d F Y') }}</div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">{{ __('projects.target_implementation') }}:</div>
                <div class="project-info-value">{{ $project->target_implementasi }}</div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">{{ __('projects.project_nature') }}:</div>
                <div class="project-info-value">{{ $project->sifat_project }}</div>
            </div>
            @if($project->deskripsi)
            <div class="project-info-item">
                <div class="project-info-label">{{ __('projects.description') }}:</div>
                <div class="project-info-value">{{ $project->deskripsi }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $activitiesSuperadmin->count() + $activitiesPGB->count() + $activitiesPKJ->count() }}</div>
                <div class="summary-label">Total Aktivitas</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activitiesSuperadmin->count() }}</div>
                <div class="summary-label">Supervisi</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activitiesPGB->count() }}</div>
                <div class="summary-label">{{ __('projects.pgb') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activitiesPKJ->count() }}</div>
                <div class="summary-label">{{ __('projects.pkj') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activitiesSuperadmin->where('status', 'Progress')->count() + $activitiesPGB->where('status', 'Progress')->count() + $activitiesPKJ->where('status', 'Progress')->count() }}</div>
                <div class="summary-label">{{ __('projects.progress') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activitiesSuperadmin->where('status', 'Pending')->count() + $activitiesPGB->where('status', 'Pending')->count() + $activitiesPKJ->where('status', 'Pending')->count() }}</div>
                <div class="summary-label">{{ __('projects.pending') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activitiesSuperadmin->where('status', 'Done')->count() + $activitiesPGB->where('status', 'Done')->count() + $activitiesPKJ->where('status', 'Done')->count() }}</div>
                <div class="summary-label">{{ __('projects.done') }}</div>
            </div>
        </div>
    </div>

    {{-- ✅ NEW SECTION: Superadmin/Kadiv Activities --}}
    @if($activitiesSuperadmin->count() > 0)
    <div class="section">
        <div class="section-title">Supervisi ({{ $activitiesSuperadmin->count() }} {{ __('projects.total_activities') }})</div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">{{ __('projects.no') }}</th>
                    <th style="width: 22%;">{{ __('projects.activity_name') }}</th>
                    <th style="width: 12%;">{{ __('projects.person_in_charge') }}</th>
                    <th style="width: 12%;">{{ __('projects.date') }}</th>
                    <th style="width: 35%;">{{ __('projects.description') }}</th>
                    <th style="width: 8%;">{{ __('projects.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activitiesSuperadmin->values() as $activity)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $activity->nama_aktivitas }}</strong><br>
                            <small style="color: #666;">{{ $activity->jenis_kegiatan }}</small>
                        </td>
                        <td>{{ $activity->user->name }}</td>
                        <td>
                            {{ $activity->tanggal_mulai->format('d/m/Y') }}
                            @if($activity->tanggal_selesai)
                                <br><small>s/d {{ $activity->tanggal_selesai->format('d/m/Y') }}</small>
                            @endif
                        </td>
                        <td>{{ $activity->deskripsi ?? '-' }}</td>
                        <td>
                            @php
                                $statusLabels = [
                                    'Progress' => __('projects.in_progress'),
                                    'Pending' => __('projects.pending_status'),
                                    'Done' => __('projects.completed')
                                ];
                            @endphp
                            <span class="badge status-{{ strtolower($activity->status) }}">
                                {{ $statusLabels[$activity->status] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="section-title">{{ __('projects.pgb') }} ({{ $activitiesPGB->count() }} {{ __('projects.total_activities') }})</div>
        
        @if($activitiesPGB->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 3%;">{{ __('projects.no') }}</th>
                        <th style="width: 22%;">{{ __('projects.activity_name') }}</th>
                        <th style="width: 12%;">{{ __('projects.person_in_charge') }}</th>
                        <th style="width: 12%;">{{ __('projects.date') }}</th>
                        <th style="width: 35%;">{{ __('projects.description') }}</th>
                        <th style="width: 8%;">{{ __('projects.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activitiesPGB->values() as $activity)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $activity->nama_aktivitas }}</strong><br>
                                <small style="color: #666;">{{ $activity->jenis_kegiatan }}</small>
                            </td>
                            <td>{{ $activity->user->name }}</td>
                            <td>
                                {{ $activity->tanggal_mulai->format('d/m/Y') }}
                                @if($activity->tanggal_selesai)
                                    <br><small>s/d {{ $activity->tanggal_selesai->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>{{ $activity->deskripsi ?? '-' }}</td>
                            <td>
                                @php
                                    $statusLabels = [
                                        'Progress' => __('projects.in_progress'),
                                        'Pending' => __('projects.pending_status'),
                                        'Done' => __('projects.completed')
                                    ];
                                @endphp
                                <span class="badge status-{{ strtolower($activity->status) }}">
                                    {{ $statusLabels[$activity->status] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">📭 {{ __('projects.no_activities_pgb') }}</div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">{{ __('projects.pkj') }} ({{ $activitiesPKJ->count() }} {{ __('projects.total_activities') }})</div>
        
        @if($activitiesPKJ->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 3%;">{{ __('projects.no') }}</th>
                        <th style="width: 22%;">{{ __('projects.activity_name') }}</th>
                        <th style="width: 12%;">{{ __('projects.person_in_charge') }}</th>
                        <th style="width: 12%;">{{ __('projects.date') }}</th>
                        <th style="width: 35%;">{{ __('projects.description') }}</th>
                        <th style="width: 8%;">{{ __('projects.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activitiesPKJ as $activity)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $activity->nama_aktivitas }}</strong><br>
                                <small style="color: #666;">{{ $activity->jenis_kegiatan }}</small>
                            </td>
                            <td>{{ $activity->user->name }}</td>
                            <td>
                                {{ $activity->tanggal_mulai->format('d/m/Y') }}
                                @if($activity->tanggal_selesai)
                                    <br><small>s/d {{ $activity->tanggal_selesai->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>{{ $activity->deskripsi ?? '-' }}</td>
                            <td>
                                @php
                                    $statusLabels = [
                                        'Progress' => __('projects.in_progress'),
                                        'Pending' => __('projects.pending_status'),
                                        'Done' => __('projects.completed')
                                    ];
                                @endphp
                                <span class="badge status-{{ strtolower($activity->status) }}">
                                    {{ $statusLabels[$activity->status] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">📭 {{ __('projects.no_activities_pkj') }}</div>
        @endif
    </div>

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh Sistem WorkLog PGD - Bank BPD Bali</p>
        <p style="margin-top: 3px;">Laporan Project: {{ $project->nama_project }} | {{ now()->format('d F Y, H:i') }} WITA</p>
    </div>
</body>
</html>