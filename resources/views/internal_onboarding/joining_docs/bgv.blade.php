<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Joining Documents</title>

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
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
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
            background-color: #007bff;
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

    <!-- Page Title -->
    <div class="container">
        <h3 class="text-center my-4 text-primary fw-bold">Upload Remaining Joining Documents</h3>

        <div class="form-section">
            <form action="{{ route('joining-docs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">BGV</label>
                        <input type="file" name="bgv" class="form-control"  >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">EPF</label>
                        <input type="file" name="epf" class="form-control"  >
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Gratuity</label>
                        <input type="file" name="gratuity" class="form-control"  >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Joining Form</label>
                        <input type="file" name="joining_form" class="form-control"  >
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nomination Declaration</label>
                        <input type="file" name="nomination_declaration" class="form-control"  >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">POSH Ack</label>
                        <input type="file" name="posh_ack" class="form-control"  >
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-20 submit-btn">Submit Joining Documents</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
