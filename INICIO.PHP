<!DOCTYPE html>

<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Clínica Veterinaria del Norte</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<l ink href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
<link href="Views/css/inicio.css" rel="stylesheet"/>
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                   "primary": "#25b7dbff",
                        "background-light": "#f6f8f8",
                        "background-dark": "#101d22",
                        "accent": "#98faadff"
                },
                fontFamily: {
                   "display": ["Manrope", "sans-serif"]
                },
                borderRadius: {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
                },

            },

            },

            }
</script>
<style>
   
    .font-sans-modern {
    
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
    }

   
    .text-subtle-dark {
        color: #374151; 
        opacity: 0.95;
    }

  
    .heading-subtle-blue {
        color: #1f2937; 
        text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.05); 
    }

    .text-accent-adjusted {
        color: #d3ff6cff; 
    }

       .gradient-curved {
        background: linear-gradient(to bottom right, #87ceeb, #5fa8d3); 
    }

    
    .curved-bg {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        overflow: hidden;
        line-height: 0;
        transform: rotate(180deg); 
        z-index: 10; 
    }

    .curved-bg svg {
        position: relative;
        display: block;
        width: calc(100% + 1.3px);
        height: 100px; 
    }

    .curved-bg .shape-fill {
        fill: #f0f8ff; 
    }

    .content-above-curve {
        position: relative;
        z-index: 20;
    }
</style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                offset: 100,
                once: true,
                easing: 'ease-out-cubic'
            });
        });
    </script>
</head>
   <body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200">
<div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
<div class="layout-container flex h-full grow flex-col">
<div class="px-4 md:px-10 lg:px-20 xl:px-40 flex flex-1 justify-center py-5">
<div class="layout-content-container flex flex-col max-w-[960px] flex-1">
<header class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between whitespace-nowrap px-4 sm:px-6 lg:px-10 py-3 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md shadow-lg transition-all duration-300">
        <div class="flex items-center gap-4 text-[#111618] dark:text-white">
            <div class="flex items-center gap-4">
                <a href="INICIO.PHP" class="group h-16 transform hover:scale-105 transition-all duration-300 relative">
                    <div class="absolute inset-0 bg-white/0 group-hover:bg-white/10 rounded-lg transition-colors duration-300"></div>
                    <img src="img/veterinarialogo.png" alt="Logo Veterinaria" class="h-full object-contain drop-shadow-md relative z-10"/>
                </a>
                <a href="INICIO.PHP" class="relative group">
                    <span class="ml-2 text-2xl md:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent drop-shadow-sm select-none block transform group-hover:scale-[1.02] transition-all duration-300">Veterinaria del Norte</span>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-primary to-accent group-hover:w-full transition-all duration-300"></span>
                </a>
            </div>
        </div>
<nav class="hidden md:flex flex-1 justify-end gap-4 md:ml-6">
<div class="flex items-center gap-1">
    <a class="relative px-4 py-2 text-sm font-semibold rounded-t-lg text-gray-700 dark:text-gray-300 group hover:bg-primary/10 transition-all duration-300" href="#">
        <span class="relative z-10 flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">home</span>
            Inicio
        </span>
        <span class="absolute inset-0 bg-gradient-to-b from-primary/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-t-lg"></span>
        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
    </a>
    <a class="relative px-4 py-2 text-sm font-semibold rounded-t-lg text-gray-700 dark:text-gray-300 group hover:bg-primary/10 transition-all duration-300" href="#sobre-nosotros">
        <span class="relative z-10 flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">groups</span>
            Nosotros
        </span>
        <span class="absolute inset-0 bg-gradient-to-b from-primary/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-t-lg"></span>
        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
    </a>
    <a class="relative px-4 py-2 text-sm font-semibold rounded-t-lg text-gray-700 dark:text-gray-300 group hover:bg-primary/10 transition-all duration-300" href="#servicios-destacados">
        <span class="relative z-10 flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">medical_services</span>
            Servicios
        </span>
        <span class="absolute inset-0 bg-gradient-to-b from-primary/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-t-lg"></span>
        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
    </a>
    <a class="relative px-4 py-2 text-sm font-semibold rounded-t-lg text-gray-700 dark:text-gray-300 group hover:bg-primary/10 transition-all duration-300" href="#ubicacion">
        <span class="relative z-10 flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">location_on</span>
            Ubicación
        </span>
        <span class="absolute inset-0 bg-gradient-to-b from-primary/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-t-lg"></span>
        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
    </a>
</div>
<div class="flex gap-3">
  <a href="Views/register.php" class="relative overflow-hidden flex items-center justify-center rounded-lg h-10 px-5 bg-primary text-white text-sm font-bold leading-normal tracking-wider hover:shadow-lg hover:scale-105 transition-all duration-300 group">
   <span class="relative z-10">Registrarse</span>
  <div class="absolute inset-0 bg-gradient-to-r from-primary to-accent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
</a>
 <a href="Views/login.php" class="relative overflow-hidden flex items-center justify-center rounded-lg h-10 px-5 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-white text-sm font-bold leading-normal tracking-wider hover:shadow-lg hover:scale-105 transition-all duration-300 group">
 <span class="relative z-10">Iniciar Sesión</span>
 <div class="absolute inset-0 bg-gradient-to-r from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
</a>
</div>
</nav>
<button class="md:hidden text-gray-700 dark:text-white hover:text-primary dark:hover:text-primary transition-colors duration-300">
    <span class="material-symbols-outlined text-2xl">menu</span>
</button>
</header>
<main class="flex-grow mt-24">
<section class="w-screen py-0 mx-0 relative left-1/2 right-1/2 -translate-x-1/2 font-sans-modern">
    <div class="relative flex flex-col md:flex-row items-stretch min-h-[450px] gradient-curved w-full rounded-b-3xl shadow-2xl overflow-hidden">

        
<div class="absolute inset-0 pointer-events-none z-0 opacity-40"> 
            <svg class="absolute left-6 top-6 w-8 h-8 heading-subtle-blue/70" viewBox="0 0 32 32"><circle cx="8" cy="8" r="4" fill="currentColor"/><circle cx="24" cy="8" r="3" fill="currentColor"/><circle cx="8" cy="24" r="3" fill="currentColor"/><circle cx="24" cy="24" r="4" fill="currentColor"/><ellipse cx="16" cy="16" rx="6" ry="4" fill="currentColor"/></svg>
            <svg class="absolute left-32 top-12 w-6 h-6 heading-subtle-blue/50 rotate-12" viewBox="0 0 32 32"><circle cx="8" cy="8" r="4" fill="currentColor"/><circle cx="24" cy="8" r="3" fill="currentColor"/><circle cx="8" cy="24" r="3" fill="currentColor"/><circle cx="24" cy="24" r="4" fill="currentColor"/><ellipse cx="16" cy="16" rx="6" ry="4" fill="currentColor"/></svg>
            <svg class="absolute left-1/2 top-8 w-10 h-10 heading-subtle-blue/40 -translate-x-1/2" viewBox="0 0 48 24"><rect x="12" y="10" width="24" height="4" rx="2" fill="currentColor"/><circle cx="8" cy="12" r="4" fill="currentColor"/><circle cx="40" cy="12" r="4" fill="currentColor"/></svg>
            <svg class="absolute right-10 top-16 w-7 h-7 heading-subtle-blue/60 rotate-6" viewBox="0 0 32 32"><circle cx="8" cy="8" r="4" fill="currentColor"/><circle cx="24" cy="8" r="3" fill="currentColor"/><circle cx="8" cy="24" r="3" fill="currentColor"/><circle cx="24" cy="24" r="4" fill="currentColor"/><ellipse cx="16" cy="16" rx="6" ry="4" fill="currentColor"/></svg>
            <svg class="absolute right-6 bottom-6 w-12 h-8 heading-subtle-blue/50" viewBox="0 0 48 24"><rect x="12" y="10" width="24" height="4" rx="2" fill="currentColor"/><circle cx="8" cy="12" r="4" fill="currentColor"/><circle cx="40" cy="12" r="4" fill="currentColor"/></svg>
            <svg class="absolute left-20 bottom-8 w-7 h-7 heading-subtle-blue/40 rotate-12" viewBox="0 0 32 32"><circle cx="8" cy="8" r="4" fill="currentColor"/><circle cx="24" cy="8" r="3" fill="currentColor"/><circle cx="8" cy="24" r="3" fill="currentColor"/><circle cx="24" cy="24" r="4" fill="currentColor"/><ellipse cx="16" cy="16" rx="6" ry="4" fill="currentColor"/></svg>
        </div>

        <div class="flex flex-col justify-center md:w-1/2 p-8 md:p-12 content-above-curve">
            <h1 class="text-4xl lg:text-5xl font-extrabold mb-5 leading-tight tracking-tight drop-shadow-md heading-subtle-blue">
                Atención Veterinaria <span class="text-accent-adjusted">Profesional 24 Horas</span>
            </h1>
            <p class="text-xl lg:text-2xl mb-4 font-semibold opacity-95 text-subtle-dark">Tu mascota siempre en las mejores manos.</p>
            
            <p class="text-base mb-6 opacity-90 text-subtle-dark">
                Más de 10 años cuidando la salud y el bienestar de tus mascotas. Contamos con equipos de diagnóstico de última generación y un equipo de veterinarios especialistas, garantizando el mejor cuidado para tu compañero fiel.
            </p>
            
            <div class="mt-2 flex flex-col gap-3">
                
                <div class="opacity-95">
                    <h3 class="text-xl font-bold mb-1 heading-subtle-blue">⭐ Nuestra Misión</h3>
                    <p class="text-base text-subtle-dark">
                        Proveer medicina veterinaria de excelencia, basada en la compasión y el respeto por los animales, asegurando un servicio integral que cubra desde la prevención hasta la atención de urgencias complejas.
                    </p>
                </div>

                <div class="opacity-95">
                    <h3 class="text-xl font-bold mb-1 heading-subtle-blue">💡 Nuestra Visión</h3>
                    <p class="text-base text-subtle-dark">
                        Ser reconocidos como el centro veterinario de referencia en la región por nuestra calidad humana, tecnológica y el impacto positivo en la salud y el bienestar de la comunidad animal.
                    </p>
                </div>

            </div>
            
        </div>
        
        <div class="md:w-1/2 flex items-center justify-center p-8 md:p-12 relative z-50 content-above-curve">
            <img src="img/veterinaria de n.jpg" alt="Veterinaria" class="w-full h-[370px] object-cover rounded-3xl shadow-2xl transform hover:scale-[1.02] transition-transform duration-500 border-4 border-white/50"/>
        </div>

        
<div class="curved-bg">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M985.66,92.83C906.67,72,823.7,46.08,755.77,35.33C695.52,26.88,632.75,9,566.6,9.13C506.65,9.25,446.69,27.08,391.17,41.93C321.64,60.18,260.63,84.06,212.37,100.41C164.08,116.68,104.47,130,0,130L0,0L1200,0L1200,120C1060.15,116.15,1022.46,113.18,985.66,92.83Z" class="shape-fill"></path>
            </svg>
        </div>

    </div>
</section>
<section id="sobre-nosotros" class="py-20 bg-primary/10 dark:bg-gray-800/80">
    <div class="container mx-auto px-4 max-w-5xl">
        <h2 class="text-[#111618] dark:text-white text-4xl font-extrabold leading-tight tracking-[-0.015em] text-center mb-16" data-aos="fade-down">
            Conoce la Excelencia de <span class="text-primary dark:text-accent">Veterinaria del Norte</span>
        </h2>
        
        <div class="grid md:grid-cols-2 gap-16 items-center">
            
            <div class="space-y-8" data-aos="fade-right" data-aos-delay="100">
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white leading-snug">Expertos en el Cuidado Integral y de <span class="text-primary">Emergencia 24 Horas</span>.</h3>
                
                <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed border-l-4 border-primary pl-4 italic">
                    Somos un equipo de profesionales apasionados, dedicados a la salud y bienestar de tus mascotas. Nuestra misión es simple: brindar la mejor medicina veterinaria con un trato humano y cercano.
                </p>
                
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg hover:shadow-primary/30 transition-shadow duration-300 transform hover:scale-[1.03] group">
                        <span class="material-symbols-outlined text-4xl text-primary mb-2 group-hover:text-accent transition-colors duration-300">emergency</span>
                        <p class="text-3xl font-extrabold text-primary dark:text-accent">24/7</p>
                        <h4 class="font-bold text-gray-800 dark:text-gray-200">Atención de Emergencia</h4>
                    </div>
                    <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg hover:shadow-primary/30 transition-shadow duration-300 transform hover:scale-[1.03] group">
                        <span class="material-symbols-outlined text-4xl text-primary mb-2 group-hover:text-accent transition-colors duration-300">work_history</span>
                        <p class="text-3xl font-extrabold text-primary dark:text-accent">+10</p>
                        <h4 class="font-bold text-gray-800 dark:text-gray-200">Años de Experiencia</h4>
                    </div>
                </div>
                
                </div>
            
            <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white dark:border-gray-900" data-aos="fade-left" data-aos-delay="200">
                <div class="p-3 bg-white/70 dark:bg-gray-900/50">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="overflow-hidden rounded-xl h-36 sm:h-48 col-span-2 sm:col-span-1">
                            <img src="img/1.jpg" alt="Foto 1" class="w-full h-full object-cover block transition-transform duration-500 hover:scale-110"/>
                        </div>
                        <div class="overflow-hidden rounded-xl h-36 sm:h-48 col-span-2 sm:col-span-1">
                            <img src="img/2.jpg" alt="Foto 2" class="w-full h-full object-cover block transition-transform duration-500 hover:scale-110"/>
                        </div>
                        <div class="overflow-hidden rounded-xl h-36 sm:h-48 col-span-2 sm:col-span-1">
                            <img src="img/3.jpg" alt="Foto 3" class="w-full h-full object-cover block transition-transform duration-500 hover:scale-110"/>
                        </div>
                        <div class="col-span-2 overflow-hidden rounded-xl h-40 sm:h-52">
                            <img src="img/4.jpg" alt="Foto 4" class="w-full h-full object-cover block transition-transform duration-500 hover:scale-110"/>
                        </div>
                        <div class="col-span-1 overflow-hidden rounded-xl h-40 sm:h-52">
                            <img src="img/5.jpg" alt="Foto 5" class="w-full h-full object-cover block transition-transform duration-500 hover:scale-110"/>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>


<section id="por-que-elegirnos" class="py-12 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-[#111618] dark:text-white text-3xl font-extrabold leading-tight text-center mb-8" data-aos="fade-up">¿Por qué elegirnos?</h2>
        <p class="max-w-3xl mx-auto text-center text-gray-600 dark:text-gray-300 mb-8" data-aos="fade-up" data-aos-delay="100">En Veterinaria del Norte unimos experiencia, tecnología y vocación para ofrecer el mejor cuidado a tu mascota. Te damos seguridad, transparencia y un trato cercano.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <div class="flex gap-4 items-start bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm" data-aos="fade-up" data-aos-delay="150">
                <span class="material-symbols-outlined text-4xl text-primary">verified</span>
                <div>
                    <h3 class="font-bold text-[#111618] dark:text-white">Profesionales Certificados</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Equipo de médicos veterinarios con amplia experiencia y formación continua.</p>
                </div>
            </div>
            <div class="flex gap-4 items-start bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm" data-aos="fade-up" data-aos-delay="200">
                <span class="material-symbols-outlined text-4xl text-primary">memory</span>
                <div>
                    <h3 class="font-bold text-[#111618] dark:text-white">Tecnología Avanzada</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Equipos de diagnóstico y quirófano de última generación para tratamientos seguros.</p>
                </div>
            </div>
            <div class="flex gap-4 items-start bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm" data-aos="fade-up" data-aos-delay="250">
                <span class="material-symbols-outlined text-4xl text-primary">schedule</span>
                <div>
                    <h3 class="font-bold text-[#111618] dark:text-white">Atención 24/7</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Servicio de urgencias permanente y seguimiento continuo en hospitalización.</p>
                </div>
            </div>
            <div class="flex gap-4 items-start bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm" data-aos="fade-up" data-aos-delay="300">
                <span class="material-symbols-outlined text-4xl text-primary">favorite</span>
                <div>
                    <h3 class="font-bold text-[#111618] dark:text-white">Cuidado Personalizado</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Planes y tratamientos adaptados a la edad, raza y condición de cada mascota.</p>
                </div>
            </div>
            <div class="flex gap-4 items-start bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm" data-aos="fade-up" data-aos-delay="350">
                <span class="material-symbols-outlined text-4xl text-primary">thumb_up</span>
                <div>
                    <h3 class="font-bold text-[#111618] dark:text-white">Transparencia y Confianza</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Explicaciones claras, presupuestos honestos y comunicación constante con el dueño.</p>
                </div>
            </div>
            <div class="flex gap-4 items-start bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm" data-aos="fade-up" data-aos-delay="400">
                <span class="material-symbols-outlined text-4xl text-primary">spa</span>
                <div>
                    <h3 class="font-bold text-[#111618] dark:text-white">Instalaciones Confortables</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Ambientes limpios y cómodos pensados para minimizar el estrés de tu mascota.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="w-screen py-0 mx-0 relative left-1/2 right-1/2 -translate-x-1/2 bg-accent dark:bg-accent">
        <div class="relative w-full max-w-[1100px] mx-auto px-4 py-12">
            <div class="absolute inset-0 pointer-events-none z-0">
                <svg class="absolute left-6 top-6 w-7 h-7 opacity-40" viewBox="0 0 32 32"><circle cx="8" cy="8" r="4" fill="#fff"/><circle cx="24" cy="8" r="3" fill="#fff"/><circle cx="8" cy="24" r="3" fill="#fff"/><circle cx="24" cy="24" r="4" fill="#fff"/><ellipse cx="16" cy="16" rx="6" ry="4" fill="#fff"/></svg>
                <svg class="absolute left-32 top-12 w-6 h-6 opacity-30 rotate-12" viewBox="0 0 32 32"><circle cx="8" cy="8" r="4" fill="#fff"/><circle cx="24" cy="8" r="3" fill="#fff"/><circle cx="8" cy="24" r="3" fill="#fff"/><circle cx="24" cy="24" r="4" fill="#fff"/><ellipse cx="16" cy="16" rx="6" ry="4" fill="#fff"/></svg>
                <svg class="absolute left-1/2 top-8 w-10 h-10 opacity-25 -translate-x-1/2" viewBox="0 0 32 32"><ellipse cx="16" cy="24" rx="8" ry="6" fill="#fff"/><circle cx="10" cy="10" r="4" fill="#fff"/><circle cx="22" cy="10" r="4" fill="#fff"/></svg>
                <svg class="absolute right-10 top-16 w-7 h-7 opacity-35 rotate-6" viewBox="0 0 32 32"><circle cx="8" cy="8" r="4" fill="#fff"/><circle cx="24" cy="8" r="3" fill="#fff"/><circle cx="8" cy="24" r="3" fill="#fff"/><circle cx="24" cy="24" r="4" fill="#fff"/><ellipse cx="16" cy="16" rx="6" ry="4" fill="#fff"/></svg>
                <svg class="absolute right-6 bottom-6 w-10 h-10 opacity-30" viewBox="0 0 32 32"><ellipse cx="16" cy="24" rx="8" ry="6" fill="#fff"/><circle cx="10" cy="10" r="4" fill="#fff"/><circle cx="22" cy="10" r="4" fill="#fff"/></svg>
                <svg class="absolute left-20 bottom-8 w-7 h-7 opacity-25 rotate-12" viewBox="0 0 32 32"><circle cx="8" cy="8" r="4" fill="#fff"/><circle cx="24" cy="8" r="3" fill="#fff"/><circle cx="8" cy="24" r="3" fill="#fff"/><circle cx="24" cy="24" r="4" fill="#fff"/><ellipse cx="16" cy="16" rx="6" ry="4" fill="#fff"/></svg>
            </div>
            <h2 id="servicios-destacados" class="text-white text-[22px] font-bold leading-tight tracking-[-0.015em] text-center pb-3 pt-5">Servicios Destacados</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 px-6 max-w-7xl mx-auto">
                <div class="flex flex-col gap-3 pb-3 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1" data-aos="fade-up" data-aos-delay="0">
                    <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg" style="background-image: url('img/hospitalización.jpeg');"></div>
                    <div>
                        <p class="text-[#111618] dark:text-white text-base font-bold leading-normal">Hospitalización</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-normal leading-normal">Cuidados intensivos y vigilancia continuada para pacientes hospitalizados.</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 pb-3 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg" style="background-image: url('img/Anestesia_inhalatoria.webp');"></div>
                    <div>
                        <p class="text-[#111618] dark:text-white text-base font-bold leading-normal">Anestesia Inhalatoria</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-normal leading-normal">Protocolos modernos de anestesia inhalatoria para mayor seguridad en procedimientos.</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 pb-3 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg" style="background-image: url('img/medicina regenerativa.jpeg');"></div>
                    <div>
                        <p class="text-[#111618] dark:text-white text-base font-bold leading-normal">Medicina Regenerativa</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-normal leading-normal">Terapias avanzadas para recuperación de tejidos y mejor calidad de vida.</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 pb-3 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg" style="background-image: url('img/medicina preventiva.jpeg');"></div>
                    <div>
                        <p class="text-[#111618] dark:text-white text-base font-bold leading-normal">Medicina Preventiva</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-normal leading-normal">Programas de prevención y control para mantener a tu mascota sana.</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 pb-3 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg" style="background-image: url('img/imagenologia.jpg');"></div>
                    <div>
                        <p class="text-[#111618] dark:text-white text-base font-bold leading-normal">Imagenología</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-normal leading-normal">Rayos X, ecografías y estudios por imagen para diagnóstico preciso.</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 pb-3 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1" data-aos="fade-up" data-aos-delay="500">
                    <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg" style="background-image: url('img/cirugia.jpeg');"></div>
                    <div>
                        <p class="text-[#111618] dark:text-white text-base font-bold leading-normal">Cirugías</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-normal leading-normal">Cirugías generales y especializadas con protocolos de seguridad estrictos.</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 pb-3 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1" data-aos="fade-up" data-aos-delay="600">
                    <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg" style="background-image: url('img/spa_canino.jpeg');"></div>
                    <div>
                        <p class="text-[#111618] dark:text-white text-base font-bold leading-normal">Spa canino</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-normal leading-normal">Baños, corte de pelo y cuidado estético para el bienestar de tu perro.</p>
                    </div>
                </div>
            </div>
    </div>
</section>

<section id="ubicacion" class="py-12 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-[#111618] dark:text-white text-3xl font-extrabold leading-tight mb-4" data-aos="fade-up">Encuéntranos</h2>
            <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">Visítanos en nuestras sedes estratégicamente ubicadas para brindarte la mejor atención veterinaria.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 items-start">
          
            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4" data-aos="fade-right">
                <div class="rounded-xl overflow-hidden shadow-lg">
                    <div class="px-4 py-3 bg-white dark:bg-gray-800">
                        <h4 class="font-bold text-[#111618] dark:text-white">Sede Independencia</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Av. Contisuyo 464 - Estación Naranjal</p>
                    </div>
                    <div class="h-64 md:h-[360px]">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3902.8942011268755!2d-77.0580722!3d-11.981822099999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105ce4c94dfc0d9%3A0x148821de90aa8e7d!2sContisuyo%20464%2C%20Lima%2015332!5e0!3m2!1ses!2spe!4v1761629991044!5m2!1ses!2spe"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>
                    </div>
                </div>

                <div class="rounded-xl overflow-hidden shadow-lg">
                    <div class="px-4 py-3 bg-white dark:bg-gray-800">
                        <h4 class="font-bold text-[#111618] dark:text-white">Sede Los Olivos</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Av. 2 de Octubre Mz. H2 lote 27 A.H., Los Olivos</p>
                    </div>
                    <div class="h-64 md:h-[360px]">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d487.918864885267!2d-77.0839483!3d-11.9501882!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105d19fc7889afb%3A0x5774bbff9fcd174a!2sVeterinaria%20del%20Norte%20Los%20Olivos!5e0!3m2!1ses-419!2spe!4v1761683229317!5m2!1ses-419!2spe"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>
                    </div>
                </div>
            </div>
            <div class="space-y-6" data-aos="fade-left">
                <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl">
                    <h3 class="text-xl font-bold text-primary mb-4">Nuestras Sedes</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-primary mt-1">location_on</span>
                            <div>
                                <p class="font-semibold text-[#111618] dark:text-white">Independencia</p>
                                <p class="text-gray-600 dark:text-gray-300">Av. Contisuyo 464 - Estación Naranjal, Lima</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tel: (01) 244 84 32</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">WhatsApp: 926 402 348</p>
                                <a href="https://www.google.com/maps/dir/?api=1&destination=Av.+Contisuyo+464+Lima" target="_blank" class="text-primary hover:underline mt-1 inline-block">Cómo llegar →</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-primary mt-1">location_on</span>
                            <div>
                                <p class="font-semibold text-[#111618] dark:text-white">Los Olivos</p>
                                <p class="text-gray-600 dark:text-gray-300">Av. 2 de Octubre Mz. H2 lote 27 A.H., Los Olivos</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tel: (01) 504 82 85</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">WhatsApp: 935 383 940</p>
                                <a href="https://www.google.com/maps/dir/?api=1&destination=Av.+2+de+Octubre+Mz.+H2+lote+27+Los+Olivos" target="_blank" class="text-primary hover:underline mt-1 inline-block">Cómo llegar →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
</section>

<section>
</section>
<section class="py-12">
<h2 class="text-[#111618] dark:text-white text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Testimonios de Clientes</h2>
    <div class="flex overflow-x-auto [-ms-scrollbar-style:none] [scrollbar-width:none] [&amp;::-webkit-scrollbar]:hidden p-4 gap-4">
<div class="flex-shrink-0 w-80 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm" data-aos="fade-up" data-aos-delay="0">
<div class="flex items-center gap-4 mb-4">
<img class="size-12 rounded-full object-cover" data-alt="Foto de cliente satisfecho con su mascota" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCgc6QH8wtsxrBGL5Kg8t4PKkvmTEiq75rEZhHg5LzpyWZYlOW5mY5P-jX4zALaHmVsY2Xj3y6eXINXLs-vbXqJ6rHVve1xyJIa_Py4B9To0qeBvl5DpxVZ_nYH7PoyUxBTo5Gh3rprHBp9TtDiFetcsxnnFniGMMfTDBwcxl162iJzNgkFBl2YApxjfyhCX1RTUkTqiBaD84BVs9qSWdGUxmRhvhEALDufcNK578tyZthB7Khx1FZ6YZiV8ar7l2RQvOAemqYcnqI"/>
<div>
<p class="font-bold text-[#111618] dark:text-white">María P.</p>
<p class="text-sm text-gray-500 dark:text-gray-400">Dueña de 'Rocky'</p>
</div>
</div>
<p class="text-gray-600 dark:text-gray-300">"El trato que recibió mi perro fue excepcional. El personal es muy profesional y cariñoso. ¡Recomendadísimo!"</p>
</div>
<div class="flex-shrink-0 w-80 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm" data-aos="fade-up" data-aos-delay="100">
<div class="flex items-center gap-4 mb-4">
<img class="size-12 rounded-full object-cover" data-alt="Foto de cliente satisfecho con su mascota" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC23Ync2BQpkXZ-yrgvtR0_o4Q4axU4WKI-ied3wlORpiPC770RWHRc5EHRoE3RlaMrOSFy8rgEJ7KDSI7FPUogJJFE_IVGS1fQjmX3VJQ9l0Wh9sq2norP-Pr9VoRheRRDB4-GrwZ7IXBx95N-FOMtukZ4C92zoAvdXeg7Lk9I4XqBc3jRCoqXBJdO3LQ6sWKwrK87XZjj8GwLL17jyOQd5M47M-lmtxBl3PWtO59d4fs0MpIImuKkEp4MvYMDZlNAyQlbD12iPAo"/>
<div>
<p class="font-bold text-[#111618] dark:text-white">Juan G.</p>
<p class="text-sm text-gray-500 dark:text-gray-400">Dueño de 'Misha'</p>
</div>
</div>
<p class="text-gray-600 dark:text-gray-300">"Siempre me siento tranquilo dejando a mi gata en sus manos. Las instalaciones son impecables y el equipo es de primera."</p>
</div>
<div class="flex-shrink-0 w-80 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm" data-aos="fade-up" data-aos-delay="200">
<div class="flex items-center gap-4 mb-4">
<img class="size-12 rounded-full object-cover" data-alt="Foto de cliente satisfecho con su mascota" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBxOz32I-Kr8aqeNyMxSo5So0dP8TF5BEaXdxlv_sUM-uAswJOeYcC8c_0aHC_MRawv3HxbUwTkDuLrlJf6Bq21sSJJjEz89o1pagSzOwmLL1zoFaXfSNEEtUn6kudC0qOH2pVCrjBxDh2QV_Y9N-iU861XGSJUv6I32mMVkC9kE7nLd26COVyhXVT6dQwrl1y9xG4Vvu8NCKIJHkks2naApOip3H9YPXbuoyCEVuNIAbL_IdmQJFav2Kxyqg8Pq2nULXBYz-0SLZA"/>
<div>
<p class="font-bold text-[#111618] dark:text-white">Ana S.</p>
<p class="text-sm text-gray-500 dark:text-gray-400">Dueña de 'Pipo'</p>
</div>
</div>
<p class="text-gray-600 dark:text-gray-300">"La atención de emergencia fue rápida y eficaz. Salvaron la vida de mi perrito. Eternamente agradecida."</p>
</div>
</div>
</section>
<section class="py-16 bg-primary/10 dark:bg-primary/20 rounded-xl my-12">
<div class="container mx-auto px-4 text-center">
<h2 class="text-3xl font-bold text-[#111618] dark:text-white mb-4">¿Listo para darle a tu mascota el mejor cuidado?</h2>
<p class="text-lg text-gray-700 dark:text-gray-300 mb-8 max-w-2xl mx-auto">Regístrate ahora para agendar citas, ver el historial clínico y acceder a nuestros servicios en línea.</p>
<div class="flex justify-center gap-4">
<button class="flex min-w-[120px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90">
<span class="truncate">Registrarse</span>
</button>
<button class="flex min-w-[120px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-accent text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-accent/90">
<span class="truncate">Iniciar Sesión</span>
</button>
</div>
</div>
</section>
</main>
<footer class="mt-auto border-t border-gray-200 dark:border-gray-700 pt-8 pb-4">
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left">
<div>
<h3 class="font-bold text-lg mb-2 text-[#111618] dark:text-.white">Sede Olivos de Pro</h3>
<p class="text-sm text-gray-600 dark:text-gray-400">Av. 2 de Octubre Mz. H2 lote 27 A.H.<br/>Lima, Perú</p>
<div class="flex items-center gap-2">
    <a href="https://www.whatsapp.com/">
        <img src="img/whatsapp.ico" alt="whatsapp" style="width:20px; height:20px;"/>
    </a>
<p class="text-sm text-gray-600 dark:text-gray-400">935 383 940</p>
</div>
<p class="text-sm text-gray-600 dark:text-gray-400">(01) 504 82 85</p>
</div>
<div>
<h3 class="font-bold text-lg mb-2 text-[#111618] dark:text-white">Sede Independencia</h3>
<p class="text-sm text-gray-600 dark:text-gray-400">Av. Continsuyo 464 - Estación Naranjal.<br/>Lima, Perú</p>
<div class="flex items-center gap-2">
    <a href="https://www.whatsapp.com/">
        <img src="img/whatsapp.ico" alt="whatsapp" style="width:20px; height:20px;"/>
    </a>
    <p class="text-sm text-gray-600 dark:text-gray-400">926 402 348</p>
</div>
<p class="text-sm text-gray-600 dark:text-gray-400">(01) 244 84 32</p>
</div>
<div>
<h3 class="font-bold text-lg mb-2 text-[#111618] dark:text-white">Síguenos</h3>
<div class="flex justify-center md:justify-start gap-4 text-gray-600 dark:text-gray-400">
<a  href = "https://web.facebook.com/VETNORTH/?locale=es_LA&_rdc=1&_rdr#/">
    <img src = "img/facebook.ico" alt="facebook" style="width:70px; height:70px;"/>
</a>
<a href= "https://www.instagram.com/vetnorth24horas/?hl=es-la">
    <img src = "img/instagram.ico" alt="instagram" style="width:70px; height:70px;"/>
</a>
</div>
</div>
</div>
<div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700 text-center text-xs text-gray-500 dark:text-gray-400">
<p>© 2025 Clínica Veterinaria del Norte S.A.C. Todos los derechos reservados.</p>
<a class="hover:text-primary" href="#">Términos y Condiciones</a> | <a class="hover:text-primary" href="#">Política de Privacidad</a>
</div>
</footer>
</div>
</div>
</div>
</div>
</body></html>
