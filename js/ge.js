document.addEventListener('DOMContentLoaded', () => {
    function showSlides() {
        const slidesContainers = document.querySelectorAll('.slideshow-container');
        
        slidesContainers.forEach(container => {
            let slideIndex = 0;
            const slides = container.getElementsByClassName('mySlides');
            
            function displaySlides() {
                for (let i = 0; i < slides.length; i++) {
                    slides[i].style.display = 'none';  
                }
                slideIndex++;
                if (slideIndex > slides.length) { slideIndex = 1; }
                slides[slideIndex - 1].style.display = 'block';  
                setTimeout(displaySlides, 2000); // Change image every 2 seconds
            }
            
            displaySlides();
        });
    }

    showSlides();
});