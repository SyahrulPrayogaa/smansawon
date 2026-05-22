<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 12px;
            margin-top: 4px;
        }

        .meta {
            margin-bottom: 16px;
        }

        .meta td {
            padding: 3px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #d1d5db;
            padding: 6px;
        }

        table.data th {
            background: #f3f4f6;
            text-align: center;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .footer {
            margin-top: 28px;
            width: 100%;
        }

        .signature {
            width: 220px;
            float: right;
            text-align: center;
        }

        .signature-space {
            height: 64px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">REKAP NILAI UJIAN SISWA</div>
        <div class="subtitle">SMA Negeri 1 Wonosari</div>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 120px;">Kelas</td>
            <td>: {{ $classRoom->name }}</td>
        </tr>
        <tr>
            <td>Mata Pelajaran</td>
            <td>: {{ $schoolSubject->name }}</td>
        </tr>
        <tr>
            <td>Paket Ujian</td>
            <td>: {{ $exam->title }}</td>
        </tr>
        <tr>
            <td>Jumlah Soal</td>
            <td>: {{ $totalQuestions }}</td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td>: {{ now()->translatedFormat('d F Y H:i') }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 90px;">NISN</th>
                <th>Nama Siswa</th>
                <th style="width: 85px;">Status</th>
                <th style="width: 42px;">Benar</th>
                <th style="width: 42px;">Salah</th>
                <th style="width: 42px;">Kosong</th>
                <th style="width: 55px;">Nilai</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($rows as $index => $row)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $row['student']->nisn }}</td>
                    <td>{{ $row['student']->name }}</td>
                    <td class="center">
                        @if ($row['status'] === 'submitted')
                            Selesai
                        @elseif ($row['status'] === 'expired')
                            Waktu Habis
                        @elseif ($row['status'] === 'in_progress')
                            Proses
                        @else
                            Belum
                        @endif
                    </td>
                    <td class="center">{{ $row['correct_count'] }}</td>
                    <td class="center">{{ $row['wrong_count'] }}</td>
                    <td class="center">{{ $row['unanswered_count'] }}</td>
                    <td class="right">
                        {{ is_null($row['score']) ? '-' : number_format((float) $row['score'], 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        <div class="signature">
            <div>Wonosari, {{ now()->translatedFormat('d F Y') }}</div>
            <div>Guru Mata Pelajaran</div>
            <div class="signature-space"></div>
            <div>________________________</div>
        </div>
    </div>
</body>

</html>
