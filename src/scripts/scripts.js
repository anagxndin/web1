document.addEventListener("DOMContentLoaded", function () {
  var toggle = document.getElementById("dropdownToggle");
  var menu = document.getElementById("dropdownMenu");

  if (toggle && menu) {
    toggle.addEventListener("click", function (e) {
      e.stopPropagation();
      menu.classList.toggle("navbar__links--open");
    });

    document.addEventListener("click", function () {
      menu.classList.remove("navbar__links--open");
    });

    menu.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        menu.classList.remove("navbar__links--open");
      });
    });
  }
});
