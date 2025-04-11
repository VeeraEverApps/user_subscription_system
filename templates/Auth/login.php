<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <form id="login-form">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember-me" name="remember_me">
                        <label class="form-check-label" for="remember-me">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#login-form').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.post('/auth/login', formData, function(response) {
            if (response.success) {
                localStorage.setItem('jwt_token', response.token);
                
                // If "Remember me" is checked, store email in cookie
                if ($('#remember-me').is(':checked')) {
                    document.cookie = `remember_email=${response.user.email}; max-age=${30*24*60*60}`;
                }
                
                window.location.href = '/users';
            } else {
                alert(response.message || 'Login failed. Please try again.');
            }
        }).fail(function() {
            alert('An error occurred. Please try again.');
        });
    });
    
    // Check for remembered email
    const rememberedEmail = getCookie('remember_email');
    if (rememberedEmail) {
        $('#email').val(rememberedEmail);
        $('#remember-me').prop('checked', true);
    }
    
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }
});
</script>