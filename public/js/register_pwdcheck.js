const password = document.getElementById("password");
const password2 = document.getElementById("password2");
const message = document.getElementById("password-match-message");
const submitBtn = document.getElementById("submitBtn");

function checkPasswords() {
  const pass1 = password.value;
  const pass2 = password2.value;

  if (pass1 && pass2 && pass1 !== pass2) {
    message.style.display = "block";
    submitBtn.disabled = true;
  } else if (pass1 && pass2 && pass1 === pass2) {
    message.style.display = "none";
    submitBtn.disabled = false;
  } else {
    message.style.display = "none";
    submitBtn.disabled = true;
  }
}

password.addEventListener("input", checkPasswords);
password2.addEventListener("input", checkPasswords);
