<div class="row">
    <div class="col-md-12">
        <h2>User List</h2>
        <table id="users-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Plan</th>
                    <th>Profile Picture</th>
                    <th>Registration Date</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    const token = localStorage.getItem('jwt_token');
    
    if (!token) {
        window.location.href = '/auth/login';
        return;
    }
    
    // Initialize DataTable
    const table = $('#users-table').DataTable({
        ajax: {
            url: '/users',
            dataSrc: 'users',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    window.location.href = '/auth/login';
                }
            }
        },
        columns: [
            { data: 'name' },
            { data: 'email' },
            { 
                data: 'plan.name',
                defaultContent: 'No plan'
            },
            { 
                data: 'profile_picture',
                render: function(data) {
                    return data ? `<img src="/uploads/${data}" width="50">` : 'No image';
                }
            },
            { 
                data: 'created',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            }
        ]
    });
    
    // Logout functionality
    $('#logout').click(function(e) {
        e.preventDefault();
        $.ajax({
            url: '/auth/logout',
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function() {
                localStorage.removeItem('jwt_token');
                window.location.href = '/auth/login';
            }
        });
    });
});
</script>