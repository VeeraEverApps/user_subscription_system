<div class="plans index content">
    <h3><?= __('Available Plans') ?></h3>
    <div class="row">
        <?php foreach ($plans as $plan): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= h($plan->name) ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">
                        $<?= h($plan->price) ?>/month
                    </h6>
                    <p class="card-text"><?= h($plan->description) ?></p>
                    <button class="btn btn-primary select-plan" 
                            data-plan-id="<?= $plan->id ?>">
                        Select Plan
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle plan selection
    document.querySelectorAll('.select-plan').forEach(button => {
        button.addEventListener('click', function() {
            const planId = this.dataset.planId;
            const redirectUrl = '/dashboard'; // Where to go after registration
            
            // Check if user is logged in
            fetch('/auth/check')
                .then(response => response.json())
                .then(data => {
                    if (data.authenticated) {
                        window.location.href = `/subscribe?plan_id=${planId}`;
                    } else {
                        window.location.href = `/users/register?plan_id=${planId}&redirect=${encodeURIComponent(redirectUrl)}`;
                    }
                })
                .catch(() => {
                    window.location.href = `/users/register?plan_id=${planId}&redirect=${encodeURIComponent(redirectUrl)}`;
                });
        });
    });
});
</script>