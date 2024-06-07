document.addEventListener('DOMContentLoaded', function () {
    const confirmDeleteButtons = document.querySelectorAll('.confirm-delete-btn');

    confirmDeleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const agentId = button.dataset.agentId;
            const deleteForm = document.querySelector(`#deleteForm${agentId}`);

            if (deleteForm) {
                deleteForm.submit();
            }
        });
    });
});