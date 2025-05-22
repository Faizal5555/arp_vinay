<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #f1f8ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            padding: 40px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .thank-title {
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="text-center card">
        <h2 class="thank-title text-success">Thank You!</h2>
        <p>Respondent Incentive Details were uploaded successfully.</p>
        <a href="{{ url('/incentive-form') }}" class="mt-3 btn btn-outline-primary">Back to Upload</a>
    </div>
</body>

</html>
