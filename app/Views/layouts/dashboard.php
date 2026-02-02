<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Recent Projects</h5>
                </div>
                <div class="card-body">
                    <div id="projects-list"></div>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                        <i class="bi bi-plus-circle"></i> New Project
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 id="team-count">0</h3>
                            <p class="text-muted">Teams</p>
                        </div>
                        <div class="col-6">
                            <h3 id="project-count">0</h3>
                            <p class="text-muted">Projects</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Project Modal -->
<div class="modal fade" id="createProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createProjectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Project Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team</label>
                        <select class="form-select" name="team_id" id="team-select" required></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Load teams for dropdown
    fetch('/api/teams')
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                const teamSelect = document.getElementById('team-select');
                data.data.forEach(team => {
                    teamSelect.innerHTML += `<option value="${team.id}">${team.name}</option>`;
                });
            }
        });

    // Load projects
    fetch('/api/projects')
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                const projectsList = document.getElementById('projects-list');
                document.getElementById('project-count').textContent = data.data.length;
                
                if(data.data.length === 0) {
                    projectsList.innerHTML = '<p class="text-muted">No projects yet. Create your first project!</p>';
                } else {
                    data.data.forEach(project => {
                        const statusBadge = project.status === 'active' ? 'bg-success' : 'bg-secondary';
                        projectsList.innerHTML += `
                            <div class="card mb-2">
                                <div class="card-body">
                                    <h6 class="card-title">${project.name}</h6>
                                    <p class="card-text text-muted">${project.description || 'No description'}</p>
                                    <span class="badge ${statusBadge}">${project.status}</span>
                                    <a href="/projects/${project.id}" class="btn btn-sm btn-outline-primary float-end">View</a>
                                </div>
                            </div>
                        `;
                    });
                }
            }
        });

    // Create project form
    document.getElementById('createProjectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/api/projects', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                location.reload();
            } else {
                alert('Error: ' + JSON.stringify(data));
            }
        });
    });
</script>
<?= $this->endSection() ?>