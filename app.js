const slider = document.querySelector('.slider');
const sliderWrapper = document.querySelector('.sliderWrapper');
const sliderItems = document.querySelectorAll('.sliderItem');
const menuItems = document.querySelectorAll('.menuItem');
let slideIndex = 0;
let userClicked = false;
let slideInterval;

function nextSlide() {
  if (!userClicked) {
    slideIndex = (slideIndex + 1) % (sliderItems.length / 2);
    updateSlider();
  }
}

function updateSlider() {
  const translateValue = -slideIndex * 100+ '%';
  sliderWrapper.style.transform = 'translateX(' + translateValue + ')';
}

function startAutoSlide() {
  slideInterval = setInterval(nextSlide, 3000);
}

// Start automatic sliding
startAutoSlide();

// Pause automatic sliding when the user clicks a button
menuItems.forEach((item, index) => {
  item.addEventListener("click", () => {
    userClicked = true;
    clearInterval(slideInterval); // Stop automatic sliding
    sliderWrapper.style.transition = 'transform 1s ease-in-out'; // Optional: add transition effect
    sliderWrapper.style.transform = `translateX(${-100 * index}vw)`;
  });
});

// Resume automatic sliding when the user stops clicking
slider.addEventListener("mouseleave", () => {
  userClicked = false;
  sliderWrapper.style.transition = 'transform 10s ease-in-out'; // Optional: add transition effect
  startAutoSlide();
});




document.addEventListener("DOMContentLoaded", function () {
    const startingScreen = document.getElementById("startingScreen");

    setTimeout(function () {
        typeText(startingScreen.querySelector("h1"), "Hello World", 0);
    }, 300); // Adjust the delay based on your preference

    // Set another timeout to initiate the slide-up animation
    setTimeout(function () {
        startingScreen.classList.add("slide-up");

        // Set another timeout to remove the starting screen after the animation duration
        setTimeout(function () {
            // Remove the starting screen from the DOM
            startingScreen.remove();
        }, 500); // Adjust the duration based on your CSS animation duration
    }, 3000); // Adjust the delay based on the typing effect duration

    
    // Your existing slider code
 
});


function typeText(element, text, index) {
    if (index < text.length) {
        element.textContent += text.charAt(index);
        index++;
        setTimeout(function () {
            typeText(element, text, index);
        }, 100); // Adjust the typing speed
    }
}