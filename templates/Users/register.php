<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\Plan|null $plan
 */
$user = $user ?? $this->Users->newEmptyEntity();
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><?= __('Create Your Account') ?></h3>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($plan)): ?>
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            You're registering for the <strong><?= h($plan->name) ?></strong> plan
                        </div>
                    <?php endif; ?>

                    <?= $this->Form->create($user, [
                        'id' => 'register-form',
                        'type' => 'file',
                        'class' => 'needs-validation',
                        'novalidate' => true,
                        'url' => [
                            'controller' => 'Users',
                            'action' => 'register',
                            '?' => [
                                'plan_id' => $plan->id ?? null,
                                'redirect' => $redirectUrl ?? null
                            ]
                        ]
                    ]) ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <?= $this->Form->control('name', [
                                'class' => 'form-control',
                                'required' => true,
                                'label' => ['class' => 'form-label fw-bold'],
                                'templateVars' => ['containerClass' => 'mb-3']
                            ]) ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?= $this->Form->control('email', [
                                'type' => 'email',
                                'class' => 'form-control',
                                'required' => true,
                                'label' => ['class' => 'form-label fw-bold'],
                                'templateVars' => ['containerClass' => 'mb-3']
                            ]) ?>
                        </div>
                        
                        <div class="col-12">
                            <?= $this->Form->control('password', [
                                'class' => 'form-control',
                                'required' => true,
                                'label' => ['class' => 'form-label fw-bold'],
                                'templateVars' => ['containerClass' => 'mb-3']
                            ]) ?>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-bold">Profile Picture</label>
                            <?= $this->Form->control('profile_picture', [
                                'type' => 'file',
                                'class' => 'form-control',
                                'label' => false,
                                'accept' => 'image/jpeg,image/png'
                            ]) ?>
                            <small class="form-text text-muted">JPEG or PNG only, max 1MB</small>
                            <div id="image-preview" class="mt-2" style="display:none;">
                                <img src="#" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-bold">Hobbies</label>
                            <div id="hobbies-container" class="mb-2">
                                <div class="input-group mb-2">
                                    <input type="text" name="hobbies[0][name]" class="form-control" placeholder="Enter hobby" required>
                                    <button type="button" class="btn btn-outline-danger remove-hobby">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" id="add-hobby" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-plus me-1"></i> Add More Hobbies
                            </button>
                        </div>
                        
                        <div class="col-12 mt-4">
                            <?= $this->Form->button(__('Register Now'), [
                                'class' => 'btn btn-primary w-100 py-2 fw-bold',
                                'id' => 'register-button'
                            ]) ?>
                        </div>
                    </div>
                    
                    <?= $this->Form->end() ?>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="mb-0">Already have an account? 
                    <?= $this->Html->link(__('Login here'), [
                        'action' => 'login',
                        '?' => ['redirect' => $redirectUrl ?? null]
                    ], ['class' => 'text-primary fw-bold']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add hobby field
    $('#add-hobby').on('click', function() {
        const container = $('#hobbies-container');
        const index = container.find('.input-group').length;
        
        const div = $('<div class="input-group mb-2">');
        div.html(`
            <input type="text" name="hobbies[${index}][name]" class="form-control" placeholder="Enter hobby" required>
            <button type="button" class="btn btn-outline-danger remove-hobby">
                <i class="fas fa-times"></i>
            </button>
        `);
        container.append(div);
    });
    
    // Remove hobby field
    $(document).on('click', '.remove-hobby', function() {
        if ($('#hobbies-container .input-group').length > 1) {
            $(this).closest('.input-group').remove();
        }
    });
    
    // Image preview
    $('input[type="file"]').on('change', function(e) {
        const file = e.target.files[0];
        const preview = $('#image-preview');
        
        if (!file) {
            preview.hide();
            return;
        }
        
        // Validate file type
        if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File',
                text: 'Only JPEG or PNG images are allowed'
            });
            $(this).val('');
            preview.hide();
            return;
        }
        
        // Validate file size (1MB)
        if (file.size > 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Image must be less than 1MB'
            });
            $(this).val('');
            preview.hide();
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(event) {
            preview.find('img').attr('src', event.target.result);
            preview.show();
        };
        reader.readAsDataURL(file);
    });
    
    // AJAX Form Submission
    $('#register-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const formData = new FormData(form[0]);
        const submitBtn = $('#register-button');
        
        // Client-side validation
        if (!form[0].checkValidity()) {
            form.addClass('was-validated');
            return false;
        }

        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Registration successful',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                } else {
                    // Clear previous errors
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').text('');
                    
                    // Show new errors
                    if (response.errors) {
                        $.each(response.errors, function(field, errors) {
                            var input = form.find(`[name="${field}"]`);
                            var feedback = input.next('.invalid-feedback');
                            
                            if (feedback.length === 0) {
                                input.after(`<div class="invalid-feedback"></div>`);
                                feedback = input.next('.invalid-feedback');
                            }
                            
                            input.addClass('is-invalid');
                            var values = Object.values(errors); // ['This email is already registered']
                            var message = values[0];
                            feedback.text(message);
                        });
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: response.message || 'Please fix the errors below'
                    });
                }
            },
            error: function(xhr) {
                let message = 'Registration failed. Please try again.';
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
    
    // Real-time validation
    // $('input[name="email"]').on('blur', function() {
    //     const email = $(this).val();
    //     if (!email) return;
        
    //     $.ajax({
    //         url: '/users/validate-field?field=email&value=' + encodeURIComponent(email),
    //         type: 'GET',
    //         dataType: 'json',
    //         headers: {
    //             'X-Requested-With': 'XMLHttpRequest'
    //         },
    //         success: function(response) {
    //             const input = $('input[name="email"]');
    //             const feedback = input.next('.invalid-feedback');
                
    //             if (!response.valid) {
    //                 input.addClass('is-invalid');
    //                 feedback.text(response.message);
    //             } else {
    //                 input.removeClass('is-invalid');
    //                 feedback.text('');
    //             }
    //         }
    //     });
    // });
});
</script>