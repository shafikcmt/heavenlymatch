<!DOCTYPE html>
<html>
<head>
    <title>Biodata PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Biodata</h2>

    <h3>General Info</h3>
    <table>
        <tr>
            <th>Biodata Type</th>
            <td>{{ $biodata->gender ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Marital Status</th>
            <td>{{ $biodata->marital_status ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Birth Date</th>
            <td>{{ $biodata->birth_date ?? 'N/A' }}</td>
        </tr>
        <!-- Add other sections like Address, Education, Family, etc. -->
    </table>

    <h3>Contact</h3>
    <table>
        <tr>
            <th>Groom Name</th>
            <td>{{ $biodata->groom_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Guardian Mobile</th>
            <td>{{ $biodata->guardian_mobile ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Guardian Relationship</th>
            <td>{{ $biodata->guardian_relationship ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Guardian Email</th>
            <td>{{ $biodata->guardian_email ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- Add remaining sections (Education, Family Info, Personal Info, etc.) similarly -->
</body>
</html>
