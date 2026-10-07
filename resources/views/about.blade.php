<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/jpeg" href="{{ asset('new-assets/images/tslogo.jpg') }}" />
    <title>About Us - TS language learning</title>
    <meta content="Learn about TS-language school and our world-class French trainers. Discover our mission to make language learning intelligently social." name="description" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link href="{{ asset('new-assets/css/main.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new-assets/images/tslogo.png') }}" rel="shortcut icon" type="image/x-icon" />
    <style>
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            -o-font-smoothing: antialiased;
        }
        
        /* Navbar fixes */
        .navbar_container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .navbar_menu {
            display: flex;
            align-items: center;
        }
        
        @media screen and (max-width: 991px) {
            .navbar_menu {
                position: fixed;
                top: 80px;
                left: 0;
                right: 0;
                background: white;
                padding: 20px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                display: none;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                z-index: 999;
                max-height: calc(100vh - 80px);
                overflow-y: auto;
                width: 100%;
            }
            
            .navbar_menu.w--open {
                display: flex !important;
            }
            
            .navbar_menu-button {
                display: flex !important;
                cursor: pointer;
                z-index: 1000;
                padding: 10px;
                background: transparent;
                border: none;
            }
            
            .menu-icon2 {
                width: 24px;
                height: 18px;
                position: relative;
                display: block;
            }
            
            .menu-icon2_line-top,
            .menu-icon2_line-middle,
            .menu-icon2_line-bottom {
                position: absolute;
                left: 0;
                width: 100%;
                height: 2px;
                background-color: #333;
                transition: all 0.3s ease;
            }
            
            .menu-icon2_line-top {
                top: 0;
            }
            
            .menu-icon2_line-middle {
                top: 50%;
                transform: translateY(-50%);
            }
            
            .menu-icon2_line-bottom {
                bottom: 0;
            }
            
            .navbar_menu-button.w--open .menu-icon2_line-top {
                transform: rotate(45deg);
                top: 8px;
            }
            
            .navbar_menu-button.w--open .menu-icon2_line-middle {
                opacity: 0;
            }
            
            .navbar_menu-button.w--open .menu-icon2_line-bottom {
                transform: rotate(-45deg);
                bottom: 8px;
            }
            
            .navbar_link {
                padding: 15px 20px;
                width: 100%;
                max-width: 300px;
                color: #333 !important;
                font-size: 16px;
                font-weight: 500;
                text-decoration: none;
                border-bottom: 1px solid #eee;
                display: block;
                transition: color 0.3s ease;
                text-align: center;
            }
            
            /* .navbar_link:hover {
                color: #ff6b6b !important;
            } */
            
            .navbar_link.w--current {
                color: #000000 !important;
                font-weight: 100;
            }
            
            .nav-join-mobile {
                background: #4d65ff !important;
                color: white !important;
                text-align: center;
                padding: 15px 20px !important;
                border-radius: 8px;
                margin-top: 15px;
                border: none !important;
                font-weight: 600;
                width: 100%;
                max-width: 300px;
                border-bottom: none !important;
            }
            
            .nav-join-mobile:hover {
                background: #4d65ff!important;
                color: white !important;
            }
            
            .navbar_button-wrapper {
                position: relative;
                z-index: 2;
            }
        }
        
        /* Join Now button fixes */
        .button.is-navbar-button {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 6px;
            background: #4d65ff;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .button.is-navbar-button:hover {
            background: #ff5252;
            transform: translateY(-1px);
        }
    </style>
    <script type="text/javascript">
        ! function(o, c) {
            var n = c.documentElement,
                t = " w-mod-";
            n.className += t + "js", ("ontouchstart" in o || o.DocumentTouch && c instanceof DocumentTouch) && (n.className += t + "touch")
        }(window, document);
    </script>
        <style>
                /* Mobile nav join button: hidden on desktop, visible on small screens */
                .nav-join-mobile { display: none; }
                @media (max-width: 991px) {
                    .nav-join-mobile { display: block; padding: .65rem 1rem; color: white; background: #ff6b6b; border-radius: 8px; text-align:center; margin: .5rem 0; }
                    /* Hide desktop join button on small screens to avoid duplicate CTA */
                    .navbar_button-wrapper .is-navbar-button { display: none !important; }
                }
        </style>
    <link href="{{ asset('new-assets/css/theme-overrides.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new-assets/css/theme-fonts-overrides.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
</head>

<body>
    <div class="page-wrapper">
        <div class="global-styles w-embed">
            <style>
                /* Set color style to inherit */
                .inherit-color * {
                    color: inherit;
                }
                /* Focus state style for keyboard navigation for the focusable elements */
                *[tabindex]:focus-visible,
                input[type="file"]:focus-visible {
                    outline: 0.125rem solid #4d65ff;
                    outline-offset: 0.125rem;
                }
                /* Make sure containers never lose their center alignment */
                .container-medium,
                .container-small,
                .container-large {
                    margin-right: auto !important;
                    margin-left: auto !important;
                }
                /* These classes are never overwritten */
                .hide {
                    display: none !important;
                }
                @media screen and (max-width: 991px) {
                    .hide,
                    .hide-tablet {
                        display: none !important;
                    }
                }
                @media screen and (max-width: 767px) {
                    .hide-mobile-landscape {
                        display: none !important;
                    }
                }
                @media screen and (max-width: 479px) {
                    .hide-mobile {
                        display: none !important;
                    }
                }
                
                /* Feature section responsive styles */
                .feature-section_component {
                    display: grid;
                    gap: 4rem;
                    align-items: center;
                }
                
                @media screen and (min-width: 768px) {
                    .feature-section_component {
                        grid-template-columns: 1fr 1fr;
                    }
                }
                
                .feature-cards_item {
                    padding: 2rem;
                    transition: transform 0.3s ease;
                }
                
                .feature-cards_item:hover {
                    transform: translateY(-5px);
                }
                
                .icon-embed-medium {
                    width: 64px;
                    height: 64px;
                    margin-bottom: 1rem;
                }
                
                @media screen and (max-width: 767px) {
                    .first-feature_image-wrapper,
                    .third-feature_image-wrapper {
                        height: 300px !important;
                        margin: -2rem -1rem 2rem -1rem;
                    }
                }
            </style>
        </div>
        <main class="main-wrapper">
            <div data-animation="default" class="navbar_component w-nav" data-easing2="ease" fs-scrolldisable-element="smart-nav" data-easing="ease" data-collapse="medium" data-w-id="bc8ac64c-e5a3-9bca-2793-85e40bc9be5a" role="banner" data-duration="400">
                <nav class="navbar_container">
                    <a href="{{ url('/') }}" aria-current="page" class="navbar_logo-link w-nav-brand">
                    <img src="{{ asset('new-assets/images/tslogo.png') }}" loading="lazy" width="Auto" height="Auto" alt="The TS-language school logo" class="navbar_logo"/></a>
                    <nav role="navigation" id="w-node-bc8ac64c-e5a3-9bca-2793-85e40bc9be5e-65a752e0" class="navbar_menu is-page-height-tablet w-nav-menu">
                        <a href="{{ route('courses.index') }}" class="navbar_link w-nav-link">Courses Offered</a>
                        <a href="{{ route('testimonials') }}" class="navbar_link w-nav-link">Testimonials</a>
                        <a href="{{ route('about') }}" class="navbar_link w-nav-link w--current">About Us</a>
                        <!-- Mobile-only Join Now link (displayed inside the collapsed nav on small screens) -->
                        <a href="{{ route('join-now') }}" class="navbar_link nav-join-mobile w-nav-link">Join Now</a>
                    
                    </nav>
                    <div id="w-node-bc8ac64c-e5a3-9bca-2793-85e40bc9be71-65a752e0" class="navbar_button-wrapper">
                        <a href="{{ route('join-now') }}" class="button is-navbar-button is-secondary w-button">Join Now</a>
                        <div class="navbar_menu-button w-nav-button">
                            <div class="menu-icon2">
                                <div class="menu-icon2_line-top"></div>
                                <div class="menu-icon2_line-middle">
                                    <div class="menu-icon_line-middle-inner"></div>
                                </div>
                                <div class="menu-icon2_line-bottom"></div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
    

            <section class="section_group-classes">
                <div class="padding-global">
                    <div class="container-large">
                        <div class="padding-section-medium">
                            <div class="w-layout-grid feature-section_component">
                                <style>
                                @keyframes imageEntrance {
                                  0% {
                                    opacity: 0;
                                    transform: translateX(-100px) scale(0.95) perspective(1000px) rotateY(-5deg);
                                  }
                                  60% {
                                    opacity: 1;
                                    transform: translateX(10px) scale(1.05) perspective(1000px) rotateY(-5deg);
                                  }
                                  100% {
                                    opacity: 1;
                                    transform: translateX(0) scale(1) perspective(1000px) rotateY(-5deg);
                                  }
                                }
                                .first-feature_image-wrapper {
                                  animation: imageEntrance 1.2s cubic-bezier(0.77,0,0.175,1) 0.2s both;
                                }
                                </style>
                                <div class="first-feature_image-wrapper" style="overflow: hidden; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.15); transform: perspective(1000px) rotateY(-5deg); transition: transform 0.4s ease, box-shadow 0.4s ease;">
                                    <img src="{{ asset('new-assets/images/city.jpg') }}" loading="lazy" sizes="(max-width: 767px) 90vw, 45vw" alt="French language class" class="feature-section_image-on-left" style="width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform 0.4s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" />
                                </div>
                                <div class="first-feature_content">
                                    <div class="margin-bottom margin-small">
                                        <h2>Our World-Class French Trainers</h2>
                                    </div>
                                    <div class="margin-bottom margin-xsmall">
                                        <p>At TS-language school, we take pride in our team of exceptional French trainers. Our instructors are not just teachers—they are passionate language experts dedicated to your success.</p>
                                    </div>
                                    <div class="first-feature_item-list">
                                        <div class="first-feature_item">
                                            <div class="first-feature_item-icon-wrapper">
                                                <div class="icon-embed-xsmall w-embed"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--ph" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 256"><path fill="currentColor" d="m232.49 80.49l-128 128a12 12 0 0 1-17 0l-56-56a12 12 0 1 1 17-17L96 183L215.51 63.51a12 12 0 0 1 17 17Z"></path></svg></div>
                                            </div>
                                            <p class="text-size-regular">Native and near-native French speakers</p>
                                        </div>
                                        <div class="first-feature_item">
                                            <div class="first-feature_item-icon-wrapper">
                                                <div class="icon-embed-xsmall w-embed"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--ph" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 256"><path fill="currentColor" d="m232.49 80.49l-128 128a12 12 0 0 1-17 0l-56-56a12 12 0 1 1 17-17L96 183L215.51 63.51a12 12 0 0 1 17 17Z"></path></svg></div>
                                            </div>
                                            <p class="text-size-regular">Certified language teaching qualifications</p>
                                        </div>
                                        <div class="first-feature_item">
                                            <div class="first-feature_item-icon-wrapper">
                                                <div class="icon-embed-xsmall w-embed"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--ph" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 256"><path fill="currentColor" d="m232.49 80.49l-128 128a12 12 0 0 1-17 0l-56-56a12 12 0 1 1 17-17L96 183L215.51 63.51a12 12 0 0 1 17 17Z"></path></svg></div>
                                            </div>
                                            <p class="text-size-regular">Years of experience in language education</p>
                                        </div>
                                        <div class="first-feature_item">
                                            <div class="first-feature_item-icon-wrapper">
                                                <div class="icon-embed-xsmall w-embed"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--ph" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 256"><path fill="currentColor" d="m232.49 80.49l-128 128a12 12 0 0 1-17 0l-56-56a12 12 0 1 1 17-17L96 183L215.51 63.51a12 12 0 0 1 17 17Z"></path></svg></div>
                                            </div>
                                            <p class="text-size-regular">Personalized teaching approach</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section_feedback">
                <div class="padding-global">
                    <div class="container-large">
                        <div class="padding-section-medium">
                            <div class="w-layout-grid feature-section_component">
                                <div class="third-feature_content scroll-animate-content">
                                    <div class="margin-bottom margin-small">
                                        <h2>Our Mission</h2>
                                    </div>
                                    <div class="margin-bottom margin-xsmall">
                                        <p class="text-size-medium">TS-language school is an intelligently social language learning platform that combines group lessons, peer exchanges, and AI-personalized feedback to make you fluent faster.</p>
                                    </div>
                                    <div class="first-feature_item-list">
                                        <div class="first-feature_item scroll-animate-item" style="--delay:0.1s">
                                            <div class="first-feature_item-icon-wrapper">
                                                <div class="icon-embed-xsmall w-embed"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--ph" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 256"><path fill="currentColor" d="m232.49 80.49l-128 128a12 12 0 0 1-17 0l-56-56a12 12 0 1 1 17-17L96 183L215.51 63.51a12 12 0 0 1 17 17Z"></path></svg></div>
                                            </div>
                                            <p class="text-size-regular">Interactive group lessons with expert instructors</p>
                                        </div>
                                        <div class="first-feature_item scroll-animate-item" style="--delay:0.2s">
                                            <div class="first-feature_item-icon-wrapper">
                                                <div class="icon-embed-xsmall w-embed"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--ph" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 256"><path fill="currentColor" d="m232.49 80.49l-128 128a12 12 0 0 1-17 0l-56-56a12 12 0 1 1 17-17L96 183L215.51 63.51a12 12 0 0 1 17 17Z"></path></svg></div>
                                            </div>
                                            <p class="text-size-regular">Immersive language exchanges with native speakers</p>
                                        </div>
                                        <div class="first-feature_item scroll-animate-item" style="--delay:0.3s">
                                            <div class="first-feature_item-icon-wrapper">
                                                <div class="icon-embed-xsmall w-embed"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--ph" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 256"><path fill="currentColor" d="m232.49 80.49l-128 128a12 12 0 0 1-17 0l-56-56a12 12 0 1 1 17-17L96 183L215.51 63.51a12 12 0 0 1 17 17Z"></path></svg></div>
                                            </div>
                                            <p class="text-size-regular">AI-powered personalized feedback and progress tracking</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="third-feature_image-wrapper scroll-animate-img" style="height: 500px; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                                    <img src="{{ asset('new-assets/images/sec.jpg') }}" alt="french images" 
                                         class="feature-section_image-on-right feedback-img"
                                         style="width: 100%; height: 100%; object-fit: cover; object-position: center;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <style>
            /* On-scroll animations */
            .scroll-animate-content,
            .scroll-animate-img,
            .scroll-animate-item {
                opacity: 0;
                transform: translateY(40px);
                transition: opacity 0.8s cubic-bezier(0.77,0,0.175,1),
                            transform 0.8s cubic-bezier(0.77,0,0.175,1);
            }
            .scroll-animate-content.revealed,
            .scroll-animate-img.revealed,
            .scroll-animate-item.revealed {
                opacity: 1;
                transform: translateY(0);
            }
            .scroll-animate-item {
                transition-delay: var(--delay, 0s);
            }
            .scroll-animate-img img {
                transition: transform 0.4s cubic-bezier(0.77,0,0.175,1),
                            box-shadow 0.4s cubic-bezier(0.77,0,0.175,1);
            }
            .scroll-animate-img img:hover {
                transform: scale(1.07);
                box-shadow: 0 8px 30px rgba(0,0,0,0.18);
            }
            </style>

            <script>
            // IntersectionObserver for scroll animations
            const animateObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            // Observe all animated elements
            document.querySelectorAll('.scroll-animate-content, .scroll-animate-img, .scroll-animate-item').forEach(el => {
                animateObserver.observe(el);
            });
            </script>



            <section class="section_feature-cards">
                <article class="padding-global">
                    <div class="container-large">
                        <div class="padding-section-medium">
                            <div class="feature-cards_component">
                                <div class="margin-bottom margin-large">
                                    <div class="text-align-center">
                                        <div class="max-width-large">
                                            <div class="margin-bottom margin-small">
                                                <h2>Why Choose TS-language school?</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-layout-grid feature-cards_list">
                                    <div class="feature-cards_item border-and-shadow-plum is-first scroll-reveal" style="--delay:0.1s">
                                        <div class="margin-bottom margin-small">
                                            <div class="icon-embed-medium text-color-grapefruit w-embed">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="margin-bottom margin-small">
                                            <h3>Expert Instruction</h3>
                                        </div>
                                        <p class="text-size-regular">Learn from world-class French trainers who bring years of experience and passion to every lesson.</p>
                                    </div>
                                    <div class="feature-cards_item border-and-shadow-plum scroll-reveal" style="--delay:0.3s">
                                        <div class="margin-bottom margin-small">
                                            <div class="icon-embed-medium text-color-grapefruit w-embed">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                    <line x1="9" y1="10" x2="15" y2="10"></line>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="margin-bottom margin-small">
                                            <h3>Personalized Learning</h3>
                                        </div>
                                        <p class="text-size-regular">Receive tailored feedback and exercises designed to meet your specific learning goals and pace.</p>
                                    </div>
                                    <div class="feature-cards_item border-and-shadow-plum is-last scroll-reveal" style="--delay:0.5s">
                                        <div class="margin-bottom margin-small">
                                            <div class="icon-embed-medium text-color-grapefruit w-embed">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="margin-bottom margin-small">
                                            <h3>Proven Results</h3>
                                        </div>
                                        <p class="text-size-regular">Join thousands of students who have achieved fluency through our innovative teaching methods.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </section>

            <style>
            /* Scroll-reveal animation */
            .scroll-reveal {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
                transition: opacity 0.8s cubic-bezier(0.77,0,0.175,1),
                            transform 0.8s cubic-bezier(0.77,0,0.175,1);
                transition-delay: var(--delay, 0s);
            }
            .scroll-reveal.revealed {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            </style>

            <script>
            // IntersectionObserver for scroll-reveal boxes
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    }
                });
            }, { threshold: 0.2 });

            document.querySelectorAll('.scroll-reveal').forEach(el => revealObserver.observe(el));
            </script>

            <section id="footer-section" class="footer accent-background footer_component footer-video">
  <div class="footer-video-bg" style="position:absolute;top:0;left:0;width:100%;height:100%;overflow:hidden;z-index:0;">
    <video autoplay muted loop playsinline poster="{{ asset('new-assets/images/footer-fallback.jpg') }}" style="width:100%;height:100%;object-fit:cover;">
      <source src="{{ asset('new-assets/images/footer.mp4') }}" type="video/mp4" />
    </video>
    <div class="footer-video-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;"></div>
  </div>
  <div class="padding-global footer-content" style="position:relative;z-index:1;color:#fff;">
    <div class="container footer-top">
      <div class="row gy-4">
        <!-- About Column -->
        <div class="col-lg-5 col-md-12 footer-about">
          <a href="{{ url('/') }}" class="logo d-flex align-items-center mb-3"><br>
            <img src="{{ asset('new-assets/images/tslogo12.jpg') }}" loading="lazy" alt="TS-Language School logo" class="navbar_logo" style="width: 120px; height: 100%;"/>
          </a>
          <p class="footer-description" style="color:#fff;">
            TS-Language School
          </p>
        </div>

        <!-- Quick Links Column -->
        <div class="col-lg-2 col-6 footer-links"><br>
          <h4 class="footer-heading" style="color:#fff;">Quick Links</h4>
          <ul class="footer-list">
            <li><a href="{{ url('/') }}" class="footer-link" style="color:#fff;">Home</a></li>
            <li><a href="{{ route('about') }}" class="footer-link" style="color:#fff;">About Us</a></li>
            <li><a href="{{ route('courses.index') }}" class="footer-link" style="color:#fff;">Courses</a></li>
            <li><a href="{{ route('testimonials') }}" class="footer-link" style="color:#fff;">Testimonials</a></li>
          </ul>
        </div>

        <!-- Social Column -->
        <div class="col-lg-5 col-md-12 footer-social text-center text-md-start"><br>
          <h4 class="footer-heading" style="color:#fff;">Follow Us</h4>
          <div class="social-links d-flex mt-2">
            <a href="#" aria-label="Twitter" class="social-link" style="color:#fff;"><i class="bi bi-twitter-x"></i></a>
            <a href="#" aria-label="Facebook" class="social-link" style="color:#fff;"><i class="bi bi-facebook"></i></a>
            <a href="#" aria-label="Instagram" class="social-link" style="color:#fff;"><i class="bi bi-instagram"></i></a>
            <a href="#" aria-label="LinkedIn" class="social-link" style="color:#fff;"><i class="bi bi-linkedin"></i></a>
            <a href="#" aria-label="YouTube" class="social-link" style="color:#fff;"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="container footer-bottom mt-4 pt-3 border-top">
      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start">
          <p class="footer-copyright mb-0 text-center" style="text-align:center;color:#fff;"> &copy; <span id="currentYear"></span> TS-Language School. All rights reserved.</p>
        </div>
      </div>
    </div>
  </div>
</section>
    </main>
    </div>
    <script src="{{ asset('new-assets/js/vendor/jquery-3.5.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('new-assets/js/webflow.98f3efb7d.js') }}" type="text/javascript"></script>
    
    <script>
        // Fix for hamburger menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.querySelector('.navbar_menu-button');
            const menu = document.querySelector('.navbar_menu');
            
            if (menuButton && menu) {
                menuButton.addEventListener('click', function() {
                    // Toggle the w--open class on both button and menu
                    menuButton.classList.toggle('w--open');
                    menu.classList.toggle('w--open');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!menuButton.contains(event.target) && !menu.contains(event.target)) {
                        menuButton.classList.remove('w--open');
                        menu.classList.remove('w--open');
                    }
                });
                
                // Close menu when clicking on a link (for mobile)
                const menuLinks = menu.querySelectorAll('.navbar_link');
                menuLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        menuButton.classList.remove('w--open');
                        menu.classList.remove('w--open');
                    });
                });
            }
        });
    </script>
    <style>
    @keyframes feedbackImageEntrance {
      0% {
        opacity: 0;
        transform: translateY(40px) scale(0.95);
      }
      60% {
        opacity: 1;
        transform: translateY(-10px) scale(1.05);
      }
      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
    .third-feature_image-wrapper img {
      animation: feedbackImageEntrance 1.2s cubic-bezier(0.77,0,0.175,1) 0.2s both;
      transition: transform 0.4s cubic-bezier(0.77,0,0.175,1), box-shadow 0.4s cubic-bezier(0.77,0,0.175,1);
    }
    .third-feature_image-wrapper img:hover {
      transform: scale(1.07);
      box-shadow: 0 8px 30px rgba(0,0,0,0.18);
    }
    </style>
</body>

</html>
