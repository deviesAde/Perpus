document.addEventListener("DOMContentLoaded", function () {
  // Smooth scrolling for sidebar links
  document.querySelectorAll(".sidebar-menu a").forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      if (this.getAttribute("href").startsWith("#")) {
        e.preventDefault();
        const targetId = this.getAttribute("href");
        const targetElement = document.querySelector(targetId);

        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 20,
            behavior: "smooth",
          });
        }
      }
    });
  });

  // Active sidebar link highlighting
  const sections = document.querySelectorAll("section");
  const navLinks = document.querySelectorAll(".sidebar-menu a");

  window.addEventListener("scroll", function () {
    let current = "";

    sections.forEach((section) => {
      const sectionTop = section.offsetTop;
      const sectionHeight = section.clientHeight;

      if (pageYOffset >= sectionTop - 60) {
        current = section.getAttribute("id");
      }
    });

    navLinks.forEach((link) => {
      link.classList.remove("active");
      if (link.getAttribute("href") === `#${current}`) {
        link.classList.add("active");
      }
    });
  });

  // Confirmation for delete actions
  document.querySelectorAll(".btn-danger").forEach((button) => {
    button.addEventListener("click", function (e) {
      if (!confirm("Apakah Anda yakin ingin melanjutkan?")) {
        e.preventDefault();
      }
    });
  });

  // Toast notification simulation
  function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.classList.add("show");
    }, 100);

    setTimeout(() => {
      toast.classList.remove("show");
      setTimeout(() => {
        toast.remove();
      }, 300);
    }, 3000);
  }

  // Check for URL parameters to show toast messages
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has("success")) {
    showToast(urlParams.get("success"), "success");
  }
  if (urlParams.has("error")) {
    showToast(urlParams.get("error"), "error");
  }
});
