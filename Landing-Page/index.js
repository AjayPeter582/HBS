let currentIndex = 0;

function moveSlide(step) {
    const slides = document.querySelectorAll(".slide");
    const totalSlides = slides.length;

    currentIndex = (currentIndex + step + totalSlides) % totalSlides;

    const slider = document.querySelector(".slider");
    slider.style.transform = `translateX(-${currentIndex * 100}%)`;

    slides.forEach((slide, index) => {
        const caption = slide.querySelector(".caption");
        const video = slide.querySelector("video");

        if (caption) {
            caption.style.display = index === currentIndex ? "block" : "none";
        }

        if (video) {
            if (index === currentIndex) {
                video.play(); 
            } else {
                video.pause();  
                video.currentTime = 0;  
            }
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    moveSlide(0);
    setInterval(() => moveSlide(1), 5000); 
});

document.querySelectorAll(".video-slide").forEach(video => {
    video.controls = false;
    video.autoplay = true;
});
