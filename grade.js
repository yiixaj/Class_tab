document.addEventListener("DOMContentLoaded", function () {
    const saveGradeButtons = document.querySelectorAll(".save-grade-btn");

    saveGradeButtons.forEach(button => {
        button.addEventListener("click", function () {
            const submissionId = this.getAttribute("data-id");
            const modal = this.closest(".modal-content");
            const gradeInput = modal.querySelector(".grade-input").value;
            const feedbackInput = modal.querySelector(".feedback-input").value;
            const gradeCell = document.querySelector(`tr:has(button[data-id="${submissionId}"]) .badge`);

            if (gradeInput === "") {
                alert("Por favor, ingresa una calificación.");
                return;
            }

            const formData = new FormData();
            formData.append("submission_id", submissionId);
            formData.append("grade", gradeInput);
            formData.append("feedback", feedbackInput);

            fetch("view-submissions.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update grade badge dynamically
                    gradeCell.textContent = `${gradeInput}/10`;
                    gradeCell.classList.remove('bg-warning');
                    gradeCell.classList.add('bg-success');

                    // Close modal
                    const modalElement = bootstrap.Modal.getInstance(modal.closest(".modal"));
                    modalElement.hide();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error al guardar la calificación:", error);
                alert("Ocurrió un error al guardar la calificación. Por favor, inténtalo de nuevo.");
            });
        });
    });
});