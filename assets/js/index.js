var intro = gsap.timeline();
intro
  .addLabel("show-wrapper")
  .fromTo(
    "#wrapper",
    { y: 100, opacity: 0 },
    { y: 0, opacity: 1, duration: 0.7 }
  )
  .addLabel("show-logo")
  .to(".logo", { top: 50, duration: 0.3, ease: "expo.out" })
  .staggerFrom(
    ".burger, .top-menu",
    0.3,
    { opacity: 0, ease: "back.inOut" },
    0.2
  )
  .addLabel("show-brands")
  .staggerFrom(
    ".input-wrapper",
    0.4,
    { x: 30, opacity: 0, ease: "back.inOut" },
    "show-logo"
  )
  .staggerFrom(
    ".brand-list li",
    0.3,
    { opacity: 0, rotationX: 180, ease: Power4.easeOut },
    0.1
  )
  .fromTo(
    ".side-title, .products-category, .main-hero-slider, .card",
    { opacity: 0, y: 150 },
    { opacity: 1, y: 0, ease: "back.inOut", duration: 0.7 }
  )
  .addLabel("show-slider")
  .fromTo(
    ".main-hero-slider h2",
    { opacity: 0, y: 30 },
    { opacity: 1, y: 0, ease: Power4.easeOut, duration: 0.5 }
  )
  .fromTo(
    ".main-hero-slider .dude",
    { opacity: 0, x: 60 },
    { opacity: 1, x: 0, ease: "back.inOut", duration: 0.5 }
  )
  .to(".content-cta", {
    opacity: 1,
    width: 250,
    duration: 0.7
  });