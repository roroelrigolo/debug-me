const mobileMenuButton = document.querySelector(".mobileMenuButton");
const mobileMenu = document.querySelector(".mobileMenu");

mobileMenuButton.addEventListener("click", () => {
  console.log("malafak");
  mobileMenu.classList.toggle("hidden");
});
