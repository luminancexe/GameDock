// app.js
document.addEventListener("DOMContentLoaded", () => {
    // Basic confirmation popup for actions
    const confirmActions = document.querySelectorAll(".confirm-action");
    confirmActions.forEach(el => {
        el.addEventListener("click", (e) => {
            if(!confirm("Are you sure you want to perform this action?")) {
                e.preventDefault();
            }
        });
    });
});
