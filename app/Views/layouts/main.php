<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeamHub - <?= $title ?? 'Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        }
        .task-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .status-todo { border-left: 4px solid #6c757d; }
        .status-in-progress { border-left: 4px solid #0d6efd; }
        .status-done { border-left: 4px solid #198754; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <h2 class="text-white px-3">TeamHub</h2>
                    <hr class="text-white">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/dashboard">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/teams">
                                <i class="bi bi-people"></i> Teams
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/projects">
                                <i class="bi bi-kanban"></i> Projects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/profile">
                                <i class="bi bi-person-circle"></i> Profile
                            </a>
                        </li>
                    </ul>
                    
                    <div class="mt-4 px-3">
                        <h6 class="text-white">My Teams</h6>
                        <div id="team-list"></div>
                    </div>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <nav class="navbar navbar-expand-lg navbar-light bg-white mt-3 rounded shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand"><?= $title ?? 'Dashboard' ?></span>
                        <div class="d-flex">
                            <span class="navbar-text me-3">
                                Welcome, <?= session()->get('name') ?>
                            </span>
                            <a href="/logout" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </div>
                    </div>
                </nav>

                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load teams for sidebar
        fetch('/api/teams')
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    const teamList = document.getElementById('team-list');
                    data.data.forEach(team => {
                        teamList.innerHTML += `
                            <a href="/teams/${team.id}" class="d-block text-white text-decoration-none mb-2">
                                <i class="bi bi-people-fill"></i> ${team.name}
                            </a>
                        `;
                    });
                }
            });
    </script>
</body>
</html>