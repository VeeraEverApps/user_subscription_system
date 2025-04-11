<?php
/**
 * @var \App\View\AppView $this
 * @var string $redirectUrl
 */
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><?= __('Login to Your Account') ?></h3>
                </div>
                <div class="card-body p-4">
                    <?= $this->Flash->render() ?>
                    
                    <?= $this->Form->create(null, [
                        'id' => 'login-form',
                        'class' => 'needs-validation',
                        'novalidate' => true,
                        'url' => [
                            'controller' => 'Users',
                            'action' => 'login',
                            '?' => ['redirect' => $redirectUrl ?? null]
                        ]
                    ]) ?>
                    
                    <div class="mb-3">
                        <?= $this->Form->control('email', [
                            'type' => 'email',
                            'class' => 'form-control',
                            'required' => true,
                            'label' => ['class' => 'form-label fw-bold'],
                            'placeholder' => 'Enter your email'
                        ]) ?>
                    </div>
                    
                    <div class="mb-3">
                        <?= $this->Form->control('password', [
                            'class' => 'form-control',
                            'required' => true,
                            'label' => ['class' => 'form-label fw-bold'],
                            'placeholder' => 'Enter your password'
                        ]) ?>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <?= $this->Form->control('remember_me', [
                            'type' => 'checkbox',
                            'class' => 'form-check-input',
                            'label' => ['class' => 'form-check-label']
                        ]) ?>
                    </div>
                    
                    <?= $this->Form->button(__('Login'), [
                        'class' => 'btn btn-primary w-100 py-2 fw-bold',
                        'id' => 'login-button'
                    ]) ?>
                    
                    <div class="text-center mt-3">
                        <a href="#" id="forgot-password-link" class="text-decoration-none">
                            <?= __('Forgot password?') ?>
                        </a>
                    </div>
                    
                    <?= $this->Form->end() ?>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="mb-0"><?= __('Don\'t have an account?') ?>
                    <?= $this->Html->link(__('Register now'), [
                        'action' => 'register',
                        '?' => ['redirect' => $redirectUrl ?? null]
                    ], ['class' => 'text-primary fw-bold']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // AJAX Login Form
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#login-button');
        
        // Client-side validation
        if (!form[0].checkValidity()) {
            form.addClass('was-validated');
            return false;
        }

        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome Back!',
                        text: response.message || 'Login successful',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message || 'Invalid email or password'
                    });
                }
            },
            error: function(xhr) {
                let message = 'Login failed. Please try again.';
                try {
                    const jsonResponse = JSON.parse(xhr.responseText);
                    message = jsonResponse.message || message;
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false);
            }
        });
    });
    
    // Forgot Password Modal
    $('#forgot-password-link').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Reset Password',
            html: `
                <form id="forgot-password-form">
                    <div class="mb-3">
                        <label for="reset-email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="reset-email" name="email" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Send Reset Link',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const email = $('#reset-email').val();
                if (!email) {
                    $('#reset-email').addClass('is-invalid');
                    $('#reset-email').next('.invalid-feedback').text('Please enter your email');
                    return false;
                }
                
                return $.ajax({
                    url: '/users/forgot-password',
                    type: 'POST',
                    data: { email: email },
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    if (!response.success) {
                        throw new Error(response.message);
                    }
                    return response;
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent!',
                    text: 'Please check your email for reset instructions.',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    });
});
</script>