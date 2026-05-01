    document.querySelectorAll('.editable').forEach(cell => {
    cell.addEventListener('blur', () => {
        const value = cell.innerText.trim();
        const id = cell.dataset.id;
        const field = cell.dataset.field;

        fetch('/dashboard/members/update-cell.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                id,
                field,
                value
            })
        });
    });
});