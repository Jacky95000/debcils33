document.addEventListener("DOMContentLoaded", () => {
  // Slider photos
  const photoSlides = document.querySelectorAll(".photo-slides img");
  let currentPhotoIndex = 0;

  function showPhotoSlide(index) {
    photoSlides.forEach((img, i) => {
      img.classList.toggle("active", i === index);
    });
  }

  function nextPhotoSlide() {
    currentPhotoIndex = (currentPhotoIndex + 1) % photoSlides.length;
    showPhotoSlide(currentPhotoIndex);
  }

  showPhotoSlide(currentPhotoIndex);
  setInterval(nextPhotoSlide, 3000);

  // Slider avis clients
   const track = document.querySelector(".avis-slider-track");
  const slides = Array.from(track.children);
  const slideWidth = slides[0].offsetWidth + 10; // largeur + margin droite
  let index = 0;

  // Duplique les slides pour l’effet infini
  slides.forEach(slide => {
    const clone = slide.cloneNode(true);
    track.appendChild(clone);
  });

  function moveSlide() {
    index++;
    track.style.transition = 'transform 0.5s ease-in-out';
    track.style.transform = `translateX(${-slideWidth * index}px)`;

    if (index >= slides.length) {
      setTimeout(() => {
        track.style.transition = 'none';
        index = 0;
        track.style.transform = `translateX(0)`;
      }, 500); // correspond à la durée de transition
    }
  }

  setInterval(moveSlide, 7000);
});
 document.addEventListener("DOMContentLoaded", () => {
  initSliders();

});