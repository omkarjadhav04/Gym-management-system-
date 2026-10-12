// This file contains JavaScript code for client-side functionality, such as form validation and dynamic content updates.

document.addEventListener('DOMContentLoaded', function() {
    // Example: Form validation for membership signup
    const signupForm = document.getElementById('signup-form');
    if (signupForm) {
        signupForm.addEventListener('submit', function(event) {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!name || !email || !password) {
                event.preventDefault();
                alert('Please fill in all fields.');
            }
        });
    }

    // Example: Dynamic content update for membership plans
    const planSelect = document.getElementById('membership-plan');
    if (planSelect) {
        planSelect.addEventListener('change', function() {
            const selectedPlan = planSelect.value;
            const planDetails = document.getElementById('plan-details');

            if (selectedPlan === 'basic') {
                planDetails.textContent = 'Basic Plan: Access to gym facilities.';
            } else if (selectedPlan === 'premium') {
                planDetails.textContent = 'Premium Plan: Access to gym facilities + group classes.';
            } else if (selectedPlan === 'vip') {
                planDetails.textContent = 'VIP Plan: All benefits + personal trainer.';
            } else {
                planDetails.textContent = '';
            }
        });
    }
});