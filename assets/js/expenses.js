document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const addExpenseBtn = document.querySelector('.add-expense-btn');
    const expenseModal = document.getElementById('expenseModal');
    const deleteModal = document.getElementById('deleteModal');
    const expenseForm = document.getElementById('expenseForm');
    const modalTitle = document.getElementById('modalTitle');
    const saveExpenseBtn = document.getElementById('saveExpense');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    const toastContainer = document.querySelector('.toast-container');
    const tbody = document.querySelector('.expence-table tbody');
    
    // Set today's date as default
    document.getElementById('date').valueAsDate = new Date();
    
    // Variables to store current expense ID for edit/delete
    let currentExpenseId = null;
    
    // Show Modal Functions
    function showExpenseModal() {
        expenseModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function showDeleteModal() {
        deleteModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    // Hide Modal Functions
    function hideExpenseModal() {
        expenseModal.classList.remove('active');
        resetExpenseForm();
    }
    
    function hideDeleteModal() {
        deleteModal.classList.remove('active');
        currentExpenseId = null;
    }
    
    // Reset expense form
    function resetExpenseForm() {
        expenseForm.reset();
        document.getElementById('expenseId').value = '';
        modalTitle.textContent = 'Add Expense';
        saveExpenseBtn.innerHTML = '<i class="fa-solid fa-plus"></i> Add Expense';
        document.getElementById('date').valueAsDate = new Date();
        currentExpenseId = null;
    }
    
    // Show Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <i class="fa-solid ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <div class="toast-text">${message}</div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Remove toast after 8 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 8000);
    }
    
    // Format date to "j M Y" format
    function formatDate(dateString) {
        const date = new Date(dateString);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
    }
    
    // Update table row after edit - FIXED VERSION
    function updateTableRow(data) {
        // Find the row using data-id attribute on either edit or delete icon
        const editIcon = document.querySelector(`.edit-icon[data-id="${data.id}"]`);
        if (!editIcon) {
            console.error('Could not find row to update');
            return;
        }
        
        const row = editIcon.closest('tr');
        if (row && row.cells.length >= 5) {
            // Update the cells with new data
            row.cells[2].textContent = formatDate(data.date); // Date column
            row.cells[3].textContent = data.amount; // Amount column
            row.cells[4].textContent = data.comment; // Comment column
        }
    }
    
    // Add new row to table
    function addNewRow(data, slNumber) {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td></td>
            <td>${slNumber}</td>
            <td>${formatDate(data.date)}</td>
            <td>${data.amount}</td>
            <td>${data.comment}</td>
            <td class="action-icons">
                <i class="fa-solid fa-pen-to-square edit-icon" data-id="${data.id}"></i>
                <i class="fa-solid fa-trash delete-icon" data-id="${data.id}"></i>
            </td>
        `;
        
        tbody.appendChild(newRow);
        
        // Add event listeners to new row icons
        addEventListenersToRow(newRow);
        
        // If there was "No expenses found" message, remove it
        const noDataRow = tbody.querySelector('tr td[colspan="6"]');
        if (noDataRow) {
            noDataRow.closest('tr').remove();
        }
    }
    
    // Remove row from table after delete
    function removeTableRow(expenseId) {
        const row = document.querySelector(`.delete-icon[data-id="${expenseId}"]`)?.closest('tr');
        if (row) {
            row.remove();
            
            // Update SL numbers
            updateSlNumbers();
            
            // If table is empty, show "No expenses found" message
            if (tbody.children.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = '<td colspan="6" style="text-align:center;">No expenses found</td>';
                tbody.appendChild(emptyRow);
            }
        }
    }
    
    // Update SL numbers after delete
    function updateSlNumbers() {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            // Skip if this is the "No expenses found" row
            if (row.cells.length > 1) {
                const slCell = row.cells[1];
                if (slCell) {
                    slCell.textContent = index + 1;
                }
            }
        });
    }
    
    // Add event listeners to row icons
    function addEventListenersToRow(row) {
        const editIcon = row.querySelector('.edit-icon');
        const deleteIcon = row.querySelector('.delete-icon');
        
        if (editIcon) {
            editIcon.addEventListener('click', editExpenseHandler);
        }
        
        if (deleteIcon) {
            deleteIcon.addEventListener('click', deleteExpenseHandler);
        }
    }
    
    // Event handler for edit
    function editExpenseHandler() {
        const expenseId = this.getAttribute('data-id');
        
        // Fetch expense data
        fetch(`/dashboard/expenses/fetch-expense.php?id=${expenseId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Populate form
                    document.getElementById('expenseId').value = data.expense.id;
                    document.getElementById('date').value = data.expense.date;
                    document.getElementById('amount').value = data.expense.amount;
                    document.getElementById('comment').value = data.expense.comment;
                    
                    // Update modal for edit
                    modalTitle.textContent = 'Edit Expense';
                    saveExpenseBtn.innerHTML = '<i class="fa-solid fa-save"></i> Update Expense';
                    
                    showExpenseModal();
                    currentExpenseId = expenseId;
                } else {
                    showToast(data.message || 'Failed to load expense data', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Network error. Please try again.', 'error');
            });
    }
    
    // Event handler for delete
    function deleteExpenseHandler() {
        currentExpenseId = this.getAttribute('data-id');
        showDeleteModal();
    }
    
    // Initialize event listeners for existing rows
    function initializeEventListeners() {
        document.querySelectorAll('.edit-icon').forEach(icon => {
            icon.removeEventListener('click', editExpenseHandler);
            icon.addEventListener('click', editExpenseHandler);
        });
        
        document.querySelectorAll('.delete-icon').forEach(icon => {
            icon.removeEventListener('click', deleteExpenseHandler);
            icon.addEventListener('click', deleteExpenseHandler);
        });
    }
    
    // Initialize
    initializeEventListeners();
    
    // Event Listeners for Modal Open
    addExpenseBtn.addEventListener('click', function() {
        showExpenseModal();
    });
    
    // Event Listeners for Modal Close
    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', function() {
            hideExpenseModal();
            hideDeleteModal();
            document.body.style.overflow = '';
        });
    });
    
    // Close modal when clicking outside
    [expenseModal, deleteModal].forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideExpenseModal();
                hideDeleteModal();
                document.body.style.overflow = '';
            }
        });
    });
    
    // Save Expense - FIXED VERSION
    saveExpenseBtn.addEventListener('click', function() {
        const formData = new FormData(expenseForm);
        const expenseId = document.getElementById('expenseId').value;
        const isEdit = expenseId !== '';
        
        // Disable button and show loading
        this.classList.add('loading');
        this.disabled = true;
        
        fetch('/dashboard/expenses/save-expense.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message || (isEdit ? 'Expense updated successfully!' : 'Expense added successfully!'));
                
                // Close modal
                hideExpenseModal();
                document.body.style.overflow = '';
                
                if (isEdit) {
                    // Fetch and update the edited row immediately
                    fetch(`/dashboard/expenses/fetch-expense.php?id=${expenseId}`)
                        .then(response => response.json())
                        .then(updatedData => {
                            if (updatedData.status === 'success') {
                                updateTableRow(updatedData.expense);
                            } else {
                                console.error('Failed to fetch updated expense data');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching updated data:', error);
                        });
                } else {
                    // Get the latest expense ID and add new row
                    fetch('/dashboard/expenses/get-latest-expense.php')
                        .then(response => response.json())
                        .then(latestData => {
                            if (latestData.status === 'success') {
                                const slNumber = tbody.querySelectorAll('tr').length + 1;
                                addNewRow(latestData.expense, slNumber);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching latest expense:', error);
                        });
                }
            } else {
                showToast(data.message || 'Failed to save expense', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Network error. Please try again.', 'error');
        })
        .finally(() => {
            // Re-enable button
            this.classList.remove('loading');
            this.disabled = false;
        });
    });
    
    // Confirm Delete
    confirmDeleteBtn.addEventListener('click', function() {
        if (!currentExpenseId) return;
        
        // Disable button and show loading
        this.classList.add('loading');
        this.disabled = true;
        
        fetch('/dashboard/expenses/delete-expense.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${currentExpenseId}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message || 'Expense deleted successfully!');
                
                // Store the ID before closing modal
                const idToDelete = currentExpenseId;
                
                // Close modal
                hideDeleteModal();
                document.body.style.overflow = '';
                
                // Remove row from table
                removeTableRow(idToDelete);
            } else {
                showToast(data.message || 'Failed to delete expense', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Network error. Please try again.', 'error');
        })
        .finally(() => {
            // Re-enable button
            this.classList.remove('loading');
            this.disabled = false;
        });
    });
    
    // Prevent form submission on Enter key
    expenseForm.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
});