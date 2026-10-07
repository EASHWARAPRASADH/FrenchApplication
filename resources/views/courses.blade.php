<!DOCTYPE html>
<html data-wf-domain="www.learnwithhiya.com" data-wf-page="courses" data-wf-site="65f05b39b3ea937565a75298" lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/jpeg" href="{{ asset('new-assets/images/tslogo.png') }}" />
    <title>French Courses - TS language learning</title>
    <meta content="Explore our comprehensive French language courses from beginner (A1, A2) to advanced (C1, C2) levels. Join interactive group lessons with world-class instructors." name="description" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link href="{{ asset('new-assets/css/main.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcgl9M+6dLJdzfHn1PZK7zF9O6Rz3iFfXb6l5z5u5u5u5u5u5u5u5u5u5u5u5u5u5u5u5u" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="{{ asset('new-assets/css/radial-orbital-timeline.css') }}" rel="stylesheet" type="text/css" />
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
                align-items: flex-start;
            }
            
            .navbar_menu.w--open {
                display: flex;
            }
            
            .navbar_link {
                padding: 10px 0;
                width: 100%;
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
            background: #ff6b6b;
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
                    .nav-join-mobile { display: block; padding: .65rem 1rem;; border-radius: 8px; text-align:center; margin: .5rem 0; }
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
                /* SVG letter animation styles - animate per-letter opacity along the path */
                .hero-fadein-wrapper { position: relative; }
                /* Target individual textPath elements we'll create dynamically */
                #hero-svg textPath.animated-letter { opacity: 0; will-change: opacity, transform; }
                @keyframes letterIn {
                    from { opacity: 0; transform: translateY(12px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                /* Ensure the three course cards remain in a single row but scale down on smaller screens */
                .feature-cards_list { overflow-x: visible; }
                @media (max-width: 900px) {
                    .feature-cards_list { grid-template-columns: repeat(3, minmax(180px, 1fr)) !important; }
                }
                @media (max-width: 600px) {
                    .feature-cards_list { grid-template-columns: repeat(3, minmax(140px, 1fr)) !important; }
                }
            </style>
        </div>
        <main class="main-wrapper">
            <div data-animation="default" class="navbar_component w-nav" data-easing2="ease" fs-scrolldisable-element="smart-nav" data-easing="ease" data-collapse="medium" data-w-id="bc8ac64c-e5a3-9bca-2793-85e40bc9be5a" role="banner" data-duration="400">
                <nav class="navbar_container">
                    <a href="{{ url('/') }}" class="navbar_logo-link w-nav-brand">
                        <img src="{{ asset('new-assets/images/tslogo.png') }}" loading="lazy" width="Auto" height="Auto" alt="The TS-language school" class="navbar_logo"/>
                    </a>
                    <nav role="navigation" id="w-node-bc8ac64c-e5a3-9bca-2793-85e40bc9be5e-65a752e0" class="navbar_menu is-page-height-tablet w-nav-menu">
                        <a href="{{ route('courses.index') }}" class="navbar_link w-nav-link w--current">Courses Offered</a>
                        <a href="{{ route('testimonials') }}" class="navbar_link w-nav-link">Testimonials</a>
                        <a href="{{ route('about') }}" class="navbar_link w-nav-link">About Us</a>
                        <!-- Mobile-only Join Now link (inside collapsed nav on small screens) -->
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
            <header class="section_header" style="margin-top: 0; padding-top: 0; position: relative; overflow: hidden;">
                <canvas id="shader-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;"></canvas>
                <div class="hero-fadein-wrapper animate-fade-in" style="height: clamp(160px, 40vw, 300px); position: relative; z-index: 1;">
                    <svg id="hero-svg" viewBox="0 0 1000 300" class="max-w-[1024px] mr-auto ml-auto w-[1024px] h-[300px]" stroke-width="2" preserveAspectRatio="xMidYMid meet" data-icon-replaced="true" style="width: 100%; height: 300px; color: #fff;">
                        <defs>
                            <path id="curve-title" d="M 100,240 Q 500,80 900,240" fill="transparent"></path>
                            <path id="curve-subtitle" d="M 100,320 L 900,320" fill="transparent"></path>
                        </defs>
                        <text class="main-hero-title" text-anchor="middle" style="font-family: 'Bebas Neue', Impact, Inter, ui-sans-serif, system-ui; font-size: 80px; font-weight: bold; letter-spacing: 0.03em; fill: #fff; text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
                            <textPath href="#curve-title" startOffset="400">Learn.  Speak.  Growth.</textPath>
                        </text>
                      
                    </svg>
                    <!-- No HTML overlay: letters will be created inside the SVG so they follow the curve exactly -->
                </div>
                <p style="font-weight: 500; font-size: 1.5rem; color: black; text-align: center; margin: 2rem 0; line-height: 1.4; position: relative; z-index: 1; text-shadow: 0 1px 4px rgba(0,0,0,0.5);">
                    Master French with ease. Accelerate your fluency through smart learning.
                </p>
            </header>

            <section class="section_animation" style="position: relative; overflow: visible; display: block; background: none;">
                <div class="padding-global" style="position: relative; z-index: 2;">
                    <div class="container-large">
                        <div class="padding-section-medium">
                            <!-- Content that should remain in normal flow (no overlay) -->
                            <div class="text-align-center">
                                <div class="max-width-large align-center">
                                    <div class="margin-bottom margin-large">
                                        <h2 style="font-size: 3rem; margin-bottom: 2rem; color: #111;">French Language Courses</h2>
                                        <p class="text-size-large" style="margin-bottom: 3rem; color: #333;">Discover our comprehensive French language program designed to take you from beginner to fluency with expert instructors and innovative teaching methods.</p>
                                    </div>
                                    
                                    <!-- Featured Course (Case Study Style) -->
                                    <div class="featured-course-container" style="border: 3px solid #1e40af; background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 50%, #ffffff 100%); border-radius: 20px; overflow: hidden; margin-bottom: 4rem; box-shadow: 0 15px 50px rgba(30, 64, 175, 0.15); transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); position: relative;" onmouseover="this.style.transform='translateY(-12px)'; this.style.boxShadow='0 25px 70px rgba(30, 64, 175, 0.25)'; this.style.borderColor='#2563eb'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 15px 50px rgba(30, 64, 175, 0.15)'; this.style.borderColor='#1e40af'">
                                        <a href="{{ route('join-now') }}" class="featured-course-link group" style="display: grid; gap: 1rem; overflow: hidden; padding: 2.5rem; text-decoration: none; transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); grid-template-columns: 1fr 1fr; align-items: center; position: relative;" onmouseover="this.style.background='linear-gradient(135deg, rgba(30, 64, 175, 0.02) 0%, rgba(37, 99, 235, 0.05) 50%, rgba(255, 255, 255, 0.1) 100%)'" onmouseout="this.style.background='transparent'">
                                            <div class="featured-course-content" style="display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; padding-top: 2rem; padding-bottom: 2rem;">
                                                <div class="course-header" style="display: flex; align-items: center; gap: 0.75rem; font-size: 1.5rem; font-weight: 600; color: #111;">
                                                    <div class="course-icon" style="width: 56px; height: 56px; background: linear-gradient(135deg, #1e40af 0%, #2563eb 30%, #3b82f6 60%, #60a5fa 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 25px rgba(30, 64, 175, 0.35); transition: all 0.4s ease; position: relative; overflow: hidden;" onmouseover="this.style.transform='rotate(15deg) scale(1.15)'; this.style.boxShadow='0 10px 35px rgba(30, 64, 175, 0.45)'; this.style.background='linear-gradient(135deg, #1e40af 0%, #2563eb 20%, #3b82f6 50%, #60a5fa 80%, #93c5fd 100%)'" onmouseout="this.style.transform='rotate(0deg) scale(1)'; this.style.boxShadow='0 6px 25px rgba(30, 64, 175, 0.35)'; this.style.background='linear-gradient(135deg, #1e40af 0%, #2563eb 30%, #3b82f6 60%, #60a5fa 100%)'">
                                                        <div class="icon-embed-medium text-color-grapefruit w-embed" style="color: white;">
                                                            <svg width="20" height="20" viewBox="0 0 55 55" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M27.5039 5.25049C15.0974 5.25049 5.00391 15.344 5.00391 27.7505C5.00391 40.157 15.0974 50.2505 27.5039 50.2505C39.9104 50.2505 50.0039 40.157 50.0039 27.7505C50.0039 15.344 39.9104 5.25049 27.5039 5.25049Z" fill="#FFD700"/>
                                                                <path d="M18.5039 36.7505L31.9837 32.2505L36.5039 18.7505L23.0039 23.2505L18.5039 36.7505Z" fill="#1e40af"/>
                                                            </svg>

                                                              
                                                        </div>
                                                    </div>
                                                    <span style="font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, #1e40af, #2563eb, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-shadow: 0 2px 10px rgba(30, 64, 175, 0.1);">Advanced French</span>
                                                </div>
                                                <div class="course-details">
                                                    <span class="course-tags" style="font-size: 0.875rem; color: #1e40af; text-transform: uppercase; letter-spacing: 0.15em; font-weight: 700; background: linear-gradient(135deg, rgba(30, 64, 175, 0.08) 0%, rgba(37, 99, 235, 0.12) 50%, rgba(59, 130, 246, 0.08) 100%); padding: 0.75rem 1.5rem; border-radius: 25px; display: inline-block; border: 1px solid rgba(30, 64, 175, 0.2); box-shadow: 0 2px 8px rgba(30, 64, 175, 0.1); transition: all 0.3s ease;" onmouseover="this.style.background='linear-gradient(135deg, rgba(30, 64, 175, 0.15) 0%, rgba(37, 99, 235, 0.2) 50%, rgba(59, 130, 246, 0.15) 100%)'; this.style.borderColor='#2563eb'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='linear-gradient(135deg, rgba(30, 64, 175, 0.08) 0%, rgba(37, 99, 235, 0.12) 50%, rgba(59, 130, 246, 0.08) 100%)'; this.style.borderColor='#1e40af'; this.style.transform='translateY(0)'">PROFICIENCY LEVEL / CERTIFICATION READY [C1 and C2]</span>
                                                    <h2 class="course-title" style="margin-top: 1rem; margin-bottom: 1.25rem; font-size: 2rem; font-weight: 700; line-height: 1.2; color: #111;">
                                                        Master French with Advanced Level Training
                                                        <span class="course-subtitle" style="font-weight: 500; color: #1e40af; opacity: 0.85; transition: all 0.4s ease; display: block; margin-top: 0.75rem; font-size: 1.125rem;" onmouseover="this.style.opacity='1'; this.style.transform='translateX(12px)'; this.style.color='#2563eb'" onmouseout="this.style.opacity='0.85'; this.style.transform='translateX(0)'; this.style.color='#1e40af'"> Achieve native-level fluency and cultural understanding</span>
                                                    </h2>
                                                    <div class="cta-button" style="display: inline-flex; align-items: center; gap: 0.875rem; font-weight: 700; color: white; background: linear-gradient(135deg, #1e40af 0%, #2563eb 30%, #3b82f6 70%, #60a5fa 100%); padding: 1.25rem 2.5rem; border-radius: 60px; box-shadow: 0 6px 25px rgba(30, 64, 175, 0.35); transition: all 0.4s ease; position: relative; overflow: hidden; border: 2px solid transparent;" onmouseover="this.style.transform='translateY(-3px) scale(1.05)'; this.style.boxShadow='0 12px 40px rgba(30, 64, 175, 0.45)'; this.style.background='linear-gradient(135deg, #1e40af 0%, #2563eb 20%, #3b82f6 60%, #60a5fa 90%, #93c5fd 100%)'; this.style.borderColor='rgba(255, 255, 255, 0.3)'" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 6px 25px rgba(30, 64, 175, 0.35)'; this.style.background='linear-gradient(135deg, #1e40af 0%, #2563eb 30%, #3b82f6 70%, #60a5fa 100%)'; this.style.borderColor='transparent'">
                                                        <span>Enroll Now</span>
                                                        <svg class="arrow-icon" style="width: 22px; height: 22px; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="featured-course-image" style="position: relative; padding: 2rem 0;">
                                                <div class="image-wrapper" style="position: relative; height: 100%; border: 3px solid #1e40af; background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 50%, #ffffff 100%); padding: 1.5rem; border-radius: 16px; box-shadow: 0 10px 40px rgba(30, 64, 175, 0.25); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.transform='scale(1.05) rotate(1deg)'; this.style.borderColor='#2563eb'; this.style.boxShadow='0 15px 50px rgba(30, 64, 175, 0.35)'" onmouseout="this.style.transform='scale(1) rotate(0deg)'; this.style.borderColor='#1e40af'; this.style.boxShadow='0 10px 40px rgba(30, 64, 175, 0.25)'">
                                                    <div style="height: 100%; overflow: hidden; border-radius: 4px;">
                                                        <img src="{{ asset('new-assets/images/ce1.jpg') }}" alt="Advanced French Course" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; aspect-ratio: 14/9;">
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="additional-courses" style="display: flex; border-top: 3px solid #1e40af; background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 50%, #ffffff 100%); transition: all 0.4s ease;" onmouseover="this.style.background='linear-gradient(135deg, #dbeafe 0%, #eff6ff 50%, #f0f9ff 100%)'" onmouseout="this.style.background='linear-gradient(135deg, #eff6ff 0%, #f0f9ff 50%, #ffffff 100%)'">
                                            <div class="course-spacer" style="display: none; width: 7rem; flex-shrink: 0; background: radial-gradient(#d1d5db 1px, transparent 1px); background-size: 10px 10px; opacity: 0.15;"></div>
                                            <div class="courses-grid" style="display: grid; grid-template-columns: 1fr 1fr; width: 100%;">
                                                
                                                <!-- Beginner Course -->
                                                <a href="{{ route('join-now') }}" class="course-card beginner" style="display: flex; flex-direction: column; padding: 2rem; text-decoration: none; color: inherit; border-right: 2px solid #1e40af; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #f0f9ff 100%); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden;" onmouseover="this.style.transform='translateX(-8px) translateY(-4px)'; this.style.background='linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #dbeafe 100%)'; this.style.boxShadow='0 12px 40px rgba(30, 64, 175, 0.15), 0 0 0 1px rgba(59, 130, 246, 0.1)'" onmouseout="this.style.transform='translateX(0) translateY(0)'; this.style.background='linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #f0f9ff 100%)'; this.style.boxShadow='none'; this.style.borderImage='none'">
                                                    <div class="course-header" style="display: flex; align-items: center; gap: 0.75rem; font-size: 1.5rem; font-weight: 600; color: #111;">
                                                        <div class="course-icon" style="width: 48px; height: 48px; background: linear-gradient(135deg, #1e40af 0%, #2563eb 30%, #3b82f6 60%, #60a5fa 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(30, 64, 175, 0.3); transition: all 0.4s ease; position: relative; overflow: hidden;" onmouseover="this.style.transform='rotate(8deg) scale(1.1)'; this.style.boxShadow='0 8px 30px rgba(30, 64, 175, 0.4)'" onmouseout="this.style.transform='rotate(0deg) scale(1)'; this.style.boxShadow='0 4px 20px rgba(30, 64, 175, 0.3)'">
                                                            <div class="icon-embed-medium text-color-grapefruit w-embed" style="color: white;">
                                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="#FFD700"/>
                                                                </svg>
                                                               

                                                            </div>
                                                        </div>
                                                        <span style="font-size: 1.5rem; font-weight: 700; background: linear-gradient(135deg, #1e40af, #2563eb, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Beginner</span>
                                                    </div>
                                                    <div class="course-details"><br>
                                                            <span class="course-tags" style="font-size: 0.75rem; color: #1e40af; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; background: linear-gradient(135deg, rgba(30, 64, 175, 0.08) 0%, rgba(37, 99, 235, 0.12) 50%, rgba(59, 130, 246, 0.08) 100%); padding: 0.5rem 1rem; border-radius: 20px; display: inline-block; border: 1px solid rgba(30, 64, 175, 0.15);">FOUNDATION LEVEL/ BASIC COMMUNICATION [A1 and A2]</span> 
                                                        <h2 class="course-title" style="margin-top: 1rem; margin-bottom: 1.25rem; font-size: 1.5rem; font-weight: 700; line-height: 1.2; color: #111;">
                                                            Start Your French Journey
                                                            <span class="course-subtitle" style="font-weight: 500; color: #1e40af; opacity: 0.8; transition: all 0.3s ease; display: block; margin-top: 0.5rem;" onmouseover="this.style.opacity='1'; this.style.transform='translateX(8px)'" onmouseout="this.style.opacity='0.8'; this.style.transform='translateX(0)'"> Learn basics and everyday conversations</span>
                                                        </h2>
                                                        <div class="cta-button" style="display: inline-flex; align-items: center; gap: 0.75rem; font-weight: 600; color: white; background: linear-gradient(135deg, #1e40af, #2563eb); padding: 0.75rem 1.5rem; border-radius: 50px; box-shadow: 0 3px 15px rgba(30, 64, 175, 0.25); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 25px rgba(30, 64, 175, 0.35)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 15px rgba(30, 64, 175, 0.25)'">
                                                            <span>Enroll Now</span>
                                                            <svg class="arrow-icon" style="width: 18px; height: 18px; transition: transform 0.3s ease;" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </a>

                                                <!-- Intermediate Course -->
                                                <a href="{{ route('join-now') }}" class="course-card group" style="display: flex; flex-direction: column; justify-content: space-between; gap: 3rem; padding: 2rem 1.5rem; text-decoration: none; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);" onmouseover="this.style.background='linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%)'; this.style.transform='translateX(4px)'" onmouseout="this.style.background='linear-gradient(135deg, #eff6ff 0%, #ffffff 100%)'; this.style.transform='translateX(0)'">
                                                    <div class="course-header" style="display: flex; align-items: center; gap: 0.75rem; font-size: 1.5rem; font-weight: 600; color: #111;">
                                                        <div class="course-icon" style="width: 40px; height: 40px; background: linear-gradient(135deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 15px rgba(37, 99, 235, 0.25); transition: all 0.3s ease;" onmouseover="this.style.transform='rotate(-5deg) scale(1.05)'" onmouseout="this.style.transform='rotate(0deg) scale(1)'">
                                                            <div class="icon-embed-medium text-color-grapefruit w-embed" style="color: white;">
                                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zM12 22a2 2 0 002-2h-4a2 2 0 002 2z" fill="#FFD700"/>
                                                                </svg>

                                                            </div>
                                                        </div>
                                                        <span style="font-size: 1.5rem; font-weight: 700; background: linear-gradient(135deg, #1e40af, #2563eb); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Intermediate</span>
                                                    </div>
                                                    <div class="course-details">
                                                        <span class="course-tags" style="font-size: 0.875rem; color: #2563eb; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; background: rgba(37, 99, 235, 0.1); padding: 0.5rem 1rem; border-radius: 20px; display: inline-block;">INTERMEDIATE LEVEL / FLUENCY BUILDING [B1 and B2]</span>
                                                        <h2 class="course-title" style="margin-top: 1rem; margin-bottom: 1.25rem; font-size: 1.5rem; font-weight: 700; line-height: 1.2; color: #111;">
                                                            Build Your Fluency
                                                            <span class="course-subtitle" style="font-weight: 500; color: #2563eb; opacity: 0.8; transition: all 0.3s ease; display: block; margin-top: 0.5rem;" onmouseover="this.style.opacity='1'; this.style.transform='translateX(8px)'" onmouseout="this.style.opacity='0.8'; this.style.transform='translateX(0)'"> Complex grammar and conversations</span>
                                                        </h2>
                                                        <div class="cta-button" style="display: inline-flex; align-items: center; gap: 0.75rem; font-weight: 600; color: white; background: linear-gradient(135deg, #1e40af, #2563eb); padding: 0.75rem 1.5rem; border-radius: 50px; box-shadow: 0 3px 15px rgba(30, 64, 175, 0.25); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 25px rgba(30, 64, 175, 0.35)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 15px rgba(30, 64, 175, 0.25)'">
                                                            <span>Enroll Now</span>
                                                            <svg class="arrow-icon" style="width: 18px; height: 18px; transition: transform 0.3s ease;" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="course-spacer" style="display: none; width: 7rem; flex-shrink: 0; background: radial-gradient(#d1d5db 1px, transparent 1px); background-size: 10px 10px; opacity: 0.15;"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Call to Action -->
                                    <div class="margin-top margin-large">
                                        <div class="cta-section" style="text-align: center; margin-top: 4rem; padding: 3rem 2rem; background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-radius: 16px; border: 2px solid #1e40af; box-shadow: 0 8px 32px rgba(30, 64, 175, 0.15); transition: all 0.4s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 40px rgba(30, 64, 175, 0.25)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 32px rgba(30, 64, 175, 0.15)'">
                                            <h3 style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem; background: linear-gradient(135deg, #1e40af, #2563eb); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Ready to Start Your French Journey?</h3>
                                            <p style="font-size: 1.125rem; color: #6b7280; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">Join thousands of students who have transformed their lives with our comprehensive French language programs.</p>
                                            <a href="{{ route('join-now') }}" class="cta-button-large" style="display: inline-flex; align-items: center; gap: 0.75rem; font-weight: 600; color: white; background: linear-gradient(135deg, #1e40af, #2563eb); padding: 1rem 2.5rem; border-radius: 50px; box-shadow: 0 6px 25px rgba(30, 64, 175, 0.3); transition: all 0.3s ease; text-decoration: none; font-size: 1.125rem;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 35px rgba(30, 64, 175, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 25px rgba(30, 64, 175, 0.3)'">
                                                <span>Get Started Today</span>
                                                <svg class="arrow-icon" style="width: 20px; height: 20px; transition: transform 0.3s ease;" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
        </section>

        <!-- JavaScript for Case Study Style Animations -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Featured course hover effects
                const featuredCourse = document.querySelector('.featured-course-link');
                const featuredImage = featuredCourse.querySelector('img');
                const arrowIcons = document.querySelectorAll('.arrow-icon');
                
                featuredCourse.addEventListener('mouseenter', () => {
                    featuredCourse.style.background = '#f9fafb';
                    featuredImage.style.transform = 'scale(1.05)';
                    arrowIcons.forEach(icon => {
                        icon.style.transform = 'translateX(4px)';
                    });
                });
                
                featuredCourse.addEventListener('mouseleave', () => {
                    featuredCourse.style.background = 'transparent';
                    featuredImage.style.transform = 'scale(1)';
                    arrowIcons.forEach(icon => {
                        icon.style.transform = 'translateX(0)';
                    });
                });

                // Individual course cards hover effects
                const courseCards = document.querySelectorAll('.course-card');
                courseCards.forEach(card => {
                    const arrowIcon = card.querySelector('.arrow-icon');
                    
                    card.addEventListener('mouseenter', () => {
                        card.style.background = '#f9fafb';
                        if (arrowIcon) {
                            arrowIcon.style.transform = 'translateX(4px)';
                        }
                    });
                    
                    card.addEventListener('mouseleave', () => {
                        card.style.background = 'white';
                        if (arrowIcon) {
                            arrowIcon.style.transform = 'translateX(0)';
                        }
                    });
                });

                // Smooth scroll animations
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                }, observerOptions);

                // Apply fade-in animation to course containers
                const courseContainers = document.querySelectorAll('.featured-course-container, .course-card');
                courseContainers.forEach((container, index) => {
                    container.style.opacity = '0';
                    container.style.transform = 'translateY(30px)';
                    container.style.transition = `all 0.6s ease ${index * 0.2}s`;
                    observer.observe(container);
                });

                // Parallax effect on scroll
                let ticking = false;
                function updateParallax() {
                    const scrolled = window.pageYOffset;
                    const featuredContainer = document.querySelector('.featured-course-container');
                    
                    if (featuredContainer) {
                        const speed = 0.5;
                        const yPos = -(scrolled * speed) * 0.1;
                        featuredContainer.style.transform = `translateY(${yPos}px)`;
                    }
                    
                    ticking = false;
                }

                function requestTick() {
                    if (!ticking) {
                        window.requestAnimationFrame(updateParallax);
                        ticking = true;
                    }
                }

                window.addEventListener('scroll', requestTick);
            });
        </script>

        <!-- Responsive Styles -->
        <style>
            @media (max-width: 991px) {
                .featured-course-link {
                    grid-template-columns: 1fr !important;
                    gap: 2rem !important;
                }
                
                .featured-course-content {
                    padding-top: 1rem !important;
                    padding-bottom: 1rem !important;
                }
                
                .featured-course-image {
                    padding: 1rem 0 !important;
                }
                
                .courses-grid {
                    grid-template-columns: 1fr !important;
                }
                
                .course-card {
                    border-right: none !important;
                    border-bottom: 1px solid #e5e7eb !important;
                }
                
                .course-card:last-child {
                    border-bottom: none !important;
                }
            }
            
            @media (max-width: 767px) {
                .featured-course-container {
                    margin-bottom: 2rem !important;
                }
                
                .featured-course-link {
                    padding: 1rem !important;
                }
                
                .course-title {
                    font-size: 1.5rem !important;
                }
                
                .course-header {
                    font-size: 1.25rem !important;
                }
                
                .course-icon {
                    width: 32px !important;
                    height: 32px !important;
                }
                
                .course-card {
                    padding: 1.5rem 1rem !important;
                    gap: 2rem !important;
                }
            }
        </style>

        <!-- New Sections for Tests and Diplomas -->
        <section class="section_test-prep" style="background: #f8f9fa; padding: 4rem 0;">
            <div class="padding-global">
                <div class="container-large">
                    <div class="padding-section-medium">
                        <div class="text-align-center" style="max-width: 1200px; margin: 0 auto;">
                            <h2 style="font-size: 2.5rem; margin-bottom: 3rem; color: #111; text-align: center;">French Certification Tests & Diplomas</h2>
                            
                            <!-- Test and Diploma Cards Container -->
                            <div style="display: flex; gap: 2rem; justify-content: center; align-items: stretch; margin-bottom: 3rem; flex-wrap: wrap;">
                                <!-- TCF Section -->
                                <div style="flex: 1; min-width: 300px; max-width: 350px;">
                                    <div class="feature-cards_item border-and-shadow-plum" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 2rem; margin-bottom: 2rem; border-radius: 16px; box-shadow: 0 8px 32px rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.2); transition: all 0.3s ease; height: 100%;">
                                        <h3 style="font-size: 1.8rem; margin-bottom: 1rem; color: #1e40af; text-align: center; font-weight: 700;">Test: TCF</h3>
                                        <p style="font-size: 1.1rem; line-height: 1.6; color: #1e293b; text-align: center;">
                                            The Test de Connaissance du Français (TCF) is a standardized test that evaluates your French language skills for academic, professional, or personal purposes. Our comprehensive TCF preparation program covers all test components to ensure you achieve your desired score.
                                        </p>
                                    </div>
                                </div>

                                <!-- DELF Section -->
                                <div style="flex: 1; min-width: 300px; max-width: 350px;">
                                    <div class="feature-cards_item border-and-shadow-plum" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 2rem; margin-bottom: 2rem; border-radius: 16px; box-shadow: 0 8px 32px rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.2); transition: all 0.3s ease; height: 100%;">
                                        <h3 style="font-size: 1.8rem; margin-bottom: 1rem; color: #1e40af; text-align: center; font-weight: 700;">Diplomas: DELF</h3>
                                        <p style="font-size: 1.1rem; line-height: 1.6; color: #1e293b; text-align: center;">
                                            The Diplôme d'Études en Langue Française (DELF) is an official diploma awarded by the French Ministry of Education to certify French language abilities. We offer preparation for all DELF levels (A1-B2) with expert guidance and practice materials.
                                        </p>
                                    </div>
                                </div>

                                <!-- Preparation Module Section -->
                                <div style="flex: 1; min-width: 300px; max-width: 350px;">
                                    <div class="feature-cards_item border-and-shadow-plum" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 2rem; margin-bottom: 2rem; border-radius: 16px; box-shadow: 0 8px 32px rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.2); transition: all 0.3s ease; height: 100%;">
                                        <h3 style="font-size: 1.8rem; margin-bottom: 1rem; color: #1e40af; text-align: center; font-weight: 700;">Preparation Module and Practice Tests</h3>
                                        <p style="font-size: 1.1rem; line-height: 1.6; color: #1e293b; text-align: center;">
                                            Our intensive preparation module combines theoretical knowledge with extensive practice. Get access to mock tests, personalized feedback, and targeted exercises to build confidence and improve your test-taking strategies.
                                        </p>
                                    </div>
                                </div>
                            </div>
                               
                        </div>
                    </div>
                </div>
            </div>
            <!-- Responsive CSS for mobile and tablet -->
            <style>
                @media (max-width: 991px) {
                    .section_test-prep {
                        padding: 2rem 0 !important;
                    }
                    
                    .section_test-prep h2 {
                        font-size: 2rem !important;
                        margin-bottom: 2rem !important;
                    }
                    
                    .section_test-prep h3 {
                        font-size: 1.5rem !important;
                    }
                    
                    .section_test-prep .feature-cards_item {
                        padding: 1.5rem !important;
                        margin-bottom: 1.5rem !important;
                    }
                    
                    .section_test-prep .feature-cards_list {
                        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
                        gap: 1rem !important;
                    }
                }
                
                @media (max-width: 767px) {
                    .section_test-prep {
                        padding: 1.5rem 0 !important;
                    }
                    
                    .section_test-prep h2 {
                        font-size: 1.8rem !important;
                        margin-bottom: 1.5rem !important;
                    }
                    
                    .section_test-prep h3 {
                        font-size: 1.3rem !important;
                    }
                    
                    .section_test-prep .feature-cards_item {
                        padding: 1rem !important;
                        margin-bottom: 1rem !important;
                    }
                    
                    .section_test-prep .feature-cards_list {
                        grid-template-columns: 1fr !important;
                        gap: 1rem !important;
                    }
                    
                    .section_test-prep .feature-cards_item[style*="max-width: 280px"] {
                        max-width: 100% !important;
                    }
                    
                    .section_test-prep .margin-bottom[style*="max-width: 800px"] {
                        max-width: 100% !important;
                        padding: 0 1rem !important;
                    }
                }
                
                @media (max-width: 480px) {
                    .section_test-prep h2 {
                        font-size: 1.5rem !important;
                    }
                    
                    .section_test-prep h3 {
                        font-size: 1.2rem !important;
                    }
                    
                    .section_test-prep p {
                        font-size: 0.95rem !important;
                    }
                    
                    .section_test-prep .button {
                        font-size: 1rem !important;
                        padding: 0.8rem 1.5rem !important;
                    }
                }
            </style>
        </section>

                            <!-- Interactive Practice Test Timeline -->
                            <section class="section_practice-timeline" style="background: #f8f9fa; padding: 4rem 0;">
                                <div class="container-default">
                                    <div class="margin-bottom margin-xlarge" style="max-width: 1000px; margin: 0 auto 3rem auto;">
                                        <h3 style="font-size: 2.2rem; margin-bottom: 2rem; color: #111; text-align: center; font-weight: 700;">Practice Test Journey</h3>
                                        <p style="font-size: 1.2rem; line-height: 1.6; color: #6b7280; text-align: center; max-width: 800px; margin: 0 auto 3rem auto;">
                                            Navigate through our comprehensive test preparation components. Click on each node to explore detailed information about the four essential language skills that will prepare you for exam success.
                                        </p>
                                        
                                        <!-- Language Skill Cards -->
                                        <style>
                                            .skill-card {
                                                perspective: 1000px;
                                                height: 200px;
                                                cursor: pointer;
                                                background: #fff;
                                                border-radius: 12px;
                                                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                                                transition: all 0.4s ease;
                                                overflow: hidden;
                                                position: relative;
                                                display: flex;
                                                flex-direction: column;
                                                justify-content: center;
                                                align-items: center;
                                                padding: 2rem;
                                            }
                                            .skill-card:hover {
                                                transform: translateY(-5px);
                                                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                                            }
                                            .skill-card-inner {
                                                position: relative;
                                                width: 100%;
                                                height: 100%;
                                                text-align: center;
                                                transition: all 0.6s ease;
                                            }
                                            .skill-card-front {
                                                display: flex;
                                                flex-direction: column;
                                                align-items: center;
                                                justify-content: center;
                                                width: 100%;
                                                height: 100%;
                                                transition: all 0.4s ease;
                                            }
                                            .skill-card-back {
                                                position: absolute;
                                                top: 0;
                                                left: 0;
                                                width: 100%;
                                                height: 100%;
                                                padding: 1.5rem;
                                                background: #fff;
                                                display: flex;
                                                flex-direction: column;
                                                justify-content: center;
                                                opacity: 0;
                                                transform: translateY(20px);
                                                transition: all 0.4s ease;
                                                border-radius: 12px;
                                            }
                                            .skill-card:hover .skill-card-front {
                                                opacity: 0;
                                                transform: translateY(-20px);
                                            }
                                            .skill-card:hover .skill-card-back {
                                                opacity: 1;
                                                transform: translateY(0);
                                            }
                                            .skill-icon {
                                                font-size: 3.5rem;
                                                margin-bottom: 1.5rem;
                                                transition: all 0.4s ease;
                                                color: #3b82f6;
                                                display: inline-block;
                                                text-shadow: 0 2px 5px rgba(0,0,0,0.1);
                                            }
                                            .skill-card:hover .skill-icon {
                                                transform: scale(1.1);
                                                filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
                                            }
                                            .skill-title {
                                                font-size: 1.3rem;
                                                margin-bottom: 0.5rem;
                                                color: #111;
                                                font-weight: 600;
                                                transition: all 0.4s ease;
                                                transform: translateY(0);
                                            }
                                            .skill-card:hover .skill-title {
                                                transform: translateY(-10px);
                                            }
                                            .skill-list {
                                                text-align: center;
                                                padding: 0 1rem;
                                                margin: 0.5rem 0 0;
                                                list-style: none;
                                                font-size: 0.9rem;
                                                color: #4b5563;
                                                line-height: 1.8;
                                                transition: all 0.6s ease 0.1s;
                                                opacity: 0;
                                                transform: translateY(10px);
                                            }
                                            .skill-card:hover .skill-list {
                                                opacity: 1;
                                                transform: translateY(0);
                                            }
                                            .skill-list li {
                                                margin: 0.3rem 0;
                                                transition: all 0.3s ease;
                                            }
                                            .skill-list li:hover {
                                                color: #1e40af;
                                                transform: translateX(5px);
                                            }
                                        </style>

                                        <div class="skills-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin: 2rem 0;">
                                            <!-- Listening Card -->
                                            <div class="skill-card" style="background-color: #eef2ff;">
                                                <div class="skill-card-front">
                                                    
                                                  
                                                   
            
                                                    <div class="skill-icon" style="color: #4f46e5; margin: 0 auto 0.5rem; display: flex; justify-content: center; width: 100%;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                                                            <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3z"></path>
                                                            <path d="M3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                                                        </svg>
                                                    </div>
                                                    <h3 class="skill-title" style="text-align: center; width: 100%;">Écouter</h3>

                                                </div>
                                                <div class="skill-card-back">
                                                    <h4 style="color: #3b82f6; margin-bottom: 0.5rem; font-size: 1rem;">Listening Skills</h4>
                                                    <ul class="skill-list">
                                                        <li>Native speakers</li>
                                                        <li>French accents</li>
                                                        <li>Conversations</li>
                                                        <li>Lectures</li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Reading Card -->
                                            <div class="skill-card" style="background-color: #ecfdf5;">
                                                <div class="skill-card-front">
                                                    <div class="skill-icon" style="color: #10b981; margin: 0 auto 0.5rem; display: flex; justify-content: center; width: 100%;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h3 class="skill-title" style="text-align: center; width: 100%;">Lire</h3>
                                                </div>
                                                <div class="skill-card-back">
                                                    <h4 style="color: #10b981; margin-bottom: 0.5rem; font-size: 1rem;">Reading Skills</h4>
                                                    <ul class="skill-list">
                                                        <li>Literature</li>
                                                        <li>News articles</li>
                                                        <li>Academic texts</li>
                                                        <li>Documents</li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Writing Card -->
                                            <div class="skill-card" style="background-color: #fffbeb;">
                                                <div class="skill-card-front">
                                                    <div class="skill-icon" style="color: #d97706; margin: 0 auto 0.5rem; display: flex; justify-content: center; width: 100%;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                                            <path d="m15 5 4 4"></path>
                                                        </svg>
                                                    </div>
                                                    <h3 class="skill-title">Écrire</h3>
                                                </div>
                                                <div class="skill-card-back">
                                                    <h4 style="color: #f59e0b; margin-bottom: 0.5rem; font-size: 1rem;">Writing Skills</h4>
                                                    <ul class="skill-list">
                                                        <li>Formal letters</li>
                                                        <li>Essays</li>
                                                        <li>Creative writing</li>
                                                        <li>Emails</li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Speaking Card -->
                                            <div class="skill-card" style="background-color: #f5f3ff;">
                                                <div class="skill-card-front">
                                                    <div class="skill-icon" style="color: #7c3aed; margin: 0 auto 0.5rem; display: flex; justify-content: center; width: 100%;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M12 1a3 3 0 0 0-3 3v16a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                                                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                                            <line x1="12" y1="21" x2="12" y2="23"></line>
                                                            <line x1="8" y1="21" x2="16" y2="21"></line>
                                                        </svg>
                                                    </div>
                                                    <h3 class="skill-title">Parler</h3>
                                                </div>
                                                <div class="skill-card-back">
                                                    <h4 style="color: #8b5cf6; margin-bottom: 0.5rem; font-size: 1rem;">Speaking Skills</h4>
                                                    <ul class="skill-list">
                                                        <li>Conversations</li>
                                                        <li>Pronunciation</li>
                                                        <li>Presentations</li>
                                                        <li>Discussions</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                    
                            
                                    </div>
                                </div>
                                 <div class="margin-top margin-large" style="text-align: center;">
                                <a href="{{ route('join-now') }}" class="button w-button" style="font-size: 1.2rem; padding: 1rem 2rem; background: #2563eb; border-radius: 8px; display: inline-block; text-decoration: none; color: white; font-weight: 600; transition: all 0.3s ease;">Enroll in Test Preparation</a>
                            </div>
                            </section>
                        
            

        <section id="footer-section" class="footer accent-background footer_component footer-video">
  <div class="footer-video-bg">
    <video autoplay muted loop playsinline poster="{{ asset('new-assets/images/footer-fallback.jpg') }}">
      <source src="{{ asset('new-assets/images/footer.mp4') }}" type="video/mp4" />
    </video>
    <div class="footer-video-overlay"></div>
  </div>
  <div class="padding-global footer-content">
    <div class="container footer-top">
      <div class="row gy-4">
        <!-- About Column -->
        <div class="col-lg-5 col-md-12 footer-about">
          <a href="{{ url('/') }}" class="logo d-flex align-items-center mb-3"><br>
            <img src="{{ asset('new-assets/images/tslogo12.jpg') }}" loading="lazy" alt="TS-Language School logo" class="navbar_logo"/>
          </a>
          <p class="footer-description">
            TS-Language School
          </p>
        </div>

        <!-- Quick Links Column -->
        <div class="col-lg-2 col-6 footer-links"><br>
          <h4 class="footer-heading">Quick Links</h4>
          <ul class="footer-list">
            <li><a href="{{ url('/') }}" class="footer-link">Home</a></li>
            <li><a href="{{ route('about') }}" class="footer-link">About Us</a></li>
            <li><a href="{{ route('courses.index') }}" class="footer-link">Courses</a></li>
            <li><a href="{{ route('testimonials') }}" class="footer-link">Testimonials</a></li>
          </ul>
        </div>

        <!-- Social Column -->
        <div class="col-lg-5 col-md-12 footer-social text-center text-md-start"><br>
          <h4 class="footer-heading">Follow Us</h4>
          <div class="social-links d-flex mt-2">
            <a href="#" aria-label="Twitter" class="social-link"><i class="bi bi-twitter-x"></i></a>
            <a href="#" aria-label="Facebook" class="social-link"><i class="bi bi-facebook"></i></a>
            <a href="#" aria-label="Instagram" class="social-link"><i class="bi bi-instagram"></i></a>
            <a href="#" aria-label="LinkedIn" class="social-link"><i class="bi bi-linkedin"></i></a>
            <a href="#" aria-label="YouTube" class="social-link"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="container footer-bottom mt-4 pt-3 border-top">
      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start">
          <p class="footer-copyright mb-0 text-center" style="color: white;"> &copy; <span id="currentYear"></span> TS-Language School. All rights reserved.</p>
        </div>
      </div>
    </div>
  </div>
</section>

    </main>
    </div>
    <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.5.1.min.dc5e7f18c8.js?site=65f05b39b3ea937565a75298" type="text/javascript" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.prod.website-files.com/65f05b39b3ea937565a75298/js/webflow.98f3efb7d.js" type="text/javascript"></script>
    <script type="module">
        import { Warp } from 'https://cdn.skypack.dev/@paper-design/shaders-react';
        import { render } from 'https://cdn.skypack.dev/preact-render-to-string';
        import { h } from 'https://cdn.skypack.dev/preact';
        
        // This is a simplified way to use a React component in a non-React project.
        // We can't directly use the component, but we can use a library that provides a similar effect.
        // For this, I'll use a library that can render a shader to a canvas.
        // A good simple library for this is `gl-shader`. However, for a quick and beautiful result,
        // I'll simulate the effect using a CSS gradient animation which is much more lightweight.
        // The provided React component is too complex to replicate without a full JS framework.
        // The below is a placeholder for a more advanced canvas-based solution.
        // For now, I'll use a CSS animated gradient that is visually appealing and performant.
    </script>
    <script>
    (function(){
        // Fade-in for hero area
        function fadeInHero() {
            const hero = document.querySelector('.hero-fadein-wrapper');
            if (hero) {
                hero.style.opacity = 0;
                hero.style.transition = 'opacity 1.2s ease';
                setTimeout(() => { hero.style.opacity = 1; }, 200);
            }
        }

        // Create per-letter elements with animation
        function createSVGLetters(textSelector, pathId, totalDuration) {
            const svg = document.getElementById('hero-svg');
            if (!svg) return;
            const textNode = svg.querySelector(textSelector);
            if (!textNode) return;
            const original = (textNode.textContent || '').trim();
            if (!original) return;
            
            // Clear existing content
            while (textNode.firstChild) textNode.removeChild(textNode.firstChild);

            // Handle curved title vs straight subtitle differently
            const isCurved = pathId === 'curve-title';
            const pathEl = isCurved ? svg.querySelector(`#${pathId}`) : null;
            const pathLength = pathEl ? pathEl.getTotalLength() : null;

            // Create a temporary invisible <text> element to measure glyph widths/heights
            const meas = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            const computedStyle = window.getComputedStyle ? window.getComputedStyle(textNode) : null;
            if (computedStyle) {
                meas.setAttribute('style', `font:${computedStyle.font}; visibility:hidden;`);
            } else {
                meas.setAttribute('style', `font: 80px Bebas Neue, Impact, Inter, system-ui; visibility:hidden;`);
            }
            meas.setAttribute('x', -9999);
            svg.appendChild(meas);

            const chars = Array.from(original);
            const measurements = chars.map(ch => {
                meas.textContent = ch;
                const w = meas.getComputedTextLength ? meas.getComputedTextLength() : 0;
                let h = 0;
                try {
                    const bbox = meas.getBBox();
                    
                    
                    
                    h = bbox && bbox.height ? bbox.height : (w * 0.6);
                } catch (e) {
                    h = w * 0.6;
                }
                return { w, h };
            });

            const totalTextWidth = measurements.reduce((a, b) => a + b.w, 0) || 1;
            const extraGapAfterFirst = 6;
            let cumulative = 0;

            for (let i = 0; i < chars.length; i++) {
                const ch = chars[i];
                const w = measurements[i].w;
                const h = measurements[i].h;

                if (isCurved) {
                    // For curved title text
                    const centerOffsetPx = cumulative + (w / 2);
                    cumulative += w;
                    if (i === 0) cumulative += extraGapAfterFirst;

                    const startOffsetValue = `${(centerOffsetPx / Math.max(totalTextWidth, 1)) * 100}%`;
                    const tp = document.createElementNS('http://www.w3.org/2000/svg', 'textPath');
                    tp.setAttribute('href', `#${pathId}`);
                    tp.setAttribute('startOffset', startOffsetValue);
                    tp.setAttribute('class', 'animated-letter');
                    tp.textContent = ch;

                    if (i === 0) {
                        const dy = -Math.round(h * 0.9);
                        tp.setAttribute('dy', String(dy));
                    } else if (i === 1) {
                        const dy = Math.round(h * 0.9);
                        tp.setAttribute('dy', String(dy));
                    }

                    const delay = (i * (totalDuration / Math.max(chars.length - 1, 1)));
                    tp.style.animation = `letterIn ${totalDuration}s cubic-bezier(.2,.8,.2,1) forwards`;
                    tp.style.animationDelay = `${delay}s`;
                    tp.setAttribute('dominant-baseline', 'middle');
                    textNode.appendChild(tp);
                } else {
                    // For straight subtitle text
                    const tspan = document.createElementNS('http://www.w3.org/2000/svg', 'tspan');
                    tspan.setAttribute('class', 'animated-letter');
                    tspan.textContent = ch;
                    
                    const delay = (i * (totalDuration / Math.max(chars.length - 1, 1)));
                    tspan.style.animation = `letterIn ${totalDuration}s cubic-bezier(.2,.8,.2,1) forwards`;
                    tspan.style.animationDelay = `${delay}s`;
                    textNode.appendChild(tspan);
                }
            }

            if (meas.parentNode) meas.parentNode.removeChild(meas);
        }

        // Init on DOMContentLoaded
        window.addEventListener('DOMContentLoaded', function() {
            fadeInHero();
            createSVGLetters('.main-hero-title', 'curve-title', 2);
            createSVGLetters('.main-hero-subtitle', 'curve-subtitle', 1.2);
        });

        // Sync small parallax transforms on scroll for the SVG text elements
        window.addEventListener('scroll', function() {
            const svg = document.getElementById('hero-svg');
            if (!svg) return;
            const scrollY = window.scrollY;
            const title = svg.querySelector('.main-hero-title');
            const subtitle = svg.querySelector('.main-hero-subtitle');
            if (title) title.setAttribute('transform', `translate(${scrollY * 0.04},${scrollY * 0.06})`);
            if (subtitle) subtitle.setAttribute('transform', `translate(${-scrollY * 0.02},${scrollY * 0.04})`);
        });
    })();

    // Simple animated gradient as a fallback for the shader
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.querySelector('.section_header');
        if (header) {
            header.style.background = `linear-gradient(-45deg, #0d3b66, #1a535c, #4ea8de, #7a7a7a)`;
            header.style.backgroundSize = `400% 400%`;
            header.style.animation = `gradient 15s ease infinite`;
        }
        const keyframes = `
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }`;
        const styleSheet = document.createElement("style");
        styleSheet.innerText = keyframes;
        document.head.appendChild(styleSheet);
    });
    </script>
    <script src="{{ asset('new-assets/js/radial-orbital-timeline.js') }}"></script>
</body>

</html>