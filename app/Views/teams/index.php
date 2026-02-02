<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Teams</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTeamModal">
            <i class="bi bi-plus-circle"></i> Create Team
        </button>
    </div>

    <div class="row" id="teams-container">
        <!-- Teams will be loaded here via JavaScript -->
    </div>
</div>

<!-- Create Team Modal -->
<div class="modal fade" id="createTeamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createTeamForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Team Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Team</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Load teams
    function loadTeams() {
        fetch('/api/teams')
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    const container = document.getElementById('teams-container');
                    
                    if(data.data.length === 0) {
                        container.innerHTML = `
                            <div class="col-12">
                                <div class="alert alert-info">
                                    You haven't joined any teams yet. Create your first team!
                                </div>
                            </div>
                        `;
                    } else {
                        container.innerHTML = '';
                        data.data.forEach(team => {
                            container.innerHTML += `
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">${team.name}</h5>
                                            <p class="card-text text-muted">${team.description || 'No description'}</p>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    Created: ${new Date(team.created_at).toLocaleDateString()}
                                                </small>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            <a href="/teams/${team.id}" class="btn btn-primary btn-sm">View Team</a>
                                            <button class="btn btn-outline-secondary btn-sm" onclick="inviteToTeam(${team.id})">
                                                <i class="bi bi-person-plus"></i> Invite
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                }
            });
    }

    // Create team form
    document.getElementById('createTeamForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/api/teams', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                $('#createTeamModal').modal('hide');
                loadTeams();
                document.getElementById('createTeamForm').reset();
            } else {
                alert('Error: ' + JSON.stringify(data));
            }
        });
    });

    // Invite to team
    function inviteToTeam(teamId) {
        const email = prompt('Enter email address to invite:');
        if (email) {
            const formData = new FormData();
            formData.append('email', email);
            
            fetch(`/api/teams/${teamId}/invite`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || 'Invitation sent!');
            });
        }
    }

    // Initial load
    loadTeams();
</script>
<?= $this->endSection() ?>