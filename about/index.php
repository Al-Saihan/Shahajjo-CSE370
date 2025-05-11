<!DOCTYPE html>
<html>

<head>
    <title>About Us | Shahajjo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .about-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .founder-card {
            transition: transform 0.3s;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .founder-card:hover {
            transform: translateY(-5px);
        }

        .founder-img {
            height: 390px;
            object-fit: cover;
            border-bottom: 3px solid rgb(44, 7, 87);
        }

        .section-header {
            color: rgb(44, 7, 87);
            border-bottom: 2px solid rgb(44, 7, 87);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        body {
            background: linear-gradient(to right, rgb(104, 91, 153), rgb(204, 205, 206));
        }

        .bg-foot {
            background-color: rgb(44, 7, 87);
        }

        .btn-bt {
            background-color: rgb(95, 83, 109);
        }

        .contact-icon {
            font-size: 1.5rem;
            margin-right: 10px;
            color: rgb(44, 7, 87);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(60, 50, 65, 0.6);">
        <div class="container">
            <a class="navbar-brand fs-2 fw-bold" href="../index.php">Shahajjo</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="profile.php">Profile</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <!-- Section 1: About Our Website -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-10">
                <div class="about-section">
                    <h2 class="section-header text-center">About Shahajjo</h2>
                    <p class="lead text-center">A dummy DBMS Web-Project, focusing on connecting communities through compassion and support</p>

                    <div class="row justify-content-center mt-4">
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm h-100 text-center" style="background-color: rgba(255, 255, 255, 0.95);">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">
                                        <i class="fas fa-project-diagram me-2" style="color: rgb(44, 7, 87);"></i>
                                        Project Informatics
                                    </h4>

                                    <div class="mb-3">
                                        <h5 class="mb-1" style="color: rgb(44, 7, 87);">Academic Context</h5>
                                        <p class="mb-0 text-muted">
                                            This database system was developed as the final course project for
                                            <strong>CSE370: Database Systems</strong>, an undergraduate course in the
                                            Computer Science curriculum at BRAC University.
                                        </p>
                                    </div>

                                    <div class="mb-3">
                                        <h5 class="mb-1" style="color: rgb(44, 7, 87);">Team Composition</h5>
                                        <p class="mb-0 text-muted">
                                            Developed by <strong>Group 7</strong> of <strong>Section 12</strong>, consisting of
                                            3 dumb yet dedicated Computer Science students with little to no prior experience in
                                            database systems and web based project development.
                                        </p>
                                    </div>

                                    <div>
                                        <h5 class="mb-1" style="color: rgb(44, 7, 87);">Tech Stack</h5>
                                        <p class="mb-0 text-muted">
                                            The "<strong>Donation DBMS</strong>" is built using the mentioned languages and frameworks:
                                        </p>
                                        <p class="mb-0 text-muted">
                                            <strong>XAMPP</strong>, <strong>MySQL</strong>, <strong>PHP</strong>, <strong>HTML</strong>, <strong>CSS</strong>, <strong>JavaScript</strong>, <strong>Bootstrap 5</strong>.
                                        </p>
                                    </div>

                                    <hr class="my-3" style="border-color: rgba(44, 7, 87, 0.2);">

                                    <div>
                                        <a href="https://github.com/Al-Saihan/Shahajjo-CSE370" class="btn btn-sm btn-outline-secondary" target="_blank">
                                            <i class="fas fa-code me-1"></i> View Github Repository
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Founders -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-10">
                <div class="about-section">
                    <h2 class="section-header text-center">Meet The Developers</h2>
                    <p class="text-center mb-4">The dumb and stoopid team behind Shahajjo</p>

                    <div class="row">
                        <!-- Founder 1 -->
                        <div class="col-md-4 mb-4">
                            <div class="card founder-card h-100">
                                <img src="saihan.jpg" class="card-img-top founder-img" alt="Member 1">
                                <div class="card-body">
                                    <h5 class="card-title">Al- Saihan Tajvi</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">Member 1</h6>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://github.com/Al-Saihan" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fab fa-github"></i></a>
                                    <a href="https://www.facebook.com/saihan4145/" class="btn btn-sm btn-outline-info" target="_blank"><i class="fab fa-facebook"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Founder 2 -->
                        <div class="col-md-4 mb-4">
                            <div class="card founder-card h-100">
                                <img src="sakib.jpg" class="card-img-top founder-img" alt="Member 2">
                                <div class="card-body">
                                    <h5 class="card-title">S. M. Sakib Zubair</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">Member 2</h6>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://github.com/SakibZubair" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fab fa-github"></i></a>
                                    <a href="https://www.facebook.com/sakib.zubair.5" class="btn btn-sm btn-outline-info" target="_blank"><i class="fab fa-facebook"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Founder 3 -->
                        <div class="col-md-4 mb-4">
                            <div class="card founder-card h-100">
                                <img src="faiyaz.jpg" class="card-img-top founder-img" alt="Member 3">
                                <div class="card-body">
                                    <h5 class="card-title">Muhtasim Faiyaz</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">Member 3</h6>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://github.com/gupx9" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fab fa-github"></i></a>
                                    <a href="https://www.facebook.com/muhtasim.41218" class="btn btn-sm btn-outline-info" target="_blank"><i class="fab fa-facebook"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 bg-foot text-white mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Shahajjo. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>