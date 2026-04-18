<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Bus Platform | Command Your Fleet</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-deep-navy: #0A0F1E;
            --bg-nav: rgba(10, 15, 30, 0.85);
            --gold-accent: #D4A847;
            --gold-accent-hover: #b8923d;
            --emerald-green: #00C896;
            --text-light: #ffffff;
            --text-muted: #A0A5B5;
            --card-bg: #12182B;
            --card-border: rgba(255, 255, 255, 0.05);

            --font-heading: 'Bebas Neue', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-deep-navy);
            color: var(--text-light);
            overflow-x: hidden;
            line-height: 1.6;
            scroll-behavior: smooth;
        }

        /* --- NAVIGATION --- */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5%;
            z-index: 1000;
            transition: all 0.3s ease;
            backdrop-filter: blur(0px);
            background: transparent;
        }

        nav.scrolled {
            background: var(--bg-nav);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--card-border);
        }

        .logo {
            font-family: var(--font-heading);
            font-size: 2rem;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-light);
            text-decoration: none;
        }

        .logo span {
            color: var(--gold-accent);
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--gold-accent);
        }

        .nav-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: var(--font-body);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Button Shimmer Effect */
        .btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
            transform: skewX(-25deg);
            transition: none;
        }

        .btn:hover::after {
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            100% { left: 200%; }
        }

        .btn-ghost {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--text-light);
        }

        .btn-gold {
            background: var(--gold-accent);
            color: var(--bg-deep-navy);
            border: 1px solid var(--gold-accent);
        }

        .btn-gold:hover {
            background: var(--gold-accent-hover);
            border-color: var(--gold-accent-hover);
        }

        /* --- HERO SECTION --- */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding-top: 80px;
        }

        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            /* Dawn shift gradient */
            background: linear-gradient(180deg, #02040A 0%, #06112C 40%, #1c183a 70%, #d46f14 100%);
            background-size: 100% 400%;
            animation: dawnShift 20s infinite alternate linear;
        }

        @keyframes dawnShift {
            0% { background-position: 0% 0%; }
            100% { background-position: 0% 100%; }
        }

        /* Stars */
        .stars {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='400'%3E%3Ccircle cx='50' cy='50' r='1' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='150' cy='150' r='1.5' fill='%23fff' opacity='0.6'/%3E%3Ccircle cx='250' cy='80' r='1' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='350' cy='200' r='2' fill='%23fff' opacity='0.5'/%3E%3C/svg%3E") repeat;
            z-index: -1;
            /* Fade out as dawn breaks */
            animation: starsFade 20s infinite alternate ease-in-out;
        }

        @keyframes starsFade {
            0% { opacity: 1; }
            40% { opacity: 0.8; }
            100% { opacity: 0; }
        }

        /* Dubai Skyline Silhouette */
        .skyline {
            position: absolute;
            bottom: 12vh;
            left: 0;
            width: 100%;
            height: 25vh;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1000 100' preserveAspectRatio='none'%3E%3Cpath d='M0,100 L0,80 L20,80 L20,60 L30,60 L30,30 L40,30 L40,10 L50,10 L50,70 L70,70 L70,50 L80,50 L80,40 L90,40 L90,80 L120,80 L120,20 L130,20 L130,50 L150,50 L150,90 L200,90 L200,40 L220,40 L220,70 L250,70 L250,15 L260,15 L260,60 L280,60 L280,85 L320,85 L320,35 L330,35 L330,50 L350,50 L350,75 L400,75 L400,25 L420,25 L420,60 L450,60 L450,10 L460,10 L460,80 L500,80 L500,45 L520,45 L520,65 L550,65 L550,5 L560,5 L560,70 L600,70 L600,30 L620,30 L620,55 L650,55 L650,20 L660,20 L660,85 L700,85 L700,40 L720,40 L720,60 L750,60 L750,10 L760,10 L760,75 L800,75 L800,35 L820,35 L820,55 L850,55 L850,25 L860,25 L860,90 L900,90 L900,45 L920,45 L920,65 L950,65 L950,15 L960,15 L960,80 L1000,80 L1000,100 Z' fill='%23050810'/%3E%3C/svg%3E") repeat-x;
            background-size: 50% 100%;
            z-index: -1;
            opacity: 0.9;
        }

        .road {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 15vh;
            background: linear-gradient(180deg, #111 0%, #050505 100%);
            z-index: 1;
            border-top: 2px solid #222;
        }

        /* Road Lines Animation */
        .road::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 200%;
            height: 4px;
            background: repeating-linear-gradient(90deg, rgba(255,255,255,0.1) 0, rgba(255,255,255,0.1) 100px, transparent 100px, transparent 200px);
            animation: roadScroll 2s linear infinite;
        }

        @keyframes roadScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100px); }
        }

        /* Bus Animation */
        .bus-container {
            position: absolute;
            bottom: 15vh;
            left: -350px;
            width: 320px;
            height: 110px;
            z-index: 2;
            animation: busDrive 20s linear infinite;
        }

        @keyframes busDrive {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(100vw + 600px)); }
        }

        .bus-body {
            position: relative;
            width: 100%;
            height: 100%;
            background: #ffffff;
            border-radius: 10px 25px 15px 10px;
            box-shadow: inset 0 -20px 0 rgba(0,0,0,0.1), 0 10px 30px rgba(0,0,0,0.5);
            z-index: 2;
        }

        /* Windows */
        .bus-window-strip {
            position: absolute;
            top: 20px;
            left: 10px;
            width: 290px;
            height: 35px;
            background: #111;
            border-radius: 5px 15px 5px 5px;
            display: flex;
            gap: 5px;
            padding: 3px;
        }
        
        .bus-window {
            flex: 1;
            background: linear-gradient(135deg, #2a3a5c 0%, #151d2f 100%);
            border-radius: 2px;
            box-shadow: inset 2px 2px 5px rgba(255,255,255,0.2);
            position: relative;
            overflow: hidden;
        }
        
        .bus-window::after {
            content: '';
            position: absolute;
            top: 0; left: -50%; width: 50%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: skewX(-20deg);
            animation: glassShine 4s infinite;
        }

        @keyframes glassShine {
            0%, 50% { left: -50%; }
            100% { left: 150%; }
        }

        /* Highlight Ribbon */
        .bus-ribbon {
            position: absolute;
            bottom: 25px;
            left: 0;
            width: 100%;
            height: 10px;
            background: var(--emerald-green);
        }
        
        .bus-ribbon-gold {
            position: absolute;
            bottom: 12px;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gold-accent);
        }

        /* Wheels */
        .wheel {
            position: absolute;
            bottom: -20px;
            width: 44px;
            height: 44px;
            background: #1a1a1a;
            border-radius: 50%;
            border: 4px solid #333;
            box-shadow: inset 0 0 0 4px #0a0a0a, inset 0 0 0 8px #999, 0 5px 10px rgba(0,0,0,0.5);
            animation: wheelSpin 0.6s linear infinite;
            z-index: 3;
        }

        .wheel-front { right: 45px; }
        .wheel-back { left: 45px; }

        @keyframes wheelSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Wheel arches */
        .wheel-arch {
            position: absolute;
            bottom: -5px;
            width: 56px;
            height: 35px;
            background: #111;
            border-radius: 50px 50px 0 0;
            z-index: 1;
        }
        
        .arch-front { right: 39px; }
        .arch-back { left: 39px; }

        /* Headlights */
        .headlight {
            position: absolute;
            right: 0px;
            bottom: 30px;
            width: 8px;
            height: 18px;
            background: #fff;
            border-radius: 5px 0 0 5px;
            box-shadow: 0 0 10px #fff, 0 0 20px #fff;
            z-index: 3;
        }

        .headlight::after {
            content: '';
            position: absolute;
            top: -25px;
            left: 5px;
            width: 250px;
            height: 70px;
            background: linear-gradient(to right, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0.05) 50%, transparent 100%);
            clip-path: polygon(0 35%, 100% 0, 100% 100%, 0 65%);
            pointer-events: none;
        }
        
        /* Tail light */
        .taillight {
            position: absolute;
            left: 0px;
            bottom: 30px;
            width: 6px;
            height: 18px;
            background: #ff0000;
            border-radius: 0 5px 5px 0;
            box-shadow: -5px 0 15px #ff0000;
            z-index: 3;
        }

        /* Dust Trail */
        .dust-container {
            position: absolute;
            bottom: -25px;
            left: -120px;
            width: 150px;
            height: 40px;
            display: flex;
            z-index: 1;
        }
        
        .dust-particle {
            position: absolute;
            bottom: 0;
            background: rgba(180, 150, 120, 0.2);
            border-radius: 50%;
            filter: blur(8px);
            animation: dustFade 1.5s infinite linear;
        }

        .dust-particle:nth-child(1) { left: 100px; width: 25px; height: 25px; animation-delay: 0s; }
        .dust-particle:nth-child(2) { left: 60px; width: 35px; height: 35px; animation-delay: 0.3s; }
        .dust-particle:nth-child(3) { left: 20px; width: 50px; height: 50px; animation-delay: 0.6s; }
        .dust-particle:nth-child(4) { left: -10px; width: 60px; height: 60px; animation-delay: 0.9s; }

        @keyframes dustFade {
            0% { transform: scale(0.5) translateY(0); opacity: 0.6; }
            100% { transform: scale(2.5) translateY(-30px) rotate(45deg); opacity: 0; }
        }

        /* Floating Cards */
        .floating-card {
            position: absolute;
            background: rgba(18, 24, 43, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            border-radius: 8px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            opacity: 0;
            z-index: 4;
            transform: translateY(20px);
            min-width: 160px;
        }

        /* Animate relative to bus container so they move with it */
        .fc-1 { top: -70px; right: 20px; animation: popupCard 5s infinite; } 
        .fc-2 { top: -50px; left: 20px; animation: popupCard 5s infinite 2.5s; }
        .fc-3 { top: -90px; left: 100px; animation: popupCard 6s infinite 1s; }
        
        .fc-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(0, 200, 150, 0.15);
            color: var(--emerald-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        
        .fc-icon.gold {
            background: rgba(212, 168, 71, 0.15);
            color: var(--gold-accent);
        }

        .fc-text h4 { font-size: 11px; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;}
        .fc-text p { font-size: 15px; color: #fff; font-weight: 700; font-family: var(--font-heading); letter-spacing: 1px;}

        @keyframes popupCard {
            0%, 100% { transform: translateY(20px); opacity: 0; }
            10%, 90% { transform: translateY(0); opacity: 1; }
            50% { transform: translateY(-8px); opacity: 1; }
        }


        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 850px;
            padding: 0 20px;
            margin-top: -15vh; /* slightly moved up to accommodate bus below */
        }

        /* Hero Title Container & Eyebrow */
        .hero-title-container {
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .eyebrow {
            font-size: 0.9rem;
            color: var(--gold-accent);
            text-transform: uppercase;
            letter-spacing: 0.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            opacity: 0;
            animation: fadeInBlur 1s forwards 0.5s;
            position: relative;
            display: inline-block;
        }

        .eyebrow::before, .eyebrow::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30px;
            height: 1px;
            background: var(--gold-accent);
            opacity: 0.5;
        }
        .eyebrow::before { right: 105%; }
        .eyebrow::after { left: 105%; }

        @keyframes fadeInBlur {
            from { opacity: 0; filter: blur(10px); transform: translateY(-10px); }
            to { opacity: 1; filter: blur(0); transform: translateY(0); }
        }

        .hero-title {
            display: flex;
            flex-direction: column;
            line-height: 0.9;
            margin-bottom: 1.5rem;
        }

        .word-command {
            font-family: var(--font-heading);
            font-size: 8rem;
            letter-spacing: -2px;
            background: linear-gradient(to bottom, #BF953F 0%, #FCF6BA 25%, #B38728 50%, #FBF5B7 75%, #AA771C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.5));
            margin-bottom: -0.5rem;
        }

        .word-fleet {
            font-family: var(--font-body);
            font-weight: 300;
            font-size: 2.5rem;
            color: #fff;
            letter-spacing: 1.2rem;
            text-transform: uppercase;
            opacity: 0.9;
        }

        /* Reveal Spans */
        .hero-title span span {
            display: inline-block;
            opacity: 0;
            transform: translateY(30px) rotateX(90deg);
            transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .hero-title span span.revealed {
            opacity: 1;
            transform: translateY(0) rotateX(0);
        }

        .hero-subtitle {
            font-size: 1.6rem;
            color: var(--text-light);
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeInUp 1s forwards 1.2s;
            font-weight: 500;
        }

        .hero-desc {
            font-size: 1.15rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            line-height: 1.7;
            opacity: 0;
            animation: fadeInUp 1s forwards 1.5s;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-actions {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-bottom: 3rem;
            opacity: 0;
            animation: fadeInUp 1s forwards 1.8s;
        }

        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            color: var(--text-light);
            font-size: 0.95rem;
            opacity: 0;
            animation: fadeInUp 1s forwards 2.1s;
            font-weight: 500;
        }
        
        .trust-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.05);
            padding: 6px 16px;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(5px);
        }
        
        .trust-badge svg {
            color: var(--emerald-green);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- STATS SECTION --- */
        .stats {
            background: #050810;
            padding: 5rem 5%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            position: relative;
            z-index: 10;
            border-top: 1px solid rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            text-align: center;
        }

        .stat-item {
            padding: 1rem;
        }

        .stat-item h3 {
            font-family: var(--font-heading);
            font-size: 4.5rem;
            color: var(--gold-accent);
            margin-bottom: 0.5rem;
            line-height: 1;
            text-shadow: 0 5px 15px rgba(212, 168, 71, 0.2);
        }

        .stat-item p {
            font-size: 1.1rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* --- FEATURES SECTION --- */
        .features {
            padding: 8rem 5%;
            background: var(--bg-deep-navy);
            position: relative;
            background-image: radial-gradient(circle at 100% 0%, rgba(212,168,71,0.05) 0%, transparent 50%),
                              radial-gradient(circle at 0% 100%, rgba(0,200,150,0.05) 0%, transparent 50%);
        }

        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-header h2 {
            font-family: var(--font-heading);
            font-size: 4rem;
            letter-spacing: 2px;
            margin-bottom: 1rem;
            color: var(--text-light);
        }
        
        .section-header p {
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
            font-size: 1.15rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 3rem 2.5rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
            transform: translateY(40px);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--emerald-green), var(--gold-accent));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .feature-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: rgba(212, 168, 71, 0.3);
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            width: 65px;
            height: 65px;
            background: rgba(212, 168, 71, 0.1);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            border: 1px solid rgba(212, 168, 71, 0.2);
            transition: all 0.3s;
        }

        .feature-card:hover .feature-icon {
            background: var(--gold-accent);
            transform: scale(1.1) rotate(5deg);
        }

        .feature-icon svg {
            width: 32px;
            height: 32px;
            stroke: var(--gold-accent);
            transition: stroke 0.3s;
        }
        
        .feature-card:hover .feature-icon svg {
            stroke: var(--bg-deep-navy);
        }

        .feature-card h3 {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            color: var(--text-light);
            font-weight: 700;
        }

        .feature-card p {
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* --- PRICING SECTION --- */
        .pricing {
            padding: 8rem 5%;
            background: #080C17; 
            border-top: 1px solid rgba(255,255,255,0.02);
            position: relative;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
            align-items: center;
        }

        .price-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 3.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: all 0.4s ease;
            opacity: 0;
            transform: translateY(40px);
        }
        
        .price-card.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .price-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            border-color: rgba(255,255,255,0.1);
        }

        .price-card.popular {
            border-color: var(--gold-accent);
            background: linear-gradient(180deg, rgba(212, 168, 71, 0.08) 0%, rgba(18, 24, 43, 1) 100%);
            transform: scale(1.05);
            z-index: 2;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6);
        }
        
        .price-card.popular.visible {
            transform: scale(1.05) translateY(0);
        }
        
        .price-card.popular:hover {
            transform: scale(1.05) translateY(-10px);
            border-color: var(--gold-accent-hover);
            box-shadow: 0 30px 60px rgba(212, 168, 71, 0.15);
        }

        .popular-badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(90deg, var(--gold-accent), #f2c94c);
            color: var(--bg-deep-navy);
            padding: 6px 20px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            box-shadow: 0 5px 15px rgba(212, 168, 71, 0.4);
        }

        .plan-name {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-light);
        }

        .plan-price {
            font-family: var(--font-heading);
            font-size: 4.5rem;
            color: var(--text-light);
            margin-bottom: 2rem;
            line-height: 1;
        }
        
        .plan-price span {
            font-family: var(--font-body);
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .plan-features {
            list-style: none;
            flex-grow: 1;
            margin-bottom: 3rem;
        }

        .plan-features li {
            padding: 12px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-muted);
            border-bottom: 1px solid rgba(255,255,255,0.03);
            font-size: 1rem;
        }
        
        .plan-features li svg {
            color: var(--emerald-green);
            flex-shrink: 0;
        }

        .price-card .btn {
            width: 100%;
            justify-content: center;
            padding: 1rem;
            font-size: 1.1rem;
        }

        /* --- CTA SECTION & FOOTER --- */
        .cta-section {
            padding: 8rem 5%;
            background: linear-gradient(135deg, var(--bg-deep-navy) 0%, #111827 100%);
            text-align: center;
            border-top: 1px solid var(--card-border);
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 800px; height: 800px;
            background: radial-gradient(circle, rgba(212,168,71,0.05) 0%, transparent 60%);
            pointer-events: none;
        }

        .cta-section h2 {
            font-family: var(--font-heading);
            font-size: 5rem;
            margin-bottom: 2.5rem;
            letter-spacing: 2px;
            position: relative;
            z-index: 1;
        }

        footer {
            background: #03050a;
            padding: 5rem 5% 2rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 3rem;
            margin-bottom: 4rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .footer-col {
            flex: 1;
            min-width: 250px;
        }

        .footer-col.brand-col {
            flex: 2;
        }

        .footer-col h4 {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            letter-spacing: 1px;
        }

        .footer-col p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            line-height: 1.7;
            max-width: 400px;
        }

        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 1rem;
        }
        
        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--gold-accent);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            color: var(--text-muted);
            font-size: 0.95rem;
            flex-wrap: wrap;
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .vat-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.6rem 1.2rem;
            background: rgba(0, 200, 150, 0.05);
            border: 1px solid rgba(0, 200, 150, 0.2);
            border-radius: 6px;
            color: var(--emerald-green);
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 1024px) {
            .hero-title { font-size: 5rem; }
            .plan-price { font-size: 3.5rem; }
            .price-card.popular { transform: scale(1); }
            .price-card.popular:hover { transform: translateY(-10px); }
            .price-card.popular.visible { transform: scale(1) translateY(0); }
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero-title { font-size: 3.5rem; }
            .hero-subtitle { font-size: 1.2rem; }
            .hero-actions { flex-direction: column; }
            .hero-actions .btn { width: 100%; justify-content: center; }
            .trust-badges { flex-direction: column; gap: 1rem; align-items: center; }
            .stats { grid-template-columns: 1fr 1fr; }
            .cta-section h2 { font-size: 3rem; }
            .bus-container { transform: scale(0.7); transform-origin: bottom left; }
            .skyline { background-size: cover; }
        }

        @media (max-width: 480px) {
            .stats { grid-template-columns: 1fr; }
            .hero-title { font-size: 2.8rem; }
            nav .btn-ghost { display: none; }
        }
    </style>
</head>
<body>

    <!-- NAVIGATION -->
    <nav id="navbar">
        <a href="#" class="logo">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gold-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="15" rx="3" ry="3"></rect>
                <polyline points="17 2 12 7 7 2"></polyline>
            </svg>OWN BUSES</span>
        </a>
        <ul class="nav-links">
            <li><a href="#features">Features</a></li>
            <li><a href="#pricing">Pricing</a></li>
            <li><a href="#stats">About</a></li>
        </ul>
        <div class="nav-actions">
            <!-- Ensure navigation routes point to actual implementation logically -->
            <a href="{{ url('/login') }}" class="btn btn-ghost">Login</a>
            <a href="{{ url('/register') }}" class="btn btn-gold">Registration</a>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="stars"></div>
        <div class="skyline"></div>
        
        <!-- Animated Bus & Road -->
        <div class="road"></div>
        <div class="bus-container">
            <!-- Floating UI Cards attached to bus surroundings -->
            <div class="floating-card fc-1">
                <div class="fc-icon gold">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="fc-text">
                    <h4>Revenue</h4>
                    <p>AED 0</p>
                </div>
            </div>
            <div class="floating-card fc-2">
                <div class="fc-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div class="fc-text">
                    <h4>GPS Dot</h4>
                    <p>Live Tracking</p>
                </div>
            </div>
            <div class="floating-card fc-3">
                <div class="fc-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="fc-text">
                    <h4>Fleet Status</h4>
                    <p>Active</p>
                </div>
            </div>

            <div class="bus-body">
                <div class="bus-window-strip">
                    <div class="bus-window"></div>
                    <div class="bus-window"></div>
                    <div class="bus-window"></div>
                    <div class="bus-window"></div>
                    <div class="bus-window"></div>
                </div>
                <div class="bus-ribbon"></div>
                <div class="bus-ribbon-gold"></div>
                <div class="headlight"></div>
                <div class="taillight"></div>
            </div>
            <div class="wheel-arch arch-front"></div>
            <div class="wheel-arch arch-back"></div>
            <div class="wheel wheel-front"></div>
            <div class="wheel wheel-back"></div>
            <div class="dust-container">
                <div class="dust-particle"></div>
                <div class="dust-particle"></div>
                <div class="dust-particle"></div>
                <div class="dust-particle"></div>
            </div>
        </div>

        <div class="hero-content">
            <div class="hero-title-container">
                <p class="eyebrow">Next-Gen Fleet Intelligence</p>
                <h1 class="hero-title" id="animated-title">
                    <span class="word-command">COMMAND</span>
                    <span class="word-fleet">YOUR FLEET.</span>
                </h1>
            </div>
            <p class="hero-subtitle">The all-in-one fleet management platform built exclusively for UAE bus & transport operators.</p>
            <p class="hero-desc">Manage rentals, track vehicles live, handle drivers & staff, automate VAT accounting, and grow your business — all from one powerful dashboard. Starting from AED 1,999 / year.</p>
            
            <div class="hero-actions">
                <a href="{{ url('/register') }}" class="btn btn-gold">START FREE TRIAL &rarr;</a>
                <a href="#features" class="btn btn-ghost">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    WATCH DEMO
                </a>
            </div>

            <div class="trust-badges">
                <div class="trust-badge">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    UAE VAT Compliant
                </div>
                <div class="trust-badge">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    RTA Ready
                </div>
                <div class="trust-badge">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Arabic & English
                </div>
            </div>
        </div>
    </section>

    <!-- STATS COUNTER -->
    <section class="stats" id="stats">
        <div class="stat-item">
            <h3 class="counter" data-target="500" data-suffix="+">0</h3>
            <p>Buses Managed</p>
        </div>
        <div class="stat-item">
            <h3 class="counter" data-target="2" data-prefix="AED " data-suffix="M+">0</h3>
            <p>Revenue Tracked</p>
        </div>
        <div class="stat-item">
            <h3 class="counter" data-target="99.9" data-suffix="%">0</h3>
            <p>Uptime</p>
        </div>
        <div class="stat-item">
            <h3 class="counter" data-target="50" data-suffix="+">0</h3>
            <p>UAE Companies</p>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="features" id="features">
        <div class="section-header">
            <h2>Everything You Need</h2>
            <p>A comprehensive suite designed specifically for the unique challenges of the UAE transportation industry.</p>
        </div>
        
        <div class="features-grid">
            <!-- Card 1 -->
            <div class="feature-card scroll-reveal">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="10" width="18" height="10" rx="2"/><path d="M5 10V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v4"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                </div>
                <h3>Fleet Management</h3>
                <p>Track every vehicle, maintenance schedule, registration (Mulkiya), insurance & route permits in real time.</p>
            </div>

            <!-- Card 2 -->
            <div class="feature-card scroll-reveal" style="transition-delay: 0.1s;">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/><path d="M12 10v4"/></svg>
                </div>
                <h3>Live GPS Tracking</h3>
                <p>See your entire fleet on a live map. Know where every bus is, right now.</p>
            </div>

            <!-- Card 3 -->
            <div class="feature-card scroll-reveal" style="transition-delay: 0.2s;">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                </div>
                <h3>Driver & Staff Management</h3>
                <p>Assign drivers, manage contracts, track performance and handle payroll all in one place.</p>
            </div>

            <!-- Card 4 -->
            <div class="feature-card scroll-reveal">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <h3>UAE VAT & Accounting</h3>
                <p>Auto-generate VAT invoices, track MTD revenue, and stay audit-ready. Built specifically for UAE FTA compliance.</p>
            </div>

            <!-- Card 5 -->
            <div class="feature-card scroll-reveal" style="transition-delay: 0.1s;">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>
                </div>
                <h3>Rental & Customer Portal</h3>
                <p>Create rentals, manage customers, set dynamic pricing and record payments seamlessly.</p>
            </div>

            <!-- Card 6 -->
            <div class="feature-card scroll-reveal" style="transition-delay: 0.2s;">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                </div>
                <h3>Company Health Dashboard</h3>
                <p>Get your Company Health Score, risk index alerts, and executive business intelligence reports.</p>
            </div>
        </div>
    </section>

    <!-- PRICING -->
    <section class="pricing" id="pricing">
        <div class="section-header">
            <h2>Simple Yearly Pricing. No Surprises.</h2>
            <p>Choose the plan that fits your fleet size. Upgrade anytime seamlessly.</p>
        </div>

        <div class="pricing-grid">
            <!-- Starter -->
            <div class="price-card scroll-reveal">
                <h3 class="plan-name">Starter</h3>
                <div class="plan-price">1,999 <span>AED / year</span></div>
                <ul class="plan-features">
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Up to 10 Vehicles</li>
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Basic Fleet Mgt</li>
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Driver Tracking</li>
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Standard Reporting</li>
                </ul>
                <a href="{{ url('/register') }}" class="btn btn-ghost">Start Free Trial</a>
            </div>

            <!-- Growth (Popular) -->
            <div class="price-card popular scroll-reveal" style="transition-delay: 0.1s;">
                <div class="popular-badge">Most Popular</div>
                <h3 class="plan-name">Growth</h3>
                <div class="plan-price">4,999 <span>AED / year</span></div>
                <ul class="plan-features">
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Up to 50 Vehicles</li>
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Advanced GPS Tracking</li>
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Auto VAT Invoicing</li>
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Customer Portal</li>
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Premium Support</li>
                </ul>
                <a href="{{ url('/register') }}" class="btn btn-gold">Get Started Now</a>
            </div>

            <!-- Enterprise -->
            <div class="price-card scroll-reveal" style="transition-delay: 0.2s;">
                <h3 class="plan-name">Enterprise</h3>
                <div class="plan-price">Custom</div>
                <ul class="plan-features">
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Unlimited Fleet</li>
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Custom API Integrations</li>
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Dedicated Account Manager</li>
                    <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg> On-premise Option</li>
                </ul>
                <a href="#contact" class="btn btn-ghost">Contact Sales</a>
            </div>
        </div>
    </section>

    <!-- FOOTER CTA -->
    <section class="cta-section">
        <h2>Ready to take control of your fleet?</h2>
        <a href="{{ url('/register') }}" class="btn btn-gold" style="font-size: 1.2rem; padding: 1.2rem 3.5rem;">START YOUR FREE TRIAL</a>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-content">
            <div class="footer-col brand-col">
                <a href="#" class="logo" style="margin-bottom: 1.5rem; display: inline-block;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gold-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="15" rx="3" ry="3"></rect>
                        <polyline points="17 2 12 7 7 2"></polyline>
                    </svg>
                    [B] <span>Khan Trader</span>
                </a>
                <p>The definitive SaaS platform for UAE bus rental and transport companies. Built to scale, designed for compliance.</p>
                <div class="vat-badge">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    UAE VAT / FTA Compliant
                </div>
            </div>
            
            <div class="footer-col">
                <h4>Product</h4>
                <ul class="footer-links">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="#">Integrations</a></li>
                    <li><a href="#">Updates</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>Company</h4>
                <ul class="footer-links">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Khan Trader. All rights reserved.</p>
            <p style="font-family: 'Arial', sans-serif; font-weight: bold; color: var(--gold-accent);">ارتقِ بأسطولك إلى مستوى جديد</p>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            // 1. Navbar Scroll Effect
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // 2. Title Letter Reveal Animation (Improved for nested structure)
            const titleElement = document.getElementById('animated-title');
            const lines = titleElement.querySelectorAll('span');
            let globalIndex = 0;

            lines.forEach((line) => {
                const text = line.innerText;
                line.innerHTML = '';
                text.split('').forEach((char) => {
                    const span = document.createElement('span');
                    span.innerText = char === ' ' ? '\u00A0' : char;
                    span.style.transitionDelay = `${globalIndex * 0.04}s`;
                    line.appendChild(span);
                    globalIndex++;
                    
                    setTimeout(() => {
                        span.classList.add('revealed');
                    }, 100);
                });
            });

            // 3. Scroll Reveal using Intersection Observer
            const revealElements = document.querySelectorAll('.scroll-reveal');
            
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: "0px 0px -50px 0px"
            });

            revealElements.forEach(el => revealObserver.observe(el));

            // 4. Number Counters Animation
            const counters = document.querySelectorAll('.counter');
            let hasCounted = false;

            const counterObserver = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !hasCounted) {
                    hasCounted = true;
                    
                    counters.forEach(counter => {
                        const target = parseFloat(counter.getAttribute('data-target'));
                        const prefix = counter.getAttribute('data-prefix') || '';
                        const suffix = counter.getAttribute('data-suffix') || '';
                        const duration = 2000; // ms
                        const frameDuration = 1000 / 60;
                        const totalFrames = Math.round(duration / frameDuration);
                        let frame = 0;
                        
                        const updateCount = () => {
                            frame++;
                            const progress = frame / totalFrames;
                            // ease out quad
                            const easeOut = progress * (2 - progress);
                            const current = target * easeOut;
                            
                            if (frame < totalFrames) {
                                // format value
                                let displayVal;
                                if (target % 1 !== 0) {
                                    displayVal = current.toFixed(1);
                                } else {
                                    displayVal = Math.ceil(current);
                                }
                                counter.innerText = prefix + displayVal + suffix;
                                requestAnimationFrame(updateCount);
                            } else {
                                counter.innerText = prefix + target + suffix;
                            }
                        };
                        requestAnimationFrame(updateCount);
                    });
                }
            }, { threshold: 0.3 });
            
            const statsSection = document.getElementById('stats');
            if (statsSection) {
                counterObserver.observe(statsSection);
            }
        });
    </script>
</body>
</html>