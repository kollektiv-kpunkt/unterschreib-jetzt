var styleSets = function styleSets() {
    var vh = window.innerHeight * 0.01;
    var vw = jQuery(document).width() * 0.01;
    document.documentElement.style.setProperty("--vh", `${vh}px`);
    document.documentElement.style.setProperty("--vw", `${vw}px`);
}

window.addEventListener("load", styleSets, false);
window.addEventListener("resize", styleSets, false);