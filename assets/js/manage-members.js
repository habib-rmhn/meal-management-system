document.addEventListener('DOMContentLoaded', function () {

    // DOM refs
    const openBtn          = document.getElementById('openManageModal');
    const manageModal      = document.getElementById('manageModal');
    const closeManageBtn   = document.getElementById('closeManageModal');
    const deleteMemberModal= document.getElementById('deleteMemberModal');
    const closeDeleteBtn   = document.getElementById('closeDeleteMemberModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteMember');
    const saveNewMemberBtn = document.getElementById('saveNewMember');
    const memberNameInput  = document.getElementById('memberName');
    const memberNumberInput= document.getElementById('memberNumber');
    const memberListDiv    = document.getElementById('memberListForDelete');
    const toastContainer   = document.getElementById('memberToastContainer');
    const tabs             = document.querySelectorAll('.member-tab');
    const tabContents      = document.querySelectorAll('.tab-content');

    let pendingDeleteId   = null;
    let pendingDeleteName = null;

    // Toast
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <i class="fa-solid ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <div class="toast-text">${message}</div>
        `;
        toastContainer.appendChild(toast);
        setTimeout(() => { if (toast.parentNode) toast.remove(); }, 8000);
    }

    // Tab switching
    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const target = this.dataset.tab;
            tabContents.forEach(tc => {
                tc.style.display = tc.id === `tab-${target}` ? 'block' : 'none';
            });
        });
    });

    // Modal open/close
    openBtn.addEventListener('click', () => {
        manageModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    function closeManage() {
        manageModal.classList.remove('active');
        document.body.style.overflow = '';
        memberNameInput.value   = '';
        memberNumberInput.value = '';
    }

    closeManageBtn.addEventListener('click', closeManage);
    manageModal.addEventListener('click', e => { if (e.target === manageModal) closeManage(); });

    function closeDeleteModal() {
        deleteMemberModal.classList.remove('active');
        document.body.style.overflow = 'hidden'; // keep manage modal open
        pendingDeleteId   = null;
        pendingDeleteName = null;
    }

    closeDeleteBtn.addEventListener('click', closeDeleteModal);
    deleteMemberModal.addEventListener('click', e => { if (e.target === deleteMemberModal) closeDeleteModal(); });

    // Delete button click (in list)
    function attachDeleteListeners() {
        document.querySelectorAll('.btn-delete-member').forEach(btn => {
            btn.addEventListener('click', function () {
                pendingDeleteId   = this.dataset.id;
                pendingDeleteName = this.dataset.name;
                document.getElementById('deleteMemberName').textContent = pendingDeleteName;
                deleteMemberModal.classList.add('active');
            });
        });
    }
    attachDeleteListeners();

    // Confirm delete
    confirmDeleteBtn.addEventListener('click', function () {
        if (!pendingDeleteId) return;

        this.classList.add('loading');
        this.disabled = true;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', pendingDeleteId);

        fetch('/dashboard/members/manage-member.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message);

                // Remove from list UI
                const item = memberListDiv.querySelector(`.member-list-item[data-id="${pendingDeleteId}"]`);
                if (item) item.remove();

                // Remove column from meal table
                removeMemberColumn(pendingDeleteId);

                // Show "no members" if list is empty
                if (!memberListDiv.querySelector('.member-list-item')) {
                    memberListDiv.innerHTML = '<div class="no-members-msg">No members found.</div>';
                }

                closeDeleteModal();
            } else {
                showToast(data.message || 'Failed to delete member', 'error');
            }
        })
        .catch(() => showToast('Network error. Please try again.', 'error'))
        .finally(() => {
            this.classList.remove('loading');
            this.disabled = false;
        });
    });

    // Add member
    saveNewMemberBtn.addEventListener('click', function () {
        const name   = memberNameInput.value.trim();
        const number = memberNumberInput.value.trim();

        if (!name) {
            showToast('Please enter a member name.', 'error');
            memberNameInput.focus();
            return;
        }

        this.classList.add('loading');
        this.disabled = true;

        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('name', name);
        formData.append('number', number);

        fetch('/dashboard/members/manage-member.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message);
                memberNameInput.value   = '';
                memberNumberInput.value = '';

                const member = data.member;

                // Add column to meal table
                addMemberColumn(member);

                // Add to delete list
                addMemberToDeleteList(member);

            } else {
                showToast(data.message || 'Failed to add member', 'error');
            }
        })
        .catch(() => showToast('Network error. Please try again.', 'error'))
        .finally(() => {
            this.classList.remove('loading');
            this.disabled = false;
        });
    });

    // DOM helpers

    function addMemberColumn(member) {
        const table = document.querySelector('.meal-table');
        if (!table) return;

        // Add header <th>
        const thead = table.querySelector('thead tr');
        const th = document.createElement('th');
        th.textContent = member.name;
        thead.appendChild(th);

        // Add cell to every row in tbody
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const td = document.createElement('td');
            td.contentEditable = 'true';
            td.dataset.id      = member.id;
            const isPaidRow    = row.classList.contains('paid-row');
            td.dataset.field   = isPaidRow ? 'payment' : (() => {
                // figure out day number from sticky-col text
                const label = row.querySelector('.sticky-col')?.textContent?.trim() || '';
                const match = label.match(/(\d+)/);
                return match ? `day_${match[1]}` : 'day_1';
            })();
            td.className = 'editable';
            row.appendChild(td);

            // Attach blur listener
            td.addEventListener('blur', () => {
                fetch('/dashboard/members/update-cell.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: td.dataset.id, field: td.dataset.field, value: td.innerText.trim() })
                });
            });
        });
    }

    function removeMemberColumn(memberId) {
        const table = document.querySelector('.meal-table');
        if (!table) return;

        // Find column index via any editable cell with that data-id
        const sampleCell = table.querySelector(`td[data-id="${memberId}"]`);
        if (!sampleCell) return;

        const colIndex = Array.from(sampleCell.parentNode.children).indexOf(sampleCell);

        // Remove that index from every row (thead + tbody)
        table.querySelectorAll('tr').forEach(row => {
            if (row.children[colIndex]) row.children[colIndex].remove();
        });
    }

    function addMemberToDeleteList(member) {
        // Remove "no members" placeholder if present
        const placeholder = memberListDiv.querySelector('.no-members-msg');
        if (placeholder) placeholder.remove();

        const initial = member.name.charAt(0).toUpperCase();
        const div = document.createElement('div');
        div.className = 'member-list-item';
        div.dataset.id   = member.id;
        div.dataset.name = member.name;
        div.innerHTML = `
            <div class="member-avatar">${initial}</div>
            <div class="member-info">
                <span class="member-name-text">${escapeHtml(member.name)}</span>
                <span class="member-number-text">${member.number ? escapeHtml(member.number) : 'No number'}</span>
            </div>
            <button class="btn-delete-member" data-id="${member.id}" data-name="${escapeHtml(member.name)}">
                <i class="fa-solid fa-trash"></i>
            </button>
        `;
        memberListDiv.appendChild(div);
        attachDeleteListeners();
    }

    function escapeHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
});