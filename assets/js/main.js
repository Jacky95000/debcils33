document.addEventListener("DOMContentLoaded", async () => {
  // === SLIDER PHOTOS ===
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

  if (photoSlides.length > 0) {
    showPhotoSlide(currentPhotoIndex);
    setInterval(nextPhotoSlide, 3000);
  }

  // === SLIDER AVIS CLIENTS ===
  const track = document.querySelector(".avis-slider-track");
  if (track) {
    const slides = Array.from(track.children);
    const slideWidth = slides[0].offsetWidth + 10;
    let index = 0;

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
        }, 500);
      }
    }

    setInterval(moveSlide, 5000);
  }

  // === PLANITY STYLE : SÉLECTION D'HORAIRE ===
  const heureInput = document.getElementById('rendez_vous_heure');
  const buttons = document.querySelectorAll('.horaire-btn');
  const dateInput = document.querySelector('#rendez_vous_date');
  let rdvs = new Map();

  async function fetchDisponibilites() {
    const response = await fetch("/api/rdv/disponibilites");
    const data = await response.json();

    data.forEach(rdv => {
      const date = rdv.date;
      const heure = rdv.heure;
      const duree = parseInt(rdv.duree);

      if (!rdvs.has(date)) rdvs.set(date, new Set());

      let time = new Date(`1970-01-01T${heure}:00`);
      const intervals = duree / 30;

      for (let i = 0; i < intervals; i++) {
        const hhmm = time.toTimeString().substring(0, 5); // "HH:mm"
        rdvs.get(date).add(hhmm);
        time.setMinutes(time.getMinutes() + 30);
      }
    });
  }

  function updateButtons() {
    const date = dateInput.value;
    const bloquees = rdvs.get(date) || new Set();

    buttons.forEach(btn => {
      const heureBtn = btn.dataset.heure; // ex: "09:00"
      if (bloquees.has(heureBtn)) {
        btn.classList.add('disabled');
        btn.disabled = true;
      } else {
        btn.classList.remove('disabled');
        btn.disabled = false;
      }
    });
  }

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      if (btn.classList.contains('disabled')) return;

      buttons.forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      heureInput.value = btn.dataset.heure;
    });
  });

  await fetchDisponibilites();
  dateInput.addEventListener('change', updateButtons);
  if (dateInput.value) updateButtons();
});
