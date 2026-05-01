function fetchMember() {
    const phone = document.getElementById('phone').value.trim();
    if (!phone) {
        showError('Please enter a phone number');
        return;
    }

    // Show loading state
    const btn = document.getElementById('lookupBtn');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('loadingSpinner');
    
    btn.disabled = true;
    btnText.textContent = 'Checking...';
    spinner.style.display = 'block';
    
    // Hide previous results/error
    document.getElementById('error').classList.remove('show');
    document.getElementById('result').classList.remove('show');

    fetch('/account/fetch-member.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'phone=' + phone
    })
    .then(res => res.json())
    .then(data => {
        // Reset button state
        btn.disabled = false;
        btnText.textContent = 'Check Account';
        spinner.style.display = 'none';

        if (data.error) {
            showError(data.error);
            return;
        }

        // Update results with animation
        document.getElementById('r-name').textContent = data.name;
        document.getElementById('r-paid').textContent = data.paid;
        document.getElementById('r-meals').textContent = data.meals;
        document.getElementById('r-rate').textContent = data.rate;
        document.getElementById('r-cost').textContent = data.cost;
        
        // Parse HTML from backend to extract status text and class
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = data.status;
        const statusSpan = tempDiv.querySelector('span');
        const statusText = statusSpan.textContent;
        const statusClass = statusSpan.className;
        
        // Update status badge
        const statusBadge = document.getElementById('r-due');
        statusBadge.className = 'status-badge ' + statusClass;
        statusBadge.innerHTML = `<i class="fas fa-circle"></i><span>${statusText}</span>`;
        
        // Show results with animation
        setTimeout(() => {
            document.getElementById('result').classList.add('show');
        }, 50);
    })
    .catch(error => {
        // Reset button state
        btn.disabled = false;
        btnText.textContent = 'Check Account';
        spinner.style.display = 'none';
        
        showError('Network error. Please try again.');
        console.error('Error:', error);
    });
}

function showError(message) {
    const errorDiv = document.getElementById('error');
    const errorText = errorDiv.querySelector('.error-text');
    
    errorText.textContent = message;
    errorDiv.classList.add('show');
    
    // Hide results if showing
    document.getElementById('result').classList.remove('show');
}

// Allow pressing Enter in phone input
document.getElementById('phone').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        fetchMember();
    }
});