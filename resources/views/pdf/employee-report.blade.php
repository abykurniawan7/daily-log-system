<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('employees.performance_report') }} - {{ $employee->name }}</title>
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
        
        .profile {
            background-color: #f0fdf4;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 3px;
            border-left: 3px solid #0F5132;
        }
        
        .profile-grid {
            display: table;
            width: 100%;
        }
        
        .profile-item {
            display: table-row;
        }
        
        .profile-label {
            display: table-cell;
            font-weight: bold;
            padding: 3px 8px 3px 0;
            width: 120px;
            color: #0F5132;
            font-size: 8px;
        }
        
        .profile-value {
            display: table-cell;
            padding: 3px 0;
            font-size: 8px;
        }
        
        .stats {
            margin-bottom: 10px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
        }
        
        .stats-item {
            display: table-cell;
            text-align: center;
            padding: 8px;
            background-color: #f0fdf4;
            border-right: 1px solid #d1fae5;
        }
        
        .stats-item:last-child {
            border-right: none;
        }
        
        .stats-value {
            font-size: 20px;
            font-weight: bold;
            color: #0F5132;
        }
        
        .stats-label {
            font-size: 8px;
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
            display: inline-block;
        }
        
        .status-progress { background-color: #dbeafe; color: #1e40af; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-done { background-color: #d1fae5; color: #065f46; }
        
        /* ✅ FIX: Status Summary Table - FULL WIDTH */
        .status-summary {
            width: 100%;
            margin: 10px 0;
        }
        
        .status-summary th {
            text-align: center;
            padding: 8px 12px;
        }
        
        .status-summary td {
            text-align: center;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 9px;
        }
        
        .status-summary .category-label {
            text-align: left;
            font-weight: bold;
            background-color: #f0fdf4;
            padding-left: 15px;
        }
        
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
            padding: 20px;
            color: #999;
            font-style: italic;
            font-size: 9px;
            background-color: #f9fafb;
            border: 1px dashed #ddd;
            border-radius: 3px;
        }
        
        /* ✅ Section Icon Replacement */
        .section-icon {
            display: inline-block;
            width: 14px;
            height: 14px;
            background-color: #0F5132;
            color: white;
            text-align: center;
            line-height: 14px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            margin-right: 5px;
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
            <h1>{{ __('employees.performance_report') }}</h1>
            <p>WorkLog PGD - {{ __('employees.bank_name') }} | Divisi Pengembangan Digital</p>
        </div>
    </div>

    <div class="info">
        <div class="info-row">
            <strong>Dicetak:</strong> {{ now()->format('d F Y, H:i') }} WITA | 
            <strong>Oleh:</strong> {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
        </div>
    </div>

    <!-- ✅ PROFILE SECTION -->
    <div class="profile">
        <div class="profile-grid">
            <div class="profile-item">
                <div class="profile-label">{{ __('employees.full_name') }}:</div>
                <div class="profile-value">{{ $employee->name }}</div>
            </div>
            <div class="profile-item">
                <div class="profile-label">{{ __('employees.email') }}:</div>
                <div class="profile-value">{{ $employee->email }}</div>
            </div>
            <div class="profile-item">
                <div class="profile-label">{{ __('employees.role') }}:</div>
                <div class="profile-value">
                    {{ $employee->role === 'karyawan' ? __('employees.employee_pgb_role') : __('employees.licensing_pkj_role') }}
                </div>
            </div>
            @if($employee->bagian)
            <div class="profile-item">
                <div class="profile-label">{{ __('employees.section') }}:</div>
                <div class="profile-value">{{ $employee->bagian }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- ✅ STATS CARDS -->
    <div class="stats">
        <div class="stats-grid">
            <div class="stats-item">
                <div class="stats-value">{{ $stats['total_projects'] }}</div>
                <div class="stats-label">{{ __('employees.total_projects') }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ $stats['total_activities'] }}</div>
                <div class="stats-label">{{ __('employees.total_activities') }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ $stats['completion_rate'] }}%</div>
                <div class="stats-label">{{ __('employees.completion_rate') }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ $stats['project_completion_rate'] }}%</div>
                <div class="stats-label">{{ __('employees.projects') }} {{ __('employees.completed') }}</div>
            </div>
        </div>
    </div>

    <!-- ✅ STATUS SUMMARY TABLE - FIXED -->
    <div class="section">
        <div class="section-title">
            Rincian Status
        </div>
        <table class="status-summary">
            <thead>
                <tr>
                    <th style="text-align: left; width: 30%;">Kategori</th>
                    <th style="width: 23%;">{{ __('employees.in_progress') }}</th>
                    <th style="width: 23%;">{{ __('employees.pending') }}</th>
                    <th style="width: 24%;">{{ __('employees.completed') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="category-label">{{ __('employees.projects') }}</td>
                    <td>{{ $stats['projects_progress'] }}</td>
                    <td>{{ $stats['projects_pending'] }}</td>
                    <td>{{ $stats['projects_done'] }}</td>
                </tr>
                <tr>
                    <td class="category-label">{{ __('employees.activities') }}</td>
                    <td>{{ $stats['activities_progress'] }}</td>
                    <td>{{ $stats['activities_pending'] }}</td>
                    <td>{{ $stats['activities_done'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ✅ PROJECTS TABLE - FIXED FIELDS -->
    <div class="section">
        <div class="section-title">
            {{ __('employees.projects_worked_on') }}
        </div>
        @if($employee->projects->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 3%;">{{ __('employees.no') }}</th>
                        <th style="width: 28%;">{{ __('employees.project_name') }}</th>
                        <th style="width: 16%;">Divisi Pemilik</th>
                        <th style="width: 10%;">{{ __('employees.start_date') }}</th>
                        <th style="width: 10%;">Target</th>
                        <th style="width: 8%;">{{ __('employees.status') }}</th>
                        <th style="width: 25%;">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employee->projects as $index => $project)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $project->nama_project }}</strong></td>
                            <td>{{ $project->pemilikProject->nama_divisi ?? '-' }}</td>
                            <td>{{ $project->tanggal_inisiasi->format('d/m/Y') }}</td>
                            <td>{{ $project->target_implementasi }}</td>
                            <td>
                                <span class="badge status-{{ strtolower($project->status) }}">
                                    @if($project->status === 'Done')
                                        {{ __('employees.completed') }}
                                    @elseif($project->status === 'Progress')
                                        {{ __('employees.in_progress') }}
                                    @else
                                        {{ __('employees.pending') }}
                                    @endif
                                </span>
                            </td>
                            <td>{{ \Str::limit($project->deskripsi ?? '-', 80) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">{{ __('employees.no_projects') }}</div>
        @endif
    </div>

    <!-- ✅ ACTIVITIES TABLE - FIXED -->
    <div class="section">
        <div class="section-title">
            Aktivitas Terbaru (5 Terakhir)
        </div>
        @if($recentActivities->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 3%;">{{ __('employees.no') }}</th>
                        <th style="width: 23%;">Nama Aktivitas</th>
                        <th style="width: 20%;">{{ __('employees.project_name') }}</th>
                        <th style="width: 9%;">{{ __('employees.start_date') }}</th>
                        <th style="width: 9%;">Tanggal Selesai</th>
                        <th style="width: 8%;">{{ __('employees.status') }}</th>
                        <th style="width: 28%;">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentActivities as $index => $activity)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $activity->nama_aktivitas }}</strong><br>
                                <small style="color: #666;">{{ $activity->jenis_kegiatan }}</small>
                            </td>
                            <td>{{ $activity->project->nama_project }}</td>
                            <td>{{ $activity->tanggal_mulai->format('d/m/Y') }}</td>
                            <td>
                                @if($activity->tanggal_selesai)
                                    {{ \Carbon\Carbon::parse($activity->tanggal_selesai)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge status-{{ strtolower($activity->status) }}">
                                    @if($activity->status === 'Done')
                                        {{ __('employees.completed') }}
                                    @elseif($activity->status === 'Progress')
                                        {{ __('employees.in_progress') }}
                                    @else
                                        {{ __('employees.pending') }}
                                    @endif
                                </span>
                            </td>
                            <td>{{ \Str::limit($activity->deskripsi ?? '-', 70) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">Belum ada aktivitas tercatat</div>
        @endif
    </div>

    <div class="footer">
        <p>{{ __('employees.document_footer') }} | {{ __('employees.bank_name') }}</p>
        <p style="margin-top: 3px;">{{ __('employees.generated_date') }}: {{ now()->format('d F Y, H:i') }} WITA</p>
    </div>
</body>
</html> 