document.addEventListener("DOMContentLoaded", function () {
  // Password strength indicator
  const passwordInput = document.getElementById("password");
  const strengthBar = document.getElementById("strength-bar");
  const strengthText = document.getElementById("strength-text");

  if (passwordInput) {
    passwordInput.addEventListener("input", function () {
      const password = passwordInput.value;
      const strength = calculatePasswordStrength(password);

      // Update strength bar
      strengthBar.style.width = strength.percentage + "%";
      strengthBar.style.backgroundColor = strength.color;

      // Update strength text
      strengthText.textContent = strength.text;
      strengthText.style.color = strength.color;
    });
  }
});




// Calculate password strength
function calculatePasswordStrength(password) {
  let strength = 0;
  const tips = [];

  // Check for length
  if (password.length < 6) {
    tips.push("Password is too short");
  } else if (password.length >= 12) {
    strength += 1;
  }

  // Check for uppercase letters
  if (/[A-Z]/.test(password)) {
    strength += 1;
  } else {
    tips.push("Add uppercase letters");
  }

  // Check for lowercase letters
  if (/[a-z]/.test(password)) {
    strength += 1;
  } else {
    tips.push("Add lowercase letters");
  }

  // Check for numbers
  if (/[0-9]/.test(password)) {
    strength += 1;
  } else {
    tips.push("Add numbers");
  }

  // Check for special characters
  if (/[^A-Za-z0-9]/.test(password)) {
    strength += 1;
  } else {
    tips.push("Add special characters");
  }

  // Return strength information
  if (strength < 2) {
    return {
      percentage: 25,
      color: "#e74c3c",
      text: "Weak" + (tips.length > 0 ? " - " + tips.join(", ") : ""),
    };
  } else if (strength < 4) {
    return {
      percentage: 50,
      color: "#f39c12",
      text: "Medium" + (tips.length > 0 ? " - " + tips.join(", ") : ""),
    };
  } else if (strength < 5) {
    return {
      percentage: 75,
      color: "#3498db",
      text: "Strong" + (tips.length > 0 ? " - " + tips[0] : ""),
    };
  } else {
    return {
      percentage: 100,
      color: "#2ecc71",
      text: "Very Strong",
    };
  }
}

// Form submission feedback
const form = document.querySelector(".register-form");
if (form) {
  form.addEventListener("submit", function (e) {
   
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
      submitButton.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Processing...';
      submitButton.disabled = true;
    }
  });
}
document.addEventListener("DOMContentLoaded", function () {
  // Toggle sidebar on mobile
  const menuToggle = document.querySelector(".menu-toggle");
  const sidebar = document.querySelector(".sidebar");
  const mainContent = document.querySelector(".main-content");

  menuToggle.addEventListener("click", function () {
    sidebar.classList.toggle("active");
    mainContent.classList.toggle("active");
  });

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", function (event) {
    if (window.innerWidth <= 768) {
      const isClickInsideSidebar = sidebar.contains(event.target);
      const isClickOnMenuToggle = menuToggle.contains(event.target);

      if (
        !isClickInsideSidebar &&
        !isClickOnMenuToggle &&
        sidebar.classList.contains("active")
      ) {
        sidebar.classList.remove("active");
        mainContent.classList.remove("active");
      }
    }
  });

  // Responsive table actions
  const tableRows = document.querySelectorAll("table tbody tr");
  tableRows.forEach((row) => {

    row.addEventListener("mouseenter", function () {
      this.style.backgroundColor = "rgba(52, 152, 219, 0.1)";
    });

    row.addEventListener("mouseleave", function () {
      this.style.backgroundColor = "";
    });
  });

  // Highlight active menu item
  const currentPage = window.location.pathname.split("/").pop() || "index.php";
  const menuItems = document.querySelectorAll(".sidebar-menu li a");

  menuItems.forEach((item) => {
    const href = item.getAttribute("href");
    if (href === currentPage) {
      item.parentElement.classList.add("active");
    } else {
      item.parentElement.classList.remove("active");
    }
  });
 document.addEventListener('DOMContentLoaded', function() {
    // Check for alert elements in the DOM (injected by PHP)
    const alertElements = document.querySelectorAll('.php-alert');
    
    alertElements.forEach(alert => {
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alert.style.animation = 'slideOut 0.5s forwards';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
    
    // Close button functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('close-btn')) {
            const alert = e.target.closest('.alert');
            alert.style.animation = 'slideOut 0.5s forwards';
            setTimeout(() => alert.remove(), 500);
        }
    });
});


function showAlert(message, type = 'error') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        ${message}
        <span class="close-btn">&times;</span>
    `;
    
    document.body.appendChild(alert);
    
   
    setTimeout(() => {
        alert.style.animation = 'slideOut 0.5s forwards';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
}

 function toggleSidebar() {
   const sidebar = document.querySelector(".sidebar");
   const mainContent = document.querySelector(".main-content");
   sidebar.classList.toggle("active");
   mainContent.classList.toggle("active");
 }

}
);
