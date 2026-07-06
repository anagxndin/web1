document.addEventListener("DOMContentLoaded", function() {
  const dropdownToggle = document.getElementById("dropdownToggle");
  const dropdownMenu = document.getElementById("dropdownMenu");

  // Toggle dropdown menu
  if (dropdownToggle && dropdownMenu) {
    dropdownToggle.addEventListener("click", function(event) {
      event.stopPropagation();
      dropdownMenu.classList.toggle("active");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function(event) {
      if (!event.target.closest(".mainHeader")) {
        dropdownMenu.classList.remove("active");
      }
    });

    // Close dropdown when clicking on a menu item
    const dropdownItems = document.querySelectorAll(".dropdownItem");
    dropdownItems.forEach(item => {
      item.addEventListener("click", function() {
        dropdownMenu.classList.remove("active");
      });
    });
  }
});