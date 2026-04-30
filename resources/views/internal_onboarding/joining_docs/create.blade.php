<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter & Acceptance Mail</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .fixed-header {
            position: fixed;
            top: 0;
            background-color: white;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
        }

        body {
            padding-top: 100px;
            background-color: #f8f9fa;
        }

        .form-section {
            max-width: 700px;
            margin: auto;
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: #444;
        }

        .form-control {
            padding: 10px;
            border-radius: 5px;
        }

        .submit-btn {
            background-color: #28a745;
            border: none;
            font-weight: bold;
            padding: 12px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="fixed-header d-flex justify-content-between align-items-center">
        <div>
            <img src="{{ asset('images/logo.png') }}" alt="Company Logo" style="height: 55px;">
        </div>
    </div>

    <!-- Form Section -->
    <div class="container">
        <h3 class="text-center my-4 text-success fw-bold">Upload Offer Letter & Acceptance Mail</h3>

        <div class="form-section">
            <form action="{{ route('joining-docs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">

                <div class="mb-4">
                    <label class="form-label">Offer Letter</label>
                    <input type="file" name="offer_letter" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Acceptance Mail</label>
                    <input type="file" name="acceptence_mail" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success w-35 submit-btn">Submit</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
